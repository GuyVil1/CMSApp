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
        $this->registerEventSystem();
        $this->registerMiddlewareSystem();
        $this->registerControllers();
        $this->registerMonitoring();
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
     * Enregistrer le système d'événements
     */
    private function registerEventSystem(): void
    {
        // EventDispatcher
        $this->container->singleton('EventDispatcher', function($container) {
            require_once __DIR__ . '/../events/EventDispatcher.php';
            $dispatcher = new EventDispatcher();
            
            // Enregistrer les listeners
            $this->registerEventListeners($dispatcher);
            
            return $dispatcher;
        });
    }
    
    /**
     * Enregistrer le système de middleware
     */
    private function registerMiddlewareSystem(): void
    {
        // MiddlewarePipeline
        $this->container->singleton('MiddlewarePipeline', function($container) {
            require_once __DIR__ . '/../middleware/MiddlewarePipeline.php';
            $pipeline = new MiddlewarePipeline();
            
            // Enregistrer les middlewares
            $this->registerMiddlewares($pipeline);
            
            return $pipeline;
        });
    }
    
    /**
     * Enregistrer les middlewares
     */
    private function registerMiddlewares(MiddlewarePipeline $pipeline): void
    {
        // Security Middleware
        require_once __DIR__ . '/../middleware/middlewares/SecurityMiddleware.php';
        $securityMiddleware = new SecurityMiddleware();
        $pipeline->add($securityMiddleware);
        
        // Rate Limit Middleware
        require_once __DIR__ . '/../middleware/middlewares/RateLimitMiddleware.php';
        $rateLimitMiddleware = new RateLimitMiddleware(100, 3600, ['/api/*', '/assets/*']);
        $pipeline->add($rateLimitMiddleware);
        
        // Authentication Middleware
        require_once __DIR__ . '/../middleware/middlewares/AuthenticationMiddleware.php';
        $authMiddleware = new AuthenticationMiddleware(
            ['/admin/*'], // Routes protégées
            ['/auth/*', '/api/public/*'] // Routes publiques
        );
        $pipeline->add($authMiddleware);
        
        // Validation Middleware
        require_once __DIR__ . '/../middleware/middlewares/ValidationMiddleware.php';
        $validationRules = [
            '/admin/articles/store' => [
                'title' => ['required' => true, 'min_length' => 3, 'max_length' => 255],
                'content' => ['required' => true, 'min_length' => 10],
                'status' => ['required' => true, 'in' => ['draft', 'published']]
            ],
            '/auth/login' => [
                'login' => ['required' => true, 'min_length' => 3],
                'password' => ['required' => true, 'min_length' => 6]
            ]
        ];
        $validationMiddleware = new ValidationMiddleware($validationRules, ['/api/*', '/assets/*', '/admin/articles/*', '/admin/settings/*']);
        $pipeline->add($validationMiddleware);
        
        // Logging Middleware
        require_once __DIR__ . '/../middleware/middlewares/LoggingMiddleware.php';
        $loggingMiddleware = new LoggingMiddleware(
            null, // Fichier par défaut
            ['/assets/*', '/favicon.ico'], // Routes exclues
            false // Ne pas logger les données POST
        );
        $pipeline->add($loggingMiddleware);
        
        // Monitoring Middleware
        require_once __DIR__ . '/../middleware/middlewares/MonitoringMiddleware.php';
        $monitoringMiddleware = new MonitoringMiddleware();
        $pipeline->add($monitoringMiddleware);
    }
    
    /**
     * Enregistrer les listeners d'événements
     */
    private function registerEventListeners(EventDispatcher $dispatcher): void
    {
        // Cache Invalidation Listener
        require_once __DIR__ . '/../events/listeners/CacheInvalidationListener.php';
        $cacheListener = new CacheInvalidationListener();
        $dispatcher->addListener('article.created', $cacheListener, 100);
        $dispatcher->addListener('article.updated', $cacheListener, 100);
        $dispatcher->addListener('article.deleted', $cacheListener, 100);
        
        // Logging Listener
        require_once __DIR__ . '/../events/listeners/LoggingListener.php';
        $loggingListener = new LoggingListener();
        $dispatcher->addListener('article.created', $loggingListener, 50);
        $dispatcher->addListener('article.updated', $loggingListener, 50);
        $dispatcher->addListener('article.deleted', $loggingListener, 50);
        $dispatcher->addListener('article.viewed', $loggingListener, 50);
        $dispatcher->addListener('user.logged_in', $loggingListener, 50);
        $dispatcher->addListener('user.logged_out', $loggingListener, 50);
        $dispatcher->addListener('user.registered', $loggingListener, 50);
        
        // Notification Listener
        require_once __DIR__ . '/../events/listeners/NotificationListener.php';
        $notificationListener = new NotificationListener();
        $dispatcher->addListener('article.created', $notificationListener, 10);
        $dispatcher->addListener('article.updated', $notificationListener, 10);
        $dispatcher->addListener('user.registered', $notificationListener, 10);
        
        // Analytics Listener
        require_once __DIR__ . '/../events/listeners/AnalyticsListener.php';
        $analyticsListener = new AnalyticsListener();
        $dispatcher->addListener('article.viewed', $analyticsListener, 5);
        $dispatcher->addListener('user.logged_in', $analyticsListener, 5);
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

        $this->container->bind('Admin\\MonitoringController', function($container) {
            require_once __DIR__ . '/../controllers/admin/MonitoringController.php';
            return new \Admin\MonitoringController();
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

    /**
     * Enregistrer le système de monitoring
     */
    private function registerMonitoring(): void
    {
        // MetricsCollector en singleton
        $this->container->singleton('MetricsCollector', function() {
            require_once __DIR__ . '/../monitoring/MetricsCollector.php';
            return new MetricsCollector();
        });

        // MonitoringMiddleware
        $this->container->bind('MonitoringMiddleware', function() {
            require_once __DIR__ . '/../middleware/middlewares/MonitoringMiddleware.php';
            return new MonitoringMiddleware();
        });
    }
}
