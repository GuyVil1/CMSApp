<?php
/**
 * Test spécifique pour SecurityHelper
 */

// Démarrer la session
session_start();

// Charger la configuration
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/core/Auth.php';
require_once __DIR__ . '/app/helpers/security_helper.php';

// Initialiser l'authentification
Auth::init();

echo "<h1>🔍 TEST SecurityHelper</h1>";

// Vérifier l'authentification
if (!Auth::isLoggedIn()) {
    echo "❌ Vous devez être connecté<br>";
    echo "<a href='/login'>Se connecter</a><br>";
    exit;
}

echo "✅ Utilisateur connecté: " . Auth::getUser()['login'] . "<br>";

// Si c'est un POST, traiter l'upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    echo "<h2>📤 TEST SecurityHelper</h2>";
    
    $file = $_FILES['file'];
    echo "✅ Fichier reçu: " . $file['name'] . "<br>";
    echo "✅ Taille: " . number_format($file['size']) . " bytes<br>";
    echo "✅ Type déclaré: " . $file['type'] . "<br>";
    
    // Test 1: getRealMimeType
    echo "<h3>1️⃣ Test getRealMimeType()</h3>";
    try {
        $realMimeType = SecurityHelper::getRealMimeType($file['tmp_name']);
        echo "✅ Type MIME réel: " . $realMimeType . "<br>";
    } catch (Exception $e) {
        echo "❌ Erreur getRealMimeType: " . $e->getMessage() . "<br>";
    }
    
    // Test 2: validateImageMimeType
    echo "<h3>2️⃣ Test validateImageMimeType()</h3>";
    try {
        $isValidMime = SecurityHelper::validateImageMimeType($realMimeType);
        echo "✅ Type MIME valide: " . ($isValidMime ? 'Oui' : 'Non') . "<br>";
    } catch (Exception $e) {
        echo "❌ Erreur validateImageMimeType: " . $e->getMessage() . "<br>";
    }
    
    // Test 3: validateFileSize
    echo "<h3>3️⃣ Test validateFileSize()</h3>";
    try {
        $isValidSize = SecurityHelper::validateFileSize($file['size'], 4194304); // 4MB
        echo "✅ Taille valide: " . ($isValidSize ? 'Oui' : 'Non') . "<br>";
    } catch (Exception $e) {
        echo "❌ Erreur validateFileSize: " . $e->getMessage() . "<br>";
    }
    
    // Test 4: validateImageContent
    echo "<h3>4️⃣ Test validateImageContent()</h3>";
    try {
        $imageValidation = SecurityHelper::validateImageContent($file['tmp_name']);
        echo "✅ Validation image: " . ($imageValidation['valid'] ? 'Valide' : 'Invalide') . "<br>";
        if (!$imageValidation['valid']) {
            echo "❌ Message: " . $imageValidation['message'] . "<br>";
        } else {
            echo "✅ Dimensions: " . $imageValidation['dimensions'][0] . "x" . $imageValidation['dimensions'][1] . "<br>";
            echo "✅ MIME: " . $imageValidation['mime'] . "<br>";
        }
    } catch (Exception $e) {
        echo "❌ Erreur validateImageContent: " . $e->getMessage() . "<br>";
        echo "❌ Trace: " . $e->getTraceAsString() . "<br>";
    }
    
    // Test 5: getimagesize directement
    echo "<h3>5️⃣ Test getimagesize() direct</h3>";
    try {
        $imageInfo = getimagesize($file['tmp_name']);
        if ($imageInfo === false) {
            echo "❌ getimagesize() a échoué<br>";
        } else {
            echo "✅ getimagesize() réussi<br>";
            echo "✅ Dimensions: " . $imageInfo[0] . "x" . $imageInfo[1] . "<br>";
            echo "✅ MIME: " . $imageInfo['mime'] . "<br>";
        }
    } catch (Exception $e) {
        echo "❌ Erreur getimagesize: " . $e->getMessage() . "<br>";
    }
    
    echo "<hr>";
}

// Formulaire de test
echo "<h2>📤 FORMULAIRE DE TEST</h2>";
$csrfToken = Auth::generateCsrfToken();
echo '<form action="/test_security_helper.php" method="POST" enctype="multipart/form-data">';
echo '<input type="hidden" name="csrf_token" value="' . $csrfToken . '">';
echo '<input type="file" name="file" accept="image/*" required><br><br>';
echo '<input type="submit" value="Tester SecurityHelper">';
echo '</form>';

echo "<hr>";
echo "<p><a href='/test_upload_detailed.php'>← Retour au test d'upload</a></p>";
?>
