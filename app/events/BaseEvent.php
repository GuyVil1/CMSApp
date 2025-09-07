<?php
declare(strict_types=1);

/**
 * Classe de base pour tous les événements
 * Implémentation commune de EventInterface
 */
abstract class BaseEvent implements EventInterface
{
    protected string $name;
    protected array $data;
    protected int $timestamp;
    protected bool $propagationStopped = false;
    
    public function __construct(string $name, array $data = [])
    {
        $this->name = $name;
        $this->data = $data;
        $this->timestamp = time();
    }
    
    public function getName(): string
    {
        return $this->name;
    }
    
    public function getData(): array
    {
        return $this->data;
    }
    
    public function getTimestamp(): int
    {
        return $this->timestamp;
    }
    
    public function isPropagationStopped(): bool
    {
        return $this->propagationStopped;
    }
    
    public function stopPropagation(): void
    {
        $this->propagationStopped = true;
    }
    
    /**
     * Obtenir une donnée spécifique
     */
    public function get(string $key, $default = null)
    {
        return $this->data[$key] ?? $default;
    }
    
    /**
     * Définir une donnée
     */
    public function set(string $key, $value): void
    {
        $this->data[$key] = $value;
    }
}
