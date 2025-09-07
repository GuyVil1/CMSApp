<?php
declare(strict_types=1);

/**
 * Contrôleur de monitoring
 * Dashboard de surveillance des performances
 */

namespace Admin;

require_once __DIR__ . '/../../monitoring/MetricsCollector.php';

class MonitoringController
{
    /**
     * Action par défaut - redirige vers le dashboard
     */
    public function index(): void
    {
        $this->dashboard();
    }
    
    /**
     * Dashboard principal de monitoring
     */
    public function dashboard(): void
    {
        $metrics = \MetricsCollector::getAllMetrics();
        $systemMetrics = \MetricsCollector::getSystemMetrics();
        
        // Calculer les statistiques
        $stats = $this->calculateStats($metrics);
        
        $this->renderDashboard([
            'metrics' => $metrics,
            'systemMetrics' => $systemMetrics,
            'stats' => $stats,
            'title' => 'Dashboard de Monitoring'
        ]);
    }
    
    /**
     * API pour récupérer les métriques en JSON
     */
    public function api(): void
    {
        header('Content-Type: application/json');
        
        $metrics = \MetricsCollector::getAllMetrics();
        $systemMetrics = \MetricsCollector::getSystemMetrics();
        $stats = $this->calculateStats($metrics);
        
        echo json_encode([
            'metrics' => $metrics,
            'system' => $systemMetrics,
            'stats' => $stats,
            'timestamp' => time()
        ]);
    }
    
    /**
     * Réinitialiser les métriques
     */
    public function reset(): void
    {
        \MetricsCollector::reset();
        
        header('Location: /admin/monitoring');
        exit;
    }
    
    /**
     * Calculer les statistiques
     */
    private function calculateStats(array $metrics): array
    {
        $stats = [
            'total_requests' => 0,
            'avg_response_time' => 0,
            'error_rate' => 0,
            'memory_usage' => 0
        ];
        
        // Total des requêtes
        if (isset($metrics['counters']['http_requests_total'])) {
            $stats['total_requests'] = $metrics['counters']['http_requests_total'];
        }
        
        // Temps de réponse moyen
        if (isset($metrics['timers']['http_request_duration'])) {
            $timerStats = $metrics['timers']['http_request_duration'];
            $stats['avg_response_time'] = round($timerStats['avg'], 2);
        }
        
        // Taux d'erreur
        $totalRequests = $stats['total_requests'];
        $totalErrors = $metrics['counters']['http_errors_total'] ?? 0;
        if ($totalRequests > 0) {
            $stats['error_rate'] = round(($totalErrors / $totalRequests) * 100, 2);
        }
        
        // Utilisation mémoire
        if (isset($metrics['gauges']['system_memory_usage'])) {
            $stats['memory_usage'] = $metrics['gauges']['system_memory_usage'];
        }
        
        return $stats;
    }
    
    /**
     * Rendre le dashboard
     */
    private function renderDashboard(array $data): void
    {
        extract($data);
        include __DIR__ . '/../../views/admin/monitoring/dashboard.php';
    }
}
