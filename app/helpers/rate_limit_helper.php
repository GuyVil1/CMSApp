<?php
declare(strict_types=1);

/**
 * Helper de rate limiting avancé
 * Protection contre les attaques DDoS et brute force
 */
class RateLimitHelper
{
    private static array $limits = [
        // Limites générales
        'default' => ['requests' => 100, 'window' => 3600], // 100 req/heure
        
        // Limites par route
        'auth' => ['requests' => 5, 'window' => 300], // 5 req/5min pour l'auth
        'admin' => ['requests' => 50, 'window' => 3600], // 50 req/heure pour admin
        'api' => ['requests' => 200, 'window' => 3600], // 200 req/heure pour API
        'upload' => ['requests' => 10, 'window' => 3600], // 10 uploads/heure
        'search' => ['requests' => 30, 'window' => 300], // 30 recherches/5min
        
        // Limites strictes pour les actions sensibles
        'login' => ['requests' => 3, 'window' => 300], // 3 tentatives/5min
        'register' => ['requests' => 2, 'window' => 3600], // 2 inscriptions/heure
        'password_reset' => ['requests' => 1, 'window' => 3600], // 1 reset/heure
    ];
    
    private static array $ipBlacklist = [];
    private static array $ipWhitelist = [];
    
    /**
     * Vérifier le rate limit pour une IP et une route
     */
    public static function checkRateLimit(string $ip, string $route = 'default'): array
    {
        // Vérifier la whitelist
        if (self::isWhitelisted($ip)) {
            return ['allowed' => true, 'remaining' => 999, 'reset' => time() + 3600];
        }
        
        // Vérifier la blacklist
        if (self::isBlacklisted($ip)) {
            return ['allowed' => false, 'remaining' => 0, 'reset' => time() + 3600, 'reason' => 'IP blacklisted'];
        }
        
        // Déterminer les limites
        $limits = self::getLimits($route);
        $key = "rate_limit:{$ip}:{$route}";
        
        // Récupérer les données actuelles
        $data = self::getRateLimitData($key);
        
        // Vérifier si la fenêtre de temps est expirée
        if ($data['reset'] < time()) {
            $data = [
                'count' => 0,
                'reset' => time() + $limits['window']
            ];
        }
        
        // Vérifier si la limite est atteinte
        if ($data['count'] >= $limits['requests']) {
            // Ajouter à la blacklist temporaire si trop de violations
            self::handleViolation($ip, $route);
            
            return [
                'allowed' => false,
                'remaining' => 0,
                'reset' => $data['reset'],
                'reason' => 'Rate limit exceeded'
            ];
        }
        
        // Incrémenter le compteur
        $data['count']++;
        self::setRateLimitData($key, $data);
        
        return [
            'allowed' => true,
            'remaining' => $limits['requests'] - $data['count'],
            'reset' => $data['reset']
        ];
    }
    
    /**
     * Enregistrer une requête
     */
    public static function recordRequest(string $ip, string $route = 'default'): void
    {
        $key = "rate_limit:{$ip}:{$route}";
        $data = self::getRateLimitData($key);
        
        if ($data['reset'] < time()) {
            $limits = self::getLimits($route);
            $data = [
                'count' => 0,
                'reset' => time() + $limits['window']
            ];
        }
        
        $data['count']++;
        self::setRateLimitData($key, $data);
    }
    
    /**
     * Gérer les violations de rate limit
     */
    private static function handleViolation(string $ip, string $route): void
    {
        $violationKey = "violations:{$ip}";
        $violations = self::getRateLimitData($violationKey);
        
        if ($violations['reset'] < time()) {
            $violations = [
                'count' => 0,
                'reset' => time() + 3600 // 1 heure
            ];
        }
        
        $violations['count']++;
        self::setRateLimitData($violationKey, $violations);
        
        // Blacklister temporairement si trop de violations
        if ($violations['count'] >= 10) {
            self::addToBlacklist($ip, 3600); // 1 heure
        }
        
        // Logger la violation
        self::logViolation($ip, $route, $violations['count']);
    }
    
    /**
     * Ajouter une IP à la blacklist
     */
    public static function addToBlacklist(string $ip, int $duration = 3600): void
    {
        self::$ipBlacklist[$ip] = time() + $duration;
        self::setRateLimitData("blacklist:{$ip}", ['expires' => time() + $duration]);
    }
    
