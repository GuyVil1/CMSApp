<?php
// Fichier temporaire pour la gestion des jeux
// À supprimer une fois .htaccess configuré

// Simuler l'URI admin/games
$_SERVER['REQUEST_URI'] = '/admin/games';

// Inclure le fichier public/index.php
require_once __DIR__ . '/public/index.php';
?>
