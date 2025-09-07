<?php
/**
 * Test de performance du cache optimisé (mémoire)
 */

session_start();
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/app/helpers/MemoryCache.php';
require_once __DIR__ . '/app/models/Setting.php';

echo "<h1>⚡ TEST PERFORMANCE CACHE OPTIMISÉ</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    .success { color: green; font-weight: bold; }
    .error { color: red; font-weight: bold; }
    .warning { color: orange; font-weight: bold; }
    .info { color: blue; }
    .test-section { margin: 20px 0; padding: 15px; border: 1px solid #ccc; border-radius: 5px; }
    .metric { background: #f5f5f5; padding: 10px; margin: 5px 0; border-radius: 5px; }
    .highlight { background: #e8f5e8; border: 2px solid #4caf50; }
</style>";

// Test 1: Vider le cache mémoire
MemoryCache::flush();

// Test 2: Test de performance - Sans cache
echo "<div class='test-section'>";
echo "<h2>📊 TEST PERFORMANCE - SANS CACHE</h2>";

$iterations = 1000; // Plus d'itérations pour voir la différence
$startTime = microtime(true);

for ($i = 0; $i < $iterations; $i++) {
    // Simulation d'une requête DB lourde
    $result = Setting::get('site_name', 'Default Site');
}

$endTime = microtime(true);
$timeWithoutCache = ($endTime - $startTime) * 1000; // en ms

echo "<div class='metric'>";
echo "<strong>Sans cache:</strong> {$iterations} requêtes en " . number_format($timeWithoutCache, 2) . "ms<br>";
echo "<strong>Moyenne:</strong> " . number_format($timeWithoutCache / $iterations, 4) . "ms par requête";
echo "</div>";

echo "</div>";

// Test 3: Test de performance - Avec cache mémoire
echo "<div class='test-section'>";
echo "<h2>⚡ TEST PERFORMANCE - AVEC CACHE MÉMOIRE</h2>";

$startTime = microtime(true);

for ($i = 0; $i < $iterations; $i++) {
    // Même requête mais avec cache mémoire
    $result = Setting::get('site_name', 'Default Site');
}

$endTime = microtime(true);
$timeWithCache = ($endTime - $startTime) * 1000; // en ms

echo "<div class='metric highlight'>";
echo "<strong>Avec cache mémoire:</strong> {$iterations} requêtes en " . number_format($timeWithCache, 2) . "ms<br>";
echo "<strong>Moyenne:</strong> " . number_format($timeWithCache / $iterations, 4) . "ms par requête";
echo "</div>";

// Calcul de l'amélioration
$improvement = (($timeWithoutCache - $timeWithCache) / $timeWithoutCache) * 100;

echo "<div class='metric highlight'>";
echo "<strong>Amélioration:</strong> " . number_format($improvement, 1) . "% plus rapide<br>";
echo "<strong>Gain de temps:</strong> " . number_format($timeWithoutCache - $timeWithCache, 2) . "ms<br>";
echo "<strong>Facteur d'accélération:</strong> " . number_format($timeWithoutCache / $timeWithCache, 1) . "x";
echo "</div>";

echo "</div>";

// Test 4: Statistiques du cache mémoire
echo "<div class='test-section'>";
echo "<h2>📈 STATISTIQUES CACHE MÉMOIRE</h2>";

$stats = MemoryCache::getStats();
echo "<div class='info'>Statistiques du cache mémoire:</div>";
echo "<pre>" . print_r($stats, true) . "</pre>";

echo "</div>";

// Test 5: Test de charge intensive
echo "<div class='test-section'>";
echo "<h2>🔥 TEST DE CHARGE INTENSIVE</h2>";

$intensiveIterations = 10000;
$startTime = microtime(true);

for ($i = 0; $i < $intensiveIterations; $i++) {
    $result = Setting::get('site_name', 'Default Site');
}

$endTime = microtime(true);
$intensiveTime = ($endTime - $startTime) * 1000;

echo "<div class='metric highlight'>";
echo "<strong>Charge intensive:</strong> {$intensiveIterations} requêtes en " . number_format($intensiveTime, 2) . "ms<br>";
echo "<strong>Moyenne:</strong> " . number_format($intensiveTime / $intensiveIterations, 4) . "ms par requête<br>";
echo "<strong>Requêtes/seconde:</strong> " . number_format(1000 / ($intensiveTime / $intensiveIterations), 0);
echo "</div>";

echo "</div>";

// Test 6: Recommandations
echo "<div class='test-section'>";
echo "<h2>💡 RECOMMANDATIONS</h2>";

if ($improvement > 80) {
    echo "<div class='success'>✅ EXCELLENT! Le cache mémoire améliore drastiquement les performances</div>";
} elseif ($improvement > 50) {
    echo "<div class='success'>✅ TRÈS BON! Le cache mémoire améliore significativement les performances</div>";
} elseif ($improvement > 20) {
    echo "<div class='warning'>⚠️ BON! Le cache mémoire améliore les performances</div>";
} else {
    echo "<div class='error'>❌ Le cache mémoire n'améliore pas significativement les performances</div>";
}

echo "<div class='info'>";
echo "<strong>Recommandations:</strong><br>";
echo "• Cache mémoire parfait pour les données fréquemment accédées<br>";
echo "• Utiliser Redis en production pour la persistance<br>";
echo "• Implémenter le cache sur tous les modèles critiques<br>";
echo "• Surveiller le taux de hit du cache<br>";
echo "• Nettoyer périodiquement les éléments expirés";
echo "</div>";

echo "</div>";

// Test 7: Nettoyage
echo "<div class='test-section'>";
echo "<h2>🧹 NETTOYAGE</h2>";

$cleaned = MemoryCache::cleanExpired();
echo "<div class='info'>Éléments expirés nettoyés: {$cleaned}</div>";

$finalStats = MemoryCache::getStats();
echo "<div class='info'>Statistiques finales:</div>";
echo "<pre>" . print_r($finalStats, true) . "</pre>";

echo "</div>";
?>
