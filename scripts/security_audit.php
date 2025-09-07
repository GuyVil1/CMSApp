<?php
/**
 * Script d'audit de sécurité complet
 * Analyse de toutes les vulnérabilités potentielles
 */

class SecurityAudit
{
    private string $baseDir;
    private array $vulnerabilities = [];
    private array $recommendations = [];
    private int $score = 100;
    
    public function __construct()
    {
        $this->baseDir = dirname(__DIR__);
    }
    
    /**
     * Exécuter l'audit complet
     */
    public function runAudit(): void
    {
        echo "🔒 AUDIT DE SÉCURITÉ - Belgium Video Gaming\n";
        echo "==========================================\n\n";
        
        $this->checkInputValidation();
        $this->checkSQLInjection();
        $this->checkXSS();
        $this->checkCSRF();
        $this->checkFileUploads();
        $this->checkAuthentication();
        $this->checkAuthorization();
        $this->checkSessionSecurity();
        $this->checkHeaders();
        $this->checkErrorHandling();
        $this->checkDependencies();
        $this->checkFilePermissions();
        
        $this->generateReport();
    }
    
    /**
     * Vérifier la validation des entrées
     */
    private function checkInputValidation(): void
    {
        echo "🔍 Vérification de la validation des entrées...\n";
        
        $issues = [];
        
        // Vérifier les contrôleurs pour la validation
        $controllers = glob($this->baseDir . '/app/controllers/**/*.php');
        
        foreach ($controllers as $controller) {
            $content = file_get_contents($controller);
            
            // Chercher les accès directs à $_POST sans validation
            if (preg_match('/\$_POST\[[\'"]([^\'"]+)[\'"]\]/', $content, $matches)) {
                if (!preg_match('/trim\(|\$_POST\[[\'"]([^\'"]+)[\'"]\]\s*??\s*[\'"]/', $content)) {
                    $issues[] = "Accès direct à \$_POST sans validation dans " . basename($controller);
                }
            }
            
            // Chercher les accès directs à $_GET sans validation
            if (preg_match('/\$_GET\[[\'"]([^\'"]+)[\'"]\]/', $content, $matches)) {
                if (!preg_match('/intval\(|\$_GET\[[\'"]([^\'"]+)[\'"]\]\s*??\s*[\'"]/', $content)) {
                    $issues[] = "Accès direct à \$_GET sans validation dans " . basename($controller);
                }
            }
        }
        
        if (!empty($issues)) {
            $this->addVulnerability('Input Validation', $issues, 'HIGH');
        } else {
            echo "   ✅ Validation des entrées correcte\n";
        }
    }
    
    /**
     * Vérifier les injections SQL
     */
    private function checkSQLInjection(): void
    {
        echo "🔍 Vérification des injections SQL...\n";
        
        $issues = [];
        
        // Vérifier que toutes les requêtes utilisent des requêtes préparées
        $files = glob($this->baseDir . '/app/**/*.php');
        
        foreach ($files as $file) {
            $content = file_get_contents($file);
            
            // Chercher les requêtes SQL directes
            if (preg_match('/Database::query\([\'"]([^\'"]*\$[^\'"]*)[\'"]/', $content, $matches)) {
                $issues[] = "Requête SQL potentiellement vulnérable dans " . basename($file) . ": " . $matches[1];
            }
            
            // Chercher les concaténations de chaînes dans les requêtes
            if (preg_match('/Database::query\([\'"][^\'"]*\.\s*\$[^\'"]*[\'"]/', $content, $matches)) {
                $issues[] = "Concaténation de chaînes dans requête SQL dans " . basename($file);
            }
        }
        
        if (!empty($issues)) {
            $this->addVulnerability('SQL Injection', $issues, 'CRITICAL');
        } else {
            echo "   ✅ Requêtes préparées utilisées correctement\n";
        }
    }
    
