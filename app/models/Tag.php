<?php
declare(strict_types=1);

/**
 * Modèle Tag - Gestion des tags
 */

require_once __DIR__ . '/../../core/Database.php';

class Tag
{
    private static $db;

    /**
     * Initialiser la connexion à la base de données
     */
    private static function initDb(): void
    {
        if (!self::$db) {
            self::$db = new Database();
        }
    }

    /**
     * Récupérer tous les tags
     */
    public static function findAll(): array
    {
        self::initDb();

        $query = "SELECT * FROM tags ORDER BY name ASC";
        return self::$db->query($query);
    }

    /**
     * Récupérer un tag par son ID
     */
    public static function findById(int $id): ?array
    {
        self::initDb();

        $query = "SELECT * FROM tags WHERE id = ?";
        $result = self::$db->query($query, [$id]);

        return $result ? $result[0] : null;
    }

    /**
     * Récupérer un tag par son slug
     */
    public static function findBySlug(string $slug): ?array
    {
        self::initDb();

        $query = "SELECT * FROM tags WHERE slug = ?";
        $result = self::$db->query($query, [$slug]);

        return $result ? $result[0] : null;
    }

    /**
     * Récupérer un tag par son nom
     */
    public static function findByName(string $name): ?array
    {
        self::initDb();

        $query = "SELECT * FROM tags WHERE name = ?";
        $result = self::$db->query($query, [$name]);

        return $result ? $result[0] : null;
    }

    /**
     * Créer un nouveau tag
     */
    public static function create(array $data): ?int
    {
        self::initDb();

        $query = "INSERT INTO tags (name, slug) VALUES (?, ?)";
        $params = [$data['name'], $data['slug']];

        if (self::$db->execute($query, $params)) {
            return self::$db->lastInsertId();
        }

        return null;
    }

    /**
     * Mettre à jour un tag
     */
    public static function update(int $id, array $data): bool
    {
        self::initDb();

        $query = "UPDATE tags SET name = ?, slug = ? WHERE id = ?";
        $params = [$data['name'], $data['slug'], $id];

        return self::$db->execute($query, $params);
    }

    /**
     * Supprimer un tag
     */
    public static function delete(int $id): bool
    {
        self::initDb();

        $query = "DELETE FROM tags WHERE id = ?";
        $affectedRows = self::$db->execute($query, [$id]);
        return $affectedRows > 0;
    }

    /**
     * Compter le nombre total de tags
     */
    public static function count(): int
    {
        self::initDb();

        $query = "SELECT COUNT(*) as count FROM tags";
        $result = self::$db->query($query);

        return $result ? (int)$result[0]['count'] : 0;
    }

    /**
     * Récupérer les tags avec le nombre d'articles
     */
    public static function findAllWithArticleCount(): array
    {
        self::initDb();

        $query = "SELECT t.*, COUNT(at.article_id) as article_count 
                  FROM tags t 
                  LEFT JOIN article_tag at ON t.id = at.tag_id 
                  GROUP BY t.id 
                  ORDER BY t.name ASC";

        return self::$db->query($query);
    }

    /**
     * Vérifier si un tag existe
     */
    public static function exists(int $id): bool
    {
        self::initDb();

        $query = "SELECT COUNT(*) as count FROM tags WHERE id = ?";
        $result = self::$db->query($query, [$id]);

        return $result && $result[0]['count'] > 0;
    }

    /**
     * Vérifier si un slug existe
     */
    public static function slugExists(string $slug, ?int $excludeId = null): bool
    {
        self::initDb();

        $query = "SELECT COUNT(*) as count FROM tags WHERE slug = ?";
        $params = [$slug];

        if ($excludeId) {
            $query .= " AND id != ?";
            $params[] = $excludeId;
        }

        $result = self::$db->query($query, $params);

        return $result && $result[0]['count'] > 0;
    }

    /**
     * Compter les tags avec conditions
     */
    public static function countWithConditions(string $query, array $params = []): int
    {
        self::initDb();

        $result = self::$db->query($query, $params);
        return $result ? (int)$result[0]['total'] : 0;
    }

    /**
     * Trouver des tags avec requête personnalisée
     */
    public static function findWithQuery(string $query, array $params = []): array
    {
        self::initDb();

        return self::$db->query($query, $params);
    }

    /**
     * Générer un slug à partir d'un nom
     */
    public static function generateSlug(string $name): string
    {
        // Convertir en minuscules
        $slug = strtolower($name);
        
        // Remplacer les caractères spéciaux par des tirets
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
        
        // Remplacer les espaces par des tirets
        $slug = preg_replace('/[\s-]+/', '-', $slug);
        
        // Supprimer les tirets en début et fin
        $slug = trim($slug, '-');
        
        return $slug;
    }

    /**
     * Récupérer les tags d'un article
     */
    public static function findByArticleId(int $articleId): array
    {
        self::initDb();

        $query = "SELECT t.* FROM tags t 
                  JOIN article_tag at ON t.id = at.tag_id 
                  WHERE at.article_id = ? 
                  ORDER BY t.name ASC";

        return self::$db->query($query, [$articleId]);
    }

    /**
     * Rechercher des tags par nom
     */
    public static function search(string $search): array
    {
        self::initDb();

        $query = "SELECT * FROM tags WHERE name LIKE ? ORDER BY name ASC";
        return self::$db->query($query, ["%{$search}%"]);
    }
}
