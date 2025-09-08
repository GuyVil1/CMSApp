<?php
require_once 'core/Database.php';
try {
    // Vérifier les articles de catégorie 10
    $articles = \Database::query('SELECT id, title, slug, category_id FROM articles WHERE category_id = 10');
    echo "📚 ARTICLES DE CATÉGORIE DOSSIERS (ID 10):\n";
    foreach ($articles as $article) {
        echo "ID: " . $article['id'] . " | " . $article['title'] . " (" . $article['slug'] . ")\n";
        
        // Vérifier les chapitres de ce dossier
        $chapters = \Database::query('SELECT id, title, status FROM dossier_chapters WHERE dossier_id = ?', [$article['id']]);
        echo "  Chapitres: " . count($chapters) . "\n";
        foreach ($chapters as $chapter) {
            echo "    - " . $chapter['title'] . " (statut: " . $chapter['status'] . ")\n";
        }
        echo "\n";
    }
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}
?>
