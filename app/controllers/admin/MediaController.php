<?php
declare(strict_types=1);

/**
 * Contrôleur MediaController - Gestion des médias dans l'admin
 * Version améliorée avec gestion d'erreurs avancée
 */

namespace Admin;

require_once __DIR__ . '/../../../core/Controller.php';
require_once __DIR__ . '/../../../core/Auth.php';
require_once __DIR__ . '/../../../app/models/Media.php';
require_once __DIR__ . '/../../../app/models/Game.php';
require_once __DIR__ . '/../../../app/models/Hardware.php';
require_once __DIR__ . '/../../../app/utils/ImageOptimizer.php';
require_once __DIR__ . '/../../../app/helpers/security_helper.php';

class MediaController extends \Controller
{
    // Configuration des types de fichiers autorisés
    private const ALLOWED_MIME_TYPES = [
        'image/jpeg' => ['jpg', 'jpeg'],
        'image/png' => ['png'],
        'image/webp' => ['webp'],
        'image/gif' => ['gif']
    ];
    
    // Limites de taille
    private const MAX_FILE_SIZE = 4 * 1024 * 1024; // 4MB
    private const MAX_DIMENSIONS = 4096; // 4096x4096 pixels max
    
    // Messages d'erreur contextuels avec solutions
    private const ERROR_MESSAGES = [
        'upload_failed' => [
            'message' => 'L\'upload a échoué',
            'solution' => 'Vérifiez votre connexion internet et réessayez',
            'code' => 'UPLOAD_001'
        ],
        'file_too_large' => [
            'message' => 'Fichier trop volumineux',
            'solution' => 'Compressez votre image ou réduisez sa résolution (max 4MB)',
            'code' => 'SIZE_001'
        ],
        'invalid_type' => [
            'message' => 'Type de fichier non supporté',
            'solution' => 'Utilisez JPG, PNG, WebP ou GIF',
            'code' => 'TYPE_001'
        ],
        'dimensions_too_large' => [
            'message' => 'Dimensions d\'image trop grandes',
            'solution' => 'Réduisez la résolution (max 4096x4096 pixels)',
            'code' => 'DIM_001'
        ],
        'gd_missing' => [
            'message' => 'Extension GD non disponible',
            'solution' => 'Contactez l\'administrateur du serveur',
            'code' => 'SYS_001'
        ],
        'directory_error' => [
            'message' => 'Erreur de dossier',
            'solution' => 'Vérifiez les permissions du serveur',
            'code' => 'DIR_001'
        ],
        'database_error' => [
            'message' => 'Erreur de base de données',
            'solution' => 'Réessayez dans quelques minutes',
            'code' => 'DB_001'
        ]
    ];

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
        
        // Vérifier si on est en mode sélection
        $selectMode = isset($_GET['select_mode']) && $_GET['select_mode'] == '1';
        
        $medias = \Media::findAll($limit, $offset);
        $totalMedias = \Media::count();
        $totalPages = ceil($totalMedias / $limit);
        
        // Charger la liste des jeux pour le dropdown
        $games = \Game::findAll(1000); // Limite élevée pour avoir tous les jeux
        
