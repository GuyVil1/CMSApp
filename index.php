<?php
declare(strict_types=1);

/**
 * Point d'entrée principal - Belgium Vidéo Gaming
 * Version adaptée pour la racine du projet
 */

// Charger la configuration
require_once __DIR__ . '/config/config.php';

// Charger les helpers globaux
require_once __DIR__ . '/app/helpers/flash_helper.php';

// Charger les classes de base
require_once __DIR__ . '/core/Database.php';
require_once __DIR__ . '/core/Controller.php';
require_once __DIR__ . '/core/Auth.php';

// Initialiser l'authentification (doit être fait avant session_start())
Auth::init();

// Démarrer la session
session_start();

// Initialiser la session après session_start()
Auth::initSession();

// Fonction d'autoload simple
spl_autoload_register(function ($class) {
    // Chercher dans les dossiers app/
    $paths = [
        __DIR__ . '/app/controllers/',
        __DIR__ . '/app/models/',
        __DIR__ . '/app/helpers/',
        __DIR__ . '/core/'
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
    $uri = trim($uri, '/');
    
    // Si URI vide, page d'accueil
    if (empty($uri)) {
        $uri = 'home';
    }
    
    // Séparer les parties de l'URI
    $parts = explode('/', $uri);
    
    // Gestion spéciale pour les routes d'authentification
    if ($parts[0] === 'login' || $parts[0] === 'logout' || $parts[0] === 'register') {
        $controller = 'AuthController';
        $action = $parts[0];
    } 
    // Gestion spéciale pour l'upload d'images
    elseif ($parts[0] === 'admin' && isset($parts[1]) && $parts[1] === 'upload-image') {
        $controller = 'UploadController';
        $action = 'image';
        $controllerFile = __DIR__ . "/app/controllers/admin/UploadController.php";
        
        if (!file_exists($controllerFile)) {
            return ['error' => '404'];
        }
        require_once $controllerFile;
    }
    // Gestion des routes admin
    elseif ($parts[0] === 'admin') {
        $controller = ucfirst($parts[1] ?? 'Dashboard') . 'Controller';
        $action = $parts[2] ?? 'index';
        $controllerFile = __DIR__ . "/app/controllers/admin/{$controller}.php";
        
        if (!file_exists($controllerFile)) {
            return ['error' => '404'];
        }
        require_once $controllerFile;
        
        // Gestion spéciale pour les actions avec ID (publish, draft, archive, delete)
        if (in_array($action, ['publish', 'draft', 'archive', 'delete']) && isset($parts[3])) {
            $action = $action;
            $params = [(int)$parts[3]]; // Convertir l'ID en int
        } else {
            $params = array_slice($parts, 3);
        }
        
        // Ajouter le namespace Admin pour les contrôleurs admin
        $controller = 'Admin\\' . $controller;
    } 
    // Routes normales
    else {
        $controller = ucfirst($parts[0]) . 'Controller';
        $action = $parts[1] ?? 'index';
        $controllerFile = __DIR__ . "/app/controllers/{$controller}.php";
        
        if (!file_exists($controllerFile)) {
            return ['error' => '404'];
        }
        require_once $controllerFile;
    }
    
    // Vérifier si la méthode existe
    if (!method_exists($controller, $action)) {
        return ['error' => '404'];
    }
    
    return [
        'controller' => $controller,
        'action' => $action,
        'params' => isset($params) ? $params : array_slice($parts, 2)
    ];
}

// Traiter la requête
try {
    $route = route($_SERVER['REQUEST_URI']);
    
    if (isset($route['error'])) {
        // Page d'erreur
        http_response_code(404);
        include __DIR__ . '/app/views/layout/404.php';
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
        include __DIR__ . '/app/views/layout/500.php';
    }
}
?>
