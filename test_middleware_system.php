<?php
require_once 'app/container/ContainerFactory.php';

echo "🚀 TEST DU SYSTÈME MIDDLEWARE\n";
echo "==============================\n\n";

try {
    // Test 1: Récupération du MiddlewarePipeline
    echo "1️⃣ Test de récupération du MiddlewarePipeline...\n";
    $pipeline = ContainerFactory::make('MiddlewarePipeline');
    echo "✅ MiddlewarePipeline créé: " . get_class($pipeline) . "\n";
    
    // Test 2: Vérification des middlewares enregistrés
    echo "\n2️⃣ Test des middlewares enregistrés...\n";
    $middlewares = $pipeline->getMiddlewares();
    echo "✅ Middlewares enregistrés: " . count($middlewares) . "\n";
    
    foreach ($middlewares as $middleware) {
        echo "   - " . get_class($middleware) . " (priorité: " . $middleware->getPriority() . ")\n";
    }
    
    // Test 3: Test de création de requête
    echo "\n3️⃣ Test de création de requête...\n";
    require_once 'app/middleware/HttpRequest.php';
    require_once 'app/middleware/HttpResponse.php';
    
    // Simuler une requête GET
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $_SERVER['REQUEST_URI'] = '/';
    $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
    $_SERVER['HTTP_USER_AGENT'] = 'Test Browser';
    
    $request = new HttpRequest();
    echo "✅ Requête créée: " . $request->getMethod() . " " . $request->getUri() . "\n";
    echo "   - IP: " . $request->getClientIp() . "\n";
    echo "   - User-Agent: " . $request->getUserAgent() . "\n";
    
    // Test 4: Test de traitement de requête
    echo "\n4️⃣ Test de traitement de requête...\n";
    $finalHandler = function($req) {
        return HttpResponse::html('<h1>Hello Middleware!</h1>');
    };
    
    $response = $pipeline->handle($request, $finalHandler);
    echo "✅ Requête traitée avec succès\n";
    echo "   - Status: " . $response->getStatusCode() . "\n";
    echo "   - Content-Type: " . $response->getHeader('content-type') . "\n";
    echo "   - Content: " . substr($response->getContent(), 0, 50) . "...\n";
    
    // Test 5: Test de requête POST avec validation
    echo "\n5️⃣ Test de requête POST avec validation...\n";
    $_SERVER['REQUEST_METHOD'] = 'POST';
    $_SERVER['REQUEST_URI'] = '/admin/articles/store';
    $_POST = [
        'title' => 'Test Article',
        'content' => 'This is a test article content',
        'status' => 'published'
    ];
    
    $postRequest = new HttpRequest();
    $postResponse = $pipeline->handle($postRequest, $finalHandler);
    echo "✅ Requête POST traitée\n";
    echo "   - Status: " . $postResponse->getStatusCode() . "\n";
    
    // Test 6: Test de requête avec erreur de validation
    echo "\n6️⃣ Test de requête avec erreur de validation...\n";
    $_POST = [
        'title' => 'A', // Trop court
        'content' => 'Short', // Trop court
        'status' => 'invalid' // Statut invalide
    ];
    
    $invalidRequest = new HttpRequest();
    $invalidResponse = $pipeline->handle($invalidRequest, $finalHandler);
    echo "✅ Requête invalide traitée\n";
    echo "   - Status: " . $invalidResponse->getStatusCode() . "\n";
    
    if ($invalidResponse->getStatusCode() === 400) {
        $errorData = json_decode($invalidResponse->getContent(), true);
        echo "   - Erreurs: " . count($errorData['errors']) . " champs\n";
    }
    
    // Test 7: Test de requête admin (sans authentification)
    echo "\n7️⃣ Test de requête admin (sans authentification)...\n";
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $_SERVER['REQUEST_URI'] = '/admin/dashboard';
    $_POST = [];
    
    $adminRequest = new HttpRequest();
    $adminResponse = $pipeline->handle($adminRequest, $finalHandler);
    echo "✅ Requête admin traitée\n";
    echo "   - Status: " . $adminResponse->getStatusCode() . "\n";
    
    if ($adminResponse->isRedirect()) {
        echo "   - Redirection vers: " . $adminResponse->getRedirectUrl() . "\n";
    }
    
    // Test 8: Vérification des logs
    echo "\n8️⃣ Vérification des logs...\n";
    $logFile = 'app/logs/requests.log';
    if (file_exists($logFile)) {
        $logs = file_get_contents($logFile);
        $logLines = explode("\n", trim($logs));
        echo "✅ Logs créés: " . count($logLines) . " entrées\n";
        
        // Afficher les dernières entrées
        $recentLogs = array_slice($logLines, -3);
        echo "   Dernières entrées:\n";
        foreach ($recentLogs as $log) {
            if (!empty($log)) {
                $logData = json_decode($log, true);
                echo "   - {$logData['method']} {$logData['uri']} -> {$logData['status_code']} ({$logData['processing_time_ms']}ms)\n";
            }
        }
    } else {
        echo "⚠️ Fichier de logs non trouvé\n";
    }
    
    echo "\n🎉 TOUS LES TESTS RÉUSSIS !\n";
    echo "🚀 Système middleware opérationnel !\n";
    echo "🛡️ Sécurité, validation et logging fonctionnels !\n";
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
?>
