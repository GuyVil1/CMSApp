<?php
declare(strict_types=1);

/**
 * Modèle Article - Belgium Vidéo Gaming
 * Gestion des articles avec mise en avant
 */

require_once __DIR__ . '/../../core/Database.php';

class Article
{
    private int $id;
    private string $title;
    private string $slug;
    private ?string $excerpt;
    private string $content;
    private string $status;
    private ?int $cover_image_id;
    private int $author_id;
    private ?int $category_id;
    private ?int $game_id;
    private ?string $published_at;
    private ?int $featured_position;
    private string $created_at;
    private ?string $updated_at;
    
    // Propriétés liées
    private ?array $author = null;
    private ?array $category = null;
    private ?array $game = null;
    private ?array $cover_image = null;
    private array $tags = [];
    
    public function __construct(array $data = [])
    {
        if (!empty($data)) {
            $this->hydrate($data);
        }
    }
    
    private function hydrate(array $data): void
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }
    
    // Getters
    public function getId(): int { return $this->id; }
    public function getTitle(): string { return $this->title; }
    public function getSlug(): string { return $this->slug; }
    public function getExcerpt(): ?string { return $this->excerpt; }
    public function getContent(): string { return $this->content; }
    public function getStatus(): string { return $this->status; }
    public function getCoverImageId(): ?int { return $this->cover_image_id; }
    public function getAuthorId(): int { return $this->author_id; }
    public function getCategoryId(): ?int { return $this->category_id; }
    public function getGameId(): ?int { return $this->game_id; }
    public function getPublishedAt(): ?string { return $this->published_at; }
    public function getFeaturedPosition(): ?int { return $this->featured_position; }
    public function getCreatedAt(): string { return $this->created_at; }
    public function getUpdatedAt(): ?string { return $this->updated_at; }
    
    // Getters des propriétés liées
    public function getAuthor(): ?array { return $this->author; }
    public function getCategory(): ?array { return $this->category; }
    public function getGame(): ?array { return $this->game; }
    public function getCoverImage(): ?array { return $this->cover_image; }
    public function getTags(): array { return $this->tags; }
    
    // Setters
    public function setTitle(string $title): self { $this->title = $title; return $this; }
    public function setSlug(string $slug): self { $this->slug = $slug; return $this; }
    public function setExcerpt(?string $excerpt): self { $this->excerpt = $excerpt; return $this; }
    public function setContent(string $content): self { $this->content = $content; return $this; }
    public function setStatus(string $status): self { $this->status = $status; return $this; }
    public function setCoverImageId(?int $cover_image_id): self { $this->cover_image_id = $cover_image_id; return $this; }
    public function setAuthorId(int $author_id): self { $this->author_id = $author_id; return $this; }
    public function setCategoryId(?int $category_id): self { $this->category_id = $category_id; return $this; }
    public function setGameId(?int $game_id): self { $this->game_id = $game_id; return $this; }
    public function setPublishedAt(?string $published_at): self { $this->published_at = $published_at; return $this; }
    public function setFeaturedPosition(?int $featured_position): self { $this->featured_position = $featured_position; return $this; }
    
    /**
     * Créer un nouvel article
     */
    public static function create(array $data): ?int
    {
        try {
            $sql = "INSERT INTO articles (title, slug, excerpt, content, status, cover_image_id, author_id, category_id, game_id, published_at, featured_position) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $params = [
                $data['title'],
                $data['slug'],
                $data['excerpt'] ?? null,
                $data['content'],
                $data['status'] ?? 'draft',
                $data['cover_image_id'] ?? null,
                $data['author_id'],
                $data['category_id'] ?? null,
                $data['game_id'] ?? null,
                $data['published_at'] ?? null,
                $data['featured_position'] ?? null
            ];
            
            return Database::execute($sql, $params) ? (int)Database::lastInsertId() : null;
        } catch (Exception $e) {
            error_log("Erreur création article: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Mettre à jour un article
     */
    public function update(array $data): bool
    {
        try {
            $sql = "UPDATE articles SET 
                    title = ?, slug = ?, excerpt = ?, content = ?, status = ?, 
                    cover_image_id = ?, category_id = ?, game_id = ?, published_at = ?, 
                    featured_position = ?, updated_at = CURRENT_TIMESTAMP 
                    WHERE id = ?";
            
            $params = [
                $data['title'],
                $data['slug'],
                $data['excerpt'] ?? null,
                $data['content'],
                $data['status'],
                $data['cover_image_id'] ?? null,
                $data['category_id'] ?? null,
                $data['game_id'] ?? null,
                $data['published_at'] ?? null,
                $data['featured_position'] ?? null,
                $this->id
            ];
            
            return Database::execute($sql, $params);
        } catch (Exception $e) {
            error_log("Erreur mise à jour article: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Supprimer un article
     */
    public function delete(): bool
    {
        try {
            // Supprimer d'abord les associations avec les tags
            Database::execute("DELETE FROM article_tag WHERE article_id = ?", [$this->id]);
            
            // Supprimer l'article
            return Database::execute("DELETE FROM articles WHERE id = ?", [$this->id]);
        } catch (Exception $e) {
            error_log("Erreur suppression article: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Trouver un article par ID
     */
    public static function findById(int $id): ?self
    {
        try {
            $sql = "SELECT a.*, 
                           u.login as author_login, u.email as author_email,
                           c.name as category_name, c.slug as category_slug,
                           g.title as game_title, g.slug as game_slug,
                           m.filename as cover_filename, m.original_name as cover_original_name
                    FROM articles a
                    LEFT JOIN users u ON a.author_id = u.id
                    LEFT JOIN categories c ON a.category_id = c.id
                    LEFT JOIN games g ON a.game_id = g.id
                    LEFT JOIN media m ON a.cover_image_id = m.id
                    WHERE a.id = ?";
            
            $article = Database::queryOne($sql, [$id]);
            if (!$article) return null;
            
            $instance = new self($article);
            $instance->loadTags();
            return $instance;
        } catch (Exception $e) {
            error_log("Erreur recherche article: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Trouver un article par slug
     */
    public static function findBySlug(string $slug): ?self
    {
        try {
            $sql = "SELECT a.*, 
                           u.login as author_login, u.email as author_email,
                           c.name as category_name, c.slug as category_slug,
                           g.title as game_title, g.slug as game_slug,
                           m.filename as cover_filename, m.original_name as cover_original_name
                    FROM articles a
                    LEFT JOIN users u ON a.author_id = u.id
                    LEFT JOIN categories c ON a.category_id = c.id
                    LEFT JOIN games g ON a.game_id = g.id
                    LEFT JOIN media m ON a.cover_image_id = m.id
                    WHERE a.slug = ?";
            
            $article = Database::queryOne($sql, [$slug]);
            if (!$article) return null;
            
            $instance = new self($article);
            $instance->loadTags();
            return $instance;
        } catch (Exception $e) {
            error_log("Erreur recherche article par slug: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Lister tous les articles avec pagination
     */
    public static function findAll(int $page = 1, int $perPage = 20, array $filters = []): array
    {
        try {
            $where = "WHERE 1=1";
            $params = [];
            
            // Filtres
            if (!empty($filters['status'])) {
                $where .= " AND a.status = ?";
                $params[] = $filters['status'];
            }
            
            if (!empty($filters['category_id'])) {
                $where .= " AND a.category_id = ?";
                $params[] = $filters['category_id'];
            }
            
            if (!empty($filters['author_id'])) {
                $where .= " AND a.author_id = ?";
                $params[] = $filters['author_id'];
            }
            
            if (!empty($filters['search'])) {
                $where .= " AND (a.title LIKE ? OR a.excerpt LIKE ? OR a.content LIKE ?)";
                $searchTerm = "%{$filters['search']}%";
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }
            
            // Compter le total
            $countSql = "SELECT COUNT(*) as total FROM articles a $where";
            $total = Database::queryOne($countSql, $params)['total'];
            
            // Pagination
            $offset = ($page - 1) * $perPage;
            $sql = "SELECT a.*, 
                           u.login as author_login,
                           c.name as category_name,
                           g.title as game_title,
                           m.filename as cover_filename
                    FROM articles a
                    LEFT JOIN users u ON a.author_id = u.id
                    LEFT JOIN categories c ON a.category_id = c.id
                    LEFT JOIN games g ON a.game_id = g.id
                    LEFT JOIN media m ON a.cover_image_id = m.id
                    $where
                    ORDER BY a.created_at DESC
                    LIMIT ? OFFSET ?";
            
            $params[] = $perPage;
            $params[] = $offset;
            
            $articles = Database::query($sql, $params);
            
            return [
                'articles' => $articles,
                'total' => $total,
                'pages' => ceil($total / $perPage),
                'current_page' => $page
            ];
        } catch (Exception $e) {
            error_log("Erreur liste articles: " . $e->getMessage());
            return ['articles' => [], 'total' => 0, 'pages' => 0, 'current_page' => 1];
        }
    }
    
    /**
     * Archiver un article
     */
    public function archive(): bool
    {
        $this->status = 'archived';
        
        return $this->update(['status' => $this->status]);
    }
    
    /**
     * Vérifier si une position en avant est disponible
     */
    public static function isPositionAvailable(int $position, ?int $excludeId = null): bool
    {
        try {
            $sql = "SELECT COUNT(*) as count FROM articles WHERE featured_position = ?";
            $params = [$position];
            
            if ($excludeId) {
                $sql .= " AND id != ?";
                $params[] = $excludeId;
            }
            
            $result = Database::queryOne($sql, $params);
            return $result['count'] == 0;
        } catch (Exception $e) {
            error_log("Erreur vérification position: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Libérer une position en avant
     */
    public static function freePosition(int $position): bool
    {
        try {
            $sql = "UPDATE articles SET featured_position = NULL WHERE featured_position = ?";
            return Database::execute($sql, [$position]);
        } catch (Exception $e) {
            error_log("Erreur libération position: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtenir les articles en avant
     */
    public static function getFeaturedArticles(int $limit = 6): array
    {
        try {
            $sql = "SELECT a.*, u.login as author_name, c.name as category_name, m.filename as cover_image 
                    FROM articles a 
                    LEFT JOIN users u ON a.author_id = u.id 
                    LEFT JOIN categories c ON a.category_id = c.id 
                    LEFT JOIN media m ON a.cover_image_id = m.id 
                    WHERE a.featured_position IS NOT NULL 
                    AND a.status = 'published' 
                    ORDER BY a.featured_position ASC 
                    LIMIT ?";
            
            $results = Database::query($sql, [$limit]);
            
            return array_map(fn($data) => new self($data), $results);
        } catch (Exception $e) {
            error_log("Erreur récupération articles en avant: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Charger les tags de l'article
     */
    private function loadTags(): void
    {
        try {
            $sql = "SELECT t.* FROM tags t
                    INNER JOIN article_tag at ON t.id = at.tag_id
                    WHERE at.article_id = ?";
            
            $this->tags = Database::query($sql, [$this->id]);
        } catch (Exception $e) {
            error_log("Erreur chargement tags: " . $e->getMessage());
            $this->tags = [];
        }
    }
    
    /**
     * Ajouter des tags à l'article
     */
    public function addTags(array $tagIds): bool
    {
        try {
            // Supprimer les anciens tags
            Database::execute("DELETE FROM article_tag WHERE article_id = ?", [$this->id]);
            
            // Ajouter les nouveaux tags
            foreach ($tagIds as $tagId) {
                Database::execute(
                    "INSERT INTO article_tag (article_id, tag_id) VALUES (?, ?)",
                    [$this->id, $tagId]
                );
            }
            
            return true;
        } catch (Exception $e) {
            error_log("Erreur ajout tags: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Générer un slug unique
     */
    public static function generateSlug(string $title, ?int $excludeId = null): string
    {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
        $slug = trim($slug, '-');
        
        $baseSlug = $slug;
        $counter = 1;
        
        while (!self::isSlugAvailable($slug, $excludeId)) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }
    
    /**
     * Vérifier si un slug est disponible
     */
    private static function isSlugAvailable(string $slug, ?int $excludeId = null): bool
    {
        try {
            $sql = "SELECT COUNT(*) as count FROM articles WHERE slug = ?";
            $params = [$slug];
            
            if ($excludeId) {
                $sql .= " AND id != ?";
                $params[] = $excludeId;
            }
            
            $result = Database::queryOne($sql, $params);
            return $result['count'] == 0;
        } catch (Exception $e) {
            error_log("Erreur vérification slug: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Publier un article
     */
    public function publish(): bool
    {
        $this->status = 'published';
        $this->published_at = date('Y-m-d H:i:s');
        
        return $this->update([
            'status' => $this->status,
            'published_at' => $this->published_at
        ]);
    }
    
    /**
     * Mettre en brouillon
     */
    public function draft(): bool
    {
        $this->status = 'draft';
        $this->published_at = null;
        
        return $this->update([
            'status' => $this->status,
            'published_at' => $this->published_at
        ]);
    }
    
    /**
     * Obtenir l'URL de l'image de couverture
     */
    public function getCoverImageUrl(): ?string
    {
        if (!$this->cover_image_id) {
            return null;
        }
        
        $coverImage = Media::find($this->cover_image_id);
        return $coverImage ? $coverImage->getUrl() : null;
    }
}
