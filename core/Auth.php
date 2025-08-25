<?php
declare(strict_types=1);

/**
 * Classe de gestion de l'authentification
 * Sessions sécurisées, gestion des rôles, protection CSRF
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/Database.php';

class Auth
{
    private static ?array $user = null;
    
    /**
     * Initialiser les sessions sécurisées
     */
    public static function init(): void
    {
        // Configuration des sessions sécurisées (doit être fait avant session_start())
        if (session_status() === PHP_SESSION_NONE) {
            ini_set('session.cookie_httponly', '1');
            ini_set('session.cookie_secure', Config::isProduction() ? '1' : '0');
            ini_set('session.cookie_samesite', 'Lax');
            ini_set('session.use_strict_mode', '1');
        }
    }
    
    /**
     * Initialiser la session après session_start()
     */
    public static function initSession(): void
    {
        // Régénérer l'ID de session si nécessaire
        if (!isset($_SESSION['auth_initialized'])) {
            session_regenerate_id(true);
            $_SESSION['auth_initialized'] = true;
        }
    }
    
    /**
     * Authentifier un utilisateur
     */
    public static function login(string $login, string $password): bool
    {
        try {
            // Récupérer l'utilisateur
            $sql = "SELECT u.*, r.name as role_name 
                    FROM users u 
                    JOIN roles r ON u.role_id = r.id 
                    WHERE u.login = ? OR u.email = ?";
            
            $user = Database::queryOne($sql, [$login, $login]);
            
            if (!$user) {
                return false;
            }
            
            // Vérifier le mot de passe
            if (!password_verify($password, $user['password_hash'])) {
                return false;
            }
            
            // Régénérer l'ID de session pour la sécurité
            session_regenerate_id(true);
            
            // Stocker les informations utilisateur
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_login'] = $user['login'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role_name'];
            $_SESSION['user_role_id'] = $user['role_id'];
            $_SESSION['logged_in'] = true;
            $_SESSION['login_time'] = time();
            
            // Mettre à jour la dernière connexion
            Database::execute(
                "UPDATE users SET last_login = NOW() WHERE id = ?",
                [$user['id']]
            );
            
            // Logger l'activité
            self::logActivity($user['id'], 'login', 'Connexion réussie');
            
            return true;
            
        } catch (Exception $e) {
            error_log("Erreur d'authentification: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Déconnecter l'utilisateur
     */
    public static function logout(): void
    {
        if (isset($_SESSION['user_id'])) {
            self::logActivity($_SESSION['user_id'], 'logout', 'Déconnexion');
        }
        
        // Détruire la session
        session_destroy();
        
        // Régénérer l'ID de session
        session_start();
        session_regenerate_id(true);
    }
    
    /**
     * Vérifier si l'utilisateur est connecté
     */
    public static function isLoggedIn(): bool
    {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }
    
    /**
     * Obtenir l'utilisateur connecté
     */
    public static function getUser(): ?array
    {
        if (!self::isLoggedIn()) {
            return null;
        }
        
        if (self::$user === null) {
            $sql = "SELECT u.*, r.name as role_name 
                    FROM users u 
                    JOIN roles r ON u.role_id = r.id 
                    WHERE u.id = ?";
            
            self::$user = Database::queryOne($sql, [$_SESSION['user_id']]);
        }
        
        return self::$user;
    }
    
    /**
     * Obtenir l'ID de l'utilisateur connecté
     */
    public static function getUserId(): ?int
    {
        return $_SESSION['user_id'] ?? null;
    }
    
    /**
     * Obtenir le rôle de l'utilisateur connecté
     */
    public static function getUserRole(): ?string
    {
        return $_SESSION['user_role'] ?? null;
    }
    
    /**
     * Vérifier si l'utilisateur a un rôle spécifique
     */
    public static function hasRole(string $role): bool
    {
        return self::getUserRole() === $role;
    }
    
    /**
     * Vérifier si l'utilisateur a au moins un des rôles spécifiés
     */
    public static function hasAnyRole(array $roles): bool
    {
        $userRole = self::getUserRole();
        return in_array($userRole, $roles);
    }
    
    /**
     * Vérifier si l'utilisateur a tous les rôles spécifiés
     */
    public static function hasAllRoles(array $roles): bool
    {
        $userRole = self::getUserRole();
        foreach ($roles as $role) {
            if ($userRole !== $role) {
                return false;
            }
        }
        return true;
    }
    
    /**
     * Vérifier les permissions (admin > editor > author > member)
     */
    public static function hasPermission(string $requiredRole): bool
    {
        $userRole = self::getUserRole();
        $roleHierarchy = [
            'admin' => 4,
            'editor' => 3,
            'author' => 2,
            'member' => 1
        ];
        
        $userLevel = $roleHierarchy[$userRole] ?? 0;
        $requiredLevel = $roleHierarchy[$requiredRole] ?? 0;
        
        return $userLevel >= $requiredLevel;
    }
    
    /**
     * Générer un token CSRF
     */
    public static function generateCsrfToken(): string
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Vérifier un token CSRF
     */
    public static function verifyCsrfToken(string $token): bool
    {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
    
    /**
     * Régénérer le token CSRF
     */
    public static function regenerateCsrfToken(): void
    {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    
    /**
     * Logger une activité
     */
    public static function logActivity(?int $userId, string $action, ?string $details = null): void
    {
        try {
            Database::execute(
                "INSERT INTO activity_logs (user_id, action, details, ip_address) VALUES (?, ?, ?, ?)",
                [$userId, $action, $details, $_SERVER['REMOTE_ADDR'] ?? null]
            );
        } catch (Exception $e) {
            error_log("Erreur lors du log d'activité: " . $e->getMessage());
        }
    }
    
    /**
     * Créer un hash de mot de passe
     */
    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }
    
    /**
     * Vérifier si un mot de passe est valide
     */
    public static function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }
    
    /**
     * Rediriger si non connecté
     */
    public static function requireLogin(): void
    {
        if (!self::isLoggedIn()) {
            header('Location: /login');
            exit;
        }
    }
    
    /**
     * Rediriger si pas le bon rôle
     * Accepte soit une chaîne, soit un tableau de rôles
     */
    public static function requireRole(string|array $role): void
    {
        self::requireLogin();
        
        if (is_array($role)) {
            // Vérifier si l'utilisateur a au moins un des rôles requis
            $hasRole = false;
            foreach ($role as $r) {
                if (self::hasRole($r)) {
                    $hasRole = true;
                    break;
                }
            }
            if (!$hasRole) {
                header('Location: /403');
                exit;
            }
        } else {
            // Vérifier un seul rôle
            if (!self::hasRole($role)) {
                header('Location: /403');
                exit;
            }
        }
    }
    
    /**
     * Rediriger si pas les bonnes permissions
     */
    public static function requirePermission(string $role): void
    {
        self::requireLogin();
        
        if (!self::hasPermission($role)) {
            header('Location: /403');
            exit;
        }
    }
}
