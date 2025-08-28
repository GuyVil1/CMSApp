<?php
/**
 * Service pour servir les images des thèmes
 */

$theme = $_GET['theme'] ?? '';
$side = $_GET['side'] ?? '';

if (empty($theme) || empty($side)) {
    http_response_code(400);
    exit('Paramètres manquants');
}

// Sécuriser les paramètres
$theme = preg_replace('/[^a-zA-Z0-9_-]/', '', $theme);
$side = preg_replace('/[^a-zA-Z0-9_-]/', '', $side);

if (!in_array($side, ['left', 'right'])) {
    http_response_code(400);
    exit('Côté invalide');
}

$imagePath = __DIR__ . "/themes/{$theme}/{$side}.png";

if (!file_exists($imagePath)) {
    http_response_code(404);
    exit('Image non trouvée');
}

// Vérifier que le fichier est bien dans le dossier themes (sécurité)
$realPath = realpath($imagePath);
$themesRealPath = realpath(__DIR__ . '/themes');

if (strpos($realPath, $themesRealPath) !== 0) {
    http_response_code(403);
    exit('Accès interdit');
}

// Déterminer le type MIME
$extension = strtolower(pathinfo($imagePath, PATHINFO_EXTENSION));
$mimeTypes = [
    'png' => 'image/png',
    'jpg' => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'gif' => 'image/gif',
    'webp' => 'image/webp'
];

$mimeType = $mimeTypes[$extension] ?? 'application/octet-stream';

// Envoyer l'image
header('Content-Type: ' . $mimeType);
header('Content-Length: ' . filesize($imagePath));
header('Cache-Control: public, max-age=31536000'); // Cache 1 an
header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');

readfile($imagePath);
?>
