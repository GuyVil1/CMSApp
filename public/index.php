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

// Charger le système de container et middleware
require_once __DIR__ . '/../app/container/Container.php';
require_once __DIR__ . '/../app/container/ContainerFactory.php';
require_once __DIR__ . '/../app/container/ContainerConfig.php';

// Initialiser l'authentification
Auth::init();

// Initialiser le container et le système middleware
try {
    $container = new Container();
    $containerConfig = new ContainerConfig($container);
    $containerConfig->register();
} catch (Exception $e) {
    error_log("Erreur initialisation container: " . $e->getMessage());
}

// Vérifier le mode maintenance
require_once __DIR__ . '/../app/models/Setting.php';
$isMaintenanceMode = \Setting::isEnabled('maintenance_mode');
$isAdmin = Auth::isLoggedIn() && Auth::hasRole('admin');

// Si le mode maintenance est activé et que l'utilisateur n'est pas admin
if ($isMaintenanceMode && !$isAdmin) {
    // Exclure certaines routes même en maintenance
    $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
    $excludedRoutes = ['/admin', '/auth/login', '/auth/register'];
    
    $isExcludedRoute = false;
    foreach ($excludedRoutes as $route) {
        if (strpos($requestUri, $route) === 0) {
            $isExcludedRoute = true;
            break;
        }
    }
    
    // Si ce n'est pas une route exclue, afficher la page de maintenance
    if (!$isExcludedRoute) {
        http_response_code(503); // Service Temporarily Unavailable
        $isAdmin = Auth::isLoggedIn() && Auth::hasRole('admin');
        include __DIR__ . '/../app/views/layout/maintenance.php';
        exit;
    }
}

