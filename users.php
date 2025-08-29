<?php
/**
 * Fichier de routage temporaire pour la gestion des utilisateurs
 */

// Récupérer les paramètres
$action = $_GET['action'] ?? 'index';
$id = $_GET['id'] ?? null;

// Construire l'URL simulée
$simulatedUrl = '/admin/users';
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
