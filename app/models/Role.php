<?php
declare(strict_types=1);

/**
 * Modèle Role - Gestion des rôles utilisateurs
 */

require_once __DIR__ . '/../../core/Database.php';

class Role
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
     * Récupérer tous les rôles
     */
    public static function findAll(): array
    {
        self::initDb();
        
        $query = "SELECT * FROM roles ORDER BY id ASC";
        return self::$db->query($query);
    }
    
    /**
     * Récupérer un rôle par son ID
     */
    public static function findById(int $id): ?array
    {
        self::initDb();
        
        $query = "SELECT * FROM roles WHERE id = ?";
        $result = self::$db->query($query, [$id]);
        
        return $result ? $result[0] : null;
    }
    
    /**
     * Récupérer un rôle par son nom
     */
    public static function findByName(string $name): ?array
    {
        self::initDb();
        
        $query = "SELECT * FROM roles WHERE name = ?";
        $result = self::$db->query($query, [$name]);
        
        return $result ? $result[0] : null;
    }
    
    /**
     * Créer un nouveau rôle
     */
    public static function create(array $data): ?int
    {
        self::initDb();
        
        $query = "INSERT INTO roles (name) VALUES (?)";
        $params = [$data['name']];
        
        if (self::$db->execute($query, $params)) {
            return self::$db->lastInsertId();
        }
        
        return null;
    }
    
    /**
     * Mettre à jour un rôle
     */
    public static function update(int $id, array $data): bool
    {
        self::initDb();
        
        $query = "UPDATE roles SET name = ? WHERE id = ?";
        $params = [$data['name'], $id];
        
        return self::$db->execute($query, $params);
    }
    
    /**
     * Supprimer un rôle
     */
    public static function delete(int $id): bool
    {
        self::initDb();
        
        // Vérifier qu'aucun utilisateur n'utilise ce rôle
        $checkQuery = "SELECT COUNT(*) as count FROM users WHERE role_id = ?";
        $result = self::$db->query($checkQuery, [$id]);
        
        if ($result && $result[0]['count'] > 0) {
            return false; // Impossible de supprimer un rôle utilisé
        }
        
        $query = "DELETE FROM roles WHERE id = ?";
        return self::$db->execute($query, [$id]);
    }
    
    /**
     * Compter le nombre total de rôles
     */
    public static function count(): int
    {
        self::initDb();
        
        $query = "SELECT COUNT(*) as count FROM roles";
        $result = self::$db->query($query);
        
        return $result ? (int)$result[0]['count'] : 0;
    }
    
    /**
     * Récupérer les rôles avec le nombre d'utilisateurs
     */
    public static function findAllWithUserCount(): array
    {
        self::initDb();
        
        $query = "SELECT r.*, COUNT(u.id) as user_count 
                  FROM roles r 
                  LEFT JOIN users u ON r.id = u.role_id 
                  GROUP BY r.id 
                  ORDER BY r.id ASC";
        
        return self::$db->query($query);
    }
    
    /**
     * Vérifier si un rôle existe
     */
    public static function exists(int $id): bool
    {
        self::initDb();
        
        $query = "SELECT COUNT(*) as count FROM roles WHERE id = ?";
        $result = self::$db->query($query, [$id]);
        
        return $result && $result[0]['count'] > 0;
    }
    
    /**
     * Récupérer les rôles par ordre de priorité (admin en premier)
     */
    public static function findAllOrdered(): array
    {
        self::initDb();
        
        $query = "SELECT * FROM roles ORDER BY 
                  CASE name 
                    WHEN 'admin' THEN 1 
                    WHEN 'editor' THEN 2 
                    WHEN 'author' THEN 3 
                    WHEN 'member' THEN 4 
                    ELSE 5 
                  END";
        
        return self::$db->query($query);
    }
}
