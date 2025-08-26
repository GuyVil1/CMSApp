<?php
declare(strict_types=1);

/**
 * Contrôleur MediaController - Gestion des médias dans l'admin
 */

namespace Admin;

require_once __DIR__ . '/../../../core/Controller.php';
require_once __DIR__ . '/../../../core/Auth.php';
require_once __DIR__ . '/../../../app/models/Media.php';

class MediaController extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        
        // Vérifier l'authentification et les permissions - TEMPORAIREMENT DÉSACTIVÉ
        // \Auth::requirePermission('author');
    }
    
    /**
     * Liste des médias
     */
    public function index(): void
    {
        $page = (int)($_GET['page'] ?? 1);
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        $medias = \Media::findAll($limit, $offset);
        $totalMedias = \Media::count();
        $totalPages = ceil($totalMedias / $limit);
        
        $this->render('admin/media/index', [
            'medias' => $medias,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalMedias' => $totalMedias
        ]);
    }
    
    /**
     * Upload d'image
     */
    public function upload(): void
    {
        // Débogage
        error_log("MediaController::upload() appelé");
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            error_log("Méthode non autorisée: " . $_SERVER['REQUEST_METHOD']);
            $this->jsonResponse(['error' => 'Méthode non autorisée'], 405);
            return;
        }
        
        // Vérifier le token CSRF - TEMPORAIREMENT DÉSACTIVÉ POUR DIAGNOSTIC
        /*
        if (!\Auth::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->jsonResponse(['error' => 'Token CSRF invalide'], 403);
            return;
        }
        */
        
        try {
            error_log("Vérification des fichiers uploadés...");
            if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
                $error = $_FILES['file']['error'] ?? 'Fichier non défini';
                error_log("Erreur upload fichier: " . $error);
                throw new \Exception('Erreur lors de l\'upload du fichier: ' . $error);
            }
            
            $file = $_FILES['file'];
            error_log("Fichier reçu: " . $file['name'] . " (" . $file['size'] . " bytes)");
            
            $uploadDir = __DIR__ . '/../../../public/uploads/';
            error_log("Dossier upload: " . $uploadDir);
            
            // Vérifier que le dossier existe
            if (!is_dir($uploadDir)) {
                error_log("Création du dossier upload...");
                mkdir($uploadDir, 0755, true);
            }
            
            // Vérifier le type MIME
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);
            
            // Types autorisés
            $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
            if (!in_array($mimeType, $allowedTypes)) {
                throw new \Exception('Type de fichier non autorisé. Formats acceptés : JPG, PNG, WebP, GIF');
            }
            
            // Vérifier la taille (4MB max)
            $maxSize = 4 * 1024 * 1024;
            if ($file['size'] > $maxSize) {
                throw new \Exception('Fichier trop volumineux (max 4MB)');
            }
            
            // Générer un nom de fichier unique
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = uniqid() . '_' . time() . '.' . $extension;
            $filepath = $uploadDir . $filename;
            
            // Déplacer le fichier
            if (!move_uploaded_file($file['tmp_name'], $filepath)) {
                throw new \Exception('Erreur lors du déplacement du fichier');
            }
            
            // Créer la vignette si c'est une image
            $this->createThumbnail($filepath, $filename);
            
            // Enregistrer en base de données
            $mediaData = [
                'filename' => $filename,
                'original_name' => $file['name'],
                'mime_type' => $mimeType,
                'size' => $file['size'],
                'uploaded_by' => \Auth::getUserId()
            ];
            
            $media = \Media::create($mediaData);
            
            if (!$media) {
                throw new \Exception('Erreur lors de l\'enregistrement en base de données');
            }
            
            // Stocker en cache temporaire (session)
            $this->storeInTempCache($media);
            
            $this->jsonResponse([
                'success' => true,
                'media' => [
                    'id' => $media->getId(),
                    'filename' => $media->getFilename(),
                    'original_name' => $media->getOriginalName(),
                    'url' => $media->getUrl(),
                    'thumbnail_url' => $media->getThumbnailUrl(),
                    'size' => $media->getFormattedSize()
                ]
            ]);
            
        } catch (\Exception $e) {
            $this->jsonResponse(['error' => $e->getMessage()], 400);
        }
    }
    
    /**
     * Supprimer un média
     */
    public function delete(int $id): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['error' => 'Méthode non autorisée'], 405);
            return;
        }
        
        // Vérifier le token CSRF
        if (!\Auth::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->jsonResponse(['error' => 'Token CSRF invalide'], 403);
            return;
        }
        
        try {
            $media = \Media::find($id);
            
            if (!$media) {
                throw new \Exception('Média non trouvé');
            }
            
            // Supprimer le média
            if ($media->delete()) {
                $this->jsonResponse(['success' => true]);
            } else {
                throw new \Exception('Erreur lors de la suppression');
            }
            
        } catch (\Exception $e) {
            $this->jsonResponse(['error' => $e->getMessage()], 400);
        }
    }
    
    /**
     * Rechercher des médias
     */
    public function search(): void
    {
        $query = $_GET['q'] ?? '';
        $limit = (int)($_GET['limit'] ?? 20);
        
        if (empty($query)) {
            $medias = \Media::findAll($limit);
        } else {
            $medias = \Media::search($query, $limit);
        }
        
        $this->jsonResponse([
            'success' => true,
            'medias' => array_map(fn($media) => $media->toArray(), $medias)
        ]);
    }
    
    /**
     * Obtenir les médias par type
     */
    public function byType(): void
    {
        $type = $_GET['type'] ?? 'image';
        $limit = (int)($_GET['limit'] ?? 20);
        
        $mimeType = $type === 'image' ? 'image/' : 'video/';
        $medias = \Media::findByMimeType($mimeType, $limit);
        
        $this->jsonResponse([
            'success' => true,
            'medias' => array_map(fn($media) => $media->toArray(), $medias)
        ]);
    }
    
    /**
     * Créer une vignette pour une image
     */
    private function createThumbnail(string $filepath, string $filename): void
    {
        $uploadDir = __DIR__ . '/../../../public/uploads/';
        $thumbnailName = 'thumb_' . $filename;
        $thumbnailPath = $uploadDir . $thumbnailName;
        
        // Dimensions de la vignette
        $thumbWidth = 320;
        $thumbHeight = 240;
        
        // Obtenir les dimensions de l'image originale
        $imageInfo = getimagesize($filepath);
        if (!$imageInfo) {
            return;
        }
        
        $originalWidth = $imageInfo[0];
        $originalHeight = $imageInfo[1];
        $mimeType = $imageInfo['mime'];
        
        // Créer l'image source
        $sourceImage = null;
        switch ($mimeType) {
            case 'image/jpeg':
                $sourceImage = imagecreatefromjpeg($filepath);
                break;
            case 'image/png':
                $sourceImage = imagecreatefrompng($filepath);
                break;
            case 'image/webp':
                $sourceImage = imagecreatefromwebp($filepath);
                break;
            case 'image/gif':
                $sourceImage = imagecreatefromgif($filepath);
                break;
        }
        
        if (!$sourceImage) {
            return;
        }
        
        // Calculer les nouvelles dimensions
        $ratio = min($thumbWidth / $originalWidth, $thumbHeight / $originalHeight);
        $newWidth = (int)($originalWidth * $ratio);
        $newHeight = (int)($originalHeight * $ratio);
        
        // Créer la vignette
        $thumbnail = imagecreatetruecolor($newWidth, $newHeight);
        
        // Préserver la transparence pour PNG et GIF
        if ($mimeType === 'image/png' || $mimeType === 'image/gif') {
            imagealphablending($thumbnail, false);
            imagesavealpha($thumbnail, true);
            $transparent = imagecolorallocatealpha($thumbnail, 255, 255, 255, 127);
            imagefill($thumbnail, 0, 0, $transparent);
        }
        
        // Redimensionner
        imagecopyresampled($thumbnail, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);
        
        // Sauvegarder la vignette
        switch ($mimeType) {
            case 'image/jpeg':
                imagejpeg($thumbnail, $thumbnailPath, 85);
                break;
            case 'image/png':
                imagepng($thumbnail, $thumbnailPath, 8);
                break;
            case 'image/webp':
                imagewebp($thumbnail, $thumbnailPath, 85);
                break;
            case 'image/gif':
                imagegif($thumbnail, $thumbnailPath);
                break;
        }
        
        // Libérer la mémoire
        imagedestroy($sourceImage);
        imagedestroy($thumbnail);
    }
    
    /**
     * Stocker l'image en cache temporaire (session)
     */
    private function storeInTempCache(\Media $media): void
    {
        if (!isset($_SESSION['temp_uploads'])) {
            $_SESSION['temp_uploads'] = [];
        }
        
        // Limiter le cache à 10 images maximum
        if (count($_SESSION['temp_uploads']) >= 10) {
            array_shift($_SESSION['temp_uploads']);
        }
        
        $_SESSION['temp_uploads'][] = [
            'id' => $media->getId(),
            'filename' => $media->getFilename(),
            'url' => $media->getUrl(),
            'thumbnail_url' => $media->getThumbnailUrl(),
            'uploaded_at' => time()
        ];
    }
    
    /**
     * Récupérer les images en cache temporaire
     */
    public function getTempCache(): void
    {
        $tempUploads = $_SESSION['temp_uploads'] ?? [];
        
        // Nettoyer les anciennes entrées (plus de 1 heure)
        $tempUploads = array_filter($tempUploads, function($upload) {
            return (time() - $upload['uploaded_at']) < 3600; // 1 heure
        });
        
        $_SESSION['temp_uploads'] = $tempUploads;
        
        $this->jsonResponse([
            'success' => true,
            'uploads' => $tempUploads
        ]);
    }
    
    /**
     * Liste des médias via API
     */
    public function list(): void
    {
        try {
            $media = Database::query("
                SELECT id, filename, original_name, mime_type, size, created_at 
                FROM media 
                ORDER BY created_at DESC
            ");
            
            $mediaList = [];
            foreach ($media as $item) {
                $mediaList[] = [
                    'id' => $item['id'],
                    'filename' => $item['filename'],
                    'original_name' => $item['original_name'],
                    'mime_type' => $item['mime_type'],
                    'size' => $item['size'],
                    'url' => '/public/uploads/' . $item['filename'],
                    'created_at' => $item['created_at']
                ];
            }
            
            $this->jsonResponse(['success' => true, 'media' => $mediaList]);
            
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'message' => $e->getMessage()]);
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
