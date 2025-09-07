<?php
declare(strict_types=1);

/**
 * Service Article - Logique métier des articles
 * Séparation des responsabilités : Contrôleur → Service → Repository
 */

require_once __DIR__ . '/../models/Article.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../repositories/ArticleRepository.php';
require_once __DIR__ . '/../helpers/MemoryCache.php';
require_once __DIR__ . '/../../core/Database.php';
require_once __DIR__ . '/../container/ContainerFactory.php';
require_once __DIR__ . '/../interfaces/ArticleServiceInterface.php';
require_once __DIR__ . '/../events/ArticleEvents.php';

class ArticleService implements ArticleServiceInterface
{
    private Article $articleModel;
    private Category $categoryModel;
    private ArticleRepository $articleRepository;
    private $eventDispatcher;
    
    public function __construct()
    {
        $this->articleModel = new Article();
        $this->categoryModel = new Category();
        $this->articleRepository = ContainerFactory::make('ArticleRepository');
        
        // Récupérer l'EventDispatcher depuis le container
        try {
            $this->eventDispatcher = ContainerFactory::make('EventDispatcher');
        } catch (Exception $e) {
            // Fallback si le container n'est pas disponible
            $this->eventDispatcher = null;
        }
    }
    
    /**
     * Récupérer un article par son slug avec cache
     */
    public function getArticleBySlug(string $slug): ?array
    {
        $cacheKey = "article_slug_{$slug}";
        
        return MemoryCache::remember($cacheKey, function() use ($slug) {
            $article = $this->articleModel->findBySlug($slug);
            
            if (!$article) {
                return null;
            }
            
            // Enrichir avec les données de la catégorie
            if ($article['category_id']) {
                $category = $this->categoryModel->findById($article['category_id']);
                $article['category'] = $category;
            }
            
            // Enrichir avec les tags
            $article['tags'] = $this->articleModel->getTags($article['id']);
            
            return $article;
        }, 1800); // Cache 30 minutes
    }
    
    /**
     * Récupérer les articles en vedette avec cache
     */
    public function getFeaturedArticles(int $limit = 3): array
    {
        $cacheKey = "featured_articles_{$limit}";
        
        return MemoryCache::remember($cacheKey, function() use ($limit) {
            $articles = $this->articleRepository->findFeatured($limit);
            
            // Enrichir avec les images de couverture
            foreach ($articles as &$article) {
                if ($article['cover_image_id']) {
                    $article['cover_image'] = $this->getCoverImagePath($article['cover_image_id']);
                } else {
                    $article['cover_image'] = 'default-article.jpg';
                }
            }
            
            return $articles;
        }, 900); // Cache 15 minutes
    }
    
    /**
     * Récupérer le chemin de l'image de couverture
     */
    private function getCoverImagePath(int $imageId): string
    {
        try {
            $sql = "SELECT filename FROM media WHERE id = ?";
            $result = Database::query($sql, [$imageId]);
            
            if (!empty($result)) {
                return $result[0]['filename'];
            }
        } catch (Exception $e) {
            error_log("Erreur récupération image: " . $e->getMessage());
        }
        
        return 'default-article.jpg';
    }
    
    /**
     * Récupérer les articles récents avec pagination
     */
    public function getRecentArticles(int $page = 1, int $limit = 10): array
    {
        $offset = ($page - 1) * $limit;
        $cacheKey = "recent_articles_{$page}_{$limit}";
        
        return MemoryCache::remember($cacheKey, function() use ($page, $limit) {
            $offset = ($page - 1) * $limit;
            $articles = $this->articleRepository->findRecent($limit, $offset);
            
            // Enrichir avec les images de couverture
            foreach ($articles as &$article) {
                if ($article['cover_image_id']) {
                    $article['cover_image'] = $this->getCoverImagePath($article['cover_image_id']);
                } else {
                    $article['cover_image'] = 'default-article.jpg';
                }
            }
            
            return $articles;
        }, 600); // Cache 10 minutes
    }
    
    /**
     * Créer un nouvel article
     */
    public function createArticle(array $data): ?int
    {
        // Validation des données
        $validation = $this->validateArticleData($data);
        if (!$validation['valid']) {
            return null; // Échec de validation
        }
        
        // Préparer les données
        $articleData = $this->prepareArticleData($data);
        
        // Créer l'article
        $articleId = $this->articleModel->create($articleData);
        
        if (!$articleId) {
            return null; // Échec de création
        }
        
        // Gérer les tags
        if (!empty($data['tags'])) {
            $this->articleModel->setTags($articleId, $data['tags']);
        }
        
        // Invalider le cache
        $this->invalidateArticleCache();
        
        // Dispatcher l'événement
        if ($this->eventDispatcher) {
            $event = new ArticleCreatedEvent($articleId, $articleData);
            $this->eventDispatcher->dispatch($event);
        }
        
        return $articleId; // Retourner l'ID de l'article créé
    }
    
