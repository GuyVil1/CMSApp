<?php
declare(strict_types=1);

/**
 * Contrôleur de gestion des thèmes - Belgium Vidéo Gaming
 */

namespace Admin;

require_once __DIR__ . '/../../../core/Controller.php';
require_once __DIR__ . '/../../../core/Auth.php';
require_once __DIR__ . '/../../../core/Database.php';

class ThemesController extends \Controller
{
    private string $themesPath;
    
    public function __construct()
    {
        // Vérifier que l'utilisateur est connecté et a les droits admin
        \Auth::requireRole(['admin']);
        
        $this->themesPath = __DIR__ . '/../../../themes/';
    }
    
    /**
     * Liste des thèmes disponibles
     */
    public function index(): void
    {
        $themes = $this->scanThemes();
        $currentTheme = $this->getCurrentTheme();
        
        $this->render('admin/themes/index', [
            'themes' => $themes,
            'currentTheme' => $currentTheme
        ]);
    }
    
    /**
     * Scanner les thèmes disponibles
     */
    private function scanThemes(): array
    {
        $themes = [];
        
        if (!is_dir($this->themesPath)) {
            return $themes;
        }
        
        $directories = scandir($this->themesPath);
        
        foreach ($directories as $dir) {
            if ($dir === '.' || $dir === '..') {
                continue;
            }
            
            $themePath = $this->themesPath . $dir;
            
            if (is_dir($themePath)) {
                $leftImage = $themePath . '/left.png';
                $rightImage = $themePath . '/right.png';
                
                // Vérifier que les deux images existent
                if (file_exists($leftImage) && file_exists($rightImage)) {
                    $themes[] = [
                        'name' => $dir,
                        'display_name' => ucfirst($dir),
                        'left_image' => $leftImage,
                        'right_image' => $rightImage,
                        'left_url' => '/theme-image.php?theme=' . $dir . '&side=left',
                        'right_url' => '/theme-image.php?theme=' . $dir . '&side=right'
                    ];
                }
            }
        }
        
        return $themes;
    }
    
    /**
     * Obtenir le thème actuel
     */
    private function getCurrentTheme(): array
    {
        $configFile = __DIR__ . '/../../../config/theme.json';
        
        if (file_exists($configFile)) {
            $config = json_decode(file_get_contents($configFile), true);
            if ($config) {
                return [
                    'name' => $config['current_theme'] ?? 'defaut',
                    'is_permanent' => $config['is_permanent'] ?? true,
                    'expires_at' => $config['expires_at'] ?? null,
                    'applied_at' => $config['applied_at'] ?? null
                ];
            }
        }
        
        return ['name' => 'defaut', 'is_permanent' => true, 'expires_at' => null, 'applied_at' => null];
    }
    
    /**
     * Appliquer un thème
     */
    public function apply(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/themes');
            return;
        }
        
        try {
            $themeName = trim($_POST['theme_name'] ?? '');
            $isPermanent = isset($_POST['is_permanent']) && $_POST['is_permanent'] === '1';
            $expiresAt = !empty($_POST['expires_at']) ? $_POST['expires_at'] : null;
            
            if (empty($themeName)) {
                throw new \Exception('Nom du thème requis');
            }
            
            // Vérifier que le thème existe
            $themes = $this->scanThemes();
            $themeExists = false;
            
            foreach ($themes as $theme) {
                if ($theme['name'] === $themeName) {
                    $themeExists = true;
                    break;
                }
            }
            
            if (!$themeExists) {
                throw new \Exception('Thème non trouvé');
            }
            
            // Si permanent, demander confirmation
            if ($isPermanent && !isset($_POST['confirm_permanent'])) {
                $this->render('admin/themes/confirm', [
                    'themeName' => $themeName,
                    'expiresAt' => $expiresAt
                ]);
                return;
            }
            
            // Sauvegarder la configuration
            $config = [
                'current_theme' => $themeName,
                'default_theme' => $isPermanent ? $themeName : 'defaut',
                'expires_at' => $expiresAt,
                'is_permanent' => $isPermanent,
                'applied_at' => date('Y-m-d H:i:s')
            ];
            
            $configFile = __DIR__ . '/../../../config/theme.json';
            $configDir = dirname($configFile);
            
            if (!is_dir($configDir)) {
                mkdir($configDir, 0755, true);
            }
            
            if (!file_put_contents($configFile, json_encode($config, JSON_PRETTY_PRINT))) {
                throw new \Exception('Erreur lors de la sauvegarde de la configuration');
            }
            
            // Log de l'activité
            $action = $isPermanent ? 'Thème permanent appliqué' : 'Thème temporaire appliqué';
            \Auth::logActivity(\Auth::getUserId(), "$action : $themeName");
            
            $this->setFlash('success', 'Thème appliqué avec succès !');
            $this->redirect('/admin/themes');
            
        } catch (\Exception $e) {
            $this->setFlash('error', 'Erreur : ' . $e->getMessage());
            $this->redirect('/admin/themes');
        }
    }
}
