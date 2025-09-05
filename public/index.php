<?php
declare(strict_types=1);

/**
 * Point d'entrée principal de l'application
 * Front controller - routeur minimal
 */

// Inclure les headers de sécurité
require_once __DIR__ . '/security-headers.php';

// Démarrer la session (seulement si pas déjà démarrée)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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
    
    // Debug pour voir ce qui se passe
    error_log("🔍 URI: " . $uri);
    error_log("🔍 Parts: " . print_r($parts, true));
    
    // Route spéciale pour uploads.php
    if ($parts[0] === 'uploads.php') {
        error_log("🔍 Route uploads.php détectée");
        return [
            'controller' => 'SpecialController',
            'action' => 'uploads',
            'params' => []
        ];
    }
    
    // Route spéciale pour article
    if ($parts[0] === 'article') {
        error_log("🔍 Route article détectée");
        $controller = 'HomeController';
        $action = 'show';
        
        // Extraire le slug de l'article (2ème partie après 'article')
        if (isset($parts[1])) {
            $params = [$parts[1]]; // Le slug de l'article
            
            // Vérifier s'il y a un slug de chapitre (3ème partie)
            if (isset($parts[2])) {
                $action = 'showChapter';
                $params = [$parts[1], $parts[2]]; // [slug_dossier, slug_chapitre]
                error_log("🔍 Chapitre détecté: " . $parts[2] . " pour dossier: " . $parts[1]);
            }
        } else {
            // Pas de slug, rediriger vers 404
            error_log("❌ Pas de slug d'article spécifié");
            return ['error' => '404'];
        }
        
        error_log("🔍 Controller: " . $controller);
        error_log("🔍 Action: " . $action);
        error_log("🔍 Params (slug): " . print_r($params, true));
        
        // Charger le HomeController
        $controllerFile = __DIR__ . "/../app/controllers/{$controller}.php";
        if (file_exists($controllerFile)) {
            require_once $controllerFile;
        } else {
            error_log("❌ HomeController non trouvé");
            return ['error' => '404'];
        }
        
        return [
            'controller' => $controller,
            'action' => $action,
            'params' => $params
        ];
    }
    
    // Gérer les routes admin
    if (strpos($uri, 'admin') === 0) {
        $controllerName = ucfirst($parts[1] ?? 'Dashboard') . 'Controller';
        $controllerFile = __DIR__ . "/../app/controllers/admin/" . $controllerName . ".php";
        
        if (file_exists($controllerFile)) {
            require_once $controllerFile;
            $controller = 'Admin\\' . $controllerName;
            // Pour les URLs admin, l'action est la 3ème partie si elle existe
            $action = $parts[2] ?? 'index';
            $params = array_slice($parts, 3);
            
            // Gestion spéciale pour les actions de chapitres
            if ($controllerName === 'ArticlesController' && in_array($action, ['save-chapters', 'load-chapters', 'update-chapter-status', 'delete-chapter'])) {
                // Convertir les tirets en camelCase pour les méthodes
                if ($action === 'save-chapters') {
                    $action = 'saveChapters';
                } elseif ($action === 'load-chapters') {
                    $action = 'loadChapters';
                } elseif ($action === 'update-chapter-status') {
                    $action = 'updateChapterStatus';
                } elseif ($action === 'delete-chapter') {
                    $action = 'deleteChapter';
                }
            }
            
            return [
                'controller' => $controller,
                'action' => $action,
                'params' => $params
            ];
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
    } elseif ($parts[0] === 'genres') {
        // Route spéciale pour les genres
        $controller = 'Admin\\GenresController';
        $action = $parts[1] ?? 'index';
        $params = array_slice($parts, 2);
        
        // Charger le contrôleur des genres
        $controllerFile = __DIR__ . "/../app/controllers/admin/GenresController.php";
        if (file_exists($controllerFile)) {
            require_once $controllerFile;
        } else {
            return ['error' => '404'];
        }
        
        return [
            'controller' => $controller,
            'action' => $action,
            'params' => $params
        ];
    } elseif ($parts[0] === 'games') {
        // Route spéciale pour les jeux
        $controller = 'Admin\\GamesController';
        $action = $parts[1] ?? 'index';
        $params = array_slice($parts, 2);
        
        // Charger le contrôleur des jeux
        $controllerFile = __DIR__ . "/../app/controllers/admin/GamesController.php";
        if (file_exists($controllerFile)) {
            require_once $controllerFile;
        } else {
            return ['error' => '404'];
        }
        
        return [
            'controller' => $controller,
            'action' => $action,
            'params' => $params
        ];
    } elseif ($parts[0] === 'articles') {
        // Route spéciale pour les articles
        $controller = 'Admin\\ArticlesController';
        $action = $parts[1] ?? 'index';
        $params = array_slice($parts, 2);
        
        // Charger le contrôleur des articles
        $controllerFile = __DIR__ . "/../app/controllers/admin/ArticlesController.php";
        if (file_exists($controllerFile)) {
            require_once $controllerFile;
        } else {
            return ['error' => '404'];
        }
        
        return [
            'controller' => $controller,
            'action' => $action,
            'params' => $params
        ];
    } elseif ($parts[0] === 'categories') {
        // Route spéciale pour les catégories
        $controller = 'Admin\\CategoriesController';
        $action = $parts[1] ?? 'index';
        $params = array_slice($parts, 2);
        
        // Charger le contrôleur des catégories
        $controllerFile = __DIR__ . "/../app/controllers/admin/CategoriesController.php";
        if (file_exists($controllerFile)) {
            require_once $controllerFile;
        } else {
            return ['error' => '404'];
        }
        
        return [
            'controller' => $controller,
            'action' => $action,
            'params' => $params
        ];
    } elseif ($parts[0] === 'hardware') {
        // Route spéciale pour le hardware
        $controller = 'Admin\\HardwareController';
        $action = $parts[1] ?? 'index';
        $params = array_slice($parts, 2);
        
        // Charger le contrôleur du hardware
        $controllerFile = __DIR__ . "/../app/controllers/admin/HardwareController.php";
        if (file_exists($controllerFile)) {
            require_once $controllerFile;
        } else {
            return ['error' => '404'];
        }
        
        return [
            'controller' => $controller,
            'action' => $action,
            'params' => $params
        ];
    } elseif ($parts[0] === 'media') {
        // Route spéciale pour les médias
        $controller = 'Admin\\MediaController';
        $action = $parts[1] ?? 'index';
        $params = array_slice($parts, 2);
        
        // Charger le contrôleur des médias
        $controllerFile = __DIR__ . "/../app/controllers/admin/MediaController.php";
        if (file_exists($controllerFile)) {
            require_once $controllerFile;
        } else {
            return ['error' => '404'];
        }
        
        return [
            'controller' => $controller,
            'action' => $action,
            'params' => $params
        ];
    } elseif ($parts[0] === 'users') {
        // Route spéciale pour les utilisateurs
        $controller = 'Admin\\UsersController';
        $action = $parts[1] ?? 'index';
        $params = array_slice($parts, 2);
        
        // Charger le contrôleur des utilisateurs
        $controllerFile = __DIR__ . "/../app/controllers/admin/UsersController.php";
        if (file_exists($controllerFile)) {
            require_once $controllerFile;
        } else {
            return ['error' => '404'];
        }
        
        return [
            'controller' => $controller,
            'action' => $action,
            'params' => $params
        ];
    } elseif ($parts[0] === 'admin') {
        // Route spéciale pour le tableau de bord admin
        $controller = 'Admin\\DashboardController';
        $action = $parts[1] ?? 'index';
        $params = array_slice($parts, 2);
        
        // Charger le contrôleur du tableau de bord
        $controllerFile = __DIR__ . "/../app/controllers/admin/DashboardController.php";
        if (file_exists($controllerFile)) {
            require_once $controllerFile;
        } else {
            return ['error' => '404'];
        }
        
        return [
            'controller' => $controller,
            'action' => $action,
            'params' => $params
        ];
    } elseif ($parts[0] === 'test') {
        // Route spéciale pour les tests
        $controller = 'TestController';
        $action = $parts[1] ?? 'index';
        $params = array_slice($parts, 2);
        
        // Charger le contrôleur de test
        $controllerFile = __DIR__ . "/../app/controllers/{$controller}.php";
        if (file_exists($controllerFile)) {
            require_once $controllerFile;
        } else {
            return ['error' => '404'];
        }
        
        return [
            'controller' => $controller,
            'action' => $action,
            'params' => $params
        ];
    }
        
        // Vérifier si le contrôleur existe
        $controllerFile = __DIR__ . "/../app/controllers/{$controller}.php";
        
        if (!file_exists($controllerFile)) {
            return ['error' => '404'];
        }
        
        require_once $controllerFile;
        $params = array_slice($parts, 2);
    }
    
    // Vérifier si la méthode existe
    if (!method_exists($controller, $action)) {
        return ['error' => '404'];
    }
    
    // Debug pour voir ce qui se passe
    error_log("🔍 URI: " . $uri);
    error_log("🔍 Parts: " . print_r($parts, true));
    error_log("🔍 Controller: " . $controller);
    error_log("🔍 Action: " . $action);
    error_log("🔍 Params: " . print_r($params, true));
    
    return [
        'controller' => $controller,
        'action' => $action,
        'params' => $params
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