    /**
     * Ajouter une IP à la whitelist
     */
    public static function addToWhitelist(string $ip): void
    {
        self::$ipWhitelist[] = $ip;
    }
    
    /**
     * Vérifier si une IP est blacklistée
     */
    private static function isBlacklisted(string $ip): bool
    {
        $data = self::getRateLimitData("blacklist:{$ip}");
        return $data && isset($data['expires']) && $data['expires'] > time();
    }
    
    /**
     * Vérifier si une IP est whitelistée
     */
    private static function isWhitelisted(string $ip): bool
    {
        return in_array($ip, self::$ipWhitelist);
    }
    
    /**
     * Obtenir les limites pour une route
     */
    private static function getLimits(string $route): array
    {
        return self::$limits[$route] ?? self::$limits['default'];
    }
    
    /**
     * Récupérer les données de rate limit
     */
    private static function getRateLimitData(string $key): array
    {
        // Nettoyer la clé pour le nom de fichier
        $safeKey = str_replace([':', '/', '\\'], '_', $key);
        $cacheFile = dirname(__DIR__, 2) . "/cache/rate_limit/{$safeKey}.json";
        
        if (file_exists($cacheFile)) {
            $content = file_get_contents($cacheFile);
            if ($content !== false) {
                $data = json_decode($content, true);
                if ($data && isset($data['reset']) && $data['reset'] > time()) {
                    return $data;
                }
            }
        }
        
        return ['count' => 0, 'reset' => time() + 3600];
    }
    
    /**
     * Sauvegarder les données de rate limit
     */
    private static function setRateLimitData(string $key, array $data): void
    {
        // Nettoyer la clé pour le nom de fichier
        $safeKey = str_replace([':', '/', '\\'], '_', $key);
        $cacheDir = dirname(__DIR__, 2) . "/cache/rate_limit";
        if (!is_dir($cacheDir)) {
            if (!mkdir($cacheDir, 0755, true)) {
                error_log("Impossible de créer le répertoire cache: {$cacheDir}");
                return;
            }
        }
        
        $cacheFile = "{$cacheDir}/{$safeKey}.json";
        if (file_put_contents($cacheFile, json_encode($data)) === false) {
            error_log("Impossible d'écrire le fichier cache: {$cacheFile}");
        }
    }
    
    /**
     * Logger les violations
     */
    private static function logViolation(string $ip, string $route, int $violationCount): void
    {
        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'ip' => $ip,
            'route' => $route,
            'violation_count' => $violationCount,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
        ];
        
        $logFile = dirname(__DIR__, 2) . "/logs/security/rate_limit_violations.log";
        $logDir = dirname($logFile);
        
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        file_put_contents($logFile, json_encode($logEntry) . "\n", FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Nettoyer les anciens fichiers de cache
     */
    public static function cleanup(): void
    {
        $cacheDir = dirname(__DIR__, 2) . "/cache/rate_limit";
        if (!is_dir($cacheDir)) {
            return;
        }
        
        $files = glob("{$cacheDir}/*.json");
        $now = time();
        
        foreach ($files as $file) {
            $content = file_get_contents($file);
            if ($content !== false) {
                $data = json_decode($content, true);
                if ($data && isset($data['reset']) && $data['reset'] < $now) {
                    unlink($file);
                }
            }
        }
    }
    
    /**
     * Obtenir les statistiques de rate limit
     */
    public static function getStats(): array
    {
        $stats = [
            'blacklisted_ips' => count(self::$ipBlacklist),
            'whitelisted_ips' => count(self::$ipWhitelist),
            'active_limits' => count(self::$limits)
        ];
        
        // Compter les violations récentes
        $logFile = dirname(__DIR__, 2) . "/logs/security/rate_limit_violations.log";
        if (file_exists($logFile)) {
            $lines = file($logFile, FILE_IGNORE_NEW_LINES);
            $recentViolations = 0;
            $oneHourAgo = time() - 3600;
            
            foreach ($lines as $line) {
                $data = json_decode($line, true);
                if ($data && strtotime($data['timestamp']) > $oneHourAgo) {
                    $recentViolations++;
                }
            }
            
            $stats['recent_violations'] = $recentViolations;
        }
        
        return $stats;
    }
}