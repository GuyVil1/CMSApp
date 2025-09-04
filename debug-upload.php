<?php
/**
 * Script de debug pour l'upload - Identifie les problèmes de connexion
 */

// Configuration
header('Content-Type: text/html; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html>
<html lang='fr'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Debug Upload - Diagnostic des Problèmes</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .success { color: green; background: #e8f5e8; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .error { color: red; background: #ffe8e8; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .warning { color: orange; background: #fff3e8; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .info { color: blue; background: #e8f0ff; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .test-section { border: 1px solid #ddd; margin: 20px 0; padding: 15px; border-radius: 5px; }
        .btn { background: #007cba; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; margin: 5px; }
        .btn:hover { background: #005a87; }
        pre { background: #f8f8f8; padding: 10px; border-radius: 5px; overflow-x: auto; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🔍 Debug Upload - Diagnostic des Problèmes</h1>
        <p>Ce script va tester tous les composants nécessaires à l'upload d'images.</p>";

// Test 1: Vérification des prérequis système
echo "<div class='test-section'>
    <h2>🔧 Test 1: Prérequis Système</h2>";

// Vérifier PHP
echo "<h3>Version PHP</h3>";
echo "<p class='info'>Version PHP: " . phpversion() . "</p>";
echo "<p class='info'>Extensions chargées: " . implode(', ', get_loaded_extensions()) . "</p>";

// Vérifier GD
if (extension_loaded('gd')) {
    $gdInfo = gd_info();
    echo "<p class='success'>✅ Extension GD disponible</p>";
    echo "<p>Version GD: " . $gdInfo['GD Version'] . "</p>";
    echo "<p>Support WebP: " . (function_exists('imagewebp') ? '✅ Oui' : '❌ Non') . "</p>";
    echo "<p>Support JPG: " . (function_exists('imagejpeg') ? '✅ Oui' : '❌ Non') . "</p>";
    echo "<p>Support PNG: " . (function_exists('imagepng') ? '✅ Oui' : '❌ Non') . "</p>";
} else {
    echo "<p class='error'>❌ Extension GD non disponible</p>";
}

// Vérifier les permissions
echo "<h3>Permissions des Dossiers</h3>";
$uploadDir = 'public/uploads/';
if (is_dir($uploadDir)) {
    echo "<p class='success'>✅ Dossier uploads existe</p>";
    echo "<p>Permissions: " . substr(sprintf('%o', fileperms($uploadDir)), -4) . "</p>";
    echo "<p>Écrivable: " . (is_writable($uploadDir) ? '✅ Oui' : '❌ Non') . "</p>";
} else {
    echo "<p class='warning'>⚠️ Dossier uploads n'existe pas</p>";
    if (mkdir($uploadDir, 0755, true)) {
        echo "<p class='success'>✅ Dossier uploads créé</p>";
    } else {
        echo "<p class='error'>❌ Impossible de créer le dossier uploads</p>";
    }
}

echo "</div>";

// Test 2: Vérification des classes
echo "<div class='test-section'>
    <h2>📚 Test 2: Classes et Dépendances</h2>";

// Vérifier ImageOptimizer
if (class_exists('ImageOptimizer')) {
    echo "<p class='success'>✅ Classe ImageOptimizer chargée</p>";
} else {
    echo "<p class='error'>❌ Classe ImageOptimizer non trouvée</p>";
    echo "<p>Tentative de chargement...</p>";
    if (file_exists('app/utils/ImageOptimizer.php')) {
        require_once 'app/utils/ImageOptimizer.php';
        if (class_exists('ImageOptimizer')) {
            echo "<p class='success'>✅ Classe ImageOptimizer chargée avec succès</p>";
        } else {
            echo "<p class='error'>❌ Échec du chargement de ImageOptimizer</p>";
        }
    } else {
        echo "<p class='error'>❌ Fichier app/utils/ImageOptimizer.php introuvable</p>";
    }
}

// Vérifier MediaController
if (class_exists('Admin\MediaController')) {
    echo "<p class='success'>✅ Classe MediaController chargée</p>";
} else {
    echo "<p class='error'>❌ Classe MediaController non trouvée</p>";
}

// Vérifier les modèles
if (class_exists('Media')) {
    echo "<p class='success'>✅ Modèle Media chargé</p>";
} else {
    echo "<p class='error'>❌ Modèle Media non trouvé</p>";
}

echo "</div>";

// Test 3: Test de l'optimisation
echo "<div class='test-section'>
    <h2>🔄 Test 3: Test d'Optimisation</h2>";

if (class_exists('ImageOptimizer')) {
    try {
        // Créer un dossier de test
        $testDir = 'debug_test';
        if (!is_dir($testDir)) {
            mkdir($testDir, 0755, true);
        }
        
        // Créer une image de test
        $testImagePath = $testDir . '/debug_test.png';
        $testImage = imagecreate(100, 100);
        $bgColor = imagecolorallocate($testImage, 255, 255, 255);
        $textColor = imagecolorallocate($testImage, 0, 0, 0);
        imagestring($testImage, 3, 20, 40, 'TEST', $textColor);
        imagepng($testImage, $testImagePath);
        imagedestroy($testImage);
        
        if (file_exists($testImagePath)) {
            echo "<p class='success'>✅ Image de test créée</p>";
            echo "<p>Taille: " . number_format(filesize($testImagePath)) . " bytes</p>";
            
            // Tester l'optimisation
            echo "<p class='info'>🔄 Test de l'optimisation...</p>";
            
            $result = ImageOptimizer::optimizeImage($testImagePath, $testDir);
            
            if ($result['success']) {
                echo "<p class='success'>✅ Optimisation réussie !</p>";
                echo "<p>Compression: " . $result['compression_ratio'] . "%</p>";
                echo "<p>Formats créés: " . implode(', ', $result['formats']) . "</p>";
            } else {
                echo "<p class='error'>❌ Échec de l'optimisation: " . ($result['error'] ?? 'Erreur inconnue') . "</p>";
            }
            
            // Nettoyer
            $files = glob($testDir . '/*');
            foreach ($files as $file) {
                if (is_file($file)) unlink($file);
            }
            rmdir($testDir);
            
        } else {
            echo "<p class='error'>❌ Impossible de créer l'image de test</p>";
        }
        
    } catch (Exception $e) {
        echo "<p class='error'>❌ Erreur lors du test: " . $e->getMessage() . "</p>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
    }
} else {
    echo "<p class='warning'>⚠️ Impossible de tester l'optimisation - classe ImageOptimizer non disponible</p>";
}

echo "</div>";

// Test 4: Test de connexion à la base de données
echo "<div class='test-section'>
    <h2>🗄️ Test 4: Base de Données</h2>";

// Vérifier si on peut accéder aux modèles
try {
    if (class_exists('Media')) {
        echo "<p class='success'>✅ Modèle Media accessible</p>";
        
        // Tenter de compter les médias
        try {
            $count = Media::count();
            echo "<p class='success'>✅ Connexion BD OK - " . $count . " médias trouvés</p>";
        } catch (Exception $e) {
            echo "<p class='error'>❌ Erreur BD: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p class='warning'>⚠️ Modèle Media non accessible</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ Erreur d'accès aux modèles: " . $e->getMessage() . "</p>";
}

echo "</div>";

// Test 5: Test d'upload simple
echo "<div class='test-section'>
    <h2>📤 Test 5: Test d'Upload Simple</h2>";

echo "<form action='debug-upload-test.php' method='post' enctype='multipart/form-data'>
    <p><strong>Sélectionnez une image de test:</strong></p>
    <input type='file' name='file' accept='image/*' required>
    <br><br>
    <button type='submit' class='btn'>🧪 Tester l'Upload</button>
</form>";

echo "</div>";

// Résumé et recommandations
echo "<div class='test-section'>
    <h2>📋 Résumé et Recommandations</h2>";

echo "<h3>Problèmes potentiels identifiés :</h3>
<ul>
    <li><strong>Extension GD :</strong> " . (extension_loaded('gd') ? '✅ OK' : '❌ MANQUANTE') . "</li>
    <li><strong>Support WebP :</strong> " . (function_exists('imagewebp') ? '✅ OK' : '❌ MANQUANT') . "</li>
    <li><strong>Dossier uploads :</strong> " . (is_dir($uploadDir) && is_writable($uploadDir) ? '✅ OK' : '❌ PROBLÈME') . "</li>
    <li><strong>Classe ImageOptimizer :</strong> " . (class_exists('ImageOptimizer') ? '✅ OK' : '❌ MANQUANTE') . "</li>
    <li><strong>Classe MediaController :</strong> " . (class_exists('Admin\MediaController') ? '✅ OK' : '❌ MANQUANTE') . "</li>
</ul>";

echo "<h3>Actions recommandées :</h3>
<ol>
    <li>Vérifiez que l'extension GD est activée dans php.ini</li>
    <li>Vérifiez les permissions du dossier public/uploads/</li>
    <li>Regardez les logs d'erreur PHP pour plus de détails</li>
    <li>Testez l'upload avec le formulaire ci-dessus</li>
</ol>";

echo "</div>";

echo "</div></body></html>";
?>
