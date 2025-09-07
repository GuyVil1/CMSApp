<?php
/**
 * Test de performance du cache intelligent
 */

session_start();
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/app/helpers/CacheManager.php';
require_once __DIR__ . '/app/models/Setting.php';

echo "<h1>⚡ TEST PERFORMANCE CACHE INTELLIGENT</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    .success { color: green; font-weight: bold; }
    .error { color: red; font-weight: bold; }
    .warning { color: orange; font-weight: bold; }
    .info { color: blue; }
    .test-section { margin: 20px 0; padding: 15px; border: 1px solid #ccc; border-radius: 5px; }
    .metric { background: #f5f5f5; padding: 10px; margin: 5px 0; border-radius: 5px; }
</style>";

// Test 1: Initialisation du cache
echo "<div class='test-section'>";
echo "<h2>🚀 INITIALISATION DU CACHE</h2>";

$cache = CacheManager::getInstance();
$stats = $cache->getStats();

echo "<div class='info'>Driver de cache: " . ($stats['driver'] ?? 'inconnu') . "</div>";
echo "<div class='info'>Statistiques:</div>";
echo "<pre>" . print_r($stats, true) . "</pre>";

echo "</div>";

// Test 2: Test de performance - Sans cache
echo "<div class='test-section'>";
echo "<h2>📊 TEST PERFORMANCE - SANS CACHE</h2>";

$iterations = 100;
$startTime = microtime(true);

for ($i = 0; $i < $iterations; $i++) {
    // Simulation d'une requête DB lourde
    $result = Setting::get('site_name', 'Default Site');
}

$endTime = microtime(true);
$timeWithoutCache = ($endTime - $startTime) * 1000; // en ms

echo "<div class='metric'>";
echo "<strong>Sans cache:</strong> {$iterations} requêtes en " . number_format($timeWithoutCache, 2) . "ms<br>";
echo "<strong>Moyenne:</strong> " . number_format($timeWithoutCache / $iterations, 2) . "ms par requête";
echo "</div>";

echo "</div>";

// Test 3: Test de performance - Avec cache
echo "<div class='test-section'>";
echo "<h2>⚡ TEST PERFORMANCE - AVEC CACHE</h2>";

// Vider le cache pour un test propre
$cache->flush();

$startTime = microtime(true);

for ($i = 0; $i < $iterations; $i++) {
    // Même requête mais avec cache
    $result = Setting::get('site_name', 'Default Site');
}

$endTime = microtime(true);
$timeWithCache = ($endTime - $startTime) * 1000; // en ms

echo "<div class='metric'>";
echo "<strong>Avec cache:</strong> {$iterations} requêtes en " . number_format($timeWithCache, 2) . "ms<br>";
echo "<strong>Moyenne:</strong> " . number_format($timeWithCache / $iterations, 2) . "ms par requête";
echo "</div>";

// Calcul de l'amélioration
$improvement = (($timeWithoutCache - $timeWithCache) / $timeWithoutCache) * 100;

echo "<div class='metric'>";
echo "<strong>Amélioration:</strong> " . number_format($improvement, 1) . "% plus rapide<br>";
echo "<strong>Gain de temps:</strong> " . number_format($timeWithoutCache - $timeWithCache, 2) . "ms";
echo "</div>";

echo "</div>";

// Test 4: Test des fonctionnalités du cache
echo "<div class='test-section'>";
echo "<h2>🧪 TEST FONCTIONNALITÉS CACHE</h2>";

// Test put/get
$testKey = 'test_performance';
$testValue = ['data' => 'test', 'timestamp' => time()];

echo "<div class='info'>Test put/get...</div>";
$putResult = $cache->put($testKey, $testValue, 60);
$getResult = $cache->get($testKey);

if ($putResult && $getResult === $testValue) {
    echo "<div class='success'>✅ Put/Get fonctionne</div>";
} else {
    echo "<div class='error'>❌ Put/Get échoue</div>";
}

// Test has
echo "<div class='info'>Test has...</div>";
if ($cache->has($testKey)) {
    echo "<div class='success'>✅ Has fonctionne</div>";
} else {
    echo "<div class='error'>❌ Has échoue</div>";
}

// Test remember
echo "<div class='info'>Test remember...</div>";
$rememberResult = $cache->remember('test_remember', function() {
    return 'valeur générée: ' . time();
}, 60);

if ($rememberResult) {
    echo "<div class='success'>✅ Remember fonctionne: {$rememberResult}</div>";
} else {
    echo "<div class='error'>❌ Remember échoue</div>";
}

// Test forget
echo "<div class='info'>Test forget...</div>";
$forgetResult = $cache->forget($testKey);
if ($forgetResult && !$cache->has($testKey)) {
    echo "<div class='success'>✅ Forget fonctionne</div>";
} else {
    echo "<div class='error'>❌ Forget échoue</div>";
}

echo "</div>";

// Test 5: Statistiques finales
echo "<div class='test-section'>";
echo "<h2>📈 STATISTIQUES FINALES</h2>";

$finalStats = $cache->getStats();
echo "<div class='info'>Statistiques finales:</div>";
echo "<pre>" . print_r($finalStats, true) . "</pre>";

echo "</div>";

// Test 6: Recommandations
echo "<div class='test-section'>";
echo "<h2>💡 RECOMMANDATIONS</h2>";

if ($improvement > 50) {
    echo "<div class='success'>✅ Excellent! Le cache améliore significativement les performances</div>";
} elseif ($improvement > 20) {
    echo "<div class='warning'>⚠️ Bon! Le cache améliore les performances</div>";
} else {
    echo "<div class='error'>❌ Le cache n'améliore pas significativement les performances</div>";
}

echo "<div class='info'>";
echo "<strong>Recommandations:</strong><br>";
echo "• Utiliser Redis en production pour de meilleures performances<br>";
echo "• Configurer des TTL appropriés selon le type de données<br>";
echo "• Implémenter le cache sur les requêtes les plus fréquentes<br>";
echo "• Surveiller les statistiques de cache régulièrement";
echo "</div>";

echo "</div>";
?>
