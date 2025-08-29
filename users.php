<?php
/**
 * Fichier de routage temporaire pour la gestion des utilisateurs
 */

// Simuler l'URL /admin/users pour le routeur
$_SERVER['REQUEST_URI'] = '/admin/users';

// Inclure le point d'entrée principal
require_once 'public/index.php';
