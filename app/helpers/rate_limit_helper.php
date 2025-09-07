<?php
declare(strict_types=1);

/**
 * Helper de rate limiting
 * Gère les limitations de fréquence pour les uploads et actions sensibles
 */

class RateLimitHelper
{
    // Configuration des limites
    private const UPLOAD_LIMITS = [
        'uploads_per_hour' => 20,        // Max 20 uploads par heure
        'uploads_per_day' => 100,        // Max 100 uploads par jour
        'size_per_hour' => 100 * 1024 * 1024,  // Max 100MB par heure
        'size_per_day' => 500 * 1024 * 1024,   // Max 500MB par jour
    ];
    
    private const LOGIN_LIMITS = [
        'attempts_per_hour' => 10,       // Max 10 tentatives de connexion par heure
        'attempts_per_day' => 50,        // Max 50 tentatives par jour
    ];
    
    private const GENERAL_LIMITS = [
        'requests_per_minute' => 60,     // Max 60 requêtes par minute
        'requests_per_hour' => 1000,     // Max 1000 requêtes par heure
    ];
    
    /**
     * Vérifier les limites d'upload pour un utilisateur
     */
    public static function checkUploadLimits(int $userId = null, string $ip = null): array
    {
        $ip = $ip ?: self::getClientIp();
        $userId = $userId ?: 0; // 0 pour les utilisateurs non connectés
        
        // Vérifier les limites par heure
        $hourlyUploads = self::getUploadCount($userId, $ip, 'hour');
        $hourlySize = self::getUploadSize($userId, $ip, 'hour');
        
        if ($hourlyUploads >= self::UPLOAD_LIMITS['uploads_per_hour']) {
            return [
                'allowed' => false,
                'reason' => 'Limite d\'uploads par heure atteinte',
                'limit' => self::UPLOAD_LIMITS['uploads_per_hour'],
                'current' => $hourlyUploads,
                'reset_time' => self::getNextHour()
            ];
        }
        
        if ($hourlySize >= self::UPLOAD_LIMITS['size_per_hour']) {
            return [
                'allowed' => false,
                'reason' => 'Limite de taille par heure atteinte',
                'limit' => self::formatBytes(self::UPLOAD_LIMITS['size_per_hour']),
                'current' => self::formatBytes($hourlySize),
                'reset_time' => self::getNextHour()
            ];
        }
        
        // Vérifier les limites par jour
        $dailyUploads = self::getUploadCount($userId, $ip, 'day');
        $dailySize = self::getUploadSize($userId, $ip, 'day');
        
        if ($dailyUploads >= self::UPLOAD_LIMITS['uploads_per_day']) {
            return [
                'allowed' => false,
                'reason' => 'Limite d\'uploads par jour atteinte',
                'limit' => self::UPLOAD_LIMITS['uploads_per_day'],
                'current' => $dailyUploads,
                'reset_time' => self::getNextDay()
            ];
        }
        
        if ($dailySize >= self::UPLOAD_LIMITS['size_per_day']) {
            return [
                'allowed' => false,
                'reason' => 'Limite de taille par jour atteinte',
                'limit' => self::formatBytes(self::UPLOAD_LIMITS['size_per_day']),
                'current' => self::formatBytes($dailySize),
                'reset_time' => self::getNextDay()
            ];
        }
        
        return [
            'allowed' => true,
            'hourly_uploads' => $hourlyUploads,
            'hourly_size' => $hourlySize,
            'daily_uploads' => $dailyUploads,
            'daily_size' => $dailySize,
            'limits' => self::UPLOAD_LIMITS
        ];
    }
    
    /**
     * Enregistrer un upload pour le rate limiting
     */
    public static function recordUpload(int $userId = null, string $ip = null, int $fileSize = 0): void
    {
        $ip = $ip ?: self::getClientIp();
        $userId = $userId ?: 0;
        
        $data = [
            'user_id' => $userId,
            'ip' => $ip,
            'file_size' => $fileSize,
            'timestamp' => time(),
            'date' => date('Y-m-d'),
            'hour' => date('Y-m-d H:00:00')
        ];
        
        self::storeRateLimitData('upload', $data);
    }
    
    /**
     * Vérifier les limites de connexion
     */
    public static function checkLoginLimits(string $ip = null): array
    {
        $ip = $ip ?: self::getClientIp();
        
        $hourlyAttempts = self::getLoginAttempts($ip, 'hour');
        $dailyAttempts = self::getLoginAttempts($ip, 'day');
        
        if ($hourlyAttempts >= self::LOGIN_LIMITS['attempts_per_hour']) {
            return [
                'allowed' => false,
                'reason' => 'Trop de tentatives de connexion par heure',
                'limit' => self::LOGIN_LIMITS['attempts_per_hour'],
                'current' => $hourlyAttempts,
                'reset_time' => self::getNextHour()
            ];
        }
        
        if ($dailyAttempts >= self::LOGIN_LIMITS['attempts_per_day']) {
            return [
                'allowed' => false,
                'reason' => 'Trop de tentatives de connexion par jour',
                'limit' => self::LOGIN_LIMITS['attempts_per_day'],
                'current' => $dailyAttempts,
                'reset_time' => self::getNextDay()
            ];
        }
        
        return [
            'allowed' => true,
            'hourly_attempts' => $hourlyAttempts,
            'daily_attempts' => $dailyAttempts,
            'limits' => self::LOGIN_LIMITS
        ];
    }
    
