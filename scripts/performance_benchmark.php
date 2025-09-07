<?php
/**
 * Benchmark de performance - Belgium Video Gaming
 * Test des temps de réponse et identification des goulots d'étranglement
 */

echo "🚀 BENCHMARK DE PERFORMANCE\n";
echo "===========================\n\n";

$baseDir = dirname(__DIR__);
$results = [];
$totalTime = 0;

// Configuration
$iterations = 10; // Nombre de tests par composant
$warmupIterations = 3; // Échauffement

echo "Configuration:\n";
echo "- Tests par composant: $iterations\n";
echo "- Échauffement: $warmupIterations\n\n";

// Fonction utilitaire pour mesurer le temps
function measureTime($callback, $iterations = 1) {
    $times = [];
    
    for ($i = 0; $i < $iterations; $i++) {
        $start = microtime(true);
        $callback();
        $end = microtime(true);
        $times[] = ($end - $start) * 1000; // en millisecondes
    }
    
    return [
        'min' => min($times),
        'max' => max($times),
        'avg' => array_sum($times) / count($times),
        'total' => array_sum($times)
    ];
}

// 1. Test du Container et DI
echo "1. Test du Container et Injection de Dépendances...\n";
$containerTime = measureTime(function() use ($baseDir) {
    require_once $baseDir . '/app/container/ContainerFactory.php';
    require_once $baseDir . '/app/events/EventInterface.php';
    require_once $baseDir . '/app/events/BaseEvent.php';
    require_once $baseDir . '/app/events/ArticleEvents.php';
    require_once $baseDir . '/app/services/ArticleService.php';
    $container = \ContainerFactory::make('Container');
    $articleService = $container->make('ArticleService');
}, $iterations);

$results['Container DI'] = $containerTime;
echo "   Min: " . round($containerTime['min'], 2) . "ms\n";
echo "   Max: " . round($containerTime['max'], 2) . "ms\n";
echo "   Moy: " . round($containerTime['avg'], 2) . "ms\n\n";

// 2. Test du système d'événements
echo "2. Test du système d'événements...\n";
$eventTime = measureTime(function() use ($baseDir) {
    require_once $baseDir . '/app/events/EventDispatcher.php';
    require_once $baseDir . '/app/events/ArticleEvents.php';
    
    $dispatcher = new \EventDispatcher();
    $event = new \ArticleCreatedEvent(1, ['title' => 'Test', 'user_id' => 1]);
    $dispatcher->dispatch($event);
}, $iterations);

$results['Event System'] = $eventTime;
echo "   Min: " . round($eventTime['min'], 2) . "ms\n";
echo "   Max: " . round($eventTime['max'], 2) . "ms\n";
echo "   Moy: " . round($eventTime['avg'], 2) . "ms\n\n";

// 3. Test du middleware pipeline
echo "3. Test du middleware pipeline...\n";
$middlewareTime = measureTime(function() use ($baseDir) {
    require_once $baseDir . '/app/middleware/MiddlewarePipeline.php';
    require_once $baseDir . '/app/middleware/HttpRequest.php';
    require_once $baseDir . '/app/middleware/HttpResponse.php';
    
    $pipeline = new \MiddlewarePipeline();
    $request = new \HttpRequest('GET', '/test', [], [], []);
    $response = $pipeline->handle($request, function($req) {
        return new \HttpResponse(200, 'OK');
    });
}, $iterations);

$results['Middleware Pipeline'] = $middlewareTime;
echo "   Min: " . round($middlewareTime['min'], 2) . "ms\n";
echo "   Max: " . round($middlewareTime['max'], 2) . "ms\n";
echo "   Moy: " . round($middlewareTime['avg'], 2) . "ms\n\n";

// 4. Test des requêtes de base de données
echo "4. Test des requêtes de base de données...\n";
$dbTime = measureTime(function() use ($baseDir) {
    require_once $baseDir . '/core/Database.php';
    
    $db = Database::getInstance();
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM articles");
    $stmt->execute();
    $result = $stmt->fetch();
    
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM users");
    $stmt->execute();
    $result = $stmt->fetch();
}, $iterations);

