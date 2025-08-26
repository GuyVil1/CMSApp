<?php
// Script pour servir les images directement
$filename = $_GET['file'] ?? '';

if (empty($filename)) {
    http_response_code(404);
    exit('Fichier non spécifié');
}

// Chemin vers le dossier uploads
$uploadPath = __DIR__ . '/public/uploads/';
$filePath = $uploadPath . $filename;

// Vérifier que le fichier existe et est dans le bon dossier
if (!file_exists($filePath) || !is_file($filePath)) {
    http_response_code(404);
    exit('Fichier non trouvé');
}

// Vérifier que le fichier est bien dans le dossier uploads (sécurité)
$realPath = realpath($filePath);
$uploadRealPath = realpath($uploadPath);

if (strpos($realPath, $uploadRealPath) !== 0) {
    http_response_code(403);
    exit('Accès interdit');
}

// Déterminer le type MIME
$extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
$mimeTypes = [
    'jpg' => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'png' => 'image/png',
    'gif' => 'image/gif',
    'webp' => 'image/webp'
];

$mimeType = $mimeTypes[$extension] ?? 'application/octet-stream';

// Servir le fichier
header('Content-Type: ' . $mimeType);
header('Content-Length: ' . filesize($filePath));
header('Cache-Control: public, max-age=31536000'); // Cache 1 an
header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 31536000));

readfile($filePath);
exit;
?>
