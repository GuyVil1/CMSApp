<?php
declare(strict_types=1);

/**
 * Tests simples sans PHPUnit
 */

require_once __DIR__ . '/SimpleTestRunner.php';
require_once __DIR__ . '/../app/helpers/MemoryCache.php';
require_once __DIR__ . '/../app/monitoring/MetricsCollector.php';

// Créer le runner de tests
$runner = new SimpleTestRunner();

// Tests MemoryCache
$runner->addTest('MemoryCache - Put and Get', function() use ($runner) {
    MemoryCache::flush();
    
    $key = 'test_key';
    $value = 'test_value';
    
    MemoryCache::put($key, $value);
    $result = MemoryCache::get($key);
    
    $runner->assertEquals($value, $result);
    $runner->assertTrue(MemoryCache::has($key));
    
    return true;
});

$runner->addTest('MemoryCache - Default Value', function() use ($runner) {
    MemoryCache::flush();
    
    $defaultValue = 'default';
    $result = MemoryCache::get('nonexistent_key', $defaultValue);
    
    $runner->assertEquals($defaultValue, $result);
    $runner->assertFalse(MemoryCache::has('nonexistent_key'));
    
    return true;
});

$runner->addTest('MemoryCache - Remember', function() use ($runner) {
    MemoryCache::flush();
    
    $key = 'remember_key';
    $value = 'remember_value';
    $callCount = 0;
    
    $callback = function() use ($value, &$callCount) {
        $callCount++;
        return $value;
    };
    
    // Premier appel
    $result1 = MemoryCache::remember($key, $callback, 3600);
    $runner->assertEquals($value, $result1);
    $runner->assertEquals(1, $callCount);
    
    // Deuxième appel
    $result2 = MemoryCache::remember($key, $callback, 3600);
    $runner->assertEquals($value, $result2);
    $runner->assertEquals(1, $callCount); // Toujours 1
    
    return true;
});

$runner->addTest('MemoryCache - Stats', function() use ($runner) {
    MemoryCache::flush();
    
    MemoryCache::put('key1', 'value1');
    MemoryCache::get('key1'); // hit
    MemoryCache::get('key1'); // hit
    MemoryCache::get('nonexistent'); // miss
    
    $stats = MemoryCache::getStats();
    
    $runner->assertIsArray($stats);
    $runner->assertArrayHasKey('hits', $stats);
    $runner->assertArrayHasKey('misses', $stats);
    $runner->assertArrayHasKey('hit_rate', $stats);
    $runner->assertArrayHasKey('items_count', $stats);
    
    $runner->assertEquals(2, $stats['hits']);
    $runner->assertEquals(1, $stats['misses']);
    $runner->assertEquals(1, $stats['items_count']);
    
    return true;
});

// Tests MetricsCollector
$runner->addTest('MetricsCollector - Increment', function() use ($runner) {
    MetricsCollector::reset();
    
    MetricsCollector::increment('test_counter');
    MetricsCollector::increment('test_counter', 5);
    
    $metrics = MetricsCollector::getAllMetrics();
    
    $runner->assertArrayHasKey('test_counter', $metrics['counters']);
    $runner->assertEquals(6, $metrics['counters']['test_counter']);
    
    return true;
});

$runner->addTest('MetricsCollector - Gauge', function() use ($runner) {
    MetricsCollector::reset();
    
    MetricsCollector::gauge('memory_usage', 1024.5);
    MetricsCollector::gauge('memory_usage', 2048.0);
    
    $metrics = MetricsCollector::getAllMetrics();
    
    $runner->assertArrayHasKey('memory_usage', $metrics['gauges']);
    $runner->assertEquals(2048.0, $metrics['gauges']['memory_usage']);
    
    return true;
});

$runner->addTest('MetricsCollector - Timer', function() use ($runner) {
    MetricsCollector::reset();
    
    MetricsCollector::timer('request_duration', 100.0);
    MetricsCollector::timer('request_duration', 200.0);
    MetricsCollector::timer('request_duration', 300.0);
    
    $metrics = MetricsCollector::getAllMetrics();
    
    $runner->assertArrayHasKey('request_duration', $metrics['timers']);
    $timerStats = $metrics['timers']['request_duration'];
    
    $runner->assertEquals(3, $timerStats['count']);
    $runner->assertEquals(100.0, $timerStats['min']);
    $runner->assertEquals(300.0, $timerStats['max']);
    $runner->assertEquals(200.0, $timerStats['avg']);
    
    return true;
});

$runner->addTest('MetricsCollector - System Metrics', function() use ($runner) {
    $systemMetrics = MetricsCollector::getSystemMetrics();
    
    $runner->assertIsArray($systemMetrics);
    $runner->assertArrayHasKey('memory_usage', $systemMetrics);
    $runner->assertArrayHasKey('memory_peak', $systemMetrics);
    $runner->assertArrayHasKey('memory_limit', $systemMetrics);
    $runner->assertArrayHasKey('execution_time', $systemMetrics);
    $runner->assertArrayHasKey('php_version', $systemMetrics);
    
    $runner->assertIsInt($systemMetrics['memory_usage']);
    $runner->assertIsInt($systemMetrics['memory_peak']);
    $runner->assertIsString($systemMetrics['memory_limit']);
    $runner->assertIsFloat($systemMetrics['execution_time']);
    $runner->assertIsString($systemMetrics['php_version']);
    
    $runner->assertGreaterThan(0, $systemMetrics['memory_usage']);
    $runner->assertGreaterThan(0, $systemMetrics['memory_peak']);
    $runner->assertGreaterThan(0, $systemMetrics['execution_time']);
    
    return true;
});

$runner->addTest('MetricsCollector - Reset', function() use ($runner) {
    MetricsCollector::increment('test_counter');
    MetricsCollector::gauge('test_gauge', 100.0);
    MetricsCollector::timer('test_timer', 50.0);
    
    $metrics = MetricsCollector::getAllMetrics();
    $runner->assertNotEmpty($metrics['counters']);
    $runner->assertNotEmpty($metrics['gauges']);
    $runner->assertNotEmpty($metrics['timers']);
    
    MetricsCollector::reset();
    
    $metrics = MetricsCollector::getAllMetrics();
    $runner->assertEmpty($metrics['counters']);
    $runner->assertEmpty($metrics['gauges']);
    $runner->assertEmpty($metrics['timers']);
    
    return true;
});

// Lancer les tests
$runner->run();
