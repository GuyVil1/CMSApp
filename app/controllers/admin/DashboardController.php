<?php
declare(strict_types=1);

/**
 * Contrôleur du tableau de bord administrateur
 */

namespace Admin;

require_once __DIR__ . '/../../../core/Controller.php';
require_once __DIR__ . '/../../../core/Auth.php';
require_once __DIR__ . '/../../../core/Database.php';
require_once __DIR__ . '/../../models/Setting.php';

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
        // Initialiser les options par défaut si nécessaire
        \Setting::initDefaults();
        
        $this->render('admin/dashboard/index', [
            'user' => \Auth::getUser(),
            'stats' => $this->getStats(),
            'options' => $this->getOptions()
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
    
    /**
     * Obtenir les options du site
     */
    private function getOptions(): array
    {
        try {
            return [
                'allow_registration' => \Setting::isEnabled('allow_registration'),
                'dark_mode' => \Setting::isEnabled('dark_mode'),
                'maintenance_mode' => \Setting::isEnabled('maintenance_mode'),
                'default_theme' => $this->getCurrentThemeName()
            ];
        } catch (\Exception $e) {
            return [
                'allow_registration' => true,
                'dark_mode' => false,
                'maintenance_mode' => false,
                'default_theme' => 'defaut'
            ];
        }
    }
    
    /**
     * Obtenir le nom du thème actuel depuis le fichier de configuration
     */
    private function getCurrentThemeName(): string
    {
        $configFile = __DIR__ . '/../../../config/theme.json';
        
        if (file_exists($configFile)) {
            $config = json_decode(file_get_contents($configFile), true);
            if ($config && isset($config['current_theme'])) {
                return $config['current_theme'];
            }
        }
        
        return 'defaut';
    }
}
