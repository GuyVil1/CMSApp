<?php
declare(strict_types=1);

/**
 * Contrôleur Home - Page d'accueil du site
 */

require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../core/Database.php';
require_once __DIR__ . '/../models/Article.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/Game.php';
require_once __DIR__ . '/../models/Media.php';

class HomeController extends Controller
{
    /**
     * Page d'accueil avec articles en vedette et dernières news
     */
    public function index(): void
    {
        try {
            // Récupérer les articles en vedette (featured_position = 1)
            $featuredArticles = $this->getFeaturedArticles();
            
            // Récupérer les dernières news (articles publiés récents)
            $latestArticles = $this->getLatestArticles();
            
            // Récupérer les catégories populaires
            $popularCategories = $this->getPopularCategories();
            
            // Récupérer les jeux populaires
            $popularGames = $this->getPopularGames();
            
            // Récupérer les trailers (articles avec vidéos)
            $trailers = $this->getTrailers();
            
            $this->render('home/index', [
                'featuredArticles' => $featuredArticles,
                'latestArticles' => $latestArticles,
                'popularCategories' => $popularCategories,
                'popularGames' => $popularGames,
                'trailers' => $trailers,
                'isLoggedIn' => Auth::isLoggedIn(),
                'user' => Auth::getUser()
            ]);
            
        } catch (Exception $e) {
            // En cas d'erreur, afficher une page d'accueil avec des données par défaut
            $this->render('home/index', [
                'featuredArticles' => [],
                'latestArticles' => [],
                'popularCategories' => [],
                'popularGames' => [],
                'trailers' => [],
                'isLoggedIn' => Auth::isLoggedIn(),
                'user' => Auth::getUser(),
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Récupérer les articles en vedette
     */
    private function getFeaturedArticles(): array
    {
        $sql = "
            SELECT 
                a.id, a.title, a.slug, a.excerpt, a.status, a.published_at,
                a.featured_position, a.author_id, a.category_id, a.game_id,
                c.name as category_name, c.color as category_color,
                u.login as author_name,
                g.title as game_title,
                m.filename as cover_image
            FROM articles a
            LEFT JOIN categories c ON a.category_id = c.id
            LEFT JOIN users u ON a.author_id = u.id
            LEFT JOIN games g ON a.game_id = g.id
            LEFT JOIN media m ON a.cover_image_id = m.id
            WHERE a.status = 'published' 
            AND a.featured_position IS NOT NULL
            ORDER BY a.featured_position ASC
            LIMIT 6
        ";
        
        return Database::query($sql);
    }
    
    /**
     * Récupérer les dernières news
     */
    private function getLatestArticles(): array
    {
        $sql = "
            SELECT 
                a.id, a.title, a.slug, a.excerpt, a.status, a.published_at,
                a.author_id, a.category_id, a.game_id,
                c.name as category_name, c.color as category_color,
                u.login as author_name,
                g.title as game_title,
                m.filename as cover_image
            FROM articles a
            LEFT JOIN categories c ON a.category_id = c.id
            LEFT JOIN users u ON a.author_id = u.id
            LEFT JOIN games g ON a.game_id = g.id
            LEFT JOIN media m ON a.cover_image_id = m.id
            WHERE a.status = 'published'
            ORDER BY a.published_at DESC
            LIMIT 30
        ";
        
        return Database::query($sql);
    }
    
    /**
     * Récupérer les catégories populaires
     */
    private function getPopularCategories(): array
    {
        $sql = "
            SELECT 
                c.id, c.name, c.slug, c.color,
                COUNT(a.id) as article_count
            FROM categories c
            LEFT JOIN articles a ON c.id = a.category_id AND a.status = 'published'
            GROUP BY c.id, c.name, c.slug, c.color
            ORDER BY article_count DESC
            LIMIT 6
        ";
        
        return Database::query($sql);
    }
    
    /**
     * Récupérer les jeux populaires
     */
    private function getPopularGames(): array
    {
        $sql = "
            SELECT 
                g.id, g.title, g.slug, g.release_date,
                COUNT(a.id) as article_count,
                m.filename as cover_image
            FROM games g
            LEFT JOIN articles a ON g.id = a.game_id AND a.status = 'published'
            LEFT JOIN media m ON g.cover_image_id = m.id
            GROUP BY g.id, g.title, g.slug, g.release_date, m.filename
            ORDER BY article_count DESC, g.release_date DESC
            LIMIT 5
        ";
        
        return Database::query($sql);
    }
    
    /**
     * Récupérer les trailers (articles avec vidéos)
     */
    private function getTrailers(): array
    {
        $sql = "
            SELECT 
                a.id, a.title, a.slug, a.excerpt, a.published_at,
                c.name as category_name,
                u.login as author_name,
                m.filename as cover_image
            FROM articles a
            LEFT JOIN categories c ON a.category_id = c.id
            LEFT JOIN users u ON a.author_id = u.id
            LEFT JOIN media m ON a.cover_image_id = m.id
            WHERE a.status = 'published'
            AND a.content LIKE '%video%'
            ORDER BY a.published_at DESC
            LIMIT 5
        ";
        
        return Database::query($sql);
    }
}