    /**
     * Enregistrer une tentative de connexion
     */
    public static function recordLoginAttempt(string $ip = null, bool $success = false): void
    {
        $ip = $ip ?: self::getClientIp();
        
        $data = [
            'ip' => $ip,
            'success' => $success,
            'timestamp' => time(),
            'date' => date('Y-m-d'),
            'hour' => date('Y-m-d H:00:00')
        ];
        
        self::storeRateLimitData('login', $data);
    }
    
    /**
     * Obtenir l'adresse IP du client
     */
    private static function getClientIp(): string
    {
        $ipKeys = ['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR'];
        
        foreach ($ipKeys as $key) {
            if (!empty($_SERVER[$key])) {
                $ip = $_SERVER[$key];
                if (strpos($ip, ',') !== false) {
                    $ip = trim(explode(',', $ip)[0]);
                }
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    }
    
    /**
     * Obtenir le nombre d'uploads pour une période
     */
    private static function getUploadCount(int $userId, string $ip, string $period): int
    {
        $cacheKey = "upload_count_{$userId}_{$ip}_{$period}_" . self::getPeriodKey($period);
        $data = self::getRateLimitData($cacheKey);
        
        return $data ? (int)$data : 0;
    }
    
    /**
     * Obtenir la taille totale uploadée pour une période
     */
    private static function getUploadSize(int $userId, string $ip, string $period): int
    {
        $cacheKey = "upload_size_{$userId}_{$ip}_{$period}_" . self::getPeriodKey($period);
        $data = self::getRateLimitData($cacheKey);
        
        return $data ? (int)$data : 0;
    }
    
    /**
     * Obtenir le nombre de tentatives de connexion
     */
    private static function getLoginAttempts(string $ip, string $period): int
    {
        $cacheKey = "login_attempts_{$ip}_{$period}_" . self::getPeriodKey($period);
        $data = self::getRateLimitData($cacheKey);
        
        return $data ? (int)$data : 0;
    }
    
    /**
     * Obtenir la clé de période pour le cache
     */
    private static function getPeriodKey(string $period): string
    {
        switch ($period) {
            case 'hour':
                return date('Y-m-d-H');
            case 'day':
                return date('Y-m-d');
            case 'minute':
                return date('Y-m-d-H-i');
            default:
                return date('Y-m-d-H');
        }
    }
    
    /**
     * Obtenir l'heure de reset (prochaine heure)
     */
    private static function getNextHour(): string
    {
        return date('Y-m-d H:00:00', strtotime('+1 hour'));
    }
    
    /**
     * Obtenir l'heure de reset (prochain jour)
     */
    private static function getNextDay(): string
    {
        return date('Y-m-d 00:00:00', strtotime('+1 day'));
    }
    
    /**
     * Stocker les données de rate limiting
     */
    private static function storeRateLimitData(string $type, array $data): void
    {
        // Utiliser un système de cache simple basé sur des fichiers
        $cacheDir = __DIR__ . '/../../cache/rate_limit';
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }
        
        $cacheFile = $cacheDir . '/' . $type . '_' . md5(serialize($data)) . '.json';
        file_put_contents($cacheFile, json_encode($data));
        
        // Nettoyer les anciens fichiers (plus de 24h)
        self::cleanOldCacheFiles($cacheDir);
    }
    
    /**
     * Récupérer les données de rate limiting
     */
    private static function getRateLimitData(string $cacheKey): mixed
    {
        $cacheDir = __DIR__ . '/../../cache/rate_limit';
        $cacheFile = $cacheDir . '/' . $cacheKey . '.json';
        
        if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < 3600) { // 1 heure
            return json_decode(file_get_contents($cacheFile), true);
        }
        
        return null;
    }
    
    /**
     * Nettoyer les anciens fichiers de cache
     */
    private static function cleanOldCacheFiles(string $cacheDir): void
    {
        $files = glob($cacheDir . '/*.json');
        $cutoff = time() - 86400; // 24 heures
        
        foreach ($files as $file) {
            if (filemtime($file) < $cutoff) {
                unlink($file);
            }
        }
    }
    
    /**
     * Formater les bytes en format lisible
     */
    private static function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }
    
    /**
     * Obtenir les statistiques de rate limiting pour un utilisateur
     */
    public static function getStats(int $userId = null, string $ip = null): array
    {
        $ip = $ip ?: self::getClientIp();
        $userId = $userId ?: 0;
        
        return [
            'user_id' => $userId,
            'ip' => $ip,
            'upload_limits' => self::checkUploadLimits($userId, $ip),
            'login_limits' => self::checkLoginLimits($ip),
            'timestamp' => time(),
            'date' => date('Y-m-d H:i:s')
        ];
    }
}
