<?php
/**
 * Test de diagnostic pour les logs de sécurité
 */

require_once __DIR__ . '/app/helpers/security_logger.php';

echo "🔍 DIAGNOSTIC DES LOGS DE SÉCURITÉ\n";
echo "==================================\n\n";

// Test 1: Vérifier le dossier
$logDir = __DIR__ . '/app/logs/security';
echo "1️⃣ Vérification du dossier de logs:\n";
echo "   Dossier: " . $logDir . "\n";
echo "   Existe: " . (is_dir($logDir) ? 'Oui' : 'Non') . "\n";
echo "   Accessible en lecture: " . (is_readable($logDir) ? 'Oui' : 'Non') . "\n";
echo "   Accessible en écriture: " . (is_writable($logDir) ? 'Oui' : 'Non') . "\n\n";

// Test 2: Créer un fichier de test
$testFile = $logDir . '/test_write.txt';
echo "2️⃣ Test d'écriture directe:\n";
$testContent = "Test d'écriture - " . date('Y-m-d H:i:s');
$writeResult = file_put_contents($testFile, $testContent);
echo "   Résultat: " . ($writeResult !== false ? 'Succès' : 'Échec') . "\n";
if ($writeResult !== false) {
    echo "   Fichier créé: " . $testFile . "\n";
    echo "   Taille: " . filesize($testFile) . " bytes\n";
    // Nettoyer
    unlink($testFile);
} else {
    echo "   Erreur: " . error_get_last()['message'] . "\n";
}
echo "\n";

// Test 3: Initialiser le logger
echo "3️⃣ Test d'initialisation du logger:\n";
SecurityLogger::init();
echo "   Logger initialisé\n\n";

// Test 4: Tester un log simple
echo "4️⃣ Test de log simple:\n";
SecurityLogger::log('TEST_DEBUG', 'INFO', 'Test de diagnostic', ['test' => true]);
echo "   Log envoyé\n\n";

// Test 5: Vérifier si le fichier a été créé
$logFile = $logDir . '/security_' . date('Y-m-d') . '.log';
echo "5️⃣ Vérification du fichier de log:\n";
echo "   Fichier attendu: " . $logFile . "\n";
echo "   Existe: " . (file_exists($logFile) ? 'Oui' : 'Non') . "\n";
if (file_exists($logFile)) {
    echo "   Taille: " . filesize($logFile) . " bytes\n";
    echo "   Contenu (dernières lignes):\n";
    $content = file_get_contents($logFile);
    $lines = explode("\n", trim($content));
    $lastLines = array_slice($lines, -3);
    foreach ($lastLines as $line) {
        if (!empty($line)) {
            echo "     " . $line . "\n";
        }
    }
} else {
    echo "   ❌ Fichier non créé\n";
    echo "   Vérifiez les permissions du dossier\n";
}
echo "\n";

// Test 6: Vérifier les permissions du dossier parent
echo "6️⃣ Vérification des permissions:\n";
$parentDir = dirname($logDir);
echo "   Dossier parent: " . $parentDir . "\n";
echo "   Existe: " . (is_dir($parentDir) ? 'Oui' : 'Non') . "\n";
echo "   Accessible en écriture: " . (is_writable($parentDir) ? 'Oui' : 'Non') . "\n";

echo "\n🎯 DIAGNOSTIC TERMINÉ\n";
echo "=====================\n";
?>
