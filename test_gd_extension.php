<?php
/**
 * Test de l'extension GD
 */

echo "<h1>🔍 TEST EXTENSION GD</h1>";

// Test 1: Vérifier si GD est chargé
echo "<h2>1️⃣ Vérification de l'extension GD</h2>";
if (extension_loaded('gd')) {
    echo "✅ Extension GD chargée<br>";
} else {
    echo "❌ Extension GD NON chargée - C'EST LE PROBLÈME !<br>";
    echo "❌ MediaController plante à la ligne 291<br>";
    exit;
}

// Test 2: Informations sur GD
echo "<h2>2️⃣ Informations sur GD</h2>";
$gdInfo = gd_info();
echo "✅ Version GD: " . $gdInfo['GD Version'] . "<br>";
echo "✅ Support JPEG: " . ($gdInfo['JPEG Support'] ? 'Oui' : 'Non') . "<br>";
echo "✅ Support PNG: " . ($gdInfo['PNG Support'] ? 'Oui' : 'Non') . "<br>";
echo "✅ Support WebP: " . ($gdInfo['WebP Support'] ? 'Oui' : 'Non') . "<br>";
echo "✅ Support GIF: " . ($gdInfo['GIF Read Support'] ? 'Oui' : 'Non') . "<br>";

// Test 3: Test de création d'image
echo "<h2>3️⃣ Test de création d'image</h2>";
try {
    $image = imagecreate(100, 100);
    if ($image) {
        echo "✅ Création d'image réussie<br>";
        imagedestroy($image);
    } else {
        echo "❌ Échec de création d'image<br>";
    }
} catch (Exception $e) {
    echo "❌ Erreur création d'image: " . $e->getMessage() . "<br>";
}

// Test 4: Test de chargement d'image
echo "<h2>4️⃣ Test de chargement d'image</h2>";
try {
    // Créer une image de test
    $testImage = imagecreate(50, 50);
    $white = imagecolorallocate($testImage, 255, 255, 255);
    $black = imagecolorallocate($testImage, 0, 0, 0);
    imagestring($testImage, 5, 10, 20, 'TEST', $black);
    
    // Sauvegarder en PNG
    $testPath = 'test_gd_image.png';
    if (imagepng($testImage, $testPath)) {
        echo "✅ Sauvegarde PNG réussie<br>";
        
        // Tester le chargement
        $loadedImage = imagecreatefrompng($testPath);
        if ($loadedImage) {
            echo "✅ Chargement PNG réussi<br>";
            imagedestroy($loadedImage);
        } else {
            echo "❌ Échec de chargement PNG<br>";
        }
        
        // Nettoyer
        unlink($testPath);
    } else {
        echo "❌ Échec de sauvegarde PNG<br>";
    }
    
    imagedestroy($testImage);
} catch (Exception $e) {
    echo "❌ Erreur test image: " . $e->getMessage() . "<br>";
}

// Test 5: Vérifier les fonctions GD disponibles
echo "<h2>5️⃣ Fonctions GD disponibles</h2>";
$gdFunctions = [
    'imagecreate',
    'imagecreatefrompng',
    'imagecreatefromjpeg',
    'imagecreatefromgif',
    'imagecreatefromwebp',
    'imagepng',
    'imagejpeg',
    'imagegif',
    'imagewebp',
    'imagesx',
    'imagesy',
    'imagedestroy'
];

foreach ($gdFunctions as $func) {
    if (function_exists($func)) {
        echo "✅ $func<br>";
    } else {
        echo "❌ $func - MANQUANTE<br>";
    }
}

echo "<hr>";
echo "<p><a href='/test_security_helper.php'>← Retour au test SecurityHelper</a></p>";
?>
