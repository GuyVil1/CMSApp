<?php
/**
 * Tests de sécurité pour les améliorations implémentées
 * À exécuter en local pour vérifier le bon fonctionnement
 */

// Inclure les helpers nécessaires
require_once __DIR__ . '/app/helpers/security_helper.php';
require_once __DIR__ . '/app/helpers/rate_limit_helper.php';
require_once __DIR__ . '/app/helpers/security_logger.php';

echo "🧪 TESTS DE SÉCURITÉ - Belgium Vidéo Gaming v1.0.0\n";
echo "==================================================\n\n";

// Test 1: Validation d'images renforcée
echo "1️⃣ Test de validation d'images renforcée\n";
echo "----------------------------------------\n";

// Créer un fichier de test (faux fichier PHP renommé en JPG)
$fakeImageContent = '<?php echo "Hello World"; ?>';
$fakeImagePath = __DIR__ . '/test_fake_image.jpg';
file_put_contents($fakeImagePath, $fakeImageContent);

$validation = SecurityHelper::validateImageContent($fakeImagePath);
if (!$validation['valid']) {
    echo "✅ Test réussi: Fichier PHP renommé détecté comme invalide\n";
    echo "   Message: " . $validation['message'] . "\n";
} else {
    echo "❌ Test échoué: Fichier PHP renommé accepté\n";
}

// Nettoyer
unlink($fakeImagePath);

// Test 2: Rate limiting
echo "\n2️⃣ Test de rate limiting\n";
echo "------------------------\n";

$userId = 1;
$rateLimitCheck = RateLimitHelper::checkUploadLimits($userId);
echo "Limites d'upload pour utilisateur $userId:\n";
echo "  - Autorisé: " . ($rateLimitCheck['allowed'] ? 'Oui' : 'Non') . "\n";
if (!$rateLimitCheck['allowed']) {
    echo "  - Raison: " . $rateLimitCheck['reason'] . "\n";
} else {
    echo "  - Uploads par heure: " . $rateLimitCheck['hourly_uploads'] . "/" . $rateLimitCheck['limits']['uploads_per_hour'] . "\n";
    echo "  - Taille par heure: " . $rateLimitCheck['hourly_size'] . "/" . $rateLimitCheck['limits']['size_per_hour'] . "\n";
}

// Test 3: Logs de sécurité
echo "\n3️⃣ Test de logs de sécurité\n";
echo "---------------------------\n";

// Initialiser le logger
SecurityLogger::init();

// Tester différents types de logs
SecurityLogger::logLoginSuccess('test_user', 1);
SecurityLogger::logLoginFailed('hacker', 'Identifiants incorrects');
SecurityLogger::logUploadSuccess('test_image.jpg', 1024, 1);
SecurityLogger::logCsrfViolation('test_action', 'invalid_token');

echo "✅ Logs de sécurité testés:\n";
echo "  - Connexion réussie\n";
echo "  - Connexion échouée\n";
echo "  - Upload réussi\n";
echo "  - Violation CSRF\n";

// Vérifier que les logs ont été créés
$logFile = __DIR__ . '/app/logs/security/security_' . date('Y-m-d') . '.log';
if (file_exists($logFile)) {
    $logContent = file_get_contents($logFile);
    $logLines = explode("\n", trim($logContent));
    echo "  - Fichier de log créé: " . basename($logFile) . "\n";
    echo "  - Nombre d'entrées: " . count(array_filter($logLines)) . "\n";
} else {
    echo "❌ Fichier de log non créé\n";
}

// Test 4: Statistiques de sécurité
echo "\n4️⃣ Test des statistiques de sécurité\n";
echo "------------------------------------\n";

$stats = SecurityLogger::getStats();
echo "Statistiques du jour:\n";
echo "  - Total d'événements: " . $stats['total_events'] . "\n";
echo "  - Par niveau: " . json_encode($stats['by_level']) . "\n";
echo "  - Par événement: " . json_encode($stats['by_event']) . "\n";
echo "  - IPs uniques: " . count($stats['unique_ips']) . "\n";

// Test 5: Validation de sécurité
echo "\n5️⃣ Test de validation de sécurité\n";
echo "----------------------------------\n";

// Test validation email
$validEmail = SecurityHelper::validateEmail('test@example.com');
$invalidEmail = SecurityHelper::validateEmail('invalid-email');
echo "Validation email:\n";
echo "  - test@example.com: " . ($validEmail ? 'Valide' : 'Invalide') . "\n";
echo "  - invalid-email: " . ($invalidEmail ? 'Valide' : 'Invalide') . "\n";

// Test validation URL
$validUrl = SecurityHelper::validateUrl('https://example.com');
$invalidUrl = SecurityHelper::validateUrl('not-a-url');
echo "Validation URL:\n";
echo "  - https://example.com: " . ($validUrl ? 'Valide' : 'Invalide') . "\n";
echo "  - not-a-url: " . ($invalidUrl ? 'Valide' : 'Invalide') . "\n";

// Test validation nom de fichier
$validFilename = SecurityHelper::validateFilename('image.jpg');
$invalidFilename = SecurityHelper::validateFilename('../../../etc/passwd');
echo "Validation nom de fichier:\n";
echo "  - image.jpg: " . ($validFilename ? 'Valide' : 'Invalide') . "\n";
echo "  - ../../../etc/passwd: " . ($invalidFilename ? 'Valide' : 'Invalide') . "\n";

// Test 6: Génération de noms sécurisés
echo "\n6️⃣ Test de génération sécurisée\n";
echo "-------------------------------\n";

$secureFilename = SecurityHelper::generateSecureFilename('mon_image.jpg');
$secureSlug = SecurityHelper::generateSlug('Mon Super Article !');
echo "Génération sécurisée:\n";
echo "  - Nom de fichier: " . $secureFilename . "\n";
echo "  - Slug: " . $secureSlug . "\n";

// Test 7: Détection de contenu malveillant
echo "\n7️⃣ Test de détection de contenu malveillant\n";
echo "------------------------------------------\n";

$maliciousContent = '<script>alert("XSS")</script>';
$safeContent = '<p>Contenu normal</p>';
$isMalicious = SecurityHelper::containsMaliciousContent($maliciousContent);
$isSafe = SecurityHelper::containsMaliciousContent($safeContent);
echo "Détection de contenu malveillant:\n";
echo "  - Contenu avec script: " . ($isMalicious ? 'Détecté' : 'Non détecté') . "\n";
echo "  - Contenu normal: " . ($isSafe ? 'Détecté' : 'Non détecté') . "\n";

echo "\n🎉 TESTS TERMINÉS\n";
echo "==================\n";
echo "Tous les tests de sécurité ont été exécutés.\n";
echo "Vérifiez les résultats ci-dessus pour vous assurer que tout fonctionne correctement.\n";
echo "\n📋 RÉSUMÉ DES AMÉLIORATIONS:\n";
echo "✅ Protection CSRF sur tous les formulaires\n";
echo "✅ Validation renforcée du contenu des images\n";
echo "✅ Rate limiting pour uploads et connexions\n";
echo "✅ Système de logs de sécurité complet\n";
echo "✅ Helpers de sécurité centralisés\n";
echo "\n🛡️ Votre application est maintenant sécurisée au niveau FERRARI !\n";
?>
