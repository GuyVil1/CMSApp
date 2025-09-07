<?php
declare(strict_types=1);

namespace App\Repositories;

require_once __DIR__ . '/../../core/Database.php';
require_once __DIR__ . '/../interfaces/CategoryRepositoryInterface.php';

use Database;

class CategoryRepository implements CategoryRepositoryInterface
{
    private string $table = 'categories';

    /**
     * Trouver toutes les catégories
     */
    public function findAll(): array
    {
        $sql = "SELECT * FROM {$this->table} ORDER BY name ASC";
        return Database::query($sql);
    }

    /**
     * Trouver une catégorie par ID
     */
    public function findById(int $id): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";
        $result = Database::query($sql, [$id]);
        return !empty($result) ? $result[0] : null;
    }

    /**
     * Trouver une catégorie par slug
     */
    public function findBySlug(string $slug): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE slug = ?";
        $result = Database::query($sql, [$slug]);
        return !empty($result) ? $result[0] : null;
    }

    /**
     * Trouver les catégories populaires
     */
    public function findPopular(int $limit = 5): array
    {
        $sql = "SELECT c.*, COUNT(a.id) as article_count 
                FROM {$this->table} c 
                LEFT JOIN articles a ON c.id = a.category_id AND a.status = 'published'
                GROUP BY c.id 
                ORDER BY article_count DESC 
                LIMIT ?";
        
        return Database::query($sql, [$limit]);
    }
}
