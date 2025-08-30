<?php
declare(strict_types=1);

/**
 * Contrôleur de test pour vérifier le routage
 */

require_once __DIR__ . '/../../core/Controller.php';

class TestController extends \Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index(): void
    {
        echo "<h1>🧪 Test du routeur</h1>";
        echo "<p>✅ Le routeur fonctionne correctement !</p>";
        echo "<p>📍 URL : " . $_SERVER['REQUEST_URI'] . "</p>";
        echo "<p>🎯 Contrôleur : TestController</p>";
        echo "<p>⚡ Action : index</p>";
        
        echo "<h2>🔗 Liens de test :</h2>";
        echo "<ul>";
        echo "<li><a href='/genres'>/genres</a> - Gestion des genres</li>";
        echo "<li><a href='/games'>/games</a> - Gestion des jeux</li>";
        echo "<li><a href='/admin'>/admin</a> - Tableau de bord</li>";
        echo "<li><a href='/articles'>/articles</a> - Gestion des articles</li>";
        echo "<li><a href='/categories'>/categories</a> - Gestion des catégories</li>";
        echo "<li><a href='/hardware'>/hardware</a> - Gestion du hardware</li>";
        echo "<li><a href='/media'>/media</a> - Gestion des médias</li>";
        echo "<li><a href='/users'>/users</a> - Gestion des utilisateurs</li>";
        echo "</ul>";
        
        echo "<h2>📊 Informations système :</h2>";
        echo "<ul>";
        echo "<li>PHP Version : " . PHP_VERSION . "</li>";
        echo "<li>Serveur : " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Inconnu') . "</li>";
        echo "<li>Base de données : " . (\Config::get('DB_NAME') ?? 'Non configurée') . "</li>";
        echo "</ul>";
    }

    public function genres(): void
    {
        echo "<h1>🎯 Test de la route /test/genres</h1>";
        echo "<p>✅ Cette route fonctionne !</p>";
        echo "<p><a href='/test'>← Retour au test principal</a></p>";
    }

    public function games(): void
    {
        echo "<h1>🎮 Test de la route /test/games</h1>";
        echo "<p>✅ Cette route fonctionne !</p>";
        echo "<p><a href='/test'>← Retour au test principal</a></p>";
    }
}
