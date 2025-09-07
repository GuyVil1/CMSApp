<?php
/**
 * Test final de l'API /media/upload
 */

echo "<h1>🔍 TEST FINAL API /media/upload</h1>";

// Démarrer la session
session_start();

// Charger la configuration
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/core/Auth.php';

// Initialiser l'authentification
Auth::init();

// Vérifier l'authentification
if (!Auth::isLoggedIn()) {
    echo "❌ Vous devez être connecté<br>";
    echo "<a href='/login'>Se connecter</a><br>";
    exit;
}

echo "✅ Utilisateur connecté: " . Auth::getUser()['login'] . "<br>";

// Si c'est un POST, traiter l'upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    echo "<h2>📤 TEST DE L'API /media/upload</h2>";
    
    // Préparer les données
    $file = $_FILES['file'];
    $csrfToken = $_POST['csrf_token'] ?? '';
    
    echo "✅ Fichier: " . $file['name'] . "<br>";
    echo "✅ Taille: " . number_format($file['size']) . " bytes<br>";
    echo "✅ Token CSRF: " . substr($csrfToken, 0, 20) . "...<br>";
    
    // Créer un formulaire pour l'API
    echo "<h3>🔄 Appel de l'API via JavaScript</h3>";
    
    echo '<script>
    async function testApiUpload() {
        const fileInput = document.querySelector("input[type=file]");
        if (!fileInput.files[0]) {
            document.getElementById("apiResult").innerHTML = `
                <h3>❌ Aucun fichier sélectionné</h3>
                <p>Veuillez sélectionner un fichier avant de tester</p>
            `;
            return;
        }
        
        const formData = new FormData();
        formData.append("file", fileInput.files[0]);
        formData.append("csrf_token", "' . $csrfToken . '");
        
        console.log("📁 Fichier sélectionné:", fileInput.files[0].name);
        console.log("📁 Taille:", fileInput.files[0].size);
        
        try {
            console.log("🚀 Début de l\'appel API...");
            
            const response = await fetch("/api_media_upload.php", {
                method: "POST",
                body: formData
            });
            
            console.log("📊 Réponse reçue:", response);
            console.log("📊 Status:", response.status);
            console.log("📊 Headers:", response.headers);
            
            const responseText = await response.text();
            console.log("📊 Contenu brut:", responseText);
            
            // Afficher les résultats
            document.getElementById("apiResult").innerHTML = `
                <h3>📊 Résultat de l\'API</h3>
                <p><strong>Status:</strong> ${response.status}</p>
                <p><strong>Status Text:</strong> ${response.statusText}</p>
                <p><strong>Content-Type:</strong> ${response.headers.get("content-type") || "Non défini"}</p>
                <p><strong>Contenu:</strong></p>
                <pre>${responseText}</pre>
            `;
            
            // Essayer de parser le JSON
            try {
                const jsonData = JSON.parse(responseText);
                console.log("📊 JSON parsé:", jsonData);
                
                if (jsonData.success) {
                    document.getElementById("apiResult").innerHTML += `
                        <h3>✅ Upload réussi !</h3>
                        <p><strong>Fichier:</strong> ${jsonData.media.filename}</p>
                        <p><strong>URL:</strong> <a href="${jsonData.media.url}" target="_blank">${jsonData.media.url}</a></p>
                        <p><strong>Taille:</strong> ${jsonData.media.size}</p>
                        ${jsonData.media.thumbnail_url ? `<p><strong>Thumbnail:</strong> <a href="${jsonData.media.thumbnail_url}" target="_blank">${jsonData.media.thumbnail_url}</a></p>` : ""}
                    `;
                } else {
                    document.getElementById("apiResult").innerHTML += `
                        <h3>❌ Upload échoué</h3>
                        <p><strong>Message:</strong> ${jsonData.message || "Erreur inconnue"}</p>
                    `;
                }
            } catch (e) {
                console.log("❌ Erreur parsing JSON:", e);
                document.getElementById("apiResult").innerHTML += `
                    <h3>⚠️ Réponse non-JSON</h3>
                    <p>La réponse n\'est pas du JSON valide</p>
                `;
            }
            
        } catch (error) {
            console.error("❌ Erreur API:", error);
            document.getElementById("apiResult").innerHTML = `
                <h3>❌ Erreur de l\'API</h3>
                <p><strong>Message:</strong> ${error.message}</p>
            `;
        }
    }
    
    // Ne pas lancer le test automatiquement
    // L'utilisateur doit cliquer sur le bouton
    </script>';
    
    echo '<div id="apiResult">Cliquez sur le bouton pour tester l\'API</div>';
    echo '<button onclick="testApiUpload()" style="padding: 10px 20px; background: #007cba; color: white; border: none; border-radius: 5px; cursor: pointer;">Tester l\'API</button>';
    
    echo "<hr>";
}

// Formulaire de test
echo "<h2>📤 FORMULAIRE DE TEST</h2>";
$csrfToken = Auth::generateCsrfToken();
echo '<form action="/test_api_upload.php" method="POST" enctype="multipart/form-data">';
echo '<input type="hidden" name="csrf_token" value="' . $csrfToken . '">';
echo '<input type="file" name="file" accept="image/*" required><br><br>';
echo '<input type="submit" value="Tester l\'API /media/upload">';
echo '</form>';

echo "<hr>";
echo "<p><a href='/test_direct_upload.php'>← Retour au test direct</a></p>";
?>
