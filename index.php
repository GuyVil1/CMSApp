<?php
declare(strict_types=1);

/**
 * Point d'entrée principal - Belgium Vidéo Gaming
 * Redirection vers le dossier public
 */

// Rediriger vers le dossier public
$public_path = __DIR__ . '/public/index.php';

if (file_exists($public_path)) {
    // Inclure le fichier public/index.php
    require_once $public_path;
} else {
    // Fallback si le fichier public n'existe pas
    echo "Erreur : Le fichier public/index.php n'existe pas";
}
?>
