<?php
declare(strict_types=1);

require_once __DIR__ . '/../MiddlewareInterface.php';
require_once __DIR__ . '/../RequestInterface.php';
require_once __DIR__ . '/../ResponseInterface.php';
require_once __DIR__ . '/../HttpResponse.php';
require_once __DIR__ . '/../../../core/Auth.php';

/**
 * Middleware d'authentification
 * Vérifie si l'utilisateur est connecté pour les routes protégées
 */
class AuthenticationMiddleware implements MiddlewareInterface
{
    private array $protectedRoutes;
    private array $publicRoutes;
    
    public function __construct(array $protectedRoutes = [], array $publicRoutes = [])
    {
        $this->protectedRoutes = $protectedRoutes;
        $this->publicRoutes = $publicRoutes;
    }
    
    public function handle(RequestInterface $request, callable $next): ResponseInterface
    {
        $uri = $request->getUri();
        
        // Vérifier si la route est protégée
        if ($this->isProtectedRoute($uri)) {
            if (!Auth::isLoggedIn()) {
                // Rediriger vers la page de connexion
                return HttpResponse::redirect('/auth/login?redirect=' . urlencode($uri));
            }
        }
        
        // Continuer vers le prochain middleware
        return $next($request);
    }
    
    public function canHandle(RequestInterface $request): bool
    {
        $uri = $request->getUri();
        return $this->isProtectedRoute($uri) || $this->isPublicRoute($uri);
    }
    
    public function getPriority(): int
    {
        return 100; // Haute priorité pour l'authentification
    }
    
    /**
     * Vérifier si une route est protégée
     */
    private function isProtectedRoute(string $uri): bool
    {
        // Routes admin par défaut
        if (strpos($uri, '/admin/') === 0) {
            return true;
        }
        
        // Vérifier les routes personnalisées
        foreach ($this->protectedRoutes as $pattern) {
            if ($this->matchRoute($uri, $pattern)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Vérifier si une route est publique
     */
    private function isPublicRoute(string $uri): bool
    {
        foreach ($this->publicRoutes as $pattern) {
            if ($this->matchRoute($uri, $pattern)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Matcher une route avec un pattern
     */
    private function matchRoute(string $uri, string $pattern): bool
    {
        // Convertir le pattern en regex
        $pattern = str_replace('*', '.*', $pattern);
        // Échapper les caractères spéciaux de regex (sauf les délimiteurs)
        $pattern = preg_quote($pattern, '#');
        $pattern = '#^' . $pattern . '$#';
        
        return preg_match($pattern, $uri) === 1;
    }
}
