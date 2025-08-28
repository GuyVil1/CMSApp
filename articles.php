<?php
// Fichier temporaire pour la gestion des articles
// À supprimer une fois .htaccess configuré

// Simuler l'URI admin/articles
$_SERVER['REQUEST_URI'] = '/admin/articles';

// Inclure le fichier public/index.php
require_once __DIR__ . '/public/index.php';
?>
