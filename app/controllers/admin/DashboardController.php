<?php
declare(strict_types=1);

/**
 * Contrôleur du tableau de bord administrateur
 */

namespace Admin;

require_once __DIR__ . '/../../../core/Controller.php';
require_once __DIR__ . '/../../../core/Auth.php';
require_once __DIR__ . '/../../../core/Database.php';

class DashboardController extends \Controller
{
    public function __construct()
    {
        // Vérifier que l'utilisateur est connecté et a les droits admin
        \Auth::requireRole('admin');
    }
    
    /**
     * Page d'accueil du tableau de bord
     */
    public function index(): void
    {
        $this->render('admin/dashboard/index', [
            'user' => \Auth::getUser(),
            'stats' => $this->getStats()
        ]);
    }
    
    /**
     * Obtenir les statistiques du site
     */
    private function getStats(): array
    {
        try {
            return [
                'articles' => \Database::queryOne("SELECT COUNT(*) as count FROM articles")['count'] ?? 0,
                'users' => \Database::queryOne("SELECT COUNT(*) as count FROM users")['count'] ?? 0,
                'games' => \Database::queryOne("SELECT COUNT(*) as count FROM games")['count'] ?? 0,
                'categories' => \Database::queryOne("SELECT COUNT(*) as count FROM categories")['count'] ?? 0
            ];
        } catch (\Exception $e) {
            return ['articles' => 0, 'users' => 0, 'games' => 0, 'categories' => 0];
        }
    }
}
