<?php
declare(strict_types=1);

/**
 * Repository Article - Accès aux données des articles
 * Pattern Repository pour séparer la logique d'accès aux données
 */

require_once __DIR__ . '/../../core/Database.php';

class ArticleRepository
{
    private string $table = 'articles';
    
    /**
     * Trouver un article par son slug
     */
    public function findBySlug(string $slug): ?array
    {
        $sql = "SELECT a.*, c.name as category_name 
                FROM {$this->table} a 
                LEFT JOIN categories c ON a.category_id = c.id 
                WHERE a.slug = ? AND a.status = 'published'";
        
        $result = Database::query($sql, [$slug]);
        return !empty($result) ? $result[0] : null;
    }
    
    /**
     * Trouver un article par son ID
     */
    public function findById(int $id): ?array
    {
        $sql = "SELECT a.*, c.name as category_name 
                FROM {$this->table} a 
                LEFT JOIN categories c ON a.category_id = c.id 
                WHERE a.id = ?";
        
        $result = Database::query($sql, [$id]);
        return !empty($result) ? $result[0] : null;
    }
    
    /**
     * Trouver les articles en vedette
     */
    public function findFeatured(int $limit = 3): array
    {
        $sql = "SELECT a.*, c.name as category_name 
                FROM {$this->table} a 
                LEFT JOIN categories c ON a.category_id = c.id 
                WHERE a.featured_position > 0 AND a.status = 'published' 
                ORDER BY a.featured_position ASC, a.published_at DESC 
                LIMIT ?";
        
        return Database::query($sql, [$limit]);
    }
    
    /**
     * Trouver les articles récents
     */
    public function findRecent(int $limit = 10, int $offset = 0): array
    {
        $sql = "SELECT a.*, c.name as category_name 
                FROM {$this->table} a 
                LEFT JOIN categories c ON a.category_id = c.id 
                WHERE a.status = 'published' 
                ORDER BY a.published_at DESC 
                LIMIT ? OFFSET ?";
        
        return Database::query($sql, [$limit, $offset]);
    }
    
    /**
     * Rechercher des articles
     */
    public function search(string $query, int $limit = 10, int $offset = 0): array
    {
        $searchTerm = "%{$query}%";
        $sql = "SELECT a.*, c.name as category_name 
                FROM {$this->table} a 
                LEFT JOIN categories c ON a.category_id = c.id 
                WHERE a.status = 'published' 
                AND (a.title LIKE ? OR a.content LIKE ? OR a.excerpt LIKE ?) 
                ORDER BY a.published_at DESC 
                LIMIT ? OFFSET ?";
        
        return Database::query($sql, [$searchTerm, $searchTerm, $searchTerm, $limit, $offset]);
    }
    
    /**
     * Créer un nouvel article
     */
    public function create(array $data): ?int
    {
        $sql = "INSERT INTO {$this->table} 
                (title, slug, content, excerpt, cover_image, category_id, status, featured, published_at, created_at, updated_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
        
        $params = [
            $data['title'],
            $data['slug'],
            $data['content'],
            $data['excerpt'],
            $data['cover_image'],
            $data['category_id'],
            $data['status'],
            $data['featured'] ? 1 : 0,
            $data['published_at']
        ];
        
        $result = Database::execute($sql, $params);
        return $result ? Database::getLastInsertId() : null;
    }
    
    /**
     * Mettre à jour un article
     */
    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE {$this->table} 
                SET title = ?, slug = ?, content = ?, excerpt = ?, cover_image = ?, 
                    category_id = ?, status = ?, featured = ?, published_at = ?, updated_at = NOW() 
                WHERE id = ?";
        
        $params = [
            $data['title'],
            $data['slug'],
            $data['content'],
            $data['excerpt'],
            $data['cover_image'],
            $data['category_id'],
            $data['status'],
            $data['featured'] ? 1 : 0,
            $data['published_at'],
            $id
        ];
        
        return Database::execute($sql, $params) !== false;
    }
    
    /**
     * Supprimer un article
     */
    public function delete(int $id): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        return Database::execute($sql, [$id]) !== false;
    }
    
    /**
     * Vérifier l'unicité du slug
     */
    public function isSlugUnique(string $slug, ?int $excludeId = null): bool
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE slug = ?";
        $params = [$slug];
        
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $result = Database::query($sql, $params);
        return $result[0]['count'] == 0;
    }
    
    /**
     * Compter les articles
     */
    public function count(array $filters = []): int
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table}";
        $params = [];
        $conditions = [];
        
        if (!empty($filters['status'])) {
            $conditions[] = "status = ?";
            $params[] = $filters['status'];
        }
        
        if (!empty($filters['category_id'])) {
            $conditions[] = "category_id = ?";
            $params[] = $filters['category_id'];
        }
        
        if (!empty($filters['featured'])) {
            $conditions[] = "featured = ?";
            $params[] = $filters['featured'] ? 1 : 0;
        }
        
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }
        
        $result = Database::query($sql, $params);
        return (int)$result[0]['count'];
    }
    
    /**
     * Trouver les articles par catégorie
     */
    public function findByCategory(int $categoryId, int $limit = 10, int $offset = 0): array
    {
        $sql = "SELECT a.*, c.name as category_name 
                FROM {$this->table} a 
                LEFT JOIN categories c ON a.category_id = c.id 
                WHERE a.category_id = ? AND a.status = 'published' 
                ORDER BY a.published_at DESC 
                LIMIT ? OFFSET ?";
        
        return Database::query($sql, [$categoryId, $limit, $offset]);
    }
    
    /**
     * Trouver les articles par tag
     */
    public function findByTag(string $tag, int $limit = 10, int $offset = 0): array
    {
        $sql = "SELECT DISTINCT a.*, c.name as category_name 
                FROM {$this->table} a 
                LEFT JOIN categories c ON a.category_id = c.id 
                INNER JOIN article_tags at ON a.id = at.article_id 
                INNER JOIN tags t ON at.tag_id = t.id 
                WHERE t.name = ? AND a.status = 'published' 
                ORDER BY a.published_at DESC 
                LIMIT ? OFFSET ?";
        
        return Database::query($sql, [$tag, $limit, $offset]);
    }
    
    /**
     * Obtenir les statistiques des articles
     */
    public function getStats(): array
    {
        $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'published' THEN 1 ELSE 0 END) as published,
                    SUM(CASE WHEN status = 'draft' THEN 1 ELSE 0 END) as draft,
                    SUM(CASE WHEN featured = 1 THEN 1 ELSE 0 END) as featured
                FROM {$this->table}";
        
        $result = Database::query($sql);
        return $result[0];
    }
}