    /**
     * Mettre à jour un article
     */
    public function updateArticle(int $id, array $data): bool
    {
        // Vérifier que l'article existe
        $article = $this->articleModel->findById($id);
        if (!$article) {
            return false; // Article non trouvé
        }
        
        // Validation des données
        $validation = $this->validateArticleData($data, $id);
        if (!$validation['valid']) {
            return false; // Échec de validation
        }
        
        // Préparer les données
        $articleData = $this->prepareArticleData($data);
        
        // Mettre à jour l'article
        $success = $this->articleModel->update($id, $articleData);
        
        if (!$success) {
            return false; // Échec de mise à jour
        }
        
        // Gérer les tags
        if (isset($data['tags'])) {
            $this->articleModel->setTags($id, $data['tags']);
        }
        
        // Invalider le cache
        $this->invalidateArticleCache($id);
        
        // Dispatcher l'événement
        if ($this->eventDispatcher) {
            $event = new ArticleUpdatedEvent($id, $article, $articleData);
            $this->eventDispatcher->dispatch($event);
        }
        
        return true; // Succès
    }
    
    /**
     * Supprimer un article
     */
    public function deleteArticle(int $id): bool
    {
        // Vérifier que l'article existe
        $article = $this->articleModel->findById($id);
        if (!$article) {
            return false; // Article non trouvé
        }
        
        // Supprimer l'article
        $success = $this->articleModel->delete($id);
        
        if (!$success) {
            return false; // Échec de suppression
        }
        
        // Invalider le cache
        $this->invalidateArticleCache($id);
        
        // Dispatcher l'événement
        if ($this->eventDispatcher) {
            $event = new ArticleDeletedEvent($id, $article);
            $this->eventDispatcher->dispatch($event);
        }
        
        return true; // Succès
    }
    
    /**
     * Rechercher des articles
     */
    public function searchArticles(string $query, int $page = 1, int $limit = 10): array
    {
        $offset = ($page - 1) * $limit;
        $cacheKey = "search_articles_" . md5($query) . "_{$page}_{$limit}";
        
        return MemoryCache::remember($cacheKey, function() use ($query, $limit, $offset) {
            $articles = $this->articleModel->search($query, $limit, $offset);
            
            // Enrichir chaque article
            foreach ($articles as &$article) {
                // Les articles sont déjà enrichis par le repository avec category_name
            }
            
            return $articles;
        }, 300); // Cache 5 minutes
    }
    
    /**
     * Validation des données d'article
     */
    private function validateArticleData(array $data, ?int $excludeId = null): array
    {
        $errors = [];
        
        // Titre obligatoire
        if (empty($data['title'])) {
            $errors[] = 'Le titre est obligatoire';
        } elseif (strlen($data['title']) > 255) {
            $errors[] = 'Le titre ne peut pas dépasser 255 caractères';
        }
        
        // Slug obligatoire et unique
        if (empty($data['slug'])) {
            $errors[] = 'Le slug est obligatoire';
        } elseif (!$this->articleModel->isSlugUnique($data['slug'], $excludeId)) {
            $errors[] = 'Ce slug est déjà utilisé';
        }
        
        // Contenu obligatoire
        if (empty($data['content'])) {
            $errors[] = 'Le contenu est obligatoire';
        }
        
        // Catégorie valide
        if (!empty($data['category_id'])) {
            $category = $this->categoryModel->findById($data['category_id']);
            if (!$category) {
                $errors[] = 'Catégorie invalide';
            }
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
    
    /**
     * Préparer les données d'article
     */
    private function prepareArticleData(array $data): array
    {
        return [
            'title' => trim($data['title']),
            'slug' => trim($data['slug']),
            'content' => $data['content'],
            'excerpt' => $data['excerpt'] ?? null,
            'cover_image' => $data['cover_image'] ?? null,
            'category_id' => $data['category_id'] ?? null,
            'status' => $data['status'] ?? 'draft',
            'featured' => $data['featured'] ?? false,
            'published_at' => $data['published_at'] ?? null
        ];
    }
    
    /**
     * Invalider le cache des articles
     */
    private function invalidateArticleCache(?int $articleId = null): void
    {
        // Invalider le cache général
        MemoryCache::forget('featured_articles_3');
        MemoryCache::forget('recent_articles_1_10');
        
        // Invalider le cache spécifique si ID fourni
        if ($articleId) {
            $article = $this->articleModel->findById($articleId);
            if ($article) {
                MemoryCache::forget("article_slug_{$article['slug']}");
            }
        }
    }
    
    /**
     * Récupérer un article par son ID avec cache
     */
    public function getArticleById(int $id): ?array
    {
        return $this->cache->remember("article_{$id}", function() use ($id) {
            return $this->articleRepository->findById($id);
        }, 600); // Cache 10 minutes
    }
    
    /**
     * Récupérer les articles par catégorie
     */
    public function getArticlesByCategory(int $categoryId, int $page = 1, int $limit = 10): array
    {
        $offset = ($page - 1) * $limit;
        
        return $this->cache->remember("articles_category_{$categoryId}_page_{$page}_limit_{$limit}", function() use ($categoryId, $limit, $offset) {
            return $this->articleRepository->findByCategory($categoryId, $limit, $offset);
        }, 300); // Cache 5 minutes
    }
    
    /**
     * Récupérer les articles par jeu
     */
    public function getArticlesByGame(int $gameId, int $page = 1, int $limit = 10): array
    {
        $offset = ($page - 1) * $limit;
        
        return $this->cache->remember("articles_game_{$gameId}_page_{$page}_limit_{$limit}", function() use ($gameId, $limit, $offset) {
            return $this->articleRepository->findByGame($gameId, $limit, $offset);
        }, 300); // Cache 5 minutes
    }
}
