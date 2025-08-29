<?php
declare(strict_types=1);

/**
 * Modèle User
 * Gestion des utilisateurs et de leurs rôles
 */

require_once __DIR__ . '/../../core/Database.php';
require_once __DIR__ . '/../../core/Auth.php';

class User
{
    private int $id;
    private string $login;
    private string $email;
    private string $password_hash;
    private int $role_id;
    private string $role_name;
    private string $created_at;
    private ?string $updated_at;
    private ?string $last_login;
    
    /**
     * Constructeur
     */
    public function __construct(array $data = [])
    {
        if (!empty($data)) {
            $this->hydrate($data);
        }
    }
    
    /**
     * Hydrater l'objet avec les données
     */
    private function hydrate(array $data): void
    {
        $this->id = (int) $data['id'];
        $this->login = $data['login'];
        $this->email = $data['email'];
        $this->password_hash = $data['password_hash'];
        $this->role_id = (int) $data['role_id'];
        $this->role_name = $data['role_name'] ?? '';
        $this->created_at = $data['created_at'];
        $this->updated_at = $data['updated_at'] ?? null;
        $this->last_login = $data['last_login'] ?? null;
    }
    
    /**
     * Trouver un utilisateur par ID
     */
    public static function findById(int $id): ?array
    {
        $db = new Database();
        $sql = "SELECT u.*, r.name as role_name 
                FROM users u 
                JOIN roles r ON u.role_id = r.id 
                WHERE u.id = ?";
        
        $result = $db->query($sql, [$id]);
        
        return $result ? $result[0] : null;
    }
    
    /**
     * Trouver un utilisateur par login ou email
     */
    public static function findByLogin(string $login): ?array
    {
        $db = new Database();
        $sql = "SELECT u.*, r.name as role_name 
                FROM users u 
                JOIN roles r ON u.role_id = r.id 
                WHERE u.login = ? OR u.email = ?";
        
        $result = $db->query($sql, [$login, $login]);
        
        return $result ? $result[0] : null;
    }
    
    /**
     * Trouver un utilisateur par email
     */
    public static function findByEmail(string $email): ?array
    {
        $db = new Database();
        $sql = "SELECT u.*, r.name as role_name 
                FROM users u 
                JOIN roles r ON u.role_id = r.id 
                WHERE u.email = ?";
        
        $result = $db->query($sql, [$email]);
        
        return $result ? $result[0] : null;
    }
    
    /**
     * Trouver tous les utilisateurs
     */
    public static function findAll(): array
    {
        $db = new Database();
        $sql = "SELECT u.*, r.name as role_name 
                FROM users u 
                JOIN roles r ON u.role_id = r.id 
                ORDER BY u.created_at DESC";
        
        return $db->query($sql);
    }
    
