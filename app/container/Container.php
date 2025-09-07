<?php
declare(strict_types=1);

/**
 * Container de dépendances - Injection de dépendances
 * Transforme notre 2CV en Ferrari architecturale !
 */
class Container
{
    private array $bindings = [];
    private array $instances = [];
    private array $aliases = [];

    /**
     * Enregistrer une liaison
     */
    public function bind(string $abstract, $concrete = null, bool $shared = false): void
    {
        $this->bindings[$abstract] = [
            'concrete' => $concrete ?: $abstract,
            'shared' => $shared
        ];
    }

    /**
     * Enregistrer un singleton
     */
    public function singleton(string $abstract, $concrete = null): void
    {
        $this->bind($abstract, $concrete, true);
    }

    /**
     * Créer une instance
     */
    public function make(string $abstract, array $parameters = [])
    {
        $abstract = $this->getAlias($abstract);

        // Si c'est un singleton et qu'on a déjà une instance
        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }

        $concrete = $this->getConcrete($abstract);
        $object = $this->build($concrete, $parameters);

        // Si c'est un singleton, on le stocke
        if ($this->isShared($abstract)) {
            $this->instances[$abstract] = $object;
        }

        return $object;
    }

    /**
     * Vérifier si une classe est enregistrée
     */
    public function bound(string $abstract): bool
    {
        return isset($this->bindings[$abstract]) || isset($this->instances[$abstract]);
    }

    /**
     * Créer un alias
     */
    public function alias(string $abstract, string $alias): void
    {
        $this->aliases[$alias] = $abstract;
    }

    /**
     * Obtenir l'alias d'une classe
     */
    private function getAlias(string $abstract): string
    {
        return $this->aliases[$abstract] ?? $abstract;
    }

    /**
     * Obtenir la classe concrète
     */
    private function getConcrete(string $abstract)
    {
        if (isset($this->bindings[$abstract])) {
            return $this->bindings[$abstract]['concrete'];
        }

        return $abstract;
    }

    /**
     * Vérifier si c'est un singleton
     */
    private function isShared(string $abstract): bool
    {
        return isset($this->bindings[$abstract]) && $this->bindings[$abstract]['shared'];
    }

    /**
     * Construire une instance avec injection de dépendances
     */
    private function build($concrete, array $parameters = [])
    {
        // Si c'est une closure
        if ($concrete instanceof Closure) {
            return $concrete($this, $parameters);
        }

        // Si c'est une classe
        if (class_exists($concrete)) {
            return $this->buildClass($concrete, $parameters);
        }

        throw new Exception("Classe {$concrete} non trouvée");
    }

    /**
     * Construire une classe avec injection automatique
     */
    private function buildClass(string $className, array $parameters = [])
    {
        $reflection = new ReflectionClass($className);
        
        // Si pas de constructeur, on crée directement
        if (!$reflection->hasMethod('__construct')) {
            return new $className();
        }

        $constructor = $reflection->getMethod('__construct');
        $dependencies = $this->resolveDependencies($constructor->getParameters(), $parameters);

        return $reflection->newInstanceArgs($dependencies);
    }

    /**
     * Résoudre les dépendances d'un constructeur
     */
    private function resolveDependencies(array $parameters, array $primitives = []): array
    {
        $dependencies = [];

        foreach ($parameters as $parameter) {
            $dependency = $parameter->getType();

            if ($dependency && !$dependency->isBuiltin()) {
                // Injection de dépendance automatique
                $dependencies[] = $this->make($dependency->getName());
            } elseif (array_key_exists($parameter->getName(), $primitives)) {
                // Paramètre primitif fourni
                $dependencies[] = $primitives[$parameter->getName()];
            } elseif ($parameter->isDefaultValueAvailable()) {
                // Valeur par défaut
                $dependencies[] = $parameter->getDefaultValue();
            } else {
                throw new Exception("Impossible de résoudre la dépendance {$parameter->getName()}");
            }
        }

        return $dependencies;
    }

    /**
     * Obtenir les statistiques du container
     */
    public function getStats(): array
    {
        return [
            'bindings' => count($this->bindings),
            'instances' => count($this->instances),
            'aliases' => count($this->aliases),
            'memory_usage' => memory_get_usage(true)
        ];
    }
}
