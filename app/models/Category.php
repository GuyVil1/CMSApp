<?php
declare(strict_types=1);

/**
 * Modèle Category - Gestion des catégories d'articles
 */

require_once __DIR__ . '/../../core/Database.php';

class Category
{
    private int $id;
    private string $name;
    private string $slug;
    private ?string $description;
    private string $createdAt;
    
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
        $this->description = $data['description'] ?? null;
        $this->createdAt = $data['created_at'] ?? '';
    }
    
    /**
     * Trouver une catégorie par ID
     */
    public static function find(int $id): ?self
    {
        $sql = "SELECT * FROM categories WHERE id = ?";
        $data = Database::queryOne($sql, [$id]);
        
        return $data ? new self($data) : null;
    }
    
    /**
     * Trouver une catégorie par slug
     */
    public static function findBySlug(string $slug): ?self
    {
        $sql = "SELECT * FROM categories WHERE slug = ?";
        $data = Database::queryOne($sql, [$slug]);
        
        return $data ? new self($data) : null;
    }
    
    /**
     * Trouver toutes les catégories
     */
    public static function findAll(): array
    {
        $sql = "SELECT * FROM categories ORDER BY name ASC";
        $results = Database::query($sql);
        
        return array_map(fn($data) => new self($data), $results);
    }
    
    /**
     * Créer une nouvelle catégorie
     */
    public static function create(array $data): ?self
    {
        $sql = "INSERT INTO categories (name, slug, description) VALUES (?, ?, ?)";
        
        $params = [
            $data['name'],
            $data['slug'],
            $data['description'] ?? null
        ];
        
        if (Database::execute($sql, $params)) {
            $id = Database::lastInsertId();
            return self::find($id);
        }
        
        return null;
    }
    
    /**
     * Mettre à jour une catégorie
     */
    public function update(array $data): bool
    {
        $sql = "UPDATE categories SET name = ?, slug = ?, description = ? WHERE id = ?";
        
        $params = [
            $data['name'] ?? $this->name,
            $data['slug'] ?? $this->slug,
            $data['description'] ?? $this->description,
            $this->id
        ];
        
        return Database::execute($sql, $params) > 0;
    }
    
    /**
     * Supprimer une catégorie
     */
    public function delete(): bool
    {
        // Vérifier s'il y a des articles dans cette catégorie
        $articlesCount = $this->getArticlesCount();
        if ($articlesCount > 0) {
            throw new Exception("Impossible de supprimer cette catégorie car elle contient {$articlesCount} article(s)");
        }
        
        $sql = "DELETE FROM categories WHERE id = ?";
        return Database::execute($sql, [$this->id]) > 0;
    }
    
    /**
     * Obtenir les articles de cette catégorie
     */
    public function getArticles(int $limit = 20, int $offset = 0): array
    {
        $sql = "SELECT a.*, u.login as author_name, m.filename as cover_image 
                FROM articles a 
                LEFT JOIN users u ON a.author_id = u.id 
                LEFT JOIN media m ON a.cover_image_id = m.id 
                WHERE a.category_id = ? AND a.status = 'published' 
                ORDER BY a.published_at DESC 
                LIMIT ? OFFSET ?";
        
        $results = Database::query($sql, [$this->id, $limit, $offset]);
        
        return array_map(fn($data) => new Article($data), $results);
    }
    
    /**
     * Compter les articles de cette catégorie
     */
    public function getArticlesCount(): int
    {
        $sql = "SELECT COUNT(*) as total FROM articles WHERE category_id = ? AND status = 'published'";
        $result = Database::queryOne($sql, [$this->id]);
        
        return (int)($result['total'] ?? 0);
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
        $sql = "SELECT COUNT(*) as total FROM categories WHERE slug = ? AND id != ?";
        $result = Database::queryOne($sql, [$slug, $excludeId]);
        
        return (int)($result['total'] ?? 0) > 0;
    }
    
    // Getters
    public function getId(): int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getSlug(): string { return $this->slug; }
    public function getDescription(): ?string { return $this->description; }
    public function getCreatedAt(): string { return $this->createdAt; }
    
    /**
     * Convertir en tableau pour l'API
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'created_at' => $this->createdAt,
            'articles_count' => $this->getArticlesCount()
        ];
    }
}
