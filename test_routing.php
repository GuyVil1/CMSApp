<?php
/**
 * Test du routage pour /media/upload
 */

echo "<h1>🔍 TEST ROUTAGE /media/upload</h1>";

// Simuler la requête
$_SERVER['REQUEST_URI'] = '/media/upload';
$_SERVER['REQUEST_METHOD'] = 'POST';

echo "<h2>1️⃣ Simulation de la requête</h2>";
echo "✅ URI: " . $_SERVER['REQUEST_URI'] . "<br>";
echo "✅ Méthode: " . $_SERVER['REQUEST_METHOD'] . "<br>";

// Inclure le routeur
echo "<h2>2️⃣ Test du routeur</h2>";

// Charger la configuration
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/core/Auth.php';

// Initialiser l'authentification
Auth::init();

// Fonction de routage (copiée de public/index.php)
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
        return [
            'controller' => 'HomeController',
            'action' => 'index',
            'params' => []
        ];
    }
    
    $parts = explode('/', $uri);
    
    // Routes spéciales
    if ($parts[0] === 'admin') {
        // Route admin
        $action = $parts[1] ?? 'index';
        $params = array_slice($parts, 2);
        
        // Déterminer le contrôleur admin
        switch ($action) {
            case 'dashboard':
                $controller = 'Admin\\DashboardController';
                $controllerFile = __DIR__ . "/app/controllers/admin/DashboardController.php";
                break;
            case 'articles':
                $controller = 'Admin\\ArticlesController';
                $controllerFile = __DIR__ . "/app/controllers/admin/ArticlesController.php";
                break;
            case 'media':
                $controller = 'Admin\\MediaController';
                $controllerFile = __DIR__ . "/app/controllers/admin/MediaController.php";
                break;
            case 'settings':
                $controller = 'Admin\\SettingsController';
                $controllerFile = __DIR__ . "/app/controllers/admin/SettingsController.php";
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
    } elseif ($parts[0] === 'media') {
        // Route media directe
        $action = $parts[1] ?? 'index';
        $params = array_slice($parts, 2);
        
        $controller = 'Admin\\MediaController';
        $controllerFile = __DIR__ . "/app/controllers/admin/MediaController.php";
        
        // Charger le contrôleur
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
    
    // Autres routes...
    return ['error' => '404'];
}

// Tester le routage
echo "<h3>Test du routage pour /media/upload</h3>";
$route = route('/media/upload');

echo "✅ Résultat du routage:<br>";
echo "Controller: " . ($route['controller'] ?? 'null') . "<br>";
echo "Action: " . ($route['action'] ?? 'null') . "<br>";
echo "Params: " . print_r($route['params'] ?? [], true) . "<br>";

if (isset($route['error'])) {
    echo "❌ Erreur de routage: " . $route['error'] . "<br>";
} else {
    echo "✅ Routage réussi<br>";
    
    // Vérifier si le contrôleur existe
    $controllerClass = $route['controller'];
    if (class_exists($controllerClass)) {
        echo "✅ Classe contrôleur existe<br>";
        
        // Vérifier si la méthode existe
        $action = $route['action'];
        if (method_exists($controllerClass, $action)) {
            echo "✅ Méthode $action existe<br>";
        } else {
            echo "❌ Méthode $action n'existe pas<br>";
            echo "Méthodes disponibles: " . implode(', ', get_class_methods($controllerClass)) . "<br>";
        }
    } else {
        echo "❌ Classe contrôleur n'existe pas<br>";
    }
}

echo "<hr>";
echo "<h2>3️⃣ Test de création d'instance</h2>";

try {
    $controllerClass = 'Admin\\MediaController';
    $controller = new $controllerClass();
    echo "✅ Instance créée avec succès<br>";
    
    // Vérifier les méthodes
    $methods = get_class_methods($controller);
    echo "✅ Méthodes disponibles: " . implode(', ', $methods) . "<br>";
    
    if (in_array('upload', $methods)) {
        echo "✅ Méthode upload existe<br>";
    } else {
        echo "❌ Méthode upload n'existe pas<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Erreur création instance: " . $e->getMessage() . "<br>";
}

echo "<hr>";
echo "<p><a href='/test_media_controller.php'>← Retour au test MediaController</a></p>";
?>
