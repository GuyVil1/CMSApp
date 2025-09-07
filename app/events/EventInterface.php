<?php
declare(strict_types=1);

/**
 * Interface pour tous les événements
 * Contrat de base pour le système d'événements
 */
interface EventInterface
{
    /**
     * Obtenir le nom de l'événement
     */
    public function getName(): string;
    
    /**
     * Obtenir les données de l'événement
     */
    public function getData(): array;
    
    /**
     * Obtenir le timestamp de l'événement
     */
    public function getTimestamp(): int;
    
    /**
     * Vérifier si l'événement peut être propagé
     */
    public function isPropagationStopped(): bool;
    
    /**
     * Arrêter la propagation de l'événement
     */
    public function stopPropagation(): void;
}
