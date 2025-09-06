<?php
declare(strict_types=1);

/**
 * Contrôleur Settings - Belgium Vidéo Gaming
 * Gestion des paramètres du site
 */

namespace Admin;

require_once __DIR__ . '/../../../core/Controller.php';
require_once __DIR__ . '/../../../core/Auth.php';
require_once __DIR__ . '/../../models/Setting.php';

class SettingsController extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        
        // Vérifier l'authentification
        if (!\Auth::isLoggedIn()) {
            header('Location: /login');
            exit;
        }
        
        // Vérifier les permissions admin
        $user = \Auth::getUser();
        if (!$user || $user['role_name'] !== 'admin') {
            http_response_code(403);
            $this->render('layout/403', [
                'pageTitle' => 'Accès refusé - Belgium Video Gaming',
                'pageDescription' => 'Vous n\'avez pas les permissions nécessaires.'
            ]);
            exit;
        }
    }
    
    /**
     * Afficher la page des paramètres
     */
    public function index(): void
    {
        try {
            // Initialiser les options par défaut si nécessaire
            \Setting::initDefaults();
            
            // Récupérer toutes les options
            $settings = \Setting::getAll();
            
            // Récupérer les thèmes disponibles
            $themes = $this->getAvailableThemes();
            
            $this->render('admin/settings/index', [
                'settings' => $settings,
                'themes' => $themes
            ]);
            
        } catch (Exception $e) {
            error_log("Erreur SettingsController::index(): " . $e->getMessage());
            http_response_code(500);
            $this->render('layout/500', [
                'pageTitle' => 'Erreur serveur - Belgium Video Gaming',
                'pageDescription' => 'Une erreur est survenue lors du chargement des paramètres.'
            ]);
        }
    }
    
    /**
     * Sauvegarder les paramètres
     */
    public function save(): void
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405);
                return;
            }
            
            $settings = [
                'allow_registration' => $_POST['allow_registration'] ?? '0',
                'dark_mode' => $_POST['dark_mode'] ?? '0',
                'maintenance_mode' => $_POST['maintenance_mode'] ?? '0',
                'default_theme' => $_POST['default_theme'] ?? 'defaut'
            ];
            
            $success = true;
            foreach ($settings as $key => $value) {
                if (!\Setting::set($key, $value)) {
                    $success = false;
                }
            }
            
            if ($success) {
                $_SESSION['flash_message'] = 'Paramètres sauvegardés avec succès !';
                $_SESSION['flash_type'] = 'success';
            } else {
                $_SESSION['flash_message'] = 'Erreur lors de la sauvegarde des paramètres.';
                $_SESSION['flash_type'] = 'error';
            }
            
            header('Location: /admin/dashboard');
            exit;
            
        } catch (Exception $e) {
            error_log("Erreur SettingsController::save(): " . $e->getMessage());
            $_SESSION['flash_message'] = 'Erreur serveur lors de la sauvegarde.';
            $_SESSION['flash_type'] = 'error';
            header('Location: /admin/dashboard');
            exit;
        }
    }
    
    /**
     * Récupérer les thèmes disponibles
     */
    private function getAvailableThemes(): array
    {
        $themesDir = __DIR__ . '/../../../themes/';
        $themes = [];
        
        if (is_dir($themesDir)) {
            $dirs = scandir($themesDir);
            foreach ($dirs as $dir) {
                if ($dir !== '.' && $dir !== '..' && is_dir($themesDir . $dir)) {
                    $themes[] = [
                        'name' => $dir,
                        'display_name' => ucfirst($dir)
                    ];
                }
            }
        }
        
        return $themes;
    }
}
