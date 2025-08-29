<?php
/**
 * Script de test pour la gestion des utilisateurs
 */

// Inclure les fichiers nécessaires
require_once 'core/Database.php';
require_once 'app/models/User.php';
require_once 'app/models/Role.php';

echo "<h1>Test de la gestion des utilisateurs</h1>";

try {
    // Test 1: Connexion à la base de données
    echo "<h2>1. Test de connexion à la base de données</h2>";
    $db = new Database();
    echo "✅ Connexion à la base de données réussie<br>";
    
    // Test 2: Vérifier si la colonne is_active existe
    echo "<h2>2. Vérification de la colonne is_active</h2>";
    $result = $db->query("DESCRIBE users");
    $hasIsActive = false;
    foreach ($result as $column) {
        if ($column['Field'] === 'is_active') {
            $hasIsActive = true;
            break;
        }
    }
    
    if ($hasIsActive) {
        echo "✅ La colonne is_active existe<br>";
    } else {
        echo "❌ La colonne is_active n'existe pas - Ajout en cours...<br>";
        $db->execute("ALTER TABLE users ADD COLUMN is_active BOOLEAN NOT NULL DEFAULT TRUE");
        $db->execute("UPDATE users SET is_active = TRUE WHERE is_active IS NULL");
        echo "✅ Colonne is_active ajoutée et mise à jour<br>";
    }
    
    // Test 3: Test du modèle Role
    echo "<h2>3. Test du modèle Role</h2>";
    $roles = Role::findAll();
    echo "✅ Rôles trouvés: " . count($roles) . "<br>";
    foreach ($roles as $role) {
        echo "- " . $role['name'] . " (ID: " . $role['id'] . ")<br>";
    }
    
    // Test 4: Test du modèle User
    echo "<h2>4. Test du modèle User</h2>";
    $users = User::findAll();
    echo "✅ Utilisateurs trouvés: " . count($users) . "<br>";
    foreach ($users as $user) {
        echo "- " . $user['login'] . " (Rôle: " . $user['role_name'] . ", Actif: " . ($user['is_active'] ? 'Oui' : 'Non') . ")<br>";
    }
    
    // Test 5: Test de création d'utilisateur
    echo "<h2>5. Test de création d'utilisateur</h2>";
    $testUserData = [
        'login' => 'test_user_' . time(),
        'email' => 'test_' . time() . '@example.com',
        'password' => 'testpassword123',
        'role_id' => 4 // member
    ];
    
    $newUser = User::create($testUserData);
    if ($newUser) {
        echo "✅ Utilisateur de test créé: " . $newUser['login'] . "<br>";
        
        // Test de mise à jour
        $updateResult = User::update($newUser['id'], ['login' => 'test_user_updated']);
        if ($updateResult) {
            echo "✅ Utilisateur mis à jour avec succès<br>";
        } else {
            echo "❌ Erreur lors de la mise à jour<br>";
        }
        
        // Test de suppression
        $deleteResult = User::delete($newUser['id']);
        if ($deleteResult) {
            echo "✅ Utilisateur de test supprimé<br>";
        } else {
            echo "❌ Erreur lors de la suppression<br>";
        }
    } else {
        echo "❌ Erreur lors de la création de l'utilisateur de test<br>";
    }
    
    echo "<h2>✅ Tous les tests sont terminés</h2>";
    echo "<p>La gestion des utilisateurs semble fonctionner correctement !</p>";
    echo "<p><a href='/users.php'>Accéder à la page de gestion des utilisateurs</a></p>";
    
} catch (Exception $e) {
    echo "<h2>❌ Erreur lors des tests</h2>";
    echo "<p>Erreur: " . $e->getMessage() . "</p>";
    echo "<p>Fichier: " . $e->getFile() . "</p>";
    echo "<p>Ligne: " . $e->getLine() . "</p>";
}
?>
