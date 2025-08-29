<?php
declare(strict_types=1);

/**
 * Classe de gestion de la base de données
 * Utilise PDO avec des requêtes préparées pour la sécurité
 */

require_once __DIR__ . '/../config/config.php';

class Database
{
    private static ?PDO $instance = null;
    
    /**
     * Obtenir l'instance PDO (singleton)
     */
    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            self::$instance = self::createConnection();
        }
        return self::$instance;
    }
    
    /**
     * Créer la connexion PDO
     */
    private static function createConnection(): PDO
    {
        $host = Config::get('DB_HOST');
        $dbname = Config::get('DB_NAME');
        $username = Config::get('DB_USER');
        $password = Config::get('DB_PASS');
        
        $dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";
        
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
        ];
        
        try {
            $pdo = new PDO($dsn, $username, $password, $options);
            return $pdo;
        } catch (PDOException $e) {
            throw new Exception("Erreur de connexion à la base de données: " . $e->getMessage());
        }
    }
    
    /**
     * Exécuter une requête SELECT
     */
    public static function query(string $sql, array $params = []): array
    {
        $pdo = self::getInstance();
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    /**
     * Exécuter une requête SELECT et retourner une seule ligne
     */
    public static function queryOne(string $sql, array $params = []): ?array
    {
        $pdo = self::getInstance();
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result ?: null;
    }
    
    /**
     * Exécuter une requête INSERT/UPDATE/DELETE
     */
    public static function execute(string $sql, array $params = []): int
    {
        $pdo = self::getInstance();
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }
    
    /**
     * Obtenir l'ID de la dernière insertion
     */
    public static function lastInsertId(): int
    {
        return (int)self::getInstance()->lastInsertId();
    }
    
    /**
     * Commencer une transaction
     */
    public static function beginTransaction(): void
    {
        self::getInstance()->beginTransaction();
    }
    
    /**
     * Valider une transaction
     */
    public static function commit(): void
    {
        self::getInstance()->commit();
    }
    
    /**
     * Annuler une transaction
     */
    public static function rollback(): void
    {
        self::getInstance()->rollback();
    }
}
