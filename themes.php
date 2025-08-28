<?php
// Fichier temporaire pour la gestion des thèmes
// À supprimer une fois .htaccess configuré

// Simuler l'URI admin/themes
$_SERVER['REQUEST_URI'] = '/admin/themes';

// Inclure le fichier public/index.php
require_once __DIR__ . '/public/index.php';
?>
