<?php
declare(strict_types=1);

namespace Tests\Unit\Monitoring;

use PHPUnit\Framework\TestCase;
use MetricsCollector;

/**
 * Tests unitaires pour MetricsCollector
 */
class MetricsCollectorTest extends TestCase
{
    protected function setUp(): void
    {
        MetricsCollector::reset();
    }
    
    protected function tearDown(): void
    {
        MetricsCollector::reset();
    }
    
    /**
     * Test d'incrémentation de compteur
     */
    public function testIncrement(): void
    {
        MetricsCollector::increment('test_counter');
        MetricsCollector::increment('test_counter', 5);
        
        $metrics = MetricsCollector::getAllMetrics();
        
        $this->assertArrayHasKey('test_counter', $metrics['counters']);
        $this->assertEquals(6, $metrics['counters']['test_counter']);
    }
    
    /**
     * Test d'incrémentation avec tags
     */
    public function testIncrementWithTags(): void
    {
        MetricsCollector::increment('http_requests', 1, ['method' => 'GET', 'path' => '/']);
        MetricsCollector::increment('http_requests', 1, ['method' => 'POST', 'path' => '/']);
        
        $metrics = MetricsCollector::getAllMetrics();
        
        $this->assertArrayHasKey('http_requests:method=GET,path=/', $metrics['counters']);
        $this->assertArrayHasKey('http_requests:method=POST,path=/', $metrics['counters']);
        $this->assertEquals(1, $metrics['counters']['http_requests:method=GET,path=/']);
        $this->assertEquals(1, $metrics['counters']['http_requests:method=POST,path=/']);
    }
    
    /**
     * Test de gauge
     */
    public function testGauge(): void
    {
        MetricsCollector::gauge('memory_usage', 1024.5);
        MetricsCollector::gauge('memory_usage', 2048.0);
        
        $metrics = MetricsCollector::getAllMetrics();
        
        $this->assertArrayHasKey('memory_usage', $metrics['gauges']);
        $this->assertEquals(2048.0, $metrics['gauges']['memory_usage']);
    }
    
    /**
     * Test de timer
     */
    public function testTimer(): void
    {
        MetricsCollector::timer('request_duration', 100.0);
        MetricsCollector::timer('request_duration', 200.0);
        MetricsCollector::timer('request_duration', 300.0);
        
        $metrics = MetricsCollector::getAllMetrics();
        
        $this->assertArrayHasKey('request_duration', $metrics['timers']);
        $timerStats = $metrics['timers']['request_duration'];
        
        $this->assertEquals(3, $timerStats['count']);
        $this->assertEquals(100.0, $timerStats['min']);
        $this->assertEquals(300.0, $timerStats['max']);
        $this->assertEquals(200.0, $timerStats['avg']);
    }
    
    /**
     * Test de measure
     */
    public function testMeasure(): void
    {
        $result = MetricsCollector::measure('test_operation', function() {
            usleep(10000); // 10ms
            return 'success';
        });
        
        $this->assertEquals('success', $result);
        
        $metrics = MetricsCollector::getAllMetrics();
        $this->assertArrayHasKey('test_operation', $metrics['timers']);
        
        $timerStats = $metrics['timers']['test_operation'];
        $this->assertEquals(1, $timerStats['count']);
        $this->assertGreaterThan(0, $timerStats['min']);
    }
    
    /**
     * Test de reset
     */
    public function testReset(): void
    {
        MetricsCollector::increment('test_counter');
        MetricsCollector::gauge('test_gauge', 100.0);
        MetricsCollector::timer('test_timer', 50.0);
        
        $metrics = MetricsCollector::getAllMetrics();
        $this->assertNotEmpty($metrics['counters']);
        $this->assertNotEmpty($metrics['gauges']);
        $this->assertNotEmpty($metrics['timers']);
        
        MetricsCollector::reset();
        
        $metrics = MetricsCollector::getAllMetrics();
        $this->assertEmpty($metrics['counters']);
        $this->assertEmpty($metrics['gauges']);
        $this->assertEmpty($metrics['timers']);
    }
    
    /**
     * Test des métriques système
     */
    public function testSystemMetrics(): void
    {
        $systemMetrics = MetricsCollector::getSystemMetrics();
        
        $this->assertIsArray($systemMetrics);
        $this->assertArrayHasKey('memory_usage', $systemMetrics);
        $this->assertArrayHasKey('memory_peak', $systemMetrics);
        $this->assertArrayHasKey('memory_limit', $systemMetrics);
        $this->assertArrayHasKey('execution_time', $systemMetrics);
        $this->assertArrayHasKey('php_version', $systemMetrics);
        
        $this->assertIsInt($systemMetrics['memory_usage']);
        $this->assertIsInt($systemMetrics['memory_peak']);
        $this->assertIsString($systemMetrics['memory_limit']);
        $this->assertIsFloat($systemMetrics['execution_time']);
        $this->assertIsString($systemMetrics['php_version']);
        
        $this->assertGreaterThan(0, $systemMetrics['memory_usage']);
        $this->assertGreaterThan(0, $systemMetrics['memory_peak']);
        $this->assertGreaterThan(0, $systemMetrics['execution_time']);
    }
    
    /**
     * Test de percentile
     */
    public function testPercentile(): void
    {
        // Créer des valeurs de test
        $values = [10, 20, 30, 40, 50, 60, 70, 80, 90, 100];
        
        // Utiliser la réflexion pour tester la méthode privée
        $reflection = new \ReflectionClass(MetricsCollector::class);
        $method = $reflection->getMethod('percentile');
        $method->setAccessible(true);
        
        $p50 = $method->invoke(null, $values, 50);
        $p95 = $method->invoke(null, $values, 95);
        $p99 = $method->invoke(null, $values, 99);
        
        $this->assertEquals(50, $p50);
        $this->assertEquals(95, $p95);
        $this->assertEquals(99, $p99);
    }
}
