<?php
/**
 * API dédiée pour la suppression de médias
 * Point d'entrée direct sans routage complexe
 */

// Démarrer la session
session_start();

// Charger la configuration
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/core/Auth.php';
require_once __DIR__ . '/app/helpers/security_logger_simple.php';

// Initialiser l'authentification
Auth::init();

// Définir le Content-Type pour JSON
header('Content-Type: application/json; charset=utf-8');

// Vérifier la méthode HTTP
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

// Vérifier l'authentification
if (!Auth::isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Non authentifié']);
    exit;
}

// Vérifier le token CSRF
$csrfToken = $_POST['csrf_token'] ?? '';
if (!Auth::verifyCsrfToken($csrfToken)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Token CSRF invalide']);
    exit;
}

// Récupérer l'ID du média à supprimer
$mediaId = $_POST['id'] ?? null;
if (!$mediaId) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'ID du média manquant']);
    exit;
}

try {
    // Charger le contrôleur MediaController
    require_once __DIR__ . '/app/controllers/admin/MediaController.php';
    
    // Créer une instance du contrôleur
    $controller = new Admin\MediaController();
    
    // Capturer la sortie
    ob_start();
    
    // Appeler la méthode delete avec l'ID
    $controller->delete($mediaId);
    
    // Récupérer la sortie
    $output = ob_get_clean();
    
    // Si la sortie est vide, il y a eu un problème
    if (empty($output)) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Erreur interne du serveur']);
        exit;
    }
    
    // Vérifier si c'est du JSON valide
    $jsonData = json_decode($output, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Réponse invalide du serveur']);
        exit;
    }
    
    // Retourner la réponse JSON
    echo $output;
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Erreur: ' . $e->getMessage()
    ]);
}
?>

