<?php
declare(strict_types=1);

/**
 * Système de tests simple sans PHPUnit
 * Pour les environnements sans Composer
 */

class SimpleTestRunner
{
    private array $tests = [];
    private int $passed = 0;
    private int $failed = 0;
    private array $results = [];
    
    public function addTest(string $name, callable $test): void
    {
        $this->tests[$name] = $test;
    }
    
    public function run(): void
    {
        echo "🧪 LANCEMENT DES TESTS SIMPLES\n";
        echo "==============================\n\n";
        
        foreach ($this->tests as $name => $test) {
            echo "Test: $name ... ";
            
            try {
                $result = $test();
                if ($result === true) {
                    echo "✅ PASSÉ\n";
                    $this->passed++;
                    $this->results[$name] = ['status' => 'PASSED', 'message' => ''];
                } else {
                    echo "❌ ÉCHOUÉ\n";
                    $this->failed++;
                    $this->results[$name] = ['status' => 'FAILED', 'message' => $result];
                }
            } catch (Exception $e) {
                echo "❌ ERREUR: " . $e->getMessage() . "\n";
                $this->failed++;
                $this->results[$name] = ['status' => 'ERROR', 'message' => $e->getMessage()];
            }
        }
        
        echo "\n📊 RÉSULTATS:\n";
        echo "=============\n";
        echo "✅ Tests passés: {$this->passed}\n";
        echo "❌ Tests échoués: {$this->failed}\n";
        echo "📈 Total: " . ($this->passed + $this->failed) . "\n";
        
        if ($this->failed > 0) {
            echo "\n❌ DÉTAILS DES ÉCHECS:\n";
            foreach ($this->results as $name => $result) {
                if ($result['status'] !== 'PASSED') {
                    echo "- $name: {$result['message']}\n";
                }
            }
        }
        
        echo "\n🎯 COUVERTURE ESTIMÉE: " . round(($this->passed / ($this->passed + $this->failed)) * 100) . "%\n";
    }
    
    public function assertTrue(bool $condition, string $message = ''): bool
    {
        if (!$condition) {
            throw new Exception($message ?: 'Assertion failed: expected true');
        }
        return true;
    }
    
    public function assertFalse(bool $condition, string $message = ''): bool
    {
        if ($condition) {
            throw new Exception($message ?: 'Assertion failed: expected false');
        }
        return true;
    }
    
    public function assertEquals($expected, $actual, string $message = ''): bool
    {
        if ($expected !== $actual) {
            throw new Exception($message ?: "Assertion failed: expected '$expected', got '$actual'");
        }
        return true;
    }
    
    public function assertNotEquals($expected, $actual, string $message = ''): bool
    {
        if ($expected === $actual) {
            throw new Exception($message ?: "Assertion failed: expected different from '$expected'");
        }
        return true;
    }
    
    public function assertIsArray($value, string $message = ''): bool
    {
        if (!is_array($value)) {
            throw new Exception($message ?: 'Assertion failed: expected array');
        }
        return true;
    }
    
    public function assertIsInt($value, string $message = ''): bool
    {
        if (!is_int($value)) {
            throw new Exception($message ?: 'Assertion failed: expected int');
        }
        return true;
    }
    
    public function assertIsString($value, string $message = ''): bool
    {
        if (!is_string($value)) {
            throw new Exception($message ?: 'Assertion failed: expected string');
        }
        return true;
    }
    
    public function assertIsFloat($value, string $message = ''): bool
    {
        if (!is_float($value)) {
            throw new Exception($message ?: 'Assertion failed: expected float');
        }
        return true;
    }
    
    public function assertGreaterThan($expected, $actual, string $message = ''): bool
    {
        if ($actual <= $expected) {
            throw new Exception($message ?: "Assertion failed: expected greater than '$expected', got '$actual'");
        }
        return true;
    }
    
    public function assertArrayHasKey($key, array $array, string $message = ''): bool
    {
        if (!array_key_exists($key, $array)) {
            throw new Exception($message ?: "Assertion failed: array does not have key '$key'");
        }
        return true;
    }
    
    public function assertInstanceOf(string $expected, $actual, string $message = ''): bool
    {
        if (!($actual instanceof $expected)) {
            $actualClass = is_object($actual) ? get_class($actual) : gettype($actual);
            throw new Exception($message ?: "Assertion failed: expected instance of '$expected', got '$actualClass'");
        }
        return true;
    }
    
    public function assertNotEmpty($value, string $message = ''): bool
    {
        if (empty($value)) {
            throw new Exception($message ?: 'Assertion failed: expected non-empty value');
        }
        return true;
    }
    
    public function assertEmpty($value, string $message = ''): bool
    {
        if (!empty($value)) {
            throw new Exception($message ?: 'Assertion failed: expected empty value');
        }
        return true;
    }
}
