<?php
declare(strict_types=1);

/**
 * Interface pour les réponses HTTP
 */
interface ResponseInterface
{
    /**
     * Obtenir le code de statut HTTP
     */
    public function getStatusCode(): int;
    
    /**
     * Obtenir le contenu de la réponse
     */
    public function getContent(): string;
    
    /**
     * Obtenir les headers de la réponse
     */
    public function getHeaders(): array;
    
    /**
     * Obtenir un header spécifique
     */
    public function getHeader(string $name): ?string;
    
    /**
     * Définir le code de statut
     */
    public function setStatusCode(int $code): void;
    
    /**
     * Définir le contenu
     */
    public function setContent(string $content): void;
    
    /**
     * Ajouter un header
     */
    public function addHeader(string $name, string $value): void;
    
    /**
     * Vérifier si la réponse est une redirection
     */
    public function isRedirect(): bool;
    
    /**
     * Obtenir l'URL de redirection
     */
    public function getRedirectUrl(): ?string;
}
