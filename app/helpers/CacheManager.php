<?php
declare(strict_types=1);

/**
 * Cache Manager - Système de cache intelligent
 * Support Redis, Memcached et cache fichier
 */

class CacheManager
{
    private static ?CacheManager $instance = null;
    private $driver;
    private string $prefix;
    private array $config;
    
    private function __construct()
    {
        $this->config = [
            'driver' => $_ENV['CACHE_DRIVER'] ?? 'file',
            'redis' => [
                'host' => $_ENV['REDIS_HOST'] ?? '127.0.0.1',
                'port' => (int)($_ENV['REDIS_PORT'] ?? 6379),
                'password' => $_ENV['REDIS_PASSWORD'] ?? null,
                'database' => (int)($_ENV['REDIS_DB'] ?? 0),
            ],
            'memcached' => [
                'host' => $_ENV['MEMCACHED_HOST'] ?? '127.0.0.1',
                'port' => (int)($_ENV['MEMCACHED_PORT'] ?? 11211),
            ],
            'file' => [
                'path' => __DIR__ . '/../../app/cache/',
                'default_ttl' => 3600, // 1 heure
            ]
        ];
        
        $this->prefix = $_ENV['CACHE_PREFIX'] ?? 'bvg_';
        $this->initializeDriver();
    }
    
    public static function getInstance(): CacheManager
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function initializeDriver(): void
    {
        switch ($this->config['driver']) {
            case 'redis':
                $this->initializeRedis();
                break;
            case 'memcached':
                $this->initializeMemcached();
                break;
            case 'file':
            default:
                $this->initializeFile();
                break;
        }
    }
    
    private function initializeRedis(): void
    {
        try {
            if (!class_exists('Redis')) {
                throw new Exception('Extension Redis non disponible');
            }
            
            $this->driver = new Redis();
            $this->driver->connect(
                $this->config['redis']['host'],
                $this->config['redis']['port']
            );
            
            if ($this->config['redis']['password']) {
                $this->driver->auth($this->config['redis']['password']);
            }
            
            $this->driver->select($this->config['redis']['database']);
            
        } catch (Exception $e) {
            error_log("Cache Redis non disponible: " . $e->getMessage());
            $this->initializeFile(); // Fallback vers cache fichier
        }
    }
    
    private function initializeMemcached(): void
    {
        try {
            if (!class_exists('Memcached')) {
                throw new Exception('Extension Memcached non disponible');
            }
            
            $this->driver = new Memcached();
            $this->driver->addServer(
                $this->config['memcached']['host'],
                $this->config['memcached']['port']
            );
            
        } catch (Exception $e) {
            error_log("Cache Memcached non disponible: " . $e->getMessage());
            $this->initializeFile(); // Fallback vers cache fichier
        }
    }
    
