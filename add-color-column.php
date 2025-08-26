<?php
// Script pour ajouter la colonne color à la table categories
require_once __DIR__ . '/core/Database.php';

echo "<h1>Ajout de la colonne color à la table categories</h1>";

try {
    // Vérifier si la colonne existe déjà
    $checkSql = "SHOW COLUMNS FROM categories LIKE 'color'";
    $result = Database::query($checkSql);
    
    if (empty($result)) {
        // Ajouter la colonne color
        $alterSql = "ALTER TABLE categories ADD COLUMN color VARCHAR(7) DEFAULT '#666666' COMMENT 'Couleur hexadécimale pour les badges (ex: #FF0000)'";
        Database::execute($alterSql);
        echo "<p style='color: green;'>✅ Colonne 'color' ajoutée avec succès</p>";
        
        // Mettre à jour les catégories existantes avec des couleurs par défaut
        $updateQueries = [
            "UPDATE categories SET color = '#dcfce7' WHERE name LIKE '%test%' OR name LIKE '%review%'",
            "UPDATE categories SET color = '#dbeafe' WHERE name LIKE '%news%' OR name LIKE '%actualité%'",
            "UPDATE categories SET color = '#f3e8ff' WHERE name LIKE '%guide%' OR name LIKE '%tutoriel%'",
            "UPDATE categories SET color = '#fef3c7' WHERE name LIKE '%esport%' OR name LIKE '%tournoi%'",
            "UPDATE categories SET color = '#fce7f3' WHERE name LIKE '%matériel%' OR name LIKE '%hardware%'"
        ];
        
        foreach ($updateQueries as $query) {
            Database::execute($query);
        }
        echo "<p style='color: green;'>✅ Couleurs par défaut appliquées</p>";
        
    } else {
        echo "<p style='color: orange;'>⚠️ La colonne 'color' existe déjà</p>";
    }
    
    // Afficher les catégories mises à jour
    $selectSql = "SELECT id, name, color FROM categories";
    $categories = Database::query($selectSql);
    
    echo "<h2>Catégories mises à jour :</h2>";
    echo "<ul>";
    foreach ($categories as $category) {
        echo "<li><strong>{$category['name']}</strong> - Couleur: <span style='color: {$category['color']};'>{$category['color']}</span></li>";
    }
    echo "</ul>";
    
    echo "<p><a href='test-homepage-data.php'>Tester maintenant les données de la page d'accueil</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erreur: " . $e->getMessage() . "</p>";
}
?>
