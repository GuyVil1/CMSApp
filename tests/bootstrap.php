<?php
declare(strict_types=1);

/**
 * Bootstrap pour les tests PHPUnit
 */

// Charger l'autoloader de Composer
require_once __DIR__ . '/../vendor/autoload.php';

// Charger la configuration de test
require_once __DIR__ . '/../config/config.php';

// Définir l'environnement de test
define('APP_ENV', 'testing');
define('TESTING', true);

// Désactiver l'affichage des erreurs en mode test
error_reporting(E_ALL);
ini_set('display_errors', '0');

// Créer les dossiers nécessaires
$dirs = [
    __DIR__ . '/../cache',
    __DIR__ . '/../logs',
    __DIR__ . '/../logs/security',
    __DIR__ . '/../cache/rate_limit',
    __DIR__ . '/../cache/security'
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}
