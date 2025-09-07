<?php
declare(strict_types=1);

require_once __DIR__ . '/EventInterface.php';
require_once __DIR__ . '/EventListenerInterface.php';

/**
 * Dispatcher d'événements
 * Gère la distribution des événements aux listeners
 */
class EventDispatcher
{
    private array $listeners = [];
    private array $sortedListeners = [];
    
    /**
     * Ajouter un listener pour un événement
     */
    public function addListener(string $eventName, EventListenerInterface $listener, int $priority = 0): void
    {
        $this->listeners[$eventName][] = [
            'listener' => $listener,
            'priority' => $priority
        ];
        
        // Marquer comme non trié
        unset($this->sortedListeners[$eventName]);
    }
    
    /**
     * Supprimer un listener
     */
    public function removeListener(string $eventName, EventListenerInterface $listener): void
    {
        if (!isset($this->listeners[$eventName])) {
            return;
        }
        
        foreach ($this->listeners[$eventName] as $key => $listenerData) {
            if ($listenerData['listener'] === $listener) {
                unset($this->listeners[$eventName][$key]);
                break;
            }
        }
        
        // Marquer comme non trié
        unset($this->sortedListeners[$eventName]);
    }
    
    /**
     * Dispatcher un événement
     */
    public function dispatch(EventInterface $event): void
    {
        $eventName = $event->getName();
        
        if (!isset($this->listeners[$eventName])) {
            return;
        }
        
        // Trier les listeners par priorité si nécessaire
        if (!isset($this->sortedListeners[$eventName])) {
            $this->sortListeners($eventName);
        }
        
        // Exécuter les listeners
        foreach ($this->sortedListeners[$eventName] as $listenerData) {
            if ($event->isPropagationStopped()) {
                break;
            }
            
            $listener = $listenerData['listener'];
            if ($listener->supports($event)) {
                $listener->handle($event);
            }
        }
    }
    
    /**
     * Trier les listeners par priorité
     */
    private function sortListeners(string $eventName): void
    {
        $listeners = $this->listeners[$eventName];
        
        // Trier par priorité décroissante
        usort($listeners, function($a, $b) {
            return $b['priority'] <=> $a['priority'];
        });
        
        $this->sortedListeners[$eventName] = $listeners;
    }
    
    /**
     * Obtenir tous les listeners pour un événement
     */
    public function getListeners(string $eventName): array
    {
        if (!isset($this->sortedListeners[$eventName])) {
            $this->sortListeners($eventName);
        }
        
        return $this->sortedListeners[$eventName] ?? [];
    }
    
    /**
     * Vérifier si un événement a des listeners
     */
    public function hasListeners(string $eventName): bool
    {
        return !empty($this->listeners[$eventName]);
    }
    
    /**
     * Obtenir tous les noms d'événements
     */
    public function getEventNames(): array
    {
        return array_keys($this->listeners);
    }
}
