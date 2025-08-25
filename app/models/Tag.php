<?php
declare(strict_types=1);

/**
 * Modèle Tag - Gestion des tags d'articles
 */

require_once __DIR__ . '/../../core/Database.php';

class Tag
{
    private int $id;
    private string $name;
    private string $slug;
    
    public function __construct(array $data = [])
    {
        if (!empty($data)) {
            $this->hydrate($data);
        }
    }
    
    private function hydrate(array $data): void
    {
        $this->id = (int)($data['id'] ?? 0);
        $this->name = $data['name'] ?? '';
        $this->slug = $data['slug'] ?? '';
    }
    
    /**
     * Trouver un tag par ID
     */
    public static function find(int $id): ?self
    {
        $sql = "SELECT * FROM tags WHERE id = ?";
        $data = Database::queryOne($sql, [$id]);
        
        return $data ? new self($data) : null;
    }
    
    /**
     * Trouver un tag par slug
     */
    public static function findBySlug(string $slug): ?self
    {
        $sql = "SELECT * FROM tags WHERE slug = ?";
        $data = Database::queryOne($sql, [$slug]);
        
        return $data ? new self($data) : null;
    }
    
    /**
     * Trouver tous les tags
     */
    public static function findAll(): array
    {
        $sql = "SELECT * FROM tags ORDER BY name ASC";
        $results = Database::query($sql);
        
        return array_map(fn($data) => new self($data), $results);
    }
    
    /**
     * Trouver les tags populaires (avec le plus d'articles)
     */
    public static function findPopular(int $limit = 10): array
    {
        $sql = "SELECT t.*, COUNT(at.article_id) as articles_count 
                FROM tags t 
                LEFT JOIN article_tag at ON t.id = at.tag_id 
                LEFT JOIN articles a ON at.article_id = a.id AND a.status = 'published'
                GROUP BY t.id 
                HAVING articles_count > 0 
                ORDER BY articles_count DESC 
                LIMIT ?";
        
        $results = Database::query($sql, [$limit]);
        
        return array_map(fn($data) => new self($data), $results);
    }
    
    /**
     * Créer un nouveau tag
     */
    public static function create(array $data): ?self
    {
        $sql = "INSERT INTO tags (name, slug) VALUES (?, ?)";
        
        $params = [
            $data['name'],
            $data['slug']
        ];
        
        if (Database::execute($sql, $params)) {
            $id = Database::lastInsertId();
            return self::find($id);
        }
        
        return null;
    }
    
    /**
     * Mettre à jour un tag
     */
    public function update(array $data): bool
    {
        $sql = "UPDATE tags SET name = ?, slug = ? WHERE id = ?";
        
        $params = [
            $data['name'] ?? $this->name,
            $data['slug'] ?? $this->slug,
            $this->id
        ];
        
        return Database::execute($sql, $params) > 0;
    }
    
    /**
     * Supprimer un tag
     */
    public function delete(): bool
    {
        // Supprimer d'abord les associations avec les articles
        $sql = "DELETE FROM article_tag WHERE tag_id = ?";
        Database::execute($sql, [$this->id]);
        
        // Puis supprimer le tag
        $sql = "DELETE FROM tags WHERE id = ?";
        return Database::execute($sql, [$this->id]) > 0;
    }
    
    /**
     * Obtenir les articles avec ce tag
     */
    public function getArticles(int $limit = 20, int $offset = 0): array
    {
        $sql = "SELECT a.*, u.login as author_name, m.filename as cover_image 
                FROM articles a 
                INNER JOIN article_tag at ON a.id = at.article_id 
                LEFT JOIN users u ON a.author_id = u.id 
                LEFT JOIN media m ON a.cover_image_id = m.id 
                WHERE at.tag_id = ? AND a.status = 'published' 
                ORDER BY a.published_at DESC 
                LIMIT ? OFFSET ?";
        
        $results = Database::query($sql, [$this->id, $limit, $offset]);
        
        return array_map(fn($data) => new Article($data), $results);
    }
    
    /**
     * Compter les articles avec ce tag
     */
    public function getArticlesCount(): int
    {
        $sql = "SELECT COUNT(*) as total 
                FROM articles a 
                INNER JOIN article_tag at ON a.id = at.article_id 
                WHERE at.tag_id = ? AND a.status = 'published'";
        $result = Database::queryOne($sql, [$this->id]);
        
        return (int)($result['total'] ?? 0);
    }
    
    /**
     * Ajouter ce tag à un article
     */
    public function addToArticle(int $articleId): bool
    {
        // Vérifier si l'association existe déjà
        $sql = "SELECT COUNT(*) as total FROM article_tag WHERE article_id = ? AND tag_id = ?";
        $result = Database::queryOne($sql, [$articleId, $this->id]);
        
        if ((int)($result['total'] ?? 0) > 0) {
            return true; // Déjà associé
        }
        
        $sql = "INSERT INTO article_tag (article_id, tag_id) VALUES (?, ?)";
        return Database::execute($sql, [$articleId, $this->id]) > 0;
    }
    
    /**
     * Retirer ce tag d'un article
     */
    public function removeFromArticle(int $articleId): bool
    {
        $sql = "DELETE FROM article_tag WHERE article_id = ? AND tag_id = ?";
        return Database::execute($sql, [$articleId, $this->id]) > 0;
    }
    
    /**
     * Obtenir les tags d'un article
     */
    public static function getArticleTags(int $articleId): array
    {
        $sql = "SELECT t.* FROM tags t 
                INNER JOIN article_tag at ON t.id = at.tag_id 
                WHERE at.article_id = ? 
                ORDER BY t.name ASC";
        
        $results = Database::query($sql, [$articleId]);
        
        return array_map(fn($data) => new self($data), $results);
    }
    
    /**
     * Ajouter des tags à un article
     */
    public static function addTagsToArticle(int $articleId, array $tagIds): bool
    {
        // Supprimer d'abord les associations existantes
        $sql = "DELETE FROM article_tag WHERE article_id = ?";
        Database::execute($sql, [$articleId]);
        
        // Ajouter les nouvelles associations
        foreach ($tagIds as $tagId) {
            $sql = "INSERT INTO article_tag (article_id, tag_id) VALUES (?, ?)";
            Database::execute($sql, [$articleId, $tagId]);
        }
        
        return true;
    }
    
    /**
     * Rechercher des tags
     */
    public static function search(string $query, int $limit = 20): array
    {
        $sql = "SELECT * FROM tags WHERE name LIKE ? ORDER BY name ASC LIMIT ?";
        $searchTerm = "%{$query}%";
        $results = Database::query($sql, [$searchTerm, $limit]);
        
        return array_map(fn($data) => new self($data), $results);
    }
    
    /**
     * Générer un slug à partir du nom
     */
    public static function generateSlug(string $name): string
    {
        $slug = strtolower(trim($name));
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
        $slug = preg_replace('/[\s-]+/', '-', $slug);
        $slug = trim($slug, '-');
        
        return $slug;
    }
    
    /**
     * Vérifier si un slug existe déjà
     */
    public static function slugExists(string $slug, int $excludeId = 0): bool
    {
        $sql = "SELECT COUNT(*) as total FROM tags WHERE slug = ? AND id != ?";
        $result = Database::queryOne($sql, [$slug, $excludeId]);
        
        return (int)($result['total'] ?? 0) > 0;
    }
    
    // Getters
    public function getId(): int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getSlug(): string { return $this->slug; }
    
    /**
     * Convertir en tableau pour l'API
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'articles_count' => $this->getArticlesCount()
        ];
    }
}
