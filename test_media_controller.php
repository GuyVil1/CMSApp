<?php
/**
 * Test complet du MediaController
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

echo "<h1>🔍 TEST COMPLET MediaController</h1>";

// Vérifier l'authentification
if (!Auth::isLoggedIn()) {
    echo "❌ Vous devez être connecté<br>";
    echo "<a href='/login'>Se connecter</a><br>";
    exit;
}

echo "✅ Utilisateur connecté: " . Auth::getUser()['login'] . "<br>";

// Si c'est un POST, traiter l'upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    echo "<h2>📤 SIMULATION MediaController</h2>";
    
    $file = $_FILES['file'];
    echo "✅ Fichier reçu: " . $file['name'] . "<br>";
    
    // Simuler le flux du MediaController
    try {
        echo "<h3>1️⃣ Validation de base</h3>";
        
        // Vérifier que l'extension GD est disponible
        if (!extension_loaded('gd')) {
            throw new \Exception('Extension GD non disponible sur le serveur', 500);
        }
        echo "✅ Extension GD OK<br>";
        
        // Vérifier que des fichiers ont été uploadés
        if (!isset($_FILES['file'])) {
            throw new \Exception('Aucun fichier reçu', 400);
        }
        echo "✅ Fichier reçu<br>";
        
        echo "<h3>2️⃣ Validation du fichier</h3>";
        
        // Vérifier les erreurs d'upload
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new \Exception('Erreur d\'upload: ' . $file['error'], 400);
        }
        echo "✅ Pas d'erreur d'upload<br>";
        
        // Vérifier que le fichier temporaire existe
        if (!file_exists($file['tmp_name'])) {
            throw new \Exception('Fichier temporaire introuvable', 400);
        }
        echo "✅ Fichier temporaire existe<br>";
        
        // Vérifier le type MIME avec validation renforcée
        $mimeType = SecurityHelper::getRealMimeType($file['tmp_name']);
        if (!SecurityHelper::validateImageMimeType($mimeType)) {
            throw new \Exception('Type de fichier non autorisé. Formats acceptés : JPG, PNG, WebP, GIF', 400);
        }
        echo "✅ Type MIME valide: " . $mimeType . "<br>";
        
        // Vérifier la taille avec validation renforcée
        $maxFileSize = 4194304; // 4MB
        if (!SecurityHelper::validateFileSize($file['size'], $maxFileSize)) {
            throw new \Exception('Fichier trop volumineux (max 4MB)', 400);
        }
        echo "✅ Taille valide: " . number_format($file['size']) . " bytes<br>";
        
        // VALIDATION RENFORCÉE : Vérifier le contenu réel de l'image
        $imageValidation = SecurityHelper::validateImageContent($file['tmp_name']);
        if (!$imageValidation['valid']) {
            throw new \Exception($imageValidation['message'], 400);
        }
        echo "✅ Contenu image valide<br>";
        
        echo "<h3>3️⃣ Détermination du dossier</h3>";
        
        // Récupérer les paramètres
        $gameId = !empty($_POST['game_id']) ? (int)$_POST['game_id'] : null;
        $category = $_POST['category'] ?? 'general';
        
        echo "✅ Game ID: " . ($gameId ?? 'null') . "<br>";
        echo "✅ Category: " . $category . "<br>";
        
        // Déterminer le dossier d'upload
        $uploadDir = 'public/uploads/';
        if ($gameId) {
            // Récupérer le nom du jeu
            require_once __DIR__ . '/app/models/Game.php';
            $game = \Game::find($gameId);
            if ($game) {
                $gameName = preg_replace('/[^a-zA-Z0-9_-]/', '', $game['name']);
                $uploadDir .= 'games/' . $gameName . '/';
            } else {
                $uploadDir .= 'games/unknown/';
            }
        } else {
            $uploadDir .= 'article/';
        }
        
        echo "✅ Dossier d'upload: " . $uploadDir . "<br>";
        
        // Créer le dossier si nécessaire
        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0755, true)) {
                throw new \Exception('Impossible de créer le dossier d\'upload', 500);
            }
        }
        echo "✅ Dossier créé/vérifié<br>";
        
        echo "<h3>4️⃣ Génération du nom de fichier</h3>";
        
        // Générer un nom de fichier unique et sécurisé
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $filename = time() . '_' . uniqid() . '.' . $extension;
        $filepath = $uploadDir . $filename;
        
        echo "✅ Nom de fichier généré: " . $filename . "<br>";
        echo "✅ Chemin complet: " . $filepath . "<br>";
        
        echo "<h3>5️⃣ Déplacement du fichier</h3>";
        
        // Déplacer le fichier
        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            throw new \Exception('Impossible de déplacer le fichier uploadé', 500);
        }
        echo "✅ Fichier déplacé avec succès<br>";
        
        echo "<h3>6️⃣ Vérification finale</h3>";
        
        if (file_exists($filepath)) {
            echo "✅ Fichier existe: " . $filepath . "<br>";
            echo "✅ Taille finale: " . number_format(filesize($filepath)) . " bytes<br>";
            
            // Afficher l'image
            echo "<h3>🖼️ Image uploadée:</h3>";
            echo "<img src='/$filepath' style='max-width: 300px; border: 1px solid #ccc;'><br>";
            
            // Nettoyer
            unlink($filepath);
            echo "✅ Fichier de test supprimé<br>";
            
        } else {
            echo "❌ Fichier introuvable après déplacement<br>";
        }
        
        echo "<h3>✅ SIMULATION RÉUSSIE !</h3>";
        echo "Toutes les étapes du MediaController ont fonctionné !<br>";
        
    } catch (Exception $e) {
        echo "<h3>❌ ERREUR DANS LA SIMULATION</h3>";
        echo "❌ Message: " . $e->getMessage() . "<br>";
        echo "❌ Code: " . $e->getCode() . "<br>";
        echo "❌ Trace: " . $e->getTraceAsString() . "<br>";
    }
    
    echo "<hr>";
}

// Formulaire de test
echo "<h2>📤 FORMULAIRE DE TEST</h2>";
$csrfToken = Auth::generateCsrfToken();
echo '<form action="/test_media_controller.php" method="POST" enctype="multipart/form-data">';
echo '<input type="hidden" name="csrf_token" value="' . $csrfToken . '">';
echo '<input type="file" name="file" accept="image/*" required><br><br>';
echo '<input type="submit" value="Tester MediaController">';
echo '</form>';

echo "<hr>";
echo "<p><a href='/test_gd_extension.php'>← Retour au test GD</a></p>";
?>
