<?php
/**
 * Test détaillé d'upload avec logs complets
 */

// Démarrer la session
session_start();

// Charger la configuration
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/core/Auth.php';
require_once __DIR__ . '/app/helpers/security_logger_simple.php';

// Initialiser l'authentification
Auth::init();

echo "<h1>🔍 TEST DÉTAILLÉ D'UPLOAD</h1>";

// Vérifier l'authentification
if (!Auth::isLoggedIn()) {
    echo "❌ Vous devez être connecté pour tester l'upload<br>";
    echo "<a href='/login'>Se connecter</a><br>";
    exit;
}

echo "✅ Utilisateur connecté: " . Auth::getUser()['login'] . "<br>";

// Si c'est un POST, traiter l'upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    echo "<h2>📤 TRAITEMENT DE L'UPLOAD</h2>";
    
    // Logs détaillés
    error_log("=== TEST UPLOAD DÉTAILLÉ ===");
    error_log("POST data: " . print_r($_POST, true));
    error_log("FILES data: " . print_r($_FILES, true));
    error_log("Session data: " . print_r($_SESSION, true));
    
    // Vérifier le token CSRF
    $csrfToken = $_POST['csrf_token'] ?? '';
    if (!Auth::verifyCsrfToken($csrfToken)) {
        echo "❌ Token CSRF invalide<br>";
        error_log("❌ Token CSRF invalide: " . $csrfToken);
        exit;
    }
    echo "✅ Token CSRF valide<br>";
    
    // Vérifier le fichier
    $file = $_FILES['file'];
    if ($file['error'] !== UPLOAD_ERR_OK) {
        echo "❌ Erreur d'upload: " . $file['error'] . "<br>";
        error_log("❌ Erreur d'upload: " . $file['error']);
        exit;
    }
    echo "✅ Fichier reçu sans erreur<br>";
    
    // Vérifier le type MIME
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    echo "✅ Type MIME détecté: " . $mimeType . "<br>";
    echo "✅ Type MIME déclaré: " . $file['type'] . "<br>";
    echo "✅ Taille: " . number_format($file['size']) . " bytes<br>";
    echo "✅ Nom: " . $file['name'] . "<br>";
    
    // Vérifier les types autorisés
    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    if (!in_array($mimeType, $allowedTypes)) {
        echo "❌ Type MIME non autorisé: " . $mimeType . "<br>";
        error_log("❌ Type MIME non autorisé: " . $mimeType);
        exit;
    }
    echo "✅ Type MIME autorisé<br>";
    
    // Test d'upload direct
    $uploadDir = 'public/uploads/test/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    $filename = time() . '_' . $file['name'];
    $uploadPath = $uploadDir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
        echo "✅ Upload direct réussi: " . $uploadPath . "<br>";
        echo "✅ Fichier accessible: " . (file_exists($uploadPath) ? 'Oui' : 'Non') . "<br>";
        echo "✅ Taille finale: " . number_format(filesize($uploadPath)) . " bytes<br>";
        
        // Afficher l'image
        echo "<h3>🖼️ Image uploadée:</h3>";
        echo "<img src='/$uploadPath' style='max-width: 300px; border: 1px solid #ccc;'><br>";
        
        // Nettoyer
        unlink($uploadPath);
        echo "✅ Fichier de test supprimé<br>";
        
    } else {
        echo "❌ Échec de l'upload direct<br>";
        error_log("❌ Échec de l'upload direct vers: " . $uploadPath);
    }
    
    echo "<hr>";
}

// Formulaire de test
echo "<h2>📤 FORMULAIRE DE TEST</h2>";
$csrfToken = Auth::generateCsrfToken();
echo '<form action="/test_upload_detailed.php" method="POST" enctype="multipart/form-data">';
echo '<input type="hidden" name="csrf_token" value="' . $csrfToken . '">';
echo '<input type="file" name="file" accept="image/*" required><br><br>';
echo '<input type="submit" value="Tester l\'upload direct">';
echo '</form>';

echo "<hr>";
echo "<h2>🔗 TEST VIA L'API</h2>";
echo '<form id="apiForm" enctype="multipart/form-data">';
echo '<input type="hidden" name="csrf_token" value="' . $csrfToken . '">';
echo '<input type="file" name="file" accept="image/*" required><br><br>';
echo '<input type="submit" value="Tester via API /media/upload">';
echo '</form>';

echo '<div id="apiResult"></div>';

// JavaScript pour tester l'API
echo '<script>
document.getElementById("apiForm").addEventListener("submit", async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const resultDiv = document.getElementById("apiResult");
    
    resultDiv.innerHTML = "⏳ Test en cours...";
    
    try {
        const response = await fetch("/media/upload", {
            method: "POST",
            body: formData
        });
        
        const result = await response.text();
        
        resultDiv.innerHTML = `
            <h3>📊 Résultat API:</h3>
            <p><strong>Status:</strong> ${response.status}</p>
            <p><strong>Response:</strong></p>
            <pre>${result}</pre>
        `;
        
    } catch (error) {
        resultDiv.innerHTML = `
            <h3>❌ Erreur API:</h3>
            <p>${error.message}</p>
        `;
    }
});
</script>';

echo "<hr>";
echo "<p><a href='/test_upload_debug.php'>← Retour au diagnostic</a></p>";
?>
