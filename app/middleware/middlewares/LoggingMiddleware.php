<?php
declare(strict_types=1);

require_once __DIR__ . '/../MiddlewareInterface.php';
require_once __DIR__ . '/../RequestInterface.php';
require_once __DIR__ . '/../ResponseInterface.php';
require_once __DIR__ . '/../HttpResponse.php';

/**
 * Middleware de logging des requêtes
 * Enregistre toutes les requêtes et réponses
 */
class LoggingMiddleware implements MiddlewareInterface
{
    private string $logFile;
    private array $excludedRoutes;
    private bool $logPostData;
    
    public function __construct(string $logFile = null, array $excludedRoutes = [], bool $logPostData = false)
    {
        $this->logFile = $logFile ?? __DIR__ . '/../../logs/requests.log';
        $this->excludedRoutes = $excludedRoutes;
        $this->logPostData = $logPostData;
        
        // Créer le dossier de logs s'il n'existe pas
        $logDir = dirname($this->logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
    }
    
    public function handle(RequestInterface $request, callable $next): ResponseInterface
    {
        $startTime = microtime(true);
        
        // Vérifier si la route est exclue
        if ($this->isExcludedRoute($request->getUri())) {
            return $next($request);
        }
        
        // Continuer vers le prochain middleware
        $response = $next($request);
        
        // Calculer le temps de traitement
        $endTime = microtime(true);
        $processingTime = round(($endTime - $startTime) * 1000, 2); // en millisecondes
        
        // Logger la requête
        $this->logRequest($request, $response, $processingTime);
        
        return $response;
    }
    
    public function canHandle(RequestInterface $request): bool
    {
        return !$this->isExcludedRoute($request->getUri());
    }
    
    public function getPriority(): int
    {
        return 10; // Priorité faible, exécuté en dernier
    }
    
    /**
     * Vérifier si une route est exclue du logging
     */
    private function isExcludedRoute(string $uri): bool
    {
        foreach ($this->excludedRoutes as $pattern) {
            if ($this->matchRoute($uri, $pattern)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Logger une requête
     */
    private function logRequest(RequestInterface $request, ResponseInterface $response, float $processingTime): void
    {
        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'method' => $request->getMethod(),
            'uri' => $request->getUri(),
            'ip' => $request->getClientIp(),
            'user_agent' => $request->getUserAgent(),
            'status_code' => $response->getStatusCode(),
            'processing_time_ms' => $processingTime,
            'query_params' => $request->getQueryParams()
        ];
        
        // Ajouter les données POST si demandé
        if ($this->logPostData && $request->isPost()) {
            $logEntry['post_params'] = $request->getPostParams();
        }
        
        // Ajouter la taille de la réponse
        $logEntry['response_size'] = strlen($response->getContent());
        
        $logLine = json_encode($logEntry, JSON_UNESCAPED_UNICODE) . "\n";
        file_put_contents($this->logFile, $logLine, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Matcher une route avec un pattern
     */
    private function matchRoute(string $uri, string $pattern): bool
    {
        $pattern = str_replace('*', '.*', $pattern);
        // Échapper les délimiteurs de regex
        $pattern = preg_quote($pattern, '/');
        $pattern = '/^' . $pattern . '$/';
        
        return preg_match($pattern, $uri) === 1;
    }
}
