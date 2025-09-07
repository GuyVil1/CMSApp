<?php
declare(strict_types=1);

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use App\Models\User;

/**
 * Tests unitaires pour le modèle User
 */
class UserTest extends TestCase
{
    private array $testUserData;
    
    protected function setUp(): void
    {
        $this->testUserData = [
            'login' => 'testuser_' . uniqid(),
            'email' => 'test_' . uniqid() . '@example.com',
            'password' => 'TestPassword123!',
            'role_id' => 4 // member
        ];
    }
    
    protected function tearDown(): void
    {
        // Nettoyer les données de test
        if (isset($this->testUserData['login'])) {
            try {
                $user = User::findByLogin($this->testUserData['login']);
                if ($user) {
                    User::delete($user->getId());
                }
            } catch (\Exception $e) {
                // Ignorer les erreurs de nettoyage
            }
        }
    }
    
    /**
     * Test de création d'un utilisateur
     */
    public function testCreateUser(): void
    {
        $user = User::create($this->testUserData);
        
        $this->assertInstanceOf(User::class, $user);
        $this->assertIsInt($user->getId());
        $this->assertEquals($this->testUserData['login'], $user->getLogin());
        $this->assertEquals($this->testUserData['email'], $user->getEmail());
        $this->assertEquals($this->testUserData['role_id'], $user->getRoleId());
        
        // Vérifier que le mot de passe est haché
        $this->assertNotEquals($this->testUserData['password'], $user->getPassword());
        $this->assertTrue(password_verify($this->testUserData['password'], $user->getPassword()));
    }
    
    /**
     * Test de recherche par login
     */
    public function testFindByLogin(): void
    {
        $user = User::create($this->testUserData);
        $foundUser = User::findByLogin($this->testUserData['login']);
        
        $this->assertInstanceOf(User::class, $foundUser);
        $this->assertEquals($user->getId(), $foundUser->getId());
        $this->assertEquals($this->testUserData['login'], $foundUser->getLogin());
    }
    
    /**
     * Test de recherche par email
     */
    public function testFindByEmail(): void
    {
        $user = User::create($this->testUserData);
        $foundUser = User::findByEmail($this->testUserData['email']);
        
        $this->assertInstanceOf(User::class, $foundUser);
        $this->assertEquals($user->getId(), $foundUser->getId());
        $this->assertEquals($this->testUserData['email'], $foundUser->getEmail());
    }
    
    /**
     * Test de vérification du mot de passe
     */
    public function testVerifyPassword(): void
    {
        $user = User::create($this->testUserData);
        
        $this->assertTrue($user->verifyPassword($this->testUserData['password']));
        $this->assertFalse($user->verifyPassword('wrongpassword'));
    }
    
    /**
     * Test de mise à jour d'un utilisateur
     */
    public function testUpdateUser(): void
    {
        $user = User::create($this->testUserData);
        $newEmail = 'updated_' . uniqid() . '@example.com';
        
        $user->setEmail($newEmail);
        $user->save();
        
        $updatedUser = User::find($user->getId());
        $this->assertEquals($newEmail, $updatedUser->getEmail());
    }
    
    /**
     * Test de suppression d'un utilisateur
     */
    public function testDeleteUser(): void
    {
        $user = User::create($this->testUserData);
        $userId = $user->getId();
        
        $this->assertTrue(User::delete($userId));
        
        $deletedUser = User::find($userId);
        $this->assertNull($deletedUser);
    }
    
    /**
     * Test de validation des données
     */
    public function testValidation(): void
    {
        // Test avec des données invalides
        $invalidData = [
            'login' => '', // Login vide
            'email' => 'invalid-email', // Email invalide
            'password' => '123', // Mot de passe trop court
            'role_id' => 999 // Rôle inexistant
        ];
        
        $this->expectException(\Exception::class);
        User::create($invalidData);
    }
    
    /**
     * Test de l'unicité du login
     */
    public function testUniqueLogin(): void
    {
        User::create($this->testUserData);
        
        // Essayer de créer un autre utilisateur avec le même login
        $duplicateData = $this->testUserData;
        $duplicateData['email'] = 'different_' . uniqid() . '@example.com';
        
        $this->expectException(\Exception::class);
        User::create($duplicateData);
    }
    
    /**
     * Test de l'unicité de l'email
     */
    public function testUniqueEmail(): void
    {
        User::create($this->testUserData);
        
        // Essayer de créer un autre utilisateur avec le même email
        $duplicateData = $this->testUserData;
        $duplicateData['login'] = 'different_' . uniqid();
        
        $this->expectException(\Exception::class);
        User::create($duplicateData);
    }
}
