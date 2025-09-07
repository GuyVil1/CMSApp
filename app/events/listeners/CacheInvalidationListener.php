<?php
declare(strict_types=1);

require_once __DIR__ . '/../EventListenerInterface.php';
require_once __DIR__ . '/../EventInterface.php';
require_once __DIR__ . '/../ArticleEvents.php';
require_once __DIR__ . '/../../helpers/MemoryCache.php';

/**
 * Listener pour invalider le cache lors des événements d'articles
 */
class CacheInvalidationListener implements EventListenerInterface
{
    private MemoryCache $cache;
    
    public function __construct()
    {
        $this->cache = new MemoryCache();
    }
    
    public function handle(EventInterface $event): void
    {
        switch ($event->getName()) {
            case 'article.created':
            case 'article.updated':
            case 'article.deleted':
                $this->invalidateArticleCache($event);
                break;
        }
    }
    
    public function supports(EventInterface $event): bool
    {
        return in_array($event->getName(), [
            'article.created',
            'article.updated', 
            'article.deleted'
        ]);
    }
    
    public function getPriority(): int
    {
        return 100; // Haute priorité pour le cache
    }
    
    private function invalidateArticleCache(EventInterface $event): void
    {
        $articleId = $event->get('article_id');
        
        // Invalider les caches spécifiques
        MemoryCache::forget("article_{$articleId}");
        MemoryCache::forget("featured_articles");
        MemoryCache::forget("recent_articles");
        
        // Invalider les caches de pagination
        MemoryCache::forgetPattern("articles_category_*");
        MemoryCache::forgetPattern("articles_game_*");
        
        // Log de l'invalidation
        error_log("Cache invalidated for article {$articleId} due to {$event->getName()}");
    }
}
