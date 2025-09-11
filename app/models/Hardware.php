<?php
declare(strict_types=1);

/**
 * Modèle Hardware
 * Gestion des matériels de jeu (consoles, PC, etc.)
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
    private ?string $image;
    private string $createdAt;
    private ?string $updatedAt;
    private int $gamesCount = 0;
    private int $articlesCount = 0;

    public function __construct(array $data = [])
    {
        if (!empty($data)) {
            $this->id = (int) $data['id'];
            $this->name = $data['name'];
            $this->slug = $data['slug'];
            $this->type = $data['type'];
            $this->manufacturer = $data['manufacturer'];
            $this->releaseYear = $data['release_year'] ? (int) $data['release_year'] : null;
            $this->description = $data['description'];
            $this->isActive = (bool) $data['is_active'];
            $this->sortOrder = (int) $data['sort_order'];
            $this->image = $data['image'] ?? null;
            $this->createdAt = $data['created_at'];
            $this->updatedAt = $data['updated_at'];
            $this->gamesCount = isset($data['games_count']) ? (int) $data['games_count'] : 0;
            $this->articlesCount = isset($data['articles_count']) ? (int) $data['articles_count'] : 0;
        }
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
    public function getImage(): ?string { return $this->image; }
    public function getCreatedAt(): string { return $this->createdAt; }
    public function getUpdatedAt(): ?string { return $this->updatedAt; }
    public function getGamesCount(): int { return $this->gamesCount; }
    public function getArticlesCount(): int { return $this->articlesCount; }
    
    /**
     * Obtenir le nom du type formaté
     */
    public function getTypeName(): string
    {
        return match($this->type) {
            'console' => 'Console',
            'pc' => 'PC',
            'other' => 'Autre',
            default => ucfirst($this->type)
        };
    }

    /**
     * Récupérer tous les hardwares actifs avec compteurs de jeux et articles
     */
    public static function getAllActive(): array
    {
        try {
            $sql = "
                SELECT 
                    h.*,
                    CASE 
                        WHEN h.name = 'Multi-plateforme' THEN (
                            SELECT COUNT(DISTINCT gp.game_id)
                            FROM game_platforms gp
                            WHERE gp.game_id IN (
                                SELECT game_id 
                                FROM game_platforms 
                                GROUP BY game_id 
                                HAVING COUNT(DISTINCT hardware_id) > 1
                            )
                        )
                        ELSE COUNT(DISTINCT gp.game_id)
                    END as games_count,
                    CASE 
                        WHEN h.name = 'Multi-plateforme' THEN (
                            SELECT COUNT(DISTINCT a.id)
                            FROM articles a
                            JOIN games g ON a.game_id = g.id
                            WHERE a.status = 'published'
                            AND g.id IN (
                                SELECT game_id 
                                FROM game_platforms 
                                GROUP BY game_id 
                                HAVING COUNT(DISTINCT hardware_id) > 1
                            )
                        )
                        ELSE COUNT(DISTINCT a.id)
                    END as articles_count
                FROM hardware h
                LEFT JOIN game_platforms gp ON h.id = gp.hardware_id
                LEFT JOIN games g ON gp.game_id = g.id
                LEFT JOIN articles a ON g.id = a.game_id AND a.status = 'published'
                WHERE h.is_active = 1
                GROUP BY h.id
                ORDER BY h.sort_order ASC, h.name ASC
            ";
            
            $results = Database::query($sql);
            
            return array_map(function($data) {
                $hardware = new self($data);
                $hardware->gamesCount = (int)$data['games_count'];
                $hardware->articlesCount = (int)$data['articles_count'];
                return $hardware;
            }, $results);
        } catch (Exception $e) {
            error_log("Erreur lors de la récupération des hardwares actifs: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Récupérer tous les hardwares actifs pour le menu
     */
    public static function getAllForMenu(): array
    {
        try {
            $db = Database::getInstance();
            $sql = "SELECT * FROM hardware WHERE is_active = 1 ORDER BY sort_order ASC, name ASC";
            $stmt = $db->prepare($sql);
            $stmt->execute();
            
            $hardwares = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $hardwares[] = new self($row);
            }
            
            return $hardwares;
        } catch (Exception $e) {
            error_log("Erreur lors de la récupération des hardwares: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Récupérer un hardware par son ID
     */
    public static function find(int $id): ?self
    {
        try {
            $db = Database::getInstance();
            $sql = "SELECT * FROM hardware WHERE id = ?";
            $stmt = $db->prepare($sql);
            $stmt->execute([$id]);
            
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ? new self($row) : null;
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération du hardware par ID: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Récupérer un hardware par son slug
     */
    public static function findBySlug(string $slug): ?self
    {
        try {
            $db = Database::getInstance();
            $sql = "SELECT * FROM hardware WHERE slug = ? AND is_active = 1";
            $stmt = $db->prepare($sql);
            $stmt->execute([$slug]);
            
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ? new self($row) : null;
        } catch (Exception $e) {
            error_log("Erreur lors de la récupération du hardware par slug: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Récupérer les hardwares par type
     */
    public static function getByType(string $type): array
    {
        try {
            $db = Database::getInstance();
            $sql = "SELECT * FROM hardware WHERE type = ? AND is_active = 1 ORDER BY sort_order ASC, name ASC";
            $stmt = $db->prepare($sql);
            $stmt->execute([$type]);
            
            $hardwares = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $hardwares[] = new self($row);
            }
            
            return $hardwares;
        } catch (Exception $e) {
            error_log("Erreur lors de la récupération des hardwares par type: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtenir l'URL du hardware
     */
    public function getUrl(): string
    {
        // Correction: utiliser /hardwares/ au lieu de /hardware/ pour cohérence
        return '/hardwares/' . $this->slug;
    }

    /**
     * Obtenir le nom complet avec fabricant
     */
    public function getFullName(): string
    {
        if ($this->manufacturer && $this->manufacturer !== 'Divers') {
            return $this->manufacturer . ' ' . $this->name;
        }
        return $this->name;
    }

    /**
     * Compter le nombre total de hardwares
     */
    public static function count(): int
    {
        try {
            $db = Database::getInstance();
            $sql = "SELECT COUNT(*) as total FROM hardware";
            $stmt = $db->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int) $result['total'];
        } catch (Exception $e) {
            error_log("Erreur lors du comptage des hardwares: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Compter le nombre de hardwares avec conditions
     */
    public static function countWithConditions(array $conditions = [], array $params = []): int
    {
        try {
            $db = Database::getInstance();
            $whereClause = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';
            $sql = "SELECT COUNT(*) as total FROM hardware {$whereClause}";
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int) $result['total'];
        } catch (Exception $e) {
            error_log("Erreur lors du comptage des hardwares avec conditions: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Récupérer tous les hardwares avec pagination
     */
    public static function findAllWithPagination(int $limit = 20, int $offset = 0, array $conditions = [], array $params = []): array
    {
        try {
            $db = Database::getInstance();
            $whereClause = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';
            $sql = "SELECT h.*, COUNT(g.id) as games_count
                    FROM hardware h
                    LEFT JOIN games g ON h.id = g.hardware_id
                    {$whereClause}
                    GROUP BY h.id
                    ORDER BY h.sort_order ASC, h.name ASC
                    LIMIT {$limit} OFFSET {$offset}";
            
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            
            $hardwares = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $hardwares[] = new self($row);
            }
            
            return $hardwares;
        } catch (Exception $e) {
            error_log("Erreur lors de la récupération des hardwares avec pagination: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Récupérer tous les types de hardware disponibles
     */
    public static function getTypes(): array
    {
        try {
            $db = Database::getInstance();
            $sql = "SELECT DISTINCT type FROM hardware ORDER BY type ASC";
            $stmt = $db->prepare($sql);
            $stmt->execute();
            
            $types = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $types[] = $row['type'];
            }
            
            return $types;
        } catch (Exception $e) {
            error_log("Erreur lors de la récupération des types de hardware: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Vérifier si un slug existe déjà
     */
    public static function slugExists(string $slug, int $excludeId = 0): bool
    {
        try {
            $db = Database::getInstance();
            $sql = "SELECT COUNT(*) as total FROM hardware WHERE slug = ? AND id != ?";
            $stmt = $db->prepare($sql);
            $stmt->execute([$slug, $excludeId]);
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)($result['total'] ?? 0) > 0;
        } catch (Exception $e) {
            error_log("Erreur lors de la vérification du slug: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Créer un nouveau hardware
     */
    public static function create(array $data): ?self
    {
        try {
            $db = Database::getInstance();
            $sql = "INSERT INTO hardware (name, slug, type, manufacturer, release_year, description, is_active, sort_order, image, created_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
            
            $stmt = $db->prepare($sql);
            $success = $stmt->execute([
                $data['name'],
                $data['slug'],
                $data['type'],
                $data['manufacturer'],
                $data['release_year'],
                $data['description'],
                $data['is_active'] ? 1 : 0,
                $data['sort_order'],
                $data['image'] ?? null
            ]);
            
            if ($success) {
                $id = $db->lastInsertId();
                return self::find($id);
            }
            
            return null;
        } catch (Exception $e) {
            error_log("Erreur lors de la création du hardware: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Mettre à jour le hardware
     */
    public function update(array $data): bool
    {
        try {
            $db = Database::getInstance();
            $sql = "UPDATE hardware SET 
                    name = ?, slug = ?, type = ?, manufacturer = ?, release_year = ?, 
                    description = ?, is_active = ?, sort_order = ?, image = ?, updated_at = NOW() 
                    WHERE id = ?";
            
            $stmt = $db->prepare($sql);
            $success = $stmt->execute([
                $data['name'],
                $data['slug'],
                $data['type'],
                $data['manufacturer'],
                $data['release_year'],
                $data['description'],
                $data['is_active'] ? 1 : 0,
                $data['sort_order'],
                $data['image'] ?? null,
                $this->id
            ]);
            
            if ($success) {
                // Mettre à jour les propriétés de l'objet
                $this->name = $data['name'];
                $this->slug = $data['slug'];
                $this->type = $data['type'];
                $this->manufacturer = $data['manufacturer'];
                $this->releaseYear = $data['release_year'];
                $this->description = $data['description'];
                $this->isActive = $data['is_active'];
                $this->sortOrder = $data['sort_order'];
                $this->image = $data['image'] ?? null;
                $this->updatedAt = date('Y-m-d H:i:s');
            }
            
            return $success;
        } catch (Exception $e) {
            error_log("Erreur lors de la mise à jour du hardware: " . $e->getMessage());
            return false;
        }
    }
}
?>