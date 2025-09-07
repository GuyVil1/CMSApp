<?php
declare(strict_types=1);

namespace Tests\Unit\Helpers;

use PHPUnit\Framework\TestCase;
use App\Helpers\MemoryCache;

/**
 * Tests unitaires pour MemoryCache
 */
class MemoryCacheTest extends TestCase
{
    protected function setUp(): void
    {
        MemoryCache::flush();
    }
    
    protected function tearDown(): void
    {
        MemoryCache::flush();
    }
    
    /**
     * Test de stockage et récupération
     */
    public function testPutAndGet(): void
    {
        $key = 'test_key';
        $value = 'test_value';
        
        MemoryCache::put($key, $value);
        
        $this->assertEquals($value, MemoryCache::get($key));
        $this->assertTrue(MemoryCache::has($key));
    }
    
    /**
     * Test de la valeur par défaut
     */
    public function testGetDefaultValue(): void
    {
        $defaultValue = 'default';
        
        $this->assertEquals($defaultValue, MemoryCache::get('nonexistent_key', $defaultValue));
        $this->assertFalse(MemoryCache::has('nonexistent_key'));
    }
    
    /**
     * Test de l'expiration
     */
    public function testExpiration(): void
    {
        $key = 'expiring_key';
        $value = 'expiring_value';
        
        // Stocker avec un TTL très court
        MemoryCache::put($key, $value, 1);
        
        $this->assertEquals($value, MemoryCache::get($key));
        
        // Attendre que ça expire
        sleep(2);
        
        $this->assertNull(MemoryCache::get($key));
        $this->assertFalse(MemoryCache::has($key));
    }
    
    /**
     * Test de suppression
     */
    public function testForget(): void
    {
        $key = 'forgettable_key';
        $value = 'forgettable_value';
        
        MemoryCache::put($key, $value);
        $this->assertTrue(MemoryCache::has($key));
        
        MemoryCache::forget($key);
        $this->assertFalse(MemoryCache::has($key));
        $this->assertNull(MemoryCache::get($key));
    }
    
    /**
     * Test de remember avec callback
     */
    public function testRemember(): void
    {
        $key = 'remember_key';
        $value = 'remember_value';
        $callCount = 0;
        
        $callback = function() use ($value, &$callCount) {
            $callCount++;
            return $value;
        };
        
        // Premier appel - le callback doit être exécuté
        $result1 = MemoryCache::remember($key, $callback, 3600);
        $this->assertEquals($value, $result1);
        $this->assertEquals(1, $callCount);
        
        // Deuxième appel - le callback ne doit pas être exécuté
        $result2 = MemoryCache::remember($key, $callback, 3600);
        $this->assertEquals($value, $result2);
        $this->assertEquals(1, $callCount); // Toujours 1
    }
    
    /**
     * Test des statistiques
     */
    public function testStats(): void
    {
        MemoryCache::flush();
        
        // Générer quelques hits et misses
        MemoryCache::put('key1', 'value1');
        MemoryCache::get('key1'); // hit
        MemoryCache::get('key1'); // hit
        MemoryCache::get('nonexistent'); // miss
        
        $stats = MemoryCache::getStats();
        
        $this->assertIsArray($stats);
        $this->assertArrayHasKey('hits', $stats);
        $this->assertArrayHasKey('misses', $stats);
        $this->assertArrayHasKey('hit_rate', $stats);
        $this->assertArrayHasKey('items_count', $stats);
        
        $this->assertEquals(2, $stats['hits']);
        $this->assertEquals(1, $stats['misses']);
        $this->assertEquals(1, $stats['items_count']);
    }
    
    /**
     * Test de nettoyage des éléments expirés
     */
    public function testCleanExpired(): void
    {
        // Ajouter des éléments avec différents TTL
        MemoryCache::put('key1', 'value1', 1); // Expire rapidement
        MemoryCache::put('key2', 'value2', 3600); // Expire lentement
        
        $this->assertEquals(2, MemoryCache::getStats()['items_count']);
        
        // Attendre que key1 expire
        sleep(2);
        
        // Nettoyer les éléments expirés
        $cleaned = MemoryCache::cleanExpired();
        
        $this->assertEquals(1, $cleaned);
        $this->assertEquals(1, MemoryCache::getStats()['items_count']);
        $this->assertFalse(MemoryCache::has('key1'));
        $this->assertTrue(MemoryCache::has('key2'));
    }
    
    /**
     * Test de suppression par pattern
     */
    public function testForgetPattern(): void
    {
        MemoryCache::put('user_1', 'value1');
        MemoryCache::put('user_2', 'value2');
        MemoryCache::put('admin_1', 'value3');
        MemoryCache::put('other', 'value4');
        
        $this->assertEquals(4, MemoryCache::getStats()['items_count']);
        
        // Supprimer tous les clés commençant par "user_"
        MemoryCache::forgetPattern('user_*');
        
        $this->assertEquals(2, MemoryCache::getStats()['items_count']);
        $this->assertFalse(MemoryCache::has('user_1'));
        $this->assertFalse(MemoryCache::has('user_2'));
        $this->assertTrue(MemoryCache::has('admin_1'));
        $this->assertTrue(MemoryCache::has('other'));
    }
    
    /**
     * Test de flush
     */
    public function testFlush(): void
    {
        MemoryCache::put('key1', 'value1');
        MemoryCache::put('key2', 'value2');
        
        $this->assertEquals(2, MemoryCache::getStats()['items_count']);
        
        MemoryCache::flush();
        
        $this->assertEquals(0, MemoryCache::getStats()['items_count']);
        $this->assertFalse(MemoryCache::has('key1'));
        $this->assertFalse(MemoryCache::has('key2'));
    }
}
