<?php
// Fichier temporaire pour gérer les routes admin
// À supprimer une fois .htaccess configuré

// Récupérer l'URI depuis l'URL
$request_uri = $_SERVER['REQUEST_URI'] ?? '/';
$path = parse_url($request_uri, PHP_URL_PATH);

// Si c'est /admin.php, rediriger vers /admin/dashboard
if ($path === '/admin.php') {
    $_SERVER['REQUEST_URI'] = '/admin/dashboard';
}

// Inclure le fichier public/index.php
require_once __DIR__ . '/public/index.php';
?>