$results['Database Queries'] = $dbTime;
echo "   Min: " . round($dbTime['min'], 2) . "ms\n";
echo "   Max: " . round($dbTime['max'], 2) . "ms\n";
echo "   Moy: " . round($dbTime['avg'], 2) . "ms\n\n";

// 5. Test du cache
echo "5. Test du système de cache...\n";
$cacheTime = measureTime(function() use ($baseDir) {
    require_once $baseDir . '/app/helpers/MemoryCache.php';
    
    MemoryCache::put('test_key', 'test_value', 60);
    $value = MemoryCache::get('test_key');
    MemoryCache::forget('test_key');
}, $iterations);

$results['Cache System'] = $cacheTime;
echo "   Min: " . round($cacheTime['min'], 2) . "ms\n";
echo "   Max: " . round($cacheTime['max'], 2) . "ms\n";
echo "   Moy: " . round($cacheTime['avg'], 2) . "ms\n\n";

// 6. Test complet d'une page (simulation)
echo "6. Test complet d'une page (simulation)...\n";
$pageTime = measureTime(function() use ($baseDir) {
    // Simulation d'une requête complète
    require_once $baseDir . '/app/container/ContainerFactory.php';
    require_once $baseDir . '/app/events/EventInterface.php';
    require_once $baseDir . '/app/events/BaseEvent.php';
    require_once $baseDir . '/app/events/ArticleEvents.php';
    require_once $baseDir . '/app/services/ArticleService.php';
    require_once $baseDir . '/app/middleware/MiddlewarePipeline.php';
    require_once $baseDir . '/app/middleware/HttpRequest.php';
    require_once $baseDir . '/app/middleware/HttpResponse.php';
    
    $container = \ContainerFactory::make('Container');
    $pipeline = new \MiddlewarePipeline();
    $request = new \HttpRequest('GET', '/', [], [], []);
    
    $response = $pipeline->handle($request, function($req) use ($container) {
        $articleService = $container->make('ArticleService');
        $articles = $articleService->getRecentArticles(1, 10);
        return new \HttpResponse(200, 'Page loaded');
    });
}, $iterations);

$results['Complete Page'] = $pageTime;
echo "   Min: " . round($pageTime['min'], 2) . "ms\n";
echo "   Max: " . round($pageTime['max'], 2) . "ms\n";
echo "   Moy: " . round($pageTime['avg'], 2) . "ms\n\n";

// Analyse des résultats
echo "📊 ANALYSE DES RÉSULTATS\n";
echo "========================\n\n";

// Calcul du temps total
$totalTime = array_sum(array_column($results, 'total'));

// Tri par performance (du plus lent au plus rapide)
uasort($results, function($a, $b) {
    return $b['avg'] <=> $a['avg'];
});

echo "Classement par performance (du plus lent au plus rapide):\n";
$rank = 1;
foreach ($results as $component => $times) {
    $status = $times['avg'] > 100 ? "🔴 LENT" : ($times['avg'] > 50 ? "🟡 MOYEN" : "🟢 RAPIDE");
    echo "$rank. $component: " . round($times['avg'], 2) . "ms $status\n";
    $rank++;
}

echo "\nTemps total d'exécution: " . round($totalTime, 2) . "ms\n";
echo "Temps moyen par test: " . round($totalTime / ($iterations * count($results)), 2) . "ms\n\n";

// Recommandations
echo "💡 RECOMMANDATIONS\n";
echo "==================\n\n";

$slowComponents = array_filter($results, function($times) {
    return $times['avg'] > 100;
});

if (!empty($slowComponents)) {
    echo "Composants nécessitant une optimisation:\n";
    foreach ($slowComponents as $component => $times) {
        echo "- $component (" . round($times['avg'], 2) . "ms)\n";
    }
    echo "\nActions recommandées:\n";
    echo "- Optimiser les requêtes de base de données\n";
    echo "- Mettre en cache les résultats fréquents\n";
    echo "- Réduire le nombre d'opérations par requête\n";
} else {
    echo "✅ Tous les composants ont des performances acceptables!\n";
    echo "L'architecture est bien optimisée.\n";
}

echo "\n📅 Benchmark effectué le: " . date('Y-m-d H:i:s') . "\n";
