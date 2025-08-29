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
    
    // Gérer les routes admin
    if (strpos($uri, 'admin') === 0) {
        $controllerName = ucfirst($parts[1] ?? 'Dashboard') . 'Controller';
        $controllerFile = __DIR__ . "/../app/controllers/admin/" . $controllerName . ".php";
        
        if (file_exists($controllerFile)) {
            require_once $controllerFile;
            $controller = 'Admin\\' . $controllerName;
            // Pour les URLs admin, l'action est la 3ème partie si elle existe
            $action = $parts[2] ?? 'index';
        } else {
            return ['error' => '404'];
        }
    } else {
        // Routes normales (non-admin)
        $controller = ucfirst($parts[0]) . 'Controller';
        $action = $parts[1] ?? 'index';
        
        // Gestion spéciale pour les routes d'authentification
        if ($parts[0] === 'login') {
            $controller = 'AuthController';
            $action = 'login';
        } elseif ($parts[0] === 'register') {
            $controller = 'AuthController';
            $action = 'register';
        } elseif ($parts[0] === 'logout') {
            $controller = 'AuthController';
            $action = 'logout';
        }
        
        // Vérifier si le contrôleur existe
        $controllerFile = __DIR__ . "/../app/controllers/{$controller}.php";
        
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
        // Convertir les paramètres selon le type attendu par la méthode
        $reflection = new ReflectionMethod($controller, $action);
        $parameters = $reflection->getParameters();
        $convertedParams = [];
        
        foreach ($route['params'] as $index => $param) {
            if (isset($parameters[$index])) {
                $parameter = $parameters[$index];
                $type = $parameter->getType();
                
                if ($type && !$type->isBuiltin()) {
                    // Type personnalisé, laisser tel quel
                    $convertedParams[] = $param;
                } elseif ($type && $type->getName() === 'int') {
                    // Convertir en int
                    $convertedParams[] = (int)$param;
                } elseif ($type && $type->getName() === 'float') {
                    // Convertir en float
                    $convertedParams[] = (float)$param;
                } elseif ($type && $type->getName() === 'bool') {
                    // Convertir en bool
                    $convertedParams[] = (bool)$param;
                } else {
                    // Type string ou pas de type, laisser tel quel
                    $convertedParams[] = $param;
                }
            } else {
                // Plus de paramètres que d'arguments, ignorer
                break;
            }
        }
        
        call_user_func_array([$controller, $action], $convertedParams);
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