// Fonction d'autoload simple
spl_autoload_register(function ($class) {
    // Chercher dans les dossiers app/
    $paths = [
        __DIR__ . '/../app/controllers/',
        __DIR__ . '/../app/models/',
        __DIR__ . '/../app/helpers/',
        __DIR__ . '/../app/middleware/',
        __DIR__ . '/../app/events/',
        __DIR__ . '/../app/services/',
        __DIR__ . '/../app/repositories/',
        __DIR__ . '/../app/interfaces/',
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
    
    // Route de debug pour tester les paramètres
    if ($parts[0] === 'debug_settings') {
        echo "🔍 DEBUG SETTINGS SAVE\n";
        echo "=====================\n\n";
        
        echo "Méthode: " . ($_SERVER['REQUEST_METHOD'] ?? 'non défini') . "\n";
        echo "URI: " . ($_SERVER['REQUEST_URI'] ?? 'non défini') . "\n";
        echo "POST: " . print_r($_POST, true) . "\n";
        echo "SESSION: " . print_r($_SESSION, true) . "\n";
        
        // Tester la route settings/save
        $_SERVER['REQUEST_URI'] = '/admin/settings/save';
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['csrf_token'] = $_SESSION['csrf_token'] ?? 'test';
        $_POST['allow_registration'] = '1';
        
        echo "\n🎯 Test de la route /admin/settings/save:\n";
        
        try {
            $route = route('/admin/settings/save');
            echo "Route trouvée: " . print_r($route, true) . "\n";
            
            // Tester le pipeline de middlewares
            echo "\n🔧 Test du pipeline de middlewares:\n";
            
            // Créer la requête HTTP
            $httpRequest = new HttpRequest(
                $_SERVER['REQUEST_METHOD'],
                $_SERVER['REQUEST_URI'],
                $_GET,
                $_POST,
                getallheaders(),
                $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
                $_SERVER['HTTP_USER_AGENT'] ?? null
            );
            
            // Récupérer le pipeline
            $pipeline = ContainerFactory::make('MiddlewarePipeline');
            
            // Handler final simple
            $finalHandler = function($request) {
                echo "✅ Handler final atteint !\n";
                return new HttpResponse(200, 'OK', []);
            };
            
            echo "Pipeline créé, test en cours...\n";
            $response = $pipeline->handle($httpRequest, $finalHandler);
            
            echo "Réponse du pipeline:\n";
            echo "- Status: " . $response->getStatusCode() . "\n";
            echo "- Content: " . $response->getContent() . "\n";
            echo "- Headers: " . print_r($response->getHeaders(), true) . "\n";
            
            // Tester directement le contrôleur
            echo "\n🎮 Test direct du contrôleur:\n";
            
            try {
                // Charger le contrôleur
                require_once __DIR__ . "/../app/controllers/admin/SettingsController.php";
                
                // Créer une instance
                $controller = new Admin\SettingsController();
                echo "✅ Contrôleur créé avec succès\n";
                
                // Tester la méthode save
                echo "Test de la méthode save()...\n";
                
                // Capturer la sortie
                ob_start();
                $controller->save();
                $output = ob_get_clean();
                
                echo "Sortie de la méthode save(): " . $output . "\n";
                echo "✅ Méthode save() exécutée sans erreur\n";
                
            } catch (Exception $e) {
                echo "❌ Erreur dans le contrôleur: " . $e->getMessage() . "\n";
                echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
            }
            
        } catch (Exception $e) {
            echo "Erreur: " . $e->getMessage() . "\n";
            echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
        }
        
        exit;
    }
    
    // Routes SEO
    if ($parts[0] === 'sitemap.xml') {
        return [
            'controller' => 'SeoController',
            'action' => 'sitemap',
            'params' => []
        ];
    }
    
    if ($parts[0] === 'robots.txt') {
        return [
            'controller' => 'SeoController',
            'action' => 'robots',
            'params' => []
        ];
    }
    
    // Routes légales
    if ($parts[0] === 'mentions-legales') {
        return [
            'controller' => 'LegalController',
            'action' => 'mentionsLegales',
            'params' => []
        ];
    }
    
    if ($parts[0] === 'politique-confidentialite') {
        return [
            'controller' => 'LegalController',
            'action' => 'politiqueConfidentialite',
            'params' => []
        ];
    }
    
    if ($parts[0] === 'cgu') {
        return [
            'controller' => 'LegalController',
            'action' => 'cgu',
            'params' => []
        ];
    }
    
    if ($parts[0] === 'cookies') {
        return [
            'controller' => 'LegalController',
            'action' => 'cookies',
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
    
    // Route spéciale pour hardware (pages publiques)
    if ($parts[0] === 'hardware') {
        error_log("🔍 Route hardware détectée");
        $controller = 'HomeController';
        
        // Charger le HomeController
        $controllerFile = __DIR__ . "/../app/controllers/{$controller}.php";
        if (file_exists($controllerFile)) {
            require_once $controllerFile;
        } else {
            error_log("❌ HomeController non trouvé");
            return ['error' => '404'];
        }
        
        // Si pas de slug, rediriger vers la page publique des hardwares
        if (!isset($parts[1]) || empty($parts[1])) {
            error_log("🔍 Route hardware: redirection vers page publique");
            return [
                'controller' => $controller,
                'action' => 'hardwareList',
                'params' => []
            ];
        } else {
            // Sinon, afficher le hardware spécifique
            error_log("🔍 Route hardware: hardware spécifique - " . $parts[1]);
            return [
                'controller' => $controller,
                'action' => 'hardware',
                'params' => [$parts[1]] // Le slug du hardware
            ];
        }
    }
    
    // Route spéciale pour hardwares (page publique de listing)
    if ($parts[0] === 'hardwares') {
        error_log("🔍 Route hardwares détectée");
        $controller = 'HomeController';
        
        // Charger le HomeController
        $controllerFile = __DIR__ . "/../app/controllers/{$controller}.php";
        if (file_exists($controllerFile)) {
            require_once $controllerFile;
        } else {
            error_log("❌ HomeController non trouvé");
            return ['error' => '404'];
        }
        
        // Si pas de slug, afficher la liste des hardwares
        if (!isset($parts[1]) || empty($parts[1])) {
            error_log("🔍 Route hardwares: liste des hardwares");
            return [
                'controller' => $controller,
                'action' => 'hardwareList',
                'params' => []
            ];
        } else {
            // Sinon, afficher le hardware spécifique
            error_log("🔍 Route hardwares: hardware spécifique - " . $parts[1]);
            return [
                'controller' => $controller,
                'action' => 'hardware',
                'params' => [$parts[1]] // Le slug du hardware
            ];
        }
    }
    
    // Route spéciale pour category
    if ($parts[0] === 'category') {
        error_log("🔍 Route category détectée");
        $controller = 'HomeController';
        $action = 'category';
        
        // Extraire le slug de la catégorie (2ème partie après 'category')
        if (isset($parts[1])) {
            $params = [$parts[1]]; // Le slug de la catégorie
        } else {
            // Pas de slug, rediriger vers 404
            error_log("❌ Pas de slug de catégorie spécifié");
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
        // Route spéciale pour SEO
        if (isset($parts[1]) && $parts[1] === 'seo') {
            $controller = 'Admin\\SeoController';
            $action = $parts[2] ?? 'test';
            $params = array_slice($parts, 3);
            
            // Charger le contrôleur SEO
            $controllerFile = __DIR__ . "/../app/controllers/SeoController.php";
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
        // Routes admin
        $adminController = $parts[1] ?? 'dashboard';
        $action = $parts[2] ?? 'index';
        $params = array_slice($parts, 3);
        
        // Déterminer le contrôleur admin
        switch ($adminController) {
            case 'dashboard':
                $controller = 'Admin\\DashboardController';
                $controllerFile = __DIR__ . "/../app/controllers/admin/DashboardController.php";
                break;
            case 'settings':
                $controller = 'Admin\\SettingsController';
                $controllerFile = __DIR__ . "/../app/controllers/admin/SettingsController.php";
                break;
            case 'media':
                $controller = 'Admin\\MediaController';
                $controllerFile = __DIR__ . "/../app/controllers/admin/MediaController.php";
                break;
            case 'monitoring':
                $controller = 'Admin\\MonitoringController';
                $controllerFile = __DIR__ . "/../app/controllers/admin/MonitoringController.php";
                break;
            default:
                return ['error' => '404'];
        }
        
        // Charger le contrôleur admin
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
    $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
    
    // Créer la requête HTTP pour le middleware pipeline
    $httpRequest = new HttpRequest(
        $_SERVER['REQUEST_METHOD'] ?? 'GET',
        $requestUri,
        $_GET,
        $_POST,
        getallheaders() ?: [],
        $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
        $_SERVER['HTTP_USER_AGENT'] ?? null
    );
    
    // Récupérer le pipeline middleware
    try {
        $pipeline = ContainerFactory::make('MiddlewarePipeline');
        
        // Ajouter le middleware de monitoring
        $monitoringMiddleware = ContainerFactory::make('MonitoringMiddleware');
        $pipeline->add($monitoringMiddleware);
        
        // Définir le handler final (le routeur existant)
        $finalHandler = function($request) use ($requestUri) {
            $route = route($requestUri);
            
            if (isset($route['error'])) {
                // Page d'erreur
                http_response_code(404);
                include __DIR__ . '/../app/views/layout/404.php';
                exit;
            }
            
            // Vérifier si c'est une requête AJAX/API
            $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                      strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
            $isApi = strpos($requestUri, '/media/') === 0;
            
            // Pour les requêtes API, ne pas afficher la page HTML
            if ($isApi || $isAjax) {
                // Définir le Content-Type pour les API
                header('Content-Type: application/json; charset=utf-8');
                
                // Empêcher l'affichage de la page HTML
                ob_start();
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
            
            // Pour les requêtes API, nettoyer la sortie et ne garder que la réponse JSON
            if ($isApi || $isAjax) {
                $output = ob_get_clean();
                // Si la sortie contient du HTML, la vider et laisser seulement la réponse JSON du contrôleur
                if (strpos($output, '<!DOCTYPE') !== false || strpos($output, '<html') !== false) {
                    // La sortie contient du HTML, on la vide
                    echo '';
                } else {
                    // La sortie contient la réponse JSON du contrôleur
                    echo $output;
                }
                exit;
            }
            
            return new HttpResponse(200, 'OK', []);
        };
        
        // Traiter la requête via le pipeline middleware
        $response = $pipeline->handle($httpRequest, $finalHandler);
        
        // Si le middleware a retourné une réponse, l'utiliser
        if ($response && $response->getStatusCode() !== 200) {
            http_response_code($response->getStatusCode());
            foreach ($response->getHeaders() as $name => $value) {
                header("$name: $value");
            }
            echo $response->getContent();
            exit;
        }
        
    } catch (Exception $e) {
        // Si le middleware échoue, continuer avec le routeur normal
        error_log("Erreur middleware: " . $e->getMessage());
        $route = route($requestUri);
        
        if (isset($route['error'])) {
            // Page d'erreur
            http_response_code(404);
            include __DIR__ . '/../app/views/layout/404.php';
            exit;
        }
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
