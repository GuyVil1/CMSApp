<?php
/**
 * Fichier de routage temporaire pour la gestion des tags
 */

// Démarrer la session pour CSRF (seulement si pas déjà démarrée)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Récupérer les paramètres
$action = $_GET['action'] ?? 'index';
$id = $_GET['id'] ?? null;

// Gestion spéciale pour les actions AJAX (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'delete' && $id) {
    // Traitement direct de la suppression AJAX
    require_once 'core/Auth.php';
    require_once 'app/models/Tag.php';
    
    // Debug
    error_log("Suppression tag ID: " . $id);
    error_log("CSRF token reçu: " . ($_POST['csrf_token'] ?? 'AUCUN'));
    error_log("CSRF token en session: " . ($_SESSION['csrf_token'] ?? 'AUCUN'));
    
    // Vérifier CSRF
    if (!Auth::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        error_log("CSRF token invalide");
        error_log("Token reçu: " . ($_POST['csrf_token'] ?? 'AUCUN'));
        error_log("Token en session: " . ($_SESSION['csrf_token'] ?? 'AUCUN'));
        http_response_code(403);
        echo json_encode(['success' => false, 'error' => 'Token de sécurité invalide']);
        exit;
    }
    
    // Supprimer le tag
    if (Tag::delete((int)$id)) {
        error_log("Tag supprimé avec succès");
        echo json_encode(['success' => true]);
    } else {
        error_log("Erreur lors de la suppression du tag");
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Erreur lors de la suppression']);
    }
    exit;
}

// Gestion spéciale pour la recherche de tags (AJAX GET)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'search-tags') {
    // Traitement direct de la recherche AJAX
    require_once 'core/Auth.php';
    require_once 'app/models/Tag.php';
    
    $query = $_GET['q'] ?? '';
    $limit = (int)($_GET['limit'] ?? 10);
    
    if (empty($query) || strlen($query) < 2) {
        echo json_encode(['success' => true, 'tags' => []]);
        exit;
    }
    
    try {
        $sql = "SELECT id, name, slug FROM tags 
                WHERE name LIKE ? 
                ORDER BY name ASC 
                LIMIT ?";
        
        $params = ["%{$query}%", $limit];
        $tags = Tag::findWithQuery($sql, $params);
        
        echo json_encode(['success' => true, 'tags' => $tags]);
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Erreur lors de la recherche des tags']);
    }
    exit;
}

// Construire l'URL simulée pour les autres actions
$simulatedUrl = '/admin/tags';
if ($action !== 'index') {
    $simulatedUrl .= '/' . $action;
    if ($id) {
        $simulatedUrl .= '/' . $id;
    }
}

// Simuler l'URL pour le routeur
$_SERVER['REQUEST_URI'] = $simulatedUrl;

// Inclure le point d'entrée principal
require_once 'public/index.php';
