<?php
declare(strict_types=1);

/**
 * Point d'entrée principal de l'application
 * Front controller - routeur minimal
 */

// Démarrer la session
session_start();

// Charger la configuration
require_once __DIR__ . '/../config/config.php';

// Charger les helpers globaux
require_once __DIR__ . '/../app/helpers/flash_helper.php';

// Charger les classes de base
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../core/Auth.php';

// Initialiser l'authentification
Auth::init();

// Fonction d'autoload simple
spl_autoload_register(function ($class) {
    // Chercher dans les dossiers app/
    $paths = [
        __DIR__ . '/../app/controllers/',
        __DIR__ . '/../app/models/',
        __DIR__ . '/../app/helpers/',
        __DIR__ . '/../core/'
    ];
    
    foreach ($paths as $path) {
        $file = $path . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Gestion des erreurs
if (Config::isLocal()) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}

// Fonction de routage simple
function route($uri) {
    // Nettoyer l'URI
    $uri = parse_url($uri, PHP_URL_PATH);
    
    // Gérer le cas où parse_url retourne null
    if ($uri === null) {
        $uri = '';
    }
    
    $uri = trim($uri, '/');
    
    // Si URI vide, page d'accueil
    if (empty($uri)) {
        $uri = 'home';
    }
    
    // Séparer les parties de l'URI
    $parts = explode('/', $uri);
    $controller = ucfirst($parts[0]) . 'Controller';
    $action = $parts[1] ?? 'index';
    
    // Vérifier si le contrôleur existe
    $controllerFile = __DIR__ . "/../app/controllers/{$controller}.php";
    if (!file_exists($controllerFile)) {
        // Essayer avec AdminController
        if (strpos($uri, 'admin') === 0) {
            $adminController = 'Admin\\' . ucfirst($parts[1] ?? 'Dashboard') . 'Controller';
            $controllerFile = __DIR__ . "/../app/controllers/admin/" . ucfirst($parts[1] ?? 'Dashboard') . "Controller.php";
            if (file_exists($controllerFile)) {
                require_once $controllerFile;
                $controller = $adminController;
                // Pour les URLs admin, l'action est la 3ème partie si elle existe
                $action = $parts[2] ?? 'index';
            } else {
                return ['error' => '404'];
            }
        } else {
            return ['error' => '404'];
        }
    } else {
        require_once $controllerFile;
    }
    
    // Vérifier si la méthode existe
    if (!method_exists($controller, $action)) {
        return ['error' => '404'];
    }
    
    return [
        'controller' => $controller,
        'action' => $action,
        'params' => array_slice($parts, 3) // Pour admin, les params commencent à partir de la 4ème partie
    ];
}

// Traiter la requête
try {
    $route = route($_SERVER['REQUEST_URI']);
    
    if (isset($route['error'])) {
        // Page d'erreur
        http_response_code(404);
        include __DIR__ . '/../app/views/layout/404.php';
        exit;
    }
    
    // Instancier le contrôleur et appeler l'action
    $controller = new $route['controller']();
    $action = $route['action'];
    
    // Appeler l'action avec les paramètres
    if (!empty($route['params'])) {
        call_user_func_array([$controller, $action], $route['params']);
    } else {
        $controller->$action();
    }
    
} catch (Exception $e) {
    // Gestion des erreurs
    if (Config::isLocal()) {
        echo "<h1>Erreur</h1>";
        echo "<p><strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
        echo "<p><strong>Fichier:</strong> " . htmlspecialchars($e->getFile()) . "</p>";
        echo "<p><strong>Ligne:</strong> " . $e->getLine() . "</p>";
        echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    } else {
        http_response_code(500);
        include __DIR__ . '/../app/views/layout/500.php';
    }
}
