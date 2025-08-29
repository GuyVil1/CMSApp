<?php
/**
 * Fichier de routage temporaire pour la gestion des catégories
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
    require_once 'app/models/Category.php';
    
    // Debug
    error_log("Suppression catégorie ID: " . $id);
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
    
    // Supprimer la catégorie
    $category = Category::find((int)$id);
    if ($category) {
        try {
            if ($category->delete()) {
                error_log("Catégorie supprimée avec succès");
                echo json_encode(['success' => true]);
            } else {
                error_log("Erreur lors de la suppression de la catégorie");
                http_response_code(500);
                echo json_encode(['success' => false, 'error' => 'Erreur lors de la suppression']);
            }
        } catch (Exception $e) {
            error_log("Exception lors de la suppression: " . $e->getMessage());
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    } else {
        error_log("Catégorie non trouvée");
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Catégorie non trouvée']);
    }
    exit;
}

// Construire l'URL simulée pour les autres actions
$simulatedUrl = '/admin/categories';
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
