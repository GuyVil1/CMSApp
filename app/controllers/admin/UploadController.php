<?php
declare(strict_types=1);

namespace Admin;

require_once __DIR__ . '/../../../core/Controller.php';
require_once __DIR__ . '/../../../core/Auth.php';
require_once __DIR__ . '/../../../app/helpers/rate_limit_helper.php';

class UploadController extends \Controller
{
    public function __construct()
    {
        \Auth::requireRole(['admin', 'editor']);
    }
    
    /**
     * Upload d'image
     */
    public function image(): void
    {
        // Vérifier que c'est une requête POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'message' => 'Méthode non autorisée'], 405);
            return;
        }
        
        // Vérifier le token CSRF
        $csrf_token = $_POST['csrf_token'] ?? '';
        if (!\Auth::verifyCsrfToken($csrf_token)) {
            $this->jsonResponse(['success' => false, 'message' => 'Token de sécurité invalide'], 403);
            return;
        }
        
        // Vérifier les limites de rate limiting
        $userId = \Auth::isLoggedIn() ? \Auth::getUserId() : null;
        $rateLimitCheck = \RateLimitHelper::checkUploadLimits($userId);
        if (!$rateLimitCheck['allowed']) {
            $this->jsonResponse([
                'success' => false, 
                'message' => $rateLimitCheck['reason'],
                'rate_limit' => $rateLimitCheck
            ], 429);
            return;
        }
        
