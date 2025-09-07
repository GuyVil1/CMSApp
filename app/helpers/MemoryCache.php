<?php
declare(strict_types=1);

/**
 * Cache mémoire ultra-rapide
 * Pour les données fréquemment accédées
 */

class MemoryCache
{
    private static array $cache = [];
    private static array $expires = [];
    private static int $hits = 0;
    private static int $misses = 0;
    
    /**
     * Stocker en mémoire
     */
    public static function put(string $key, $value, int $ttl = 3600): void
    {
        self::$cache[$key] = $value;
        self::$expires[$key] = time() + $ttl;
    }
    
    /**
     * Récupérer de la mémoire
     */
    public static function get(string $key, $default = null)
    {
        // Vérifier l'expiration
        if (isset(self::$expires[$key]) && self::$expires[$key] < time()) {
            self::forget($key);
            self::$misses++;
            return $default;
        }
        
        if (isset(self::$cache[$key])) {
            self::$hits++;
            return self::$cache[$key];
        }
        
        self::$misses++;
        return $default;
    }
    
    /**
     * Vérifier si existe
     */
    public static function has(string $key): bool
    {
        if (isset(self::$expires[$key]) && self::$expires[$key] < time()) {
            self::forget($key);
            return false;
        }
        
        return isset(self::$cache[$key]);
    }
    
    /**
     * Supprimer
     */
    public static function forget(string $key): void
    {
        unset(self::$cache[$key]);
        unset(self::$expires[$key]);
    }
    
    /**
     * Vider le cache
     */
    public static function flush(): void
    {
        self::$cache = [];
        self::$expires = [];
        self::$hits = 0;
        self::$misses = 0;
    }
    
    /**
     * Cache avec callback
     */
    public static function remember(string $key, callable $callback, int $ttl = 3600)
    {
        if (self::has($key)) {
            return self::get($key);
        }
        
        $value = $callback();
        self::put($key, $value, $ttl);
        
        return $value;
    }
    
    /**
     * Statistiques
     */
    public static function getStats(): array
    {
        $total = self::$hits + self::$misses;
        $hitRate = $total > 0 ? (self::$hits / $total) * 100 : 0;
        
        return [
            'driver' => 'memory',
            'hits' => self::$hits,
            'misses' => self::$misses,
            'hit_rate' => round($hitRate, 2) . '%',
            'items_count' => count(self::$cache),
            'memory_usage' => memory_get_usage(true)
        ];
    }
    
    /**
     * Nettoyer les éléments expirés
     */
    public static function cleanExpired(): int
    {
        $cleaned = 0;
        $now = time();
        
        foreach (self::$expires as $key => $expire) {
            if ($expire < $now) {
                self::forget($key);
                $cleaned++;
            }
        }
        
        return $cleaned;
    }
}