    private function initializeFile(): void
    {
        $cacheDir = $this->config['file']['path'];
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }
        $this->driver = 'file';
    }
    
    /**
     * Stocker une valeur en cache
     */
    public function put(string $key, $value, int $ttl = null): bool
    {
        $key = $this->prefix . $key;
        $ttl = $ttl ?? $this->config['file']['default_ttl'];
        
        switch ($this->config['driver']) {
            case 'redis':
                return $this->driver->setex($key, $ttl, serialize($value));
                
            case 'memcached':
                return $this->driver->set($key, $value, $ttl);
                
            case 'file':
            default:
                return $this->putFile($key, $value, $ttl);
        }
    }
    
    /**
     * Récupérer une valeur du cache
     */
    public function get(string $key, $default = null)
    {
        $key = $this->prefix . $key;
        
        switch ($this->config['driver']) {
            case 'redis':
                $value = $this->driver->get($key);
                return $value !== false ? unserialize($value) : $default;
                
            case 'memcached':
                $value = $this->driver->get($key);
                return $value !== false ? $value : $default;
                
            case 'file':
            default:
                return $this->getFile($key, $default);
        }
    }
    
    /**
     * Vérifier si une clé existe
     */
    public function has(string $key): bool
    {
        $key = $this->prefix . $key;
        
        switch ($this->config['driver']) {
            case 'redis':
                return $this->driver->exists($key) > 0;
                
            case 'memcached':
                $this->driver->get($key);
                return $this->driver->getResultCode() === Memcached::RES_SUCCESS;
                
            case 'file':
            default:
                return $this->hasFile($key);
        }
    }
    
    /**
     * Supprimer une clé du cache
     */
    public function forget(string $key): bool
    {
        $key = $this->prefix . $key;
        
        switch ($this->config['driver']) {
            case 'redis':
                return $this->driver->del($key) > 0;
                
            case 'memcached':
                return $this->driver->delete($key);
                
            case 'file':
            default:
                return $this->forgetFile($key);
        }
    }
    
    /**
     * Vider tout le cache
     */
    public function flush(): bool
    {
        switch ($this->config['driver']) {
            case 'redis':
                return $this->driver->flushDB();
                
            case 'memcached':
                return $this->driver->flush();
                
            case 'file':
            default:
                return $this->flushFile();
        }
    }
    
    /**
     * Cache avec callback (cache-aside pattern)
     */
    public function remember(string $key, callable $callback, int $ttl = null)
    {
        if ($this->has($key)) {
            return $this->get($key);
        }
        
        $value = $callback();
        $this->put($key, $value, $ttl);
        
        return $value;
    }
    
    // Méthodes pour le cache fichier
    private function putFile(string $key, $value, int $ttl): bool
    {
        $file = $this->config['file']['path'] . md5($key) . '.cache';
        $data = [
            'value' => $value,
            'expires' => time() + $ttl,
            'created' => time()
        ];
        
        // Utiliser un verrou pour éviter les conflits
        $lockFile = $file . '.lock';
        $lock = fopen($lockFile, 'w');
        if (!$lock || !flock($lock, LOCK_EX)) {
            return false;
        }
        
        $result = file_put_contents($file, serialize($data), LOCK_EX) !== false;
        
        flock($lock, LOCK_UN);
        fclose($lock);
        unlink($lockFile);
        
        return $result;
    }
    
    private function getFile(string $key, $default = null)
    {
        $file = $this->config['file']['path'] . md5($key) . '.cache';
        
        if (!file_exists($file)) {
            return $default;
        }
        
        $data = unserialize(file_get_contents($file));
        
        if ($data['expires'] < time()) {
            unlink($file);
            return $default;
        }
        
        return $data['value'];
    }
    
    private function hasFile(string $key): bool
    {
        $file = $this->config['file']['path'] . md5($key) . '.cache';
        
        if (!file_exists($file)) {
            return false;
        }
        
        $data = unserialize(file_get_contents($file));
        
        if ($data['expires'] < time()) {
            unlink($file);
            return false;
        }
        
        return true;
    }
    
    private function forgetFile(string $key): bool
    {
        $file = $this->config['file']['path'] . md5($key) . '.cache';
        
        if (file_exists($file)) {
            return unlink($file);
        }
        
        return true;
    }
    
    private function flushFile(): bool
    {
        $files = glob($this->config['file']['path'] . '*.cache');
        $success = true;
        
        foreach ($files as $file) {
            if (!unlink($file)) {
                $success = false;
            }
        }
        
        return $success;
    }
    
    /**
     * Statistiques du cache
     */
    public function getStats(): array
    {
        switch ($this->config['driver']) {
            case 'redis':
                return $this->driver->info();
                
            case 'memcached':
                return $this->driver->getStats();
                
            case 'file':
            default:
                return $this->getFileStats();
        }
    }
    
    private function getFileStats(): array
    {
        $files = glob($this->config['file']['path'] . '*.cache');
        $totalSize = 0;
        $expiredCount = 0;
        
        foreach ($files as $file) {
            $totalSize += filesize($file);
            
            $data = unserialize(file_get_contents($file));
            if ($data['expires'] < time()) {
                $expiredCount++;
            }
        }
        
        return [
            'driver' => 'file',
            'files_count' => count($files),
            'total_size' => $totalSize,
            'expired_count' => $expiredCount,
            'path' => $this->config['file']['path']
        ];
    }
}
