<?php
declare(strict_types=1);

/**
 * API de suppression des médias
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/core/Database.php';
require_once __DIR__ . '/core/Auth.php';

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

// Récupérer les données (JSON ou form data)
$mediaId = null;

// Essayer d'abord JSON
$jsonInput = json_decode(file_get_contents('php://input'), true);
if ($jsonInput && isset($jsonInput['id'])) {
    $mediaId = $jsonInput['id'];
}

// Sinon essayer les données de formulaire
if (!$mediaId && isset($_POST['id'])) {
    $mediaId = $_POST['id'];
}

// Sinon essayer GET (pour compatibilité)
if (!$mediaId && isset($_GET['id'])) {
    $mediaId = $_GET['id'];
}

if (!$mediaId || !is_numeric($mediaId)) {
    http_response_code(400);
    echo json_encode(['error' => 'ID média invalide']);
    exit;
}

try {
    $db = Database::getInstance();
    
    // Récupérer les informations du média
    $sql = "SELECT filename, original_name FROM media WHERE id = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute([$mediaId]);
    $media = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$media) {
        http_response_code(404);
        echo json_encode(['error' => 'Média non trouvé']);
        exit;
    }
    
    // Supprimer le fichier physique
    $filePath = __DIR__ . '/public/uploads/' . $media['filename'];
    if (file_exists($filePath)) {
        unlink($filePath);
    }
    
    // Supprimer les références dans les autres tables
    // 1. Mettre à jour les articles qui utilisent ce média comme cover_image_id
    $updateArticlesSql = "UPDATE articles SET cover_image_id = NULL WHERE cover_image_id = ?";
    $updateArticlesStmt = $db->prepare($updateArticlesSql);
    $updateArticlesStmt->execute([$mediaId]);
    
    // 2. Mettre à jour les jeux qui utilisent ce média comme cover_image_id
    $updateGamesSql = "UPDATE games SET cover_image_id = NULL WHERE cover_image_id = ?";
    $updateGamesStmt = $db->prepare($updateGamesSql);
    $updateGamesStmt->execute([$mediaId]);
    
    // 3. Supprimer la référence en base
    $deleteSql = "DELETE FROM media WHERE id = ?";
    $deleteStmt = $db->prepare($deleteSql);
    $deleteStmt->execute([$mediaId]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Média supprimé avec succès'
    ]);
    
} catch (Exception $e) {
    error_log("Erreur suppression média: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Erreur lors de la suppression']);
}