    /**
     * Vérifier les vulnérabilités XSS
     */
    private function checkXSS(): void
    {
        echo "🔍 Vérification des vulnérabilités XSS...\n";
        
        $issues = [];
        
        // Vérifier les vues pour l'échappement
        $views = glob($this->baseDir . '/app/views/**/*.php');
        
        foreach ($views as $view) {
            $content = file_get_contents($view);
            
            // Chercher les sorties directes sans échappement
            if (preg_match('/<\?=\s*\$[a-zA-Z_][a-zA-Z0-9_]*\s*\?>/', $content, $matches)) {
                $issues[] = "Sortie directe sans échappement dans " . basename($view) . ": " . $matches[0];
            }
            
            // Chercher les echo sans htmlspecialchars
            if (preg_match('/echo\s+\$[a-zA-Z_][a-zA-Z0-9_]*[^;]*;/', $content, $matches)) {
                if (!preg_match('/htmlspecialchars\(/', $content)) {
                    $issues[] = "Echo sans échappement dans " . basename($view);
                }
            }
        }
        
        if (!empty($issues)) {
            $this->addVulnerability('XSS', $issues, 'HIGH');
        } else {
            echo "   ✅ Échappement XSS correct\n";
        }
    }
    
    /**
     * Vérifier la protection CSRF
     */
    private function checkCSRF(): void
    {
        echo "🔍 Vérification de la protection CSRF...\n";
        
        $issues = [];
        
        // Vérifier que les formulaires ont des tokens CSRF
        $views = glob($this->baseDir . '/app/views/**/*.php');
        
        foreach ($views as $view) {
            $content = file_get_contents($view);
            
            if (preg_match('/<form[^>]*method=[\'"]post[\'"]/', $content)) {
                if (!preg_match('/csrf_token|CSRF/', $content)) {
                    $issues[] = "Formulaire POST sans token CSRF dans " . basename($view);
                }
            }
        }
        
        if (!empty($issues)) {
            $this->addVulnerability('CSRF', $issues, 'MEDIUM');
        } else {
            echo "   ✅ Protection CSRF en place\n";
        }
    }
    
    /**
     * Vérifier la sécurité des uploads
     */
    private function checkFileUploads(): void
    {
        echo "🔍 Vérification de la sécurité des uploads...\n";
        
        $issues = [];
        
        // Vérifier le contrôleur d'upload
        $uploadController = $this->baseDir . '/app/controllers/admin/UploadController.php';
        
        if (file_exists($uploadController)) {
            $content = file_get_contents($uploadController);
            
            // Vérifier la validation des types MIME
            if (!preg_match('/finfo_file\(/', $content)) {
                $issues[] = "Validation MIME manquante dans UploadController";
            }
            
            // Vérifier la validation des extensions
            if (!preg_match('/pathinfo\(.*PATHINFO_EXTENSION/', $content)) {
                $issues[] = "Validation d'extension manquante dans UploadController";
            }
            
            // Vérifier la validation des dimensions
            if (!preg_match('/getimagesize\(/', $content)) {
                $issues[] = "Validation des dimensions d'image manquante dans UploadController";
            }
        }
        
        if (!empty($issues)) {
            $this->addVulnerability('File Upload', $issues, 'HIGH');
        } else {
            echo "   ✅ Sécurité des uploads correcte\n";
        }
    }
    
    /**
     * Vérifier l'authentification
     */
    private function checkAuthentication(): void
    {
        echo "🔍 Vérification de l'authentification...\n";
        
        $issues = [];
        
        // Vérifier le système d'authentification
        $authFile = $this->baseDir . '/core/Auth.php';
        
        if (file_exists($authFile)) {
            $content = file_get_contents($authFile);
            
            // Vérifier le hachage des mots de passe
            if (!preg_match('/password_hash\(/', $content)) {
                $issues[] = "Hachage des mots de passe non sécurisé";
            }
            
            // Vérifier la vérification des mots de passe
            if (!preg_match('/password_verify\(/', $content)) {
                $issues[] = "Vérification des mots de passe non sécurisée";
            }
        }
        
        if (!empty($issues)) {
            $this->addVulnerability('Authentication', $issues, 'CRITICAL');
        } else {
            echo "   ✅ Authentification sécurisée\n";
        }
    }
    
