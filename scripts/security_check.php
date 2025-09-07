<?php
/**
 * Verification rapide de securite
 */

echo "SECURITE CHECK - Belgium Video Gaming\n";
echo "====================================\n\n";

$baseDir = dirname(__DIR__);
$issues = [];
$score = 100;

// 1. Verifier les requetes SQL
echo "1. Verification des requetes SQL...\n";
$files = glob($baseDir . '/app/**/*.php');
$sqlIssues = 0;

foreach ($files as $file) {
    $content = file_get_contents($file);
    
    if (preg_match('/Database::query\([\'"][^\'"]*\$[^\'"]*[\'"]/', $content)) {
        $sqlIssues++;
    }
}

if ($sqlIssues > 0) {
    $issues[] = "Requetes SQL non preparees: $sqlIssues";
    $score -= 20;
} else {
    echo "   OK - Requetes preparees\n";
}

// 2. Verifier l'authentification
echo "2. Verification de l'authentification...\n";
$authFile = $baseDir . '/core/Auth.php';

if (file_exists($authFile)) {
    $content = file_get_contents($authFile);
    
    if (!preg_match('/password_hash\(/', $content)) {
        $issues[] = "Hachage des mots de passe non securise";
        $score -= 25;
    } else {
        echo "   OK - Hachage securise\n";
    }
} else {
    $issues[] = "Fichier d'authentification manquant";
    $score -= 30;
}

// 3. Verifier les uploads
echo "3. Verification des uploads...\n";
$uploadFile = $baseDir . '/app/controllers/admin/UploadController.php';

if (file_exists($uploadFile)) {
    $content = file_get_contents($uploadFile);
    
    $validations = [
        'finfo_file' => 'Validation MIME',
        'pathinfo.*PATHINFO_EXTENSION' => 'Validation extension',
        'getimagesize' => 'Validation image'
    ];
    
    $missing = [];
    foreach ($validations as $pattern => $name) {
        if (!preg_match('/' . $pattern . '/', $content)) {
            $missing[] = $name;
        }
    }
    
    if (!empty($missing)) {
        $issues[] = "Validations d'upload manquantes: " . implode(', ', $missing);
        $score -= 15;
    } else {
        echo "   OK - Validations upload\n";
    }
} else {
    $issues[] = "Controleur d'upload manquant";
    $score -= 20;
}

// 4. Verifier les en-tetes de securite
echo "4. Verification des en-tetes de securite...\n";
$securityFile = $baseDir . '/public/security-headers.php';

if (file_exists($securityFile)) {
    $content = file_get_contents($securityFile);
    
    $requiredHeaders = ['X-Content-Type-Options', 'X-Frame-Options', 'X-XSS-Protection'];
    $missing = [];
    
    foreach ($requiredHeaders as $header) {
        if (!preg_match('/' . preg_quote($header, '/') . '/', $content)) {
            $missing[] = $header;
        }
    }
    
    if (!empty($missing)) {
        $issues[] = "En-tetes de securite manquants: " . implode(', ', $missing);
        $score -= 10;
    } else {
        echo "   OK - En-tetes de securite\n";
    }
} else {
    $issues[] = "Fichier d'en-tetes de securite manquant";
    $score -= 15;
}

// 5. Verifier la configuration
echo "5. Verification de la configuration...\n";
$configFile = $baseDir . '/config/config.php';

if (file_exists($configFile)) {
    $content = file_get_contents($configFile);
    
    if (preg_match('/display_errors.*true/', $content)) {
        $issues[] = "Affichage des erreurs active";
        $score -= 5;
    } else {
        echo "   OK - Configuration production\n";
    }
} else {
    $issues[] = "Fichier de configuration manquant";
    $score -= 10;
}

// Resultat
echo "\nRESULTAT:\n";
echo "=========\n\n";

echo "SCORE DE SECURITE: $score/100\n\n";

if (empty($issues)) {
    echo "AUCUN PROBLEME DETECTE !\n";
    echo "L'application semble securisee.\n";
} else {
    echo "PROBLEMES DETECTES:\n";
    foreach ($issues as $issue) {
        echo "   - $issue\n";
    }
    
    echo "\nRECOMMANDATIONS:\n";
    echo "   - Corriger les problemes identifies\n";
    echo "   - Tester les corrections\n";
    echo "   - Mettre en place une surveillance continue\n";
}

echo "\nVerification effectuee le: " . date('Y-m-d H:i:s') . "\n";
