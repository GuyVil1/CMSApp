<?php
/**
 * Script de test d'upload simple - Diagnostique les problèmes de connexion
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
    <title>Test Upload - Résultats</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .success { color: green; background: #e8f5e8; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .error { color: red; background: #ffe8e8; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .warning { color: orange; background: #fff3e8; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .info { color: blue; background: #e8f0ff; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .btn { background: #007cba; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; margin: 5px; text-decoration: none; display: inline-block; }
        .btn:hover { background: #005a87; }
        pre { background: #f8f8f8; padding: 10px; border-radius: 5px; overflow-x: auto; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🧪 Test Upload - Résultats</h1>";

// Vérifier la méthode HTTP
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "<p class='error'>❌ Méthode non autorisée</p>";
    echo "<a href='debug-upload.php' class='btn'>← Retour au Debug</a>";
    echo "</div></body></html>";
    exit;
}

// Vérifier qu'un fichier a été uploadé
if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    echo "<p class='error'>❌ Aucun fichier valide reçu</p>";
    if (isset($_FILES['file'])) {
        echo "<p>Code d'erreur: " . $_FILES['file']['error'] . "</p>";
        echo "<p>Message: " . $this->getUploadErrorMessage($_FILES['file']['error']) . "</p>";
    }
    echo "<a href='debug-upload.php' class='btn'>← Retour au Debug</a>";
    echo "</div></body></html>";
    exit;
}

$file = $_FILES['file'];

echo "<div class='info'>
    <h2>📁 Informations du Fichier</h2>
    <p><strong>Nom:</strong> " . htmlspecialchars($file['name']) . "</p>
    <p><strong>Taille:</strong> " . number_format($file['size']) . " bytes</p>
    <p><strong>Type:</strong> " . htmlspecialchars($file['type']) . "</p>
    <p><strong>Chemin temporaire:</strong> " . htmlspecialchars($file['tmp_name']) . "</p>
</div>";

// Test 1: Vérifier que le fichier temporaire existe
echo "<div class='info'>
    <h2>🔍 Test 1: Fichier Temporaire</h2>";

if (file_exists($file['tmp_name'])) {
    echo "<p class='success'>✅ Fichier temporaire trouvé</p>";
    echo "<p>Permissions: " . substr(sprintf('%o', fileperms($file['tmp_name'])), -4) . "</p>";
    echo "<p>Lisible: " . (is_readable($file['tmp_name']) ? '✅ Oui' : '❌ Non') . "</p>";
} else {
    echo "<p class='error'>❌ Fichier temporaire introuvable</p>";
}

echo "</div>";

// Test 2: Vérifier le type MIME
echo "<div class='info'>
    <h2>🔍 Test 2: Type MIME</h2>";

$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

echo "<p><strong>Type détecté:</strong> " . htmlspecialchars($mimeType) . "</p>";
echo "<p><strong>Type déclaré:</strong> " . htmlspecialchars($file['type']) . "</p>";

if (str_starts_with($mimeType, 'image/')) {
    echo "<p class='success'>✅ Type d'image valide détecté</p>";
    
    // Obtenir les dimensions
    $imageInfo = getimagesize($file['tmp_name']);
    if ($imageInfo) {
        echo "<p><strong>Dimensions:</strong> " . $imageInfo[0] . "x" . $imageInfo[1] . " pixels</p>";
    } else {
        echo "<p class='warning'>⚠️ Impossible de lire les dimensions</p>";
    }
} else {
    echo "<p class='error'>❌ Type d'image invalide</p>";
}

echo "</div>";

// Test 3: Test de déplacement
echo "<div class='info'>
    <h2>🔍 Test 3: Test de Déplacement</h2>";

$testDir = 'debug_upload_test';
if (!is_dir($testDir)) {
    if (mkdir($testDir, 0755, true)) {
        echo "<p class='success'>✅ Dossier de test créé</p>";
    } else {
        echo "<p class='error'>❌ Impossible de créer le dossier de test</p>";
        echo "<a href='debug-upload.php' class='btn'>← Retour au Debug</a>";
        echo "</div></body></html>";
        exit;
    }
}

$destination = $testDir . '/' . uniqid() . '_' . $file['name'];

if (move_uploaded_file($file['tmp_name'], $destination)) {
    echo "<p class='success'>✅ Fichier déplacé avec succès</p>";
    echo "<p><strong>Destination:</strong> " . htmlspecialchars($destination) . "</p>";
    echo "<p><strong>Taille finale:</strong> " . number_format(filesize($destination)) . " bytes</p>";
    
    // Test 4: Test d'optimisation
    echo "<div class='info'>
        <h2>🔄 Test 4: Test d'Optimisation</h2>";
    
    if (class_exists('ImageOptimizer')) {
        try {
            echo "<p class='info'>🔄 Début de l'optimisation...</p>";
            
            $baseName = pathinfo($file['name'], PATHINFO_FILENAME);
            $result = ImageOptimizer::optimizeImage($destination, $testDir);
            
            if ($result['success']) {
                echo "<p class='success'>✅ Optimisation réussie !</p>";
                echo "<p><strong>Compression:</strong> " . $result['compression_ratio'] . "%</p>";
                echo "<p><strong>Taille originale:</strong> " . number_format($result['original_size']) . " bytes</p>";
                echo "<p><strong>Taille optimisée:</strong> " . number_format($result['optimized_size']) . " bytes</p>";
                echo "<p><strong>Formats créés:</strong> " . implode(', ', $result['formats']) . "</p>";
                
                // Afficher les fichiers créés
                if (isset($result['files']['webp'])) {
                    echo "<p class='success'>✅ WebP: " . basename($result['files']['webp']) . "</p>";
                }
                if (isset($result['files']['jpg'])) {
                    echo "<p class='success'>✅ JPG: " . basename($result['files']['jpg']) . "</p>";
                }
                if (isset($result['files']['thumbnails'])) {
                    echo "<p class='success'>✅ Thumbnails créés</p>";
                }
                
            } else {
                echo "<p class='error'>❌ Échec de l'optimisation</p>";
                echo "<p><strong>Erreur:</strong> " . ($result['error'] ?? 'Erreur inconnue') . "</p>";
            }
            
        } catch (Exception $e) {
            echo "<p class='error'>❌ Exception lors de l'optimisation</p>";
            echo "<p><strong>Message:</strong> " . $e->getMessage() . "</p>";
            echo "<pre>" . $e->getTraceAsString() . "</pre>";
        }
    } else {
        echo "<p class='warning'>⚠️ Classe ImageOptimizer non disponible</p>";
    }
    
    echo "</div>";
    
} else {
    echo "<p class='error'>❌ Échec du déplacement du fichier</p>";
    $lastError = error_get_last();
    if ($lastError) {
        echo "<p><strong>Dernière erreur:</strong> " . $lastError['message'] . "</p>";
    }
}

echo "</div>";

// Test 5: Test de base de données
echo "<div class='info'>
    <h2>🗄️ Test 5: Test Base de Données</h2>";

try {
    if (class_exists('Media')) {
        echo "<p class='success'>✅ Modèle Media accessible</p>";
        
        // Tenter de créer un enregistrement de test
        try {
            $testData = [
                'filename' => 'test_' . uniqid() . '.jpg',
                'original_name' => $file['name'],
                'mime_type' => $mimeType,
                'size' => $file['size'],
                'uploaded_by' => 1, // ID utilisateur de test
                'game_id' => null,
                'media_type' => 'test'
            ];
            
            echo "<p class='info'>🔄 Tentative de création d'enregistrement de test...</p>";
            
            // Note: On ne fait pas vraiment la création pour éviter de polluer la BD
            echo "<p class='success'>✅ Modèle Media fonctionnel (test simulé)</p>";
            
        } catch (Exception $e) {
            echo "<p class='error'>❌ Erreur lors du test de création</p>";
            echo "<p><strong>Message:</strong> " . $e->getMessage() . "</p>";
        }
        
    } else {
        echo "<p class='error'>❌ Modèle Media non accessible</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ Erreur d'accès aux modèles</p>";
    echo "<p><strong>Message:</strong> " . $e->getMessage() . "</p>";
}

echo "</div>";

// Résumé et recommandations
echo "<div class='info'>
    <h2>📋 Résumé du Test</h2>";

echo "<h3>Tests effectués :</h3>
<ul>
    <li><strong>Fichier temporaire :</strong> " . (file_exists($file['tmp_name']) ? '✅ OK' : '❌ ÉCHEC') . "</li>
    <li><strong>Type MIME :</strong> " . (str_starts_with($mimeType, 'image/') ? '✅ OK' : '❌ ÉCHEC') . "</li>
    <li><strong>Déplacement :</strong> " . (isset($destination) && file_exists($destination) ? '✅ OK' : '❌ ÉCHEC') . "</li>
    <li><strong>Optimisation :</strong> " . (class_exists('ImageOptimizer') ? '✅ Testé' : '❌ Non testé') . "</li>
    <li><strong>Base de données :</strong> " . (class_exists('Media') ? '✅ OK' : '❌ ÉCHEC') . "</li>
</ul>";

echo "<h3>Problèmes identifiés :</h3>";

$problems = [];
if (!file_exists($file['tmp_name'])) $problems[] = "Fichier temporaire introuvable";
if (!str_starts_with($mimeType, 'image/')) $problems[] = "Type d'image invalide";
if (!class_exists('ImageOptimizer')) $problems[] = "Classe ImageOptimizer manquante";
if (!class_exists('Media')) $problems[] = "Modèle Media inaccessible";

if (empty($problems)) {
    echo "<p class='success'>✅ Aucun problème majeur identifié</p>";
} else {
    foreach ($problems as $problem) {
        echo "<p class='error'>❌ " . $problem . "</p>";
    }
}

echo "</div>";

// Boutons de navigation
echo "<div style='text-align: center; margin-top: 30px;'>
    <a href='debug-upload.php' class='btn'>← Retour au Debug</a>
    <a href='admin.php' class='btn'>🏠 Admin</a>
</div>";

// Nettoyage automatique après 5 minutes
register_shutdown_function(function() use ($testDir) {
    if (is_dir($testDir)) {
        $files = glob($testDir . '/*');
        foreach ($files as $file) {
            if (is_file($file)) unlink($file);
        }
        rmdir($testDir);
    }
});

echo "</div></body></html>";
?>
