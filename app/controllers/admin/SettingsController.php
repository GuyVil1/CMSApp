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
        if (!\Auth::isLoggedIn()) {
            // Si c'est une requête POST, afficher une erreur
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                http_response_code(401);
                echo json_encode(['error' => 'Non authentifié. Veuillez vous connecter.']);
                exit;
            }
            $this->redirect('/auth/login');
            return;
        }
        
        if (!\Auth::hasRole('admin')) {
            // Si c'est une requête POST, afficher une erreur
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                http_response_code(403);
                echo json_encode(['error' => 'Accès refusé. Droits administrateur requis.']);
                exit;
            }
            $this->redirect('/admin/dashboard');
            return;
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
            
            // Récupérer les logos disponibles
            $logos = $this->getAvailableLogos();
            
            $this->render('admin/settings/index', [
                'settings' => $settings,
                'themes' => $themes,
                'logos' => $logos
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
            
            // Vérifier le token CSRF
            $csrfToken = $this->getPostParam('csrf_token', '');
            if (!\Auth::verifyCsrfToken($csrfToken)) {
                $this->setFlash('error', 'Token de sécurité invalide. Veuillez réessayer.');
                $this->redirect('/admin/settings');
                return;
            }
            
            
        $settings = [
            'allow_registration' => $this->getPostParam('allow_registration', '0'),
            'dark_mode' => $this->getPostParam('dark_mode', '0'),
            'maintenance_mode' => $this->getPostParam('maintenance_mode', '0'),
            'default_theme' => $this->getPostParam('default_theme', 'defaut'),
            'footer_tagline' => $this->getPostParam('footer_tagline', ''),
            'social_twitter' => $this->getPostParam('social_twitter', ''),
            'social_facebook' => $this->getPostParam('social_facebook', ''),
            'social_youtube' => $this->getPostParam('social_youtube', ''),
            'header_logo' => $this->getPostParam('header_logo', 'Logo.svg'),
            'footer_logo' => $this->getPostParam('footer_logo', 'Logo_neutre_500px.png'),
            'legal_mentions_content' => $this->getPostParam('legal_mentions_content', ''),
            'legal_privacy_content' => $this->getPostParam('legal_privacy_content', ''),
            'legal_cgu_content' => $this->getPostParam('legal_cgu_content', ''),
            'legal_cookies_content' => $this->getPostParam('legal_cookies_content', '')
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
     * Récupérer la liste des logos disponibles
     */
    private function getAvailableLogos(): array
    {
        $logosDir = __DIR__ . '/../../../public/assets/images/logos/';
        $logos = [];
        
        if (is_dir($logosDir)) {
            $files = scandir($logosDir);
            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..' && $file !== 'favicons' && !is_dir($logosDir . $file)) {
                    $extension = pathinfo($file, PATHINFO_EXTENSION);
                    if (in_array(strtolower($extension), ['png', 'jpg', 'jpeg', 'svg', 'gif'])) {
                        $logos[] = [
                            'filename' => $file,
                            'display_name' => pathinfo($file, PATHINFO_FILENAME),
                            'extension' => $extension
                        ];
                    }
                }
            }
        }
        
        return $logos;
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
