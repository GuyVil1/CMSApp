<?php
/**
 * Diagnostic de l'erreur JavaScript
 */

require_once __DIR__ . '/core/Database.php';
require_once __DIR__ . '/app/models/Article.php';

echo "<h1>🔍 Diagnostic de l'erreur JavaScript</h1>";

try {
    // Récupérer l'article 37
    $article = \Article::findById(37);
    
    if ($article) {
        echo "<h2>Article : " . htmlspecialchars($article->getTitle()) . "</h2>";
        
        // Afficher le contenu HTML avec numérotation des lignes
        $content = $article->getContent();
        $lines = explode("\n", $content);
        
        echo "<h3>Contenu HTML avec numérotation (lignes 30-45) :</h3>";
        echo "<div style='background: #f5f5f5; padding: 1rem; border-radius: 4px; font-family: monospace; font-size: 12px;'>";
        
        for ($i = 29; $i <= 44; $i++) {
            if (isset($lines[$i])) {
                $lineNumber = $i + 1;
                $lineContent = $lines[$i];
                $highlighted = ($lineNumber === 37) ? 'background: #ffeb3b;' : '';
                
                echo "<div style='{$highlighted} padding: 2px 0;'>";
                echo "<span style='color: #666; min-width: 30px; display: inline-block;'>{$lineNumber}:</span> ";
                echo htmlspecialchars($lineContent);
                echo "</div>";
            }
        }
        echo "</div>";
        
        // Analyser la ligne 37 spécifiquement
        if (isset($lines[36])) { // Index 36 = ligne 37
            $line37 = $lines[36];
            echo "<h3>Analyse de la ligne 37 :</h3>";
            echo "<p><strong>Contenu brut :</strong></p>";
            echo "<pre style='background: #fff3cd; padding: 1rem; border: 1px solid #ffeaa7;'>" . htmlspecialchars($line37) . "</pre>";
            
            // Analyser les caractères autour de la position 540
            echo "<h4>Caractères autour de la position 540 :</h4>";
            $length = strlen($line37);
            $start = max(0, 535);
            $end = min($length, 545);
            
            echo "<p><strong>Longueur de la ligne :</strong> {$length} caractères</p>";
            echo "<p><strong>Position 535-545 :</strong></p>";
            echo "<pre style='background: #e3f2fd; padding: 1rem; border: 1px solid #90caf9;'>";
            echo "Position: " . str_pad($start, 3, ' ', STR_PAD_LEFT) . " ";
            for ($i = $start; $i < $end; $i++) {
                echo str_pad($i, 3, ' ', STR_PAD_LEFT) . " ";
            }
            echo "\n";
            echo "Char:    ";
            for ($i = $start; $i < $end; $i++) {
                if (isset($line37[$i])) {
                    $char = $line37[$i];
                    $ord = ord($char);
                    echo str_pad($char, 3, ' ', STR_PAD_LEFT) . " ";
                } else {
                    echo "   ";
                }
            }
            echo "\n";
            echo "ASCII:   ";
            for ($i = $start; $i < $end; $i++) {
                if (isset($line37[$i])) {
                    $char = $line37[$i];
                    $ord = ord($char);
                    echo str_pad($ord, 3, ' ', STR_PAD_LEFT) . " ";
                } else {
                    echo "   ";
                }
            }
            echo "</pre>";
            
            // Rechercher les caractères problématiques
            echo "<h4>Caractères potentiellement problématiques :</h4>";
            $problematicChars = [];
            for ($i = 0; $i < $length; $i++) {
                $char = $line37[$i];
                $ord = ord($char);
                
                // Caractères qui peuvent causer des problèmes JavaScript
                if ($ord < 32 && $ord !== 9 && $ord !== 10 && $ord !== 13) { // Caractères de contrôle
                    $problematicChars[] = [
                        'position' => $i,
                        'char' => $char,
                        'ascii' => $ord,
                        'hex' => dechex($ord)
                    ];
                }
            }
            
            if (!empty($problematicChars)) {
                echo "<ul>";
                foreach ($problematicChars as $char) {
                    echo "<li><strong>Position {$char['position']}:</strong> ";
                    echo "Char: '{$char['char']}' (ASCII: {$char['ascii']}, Hex: 0x{$char['hex']})";
                    echo "</li>";
                }
                echo "</ul>";
            } else {
                echo "<p>✅ Aucun caractère de contrôle problématique détecté</p>";
            }
        }
        
    } else {
        echo "<p>❌ Article non trouvé</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erreur : " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
