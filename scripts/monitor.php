<?php
/**
 * Script de monitoring des performances
 * Surveillance de l'application en temps réel
 */

require_once __DIR__ . '/../config/performance.php';
require_once __DIR__ . '/../app/helpers/MemoryCache.php';

class PerformanceMonitor
{
    private array $config;
    private array $metrics = [];
    
    public function __construct()
    {
        $this->config = require __DIR__ . '/../config/performance.php';
    }
    
    /**
     * Collecter les métriques système
     */
    public function collectMetrics(): array
    {
        $this->metrics = [
            'timestamp' => date('Y-m-d H:i:s'),
            'memory_usage' => $this->getMemoryUsage(),
            'cache_stats' => $this->getCacheStats(),
            'database_stats' => $this->getDatabaseStats(),
            'log_stats' => $this->getLogStats(),
            'system_load' => $this->getSystemLoad()
        ];
        
        return $this->metrics;
    }
    
    /**
     * Utilisation mémoire
     */
    private function getMemoryUsage(): array
    {
        return [
            'current' => memory_get_usage(true),
            'peak' => memory_get_peak_usage(true),
            'limit' => ini_get('memory_limit'),
            'usage_percent' => round((memory_get_usage(true) / $this->parseMemoryLimit()) * 100, 2)
        ];
    }
    
    /**
     * Statistiques du cache
     */
    private function getCacheStats(): array
    {
        try {
            return MemoryCache::getStats();
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
    
    /**
     * Statistiques de la base de données
     */
    private function getDatabaseStats(): array
    {
        try {
            require_once __DIR__ . '/../core/Database.php';
            $db = Database::getInstance();
            
            $stats = [];
            $stats['status'] = 'connected';
            $stats['driver'] = 'PDO';
            
            // Test de connectivité simple
            $result = $db->query("SELECT 1 as test");
            $stats['test_query'] = $result ? 'success' : 'failed';
            
            return $stats;
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
    
    /**
     * Statistiques des logs
     */
    private function getLogStats(): array
    {
        $logDir = __DIR__ . '/../app/logs';
        $stats = [];
        
        if (is_dir($logDir)) {
            $files = glob($logDir . '/*.log');
            $stats['total_files'] = count($files);
            $stats['total_size'] = 0;
            
            foreach ($files as $file) {
                $stats['total_size'] += filesize($file);
            }
            
            $stats['total_size_mb'] = round($stats['total_size'] / 1024 / 1024, 2);
        }
        
        return $stats;
    }
    
    /**
     * Charge système
     */
    private function getSystemLoad(): array
    {
        if (function_exists('sys_getloadavg')) {
            $load = sys_getloadavg();
            return [
                '1min' => $load[0],
                '5min' => $load[1],
                '15min' => $load[2]
            ];
        }
        
        return ['error' => 'sys_getloadavg not available'];
    }
    
    /**
     * Parser la limite mémoire
     */
    private function parseMemoryLimit(): int
    {
        $limit = ini_get('memory_limit');
        $unit = strtolower(substr($limit, -1));
        $value = (int) $limit;
        
        switch ($unit) {
            case 'g': return $value * 1024 * 1024 * 1024;
            case 'm': return $value * 1024 * 1024;
            case 'k': return $value * 1024;
            default: return $value;
        }
    }
    
    /**
     * Générer un rapport
     */
    public function generateReport(): string
    {
        $metrics = $this->collectMetrics();
        
        $report = "📊 RAPPORT DE PERFORMANCE - " . $metrics['timestamp'] . "\n\n";
        
        // Mémoire
        $report .= "🧠 MÉMOIRE:\n";
        $report .= "   Utilisation: " . $this->formatBytes($metrics['memory_usage']['current']) . "\n";
        $report .= "   Pic: " . $this->formatBytes($metrics['memory_usage']['peak']) . "\n";
        $report .= "   Limite: " . $metrics['memory_usage']['limit'] . "\n";
        $report .= "   Pourcentage: " . $metrics['memory_usage']['usage_percent'] . "%\n\n";
        
        // Cache
        $report .= "⚡ CACHE:\n";
        if (isset($metrics['cache_stats']['error'])) {
            $report .= "   Erreur: " . $metrics['cache_stats']['error'] . "\n";
        } else {
            $report .= "   Hits: " . $metrics['cache_stats']['hits'] . "\n";
            $report .= "   Misses: " . $metrics['cache_stats']['misses'] . "\n";
            $report .= "   Taux de réussite: " . $metrics['cache_stats']['hit_rate'] . "\n";
            $report .= "   Éléments: " . $metrics['cache_stats']['items_count'] . "\n";
        }
        $report .= "\n";
        
        // Logs
        $report .= "📝 LOGS:\n";
        $report .= "   Fichiers: " . $metrics['log_stats']['total_files'] . "\n";
        $report .= "   Taille totale: " . $metrics['log_stats']['total_size_mb'] . " MB\n\n";
        
        // Charge système
        $report .= "⚙️ CHARGE SYSTÈME:\n";
        if (isset($metrics['system_load']['error'])) {
            $report .= "   " . $metrics['system_load']['error'] . "\n";
        } else {
            $report .= "   1 min: " . $metrics['system_load']['1min'] . "\n";
            $report .= "   5 min: " . $metrics['system_load']['5min'] . "\n";
            $report .= "   15 min: " . $metrics['system_load']['15min'] . "\n";
        }
        
        return $report;
    }
    
    /**
     * Formater les bytes
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }
}

// Exécution du monitoring
if (php_sapi_name() === 'cli') {
    $monitor = new PerformanceMonitor();
    echo $monitor->generateReport();
}
