<?php
// Fichier de debug pour tester le routeur
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Simuler l'URI /article/36
$_SERVER['REQUEST_URI'] = '/article/36';

echo "<h1>🧪 Debug du Routeur</h1>";
echo "<p>URI: " . $_SERVER['REQUEST_URI'] . "</p>";

// Inclure le routeur
require_once __DIR__ . '/public/index.php';
?>
