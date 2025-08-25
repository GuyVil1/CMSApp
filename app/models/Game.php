<?php
declare(strict_types=1);

/**
 * Modèle Game - Gestion des jeux vidéo
 */

require_once __DIR__ . '/../../core/Database.php';

class Game
{
    private int $id;
    private string $title;
    private string $slug;
    private ?string $description;
    private ?string $platform;
    private ?string $genre;
    private ?int $coverImageId;
    private ?string $releaseDate;
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
        $this->title = $data['title'] ?? '';
        $this->slug = $data['slug'] ?? '';
        $this->description = $data['description'] ?? null;
        $this->platform = $data['platform'] ?? null;
        $this->genre = $data['genre'] ?? null;
        $this->coverImageId = $data['cover_image_id'] ? (int)$data['cover_image_id'] : null;
        $this->releaseDate = $data['release_date'] ?? null;
        $this->createdAt = $data['created_at'] ?? '';
    }
    
    /**
     * Trouver un jeu par ID
     */
    public static function find(int $id): ?self
    {
        $sql = "SELECT * FROM games WHERE id = ?";
        $data = Database::queryOne($sql, [$id]);
        
        return $data ? new self($data) : null;
    }
    
    /**
     * Trouver un jeu par slug
     */
    public static function findBySlug(string $slug): ?self
    {
        $sql = "SELECT * FROM games WHERE slug = ?";
        $data = Database::queryOne($sql, [$slug]);
        
        return $data ? new self($data) : null;
    }
    
    /**
     * Trouver tous les jeux avec pagination
     */
    public static function findAll(int $limit = 20, int $offset = 0): array
    {
        $sql = "SELECT g.*, m.filename as cover_image 
                FROM games g 
                LEFT JOIN media m ON g.cover_image_id = m.id 
                ORDER BY g.created_at DESC 
                LIMIT ? OFFSET ?";
        
        $results = Database::query($sql, [$limit, $offset]);
        
        return array_map(fn($data) => new self($data), $results);
    }
    
    /**
     * Compter le nombre total de jeux
     */
    public static function count(): int
    {
        $sql = "SELECT COUNT(*) as total FROM games";
        $result = Database::queryOne($sql);
        
        return (int)($result['total'] ?? 0);
    }
    
    /**
     * Rechercher des jeux
     */
    public static function search(string $query, int $limit = 20): array
    {
        $sql = "SELECT g.*, m.filename as cover_image 
                FROM games g 
                LEFT JOIN media m ON g.cover_image_id = m.id 
                WHERE g.title LIKE ? OR g.description LIKE ? 
                ORDER BY g.created_at DESC 
                LIMIT ?";
        
        $searchTerm = "%{$query}%";
        $results = Database::query($sql, [$searchTerm, $searchTerm, $limit]);
        
        return array_map(fn($data) => new self($data), $results);
    }
    
    /**
     * Trouver les jeux par plateforme
     */
    public static function findByPlatform(string $platform, int $limit = 20): array
    {
        $sql = "SELECT g.*, m.filename as cover_image 
                FROM games g 
                LEFT JOIN media m ON g.cover_image_id = m.id 
                WHERE g.platform LIKE ? 
                ORDER BY g.created_at DESC 
                LIMIT ?";
        
        $results = Database::query($sql, ["%{$platform}%", $limit]);
        
        return array_map(fn($data) => new self($data), $results);
    }
    
    /**
     * Trouver les jeux par genre
     */
    public static function findByGenre(string $genre, int $limit = 20): array
    {
        $sql = "SELECT g.*, m.filename as cover_image 
                FROM games g 
                LEFT JOIN media m ON g.cover_image_id = m.id 
                WHERE g.genre LIKE ? 
                ORDER BY g.created_at DESC 
                LIMIT ?";
        
        $results = Database::query($sql, ["%{$genre}%", $limit]);
        
        return array_map(fn($data) => new self($data), $results);
    }
    
    /**
     * Obtenir tous les plateformes uniques
     */
    public static function getPlatforms(): array
    {
        $sql = "SELECT DISTINCT platform FROM games WHERE platform IS NOT NULL AND platform != '' ORDER BY platform";
        $results = Database::query($sql);
        
        return array_column($results, 'platform');
    }
    
    /**
     * Obtenir tous les genres uniques
     */
    public static function getGenres(): array
    {
        $sql = "SELECT DISTINCT genre FROM games WHERE genre IS NOT NULL AND genre != '' ORDER BY genre";
        $results = Database::query($sql);
        
        return array_column($results, 'genre');
    }
    
    /**
     * Créer un nouveau jeu
     */
    public static function create(array $data): ?self
    {
        $sql = "INSERT INTO games (title, slug, description, platform, genre, cover_image_id, release_date) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $params = [
            $data['title'],
            $data['slug'],
            $data['description'] ?? null,
            $data['platform'] ?? null,
            $data['genre'] ?? null,
            $data['cover_image_id'] ?? null,
            $data['release_date'] ?? null
        ];
        
        if (Database::execute($sql, $params)) {
            $id = Database::lastInsertId();
            return self::find($id);
        }
        
        return null;
    }
    
    /**
     * Mettre à jour un jeu
     */
    public function update(array $data): bool
    {
        $sql = "UPDATE games SET 
                title = ?, slug = ?, description = ?, platform = ?, 
                genre = ?, cover_image_id = ?, release_date = ? 
                WHERE id = ?";
        
        $params = [
            $data['title'] ?? $this->title,
            $data['slug'] ?? $this->slug,
            $data['description'] ?? $this->description,
            $data['platform'] ?? $this->platform,
            $data['genre'] ?? $this->genre,
            $data['cover_image_id'] ?? $this->coverImageId,
            $data['release_date'] ?? $this->releaseDate,
            $this->id
        ];
        
        return Database::execute($sql, $params) > 0;
    }
    
    /**
     * Supprimer un jeu
     */
    public function delete(): bool
    {
        $sql = "DELETE FROM games WHERE id = ?";
        return Database::execute($sql, [$this->id]) > 0;
    }
    
    /**
     * Obtenir les articles liés à ce jeu
     */
    public function getArticles(int $limit = 10): array
    {
        $sql = "SELECT a.*, u.login as author_name, c.name as category_name 
                FROM articles a 
                LEFT JOIN users u ON a.author_id = u.id 
                LEFT JOIN categories c ON a.category_id = c.id 
                WHERE a.game_id = ? AND a.status = 'published' 
                ORDER BY a.published_at DESC 
                LIMIT ?";
        
        $results = Database::query($sql, [$this->id, $limit]);
        
        return array_map(fn($data) => new Article($data), $results);
    }
    
    /**
     * Compter les articles liés à ce jeu
     */
    public function getArticlesCount(): int
    {
        $sql = "SELECT COUNT(*) as total FROM articles WHERE game_id = ? AND status = 'published'";
        $result = Database::queryOne($sql, [$this->id]);
        
        return (int)($result['total'] ?? 0);
    }
    
    /**
     * Obtenir l'image de couverture
     */
    public function getCoverImage(): ?Media
    {
        if (!$this->coverImageId) {
            return null;
        }
        
        return Media::find($this->coverImageId);
    }
    
    /**
     * Obtenir l'URL de l'image de couverture
     */
    public function getCoverImageUrl(): ?string
    {
        $coverImage = $this->getCoverImage();
        return $coverImage ? $coverImage->getUrl() : null;
    }
    
    /**
     * Vérifier si le jeu est sorti
     */
    public function isReleased(): bool
    {
        if (!$this->releaseDate) {
            return false;
        }
        
        return strtotime($this->releaseDate) <= time();
    }
    
    /**
     * Obtenir le statut de sortie
     */
    public function getReleaseStatus(): string
    {
        if (!$this->releaseDate) {
            return 'Date inconnue';
        }
        
        $releaseTime = strtotime($this->releaseDate);
        $now = time();
        
        if ($releaseTime <= $now) {
            return 'Sorti le ' . date('d/m/Y', $releaseTime);
        } else {
            $daysLeft = ceil(($releaseTime - $now) / 86400);
            return "Sortie dans {$daysLeft} jour(s)";
        }
    }
    
    /**
     * Générer un slug à partir du titre
     */
    public static function generateSlug(string $title): string
    {
        $slug = strtolower(trim($title));
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
        $sql = "SELECT COUNT(*) as total FROM games WHERE slug = ? AND id != ?";
        $result = Database::queryOne($sql, [$slug, $excludeId]);
        
        return (int)($result['total'] ?? 0) > 0;
    }
    
    // Getters
    public function getId(): int { return $this->id; }
    public function getTitle(): string { return $this->title; }
    public function getSlug(): string { return $this->slug; }
    public function getDescription(): ?string { return $this->description; }
    public function getPlatform(): ?string { return $this->platform; }
    public function getGenre(): ?string { return $this->genre; }
    public function getCoverImageId(): ?int { return $this->coverImageId; }
    public function getReleaseDate(): ?string { return $this->releaseDate; }
    public function getCreatedAt(): string { return $this->createdAt; }
    
    /**
     * Convertir en tableau pour l'API
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'platform' => $this->platform,
            'genre' => $this->genre,
            'cover_image_id' => $this->coverImageId,
            'cover_image_url' => $this->getCoverImageUrl(),
            'release_date' => $this->releaseDate,
            'created_at' => $this->createdAt,
            'is_released' => $this->isReleased(),
            'release_status' => $this->getReleaseStatus(),
            'articles_count' => $this->getArticlesCount()
        ];
    }
}
