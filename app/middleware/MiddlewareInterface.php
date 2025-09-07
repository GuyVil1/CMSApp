<?php
declare(strict_types=1);

/**
 * Interface pour tous les middlewares
 * Contrat de base pour le pipeline de traitement des requêtes
 */
interface MiddlewareInterface
{
    /**
     * Traiter la requête
     * @param RequestInterface $request La requête à traiter
     * @param callable $next Le prochain middleware dans le pipeline
     * @return ResponseInterface La réponse générée
     */
    public function handle(RequestInterface $request, callable $next): ResponseInterface;
    
    /**
     * Vérifier si le middleware peut traiter cette requête
     * @param RequestInterface $request La requête à vérifier
     * @return bool True si le middleware peut traiter la requête
     */
    public function canHandle(RequestInterface $request): bool;
    
    /**
     * Obtenir la priorité du middleware (plus élevé = traité en premier)
     * @return int La priorité du middleware
     */
    public function getPriority(): int;
}
