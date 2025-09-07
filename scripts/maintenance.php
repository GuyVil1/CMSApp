<?php
/**
 * Script de maintenance
 * Nettoyage et optimisation de l'application
 */

class MaintenanceScript
{
    private string $baseDir;
    private array $stats = [];
    
    public function __construct()
    {
        $this->baseDir = dirname(__DIR__);
    }
    
    /**
     * Exécuter toutes les tâches de maintenance
     */
    public function runAll(): void
    {
        echo "🧹 DÉMARRAGE DE LA MAINTENANCE\n\n";
        
        $this->cleanLogs();
        $this->cleanCache();
        $this->optimizeDatabase();
        $this->cleanTempFiles();
        $this->generateReport();
    }
    
    /**
     * Nettoyer les logs anciens
     */
    private function cleanLogs(): void
    {
        echo "📝 Nettoyage des logs...\n";
        
        $logDir = $this->baseDir . '/app/logs';
        $maxAge = 30; // jours
        $maxSize = 100 * 1024 * 1024; // 100MB
        
        if (!is_dir($logDir)) {
            echo "   ⚠️ Dossier logs non trouvé\n";
            return;
        }
        
        $files = glob($logDir . '/*.log');
        $deleted = 0;
        $freed = 0;
        
        foreach ($files as $file) {
            $age = (time() - filemtime($file)) / (24 * 3600);
            $size = filesize($file);
            
            if ($age > $maxAge || $size > $maxSize) {
                $freed += $size;
                unlink($file);
                $deleted++;
            }
        }
        
        $this->stats['logs'] = [
            'deleted' => $deleted,
            'freed_mb' => round($freed / 1024 / 1024, 2)
        ];
        
        echo "   ✅ $deleted fichiers supprimés, " . round($freed / 1024 / 1024, 2) . " MB libérés\n";
    }
    
    /**
     * Nettoyer le cache
     */
    private function cleanCache(): void
    {
        echo "⚡ Nettoyage du cache...\n";
        
        $cacheDir = $this->baseDir . '/app/cache';
        $deleted = 0;
        $freed = 0;
        
        if (is_dir($cacheDir)) {
            $files = glob($cacheDir . '/**/*', GLOB_BRACE);
            
            foreach ($files as $file) {
                if (is_file($file)) {
                    $age = (time() - filemtime($file)) / 3600; // heures
                    
                    if ($age > 24) { // Plus de 24h
                        $freed += filesize($file);
                        unlink($file);
                        $deleted++;
                    }
                }
            }
        }
        
        $this->stats['cache'] = [
            'deleted' => $deleted,
            'freed_mb' => round($freed / 1024 / 1024, 2)
        ];
        
        echo "   ✅ $deleted fichiers supprimés, " . round($freed / 1024 / 1024, 2) . " MB libérés\n";
    }
    
    /**
     * Optimiser la base de données
     */
    private function optimizeDatabase(): void
    {
        echo "🗄️ Optimisation de la base de données...\n";
        
        try {
            require_once $this->baseDir . '/core/Database.php';
            $db = Database::getInstance();
            
            $tables = ['articles', 'categories', 'games', 'media', 'users', 'tags'];
            $optimized = 0;
            
            foreach ($tables as $table) {
                try {
                    $db->query("OPTIMIZE TABLE $table");
                    $optimized++;
                } catch (Exception $e) {
                    echo "   ⚠️ Erreur optimisation table $table: " . $e->getMessage() . "\n";
                }
            }
            
            $this->stats['database'] = ['optimized_tables' => $optimized];
            echo "   ✅ $optimized tables optimisées\n";
            
        } catch (Exception $e) {
            echo "   ❌ Erreur: " . $e->getMessage() . "\n";
        }
    }
    
    /**
     * Nettoyer les fichiers temporaires
     */
    private function cleanTempFiles(): void
    {
        echo "🗑️ Nettoyage des fichiers temporaires...\n";
        
        $tempDirs = [
            $this->baseDir . '/tmp',
            $this->baseDir . '/temp',
            sys_get_temp_dir() . '/bvg_*'
        ];
        
        $deleted = 0;
        $freed = 0;
        
        foreach ($tempDirs as $dir) {
            if (is_dir($dir)) {
                $files = glob($dir . '/*');
                
                foreach ($files as $file) {
                    if (is_file($file)) {
                        $age = (time() - filemtime($file)) / 3600; // heures
                        
                        if ($age > 1) { // Plus d'1h
                            $freed += filesize($file);
                            unlink($file);
                            $deleted++;
                        }
                    }
                }
            }
        }
        
        $this->stats['temp'] = [
            'deleted' => $deleted,
            'freed_mb' => round($freed / 1024 / 1024, 2)
        ];
        
        echo "   ✅ $deleted fichiers supprimés, " . round($freed / 1024 / 1024, 2) . " MB libérés\n";
    }
    
    /**
     * Générer le rapport
     */
    private function generateReport(): void
    {
        echo "\n📊 RAPPORT DE MAINTENANCE\n";
        echo "========================\n\n";
        
        $totalFreed = 0;
        
        foreach ($this->stats as $type => $data) {
            echo strtoupper($type) . ":\n";
            foreach ($data as $key => $value) {
                echo "   $key: $value\n";
                if (strpos($key, 'freed') !== false) {
                    $totalFreed += $value;
                }
            }
            echo "\n";
        }
        
        echo "🎯 RÉSUMÉ:\n";
        echo "   Espace libéré total: " . round($totalFreed, 2) . " MB\n";
        echo "   Maintenance terminée: " . date('Y-m-d H:i:s') . "\n";
    }
}

// Exécution du script
if (php_sapi_name() === 'cli') {
    $maintenance = new MaintenanceScript();
    $maintenance->runAll();
}
