<?php
/**
 * Fichier temporaire pour gérer les routes du média
 * TODO: Intégrer dans le système de routing principal
 */

session_start();

// Inclure les fichiers nécessaires
require_once __DIR__ . '/core/Auth.php';
require_once __DIR__ . '/app/controllers/admin/MediaController.php';

// Vérifier l'authentification
if (!\Auth::isLoggedIn()) {
    header('Location: /auth/login');
    exit;
}

// Récupérer l'action
$action = $_GET['action'] ?? 'index';

// Instancier le contrôleur
$controller = new \Admin\MediaController();

// Router les actions
switch ($action) {
    case 'index':
        $controller->index();
        break;
        
    case 'upload':
        $controller->upload();
        break;
        
    case 'delete':
        $id = (int)($_GET['id'] ?? 0);
        if ($id > 0) {
            $controller->delete($id);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'ID invalide']);
        }
        break;
        
    case 'search':
        $controller->search();
        break;
        
    case 'search-games':
        $controller->searchGames();
        break;
        
    case 'by-type':
        $controller->byType();
        break;
        
    default:
        http_response_code(404);
        echo json_encode(['error' => 'Action non trouvée']);
        break;
}
?>
