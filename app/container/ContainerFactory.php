<?php
declare(strict_types=1);

require_once __DIR__ . '/Container.php';
require_once __DIR__ . '/ContainerConfig.php';

/**
 * Factory pour créer et configurer le Container
 * Point d'entrée unique pour l'injection de dépendances
 */
class ContainerFactory
{
    private static ?Container $container = null;

    /**
     * Obtenir l'instance du container (singleton)
     */
    public static function getContainer(): Container
    {
        if (self::$container === null) {
            self::$container = self::createContainer();
        }

        return self::$container;
    }

    /**
     * Créer et configurer le container
     */
    private static function createContainer(): Container
    {
        $container = new Container();
        
        // Configurer le container
        $config = new ContainerConfig($container);
        $config->register();
        $config->registerAliases();

        return $container;
    }

    /**
     * Créer une instance d'une classe via le container
     */
    public static function make(string $abstract, array $parameters = [])
    {
        return self::getContainer()->make($abstract, $parameters);
    }

    /**
     * Vérifier si une classe est enregistrée
     */
    public static function bound(string $abstract): bool
    {
        return self::getContainer()->bound($abstract);
    }

    /**
     * Obtenir les statistiques du container
     */
    public static function getStats(): array
    {
        return self::getContainer()->getStats();
    }

    /**
     * Réinitialiser le container (pour les tests)
     */
    public static function reset(): void
    {
        self::$container = null;
    }
}
