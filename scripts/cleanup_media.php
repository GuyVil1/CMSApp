<?php
declare(strict_types=1);

/**
 * Script de nettoyage de la médiathèque
 * Supprime les références aux fichiers qui n'existent plus
 */

echo "🧹 NETTOYAGE DE LA MÉDIATHÈQUE\n";
echo "==============================\n\n";

// Charger la configuration
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../core/Database.php';

try {
    $db = Database::getInstance();
    
    // Récupérer tous les médias
    $sql = "SELECT id, filename, original_name FROM media ORDER BY id";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $medias = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "📊 Analyse de " . count($medias) . " médias...\n\n";
    
    $deletedCount = 0;
    $keptCount = 0;
    
    foreach ($medias as $media) {
        $filePath = $media['filename'];
        $fullPath = __DIR__ . '/../public/uploads/' . $filePath;
        
        echo "Vérification: {$media['original_name']}... ";
        
        if (!file_exists($fullPath)) {
            // Le fichier n'existe plus, supprimer la référence
            $deleteSql = "DELETE FROM media WHERE id = ?";
            $deleteStmt = $db->prepare($deleteSql);
            $deleteStmt->execute([$media['id']]);
            
            echo "❌ SUPPRIMÉ (fichier manquant)\n";
            $deletedCount++;
        } else {
            echo "✅ OK\n";
            $keptCount++;
        }
    }
    
    echo "\n📊 RÉSULTATS:\n";
    echo "=============\n";
    echo "✅ Médias conservés: {$keptCount}\n";
    echo "❌ Médias supprimés: {$deletedCount}\n";
    echo "📈 Total traité: " . ($keptCount + $deletedCount) . "\n\n";
    
    if ($deletedCount > 0) {
        echo "🎉 Nettoyage terminé avec succès !\n";
        echo "Les références aux fichiers manquants ont été supprimées.\n\n";
    } else {
        echo "✨ Aucun nettoyage nécessaire !\n";
        echo "Tous les fichiers existent.\n\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erreur lors du nettoyage: " . $e->getMessage() . "\n";
    exit(1);
}

echo "🔧 VÉRIFICATION DES DOSSIERS UPLOAD...\n";
echo "=====================================\n";

// Vérifier les dossiers upload
$uploadDirs = [
    'public/uploads/article',
    'public/uploads/games',
    'public/uploads/media'
];

foreach ($uploadDirs as $dir) {
    $fullDir = __DIR__ . '/../' . $dir;
    if (!is_dir($fullDir)) {
        echo "📁 Création du dossier: {$dir}\n";
        mkdir($fullDir, 0755, true);
    } else {
        echo "✅ Dossier existe: {$dir}\n";
    }
}

echo "\n✅ Vérification des dossiers terminée !\n";