    /**
     * Vérifier l'autorisation
     */
    private function checkAuthorization(): void
    {
        echo "🔍 Vérification de l'autorisation...\n";
        
        $issues = [];
        
        // Vérifier les contrôleurs admin
        $adminControllers = glob($this->baseDir . '/app/controllers/admin/*.php');
        
        foreach ($adminControllers as $controller) {
            $content = file_get_contents($controller);
            
            if (!preg_match('/requireRole\(|requirePermission\(/', $content)) {
                $issues[] = "Contrôleur admin sans vérification d'autorisation: " . basename($controller);
            }
        }
        
        if (!empty($issues)) {
            $this->addVulnerability('Authorization', $issues, 'HIGH');
        } else {
            echo "   ✅ Autorisation correcte\n";
        }
    }
    
    /**
     * Vérifier la sécurité des sessions
     */
    private function checkSessionSecurity(): void
    {
        echo "🔍 Vérification de la sécurité des sessions...\n";
        
        $issues = [];
        
        // Vérifier la configuration des sessions
        $configFile = $this->baseDir . '/config/config.php';
        
        if (file_exists($configFile)) {
            $content = file_get_contents($configFile);
            
            if (!preg_match('/session\.cookie_httponly|session\.cookie_secure/', $content)) {
                $issues[] = "Configuration de session non sécurisée";
            }
        }
        
        if (!empty($issues)) {
            $this->addVulnerability('Session Security', $issues, 'MEDIUM');
        } else {
            echo "   ✅ Sécurité des sessions correcte\n";
        }
    }
    
    /**
     * Vérifier les en-têtes de sécurité
     */
    private function checkHeaders(): void
    {
        echo "🔍 Vérification des en-têtes de sécurité...\n";
        
        $issues = [];
        
        // Vérifier les en-têtes de sécurité
        $securityFile = $this->baseDir . '/public/security-headers.php';
        
        if (file_exists($securityFile)) {
            $content = file_get_contents($securityFile);
            
            $requiredHeaders = [
                'X-Content-Type-Options',
                'X-Frame-Options',
                'X-XSS-Protection',
                'Strict-Transport-Security',
                'Content-Security-Policy'
            ];
            
            foreach ($requiredHeaders as $header) {
                if (!preg_match('/' . preg_quote($header, '/') . '/', $content)) {
                    $issues[] = "En-tête de sécurité manquant: $header";
                }
            }
        } else {
            $issues[] = "Fichier d'en-têtes de sécurité manquant";
        }
        
        if (!empty($issues)) {
            $this->addVulnerability('Security Headers', $issues, 'MEDIUM');
        } else {
            echo "   ✅ En-têtes de sécurité en place\n";
        }
    }
    
    /**
     * Vérifier la gestion des erreurs
     */
    private function checkErrorHandling(): void
    {
        echo "🔍 Vérification de la gestion des erreurs...\n";
        
        $issues = [];
        
        // Vérifier que les erreurs ne sont pas affichées en production
        $configFile = $this->baseDir . '/config/config.php';
        
        if (file_exists($configFile)) {
            $content = file_get_contents($configFile);
            
            if (preg_match('/display_errors.*true/', $content)) {
                $issues[] = "Affichage des erreurs activé en production";
            }
        }
        
        if (!empty($issues)) {
            $this->addVulnerability('Error Handling', $issues, 'MEDIUM');
        } else {
            echo "   ✅ Gestion des erreurs correcte\n";
        }
    }
    
    /**
     * Vérifier les dépendances
     */
    private function checkDependencies(): void
    {
        echo "🔍 Vérification des dépendances...\n";
        
        $issues = [];
        
        // Vérifier les versions PHP
        $phpVersion = PHP_VERSION;
        if (version_compare($phpVersion, '8.0.0', '<')) {
            $issues[] = "Version PHP obsolète: $phpVersion (minimum recommandé: 8.0)";
        }
        
        // Vérifier les extensions requises
        $requiredExtensions = ['pdo', 'pdo_mysql', 'gd', 'fileinfo', 'json'];
        foreach ($requiredExtensions as $ext) {
            if (!extension_loaded($ext)) {
                $issues[] = "Extension PHP manquante: $ext";
            }
        }
        
        if (!empty($issues)) {
            $this->addVulnerability('Dependencies', $issues, 'LOW');
        } else {
            echo "   ✅ Dépendances à jour\n";
        }
    }
    
