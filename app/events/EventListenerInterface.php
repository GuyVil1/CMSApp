<?php
declare(strict_types=1);

/**
 * Interface pour les listeners d'événements
 */
interface EventListenerInterface
{
    /**
     * Traiter un événement
     */
    public function handle(EventInterface $event): void;
    
    /**
     * Vérifier si le listener peut traiter cet événement
     */
    public function supports(EventInterface $event): bool;
    
    /**
     * Obtenir la priorité du listener (plus élevé = traité en premier)
     */
    public function getPriority(): int;
}
