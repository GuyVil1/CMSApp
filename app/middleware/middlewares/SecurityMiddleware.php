<?php
declare(strict_types=1);

require_once __DIR__ . '/../MiddlewareInterface.php';
require_once __DIR__ . '/../RequestInterface.php';
require_once __DIR__ . '/../ResponseInterface.php';
require_once __DIR__ . '/../HttpResponse.php';

/**
 * Middleware de sécurité
 * Protection contre les attaques courantes
 */
class SecurityMiddleware implements MiddlewareInterface
{
    private array $blockedUserAgents;
    private array $blockedIps;
    private array $allowedMethods;
    
    public function __construct(array $blockedUserAgents = [], array $blockedIps = [], array $allowedMethods = ['GET', 'POST', 'PUT', 'DELETE', 'PATCH'])
    {
        $this->blockedUserAgents = $blockedUserAgents;
        $this->blockedIps = $blockedIps;
        $this->allowedMethods = $allowedMethods;
    }
    
    public function handle(RequestInterface $request, callable $next): ResponseInterface
    {
        // Vérifier la méthode HTTP
        if (!$this->isAllowedMethod($request->getMethod())) {
            return HttpResponse::error('Method not allowed', 405);
        }
        
        // Vérifier l'IP
        if ($this->isBlockedIp($request->getClientIp())) {
            return HttpResponse::error('Access denied', 403);
        }
        
        // Vérifier le User-Agent
        if ($this->isBlockedUserAgent($request->getUserAgent())) {
            return HttpResponse::error('Access denied', 403);
        }
        
        // Vérifier les injections SQL basiques
        if ($this->hasSqlInjection($request)) {
            return HttpResponse::error('Invalid request', 400);
        }
        
        // Vérifier les scripts XSS basiques
        if ($this->hasXssAttempt($request)) {
            return HttpResponse::error('Invalid request', 400);
        }
        
        // Continuer vers le prochain middleware
        return $next($request);
    }
    
    public function canHandle(RequestInterface $request): bool
    {
        return true; // Appliquer à toutes les requêtes
    }
    
    public function getPriority(): int
    {
        return 95; // Priorité très élevée, juste après l'authentification
    }
    
    /**
     * Vérifier si la méthode HTTP est autorisée
     */
    private function isAllowedMethod(string $method): bool
    {
        return in_array(strtoupper($method), $this->allowedMethods);
    }
    
    /**
     * Vérifier si l'IP est bloquée
     */
    private function isBlockedIp(string $ip): bool
    {
        foreach ($this->blockedIps as $blockedIp) {
            if ($this->matchIp($ip, $blockedIp)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Vérifier si le User-Agent est bloqué
     */
    private function isBlockedUserAgent(?string $userAgent): bool
    {
        if (!$userAgent) {
            return false;
        }
        
        foreach ($this->blockedUserAgents as $blockedUA) {
            if (stripos($userAgent, $blockedUA) !== false) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Détecter les tentatives d'injection SQL
     */
    private function hasSqlInjection(RequestInterface $request): bool
    {
        $sqlPatterns = [
            '/(\bunion\b.*\bselect\b)/i',
            '/(\bselect\b.*\bfrom\b)/i',
            '/(\binsert\b.*\binto\b)/i',
            '/(\bupdate\b.*\bset\b)/i',
            '/(\bdelete\b.*\bfrom\b)/i',
            '/(\bdrop\b.*\btable\b)/i',
            '/(\bexec\b|\bexecute\b)/i',
            '/(\bscript\b.*\balert\b)/i'
        ];
        
        $data = array_merge($request->getQueryParams(), $request->getPostParams());
        
        foreach ($data as $value) {
            if (is_string($value)) {
                foreach ($sqlPatterns as $pattern) {
                    if (preg_match($pattern, $value)) {
                        return true;
                    }
                }
            }
        }
        
        return false;
    }
    
    /**
     * Détecter les tentatives XSS
     */
    private function hasXssAttempt(RequestInterface $request): bool
    {
        $xssPatterns = [
            '/<script[^>]*>.*?<\/script>/i',
            '/javascript:/i',
            '/on\w+\s*=/i',
            '/<object[^>]*>/i',
            '/<embed[^>]*>/i'
        ];
        
        $data = array_merge($request->getQueryParams(), $request->getPostParams());
        
        foreach ($data as $value) {
            if (is_string($value)) {
                // Vérifier les patterns XSS généraux
                foreach ($xssPatterns as $pattern) {
                    if (preg_match($pattern, $value)) {
                        return true;
                    }
                }
                
                // Vérifier les iframes - autoriser seulement YouTube et Vimeo
                if (preg_match('/<iframe[^>]*>/i', $value)) {
                    // Extraire tous les iframes
                    preg_match_all('/<iframe[^>]*src=["\']([^"\']*)["\'][^>]*>/i', $value, $matches);
                    
                    foreach ($matches[1] as $src) {
                        // Autoriser seulement YouTube et Vimeo
                        if (!preg_match('/^(https?:\/\/)?(www\.)?(youtube\.com\/embed\/|youtu\.be\/|player\.vimeo\.com\/video\/)/i', $src)) {
                            return true; // iframe non autorisé
                        }
                    }
                }
            }
        }
        
        return false;
    }
    
    /**
     * Matcher une IP avec un pattern
     */
    private function matchIp(string $ip, string $pattern): bool
    {
        // Support des CIDR et des IPs simples
        if (strpos($pattern, '/') !== false) {
            // CIDR notation
            list($subnet, $mask) = explode('/', $pattern);
            $ipLong = ip2long($ip);
            $subnetLong = ip2long($subnet);
            $maskLong = -1 << (32 - $mask);
            
            return ($ipLong & $maskLong) === ($subnetLong & $maskLong);
        }
        
        return $ip === $pattern;
    }
}
