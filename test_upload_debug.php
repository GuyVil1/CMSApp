<?php
/**
 * Test de diagnostic pour l'upload d'images
 */

// Démarrer la session
session_start();

// Charger la configuration
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/core/Auth.php';
require_once __DIR__ . '/app/helpers/security_logger_simple.php';

// Initialiser l'authentification
Auth::init();

echo "<h1>🔍 DIAGNOSTIC UPLOAD D'IMAGES</h1>";

// 1. Vérifier l'authentification
echo "<h2>1️⃣ Vérification de l'authentification</h2>";
if (Auth::isLoggedIn()) {
    $user = Auth::getUser();
    echo "✅ Utilisateur connecté: " . ($user['login'] ?? 'N/A') . "<br>";
    echo "✅ ID utilisateur: " . Auth::getUserId() . "<br>";
    echo "✅ Rôle: " . (Auth::hasRole('admin') ? 'Admin' : 'User') . "<br>";
} else {
    echo "❌ Utilisateur non connecté<br>";
    echo "<a href='/login'>Se connecter</a><br>";
}

// 2. Vérifier le token CSRF
echo "<h2>2️⃣ Vérification du token CSRF</h2>";
$csrfToken = Auth::generateCsrfToken();
echo "✅ Token CSRF généré: " . substr($csrfToken, 0, 20) . "...<br>";

// 3. Test de validation CSRF
if (Auth::verifyCsrfToken($csrfToken)) {
    echo "✅ Validation CSRF fonctionne<br>";
} else {
    echo "❌ Validation CSRF échoue<br>";
}

// 4. Vérifier les permissions de dossiers
echo "<h2>3️⃣ Vérification des permissions</h2>";
$uploadDirs = [
    'public/uploads/',
    'public/uploads/article/',
    'public/uploads/games/',
    'app/cache/rate_limit/',
    'app/logs/security/'
];

foreach ($uploadDirs as $dir) {
    if (is_dir($dir)) {
        if (is_writable($dir)) {
            echo "✅ $dir - Existe et accessible en écriture<br>";
        } else {
            echo "❌ $dir - Existe mais non accessible en écriture<br>";
        }
    } else {
        echo "⚠️ $dir - N'existe pas<br>";
        if (mkdir($dir, 0755, true)) {
            echo "✅ $dir - Créé avec succès<br>";
        } else {
            echo "❌ $dir - Impossible de créer<br>";
        }
    }
}

// 5. Test de log de sécurité
echo "<h2>4️⃣ Test de log de sécurité</h2>";
try {
    SecurityLoggerSimple::logLoginSuccess('test_user', 1);
    echo "✅ Log de sécurité fonctionne<br>";
} catch (Exception $e) {
    echo "❌ Erreur de log: " . $e->getMessage() . "<br>";
}

// 6. Formulaire de test d'upload
echo "<h2>5️⃣ Formulaire de test d'upload</h2>";
if (Auth::isLoggedIn()) {
    echo '<form action="/media/upload" method="POST" enctype="multipart/form-data">';
    echo '<input type="hidden" name="csrf_token" value="' . $csrfToken . '">';
    echo '<input type="file" name="file" accept="image/*" required><br><br>';
    echo '<input type="submit" value="Tester l\'upload">';
    echo '</form>';
} else {
    echo "Connectez-vous pour tester l'upload<br>";
}

// 7. Vérifier les logs d'erreur
echo "<h2>6️⃣ Vérification des logs d'erreur</h2>";
$errorLog = ini_get('error_log');
if ($errorLog) {
    echo "✅ Log d'erreur configuré: $errorLog<br>";
    if (file_exists($errorLog)) {
        $size = filesize($errorLog);
        echo "✅ Fichier de log existe (taille: " . number_format($size) . " bytes)<br>";
    } else {
        echo "⚠️ Fichier de log n'existe pas encore<br>";
    }
} else {
    echo "⚠️ Log d'erreur non configuré<br>";
}

echo "<hr>";
echo "<p><a href='/test_security_web.php'>← Retour aux tests de sécurité</a></p>";
?>
