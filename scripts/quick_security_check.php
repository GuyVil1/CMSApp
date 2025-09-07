<?php
/**
 * Vérification rapide de sécurité
 * Contrôles essentiels de sécurité
 */

echo "🔒 VERIFICATION RAPIDE DE SECURITE\n";
echo "==================================\n\n";

$baseDir = dirname(__DIR__);
$issues = [];
$score = 100;

// 1. Vérifier les requêtes préparées
echo "🔍 Verification des requetes SQL...\n";
$files = glob($baseDir . '/app/**/*.php');
$sqlIssues = 0;

foreach ($files as $file) {
    $content = file_get_contents($file);
    
    // Chercher les requêtes non préparées
    if (preg_match('/Database::query\([\'"][^\'"]*\$[^\'"]*[\'"]/', $content)) {
        $sqlIssues++;
    }
}

if ($sqlIssues > 0) {
    $issues[] = "Requetes SQL non preparees detectees: $sqlIssues";
    $score -= 20;
} else {
    echo "   ✅ Requetes preparees OK\n";
}

// 2. Vérifier l'authentification
echo "🔍 Verification de l'authentification...\n";
$authFile = $baseDir . '/core/Auth.php';

if (file_exists($authFile)) {
    $content = file_get_contents($authFile);
    
    if (!preg_match('/password_hash\(/', $content)) {
        $issues[] = "Hachage des mots de passe non securise";
        $score -= 25;
    } else {
        echo "   ✅ Hachage securise OK\n";
    }
} else {
    $issues[] = "Fichier d'authentification manquant";
    $score -= 30;
}

// 3. Vérifier les uploads
echo "🔍 Verification des uploads...\n";
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
        echo "   ✅ Validations upload OK\n";
    }
} else {
    $issues[] = "Contrôleur d'upload manquant";
    $score -= 20;
}

// 4. Vérifier les en-têtes de sécurité
echo "🔍 Vérification des en-têtes de sécurité...\n";
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
        $issues[] = "En-têtes de sécurité manquants: " . implode(', ', $missing);
        $score -= 10;
    } else {
        echo "   ✅ En-têtes de sécurité OK\n";
    }
} else {
    $issues[] = "Fichier d'en-têtes de sécurité manquant";
    $score -= 15;
}

// 5. Vérifier la configuration
echo "🔍 Vérification de la configuration...\n";
$configFile = $baseDir . '/config/config.php';

if (file_exists($configFile)) {
    $content = file_get_contents($configFile);
    
    if (preg_match('/display_errors.*true/', $content)) {
        $issues[] = "Affichage des erreurs activé";
        $score -= 5;
    } else {
        echo "   ✅ Configuration production OK\n";
    }
} else {
    $issues[] = "Fichier de configuration manquant";
    $score -= 10;
}

// Résultat
echo "\n📊 RÉSULTAT:\n";
echo "============\n\n";

echo "🎯 SCORE DE SÉCURITÉ: $score/100\n\n";

if (empty($issues)) {
    echo "🎉 AUCUN PROBLÈME DÉTECTÉ !\n";
    echo "L'application semble sécurisée.\n";
} else {
    echo "⚠️ PROBLÈMES DÉTECTÉS:\n";
    foreach ($issues as $issue) {
        echo "   • $issue\n";
    }
    
    echo "\n💡 RECOMMANDATIONS:\n";
    echo "   • Corriger les problèmes identifiés\n";
    echo "   • Tester les corrections\n";
    echo "   • Mettre en place une surveillance continue\n";
}

// Vérifications avancées
echo "\n🔍 VÉRIFICATIONS AVANCÉES:\n";

// Vérifier les permissions de fichiers sensibles
$sensitiveFiles = [
    'config/config.php',
    'core/Database.php',
    'core/Auth.php'
];

foreach ($sensitiveFiles as $file) {
    if (file_exists($file)) {
        $perms = fileperms($file);
        $octal = substr(sprintf('%o', $perms), -4);
        if ($octal !== '0644' && $octal !== '0600') {
            echo "⚠️  Permissions incorrectes pour $file: $octal (recommandé: 0644)\n";
        } else {
            echo "✅ Permissions correctes pour $file: $octal\n";
        }
    }
}

// Vérifier les headers de sécurité
echo "\n🔒 HEADERS DE SÉCURITÉ:\n";
$securityHeaders = [
    'X-Frame-Options',
    'X-Content-Type-Options',
    'X-XSS-Protection',
    'Strict-Transport-Security',
    'Content-Security-Policy'
];

foreach ($securityHeaders as $header) {
    echo "   • $header: À implémenter\n";
}

// Vérifier les dépendances
echo "\n📦 DÉPENDANCES:\n";
$composerLock = 'composer.lock';
if (file_exists($composerLock)) {
    echo "✅ Composer.lock présent\n";
} else {
    echo "⚠️  Composer.lock manquant - vulnérabilités possibles\n";
}

// Vérifier les logs d'erreur
echo "\n📝 LOGS D'ERREUR:\n";
$logFiles = [
    'logs/security/',
    'app/logs/',
    'error.log'
];

foreach ($logFiles as $log) {
    if (file_exists($log)) {
        echo "✅ Log trouvé: $log\n";
    } else {
        echo "⚠️  Log manquant: $log\n";
    }
}

echo "\n📅 Vérification effectuée le: " . date('Y-m-d H:i:s') . "\n";
echo "🎯 Score de sécurité: " . calculateSecurityScore($issues) . "/100\n";

function calculateSecurityScore($issues) {
    $totalChecks = 20; // Nombre total de vérifications
    $issuesCount = count($issues);
    $score = max(0, 100 - ($issuesCount * 5));
    return $score;
}
