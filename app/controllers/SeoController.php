<?php
declare(strict_types=1);

/**
 * Contrôleur SEO
 * Gestion du sitemap, robots.txt et autres optimisations SEO
 */

require_once __DIR__ . '/../helpers/seo_helper.php';

class SeoController extends Controller
{
    /**
     * Générer et afficher le sitemap XML
     */
    public function sitemap()
    {
        try {
            header('Content-Type: application/xml; charset=utf-8');
            echo SeoHelper::generateSitemap();
            exit;
        } catch (Exception $e) {
            error_log("Erreur génération sitemap: " . $e->getMessage());
            http_response_code(500);
            header('Content-Type: application/xml; charset=utf-8');
            echo '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></urlset>';
            exit;
        }
    }
    
    /**
     * Générer et afficher le robots.txt
     */
    public function robots()
    {
        try {
            header('Content-Type: text/plain; charset=utf-8');
            echo SeoHelper::generateRobotsTxt();
            exit;
        } catch (Exception $e) {
            error_log("Erreur génération robots.txt: " . $e->getMessage());
            http_response_code(500);
            header('Content-Type: text/plain; charset=utf-8');
            echo "User-agent: *\nDisallow: /";
            exit;
        }
    }
    
    /**
     * Page de test SEO (pour debug)
     */
    public function test()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            $this->redirect('/admin/login');
            return;
        }
        
        $data = [
            'title' => 'Test SEO - Belgium Video Gaming',
            'description' => 'Page de test pour vérifier les optimisations SEO',
            'keywords' => 'test, seo, belgium video gaming, belgique',
            'url' => 'https://belgium-video-gaming.be/seo/test',
            'image' => 'https://belgium-video-gaming.be/public/assets/images/default-featured.jpg',
            'type' => 'website'
        ];
        
        $seoMetaTags = SeoHelper::generateMetaTags($data);
        
        $this->render('admin/seo/test', [
            'seoMetaTags' => $seoMetaTags,
            'sitemap' => SeoHelper::generateSitemap(),
            'robots' => SeoHelper::generateRobotsTxt()
        ]);
    }
}
