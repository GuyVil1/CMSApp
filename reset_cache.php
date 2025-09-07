<?php
require_once 'app/helpers/rate_limit_helper.php';

echo "=== Reset du cache de rate limiting ===\n";

try {
    // Chercher le dossier cache dans différents emplacements
    $possiblePaths = [
        __DIR__ . '/cache/rate_limit',
        __DIR__ . '/../cache/rate_limit', 
        __DIR__ . '/../../cache/rate_limit',
        dirname(__DIR__, 2) . '/cache/rate_limit',
        dirname(__DIR__, 3) . '/cache/rate_limit'
    ];
    
    $cacheDir = null;
    foreach ($possiblePaths as $path) {
        if (is_dir($path)) {
            $cacheDir = $path;
            echo "✅ Dossier cache trouvé: $path\n";
            break;
        }
    }
    
    if (!$cacheDir) {
        echo "❌ Aucun dossier cache trouvé\n";
        echo "Création du dossier cache...\n";
        $cacheDir = __DIR__ . '/cache/rate_limit';
        if (!mkdir($cacheDir, 0755, true)) {
            echo "❌ Impossible de créer le dossier cache\n";
            exit;
        }
        echo "✅ Dossier cache créé: $cacheDir\n";
    }
    
    // Lister tous les fichiers
    $files = glob($cacheDir . '/*');
    echo "\nFichiers trouvés:\n";
    foreach ($files as $file) {
        echo "- " . basename($file) . "\n";
    }
    
    // Supprimer tous les fichiers
    $cleared = 0;
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
            echo "✅ Supprimé: " . basename($file) . "\n";
            $cleared++;
        }
    }
    
    echo "\n✅ $cleared fichiers supprimés\n";
    echo "✅ Tu peux maintenant te reconnecter !\n";
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}