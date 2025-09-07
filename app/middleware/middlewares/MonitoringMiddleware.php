<?php
declare(strict_types=1);

/**
 * Middleware de monitoring des performances
 * Collecte automatique des métriques
 */

require_once __DIR__ . '/../MiddlewareInterface.php';
require_once __DIR__ . '/../RequestInterface.php';
require_once __DIR__ . '/../ResponseInterface.php';
require_once __DIR__ . '/../../monitoring/MetricsCollector.php';

class MonitoringMiddleware implements MiddlewareInterface
{
    private float $startTime;
    private int $startMemory;
    
    public function __construct()
    {
        $this->startTime = microtime(true);
        $this->startMemory = memory_get_usage(true);
    }
    
    public function handle(RequestInterface $request, callable $next): ResponseInterface
    {
        // Démarrer le monitoring
        $this->startMonitoring($request);
        
        try {
            // Exécuter la requête suivante
            $response = $next($request);
            
            // Enregistrer les métriques de succès
            $this->recordSuccessMetrics($request, $response);
            
            return $response;
            
        } catch (Exception $e) {
            // Enregistrer les métriques d'erreur
            $this->recordErrorMetrics($request, $e);
            throw $e;
        }
    }
    
    private function startMonitoring(RequestInterface $request): void
    {
        // Incrémenter le compteur de requêtes
        MetricsCollector::increment('http_requests_total', 1, [
            'method' => $request->getMethod(),
            'route' => $this->extractRoute($request->getUri())
        ]);
        
        // Enregistrer l'utilisation mémoire au début
        MetricsCollector::gauge('system_memory_usage', memory_get_usage(true));
        MetricsCollector::gauge('system_memory_peak', memory_get_peak_usage(true));
    }
    
    private function recordSuccessMetrics(RequestInterface $request, ResponseInterface $response): void
    {
        $duration = (microtime(true) - $this->startTime) * 1000; // en ms
        $memoryUsed = memory_get_usage(true) - $this->startMemory;
        
        // Timer de la requête
        MetricsCollector::timer('http_request_duration', $duration, [
            'method' => $request->getMethod(),
            'route' => $this->extractRoute($request->getUri()),
            'status' => $response->getStatusCode()
        ]);
        
        // Métriques de mémoire
        MetricsCollector::gauge('http_memory_usage', $memoryUsed, [
            'method' => $request->getMethod(),
            'route' => $this->extractRoute($request->getUri())
        ]);
        
        // Compteurs par statut
        MetricsCollector::increment('http_responses_total', 1, [
            'status' => $response->getStatusCode(),
            'method' => $request->getMethod()
        ]);
        
        // Métriques système finales
        MetricsCollector::gauge('system_memory_usage_final', memory_get_usage(true));
        MetricsCollector::gauge('system_memory_peak_final', memory_get_peak_usage(true));
    }
    
    private function recordErrorMetrics(RequestInterface $request, Exception $e): void
    {
        $duration = (microtime(true) - $this->startTime) * 1000;
        
        // Incrémenter le compteur d'erreurs
        MetricsCollector::increment('http_errors_total', 1, [
            'method' => $request->getMethod(),
            'route' => $this->extractRoute($request->getUri()),
            'error_type' => get_class($e)
        ]);
        
        // Timer même pour les erreurs
        MetricsCollector::timer('http_error_duration', $duration, [
            'method' => $request->getMethod(),
            'route' => $this->extractRoute($request->getUri()),
            'error_type' => get_class($e)
        ]);
    }
    
    private function extractRoute(string $uri): string
    {
        // Extraire la route principale (sans paramètres)
        $path = parse_url($uri, PHP_URL_PATH);
        $segments = explode('/', trim($path, '/'));
        
        if (empty($segments[0])) {
            return 'home';
        }
        
        // Limiter à 2 segments pour éviter trop de granularité
        return implode('/', array_slice($segments, 0, 2));
    }
    
    /**
     * Vérifier si le middleware peut traiter cette requête
     */
    public function canHandle(RequestInterface $request): bool
    {
        // Le monitoring peut traiter toutes les requêtes
        return true;
    }
    
    /**
     * Obtenir la priorité du middleware
     */
    public function getPriority(): int
    {
        // Priorité élevée pour capturer toutes les métriques
        return 100;
    }
}