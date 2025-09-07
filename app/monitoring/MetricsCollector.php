<?php
declare(strict_types=1);

/**
 * Collecteur de métriques en temps réel
 * Surveillance des performances de l'application
 */

class MetricsCollector
{
    private static array $metrics = [];
    private static array $counters = [];
    private static array $timers = [];
    private static array $gauges = [];
    
    /**
     * Enregistrer une métrique de type counter
     */
    public static function increment(string $name, int $value = 1, array $tags = []): void
    {
        $key = self::buildKey($name, $tags);
        self::$counters[$key] = (self::$counters[$key] ?? 0) + $value;
        self::recordMetric('counter', $name, $value, $tags);
    }
    
    /**
     * Enregistrer une métrique de type gauge
     */
    public static function gauge(string $name, float $value, array $tags = []): void
    {
        $key = self::buildKey($name, $tags);
        self::$gauges[$key] = $value;
        self::recordMetric('gauge', $name, $value, $tags);
    }
    
    /**
     * Enregistrer une métrique de type timer
     */
    public static function timer(string $name, float $duration, array $tags = []): void
    {
        $key = self::buildKey($name, $tags);
        if (!isset(self::$timers[$key])) {
            self::$timers[$key] = [];
        }
        self::$timers[$key][] = $duration;
        self::recordMetric('timer', $name, $duration, $tags);
    }
    
    /**
     * Mesurer le temps d'exécution d'une fonction
     */
    public static function measure(string $name, callable $callback, array $tags = []): mixed
    {
        $start = microtime(true);
        $result = $callback();
        $duration = (microtime(true) - $start) * 1000; // en millisecondes
        
        self::timer($name, $duration, $tags);
        return $result;
    }
    
    /**
     * Obtenir toutes les métriques
     */
    public static function getAllMetrics(): array
    {
        return [
            'counters' => self::$counters,
            'gauges' => self::$gauges,
            'timers' => self::calculateTimerStats(),
            'raw_metrics' => self::$metrics
        ];
    }
    
    /**
     * Obtenir les statistiques des timers
     */
    private static function calculateTimerStats(): array
    {
        $stats = [];
        
        foreach (self::$timers as $key => $values) {
            if (empty($values)) continue;
            
            $stats[$key] = [
                'count' => count($values),
                'min' => min($values),
                'max' => max($values),
                'avg' => array_sum($values) / count($values),
                'p95' => self::percentile($values, 95),
                'p99' => self::percentile($values, 99)
            ];
        }
        
        return $stats;
    }
    
    /**
     * Calculer un percentile
     */
    private static function percentile(array $values, float $percentile): float
    {
        sort($values);
        $index = ($percentile / 100) * (count($values) - 1);
        
        if (floor($index) == $index) {
            return $values[$index];
        }
        
        $lower = $values[floor($index)];
        $upper = $values[ceil($index)];
        $fraction = $index - floor($index);
        
        return $lower + ($upper - $lower) * $fraction;
    }
    
    /**
     * Construire une clé unique pour les métriques
     */
    private static function buildKey(string $name, array $tags): string
    {
        if (empty($tags)) {
            return $name;
        }
        
        ksort($tags);
        return $name . ':' . http_build_query($tags, '', ',');
    }
    
    /**
     * Enregistrer une métrique brute
     */
    private static function recordMetric(string $type, string $name, float $value, array $tags): void
    {
        self::$metrics[] = [
            'timestamp' => microtime(true),
            'type' => $type,
            'name' => $name,
            'value' => $value,
            'tags' => $tags
        ];
        
        // Garder seulement les 1000 dernières métriques
        if (count(self::$metrics) > 1000) {
            self::$metrics = array_slice(self::$metrics, -1000);
        }
    }
    
    /**
     * Réinitialiser toutes les métriques
     */
    public static function reset(): void
    {
        self::$metrics = [];
        self::$counters = [];
        self::$timers = [];
        self::$gauges = [];
    }
    
    /**
     * Obtenir les métriques système
     */
    public static function getSystemMetrics(): array
    {
        return [
            'memory_usage' => memory_get_usage(true),
            'memory_peak' => memory_get_peak_usage(true),
            'memory_limit' => ini_get('memory_limit'),
            'execution_time' => microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'],
            'php_version' => PHP_VERSION,
            'timestamp' => time()
        ];
    }
}
