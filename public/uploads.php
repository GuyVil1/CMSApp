<?php
/**
 * Script pour servir les fichiers uploadés
 * Sécurisé pour éviter l'exécution de code malveillant
 */

// Vérifier que le fichier demandé existe
$requestedFile = $_GET['file'] ?? '';

if (empty($requestedFile)) {
    http_response_code(404);
    exit('Fichier non trouvé');
}

// Nettoyer le chemin pour éviter les attaques de traversée de répertoire
$requestedFile = str_replace(['../', '..\\'], '', $requestedFile);
$filePath = __DIR__ . '/uploads/' . $requestedFile;

// Vérifier que le fichier existe et est dans le bon répertoire
if (!file_exists($filePath) || !is_file($filePath)) {
    http_response_code(404);
    exit('Fichier non trouvé');
}

// Vérifier que le fichier est bien dans le dossier uploads
$realPath = realpath($filePath);
$uploadsDir = realpath(__DIR__ . '/uploads/');

if (strpos($realPath, $uploadsDir) !== 0) {
    http_response_code(403);
    exit('Accès interdit');
}

// Déterminer le type MIME
$extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
$mimeTypes = [
    'jpg' => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'png' => 'image/png',
    'gif' => 'image/gif',
    'webp' => 'image/webp',
    'pdf' => 'application/pdf',
    'txt' => 'text/plain'
];

$mimeType = $mimeTypes[$extension] ?? 'application/octet-stream';

// Envoyer les headers
header('Content-Type: ' . $mimeType);
header('Content-Length: ' . filesize($filePath));
header('Cache-Control: public, max-age=31536000'); // Cache 1 an
header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 31536000));

// Lire et envoyer le fichier
readfile($filePath);
