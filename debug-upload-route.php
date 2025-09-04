<?php
/**
 * Debug simple pour tester la route d'upload
 */

// Simuler une requête vers /admin/media/upload
$_SERVER['REQUEST_URI'] = '/admin/media/upload';
$_SERVER['REQUEST_METHOD'] = 'POST';

// Charger le routeur
require_once __DIR__ . '/public/index.php';

// Simuler la fonction route
function route($uri) {
    $uri = parse_url($uri, PHP_URL_PATH);
    $uri = trim($uri, '/');
    $parts = explode('/', $uri);
    
    echo "URI: " . $uri . "\n";
    echo "Parts: " . print_r($parts, true) . "\n";
    
    // Gérer les routes admin
    if (strpos($uri, 'admin') === 0) {
        $controllerName = ucfirst($parts[1] ?? 'Dashboard') . 'Controller';
        $controllerFile = __DIR__ . "/app/controllers/admin/" . $controllerName . ".php";
        $action = $parts[2] ?? 'index';
        
        echo "Controller Name: " . $controllerName . "\n";
        echo "Controller File: " . $controllerFile . "\n";
        echo "Action: " . $action . "\n";
        
        if (file_exists($controllerFile)) {
            echo "✅ Fichier contrôleur trouvé\n";
            require_once $controllerFile;
            
            $controller = 'Admin\\' . $controllerName;
            echo "Controller class: " . $controller . "\n";
            
            if (class_exists($controller)) {
                echo "✅ Classe trouvée\n";
                
                if (method_exists($controller, $action)) {
                    echo "✅ Méthode " . $action . " trouvée\n";
                } else {
                    echo "❌ Méthode " . $action . " non trouvée\n";
                }
            } else {
                echo "❌ Classe non trouvée\n";
            }
        } else {
            echo "❌ Fichier contrôleur non trouvé\n";
        }
    }
}

// Tester la route
route('/admin/media/upload');
?>
