<?php
declare(strict_types=1);

/**
 * Modèle Genre - Gestion des genres de jeux
 */

require_once __DIR__ . '/../../core/Database.php';

class Genre
{
    private int $id;
    private string $name;
    private ?string $description;
    private string $color;
    private string $createdAt;
    private string $updatedAt;
    
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
        $this->description = $data['description'] ?? null;
        $this->color = $data['color'] ?? '#007bff';
        $this->createdAt = $data['created_at'] ?? '';
        $this->updatedAt = $data['updated_at'] ?? '';
    }
    
    // Getters
    public function getId(): int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getDescription(): ?string { return $this->description; }
    public function getColor(): string { return $this->color; }
    public function getCreatedAt(): string { return $this->createdAt; }
    public function getUpdatedAt(): string { return $this->updatedAt; }
    
    /**
     * Récupérer tous les genres
     */
    public static function findAll(): array
    {
        $sql = "SELECT * FROM genres ORDER BY name ASC";
        $results = Database::query($sql);
        
        return array_map(fn($data) => new self($data), $results);
    }
    
    /**
     * Récupérer un genre par ID
     */
    public static function find(int $id): ?self
    {
        $sql = "SELECT * FROM genres WHERE id = ?";
        $data = Database::queryOne($sql, [$id]);
        
        return $data ? new self($data) : null;
    }
    
    /**
     * Créer un nouveau genre
     */
    public static function create(array $data): ?self
    {
        $sql = "INSERT INTO genres (name, description, color) VALUES (?, ?, ?)";
        
        if (Database::execute($sql, [$data['name'], $data['description'], $data['color']])) {
            $id = Database::lastInsertId();
            return self::find($id);
        }
        
        return null;
    }
    
    /**
     * Mettre à jour un genre
     */
    public function update(array $data): bool
    {
        $sql = "UPDATE genres SET name = ?, description = ?, color = ? WHERE id = ?";
        
        return Database::execute($sql, [
            $data['name'] ?? $this->name,
            $data['description'] ?? $this->description,
            $data['color'] ?? $this->color,
            $this->id
        ]) > 0;
    }
    
    /**
     * Supprimer un genre
     */
    public function delete(): bool
    {
        // Vérifier si le genre est utilisé par des jeux
        $sql = "SELECT COUNT(*) as total FROM games WHERE genre_id = ?";
        $result = Database::queryOne($sql, [$this->id]);
        $count = (int)($result['total'] ?? 0);
        
        if ($count > 0) {
            throw new Exception("Ce genre ne peut pas être supprimé car il est utilisé par " . $count . " jeu(x)");
        }
        
        $sql = "DELETE FROM genres WHERE id = ?";
        return Database::execute($sql, [$this->id]) > 0;
    }
    
    /**
     * Récupérer les genres avec le nombre de jeux associés
     */
    public static function findAllWithGameCount(): array
    {
        $sql = "SELECT g.*, COUNT(ga.id) as game_count 
                FROM genres g 
                LEFT JOIN games ga ON g.id = ga.genre_id 
                GROUP BY g.id 
                ORDER BY g.name ASC";
        
        return Database::query($sql);
    }
    
    /**
     * Rechercher des genres par nom
     */
    public static function search(string $query): array
    {
        $sql = "SELECT * FROM genres WHERE name LIKE ? ORDER BY name ASC";
        $results = Database::query($sql, ['%' . $query . '%']);
        
        return array_map(fn($data) => new self($data), $results);
    }
    
    /**
     * Convertir en tableau pour l'API
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'color' => $this->color,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt
        ];
    }
}
