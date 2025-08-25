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
    public static function findById(int $id): ?self
    {
        $sql = "SELECT u.*, r.name as role_name 
                FROM users u 
                JOIN roles r ON u.role_id = r.id 
                WHERE u.id = ?";
        
        $data = Database::queryOne($sql, [$id]);
        
        return $data ? new self($data) : null;
    }
    
    /**
     * Trouver un utilisateur par login ou email
     */
    public static function findByLogin(string $login): ?self
    {
        $sql = "SELECT u.*, r.name as role_name 
                FROM users u 
                JOIN roles r ON u.role_id = r.id 
                WHERE u.login = ? OR u.email = ?";
        
        $data = Database::queryOne($sql, [$login, $login]);
        
        return $data ? new self($data) : null;
    }
    
    /**
     * Trouver tous les utilisateurs
     */
    public static function findAll(): array
    {
        $sql = "SELECT u.*, r.name as role_name 
                FROM users u 
                JOIN roles r ON u.role_id = r.id 
                ORDER BY u.created_at DESC";
        
        $users = Database::query($sql);
        
        return array_map(fn($data) => new self($data), $users);
    }
    
    /**
     * Créer un nouvel utilisateur
     */
    public static function create(array $data): ?self
    {
        try {
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
            if (Database::queryOne($sql, [$data['email']])) {
                throw new Exception('Cet email existe déjà');
            }
            
            // Hasher le mot de passe
            $password_hash = Auth::hashPassword($data['password']);
            
            // Insérer l'utilisateur
            $sql = "INSERT INTO users (login, email, password_hash, role_id) VALUES (?, ?, ?, ?)";
            Database::execute($sql, [
                $data['login'],
                $data['email'],
                $password_hash,
                $data['role_id'] ?? 4 // member par défaut
            ]);
            
            $userId = (int) Database::lastInsertId();
            
            // Logger l'activité
            Auth::logActivity(Auth::getUserId(), 'user_create', "Création de l'utilisateur {$data['login']}");
            
            return self::findById($userId);
            
        } catch (Exception $e) {
            error_log("Erreur création utilisateur: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Mettre à jour un utilisateur
     */
    public function update(array $data): bool
    {
        try {
            $updates = [];
            $params = [];
            
            // Mettre à jour le login
            if (!empty($data['login']) && $data['login'] !== $this->login) {
                // Vérifier si le login existe déjà
                $existing = self::findByLogin($data['login']);
                if ($existing && $existing->getId() !== $this->id) {
                    throw new Exception('Ce login existe déjà');
                }
                $updates[] = 'login = ?';
                $params[] = $data['login'];
            }
            
            // Mettre à jour l'email
            if (!empty($data['email']) && $data['email'] !== $this->email) {
                // Vérifier si l'email existe déjà
                $sql = "SELECT id FROM users WHERE email = ? AND id != ?";
                if (Database::queryOne($sql, [$data['email'], $this->id])) {
                    throw new Exception('Cet email existe déjà');
                }
                $updates[] = 'email = ?';
                $params[] = $data['email'];
            }
            
            // Mettre à jour le mot de passe
            if (!empty($data['password'])) {
                $updates[] = 'password_hash = ?';
                $params[] = Auth::hashPassword($data['password']);
            }
            
            // Mettre à jour le rôle
            if (!empty($data['role_id']) && $data['role_id'] != $this->role_id) {
                $updates[] = 'role_id = ?';
                $params[] = $data['role_id'];
            }
            
            if (empty($updates)) {
                return true; // Aucune modification
            }
            
            $updates[] = 'updated_at = NOW()';
            $params[] = $this->id;
            
            $sql = "UPDATE users SET " . implode(', ', $updates) . " WHERE id = ?";
            Database::execute($sql, $params);
            
            // Logger l'activité
            Auth::logActivity(Auth::getUserId(), 'user_update', "Modification de l'utilisateur {$this->login}");
            
            // Recharger les données
            $updated = self::findById($this->id);
            if ($updated) {
                $this->hydrate($updated->toArray());
            }
            
            return true;
            
        } catch (Exception $e) {
            error_log("Erreur mise à jour utilisateur: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Supprimer un utilisateur
     */
    public function delete(): bool
    {
        try {
            // Empêcher la suppression de l'utilisateur connecté
            if ($this->id === Auth::getUserId()) {
                throw new Exception('Impossible de supprimer votre propre compte');
            }
            
            $sql = "DELETE FROM users WHERE id = ?";
            Database::execute($sql, [$this->id]);
            
            // Logger l'activité
            Auth::logActivity(Auth::getUserId(), 'user_delete', "Suppression de l'utilisateur {$this->login}");
            
            return true;
            
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
        $sql = "SELECT * FROM roles ORDER BY id";
        return Database::query($sql);
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