    /**
     * Créer un nouvel utilisateur
     */
    public static function create(array $data): ?array
    {
        try {
            $db = new Database();
            
            // Valider les données
            if (empty($data['login']) || empty($data['email']) || empty($data['password'])) {
                throw new Exception('Données manquantes');
            }
            
            // Vérifier si le login existe déjà
            if (self::findByLogin($data['login'])) {
                throw new Exception('Ce login existe déjà');
            }
            
            // Vérifier si l'email existe déjà
            $sql = "SELECT id FROM users WHERE email = ?";
            $result = $db->query($sql, [$data['email']]);
            if ($result) {
                throw new Exception('Cet email existe déjà');
            }
            
            // Hasher le mot de passe
            $password_hash = password_hash($data['password'], PASSWORD_DEFAULT);
            
            // Insérer l'utilisateur
            $sql = "INSERT INTO users (login, email, password_hash, role_id) VALUES (?, ?, ?, ?)";
            $db->execute($sql, [
                $data['login'],
                $data['email'],
                $password_hash,
                $data['role_id'] ?? 4 // member par défaut
            ]);
            
            $userId = (int) $db->lastInsertId();
            
            return self::findById($userId);
            
        } catch (Exception $e) {
            error_log("Erreur création utilisateur: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Mettre à jour un utilisateur
     */
    public static function update(int $id, array $data): bool
    {
        try {
            $db = new Database();
            $updates = [];
            $params = [];
            
            // Mettre à jour le login
            if (!empty($data['login'])) {
                // Vérifier si le login existe déjà
                $existing = self::findByLogin($data['login']);
                if ($existing && $existing['id'] !== $id) {
                    throw new Exception('Ce login existe déjà');
                }
                $updates[] = 'login = ?';
                $params[] = $data['login'];
            }
            
            // Mettre à jour l'email
            if (!empty($data['email'])) {
                // Vérifier si l'email existe déjà
                $sql = "SELECT id FROM users WHERE email = ? AND id != ?";
                $result = $db->query($sql, [$data['email'], $id]);
                if ($result) {
                    throw new Exception('Cet email existe déjà');
                }
                $updates[] = 'email = ?';
                $params[] = $data['email'];
            }
            
            // Mettre à jour le mot de passe
            if (!empty($data['password'])) {
                $updates[] = 'password_hash = ?';
                $params[] = password_hash($data['password'], PASSWORD_DEFAULT);
            }
            
            // Mettre à jour le rôle
            if (!empty($data['role_id'])) {
                $updates[] = 'role_id = ?';
                $params[] = $data['role_id'];
            }
            
            // Mettre à jour le statut actif
            if (isset($data['is_active'])) {
                $updates[] = 'is_active = ?';
                $params[] = $data['is_active'] ? 1 : 0;
            }
            
            if (empty($updates)) {
                return true; // Aucune modification
            }
            
            $updates[] = 'updated_at = NOW()';
            $params[] = $id;
            
            $sql = "UPDATE users SET " . implode(', ', $updates) . " WHERE id = ?";
            return $db->execute($sql, $params);
            
        } catch (Exception $e) {
            error_log("Erreur mise à jour utilisateur: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Supprimer un utilisateur
     */
    public static function delete(int $id): bool
    {
        try {
            $db = new Database();
            
            $sql = "DELETE FROM users WHERE id = ?";
            return $db->execute($sql, [$id]);
            
        } catch (Exception $e) {
            error_log("Erreur suppression utilisateur: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtenir tous les rôles disponibles
     */
    public static function getRoles(): array
    {
        $db = new Database();
        $sql = "SELECT * FROM roles ORDER BY id";
        return $db->query($sql);
    }
    
    /**
     * Compter les utilisateurs avec conditions
     */
    public static function countWithConditions(string $query, array $params = []): int
    {
        $db = new Database();
        $result = $db->query($query, $params);
        return $result ? (int)$result[0]['total'] : 0;
    }
    
    /**
     * Trouver des utilisateurs avec requête personnalisée
     */
    public static function findWithQuery(string $query, array $params = []): array
    {
        $db = new Database();
        return $db->query($query, $params);
    }
    
    /**
     * Vérifier si l'utilisateur a un rôle spécifique
     */
    public function hasRole(string $role): bool
    {
        return $this->role_name === $role;
    }
    
    /**
     * Vérifier les permissions
     */
    public function hasPermission(string $requiredRole): bool
    {
        $roleHierarchy = [
            'admin' => 4,
            'editor' => 3,
            'author' => 2,
            'member' => 1
        ];
        
        $userLevel = $roleHierarchy[$this->role_name] ?? 0;
        $requiredLevel = $roleHierarchy[$requiredRole] ?? 0;
        
        return $userLevel >= $requiredLevel;
    }
    
    /**
     * Convertir en tableau
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'login' => $this->login,
            'email' => $this->email,
            'role_id' => $this->role_id,
            'role_name' => $this->role_name,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'last_login' => $this->last_login
        ];
    }
    
    // Getters
    public function getId(): int { return $this->id; }
    public function getLogin(): string { return $this->login; }
    public function getEmail(): string { return $this->email; }
    public function getRoleId(): int { return $this->role_id; }
    public function getRoleName(): string { return $this->role_name; }
    public function getCreatedAt(): string { return $this->created_at; }
    public function getUpdatedAt(): ?string { return $this->updated_at; }
    public function getLastLogin(): ?string { return $this->last_login; }
}
