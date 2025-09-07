<?php
declare(strict_types=1);

/**
 * Interface pour les requêtes HTTP
 */
interface RequestInterface
{
    /**
     * Obtenir la méthode HTTP
     */
    public function getMethod(): string;
    
    /**
     * Obtenir l'URI de la requête
     */
    public function getUri(): string;
    
    /**
     * Obtenir les paramètres GET
     */
    public function getQueryParams(): array;
    
    /**
     * Obtenir les paramètres POST
     */
    public function getPostParams(): array;
    
    /**
     * Obtenir les headers
     */
    public function getHeaders(): array;
    
    /**
     * Obtenir un header spécifique
     */
    public function getHeader(string $name, string $default = null): ?string;
    
    /**
     * Obtenir l'IP du client
     */
    public function getClientIp(): string;
    
    /**
     * Obtenir le User-Agent
     */
    public function getUserAgent(): ?string;
    
    /**
     * Vérifier si la requête est en POST
     */
    public function isPost(): bool;
    
    /**
     * Vérifier si la requête est en GET
     */
    public function isGet(): bool;
    
    /**
     * Obtenir un paramètre GET
     */
    public function getQueryParam(string $key, $default = null);
    
    /**
     * Obtenir un paramètre POST
     */
    public function getPostParam(string $key, $default = null);
}
