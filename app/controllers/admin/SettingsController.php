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
        // Vérifier que l'utilisateur est connecté et a les droits admin
        \Auth::requireRole('admin');
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
                $this->redirect('/admin/settings');
                return;
            }
            
            $settings = [
                'allow_registration' => $_POST['allow_registration'] ?? '0',
                'dark_mode' => $_POST['dark_mode'] ?? '0',
                'maintenance_mode' => $_POST['maintenance_mode'] ?? '0',
                'default_theme' => $_POST['default_theme'] ?? 'defaut',
                'footer_tagline' => $_POST['footer_tagline'] ?? '',
                'social_twitter' => $_POST['social_twitter'] ?? '',
                'social_facebook' => $_POST['social_facebook'] ?? '',
                'social_youtube' => $_POST['social_youtube'] ?? ''
            ];
            
            $success = true;
            foreach ($settings as $key => $value) {
                if (!\Setting::set($key, $value)) {
                    $success = false;
                }
            }
            
            // Mettre à jour le thème actuel si le thème par défaut a changé
            if ($success && isset($settings['default_theme'])) {
                $this->updateCurrentTheme($settings['default_theme']);
            }
            
            if ($success) {
                $this->setFlash('success', 'Paramètres sauvegardés avec succès !');
            } else {
                $this->setFlash('error', 'Erreur lors de la sauvegarde des paramètres.');
            }
            
            $this->redirect('/admin/settings');
            
        } catch (\Exception $e) {
            error_log("Erreur SettingsController::save(): " . $e->getMessage());
            $this->setFlash('error', 'Erreur serveur lors de la sauvegarde.');
            $this->redirect('/admin/settings');
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
    
    /**
     * Mettre à jour le thème actuel dans le fichier de configuration
     */
    private function updateCurrentTheme(string $themeName): void
    {
        $configFile = __DIR__ . '/../../../config/theme.json';
        $configDir = dirname($configFile);
        
        // Créer le dossier config s'il n'existe pas
        if (!is_dir($configDir)) {
            mkdir($configDir, 0755, true);
        }
        
        // Lire la configuration existante ou créer une nouvelle
        $config = [];
        if (file_exists($configFile)) {
            $config = json_decode(file_get_contents($configFile), true) ?? [];
        }
        
        // Mettre à jour la configuration
        $config['current_theme'] = $themeName;
        $config['default_theme'] = $themeName;
        $config['is_permanent'] = true;
        $config['expires_at'] = null;
        $config['applied_at'] = date('Y-m-d H:i:s');
        
        // Sauvegarder la configuration
        file_put_contents($configFile, json_encode($config, JSON_PRETTY_PRINT));
    }
}
