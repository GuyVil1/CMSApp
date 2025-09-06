<?php
declare(strict_types=1);

/**
 * Contrôleur SEO
 * Gestion du sitemap, robots.txt et optimisations SEO
 */

require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../helpers/seo_helper.php';

class SeoController extends Controller
{
    /**
     * Générer le sitemap XML
     */
    public function sitemap(): void
    {
        try {
            $sitemap = SeoHelper::generateSitemap();
            
            // Headers pour le XML
            header('Content-Type: application/xml; charset=utf-8');
            header('Cache-Control: public, max-age=3600'); // Cache 1 heure
            
            echo $sitemap;
            exit;
            
        } catch (Exception $e) {
            error_log("Erreur génération sitemap: " . $e->getMessage());
            http_response_code(500);
            echo '<?xml version="1.0" encoding="UTF-8"?><error>Erreur lors de la génération du sitemap</error>';
            exit;
        }
    }
    
    /**
     * Générer le robots.txt
     */
    public function robots(): void
    {
        try {
            $robots = SeoHelper::generateRobotsTxt();
            
            // Headers pour le texte
            header('Content-Type: text/plain; charset=utf-8');
            header('Cache-Control: public, max-age=86400'); // Cache 24 heures
            
            echo $robots;
            exit;
            
        } catch (Exception $e) {
            error_log("Erreur génération robots.txt: " . $e->getMessage());
            http_response_code(500);
            echo "User-agent: *\nDisallow: /";
            exit;
        }
    }
    
    /**
     * Générer les meta tags pour la page d'accueil
     */
    public function homeMetaTags(): string
    {
        return SeoHelper::generateMetaTags([
            'title' => 'Belgium Video Gaming - L\'actualité jeux vidéo en Belgique',
            'description' => 'On joue, on observe, on t\'éclaire. Pas de pub, pas de langue de bois — juste notre regard de passionnés, pour affiner le tien. Actualités, tests et dossiers gaming.',
            'keywords' => 'gaming, jeux vidéo, belgique, actualité, tests, dossiers, passionnés, belgium video gaming',
            'url' => 'https://belgium-video-gaming.be',
            'image' => 'https://belgium-video-gaming.be/public/assets/images/default-featured.jpg',
            'type' => 'website'
        ]);
    }
    
    /**
     * Générer les meta tags pour une catégorie
     */
    public function categoryMetaTags($category, $baseUrl = 'https://belgium-video-gaming.be'): string
    {
        $title = htmlspecialchars($category['name']) . ' - Belgium Video Gaming';
        $description = $category['description'] ?? 
            'Découvrez tous nos articles sur ' . strtolower($category['name']) . ' sur Belgium Video Gaming. Actualités, tests et analyses par des passionnés.';
        
        return SeoHelper::generateMetaTags([
            'title' => $title,
            'description' => $description,
            'keywords' => 'gaming, jeux vidéo, belgique, ' . strtolower($category['name']),
            'url' => $baseUrl . '/category/' . $category['slug'],
            'image' => $baseUrl . '/public/assets/images/default-featured.jpg',
            'type' => 'website'
        ]);
    }
    
    /**
     * Générer les meta tags pour un jeu
     */
    public function gameMetaTags($game, $baseUrl = 'https://belgium-video-gaming.be'): string
    {
        $title = htmlspecialchars($game['title']) . ' - Belgium Video Gaming';
        $description = $game['description'] ?? 
            'Découvrez ' . $game['title'] . ' sur Belgium Video Gaming. Actualités, tests et analyses par des passionnés belges.';
        
        return SeoHelper::generateMetaTags([
            'title' => $title,
            'description' => $description,
            'keywords' => 'gaming, jeux vidéo, belgique, ' . strtolower($game['title']),
            'url' => $baseUrl . '/game/' . $game['slug'],
            'image' => $game['cover_image'] ? 
                $baseUrl . '/public/uploads.php?file=games/' . $game['cover_image'] : 
                $baseUrl . '/public/assets/images/default-featured.jpg',
            'type' => 'website'
        ]);
    }
}