        $this->render('admin/media/index', [
            'medias' => $medias,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalMedias' => $totalMedias,
            'games' => $games,
            'selectMode' => $selectMode
        ]);
    }
    
    /**
     * Upload d'image avec gestion d'erreurs avancée
     */
    public function upload(): void
    {
        // Débogage détaillé
        error_log("=== DÉBUT UPLOAD ===");
        error_log("Méthode HTTP: " . $_SERVER['REQUEST_METHOD']);
        error_log("POST data: " . print_r($_POST, true));
        error_log("FILES data: " . print_r($_FILES, true));
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse($this->formatError('upload_failed', 'Méthode non autorisée'), 405);
            return;
        }
        
        // Vérifier le token CSRF
        if (!\Auth::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->jsonResponse($this->formatError('upload_failed', 'Token CSRF invalide'), 403);
            return;
        }
        
        try {
            // Validation de base
            $this->validateUploadBasics();
            
            // Récupérer et valider le fichier
            $file = $this->getAndValidateFile();
            
            // Récupérer les paramètres
            $gameId = !empty($_POST['game_id']) ? (int)$_POST['game_id'] : null;
            $category = $_POST['category'] ?? 'general';
            
            // Déterminer le dossier d'upload
            $uploadDir = $this->determineUploadDirectory($gameId, $category);
            
            // Créer le dossier si nécessaire
            $this->ensureUploadDirectory($uploadDir);
            
            // Générer un nom de fichier unique et sécurisé
            $filename = $this->generateSecureFilename($file['name']);
            $filepath = $uploadDir . '/' . $filename;
            
            // Déplacer le fichier
            $this->moveUploadedFile($file['tmp_name'], $filepath);
            
            // OPTIMISATION AUTOMATIQUE - Nouveau !
            $optimizationResult = null;
            $originalFilepath = $filepath;
            
            // Vérifier si c'est une image à optimiser
            if (str_starts_with($file['type'], 'image/')) {
                error_log("🔄 Début de l'optimisation pour: " . $file['name']);
                $baseName = pathinfo($filename, PATHINFO_FILENAME);
                $optimizationResult = $this->optimizeImage($filepath, $uploadDir, $baseName);
                error_log("📊 Résultat d'optimisation: " . json_encode($optimizationResult));
                
                if ($optimizationResult['success']) {
                    // Utiliser l'image optimisée WebP comme fichier principal si disponible
                    $webpPath = $optimizationResult['files']['webp'] ?? null;
                    if ($webpPath && file_exists($webpPath)) {
                        $filepath = $webpPath;
                        $filename = basename($webpPath);
                        error_log("✅ Utilisation de l'image WebP optimisée: " . $filename);
                    } else {
                        // Fallback sur JPG si WebP n'est pas disponible
                        $jpgPath = $optimizationResult['files']['jpg'] ?? null;
                        if ($jpgPath && file_exists($jpgPath)) {
                            $filepath = $jpgPath;
                            $filename = basename($jpgPath);
                            error_log("✅ Utilisation de l'image JPG optimisée: " . $filename);
                        }
                    }
                    
                    // Nettoyer le fichier original si l'optimisation a réussi
                    if (file_exists($originalFilepath) && $originalFilepath !== $filepath) {
                        unlink($originalFilepath);
                        error_log("🗑️ Fichier original supprimé après optimisation");
                    }
                } else {
                    error_log("⚠️ L'optimisation a échoué, utilisation du fichier original");
                    error_log("⚠️ Erreur d'optimisation: " . ($optimizationResult['error'] ?? 'Erreur inconnue'));
                }
            }
            
            // Créer la vignette depuis l'image optimisée (ou originale si échec)
            $thumbnailCreated = $this->createThumbnail($filepath, $filename, $uploadDir);
            
            // Enregistrer en base de données avec le chemin complet
            $media = $this->saveMediaToDatabase($file, $filepath, $gameId, $category);
            
            // Stocker en cache temporaire
            $this->storeInTempCache($media);
            
            // Réponse de succès
            $response = [
                'success' => true,
                'media' => [
                    'id' => $media->getId(),
                    'filename' => $media->getFilename(),
                    'original_name' => $media->getOriginalName(),
                    'url' => $media->getUrl(),
                    'thumbnail_url' => $media->getThumbnailUrl(),
                    'size' => $media->getFormattedSize(),
                    'game_id' => $gameId,
                    'category' => $category,
                    'thumbnail_created' => $thumbnailCreated
                ],
                'message' => 'Fichier uploadé avec succès !',
                'upload_info' => [
                    'file_size' => $this->formatBytes($file['size']),
                    'dimensions' => $this->getImageDimensions($filepath),
                    'upload_time' => date('Y-m-d H:i:s')
                ]
            ];
            
            // Ajouter les informations d'optimisation si disponible
            if ($optimizationResult && $optimizationResult['success']) {
                $response['optimization'] = [
                    'success' => true,
                    'compression_ratio' => $optimizationResult['compression_ratio'] . '%',
                    'original_size' => $this->formatBytes($optimizationResult['original_size']),
                    'optimized_size' => $this->formatBytes($optimizationResult['optimized_size']),
                    'space_saved' => $this->formatBytes($optimizationResult['original_size'] - $optimizationResult['optimized_size']),
                    'formats_available' => $optimizationResult['formats'],
                    'message' => '🎉 Image optimisée avec succès ! Gain de ' . $optimizationResult['compression_ratio'] . '%'
                ];
            } elseif ($optimizationResult && !$optimizationResult['success']) {
                $response['optimization'] = [
                    'success' => false,
                    'error' => $optimizationResult['error'],
                    'message' => '⚠️ L\'optimisation a échoué, mais l\'upload a réussi'
                ];
            }
            
            error_log("✅ Upload réussi, réponse: " . json_encode($response));
            $this->jsonResponse($response);
            
        } catch (\Exception $e) {
            $errorCode = $this->determineErrorCode($e);
            $errorData = $this->formatError($errorCode, $e->getMessage());
            
            error_log("❌ ERREUR UPLOAD: " . $e->getMessage());
            error_log("Code d'erreur: " . $errorCode);
            error_log("Trace: " . $e->getTraceAsString());
            
            $this->jsonResponse($errorData, 400);
        }
        
        error_log("=== FIN UPLOAD ===");
    }
    
    /**
     * Validation de base de l'upload
     */
    private function validateUploadBasics(): void
    {
        // Vérifier que l'extension GD est disponible
        if (!extension_loaded('gd')) {
            throw new \Exception('Extension GD non disponible sur le serveur', 500);
        }
        
        // Vérifier que des fichiers ont été uploadés
        if (!isset($_FILES['file'])) {
            throw new \Exception('Aucun fichier reçu', 400);
        }
        
        error_log("✅ Validation de base réussie");
    }
    
    /**
     * Récupérer et valider le fichier
     */
    private function getAndValidateFile(): array
    {
        $file = $_FILES['file'];
        
        // Vérifier les erreurs d'upload
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errorMessage = $this->getUploadErrorMessage($file['error']);
            throw new \Exception($errorMessage, 400);
        }
        
        // Vérifier que le fichier temporaire existe
        if (!file_exists($file['tmp_name'])) {
            throw new \Exception('Fichier temporaire introuvable', 400);
        }
        
        // Vérifier le type MIME avec validation renforcée
        $mimeType = \SecurityHelper::getRealMimeType($file['tmp_name']);
        if (!\SecurityHelper::validateImageMimeType($mimeType)) {
            throw new \Exception('Type de fichier non autorisé. Formats acceptés : JPG, PNG, WebP, GIF', 400);
        }
        
        // Vérifier la taille avec validation renforcée
        if (!\SecurityHelper::validateFileSize($file['size'], self::MAX_FILE_SIZE)) {
            throw new \Exception('Fichier trop volumineux (max 4MB)', 400);
        }
        
        // Vérifier les dimensions si c'est une image
        if (!\SecurityHelper::validateImageDimensions($file['tmp_name'], self::MAX_DIMENSIONS, self::MAX_DIMENSIONS)) {
            throw new \Exception('Dimensions d\'image trop grandes (max 4096x4096 pixels)', 400);
        }
        
        error_log("✅ Fichier validé: " . $file['name'] . " (" . $file['size'] . " bytes, " . $mimeType . ")");
        
        return $file;
    }
    
    /**
     * Obtenir le type MIME d'un fichier
     */
    private function getFileMimeType(string $filepath): string
    {
        // Vérifier que le fichier existe
        if (!file_exists($filepath)) {
            error_log("⚠️ Fichier introuvable pour getFileMimeType: " . $filepath);
            // Fallback sur l'extension du fichier
            $extension = strtolower(pathinfo($filepath, PATHINFO_EXTENSION));
            $extensionMap = [
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
                'webp' => 'image/webp',
                'bmp' => 'image/bmp'
            ];
            return $extensionMap[$extension] ?? 'application/octet-stream';
        }
        
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $filepath);
        finfo_close($finfo);
        
        // Si finfo_file échoue, utiliser l'extension
        if ($mimeType === false) {
            error_log("⚠️ finfo_file échoué pour: " . $filepath);
            $extension = strtolower(pathinfo($filepath, PATHINFO_EXTENSION));
            $extensionMap = [
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
                'webp' => 'image/webp',
                'bmp' => 'image/bmp'
            ];
            return $extensionMap[$extension] ?? 'application/octet-stream';
        }
        
        return $mimeType;
    }
    
    /**
     * Valider les dimensions d'une image
     */
    private function validateImageDimensions(string $filepath, string $mimeType): void
    {
        if (!str_starts_with($mimeType, 'image/')) {
            return; // Pas une image
        }
        
        $imageInfo = getimagesize($filepath);
        if (!$imageInfo) {
            throw new \Exception('Impossible de lire les dimensions de l\'image', 400);
        }
        
        $width = $imageInfo[0];
        $height = $imageInfo[1];
        
        if ($width > self::MAX_DIMENSIONS || $height > self::MAX_DIMENSIONS) {
            throw new \Exception('Dimensions d\'image trop grandes (max 4096x4096 pixels)', 400);
        }
        
        error_log("✅ Dimensions validées: {$width}x{$height} pixels");
    }
    
    /**
     * Obtenir les dimensions d'une image
     */
    private function getImageDimensions(string $filepath): ?array
    {
        $imageInfo = getimagesize($filepath);
        if (!$imageInfo) {
            return null;
        }
        
        return [
            'width' => $imageInfo[0],
            'height' => $imageInfo[1],
            'aspect_ratio' => round($imageInfo[0] / $imageInfo[1], 2)
        ];
    }
    
    /**
     * Déterminer le dossier d'upload selon le jeu et la catégorie
     */
    private function determineUploadDirectory(?int $gameId, string $category): string
    {
        $baseDir = __DIR__ . '/../../../public/uploads/';
        
        if ($gameId) {
            // Image liée à un jeu
            $game = \Game::find($gameId);
            if ($game) {
                return $baseDir . 'games/' . $game->getSlug();
            }
        }
        
        // Image classique - utiliser le dossier article
        return $baseDir . 'article';
    }
    
    /**
     * S'assurer que le dossier d'upload existe
     */
    private function ensureUploadDirectory(string $uploadDir): void
    {
        if (!is_dir($uploadDir)) {
            error_log("Création du dossier upload: " . $uploadDir);
            if (!mkdir($uploadDir, 0755, true)) {
                throw new \Exception('Impossible de créer le dossier d\'upload', 500);
            }
            error_log("✅ Dossier créé: " . $uploadDir);
        }
        
        if (!is_writable($uploadDir)) {
            throw new \Exception('Dossier d\'upload non écrivable', 500);
        }
        
        error_log("✅ Dossier upload prêt: " . $uploadDir);
    }
    
    /**
     * Générer un nom de fichier sécurisé
     */
    private function generateSecureFilename(string $originalName): string
    {
        return \SecurityHelper::generateSecureFilename($originalName);
    }
    
    /**
     * Déplacer le fichier uploadé
     */
    private function moveUploadedFile(string $tmpPath, string $destination): void
    {
        // Vérifier si c'est un vrai fichier uploadé
        if (is_uploaded_file($tmpPath)) {
            // Fichier uploadé via HTTP - utiliser move_uploaded_file
            if (!move_uploaded_file($tmpPath, $destination)) {
                $lastError = error_get_last();
                $errorMessage = $lastError['message'] ?? 'Erreur inconnue lors du déplacement';
                throw new \Exception('Erreur lors du déplacement du fichier: ' . $errorMessage, 500);
            }
        } else {
            // Fichier non-uploadé (test, import, etc.) - utiliser copy
            if (!copy($tmpPath, $destination)) {
                $lastError = error_get_last();
                $errorMessage = $lastError['message'] ?? 'Erreur inconnue lors de la copie';
                throw new \Exception('Erreur lors de la copie du fichier: ' . $errorMessage, 500);
            }
        }
        
        if (!file_exists($destination)) {
            throw new \Exception('Fichier introuvable après déplacement/copie', 500);
        }
        
        error_log("✅ Fichier traité avec succès vers: " . $destination);
    }
    
    /**
     * Optimiser automatiquement une image avec conversion WebP
     */
    private function optimizeImage(string $filepath, string $uploadDir, string $baseName): array
    {
        try {
            error_log("🔄 Début de l'optimisation de l'image: " . $baseName);
            
            // Utiliser notre classe ImageOptimizer
            $optimizationResult = \ImageOptimizer::optimizeImage($filepath, $uploadDir);
            
            if (!$optimizationResult['success']) {
                error_log("⚠️ Échec de l'optimisation: " . ($optimizationResult['error'] ?? 'Erreur inconnue'));
                return [
                    'success' => false,
                    'error' => $optimizationResult['error'] ?? 'Erreur d\'optimisation',
                    'files' => []
                ];
            }
            
            error_log("✅ Optimisation réussie ! Compression: " . $optimizationResult['compression_ratio'] . "%");
            error_log("📊 Taille originale: " . $this->formatBytes($optimizationResult['original_size']));
            error_log("📊 Taille optimisée: " . $this->formatBytes($optimizationResult['optimized_size']));
            
            return $optimizationResult;
            
        } catch (\Exception $e) {
            error_log("❌ Erreur lors de l'optimisation: " . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'files' => []
            ];
        }
    }
    
    /**
     * Créer une vignette pour une image
     */
    private function createThumbnail(string $filepath, string $filename, string $uploadDir): bool
    {
        try {
            error_log("Création de la vignette pour: " . $filename);
            
            $thumbnailName = 'thumb_' . $filename;
            $thumbnailPath = $uploadDir . '/' . $thumbnailName;
            
            // Dimensions de la vignette
            $thumbWidth = 320;
            $thumbHeight = 240;
            
            // Obtenir les dimensions de l'image originale
            $imageInfo = getimagesize($filepath);
            if (!$imageInfo) {
                error_log("⚠️ Impossible de lire les dimensions de l'image");
                return false;
            }
            
            $originalWidth = $imageInfo[0];
            $originalHeight = $imageInfo[1];
            $mimeType = $imageInfo['mime'];
            
            // Créer l'image source
            $sourceImage = $this->createSourceImage($filepath, $mimeType);
            if (!$sourceImage) {
                error_log("⚠️ Impossible de créer l'image source");
                return false;
            }
            
            // Calculer les nouvelles dimensions
            $ratio = min($thumbWidth / $originalWidth, $thumbHeight / $originalHeight);
            $newWidth = (int)($originalWidth * $ratio);
            $newHeight = (int)($originalHeight * $ratio);
            
            // Créer la vignette
            $thumbnail = $this->createThumbnailImage($newWidth, $newHeight, $mimeType);
            
            // Redimensionner
            imagecopyresampled($thumbnail, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);
            
            // Sauvegarder la vignette
            $this->saveThumbnail($thumbnail, $thumbnailPath, $mimeType);
            
            // Libérer la mémoire
            imagedestroy($sourceImage);
            imagedestroy($thumbnail);
            
            error_log("✅ Vignette créée avec succès: " . $thumbnailPath);
            return true;
            
        } catch (\Exception $e) {
            error_log("⚠️ Erreur création vignette: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Créer l'image source selon le type MIME
     */
    private function createSourceImage(string $filepath, string $mimeType)
    {
        switch ($mimeType) {
            case 'image/jpeg':
                return imagecreatefromjpeg($filepath);
            case 'image/png':
                return imagecreatefrompng($filepath);
            case 'image/webp':
                return imagecreatefromwebp($filepath);
            case 'image/gif':
                return imagecreatefromgif($filepath);
            default:
                return null;
        }
    }
    
    /**
     * Créer l'image de vignette
     */
    private function createThumbnailImage(int $width, int $height, string $mimeType)
    {
        $thumbnail = imagecreatetruecolor($width, $height);
        
        // Préserver la transparence pour PNG et GIF
        if ($mimeType === 'image/png' || $mimeType === 'image/gif') {
            imagealphablending($thumbnail, false);
            imagesavealpha($thumbnail, true);
            $transparent = imagecolorallocatealpha($thumbnail, 255, 255, 255, 127);
            imagefill($thumbnail, 0, 0, $transparent);
        }
        
        return $thumbnail;
    }
    
    /**
     * Sauvegarder la vignette
     */
    private function saveThumbnail($thumbnail, string $path, string $mimeType): void
    {
        switch ($mimeType) {
            case 'image/jpeg':
                imagejpeg($thumbnail, $path, 85);
                break;
            case 'image/png':
                imagepng($thumbnail, $path, 8);
                break;
            case 'image/webp':
                imagewebp($thumbnail, $path, 85);
                break;
            case 'image/gif':
                imagegif($thumbnail, $path);
                break;
        }
    }
    
    /**
     * Sauvegarder le média en base de données
     */
    private function saveMediaToDatabase(array $file, string $filename, ?int $gameId, string $category): \Media
    {
        // Déterminer le type MIME du fichier final (optimisé) ou utiliser celui du fichier original
        $finalMimeType = $this->getFileMimeType($filename);
        if (!$finalMimeType || $finalMimeType === 'application/octet-stream') {
            // Fallback sur le type MIME du fichier original
            $finalMimeType = $file['type'];
        }
        
        // Déterminer la taille du fichier final
        $finalSize = $file['size']; // Taille originale par défaut
        if (file_exists($filename)) {
            $finalSize = filesize($filename);
        }
        
        $mediaData = [
            'filename' => $this->getRelativePath($filename),
            'original_name' => $file['name'],
            'mime_type' => $finalMimeType,
            'size' => $finalSize,
            'uploaded_by' => \Auth::getUserId(),
            'game_id' => $gameId,
            'media_type' => $category
        ];
        
        error_log("Tentative d'enregistrement en base de données...");
        error_log("Données média: " . print_r($mediaData, true));
        
        $media = \Media::create($mediaData);
        
        if (!$media) {
            throw new \Exception('Erreur lors de l\'enregistrement en base de données', 500);
        }
        
        error_log("✅ Média enregistré avec succès, ID: " . $media->getId());
        return $media;
    }
    
    /**
     * Obtenir le chemin relatif pour la base de données
     */
    private function getRelativePath(string $filename): string
    {
        $uploadsDir = __DIR__ . '/../../../public/uploads/';
        $relativePath = str_replace($uploadsDir, '', $filename);
        // Stocker le chemin relatif complet (avec les sous-dossiers)
        return $relativePath;
    }
    
    /**
     * Stocker en cache temporaire
     */
    private function storeInTempCache(\Media $media): void
    {
        if (!isset($_SESSION['temp_media'])) {
            $_SESSION['temp_media'] = [];
        }
        
        $_SESSION['temp_media'][] = $media->getId();
        
        // Limiter à 10 éléments
        if (count($_SESSION['temp_media']) > 10) {
            array_shift($_SESSION['temp_media']);
        }
    }
    
    /**
     * Obtenir le message d'erreur d'upload
     */
    private function getUploadErrorMessage(int $errorCode): string
    {
        $errorMessages = [
            UPLOAD_ERR_INI_SIZE => 'Fichier trop volumineux (limite php.ini)',
            UPLOAD_ERR_FORM_SIZE => 'Fichier trop volumineux (limite formulaire)',
            UPLOAD_ERR_PARTIAL => 'Upload partiel - fichier corrompu',
            UPLOAD_ERR_NO_FILE => 'Aucun fichier reçu',
            UPLOAD_ERR_NO_TMP_DIR => 'Dossier temporaire manquant sur le serveur',
            UPLOAD_ERR_CANT_WRITE => 'Erreur d\'écriture sur le serveur',
            UPLOAD_ERR_EXTENSION => 'Extension bloquée par le serveur'
        ];
        
        return $errorMessages[$errorCode] ?? 'Erreur d\'upload inconnue (code: ' . $errorCode . ')';
    }
    
    /**
     * Déterminer le code d'erreur selon l'exception
     */
    private function determineErrorCode(\Exception $e): string
    {
        $message = strtolower($e->getMessage());
        
        if (strpos($message, 'gd') !== false) return 'gd_missing';
        if (strpos($message, 'taille') !== false || strpos($message, 'volumineux') !== false) return 'file_too_large';
        if (strpos($message, 'type') !== false || strpos($message, 'format') !== false) return 'invalid_type';
        if (strpos($message, 'dimensions') !== false) return 'dimensions_too_large';
        if (strpos($message, 'dossier') !== false || strpos($message, 'directory') !== false) return 'directory_error';
        if (strpos($message, 'base de données') !== false || strpos($message, 'database') !== false) return 'database_error';
        
        return 'upload_failed';
    }
    
    /**
     * Formater une erreur avec message et solution
     */
    private function formatError(string $errorCode, string $customMessage = ''): array
    {
        $errorData = self::ERROR_MESSAGES[$errorCode] ?? self::ERROR_MESSAGES['upload_failed'];
        
        return [
            'success' => false,
            'error' => [
                'message' => $customMessage ?: $errorData['message'],
                'solution' => $errorData['solution'],
                'code' => $errorData['code'],
                'timestamp' => date('Y-m-d H:i:s'),
                'request_id' => uniqid('req_', true)
            ]
        ];
    }
    
    /**
     * Formater les bytes en unités lisibles
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }
    
    /**
     * Rechercher des jeux pour l'autocomplétion
     */
    public function searchGames(): void
    {
        try {
            $query = $_GET['q'] ?? '';
            $limit = (int)($_GET['limit'] ?? 10);
            
            error_log("MediaController::searchGames() - Query: '{$query}', Limit: {$limit}");
            
            if (empty($query)) {
                $games = \Game::findAll($limit);
                error_log("MediaController::searchGames() - Recherche sans query, trouvé " . count($games) . " jeux");
            } else {
                $games = \Game::search($query, $limit);
                error_log("MediaController::searchGames() - Recherche avec query '{$query}', trouvé " . count($games) . " jeux");
            }
            
            $results = array_map(function($game) {
                return [
                    'id' => $game->getId(),
                    'title' => $game->getTitle(),
                    'slug' => $game->getSlug(),
                    'hardware' => $game->getHardwareName() ?? 'Aucun hardware'
                ];
            }, $games);
            
            error_log("MediaController::searchGames() - Résultats préparés: " . json_encode($results));
            
            $this->jsonResponse([
                'success' => true,
                'games' => $results,
                'query' => $query,
                'count' => count($results)
            ]);
            
        } catch (\Exception $e) {
            error_log("MediaController::searchGames() - Erreur: " . $e->getMessage());
            error_log("MediaController::searchGames() - Trace: " . $e->getTraceAsString());
            
            $this->jsonResponse([
                'success' => false,
                'error' => 'Erreur lors de la recherche: ' . $e->getMessage(),
                'query' => $query ?? '',
                'games' => []
            ], 500);
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
        $gameId = !empty($_GET['game_id']) ? (int)$_GET['game_id'] : null;
        $category = $_GET['category'] ?? '';
        $type = $_GET['type'] ?? '';
        
        try {
            // Utiliser la nouvelle méthode avec filtres
            $filters = [];
            if (!empty($query)) $filters['query'] = $query;
            if ($gameId) $filters['game_id'] = $gameId;
            if (!empty($category)) $filters['category'] = $category;
            if (!empty($type)) $filters['type'] = $type;
            
            $medias = \Media::searchWithFilters($filters, $limit);
            $total = \Media::countWithFilters($filters);
            
            $this->jsonResponse([
                'success' => true,
                'medias' => array_map(fn($media) => $media->toArray(), $medias),
                'total' => $total,
                'limit' => $limit,
                'filters_applied' => $filters
            ]);
            
        } catch (\Exception $e) {
            error_log("Erreur lors de la recherche de médias: " . $e->getMessage());
            $this->jsonResponse([
                'success' => false,
                'error' => 'Erreur lors de la recherche: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Obtenir les médias par type
     */
    public function byType(): void
    {
        $type = $_GET['type'] ?? 'image';
        $limit = (int)($_GET['limit'] ?? 20);
        $gameId = !empty($_GET['game_id']) ? (int)$_GET['game_id'] : null;
        $category = $_GET['category'] ?? '';
        
        try {
            $filters = ['type' => $type];
            if ($gameId) $filters['game_id'] = $gameId;
            if (!empty($category)) $filters['category'] = $category;
            
            $medias = \Media::searchWithFilters($filters, $limit);
            $total = \Media::countWithFilters($filters);
            
            $this->jsonResponse([
                'success' => true,
                'medias' => array_map(fn($media) => $media->toArray(), $medias),
                'total' => $total,
                'type' => $type,
                'filters_applied' => $filters
            ]);
            
        } catch (\Exception $e) {
            error_log("Erreur lors de la récupération des médias par type: " . $e->getMessage());
            $this->jsonResponse([
                'success' => false,
                'error' => 'Erreur lors de la récupération: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtenir un média par ID
     */
    public function get(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        
        if ($id <= 0) {
            $this->jsonResponse([
                'success' => false,
                'error' => 'ID invalide'
            ], 400);
            return;
        }
        
        try {
            $media = \Media::findById($id);
            
            if (!$media) {
                $this->jsonResponse([
                    'success' => false,
                    'error' => 'Média non trouvé'
                ], 404);
                return;
            }
            
            $this->jsonResponse([
                'success' => true,
                'media' => $media->toArray()
            ]);
            
        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtenir tous les jeux pour les filtres
     */
    public function getGames(): void
    {
        try {
            $limit = (int)($_GET['limit'] ?? 1000);
            $games = \Game::findAll($limit);
            
            $gamesList = [];
            foreach ($games as $game) {
                $gamesList[] = [
                    'id' => $game->getId(),
                    'title' => $game->getTitle(),
                    'slug' => $game->getSlug(),
                    'hardware_name' => $game->getHardwareName()
                ];
            }
            
            $this->jsonResponse([
                'success' => true,
                'games' => $gamesList
            ]);
            
        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
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
