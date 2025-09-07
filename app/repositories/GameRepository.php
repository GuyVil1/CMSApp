<?php
declare(strict_types=1);

namespace App\Repositories;

require_once __DIR__ . '/../../core/Database.php';
require_once __DIR__ . '/../interfaces/GameRepositoryInterface.php';

use Database;

class GameRepository implements GameRepositoryInterface
{
    private string $table = 'games';

    /**
     * Trouver tous les jeux
     */
    public function findAll(): array
    {
        $sql = "SELECT * FROM {$this->table} ORDER BY title ASC";
        return Database::query($sql);
    }

    /**
     * Trouver un jeu par ID
     */
    public function findById(int $id): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";
        $result = Database::query($sql, [$id]);
        return !empty($result) ? $result[0] : null;
    }

    /**
     * Trouver un jeu par slug
     */
    public function findBySlug(string $slug): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE slug = ?";
        $result = Database::query($sql, [$slug]);
        return !empty($result) ? $result[0] : null;
    }

    /**
     * Trouver les jeux populaires
     */
    public function findPopular(int $limit = 5): array
    {
        $sql = "SELECT g.*, COUNT(a.id) as article_count 
                FROM {$this->table} g 
                LEFT JOIN articles a ON g.id = a.game_id AND a.status = 'published'
                GROUP BY g.id 
                ORDER BY article_count DESC 
                LIMIT ?";
        
        return Database::query($sql, [$limit]);
    }

    /**
     * Rechercher des jeux par nom
     */
    public function searchByName(string $query, int $limit = 10): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE title LIKE ? ORDER BY title ASC LIMIT ?";
        return Database::query($sql, ["%{$query}%", $limit]);
    }
}
