<?php
// Fichier temporaire pour la gestion des médias
// À supprimer une fois .htaccess configuré

// Simuler l'URI admin/media
$_SERVER['REQUEST_URI'] = '/admin/media';

// Inclure le fichier public/index.php
require_once __DIR__ . '/public/index.php';
?>
