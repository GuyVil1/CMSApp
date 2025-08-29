<?php
declare(strict_types=1);

/**
 * Modèle Hardware - Gestion des plateformes de jeux
 */

require_once __DIR__ . '/../../core/Database.php';

class Hardware
{
    private int $id;
    private string $name;
    private string $slug;
    private string $type;
    private ?string $manufacturer;
    private ?int $releaseYear;
    private ?string $description;
    private bool $isActive;
    private int $sortOrder;
    private string $createdAt;
    private ?string $updatedAt;
    
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
        $this->type = $data['type'] ?? 'console';
        $this->manufacturer = $data['manufacturer'] ?? null;
        $this->releaseYear = isset($data['release_year']) ? (int)$data['release_year'] : null;
        $this->description = $data['description'] ?? null;
        $this->isActive = (bool)($data['is_active'] ?? true);
        $this->sortOrder = (int)($data['sort_order'] ?? 0);
        $this->createdAt = $data['created_at'] ?? '';
        $this->updatedAt = $data['updated_at'] ?? null;
    }
    
    /**
     * Trouver un hardware par ID
     */
    public static function find(int $id): ?self
    {
        $sql = "SELECT * FROM hardware WHERE id = ?";
        $data = Database::queryOne($sql, [$id]);
        
        return $data ? new self($data) : null;
    }
    
    /**
     * Trouver un hardware par slug
     */
    public static function findBySlug(string $slug): ?self
    {
        $sql = "SELECT * FROM hardware WHERE slug = ?";
        $data = Database::queryOne($sql, [$slug]);
        
        return $data ? new self($data) : null;
    }
    
    /**
     * Trouver tous les hardware actifs
     */
    public static function findAllActive(): array
    {
        $sql = "SELECT * FROM hardware WHERE is_active = 1 ORDER BY sort_order ASC, name ASC";
        $results = Database::query($sql);
        
        return array_map(fn($data) => new self($data), $results);
    }
    
    /**
     * Trouver tous les hardware avec pagination
     */
    public static function findAll(int $limit = 20, int $offset = 0): array
    {
        $sql = "SELECT * FROM hardware ORDER BY sort_order ASC, name ASC LIMIT ? OFFSET ?";
        $results = Database::query($sql, [$limit, $offset]);
        
        return array_map(fn($data) => new self($data), $results);
    }
    
    /**
     * Compter le nombre total de hardware
     */
    public static function count(): int
    {
        $sql = "SELECT COUNT(*) as total FROM hardware";
        $result = Database::queryOne($sql);
        
        return (int)($result['total'] ?? 0);
    }
    
    /**
     * Trouver les hardware par type
     */
    public static function findByType(string $type): array
    {
        $sql = "SELECT * FROM hardware WHERE type = ? AND is_active = 1 ORDER BY sort_order ASC, name ASC";
        $results = Database::query($sql, [$type]);
        
        return array_map(fn($data) => new self($data), $results);
    }
    
    /**
     * Créer un nouveau hardware
     */
    public static function create(array $data): ?self
    {
        $sql = "INSERT INTO hardware (name, slug, type, manufacturer, release_year, description, is_active, sort_order) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $params = [
            $data['name'],
            $data['slug'],
            $data['type'],
            $data['manufacturer'] ?? null,
            $data['release_year'] ?? null,
            $data['description'] ?? null,
            $data['is_active'] ?? true,
            $data['sort_order'] ?? 0
        ];
        
        if (Database::execute($sql, $params)) {
            $id = (int)Database::lastInsertId();
            return self::find($id);
        }
        
        return null;
    }
    
    /**
     * Mettre à jour un hardware
     */
    public function update(array $data): bool
    {
        $sql = "UPDATE hardware SET name = ?, slug = ?, type = ?, manufacturer = ?, release_year = ?, 
                       description = ?, is_active = ?, sort_order = ?, updated_at = CURRENT_TIMESTAMP 
                WHERE id = ?";
        
        $params = [
            $data['name'],
            $data['slug'],
            $data['type'],
            $data['manufacturer'] ?? null,
            $data['release_year'] ?? null,
            $data['description'] ?? null,
            $data['is_active'] ?? $this->isActive,
            $data['sort_order'] ?? $this->sortOrder,
            $this->id
        ];
        
        return Database::execute($sql, $params) > 0;
    }
    
    /**
     * Supprimer un hardware
     */
    public function delete(): bool
    {
        // Vérifier s'il y a des jeux associés
        $sql = "SELECT COUNT(*) as total FROM games WHERE hardware_id = ?";
        $result = Database::queryOne($sql, [$this->id]);
        $gameCount = (int)($result['total'] ?? 0);
        
        if ($gameCount > 0) {
            throw new Exception("Impossible de supprimer ce hardware car il est associé à {$gameCount} jeu(x)");
        }
        
        $sql = "DELETE FROM hardware WHERE id = ?";
        return Database::execute($sql, [$this->id]) > 0;
    }
    
    /**
     * Vérifier si un slug existe déjà
     */
    public static function slugExists(string $slug, int $excludeId = 0): bool
    {
        $sql = "SELECT COUNT(*) as total FROM hardware WHERE slug = ? AND id != ?";
        $result = Database::queryOne($sql, [$slug, $excludeId]);
        
        return (int)($result['total'] ?? 0) > 0;
    }
    
    /**
     * Générer un slug à partir d'un nom
     */
    public static function generateSlug(string $name): string
    {
        $slug = strtolower(trim($name));
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
        $slug = preg_replace('/[\s-]+/', '-', $slug);
        $slug = trim($slug, '-');
        
        // Si le slug existe déjà, ajouter un suffixe numérique
        $originalSlug = $slug;
        $counter = 1;
        
        while (self::slugExists($slug)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }
    
    /**
     * Obtenir les types disponibles
     */
    public static function getTypes(): array
    {
        return [
            'console' => 'Console de jeux',
            'pc' => 'Ordinateur personnel',
            'mobile' => 'Mobile/Tablette',
            'other' => 'Autre'
        ];
    }
    
    /**
     * Obtenir le nom du type
     */
    public function getTypeName(): string
    {
        $types = self::getTypes();
        return $types[$this->type] ?? $this->type;
    }
    
    /**
     * Obtenir le nombre de jeux associés
     */
    public function getGamesCount(): int
    {
        $sql = "SELECT COUNT(*) as total FROM games WHERE hardware_id = ?";
        $result = Database::queryOne($sql, [$this->id]);
        
        return (int)($result['total'] ?? 0);
    }
    
    // Getters
    public function getId(): int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getSlug(): string { return $this->slug; }
    public function getType(): string { return $this->type; }
    public function getManufacturer(): ?string { return $this->manufacturer; }
    public function getReleaseYear(): ?int { return $this->releaseYear; }
    public function getDescription(): ?string { return $this->description; }
    public function isActive(): bool { return $this->isActive; }
    public function getSortOrder(): int { return $this->sortOrder; }
    public function getCreatedAt(): string { return $this->createdAt; }
    public function getUpdatedAt(): ?string { return $this->updatedAt; }
    
    /**
     * Convertir en tableau pour l'API
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'type' => $this->type,
            'type_name' => $this->getTypeName(),
            'manufacturer' => $this->manufacturer,
            'release_year' => $this->releaseYear,
            'description' => $this->description,
            'is_active' => $this->isActive,
            'sort_order' => $this->sortOrder,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
            'games_count' => $this->getGamesCount()
        ];
    }
}
