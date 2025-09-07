<?php
declare(strict_types=1);

/**
 * API d'upload des médias
 */

// Augmenter les limites pour les gros fichiers
ini_set('memory_limit', '512M');
ini_set('max_execution_time', 600); // 10 minutes
set_time_limit(600);
ini_set('max_input_time', 600);

// Vérifier les limites actuelles
$uploadLimit = ini_get('upload_max_filesize');
$postLimit = ini_get('post_max_size');

error_log("📊 Limites actuelles - upload: $uploadLimit, post: $postLimit");

// Si les limites sont trop faibles, essayer de les augmenter
if (convertToBytes($uploadLimit) < 10 * 1024 * 1024) {
    ini_set('upload_max_filesize', '15M');
    error_log("⚠️ Limite upload augmentée à 20M");
}

if (convertToBytes($postLimit) < 15 * 1024 * 1024) {
    ini_set('post_max_size', '20M');
    error_log("⚠️ Limite post augmentée à 25M");
}

function convertToBytes($val) {
    $val = trim($val);
    $last = strtolower($val[strlen($val)-1]);
    $val = (int)$val;
    switch($last) {
        case 'g': $val *= 1024;
        case 'm': $val *= 1024;
        case 'k': $val *= 1024;
    }
    return $val;
}

function formatBytes($bytes, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
        $bytes /= 1024;
    }
    return round($bytes, $precision) . ' ' . $units[$i];
}

// Gestion d'erreur globale
set_error_handler(function($severity, $message, $file, $line) {
    error_log("Erreur API Upload: $message dans $file ligne $line");
    if (!(error_reporting() & $severity)) {
        return false;
    }
    throw new ErrorException($message, 0, $severity, $file, $line);
});

