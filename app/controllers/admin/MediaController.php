<?php
declare(strict_types=1);

/**
 * Contrôleur MediaController - Gestion des médias dans l'admin
 */

namespace Admin;

require_once __DIR__ . '/../../../core/Controller.php';
require_once __DIR__ . '/../../../core/Auth.php';
require_once __DIR__ . '/../../../app/models/Media.php';
require_once __DIR__ . '/../../../app/models/Game.php';
require_once __DIR__ . '/../../../app/models/Hardware.php';

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
        
        // Charger la liste des jeux pour le dropdown
        $games = \Game::findAll(1000); // Limite élevée pour avoir tous les jeux
        
        $this->render('admin/media/index', [
            'medias' => $medias,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalMedias' => $totalMedias,
            'games' => $games
        ]);
    }
    
    /**
     * Upload d'image
     */
    public function upload(): void
    {
        // Débogage détaillé
        error_log("=== DÉBUT UPLOAD ===");
        error_log("Méthode HTTP: " . $_SERVER['REQUEST_METHOD']);
        error_log("POST data: " . print_r($_POST, true));
        error_log("FILES data: " . print_r($_FILES, true));
        
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
            // Vérifier que l'extension GD est disponible
            if (!extension_loaded('gd')) {
                error_log("Extension GD non disponible");
                throw new \Exception('Extension GD non disponible sur le serveur');
            }
            error_log("✅ Extension GD disponible: " . gd_info()['GD Version']);
            
            error_log("Vérification des fichiers uploadés...");
            if (!isset($_FILES['file'])) {
                error_log("❌ Aucun fichier dans \$_FILES['file']");
                throw new \Exception('Aucun fichier reçu');
            }
            
            $file = $_FILES['file'];
            error_log("Fichier reçu: " . $file['name'] . " (" . $file['size'] . " bytes)");
            error_log("Type MIME du navigateur: " . ($file['type'] ?? 'Non défini'));
            error_log("Code d'erreur: " . $file['error']);
            error_log("Fichier temporaire: " . $file['tmp_name']);
            
            if ($file['error'] !== UPLOAD_ERR_OK) {
                $errorMessages = [
                    UPLOAD_ERR_INI_SIZE => 'Fichier trop volumineux (php.ini)',
                    UPLOAD_ERR_FORM_SIZE => 'Fichier trop volumineux (form)',
                    UPLOAD_ERR_PARTIAL => 'Upload partiel',
                    UPLOAD_ERR_NO_FILE => 'Aucun fichier',
                    UPLOAD_ERR_NO_TMP_DIR => 'Dossier temporaire manquant',
                    UPLOAD_ERR_CANT_WRITE => 'Erreur d\'écriture',
                    UPLOAD_ERR_EXTENSION => 'Extension bloquée'
                ];
                
                $errorMsg = $errorMessages[$file['error']] ?? 'Erreur inconnue: ' . $file['error'];
                error_log("❌ Erreur upload: " . $errorMsg);
                throw new \Exception('Erreur lors de l\'upload: ' . $errorMsg);
            }
            
            // Vérifier que le fichier temporaire existe
            if (!file_exists($file['tmp_name'])) {
                error_log("❌ Fichier temporaire n'existe pas: " . $file['tmp_name']);
                throw new \Exception('Fichier temporaire introuvable');
            }
            
            // Récupérer les paramètres
            $gameId = !empty($_POST['game_id']) ? (int)$_POST['game_id'] : null;
            $category = $_POST['category'] ?? 'general';
            error_log("Game ID: " . ($gameId ?? 'null') . ", Catégorie: " . $category);
            
            // Déterminer le dossier d'upload
            $uploadDir = $this->determineUploadDirectory($gameId, $category);
            error_log("Dossier upload: " . $uploadDir);
            
            // Vérifier que le dossier existe
            if (!is_dir($uploadDir)) {
                error_log("Création du dossier upload...");
                if (!mkdir($uploadDir, 0755, true)) {
                    error_log("❌ Impossible de créer le dossier: " . $uploadDir);
                    throw new \Exception('Impossible de créer le dossier d\'upload');
                }
                error_log("✅ Dossier créé: " . $uploadDir);
            }
            
            // Vérifier les permissions d'écriture
            if (!is_writable($uploadDir)) {
                error_log("❌ Dossier non écrivable: " . $uploadDir);
                throw new \Exception('Dossier d\'upload non écrivable');
            }
            error_log("✅ Dossier écrivable: " . $uploadDir);
            
            // Vérifier le type MIME
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);
            
            error_log("Type MIME détecté: " . $mimeType);
            
            // Types autorisés
            $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
            if (!in_array($mimeType, $allowedTypes)) {
                error_log("❌ Type MIME non autorisé: " . $mimeType);
                throw new \Exception('Type de fichier non autorisé. Formats acceptés : JPG, PNG, WebP, GIF');
            }
            error_log("✅ Type MIME autorisé: " . $mimeType);
            
            // Vérifier la taille (4MB max)
            $maxSize = 4 * 1024 * 1024;
            $phpMaxUpload = ini_get('upload_max_filesize');
            $phpMaxPost = ini_get('post_max_size');
            
            if ($file['size'] > $maxSize) {
                error_log("❌ Fichier trop volumineux: " . $file['size'] . " > " . $maxSize);
                throw new \Exception('Fichier trop volumineux (max 4MB)');
            }
            
            // Vérifier les limites PHP
            if ($file['size'] > $this->parseSize($phpMaxUpload)) {
                error_log("❌ Fichier dépasse la limite PHP upload_max_filesize: " . $phpMaxUpload);
                throw new \Exception("Fichier trop volumineux. Limite PHP: {$phpMaxUpload}. Contactez l'administrateur.");
            }
            
            if ($file['size'] > $this->parseSize($phpMaxPost)) {
                error_log("❌ Fichier dépasse la limite PHP post_max_size: " . $phpMaxPost);
                throw new \Exception("Fichier trop volumineux. Limite PHP: {$phpMaxPost}. Contactez l'administrateur.");
            }
            
            error_log("✅ Taille OK: " . $file['size'] . " bytes (Limite PHP: {$phpMaxUpload})");
            
            // Générer un nom de fichier unique
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = uniqid() . '_' . time() . '.' . $extension;
            $filepath = $uploadDir . '/' . $filename;
            error_log("Nom de fichier généré: " . $filename);
            error_log("Chemin complet: " . $filepath);
            
            // Déplacer le fichier
            error_log("Tentative de déplacement du fichier...");
            if (!move_uploaded_file($file['tmp_name'], $filepath)) {
                $lastError = error_get_last();
                error_log("❌ Erreur lors du déplacement: " . ($lastError['message'] ?? 'Erreur inconnue'));
                throw new \Exception('Erreur lors du déplacement du fichier: ' . ($lastError['message'] ?? 'Erreur inconnue'));
            }
            error_log("✅ Fichier déplacé avec succès");
            
            // Vérifier que le fichier existe maintenant
            if (!file_exists($filepath)) {
                error_log("❌ Fichier n'existe pas après déplacement: " . $filepath);
                throw new \Exception('Fichier introuvable après upload');
            }
            
            // Créer la vignette si c'est une image
            try {
                error_log("Création de la vignette...");
                $this->createThumbnail($filepath, $filename, $uploadDir);
                error_log("✅ Vignette créée avec succès");
            } catch (\Exception $e) {
                error_log("⚠️ Erreur création vignette: " . $e->getMessage());
                // Continuer sans vignette
            }
            
            // Enregistrer en base de données
            $mediaData = [
                'filename' => $this->getRelativePath($filepath),
                'original_name' => $file['name'],
                'mime_type' => $mimeType,
                'size' => $file['size'],
                'uploaded_by' => \Auth::getUserId(),
                'game_id' => $gameId,
                'media_type' => $category
            ];
            
            error_log("Tentative d'enregistrement en base de données...");
            error_log("Données média: " . print_r($mediaData, true));
            
            $media = \Media::create($mediaData);
            
            if (!$media) {
                error_log("❌ Échec de l'enregistrement en base de données");
                throw new \Exception('Erreur lors de l\'enregistrement en base de données');
            }
            
            error_log("✅ Média enregistré avec succès, ID: " . $media->getId());
            
            // Stocker en cache temporaire (session)
            $this->storeInTempCache($media);
            
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
                    'category' => $category
                ]
            ];
            
            error_log("✅ Upload réussi, réponse: " . json_encode($response));
            $this->jsonResponse($response);
            
        } catch (\Exception $e) {
            error_log("❌ ERREUR UPLOAD: " . $e->getMessage());
            error_log("Trace: " . $e->getTraceAsString());
            $this->jsonResponse(['error' => $e->getMessage()], 400);
        }
        
        error_log("=== FIN UPLOAD ===");
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
        
        // Image générale - organiser par mois/année
        $currentMonth = date('m-Y');
        return $baseDir . 'general/' . $category . '/' . $currentMonth;
    }
    
    /**
     * Obtenir le chemin relatif pour la base de données
     */
    private function getRelativePath(string $fullPath): string
    {
        $uploadsDir = __DIR__ . '/../../../public/uploads/';
        return str_replace($uploadsDir, '', $fullPath);
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
     * Créer une vignette pour une image
     */
    private function createThumbnail(string $filepath, string $filename, string $uploadDir): void
    {
        error_log("Création vignette pour: " . $filename);
        
        $thumbnailName = 'thumb_' . $filename;
        $thumbnailPath = $uploadDir . '/' . $thumbnailName;
        
        // Dimensions de la vignette
        $thumbWidth = 320;
        $thumbHeight = 240;
        
        // Obtenir les dimensions de l'image originale
        $imageInfo = getimagesize($filepath);
        if (!$imageInfo) {
            error_log("Impossible de lire les dimensions de l'image: " . $filepath);
            return;
        }
        
        error_log("Type MIME détecté: " . $imageInfo['mime']);
        
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
                error_log("Tentative de création d'image PNG depuis: " . $filepath);
                $sourceImage = imagecreatefrompng($filepath);
                if (!$sourceImage) {
                    error_log("Échec de création d'image PNG. Erreur GD: " . error_get_last()['message'] ?? 'Inconnue');
                }
                break;
            case 'image/webp':
                $sourceImage = imagecreatefromwebp($filepath);
                break;
            case 'image/gif':
                $sourceImage = imagecreatefromgif($filepath);
                break;
        }
        
        if (!$sourceImage) {
            error_log("Impossible de créer l'image source pour: " . $filepath);
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
                error_log("Sauvegarde vignette PNG vers: " . $thumbnailPath);
                $result = imagepng($thumbnail, $thumbnailPath, 8);
                if (!$result) {
                    error_log("Échec de sauvegarde vignette PNG. Erreur GD: " . error_get_last()['message'] ?? 'Inconnue');
                } else {
                    error_log("Vignette PNG sauvegardée avec succès");
                }
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

    /**
     * Parse la taille en octets depuis une chaîne PHP (ex: 10M, 100K, 1G)
     */
    private function parseSize(string $size): int
    {
        $unit = strtolower(substr($size, -1));
        $value = (int)substr($size, 0, -1);

        switch ($unit) {
            case 'k':
                return $value * 1024;
            case 'm':
                return $value * 1024 * 1024;
            case 'g':
                return $value * 1024 * 1024 * 1024;
            default:
                return $value;
        }
    }
}
