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
    private ?int $genreId;
    private ?int $coverImageId;
    private ?int $hardwareId;
    private ?string $releaseDate;
    private string $createdAt;
    
    // Nouveaux champs
    private ?float $score;
    private bool $isTested;
    private ?string $developer;
    private ?string $publisher;
    private ?int $pegiRating;
    
    // Liens d'achat
    private ?string $steamUrl;
    private ?string $eshopUrl;
    private ?string $xboxUrl;
    private ?string $psnUrl;
    private ?string $epicUrl;
    private ?string $gogUrl;
    
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
        $this->genreId = $data['genre_id'] ? (int)$data['genre_id'] : null;
        $this->coverImageId = $data['cover_image_id'] ? (int)$data['cover_image_id'] : null;
        $this->hardwareId = $data['hardware_id'] ? (int)$data['hardware_id'] : null;
        $this->releaseDate = $data['release_date'] ?? null;
        $this->createdAt = $data['created_at'] ?? '';
        
        // Nouveaux champs
        $this->score = isset($data['score']) ? (float)$data['score'] : null;
        $this->isTested = (bool)($data['is_tested'] ?? false);
        $this->developer = $data['developer'] ?? null;
        $this->publisher = $data['publisher'] ?? null;
        $this->pegiRating = $data['pegi_rating'] ? (int)$data['pegi_rating'] : null;
        
        // Liens d'achat
        $this->steamUrl = $data['steam_url'] ?? null;
        $this->eshopUrl = $data['eshop_url'] ?? null;
        $this->xboxUrl = $data['xbox_url'] ?? null;
        $this->psnUrl = $data['psn_url'] ?? null;
        $this->epicUrl = $data['epic_url'] ?? null;
        $this->gogUrl = $data['gog_url'] ?? null;
    }

    // Getters
    public function getId(): int { return $this->id; }
    public function getTitle(): string { return $this->title; }
    public function getSlug(): string { return $this->slug; }
    public function getDescription(): ?string { return $this->description; }
    public function getPlatform(): ?string { return $this->platform; }
    public function getGenreId(): ?int { return $this->genreId; }
    public function getCoverImageId(): ?int { return $this->coverImageId; }
    public function getHardwareId(): ?int { return $this->hardwareId; }
    public function getReleaseDate(): ?string { return $this->releaseDate; }
    public function getCreatedAt(): string { return $this->createdAt; }
    
    // Nouveaux getters
    public function getScore(): ?float { return $this->score; }
    public function isTested(): bool { return $this->isTested; }
    public function getDeveloper(): ?string { return $this->developer; }
    public function getPublisher(): ?string { return $this->publisher; }
    public function getPegiRating(): ?int { return $this->pegiRating; }
    
    // Getters pour les liens d'achat
    public function getSteamUrl(): ?string { return $this->steamUrl; }
    public function getEshopUrl(): ?string { return $this->eshopUrl; }
    public function getXboxUrl(): ?string { return $this->xboxUrl; }
    public function getPsnUrl(): ?string { return $this->psnUrl; }
    public function getEpicUrl(): ?string { return $this->epicUrl; }
    public function getGogUrl(): ?string { return $this->gogUrl; }


    
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
     * Rechercher des jeux par titre
     */
    public static function search(string $query, int $limit = 20): array
    {
        $sql = "SELECT g.*, m.filename as cover_image, h.name as hardware_name 
                FROM games g 
                LEFT JOIN media m ON g.cover_image_id = m.id 
                LEFT JOIN hardware h ON g.hardware_id = h.id 
                WHERE g.title LIKE ? 
                ORDER BY g.title ASC 
                LIMIT ?";
        
        $searchTerm = "%{$query}%";
        $results = Database::query($sql, [$searchTerm, $limit]);
        
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
     * Compter les jeux avec conditions
     */
    public static function countWithConditions(string $query, array $params = []): int
    {
        $result = Database::queryOne($query, $params);
        return (int)($result['total'] ?? 0);
    }

    /**
     * Trouver des jeux avec une requête personnalisée
     */
    public static function findWithQuery(string $query, array $params = []): array
    {
        $results = Database::query($query, $params);
        return array_map(fn($data) => new self($data), $results);
    }

    /**
     * Obtenir toutes les plateformes uniques
     */
    public static function getPlatforms(): array
    {
        $sql = "SELECT DISTINCT platform FROM games WHERE platform IS NOT NULL AND platform != '' ORDER BY platform";
        $results = Database::query($sql);
        
        return array_column($results, 'platform');
    }
    
    /**
     * Obtenir tous les genres uniques (depuis la table genres)
     */
    public static function getGenres(): array
    {
        $sql = "SELECT id, name, description, color FROM genres ORDER BY name";
        $results = Database::query($sql);
        
        return $results;
    }
    
    /**
     * Créer un nouveau jeu
     */
    public static function create(array $data): ?self
    {
        $sql = "INSERT INTO games (title, slug, description, platform, genre_id, cover_image_id, hardware_id, release_date, score, is_tested, developer, publisher, pegi_rating, steam_url, eshop_url, xbox_url, psn_url, epic_url, gog_url) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $params = [
            $data['title'],
            $data['slug'],
            $data['description'] ?? null,
            $data['platform'] ?? null,
            $data['genre_id'] ?? null,
            $data['cover_image_id'] ?? null,
            $data['hardware_id'] ?? null,
            $data['release_date'] ?? null,
            $data['score'] ?? null,
            $data['is_tested'] ?? false,
            $data['developer'] ?? null,
            $data['publisher'] ?? null,
            $data['pegi_rating'] ?? null,
            $data['steam_url'] ?? null,
            $data['eshop_url'] ?? null,
            $data['xbox_url'] ?? null,
            $data['psn_url'] ?? null,
            $data['epic_url'] ?? null,
            $data['gog_url'] ?? null
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
                genre_id = ?, cover_image_id = ?, hardware_id = ?, release_date = ?,
                score = ?, is_tested = ?, developer = ?, publisher = ?, pegi_rating = ?,
                steam_url = ?, eshop_url = ?, xbox_url = ?, psn_url = ?, epic_url = ?, gog_url = ?
                WHERE id = ?";
        
        $params = [
            $data['title'] ?? $this->title,
            $data['slug'] ?? $this->slug,
            $data['description'] ?? $this->description,
            $data['platform'] ?? $this->platform,
            $data['genre_id'] ?? $this->genreId,
            $data['cover_image_id'] ?? $this->coverImageId,
            $data['hardware_id'] ?? $this->hardwareId,
            $data['release_date'] ?? $this->releaseDate,
            $data['score'] ?? $this->score,
            $data['is_tested'] ?? $this->isTested,
            $data['developer'] ?? $this->developer,
            $data['publisher'] ?? $this->publisher,
            $data['pegi_rating'] ?? $this->pegiRating,
            $data['steam_url'] ?? $this->steamUrl,
            $data['eshop_url'] ?? $this->eshopUrl,
            $data['xbox_url'] ?? $this->xboxUrl,
            $data['psn_url'] ?? $this->psnUrl,
            $data['epic_url'] ?? $this->epicUrl,
            $data['gog_url'] ?? $this->gogUrl,
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
    
    /**
     * Obtenir le hardware associé
     */
    public function getHardware(): ?\Hardware
    {
        if (!$this->hardwareId) {
            return null;
        }
        
        return \Hardware::find($this->hardwareId);
    }
    
    /**
     * Obtenir le nom du hardware
     */
    public function getHardwareName(): ?string
    {
        $hardware = $this->getHardware();
        return $hardware ? $hardware->getName() : null;
    }
    
    /**
     * Obtenir le genre associé
     */
    public function getGenre(): ?\Genre
    {
        if (!$this->genreId) {
            return null;
        }
        
        return \Genre::find($this->genreId);
    }
    
    /**
     * Obtenir le nom du genre
     */
    public function getGenreName(): ?string
    {
        $genre = $this->getGenre();
        return $genre ? $genre->getName() : null;
    }
    
    /**
     * Convertir en tableau pour l'API
     */
    public function toArray(): array
    {
        $hardware = $this->getHardware();
        
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'platform' => $this->platform,
            'genre_id' => $this->genreId,
            'genre_name' => $this->getGenreName(),
            'cover_image_id' => $this->coverImageId,
            'cover_image_url' => $this->getCoverImageUrl(),
            'hardware_id' => $this->hardwareId,
            'hardware_name' => $hardware ? $hardware->getName() : null,
            'hardware_type' => $hardware ? $hardware->getType() : null,
            'release_date' => $this->releaseDate,
            'created_at' => $this->createdAt,
            'is_released' => $this->isReleased(),
            'release_status' => $this->getReleaseStatus(),
            'articles_count' => $this->getArticlesCount()
        ];
    }
}
