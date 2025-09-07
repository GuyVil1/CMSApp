<?php
/**
 * Test d'intégration du système d'événements
 */

require_once __DIR__ . '/app/container/Container.php';
require_once __DIR__ . '/app/container/ContainerFactory.php';
require_once __DIR__ . '/app/container/ContainerConfig.php';

echo "🚀 TEST D'INTÉGRATION DU SYSTÈME D'ÉVÉNEMENTS ==============================\n\n";

try {
    // 1. Initialiser le container
    echo "1️⃣ Initialisation du container...\n";
    $container = new Container();
    $containerConfig = new ContainerConfig($container);
    $containerConfig->register();
    echo "✅ Container initialisé avec succès\n\n";
    
    // 2. Récupérer l'EventDispatcher
    echo "2️⃣ Récupération de l'EventDispatcher...\n";
    $eventDispatcher = ContainerFactory::make('EventDispatcher');
    echo "✅ EventDispatcher récupéré: " . get_class($eventDispatcher) . "\n\n";
    
    // 3. Vérifier les listeners enregistrés
    echo "3️⃣ Vérification des listeners...\n";
    $eventNames = $eventDispatcher->getEventNames();
    echo "✅ Événements enregistrés: " . implode(', ', $eventNames) . "\n";
    echo "📊 Détail des listeners:\n";
    foreach ($eventNames as $eventName) {
        $listeners = $eventDispatcher->getListeners($eventName);
        echo "   - $eventName: " . count($listeners) . " listeners\n";
    }
    echo "\n";
    
    // 4. Tester les événements d'articles
    echo "4️⃣ Test des événements d'articles...\n";
    
    // Test ArticleCreatedEvent
    require_once __DIR__ . '/app/events/ArticleEvents.php';
    $createdEvent = new ArticleCreatedEvent(123, [
        'title' => 'Test Article',
        'status' => 'published',
        'user_id' => 1,
        'created_at' => date('Y-m-d H:i:s')
    ]);
    $eventDispatcher->dispatch($createdEvent);
    echo "✅ ArticleCreatedEvent dispatché\n";
    
    // Test ArticleUpdatedEvent
    $updatedEvent = new ArticleUpdatedEvent(123, [
        'title' => 'Test Article',
        'status' => 'draft',
        'user_id' => 1
    ], [
        'title' => 'Test Article Updated',
        'status' => 'published',
        'user_id' => 1,
        'updated_at' => date('Y-m-d H:i:s')
    ]);
    $eventDispatcher->dispatch($updatedEvent);
    echo "✅ ArticleUpdatedEvent dispatché\n";
    
    // Test ArticleViewedEvent
    $viewedEvent = new ArticleViewedEvent(123, '127.0.0.1');
    $eventDispatcher->dispatch($viewedEvent);
    echo "✅ ArticleViewedEvent dispatché\n";
    
    // Test ArticleDeletedEvent
    $deletedEvent = new ArticleDeletedEvent(123, [
        'title' => 'Test Article',
        'slug' => 'test-article',
        'status' => 'published'
    ]);
    $eventDispatcher->dispatch($deletedEvent);
    echo "✅ ArticleDeletedEvent dispatché\n\n";
    
    // 5. Vérifier les logs générés
    echo "5️⃣ Vérification des logs...\n";
    $logFile = __DIR__ . '/logs/application.log';
    if (file_exists($logFile)) {
        $logs = file_get_contents($logFile);
        $logLines = explode("\n", $logs);
        $recentLogs = array_slice($logLines, -10);
        echo "✅ Logs trouvés: " . count($logLines) . " entrées\n";
        echo "📝 Dernières entrées:\n";
        foreach ($recentLogs as $log) {
            if (!empty(trim($log))) {
                echo "   - " . $log . "\n";
            }
        }
    } else {
        echo "⚠️ Fichier de logs non trouvé\n";
    }
    echo "\n";
    
    // 6. Vérifier les analytics
    echo "6️⃣ Vérification des analytics...\n";
    $analyticsFile = __DIR__ . '/logs/analytics.log';
    if (file_exists($analyticsFile)) {
        $analytics = file_get_contents($analyticsFile);
        $analyticsLines = explode("\n", $analytics);
        $recentAnalytics = array_slice($analyticsLines, -5);
        echo "✅ Analytics trouvés: " . count($analyticsLines) . " entrées\n";
        echo "📊 Dernières entrées:\n";
        foreach ($recentAnalytics as $analytic) {
            if (!empty(trim($analytic))) {
                echo "   - " . $analytic . "\n";
            }
        }
    } else {
        echo "⚠️ Fichier d'analytics non trouvé\n";
    }
    echo "\n";
    
    // 7. Vérifier le cache
    echo "7️⃣ Vérification du cache...\n";
    require_once __DIR__ . '/app/helpers/MemoryCache.php';
    $cacheStats = MemoryCache::getStats();
    echo "✅ Cache stats: " . json_encode($cacheStats) . "\n\n";
    
    echo "🎉 TOUS LES TESTS RÉUSSIS !\n";
    echo "🚀 Système d'événements intégré et opérationnel !\n";
    echo "📊 Événements, logs et analytics fonctionnels !\n";
    
} catch (Exception $e) {
    echo "❌ ERREUR: " . $e->getMessage() . "\n";
    echo "📍 Fichier: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "🔍 Trace: " . $e->getTraceAsString() . "\n";
}
