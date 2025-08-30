<?php
/**
 * Routeur d'administration des genres
 * Gère toutes les actions CRUD pour les genres de jeux
 */

require_once 'app/controllers/admin/GenresController.php';

// Récupérer l'action depuis l'URL
$action = $_GET['action'] ?? 'index';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Créer l'instance du contrôleur
$controller = new Admin\GenresController();

// Router vers la bonne action
switch ($action) {
    case 'index':
        $controller->index();
        break;
        
    case 'create':
        $controller->create();
        break;
        
    case 'store':
        $controller->store();
        break;
        
    case 'edit':
        if ($id > 0) {
            $controller->edit($id);
        } else {
            header('Location: /genres.php?error=invalid_id');
            exit;
        }
        break;
        
    case 'update':
        if ($id > 0) {
            $controller->update($id);
        } else {
            header('Location: /genres.php?error=invalid_id');
            exit;
        }
        break;
        
    case 'delete':
        if ($id > 0) {
            $controller->delete($id);
        } else {
            header('Location: /genres.php?error=invalid_id');
            exit;
        }
        break;
        
    default:
        // Action non reconnue, rediriger vers l'index
        header('Location: /genres.php');
        exit;
}
