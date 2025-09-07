<?php
declare(strict_types=1);

/**
 * Configuration du Container de dépendances
 * Définit toutes les liaisons et singletons
 */
class ContainerConfig
{
    private Container $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Enregistrer toutes les dépendances
     */
    public function register(): void
    {
        $this->registerCore();
        $this->registerRepositories();
        $this->registerServices();
        $this->registerControllers();
    }

    /**
     * Enregistrer les classes core (singletons)
     */
    private function registerCore(): void
    {
        // Database en singleton (une seule connexion)
        $this->container->singleton('Database', function() {
            require_once __DIR__ . '/../../core/Database.php';
            return new Database();
        });

        // Auth en singleton
        $this->container->singleton('Auth', function() {
            require_once __DIR__ . '/../../core/Auth.php';
            return new Auth();
        });

        // MemoryCache en singleton
        $this->container->singleton('MemoryCache', function() {
            require_once __DIR__ . '/../helpers/MemoryCache.php';
            return new MemoryCache();
        });
    }

    /**
     * Enregistrer les repositories
     */
    private function registerRepositories(): void
    {
        // Liaisons par interface
        $this->container->bind('ArticleRepositoryInterface', function() {
            require_once __DIR__ . '/../repositories/ArticleRepository.php';
            return new ArticleRepository();
        });

        $this->container->bind('CategoryRepositoryInterface', function() {
            require_once __DIR__ . '/../repositories/CategoryRepository.php';
            return new CategoryRepository();
        });

        $this->container->bind('GameRepositoryInterface', function() {
            require_once __DIR__ . '/../repositories/GameRepository.php';
            return new GameRepository();
        });

        // Liaisons par classe (pour compatibilité)
        $this->container->bind('ArticleRepository', function() {
            require_once __DIR__ . '/../repositories/ArticleRepository.php';
            return new ArticleRepository();
        });

        $this->container->bind('CategoryRepository', function() {
            require_once __DIR__ . '/../repositories/CategoryRepository.php';
            return new CategoryRepository();
        });

        $this->container->bind('GameRepository', function() {
            require_once __DIR__ . '/../repositories/GameRepository.php';
            return new GameRepository();
        });
    }

    /**
     * Enregistrer les services
     */
    private function registerServices(): void
    {
        // Liaisons par interface
        $this->container->singleton('ArticleServiceInterface', function($container) {
            require_once __DIR__ . '/../services/ArticleService.php';
            return new ArticleService();
        });

        $this->container->bind('CategoryServiceInterface', function($container) {
            require_once __DIR__ . '/../services/CategoryService.php';
            return new CategoryService();
        });

        $this->container->bind('GameServiceInterface', function($container) {
            require_once __DIR__ . '/../services/GameService.php';
            return new GameService();
        });

        // Liaisons par classe (pour compatibilité)
        $this->container->singleton('ArticleService', function($container) {
            require_once __DIR__ . '/../services/ArticleService.php';
            return new ArticleService();
        });

        $this->container->bind('CategoryService', function($container) {
            require_once __DIR__ . '/../services/CategoryService.php';
            return new CategoryService();
        });

        $this->container->bind('GameService', function($container) {
            require_once __DIR__ . '/../services/GameService.php';
            return new GameService();
        });
    }

    /**
     * Enregistrer les contrôleurs
     */
    private function registerControllers(): void
    {
        $this->container->bind('HomeController', function($container) {
            require_once __DIR__ . '/../controllers/HomeController.php';
            return new HomeController();
        });

        $this->container->bind('ArticlesController', function($container) {
            require_once __DIR__ . '/../controllers/admin/ArticlesController.php';
            return new ArticlesController();
        });
    }

    /**
     * Créer des alias pour faciliter l'utilisation
     */
    public function registerAliases(): void
    {
        // Alias pour les interfaces
        $this->container->alias('ArticleServiceInterface', 'article.service');
        $this->container->alias('CategoryServiceInterface', 'category.service');
        $this->container->alias('GameServiceInterface', 'game.service');
        
        // Alias pour les classes (compatibilité)
        $this->container->alias('ArticleService', 'article.service.class');
        $this->container->alias('CategoryService', 'category.service.class');
        $this->container->alias('GameService', 'game.service.class');
    }
}
