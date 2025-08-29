<?php
// Redirection vers le dossier public
$request_uri = $_SERVER['REQUEST_URI'] ?? '/';

// Si c'est la racine, rediriger vers la page d'accueil
if ($request_uri === '/') {
    require_once __DIR__ . '/public/index.php';
    exit;
}

// Pour toutes les autres routes, rediriger vers public
$public_path = __DIR__ . '/public' . $request_uri;

if (file_exists($public_path)) {
    // Si c'est un fichier statique, le servir directement
    $mime_types = [
        'css' => 'text/css',
        'js' => 'application/javascript',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif' => 'image/gif',
        'webp' => 'image/webp',
        'ico' => 'image/x-icon'
    ];
    
    $extension = pathinfo($public_path, PATHINFO_EXTENSION);
    if (isset($mime_types[$extension])) {
        header('Content-Type: ' . $mime_types[$extension]);
        readfile($public_path);
        exit;
    }
}

// Sinon, passer au routeur principal
require_once __DIR__ . '/public/index.php';
?>
