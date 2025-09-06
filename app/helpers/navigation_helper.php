<?php
declare(strict_types=1);

/**
 * Helper pour la navigation
 * Fonctions utilitaires pour gérer les menus de navigation
 */

class NavigationHelper
{
    /**
     * Récupérer les menus principaux de navigation (sans sous-menus)
     * 
     * @return array
     */
    public static function getMainMenus(): array
    {
        // Définir les catégories principales (même structure que dans navbar.php)
        $categories = [
            'actualités' => ['name' => 'ACTUALITÉS', 'slug' => 'actu'],
            'tests' => ['name' => 'TESTS', 'slug' => 'test'],
            'dossiers' => ['name' => 'DOSSIERS', 'slug' => 'dossiers'],
            'trailers' => ['name' => 'TRAILERS', 'slug' => 'trailers']
        ];
        
        $menus = [];
        
        // Ajouter l'accueil
        $menus[] = [
            'name' => 'Accueil',
            'url' => '/',
            'slug' => 'home'
        ];
        
        // Ajouter les catégories
        foreach ($categories as $key => $category) {
            $menus[] = [
                'name' => $category['name'],
                'url' => '/category/' . $category['slug'],
                'slug' => $category['slug']
            ];
        }
        
        // Ajouter le hardware (menu principal, pas les sous-menus)
        $menus[] = [
            'name' => 'HARDWARE',
            'url' => '/hardwares',
            'slug' => 'hardware'
        ];
        
        return $menus;
    }
    
    /**
     * Récupérer les menus pour le footer (version simplifiée)
     * 
     * @return array
     */
    public static function getFooterMenus(): array
    {
        $menus = self::getMainMenus();
        
        // Adapter les noms pour le footer (moins de majuscules)
        $footerMenus = [];
        foreach ($menus as $menu) {
            $footerMenus[] = [
                'name' => self::formatFooterName($menu['name']),
                'url' => $menu['url'],
                'slug' => $menu['slug']
            ];
        }
        
        return $footerMenus;
    }
    
    /**
     * Formater le nom pour le footer (moins agressif que la navbar)
     * 
     * @param string $name
     * @return string
     */
    private static function formatFooterName(string $name): string
    {
        // Remplacer les noms en majuscules par des versions plus douces
        $replacements = [
            'ACTUALITÉS' => 'Actualités',
            'TESTS' => 'Tests & Reviews',
            'DOSSIERS' => 'Dossiers',
            'TRAILERS' => 'Trailers',
            'HARDWARE' => 'Matériel'
        ];
        
        return $replacements[$name] ?? $name;
    }
    
    /**
     * Vérifier la cohérence entre navbar et footer
     * 
     * @return array
     */
    public static function validateNavigation(): array
    {
        $navbarMenus = self::getMainMenus();
        $footerMenus = self::getFooterMenus();
        
        $issues = [];
        
        // Vérifier que le nombre de menus correspond
        if (count($navbarMenus) !== count($footerMenus)) {
            $issues[] = "Nombre de menus différent entre navbar (" . count($navbarMenus) . ") et footer (" . count($footerMenus) . ")";
        }
        
        // Vérifier que les URLs correspondent
        foreach ($navbarMenus as $index => $navbarMenu) {
            if (isset($footerMenus[$index])) {
                if ($navbarMenu['url'] !== $footerMenus[$index]['url']) {
                    $issues[] = "URL différente pour '{$navbarMenu['name']}': navbar='{$navbarMenu['url']}' vs footer='{$footerMenus[$index]['url']}'";
                }
            }
        }
        
        return $issues;
    }
}