// Gestion des exceptions non capturées
set_exception_handler(function($exception) {
    error_log("Exception API Upload: " . $exception->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Erreur interne du serveur']);
    exit;
});

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/core/Database.php';
require_once __DIR__ . '/core/Auth.php';
require_once __DIR__ . '/app/utils/ImageOptimizer.php';
require_once __DIR__ . '/app/models/Game.php';

// Headers pour éviter les problèmes de cache
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

error_log("🚀 API d'upload appelée - " . date('Y-m-d H:i:s'));

// Initialiser les sessions
Auth::init();
session_start();
Auth::initSession();

// Vérifier l'authentification
if (!Auth::isLoggedIn() || !Auth::hasAnyRole(['admin', 'editor'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Accès non autorisé']);
    exit;
}

// Vérifier la méthode
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Méthode non autorisée']);
    exit;
}

// Vérifier qu'un fichier a été uploadé
if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    $error = $_FILES['file']['error'] ?? 'Fichier non fourni';
    $errorMessages = [
        UPLOAD_ERR_INI_SIZE => 'Fichier trop volumineux (limite PHP)',
        UPLOAD_ERR_FORM_SIZE => 'Fichier trop volumineux (limite formulaire)',
        UPLOAD_ERR_PARTIAL => 'Upload partiel',
        UPLOAD_ERR_NO_FILE => 'Aucun fichier',
        UPLOAD_ERR_NO_TMP_DIR => 'Dossier temporaire manquant',
        UPLOAD_ERR_CANT_WRITE => 'Erreur d\'écriture',
        UPLOAD_ERR_EXTENSION => 'Extension bloquée'
    ];
    
    $errorMessage = $errorMessages[$error] ?? "Erreur d'upload: $error";
    error_log("❌ Erreur upload: $errorMessage");
    
    http_response_code(400);
    echo json_encode(['error' => $errorMessage]);
    exit;
}

// Récupérer le game_id optionnel
$gameId = null;
if (isset($_POST['game_id']) && !empty($_POST['game_id'])) {
    $gameId = (int)$_POST['game_id'];
}

$file = $_FILES['file'];

// Vérifier la taille du fichier
$fileSize = $file['size'];
$maxSize = convertToBytes(ini_get('upload_max_filesize'));
$postMaxSize = convertToBytes(ini_get('post_max_size'));

error_log("📏 Taille fichier: " . formatBytes($fileSize) . " / Limite upload: " . formatBytes($maxSize) . " / Limite post: " . formatBytes($postMaxSize));

if ($fileSize > $maxSize) {
    error_log("❌ Fichier trop volumineux: " . formatBytes($fileSize) . " > " . formatBytes($maxSize));
    http_response_code(400);
    echo json_encode(['error' => 'Fichier trop volumineux. Limite: ' . ini_get('upload_max_filesize')]);
    exit;
}

if ($fileSize > $postMaxSize) {
    error_log("❌ Fichier dépasse limite POST: " . formatBytes($fileSize) . " > " . formatBytes($postMaxSize));
    http_response_code(400);
    echo json_encode(['error' => 'Fichier trop volumineux. Limite POST: ' . ini_get('post_max_size')]);
    exit;
}

// Vérifier le type MIME
$allowedTypes = [
    'image/jpeg',
    'image/png', 
    'image/gif',
    'image/webp'
];

$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

if (!in_array($mimeType, $allowedTypes)) {
    http_response_code(400);
    echo json_encode(['error' => 'Type de fichier non autorisé']);
    exit;
}

// Vérifier l'extension
$extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
$allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

if (!in_array($extension, $allowedExtensions)) {
    http_response_code(400);
    echo json_encode(['error' => 'Extension de fichier non autorisée']);
    exit;
}

try {
    error_log("🚀 Début de l'upload API");
    
    // Créer le nom de fichier unique
    $timestamp = time();
    $randomString = bin2hex(random_bytes(8));
    $baseFilename = $timestamp . '_' . $randomString;
    
    error_log("📁 Nom de fichier généré: $baseFilename");
    
    // Déterminer le dossier de destination selon le jeu
    $baseDir = __DIR__ . '/public/uploads/';
    if ($gameId) {
        // Image liée à un jeu - aller dans games/nom_du_jeu/
        $game = \Game::find($gameId);
        if ($game && $game->getSlug()) {
            $uploadDir = $baseDir . 'games/' . $game->getSlug() . '/';
            error_log("🎮 Jeu trouvé: " . $game->getSlug());
        } else {
            $uploadDir = $baseDir . 'article/';
            error_log("⚠️ Jeu non trouvé ou slug vide, utilisation du dossier article");
        }
    } else {
        // Image classique - utiliser le dossier article
        $uploadDir = $baseDir . 'article/';
        error_log("📁 Upload sans jeu, dossier article");
    }
    
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
        error_log("📂 Dossier créé: $uploadDir");
    }
    
    error_log("📂 Dossier de destination: $uploadDir");
    
    // Déplacer le fichier temporaire
    $tempPath = $uploadDir . $baseFilename . '_temp.' . $extension;
    error_log("📄 Chemin temporaire: $tempPath");
    
    if (!move_uploaded_file($file['tmp_name'], $tempPath)) {
        error_log("❌ Erreur move_uploaded_file");
        throw new Exception('Erreur lors du déplacement du fichier');
    }
    
    error_log("✅ Fichier déplacé avec succès");
    
    // Optimiser l'image
    error_log("🔄 Début de l'optimisation");
    $optimizationResult = ImageOptimizer::optimizeImage($tempPath, $uploadDir);
    
    if (!$optimizationResult['success']) {
        error_log("❌ Erreur d'optimisation: " . $optimizationResult['error']);
        unlink($tempPath); // Supprimer le fichier temporaire
        throw new Exception('Erreur lors de l\'optimisation: ' . $optimizationResult['error']);
    }
    
    error_log("✅ Optimisation réussie");
    
    // Supprimer le fichier temporaire
    if (file_exists($tempPath)) {
        unlink($tempPath);
        error_log("🗑️ Fichier temporaire supprimé");
    }
    
    // Déterminer le fichier final (WebP en priorité, sinon JPG)
    $finalFilename = null;
    $finalMimeType = $mimeType;
    $finalSize = $file['size'];
    
    if (isset($optimizationResult['files']['webp']) && file_exists($optimizationResult['files']['webp'])) {
        $finalFilename = basename($optimizationResult['files']['webp']);
        $finalMimeType = 'image/webp';
        $finalSize = filesize($optimizationResult['files']['webp']);
        error_log("✅ Fichier WebP final: $finalFilename");
        
        // Supprimer le fichier JPG s'il existe (on ne garde que WebP)
        if (isset($optimizationResult['files']['jpg']) && file_exists($optimizationResult['files']['jpg'])) {
            unlink($optimizationResult['files']['jpg']);
            error_log("🗑️ Fichier JPG supprimé");
        }
    } elseif (isset($optimizationResult['files']['jpg']) && file_exists($optimizationResult['files']['jpg'])) {
        $finalFilename = basename($optimizationResult['files']['jpg']);
        $finalMimeType = 'image/jpeg';
        $finalSize = filesize($optimizationResult['files']['jpg']);
        error_log("✅ Fichier JPG final: $finalFilename");
    } else {
        error_log("❌ Aucun fichier optimisé trouvé");
        throw new Exception('Aucun fichier optimisé généré');
    }
    
    // Calculer le chemin relatif pour la base de données
    $relativePath = str_replace($baseDir, '', $uploadDir . $finalFilename);
    
    // Utiliser uploads.php pour tous les fichiers
    $url = '/uploads.php?file=' . $relativePath;
    
    // Sauvegarder en base de données
    error_log("💾 Sauvegarde en base de données");
    $db = Database::getInstance();
    $sql = "INSERT INTO media (filename, original_name, mime_type, size, uploaded_by, game_id, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, NOW())";
    
    $stmt = $db->prepare($sql);
    $stmt->execute([
        $relativePath,
        $file['name'],
        $finalMimeType,
        $finalSize,
        Auth::getUserId(),
        $gameId
    ]);
    
    $mediaId = $db->lastInsertId();
    error_log("✅ Média sauvegardé avec ID: $mediaId");
    
    error_log("📤 Envoi de la réponse JSON");
    
    $response = [
        'success' => true,
        'media' => [
            'id' => $mediaId,
            'filename' => $relativePath,
            'original_name' => $file['name'],
            'mime_type' => $finalMimeType,
            'size' => $finalSize,
            'url' => $url,
            'optimization' => [
                'original_size' => $file['size'],
                'optimized_size' => $finalSize,
                'compression_ratio' => $optimizationResult['compression_ratio'],
                'formats' => $optimizationResult['formats']
            ]
        ]
    ];
    
    echo json_encode($response);
    error_log("✅ Réponse envoyée avec succès");
    
} catch (Exception $e) {
    error_log("Erreur upload média: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Erreur lors de l\'upload']);
}
