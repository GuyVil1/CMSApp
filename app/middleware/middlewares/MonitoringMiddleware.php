<?php
declare(strict_types=1);

/**
 * Middleware de monitoring
 * Collecte automatiquement les métriques de performance
 */

require_once __DIR__ . '/../MiddlewareInterface.php';
require_once __DIR__ . '/../RequestInterface.php';
require_once __DIR__ . '/../ResponseInterface.php';
require_once __DIR__ . '/../../monitoring/MetricsCollector.php';

class MonitoringMiddleware implements MiddlewareInterface
{
    private int $priority = 5; // Très haute priorité pour capturer tout
    
    public function getPriority(): int
    {
        return $this->priority;
    }
    
    public function canHandle(RequestInterface $request): bool
    {
        // Le monitoring middleware peut traiter toutes les requêtes
        return true;
    }
    
    public function handle(RequestInterface $request, callable $next): ResponseInterface
    {
        $startTime = microtime(true);
        $startMemory = memory_get_usage(true);
        
        // Métriques de requête
        MetricsCollector::increment('http_requests_total', 1, [
            'method' => $request->getMethod(),
            'path' => $this->normalizePath($request->getUri())
        ]);
        
        // Traitement de la requête
        $response = $next($request);
        
        // Calcul des métriques
        $duration = (microtime(true) - $startTime) * 1000; // en ms
        $memoryUsed = memory_get_usage(true) - $startMemory;
        
        // Enregistrement des métriques
        MetricsCollector::timer('http_request_duration', $duration, [
            'method' => $request->getMethod(),
            'path' => $this->normalizePath($request->getUri()),
            'status' => $response->getStatusCode()
        ]);
        
        MetricsCollector::gauge('http_request_memory', $memoryUsed, [
            'method' => $request->getMethod(),
            'path' => $this->normalizePath($request->getUri())
        ]);
        
        MetricsCollector::increment('http_responses_total', 1, [
            'method' => $request->getMethod(),
            'path' => $this->normalizePath($request->getUri()),
            'status' => $response->getStatusCode()
        ]);
        
        // Métriques d'erreur
        if ($response->getStatusCode() >= 400) {
            MetricsCollector::increment('http_errors_total', 1, [
                'status' => $response->getStatusCode(),
                'path' => $this->normalizePath($request->getUri())
            ]);
        }
        
        // Métriques système
        MetricsCollector::gauge('system_memory_usage', memory_get_usage(true));
        MetricsCollector::gauge('system_memory_peak', memory_get_peak_usage(true));
        
        return $response;
    }
    
    /**
     * Normaliser le chemin pour éviter trop de métriques uniques
     */
    private function normalizePath(string $path): string
    {
        // Remplacer les IDs par des placeholders
        $path = preg_replace('/\/\d+/', '/{id}', $path);
        $path = preg_replace('/\/[a-f0-9-]{36}/', '/{uuid}', $path); // UUIDs
        
        // Limiter la profondeur
        $parts = explode('/', trim($path, '/'));
        if (count($parts) > 4) {
            $parts = array_slice($parts, 0, 4);
            $path = '/' . implode('/', $parts) . '/...';
        }
        
        return $path ?: '/';
    }
}
