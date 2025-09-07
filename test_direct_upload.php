<?php
/**
 * Test d'appel direct de MediaController::upload()
 */

// Démarrer la session
session_start();

// Charger la configuration
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/core/Auth.php';
require_once __DIR__ . '/app/helpers/security_logger_simple.php';
require_once __DIR__ . '/app/helpers/rate_limit_helper.php';
require_once __DIR__ . '/app/helpers/security_helper.php';

// Initialiser l'authentification
Auth::init();

echo "<h1>🔍 TEST APPEL DIRECT MediaController::upload()</h1>";

// Vérifier l'authentification
if (!Auth::isLoggedIn()) {
    echo "❌ Vous devez être connecté<br>";
    echo "<a href='/login'>Se connecter</a><br>";
    exit;
}

echo "✅ Utilisateur connecté: " . Auth::getUser()['login'] . "<br>";

// Si c'est un POST, traiter l'upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    echo "<h2>📤 APPEL DIRECT DE LA MÉTHODE UPLOAD</h2>";
    
    // Charger le contrôleur
    require_once __DIR__ . '/app/controllers/admin/MediaController.php';
    
    try {
        echo "<h3>1️⃣ Création de l'instance</h3>";
        $controller = new Admin\MediaController();
        echo "✅ Instance créée<br>";
        
        echo "<h3>2️⃣ Préparation des données</h3>";
        echo "✅ Fichier: " . $_FILES['file']['name'] . "<br>";
        echo "✅ Taille: " . number_format($_FILES['file']['size']) . " bytes<br>";
        echo "✅ Type: " . $_FILES['file']['type'] . "<br>";
        
        // Vérifier le token CSRF
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!Auth::verifyCsrfToken($csrfToken)) {
            echo "❌ Token CSRF invalide<br>";
            exit;
        }
        echo "✅ Token CSRF valide<br>";
        
        echo "<h3>3️⃣ Appel de la méthode upload()</h3>";
        
        // Capturer la sortie
        ob_start();
        
        // Appeler la méthode upload
        $controller->upload();
        
        // Récupérer la sortie
        $output = ob_get_clean();
        
        echo "✅ Méthode upload() exécutée<br>";
        echo "✅ Sortie capturée: " . strlen($output) . " caractères<br>";
        
        if (!empty($output)) {
            echo "<h3>4️⃣ Contenu de la sortie</h3>";
            echo "<pre>" . htmlspecialchars($output) . "</pre>";
        } else {
            echo "⚠️ Aucune sortie générée<br>";
        }
        
        // Vérifier les headers
        echo "<h3>5️⃣ Vérification des headers</h3>";
        $headers = headers_list();
        if (!empty($headers)) {
            echo "✅ Headers envoyés:<br>";
            foreach ($headers as $header) {
                echo "&nbsp;&nbsp;• " . htmlspecialchars($header) . "<br>";
            }
        } else {
            echo "⚠️ Aucun header envoyé<br>";
        }
        
        // Vérifier le code de réponse
        echo "<h3>6️⃣ Code de réponse HTTP</h3>";
        $httpCode = http_response_code();
        echo "✅ Code HTTP: " . $httpCode . "<br>";
        
        if ($httpCode === 200) {
            echo "✅ Code 200 - Succès<br>";
        } else {
            echo "⚠️ Code non-200: " . $httpCode . "<br>";
        }
        
    } catch (Exception $e) {
        echo "<h3>❌ ERREUR DANS L'APPEL DIRECT</h3>";
        echo "❌ Message: " . $e->getMessage() . "<br>";
        echo "❌ Code: " . $e->getCode() . "<br>";
        echo "❌ Fichier: " . $e->getFile() . ":" . $e->getLine() . "<br>";
        echo "❌ Trace: " . $e->getTraceAsString() . "<br>";
    }
    
    echo "<hr>";
}

// Formulaire de test
echo "<h2>📤 FORMULAIRE DE TEST</h2>";
$csrfToken = Auth::generateCsrfToken();
echo '<form action="/test_direct_upload.php" method="POST" enctype="multipart/form-data">';
echo '<input type="hidden" name="csrf_token" value="' . $csrfToken . '">';
echo '<input type="file" name="file" accept="image/*" required><br><br>';
echo '<input type="submit" value="Tester l\'appel direct">';
echo '</form>';

echo "<hr>";
echo "<p><a href='/test_routing.php'>← Retour au test de routage</a></p>";
?>
