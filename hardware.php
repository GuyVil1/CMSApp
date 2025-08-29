<?php
/**
 * Routeur temporaire pour la gestion des hardware
 * Simule les routes /admin/hardware/*
 */

// Démarrer la session si nécessaire
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Parser les paramètres
$action = $_GET['action'] ?? 'index';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Gestion directe des suppressions AJAX
if ($action === 'delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/core/Auth.php';
    require_once __DIR__ . '/app/models/Hardware.php';
    
    // Vérifier CSRF
    if (!\Auth::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        http_response_code(403);
        echo json_encode(['success' => false, 'error' => 'Token de sécurité invalide']);
        exit;
    }
    
    // Supprimer le hardware
    $hardware = \Hardware::find($id);
    if (!$hardware) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Hardware non trouvé']);
        exit;
    }
    
    try {
        if ($hardware->delete()) {
            echo json_encode(['success' => true]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Erreur lors de la suppression']);
        }
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

// Construire l'URL simulée pour le routeur principal
switch ($action) {
    case 'index':
        $_SERVER['REQUEST_URI'] = '/admin/hardware';
        break;
    case 'create':
        $_SERVER['REQUEST_URI'] = '/admin/hardware/create';
        break;
    case 'store':
        $_SERVER['REQUEST_URI'] = '/admin/hardware/store';
        break;
    case 'edit':
        $_SERVER['REQUEST_URI'] = '/admin/hardware/edit/' . $id;
        break;
    case 'update':
        $_SERVER['REQUEST_URI'] = '/admin/hardware/update/' . $id;
        break;
    case 'delete':
        $_SERVER['REQUEST_URI'] = '/admin/hardware/delete/' . $id;
        break;
    default:
        $_SERVER['REQUEST_URI'] = '/admin/hardware';
}

// Inclure le routeur principal
require_once __DIR__ . '/public/index.php';
