<?php
/**
 * Configuration des performances
 * Optimisations pour l'environnement de production
 */

return [
    // Cache
    'cache' => [
        'enabled' => true,
        'driver' => 'memory', // memory, file, redis
        'ttl' => 3600, // 1 heure
        'prefix' => 'bvg_',
        'memory_limit' => '64M'
    ],
    
    // Logs
    'logging' => [
        'enabled' => true,
        'level' => 'info', // debug, info, warning, error
        'max_files' => 30,
        'max_size' => '10M',
        'compress' => true
    ],
    
    // Middleware
    'middleware' => [
        'rate_limit' => [
            'enabled' => true,
            'requests_per_minute' => 60,
            'burst_limit' => 100
        ],
        'security' => [
            'enabled' => true,
            'cors' => true,
            'csp' => true,
            'hsts' => true
        ]
    ],
    
    // Database
    'database' => [
        'connection_pool' => 10,
        'query_cache' => true,
        'slow_query_log' => true,
        'slow_query_threshold' => 1000 // ms
    ],
    
    // Events
    'events' => [
        'async_processing' => false, // true pour traitement asynchrone
        'batch_size' => 100,
        'retry_attempts' => 3
    ],
    
    // Assets
    'assets' => [
        'minify' => true,
        'combine' => true,
        'versioning' => true,
        'cdn' => false
    ]
];
