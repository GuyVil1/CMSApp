<?php
declare(strict_types=1);

/**
 * Modèle Media - Gestion des uploads d'images et médias
 */

require_once __DIR__ . '/../../core/Database.php';

class Media
{
    private int $id;
    private string $filename;
    private string $originalName;
    private string $mimeType;
    private int $size;
    private int $uploadedBy;
    private ?int $gameId;
    private string $mediaType;
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
        $this->filename = $data['filename'] ?? '';
        $this->originalName = $data['original_name'] ?? '';
        $this->mimeType = $data['mime_type'] ?? '';
        $this->size = (int)($data['size'] ?? 0);
        $this->uploadedBy = (int)($data['uploaded_by'] ?? 0);
        $this->gameId = isset($data['game_id']) ? (int)$data['game_id'] : null;
        $this->mediaType = $data['media_type'] ?? 'other';
        $this->createdAt = $data['created_at'] ?? '';
    }
    
    /**
     * Trouver un média par ID
     */
    public static function find(int $id): ?self
    {
        $sql = "SELECT * FROM media WHERE id = ?";
        $data = Database::queryOne($sql, [$id]);
        
        return $data ? new self($data) : null;
    }
    
    /**
     * Trouver tous les médias avec pagination
     */
    public static function findAll(int $limit = 20, int $offset = 0): array
    {
        $sql = "SELECT m.*, u.login as uploader_name 
                FROM media m 
                LEFT JOIN users u ON m.uploaded_by = u.id 
                ORDER BY m.created_at DESC 
                LIMIT ? OFFSET ?";
        
        $results = Database::query($sql, [$limit, $offset]);
        
        return array_map(fn($data) => new self($data), $results);
    }
    
    /**
     * Compter le nombre total de médias
     */
    public static function count(): int
    {
        $sql = "SELECT COUNT(*) as total FROM media";
        $result = Database::queryOne($sql);
        
        return (int)($result['total'] ?? 0);
    }
    
    /**
     * Trouver les médias par type MIME
     */
    public static function findByMimeType(string $mimeType, int $limit = 20): array
    {
        $sql = "SELECT * FROM media WHERE mime_type LIKE ? ORDER BY created_at DESC LIMIT ?";
        $results = Database::query($sql, ["%{$mimeType}%", $limit]);
        
        return array_map(fn($data) => new self($data), $results);
    }
    
    /**
     * Créer un nouveau média
     */
    public static function create(array $data): ?self
    {
        $sql = "INSERT INTO media (filename, original_name, mime_type, size, uploaded_by, game_id, media_type) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $params = [
            $data['filename'],
            $data['original_name'],
            $data['mime_type'],
            $data['size'],
            $data['uploaded_by'],
            $data['game_id'] ?? null,
            $data['media_type'] ?? 'other'
        ];
        
        if (Database::execute($sql, $params)) {
            $id = (int)Database::lastInsertId();
            return self::find($id);
        }
        
        return null;
    }
    
    /**
     * Supprimer un média
     */
    public function delete(): bool
    {
        // Supprimer le fichier physique
        $filePath = $this->getFilePath();
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        
        // Supprimer les vignettes
        $this->deleteThumbnails();
        
        // Supprimer de la base de données
        $sql = "DELETE FROM media WHERE id = ?";
        return Database::execute($sql, [$this->id]) > 0;
    }
    
    /**
     * Obtenir le chemin du fichier
     */
    public function getFilePath(): string
    {
        return __DIR__ . '/../../public/uploads/' . $this->filename;
    }
    
    /**
     * Obtenir l'URL du fichier
     */
    public function getUrl(): string
    {
        return '/public/uploads.php?file=' . urlencode($this->filename);
    }
    
    /**
     * Obtenir l'URL de la vignette
     */
    public function getThumbnailUrl(): string
    {
        $pathInfo = pathinfo($this->filename);
        $thumbnailName = 'thumb_' . $pathInfo['basename'];
        $thumbnailPath = dirname($this->filename) . '/' . $thumbnailName;
        return '/public/uploads.php?file=' . urlencode($thumbnailPath);
    }
    
    /**
     * Vérifier si le fichier existe
     */
    public function fileExists(): bool
    {
        return file_exists($this->getFilePath());
    }
    
    /**
     * Obtenir la taille formatée
     */
    public function getFormattedSize(): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $size = $this->size;
        $unit = 0;
        
        while ($size >= 1024 && $unit < count($units) - 1) {
            $size /= 1024;
            $unit++;
        }
        
        return round($size, 2) . ' ' . $units[$unit];
    }
    
    /**
     * Vérifier si c'est une image
     */
    public function isImage(): bool
    {
        return strpos($this->mimeType, 'image/') === 0;
    }
    
    /**
     * Vérifier si c'est une vidéo
     */
    public function isVideo(): bool
    {
        return strpos($this->mimeType, 'video/') === 0;
    }
    
    /**
     * Trouver les médias d'un jeu
     */
    public static function findByGame(int $gameId, string $mediaType = null): array
    {
        $sql = "SELECT m.*, u.login as uploader_name 
                FROM media m 
                LEFT JOIN users u ON m.uploaded_by = u.id 
                WHERE m.game_id = ?";
        $params = [$gameId];
        
        if ($mediaType) {
            $sql .= " AND m.media_type = ?";
            $params[] = $mediaType;
        }
        
        $sql .= " ORDER BY m.created_at DESC";
        
        $results = Database::query($sql, $params);
        return array_map(fn($data) => new self($data), $results);
    }
    
    /**
     * Trouver la cover d'un jeu
     */
    public static function findCoverByGame(int $gameId): ?self
    {
        $sql = "SELECT * FROM media WHERE game_id = ? AND media_type = 'cover' LIMIT 1";
        $data = Database::queryOne($sql, [$gameId]);
        
        return $data ? new self($data) : null;
    }
    
    /**
     * Créer un dossier pour un jeu
     */
    public static function createGameDirectory(string $gameSlug): string
    {
        $gameDir = __DIR__ . '/../../public/uploads/games/' . $gameSlug;
        
        if (!is_dir($gameDir)) {
            mkdir($gameDir, 0755, true);
        }
        
        return $gameDir;
    }
    
    /**
     * Obtenir le chemin du fichier pour un jeu
     */
    public function getGameFilePath(): string
    {
        if (!$this->gameId) {
            return $this->getFilePath();
        }
        
        // Récupérer le slug du jeu
        $sql = "SELECT slug FROM games WHERE id = ?";
        $game = Database::queryOne($sql, [$this->gameId]);
        
        if (!$game) {
            return $this->getFilePath();
        }
        
        return __DIR__ . '/../../public/uploads/games/' . $game['slug'] . '/' . $this->filename;
    }
    
    /**
     * Obtenir l'URL du fichier pour un jeu
     */
    public function getGameUrl(): string
    {
        if (!$this->gameId) {
            return $this->getUrl();
        }
        
        // Récupérer le slug du jeu
        $sql = "SELECT slug FROM games WHERE id = ?";
        $game = Database::queryOne($sql, [$this->gameId]);
        
        if (!$game) {
            return $this->getUrl();
        }
        
        return '/public/uploads/games/' . $game['slug'] . '/' . $this->filename;
    }
    
    /**
     * Supprimer les vignettes
     */
    private function deleteThumbnails(): void
    {
        if (!$this->isImage()) {
            return;
        }
        
        $pathInfo = pathinfo($this->filename);
        $thumbnailName = 'thumb_' . $pathInfo['basename'];
        $thumbnailPath = __DIR__ . '/../../public/uploads/' . $thumbnailName;
        
        if (file_exists($thumbnailPath)) {
            unlink($thumbnailPath);
        }
    }
    
    /**
     * Obtenir l'ID du jeu associé
     */
    public function getGameId(): ?int
    {
        return $this->gameId;
    }
    
    /**
     * Obtenir le type de média
     */
    public function getMediaType(): string
    {
        return $this->mediaType;
    }
    
    /**
     * Obtenir le nom du fichier original
     */
    public function getOriginalName(): string
    {
        return $this->originalName;
    }
    
    /**
     * Obtenir le type MIME
     */
    public function getMimeType(): string
    {
        return $this->mimeType;
    }
    
    /**
     * Obtenir la taille
     */
    public function getSize(): int
    {
        return $this->size;
    }
    
    /**
     * Obtenir l'ID de l'utilisateur qui a uploadé
     */
    public function getUploadedBy(): int
    {
        return $this->uploadedBy;
    }
    
    /**
     * Obtenir la date de création
     */
    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }
    
    /**
     * Obtenir l'ID
     */
    public function getId(): int
    {
        return $this->id;
    }
    
    /**
     * Obtenir le nom du fichier
     */
    public function getFilename(): string
    {
        return $this->filename;
    }
    
    /**
     * Convertir en tableau
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'filename' => $this->filename,
            'original_name' => $this->originalName,
            'mime_type' => $this->mimeType,
            'size' => $this->size,
            'uploaded_by' => $this->uploadedBy,
            'game_id' => $this->gameId,
            'media_type' => $this->mediaType,
            'created_at' => $this->createdAt,
            'url' => $this->getUrl(),
            'thumbnail_url' => $this->getThumbnailUrl(),
            'formatted_size' => $this->getFormattedSize(),
            'is_image' => $this->isImage(),
            'is_video' => $this->isVideo()
        ];
    }
}
