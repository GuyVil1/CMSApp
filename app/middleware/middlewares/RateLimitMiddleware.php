<?php
declare(strict_types=1);

require_once __DIR__ . '/../MiddlewareInterface.php';
require_once __DIR__ . '/../RequestInterface.php';
require_once __DIR__ . '/../ResponseInterface.php';
require_once __DIR__ . '/../HttpResponse.php';
require_once __DIR__ . '/../../helpers/MemoryCache.php';

/**
 * Middleware de rate limiting
 * Limite le nombre de requêtes par IP
 */
class RateLimitMiddleware implements MiddlewareInterface
{
    private int $maxRequests;
    private int $timeWindow;
    private array $excludedRoutes;
    
    public function __construct(int $maxRequests = 100, int $timeWindow = 3600, array $excludedRoutes = [])
    {
        $this->maxRequests = $maxRequests;
        $this->timeWindow = $timeWindow;
        $this->excludedRoutes = $excludedRoutes;
    }
    
    public function handle(RequestInterface $request, callable $next): ResponseInterface
    {
        $ip = $request->getClientIp();
        $uri = $request->getUri();
        
        // Vérifier si la route est exclue
        if ($this->isExcludedRoute($uri)) {
            return $next($request);
        }
        
        // Vérifier le rate limit
        if (!$this->checkRateLimit($ip)) {
            return HttpResponse::error('Rate limit exceeded', 429);
        }
        
        // Enregistrer la requête
        $this->recordRequest($ip);
        
        // Continuer vers le prochain middleware
        return $next($request);
    }
    
    public function canHandle(RequestInterface $request): bool
    {
        return true; // Appliquer à toutes les requêtes
    }
    
    public function getPriority(): int
    {
        return 90; // Priorité élevée, juste après l'authentification
    }
    
    /**
     * Vérifier si une route est exclue du rate limiting
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
     * Vérifier le rate limit pour une IP
     */
    private function checkRateLimit(string $ip): bool
    {
        $key = "rate_limit_{$ip}";
        $requests = MemoryCache::get($key, []);
        
        $now = time();
        $windowStart = $now - $this->timeWindow;
        
        // Filtrer les requêtes dans la fenêtre de temps
        $requests = array_filter($requests, function($timestamp) use ($windowStart) {
            return $timestamp > $windowStart;
        });
        
        return count($requests) < $this->maxRequests;
    }
    
    /**
     * Enregistrer une requête
     */
    private function recordRequest(string $ip): void
    {
        $key = "rate_limit_{$ip}";
        $requests = MemoryCache::get($key, []);
        
        $requests[] = time();
        
        // Garder seulement les requêtes récentes
        $now = time();
        $windowStart = $now - $this->timeWindow;
        $requests = array_filter($requests, function($timestamp) use ($windowStart) {
            return $timestamp > $windowStart;
        });
        
        MemoryCache::put($key, $requests, $this->timeWindow);
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