    /**
     * Vérifier les permissions de fichiers
     */
    private function checkFilePermissions(): void
    {
        echo "🔍 Vérification des permissions de fichiers...\n";
        
        $issues = [];
        
        // Vérifier les permissions des dossiers sensibles
        $sensitiveDirs = [
            '/app/logs',
            '/public/uploads',
            '/config'
        ];
        
        foreach ($sensitiveDirs as $dir) {
            $fullPath = $this->baseDir . $dir;
            if (is_dir($fullPath)) {
                $perms = fileperms($fullPath);
                if ($perms & 0x0002) { // World writable
                    $issues[] = "Dossier accessible en écriture: $dir";
                }
            }
        }
        
        if (!empty($issues)) {
            $this->addVulnerability('File Permissions', $issues, 'MEDIUM');
        } else {
            echo "   ✅ Permissions de fichiers correctes\n";
        }
    }
    
    /**
     * Ajouter une vulnérabilité
     */
    private function addVulnerability(string $type, array $issues, string $severity): void
    {
        $this->vulnerabilities[] = [
            'type' => $type,
            'issues' => $issues,
            'severity' => $severity
        ];
        
        // Calculer le score
        $penalty = match($severity) {
            'CRITICAL' => 20,
            'HIGH' => 15,
            'MEDIUM' => 10,
            'LOW' => 5,
            default => 0
        };
        
        $this->score -= $penalty;
        if ($this->score < 0) $this->score = 0;
    }
    
    /**
     * Générer le rapport
     */
    private function generateReport(): void
    {
        echo "\n📊 RAPPORT D'AUDIT DE SÉCURITÉ\n";
        echo "==============================\n\n";
        
        echo "🎯 SCORE DE SÉCURITÉ: {$this->score}/100\n\n";
        
        if (empty($this->vulnerabilities)) {
            echo "🎉 AUCUNE VULNÉRABILITÉ DÉTECTÉE !\n";
            echo "L'application est sécurisée.\n";
        } else {
            echo "⚠️ VULNÉRABILITÉS DÉTECTÉES:\n\n";
            
            foreach ($this->vulnerabilities as $vuln) {
                $severityIcon = match($vuln['severity']) {
                    'CRITICAL' => '🔴',
                    'HIGH' => '🟠',
                    'MEDIUM' => '🟡',
                    'LOW' => '🟢',
                    default => '⚪'
                };
                
                echo "{$severityIcon} {$vuln['type']} ({$vuln['severity']})\n";
                foreach ($vuln['issues'] as $issue) {
                    echo "   • $issue\n";
                }
                echo "\n";
            }
            
            $this->generateRecommendations();
        }
        
        echo "📅 Audit effectué le: " . date('Y-m-d H:i:s') . "\n";
    }
    
    /**
     * Générer les recommandations
     */
    private function generateRecommendations(): void
    {
        echo "💡 RECOMMANDATIONS:\n\n";
        
        $recommendations = [
            'CRITICAL' => [
                'Implementer immediatement les corrections',
                'Tester toutes les corrections en environnement de test',
                'Mettre a jour la documentation de securite'
            ],
            'HIGH' => [
                'Planifier les corrections dans les 48h',
                'Mettre en place des tests de securite automatises',
                'Former l\'équipe aux bonnes pratiques'
            ],
            'MEDIUM' => [
                'Corriger lors de la prochaine iteration',
                'Mettre en place un processus de revue de code',
                'Documenter les procedures de securite'
            ],
            'LOW' => [
                'Corriger lors des prochaines mises a jour',
                'Ameliorer la surveillance continue',
                'Maintenir les dependances a jour'
            ]
        ];
        
        foreach ($recommendations as $severity => $recs) {
            if ($this->hasVulnerabilityOfSeverity($severity)) {
                echo "🔸 $severity:\n";
                foreach ($recs as $rec) {
                    echo "   • $rec\n";
                }
                echo "\n";
            }
        }
    }
    
    /**
     * Vérifier s'il y a des vulnérabilités d'une certaine sévérité
     */
    private function hasVulnerabilityOfSeverity(string $severity): bool
    {
        foreach ($this->vulnerabilities as $vuln) {
            if ($vuln['severity'] === $severity) {
                return true;
            }
        }
        return false;
    }
}

// Exécution de l'audit
if (php_sapi_name() === 'cli') {
    $audit = new SecurityAudit();
    $audit->runAudit();
}
