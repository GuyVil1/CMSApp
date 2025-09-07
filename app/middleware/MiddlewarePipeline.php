<?php
declare(strict_types=1);

require_once __DIR__ . '/MiddlewareInterface.php';
require_once __DIR__ . '/RequestInterface.php';
require_once __DIR__ . '/ResponseInterface.php';

/**
 * Pipeline de middleware pour traiter les requêtes
 * Pattern Chain of Responsibility
 */
class MiddlewarePipeline
{
    private array $middlewares = [];
    private array $sortedMiddlewares = [];
    private bool $sorted = false;
    
    /**
     * Ajouter un middleware au pipeline
     */
    public function add(MiddlewareInterface $middleware): self
    {
        $this->middlewares[] = $middleware;
        $this->sorted = false;
        return $this;
    }
    
    /**
     * Traiter une requête à travers le pipeline
     */
    public function handle(RequestInterface $request, callable $finalHandler = null): ResponseInterface
    {
        // Trier les middlewares par priorité si nécessaire
        if (!$this->sorted) {
            $this->sortMiddlewares();
        }
        
        // Créer le stack des middlewares
        $stack = $this->buildStack($finalHandler);
        
        // Exécuter le premier middleware
        return $stack($request);
    }
    
    /**
     * Trier les middlewares par priorité
     */
    private function sortMiddlewares(): void
    {
        usort($this->middlewares, function($a, $b) {
            return $b->getPriority() <=> $a->getPriority();
        });
        
        $this->sortedMiddlewares = $this->middlewares;
        $this->sorted = true;
    }
    
    /**
     * Construire le stack des middlewares
     */
    private function buildStack(callable $finalHandler = null): callable
    {
        $stack = $finalHandler ?? function(RequestInterface $request) {
            return new HttpResponse(404, 'Not Found');
        };
        
        // Construire le stack en ordre inverse
        foreach (array_reverse($this->sortedMiddlewares) as $middleware) {
            $stack = function(RequestInterface $request) use ($middleware, $stack) {
                if ($middleware->canHandle($request)) {
                    return $middleware->handle($request, $stack);
                }
                return $stack($request);
            };
        }
        
        return $stack;
    }
    
    /**
     * Obtenir tous les middlewares
     */
    public function getMiddlewares(): array
    {
        if (!$this->sorted) {
            $this->sortMiddlewares();
        }
        return $this->sortedMiddlewares;
    }
    
    /**
     * Vérifier si le pipeline a des middlewares
     */
    public function hasMiddlewares(): bool
    {
        return !empty($this->middlewares);
    }
    
    /**
     * Vider le pipeline
     */
    public function clear(): void
    {
        $this->middlewares = [];
        $this->sortedMiddlewares = [];
        $this->sorted = false;
    }
}
