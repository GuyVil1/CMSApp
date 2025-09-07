<?php
declare(strict_types=1);

/**
 * Gestionnaire d'alertes de monitoring
 * Système d'alertes intelligent basé sur les métriques
 */

class AlertManager
{
    private static array $alerts = [];
    private static array $alertHistory = [];
    
    /**
     * Vérifier les métriques et générer des alertes
     */
    public static function checkMetrics(array $metrics, array $systemMetrics): array
    {
        $alerts = [];
        
        // Vérifier le temps de réponse
        if (isset($metrics['timers']['http_request_duration'])) {
            $avgResponseTime = $metrics['timers']['http_request_duration']['avg'] ?? 0;
            if ($avgResponseTime > 1000) { // Plus de 1 seconde
                $alerts[] = [
                    'level' => 'critical',
                    'type' => 'performance',
                    'message' => "Temps de réponse moyen élevé: {$avgResponseTime}ms",
                    'timestamp' => time(),
                    'action' => 'Vérifier les requêtes lentes et optimiser la base de données'
                ];
            } elseif ($avgResponseTime > 500) { // Plus de 500ms
                $alerts[] = [
                    'level' => 'warning',
                    'type' => 'performance',
                    'message' => "Temps de réponse moyen élevé: {$avgResponseTime}ms",
                    'timestamp' => time(),
                    'action' => 'Surveiller les performances'
                ];
            }
        }
        
        // Vérifier le taux d'erreur
        $totalRequests = $metrics['counters']['http_requests_total'] ?? 0;
        $totalErrors = $metrics['counters']['http_errors_total'] ?? 0;
        if ($totalRequests > 0) {
            $errorRate = ($totalErrors / $totalRequests) * 100;
            if ($errorRate > 10) { // Plus de 10% d'erreurs
                $alerts[] = [
                    'level' => 'critical',
                    'type' => 'reliability',
                    'message' => "Taux d'erreur élevé: {$errorRate}%",
                    'timestamp' => time(),
                    'action' => 'Vérifier les logs d\'erreur et corriger les bugs'
                ];
            } elseif ($errorRate > 5) { // Plus de 5% d'erreurs
                $alerts[] = [
                    'level' => 'warning',
                    'type' => 'reliability',
                    'message' => "Taux d'erreur élevé: {$errorRate}%",
                    'timestamp' => time(),
                    'action' => 'Surveiller les erreurs'
                ];
            }
        }
        
        // Vérifier l'utilisation mémoire
        $memoryUsage = $systemMetrics['memory_usage'] ?? 0;
        $memoryLimit = self::parseMemoryLimit($systemMetrics['memory_limit'] ?? '128M');
        if ($memoryLimit > 0) {
            $memoryPercent = ($memoryUsage / $memoryLimit) * 100;
            if ($memoryPercent > 90) { // Plus de 90% de mémoire utilisée
                $alerts[] = [
                    'level' => 'critical',
                    'type' => 'resource',
                    'message' => "Utilisation mémoire critique: {$memoryPercent}%",
                    'timestamp' => time(),
                    'action' => 'Augmenter la limite mémoire ou optimiser le code'
                ];
            } elseif ($memoryPercent > 75) { // Plus de 75% de mémoire utilisée
                $alerts[] = [
                    'level' => 'warning',
                    'type' => 'resource',
                    'message' => "Utilisation mémoire élevée: {$memoryPercent}%",
                    'timestamp' => time(),
                    'action' => 'Surveiller l\'utilisation mémoire'
                ];
            }
        }
        
        // Vérifier la base de données
        if (isset($systemMetrics['database_metrics']['db_ping_time'])) {
            $dbPingTime = $systemMetrics['database_metrics']['db_ping_time'];
            if ($dbPingTime > 100) { // Plus de 100ms
                $alerts[] = [
                    'level' => 'warning',
                    'type' => 'database',
                    'message' => "Latence base de données élevée: {$dbPingTime}ms",
                    'timestamp' => time(),
                    'action' => 'Vérifier la connexion DB et optimiser les requêtes'
                ];
            }
        }
        
        // Stocker les alertes
        self::$alerts = $alerts;
        self::storeAlertHistory($alerts);
        
        return $alerts;
    }
    
    /**
     * Obtenir les alertes actives
     */
    public static function getActiveAlerts(): array
    {
        return self::$alerts;
    }
    
    /**
     * Obtenir l'historique des alertes
     */
    public static function getAlertHistory(int $limit = 50): array
    {
        return array_slice(self::$alertHistory, -$limit);
    }
    
    /**
     * Parser la limite de mémoire PHP
     */
    private static function parseMemoryLimit(string $limit): int
    {
        $limit = trim($limit);
        $unit = strtoupper(substr($limit, -1));
        $value = (int) substr($limit, 0, -1);
        
        switch ($unit) {
            case 'G':
                return $value * 1024 * 1024 * 1024;
            case 'M':
                return $value * 1024 * 1024;
            case 'K':
                return $value * 1024;
            default:
                return (int) $limit;
        }
    }
    
    /**
     * Stocker l'historique des alertes
     */
    private static function storeAlertHistory(array $alerts): void
    {
        foreach ($alerts as $alert) {
            self::$alertHistory[] = $alert;
        }
        
        // Garder seulement les 1000 dernières alertes
        if (count(self::$alertHistory) > 1000) {
            self::$alertHistory = array_slice(self::$alertHistory, -1000);
        }
    }
    
    /**
     * Obtenir le statut global du système
     */
    public static function getSystemStatus(): string
    {
        $criticalAlerts = array_filter(self::$alerts, fn($alert) => $alert['level'] === 'critical');
        $warningAlerts = array_filter(self::$alerts, fn($alert) => $alert['level'] === 'warning');
        
        if (count($criticalAlerts) > 0) {
            return 'critical';
        } elseif (count($warningAlerts) > 0) {
            return 'warning';
        } else {
            return 'healthy';
        }
    }
}