        // Vérifier qu'un fichier a été uploadé
        if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            $this->jsonResponse(['success' => false, 'message' => 'Aucun fichier uploadé'], 400);
            return;
        }
        
        $file = $_FILES['image'];
        $type = $_POST['type'] ?? 'article';
        
        // Validation du fichier
        $validation = $this->validateImage($file);
        if (!$validation['valid']) {
            $this->jsonResponse(['success' => false, 'message' => $validation['message']], 400);
            return;
        }
        
        // Upload et traitement de l'image
        $result = $this->processImage($file, $type);
        if ($result['success']) {
            // Enregistrer l'upload pour le rate limiting
            \RateLimitHelper::recordUpload($userId, null, $file['size']);
            $this->jsonResponse($result);
        } else {
            $this->jsonResponse($result, 500);
        }
    }
    
    /**
     * Valider l'image uploadée
     */
    private function validateImage(array $file): array
    {
        // Vérifier la taille (max 5MB)
        $maxSize = 5 * 1024 * 1024; // 5MB
        if ($file['size'] > $maxSize) {
            return ['valid' => false, 'message' => 'Fichier trop volumineux (max 5MB)'];
        }
        
        // Vérifier le type MIME
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mimeType, $allowedTypes)) {
            return ['valid' => false, 'message' => 'Type de fichier non autorisé. Utilisez JPG, PNG, GIF ou WebP'];
        }
        
        // Vérifier l'extension
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (!in_array($extension, $allowedExtensions)) {
            return ['valid' => false, 'message' => 'Extension de fichier non autorisée'];
        }
        
        // VALIDATION RENFORCÉE : Vérifier le contenu réel de l'image
        $imageInfo = getimagesize($file['tmp_name']);
        if ($imageInfo === false) {
            return ['valid' => false, 'message' => 'Fichier corrompu ou non-image'];
        }
        
        // Vérifier que le MIME type détecté par getimagesize correspond
        $detectedMimeType = $imageInfo['mime'];
        if (!in_array($detectedMimeType, $allowedTypes)) {
            return ['valid' => false, 'message' => 'Contenu d\'image invalide détecté'];
        }
        
        // Vérifier les dimensions (protection contre les images malveillantes)
        $maxWidth = 4096;
        $maxHeight = 4096;
        if ($imageInfo[0] > $maxWidth || $imageInfo[1] > $maxHeight) {
            return ['valid' => false, 'message' => "Image trop grande (max {$maxWidth}x{$maxHeight}px)"];
        }
        
        // Vérifier que l'image n'est pas vide
        if ($imageInfo[0] <= 0 || $imageInfo[1] <= 0) {
            return ['valid' => false, 'message' => 'Image invalide (dimensions nulles)'];
        }
        
        return ['valid' => true];
    }
    
    /**
     * Traiter et sauvegarder l'image
     */
    private function processImage(array $file, string $type): array
    {
        try {
            // Créer le dossier d'upload s'il n'existe pas
            $uploadDir = __DIR__ . '/../../../public/uploads/' . $type;
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            // Générer un nom de fichier unique
            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $filename = uniqid() . '_' . time() . '.' . $extension;
            $filepath = $uploadDir . '/' . $filename;
            
            // Déplacer le fichier uploadé
            if (!move_uploaded_file($file['tmp_name'], $filepath)) {
                return ['success' => false, 'message' => 'Erreur lors du déplacement du fichier'];
            }
            
            // Créer une miniature si c'est une image
            $thumbnailPath = $this->createThumbnail($filepath, $uploadDir, $filename);
            
            // Retourner les informations de l'image
            return [
                'success' => true,
                'url' => '/public/uploads/' . $type . '/' . $filename,
                'thumbnail' => $thumbnailPath ? '/public/uploads/' . $type . '/' . $thumbnailPath : null,
                'filename' => $filename,
                'size' => $file['size'],
                'type' => $type
            ];
            
        } catch (\Exception $e) {
            error_log('Erreur upload image: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur interne du serveur'];
        }
    }
    
    /**
     * Créer une miniature de l'image
     */
    private function createThumbnail(string $sourcePath, string $uploadDir, string $filename): ?string
    {
        try {
            // Obtenir les informations de l'image
            $imageInfo = getimagesize($sourcePath);
            if (!$imageInfo) {
                return null;
            }
            
            $width = $imageInfo[0];
            $height = $imageInfo[1];
            $mimeType = $imageInfo['mime'];
            
            // Créer l'image source
            $sourceImage = $this->createImageFromFile($sourcePath, $mimeType);
            if (!$sourceImage) {
                return null;
            }
            
            // Calculer les dimensions de la miniature
            $maxThumbWidth = 300;
            $maxThumbHeight = 300;
            
            if ($width > $height) {
                $thumbWidth = $maxThumbWidth;
                $thumbHeight = intval(($height / $width) * $maxThumbWidth);
            } else {
                $thumbHeight = $maxThumbHeight;
                $thumbWidth = intval(($width / $height) * $maxThumbHeight);
            }
            
            // Créer la miniature
            $thumbImage = imagecreatetruecolor($thumbWidth, $thumbHeight);
            
            // Préserver la transparence pour PNG et GIF
            if (in_array($mimeType, ['image/png', 'image/gif'])) {
                imagealphablending($thumbImage, false);
                imagesavealpha($thumbImage, true);
                $transparent = imagecolorallocatealpha($thumbImage, 255, 255, 255, 127);
                imagefill($thumbImage, 0, 0, $transparent);
            }
            
            // Redimensionner l'image
            imagecopyresampled($thumbImage, $sourceImage, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $width, $height);
            
            // Générer le nom de la miniature
            $thumbFilename = 'thumb_' . $filename;
            $thumbPath = $uploadDir . '/' . $thumbFilename;
            
            // Sauvegarder la miniature
            $this->saveImage($thumbImage, $thumbPath, $mimeType);
            
            // Libérer la mémoire
            imagedestroy($sourceImage);
            imagedestroy($thumbImage);
            
            return $thumbFilename;
            
        } catch (\Exception $e) {
            error_log('Erreur création miniature: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Créer une image depuis un fichier
     */
    private function createImageFromFile(string $filepath, string $mimeType)
    {
        switch ($mimeType) {
            case 'image/jpeg':
            case 'image/jpg':
                return imagecreatefromjpeg($filepath);
            case 'image/png':
                return imagecreatefrompng($filepath);
            case 'image/gif':
                return imagecreatefromgif($filepath);
            case 'image/webp':
                return imagecreatefromwebp($filepath);
            default:
                return null;
        }
    }
    
    /**
     * Sauvegarder une image
     */
    private function saveImage($image, string $filepath, string $mimeType): bool
    {
        switch ($mimeType) {
            case 'image/jpeg':
            case 'image/jpg':
                return imagejpeg($image, $filepath, 85);
            case 'image/png':
                return imagepng($image, $filepath, 8);
            case 'image/gif':
                return imagegif($image, $filepath);
            case 'image/webp':
                return imagewebp($image, $filepath, 85);
            default:
                return false;
        }
    }
    
    /**
     * Réponse JSON
     */
    private function jsonResponse(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
