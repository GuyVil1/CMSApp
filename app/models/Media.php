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
        $sql = "INSERT INTO media (filename, original_name, mime_type, size, uploaded_by) 
                VALUES (?, ?, ?, ?, ?)";
        
        $params = [
            $data['filename'],
            $data['original_name'],
            $data['mime_type'],
            $data['size'],
            $data['uploaded_by']
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
        return '/image.php?file=' . urlencode($this->filename);
    }
    
    /**
     * Obtenir l'URL de la vignette
     */
    public function getThumbnailUrl(): string
    {
        $pathInfo = pathinfo($this->filename);
        $thumbnailName = 'thumb_' . $pathInfo['basename'];
        return '/image.php?file=' . urlencode($thumbnailName);
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
    
    // Getters
    public function getId(): int { return $this->id; }
    public function getFilename(): string { return $this->filename; }
    public function getOriginalName(): string { return $this->originalName; }
    public function getMimeType(): string { return $this->mimeType; }
    public function getSize(): int { return $this->size; }
    public function getUploadedBy(): int { return $this->uploadedBy; }
    public function getCreatedAt(): string { return $this->createdAt; }
    
    /**
     * Convertir en tableau pour l'API
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'filename' => $this->filename,
            'original_name' => $this->originalName,
            'mime_type' => $this->mimeType,
            'size' => $this->size,
            'size_formatted' => $this->getFormattedSize(),
            'uploaded_by' => $this->uploadedBy,
            'created_at' => $this->createdAt,
            'url' => $this->getUrl(),
            'thumbnail_url' => $this->isImage() ? $this->getThumbnailUrl() : null,
            'is_image' => $this->isImage(),
            'is_video' => $this->isVideo(),
            'file_exists' => $this->fileExists()
        ];
    }
}
