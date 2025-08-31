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
            // Récupérer le thème actuel
            $currentTheme = $this->getCurrentTheme();
            
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
            
            $this->render('layout/public', [
                'pageTitle' => 'GameNews - L\'actualité jeux vidéo en Belgique',
                'pageDescription' => 'Votre source #1 pour l\'actualité jeux vidéo en Belgique. Reviews, tests, guides et tout l\'univers gaming depuis 2020.',
                'currentTheme' => $currentTheme,
                'featuredArticles' => $featuredArticles,
                'latestArticles' => $latestArticles,
                'popularCategories' => $popularCategories,
                'popularGames' => $popularGames,
                'trailers' => $trailers,
                'isLoggedIn' => Auth::isLoggedIn(),
                'user' => Auth::getUser(),
                'content' => $this->renderPartial('home/index', [
                    'currentTheme' => $currentTheme,
                    'featuredArticles' => $featuredArticles,
                    'latestArticles' => $latestArticles,
                    'popularCategories' => $popularCategories,
                    'popularGames' => $popularGames,
                    'trailers' => $trailers,
                    'isLoggedIn' => Auth::isLoggedIn(),
                    'user' => Auth::getUser()
                ])
            ]);
            
        } catch (Exception $e) {
            // En cas d'erreur, afficher une page d'accueil avec des données par défaut
            $this->render('layout/public', [
                'pageTitle' => 'GameNews - L\'actualité jeux vidéo en Belgique',
                'pageDescription' => 'Votre source #1 pour l\'actualité jeux vidéo en Belgique. Reviews, tests, guides et tout l\'univers gaming depuis 2020.',
                'featuredArticles' => [],
                'latestArticles' => [],
                'popularCategories' => [],
                'popularGames' => [],
                'trailers' => [],
                'isLoggedIn' => Auth::isLoggedIn(),
                'user' => Auth::getUser(),
                'error' => $e->getMessage(),
                'content' => $this->renderPartial('home/index', [
                    'featuredArticles' => [],
                    'latestArticles' => [],
                    'popularCategories' => [],
                    'popularGames' => [],
                    'trailers' => [],
                    'isLoggedIn' => Auth::isLoggedIn(),
                    'user' => Auth::getUser(),
                    'error' => $e->getMessage()
                ])
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
    
    /**
     * Récupérer le thème actuel
     */
    private function getCurrentTheme(): array
    {
        $configFile = __DIR__ . '/../../config/theme.json';
        
        if (file_exists($configFile)) {
            $config = json_decode(file_get_contents($configFile), true);
            
            // Vérifier si le thème a expiré
            if ($config && isset($config['expires_at']) && $config['expires_at']) {
                $expiresAt = strtotime($config['expires_at']);
                if ($expiresAt < time()) {
                    // Le thème a expiré, revenir au thème par défaut
                    $config['current_theme'] = $config['default_theme'];
                    $config['is_permanent'] = true;
                    $config['expires_at'] = null;
                    
                    // Sauvegarder la configuration mise à jour
                    file_put_contents($configFile, json_encode($config, JSON_PRETTY_PRINT));
                }
            }
            
            if ($config) {
                $themeName = $config['current_theme'] ?? 'defaut';
                return [
                    'name' => $themeName,
                    'is_permanent' => $config['is_permanent'] ?? true,
                    'expires_at' => $config['expires_at'] ?? null,
                    'applied_at' => $config['applied_at'] ?? null,
                    'left_image' => "themes/{$themeName}/left.png",
                    'right_image' => "themes/{$themeName}/right.png"
                ];
            }
        }
        
        return [
            'name' => 'defaut', 
            'is_permanent' => true, 
            'expires_at' => null, 
            'applied_at' => null,
            'left_image' => 'themes/defaut/left.png',
            'right_image' => 'themes/defaut/right.png'
        ];
    }
    
    /**
     * Afficher un article individuel
     */
    public function show(string $slug): void
    {
        try {
            // Debug
            error_log("🔍 HomeController::show() appelé avec slug: " . $slug);
            
            // Récupérer l'article par slug
            $article = \Article::findBySlug($slug);
            
            error_log("📚 Article trouvé: " . ($article ? 'OUI' : 'NON'));
            
            if (!$article) {
                error_log("❌ Article non trouvé pour slug: " . $slug);
                // Article non trouvé
                http_response_code(404);
                $this->render('layout/404', [
                    'message' => 'Article non trouvé',
                    'isLoggedIn' => Auth::isLoggedIn(),
                    'user' => Auth::getUser()
                ]);
                return;
            }
            
            error_log("✅ Article trouvé: " . $article->getTitle());
            
            // Vérifier que l'article est publié (sauf pour les admins/éditeurs)
            if ($article->getStatus() !== 'published' && !Auth::hasRole(['admin', 'editor'])) {
                error_log("🚫 Article non publié, statut: " . $article->getStatus());
                http_response_code(403);
                $this->render('layout/403', [
                    'message' => 'Cet article n\'est pas encore publié',
                    'isLoggedIn' => Auth::isLoggedIn(),
                    'user' => Auth::getUser()
                ]);
                return;
            }
            
            // Récupérer le thème actuel
            $currentTheme = $this->getCurrentTheme();
            
            // Récupérer les articles liés (même catégorie ou jeu)
            $relatedArticles = $this->getRelatedArticles($article);
            
            // Récupérer les articles populaires pour la sidebar
            $popularArticles = $this->getPopularArticles();
            
            error_log("🎨 Rendu de l'article: " . $article->getTitle());
            
            // Utiliser le template unifié public
            $this->render('layout/public', [
                'pageTitle' => $article->getTitle() . ' - GameNews Belgium',
                'pageDescription' => $article->getExcerpt() ?? 'Découvrez cet article sur GameNews, votre source gaming belge.',
                'currentTheme' => $currentTheme,
                'article' => $article,
                'relatedArticles' => $relatedArticles,
                'popularArticles' => $popularArticles,
                'isLoggedIn' => Auth::isLoggedIn(),
                'user' => Auth::getUser(),
                'additionalCSS' => [
                    '/public/assets/css/components/article-display.css',
                    '/public/assets/css/components/content-modules.css',
                    '/public/assets/css/components/article-hero.css',
                    '/public/assets/css/components/article-meta.css'
                ],
                'additionalJS' => []
            ]);
            
        } catch (Exception $e) {
            error_log("❌ Erreur dans HomeController::show(): " . $e->getMessage());
            // En cas d'erreur
            http_response_code(500);
            $this->render('layout/500', [
                'message' => 'Erreur lors du chargement de l\'article',
                'error' => Config::isLocal() ? $e->getMessage() : null,
                'isLoggedIn' => Auth::isLoggedIn(),
                'user' => Auth::getUser()
            ]);
        }
    }
    
    /**
     * Récupérer les articles liés
     */
    private function getRelatedArticles(\Article $article): array
    {
        $relatedArticles = [];
        
        // Articles de la même catégorie
        if ($article->getCategoryId()) {
            $sql = "
                SELECT a.id, a.title, a.slug, a.excerpt, a.published_at,
                       c.name as category_name, c.color as category_color,
                       u.login as author_name,
                       m.filename as cover_image
                FROM articles a
                LEFT JOIN categories c ON a.category_id = c.id
                LEFT JOIN users u ON a.author_id = u.id
                LEFT JOIN media m ON a.cover_image_id = m.id
                WHERE a.status = 'published' 
                AND a.category_id = ?
                AND a.id != ?
                ORDER BY a.published_at DESC
                LIMIT 3
            ";
            
            $categoryArticles = Database::query($sql, [$article->getCategoryId(), $article->getId()]);
            $relatedArticles = array_merge($relatedArticles, $categoryArticles);
        }
        
        // Articles du même jeu
        if ($article->getGameId() && count($relatedArticles) < 3) {
            $sql = "
                SELECT a.id, a.title, a.slug, a.excerpt, a.published_at,
                       c.name as category_name, c.color as category_color,
                       u.login as author_name,
                       m.filename as cover_image
                FROM articles a
                LEFT JOIN categories c ON a.category_id = c.id
                LEFT JOIN users u ON a.author_id = u.id
                LEFT JOIN media m ON a.cover_image_id = m.id
                WHERE a.status = 'published' 
                AND a.game_id = ?
                AND a.id != ?
                ORDER BY a.published_at DESC
                LIMIT ?
            ";
            
            $limit = 3 - count($relatedArticles);
            $gameArticles = Database::query($sql, [$article->getGameId(), $article->getId(), $limit]);
            $relatedArticles = array_merge($relatedArticles, $gameArticles);
        }
        
        return array_slice($relatedArticles, 0, 3);
    }
    
    /**
     * Récupérer les articles populaires
     */
    private function getPopularArticles(): array
    {
        $sql = "
            SELECT a.id, a.title, a.slug, a.excerpt, a.published_at,
                   c.name as category_name, c.color as category_color,
                   u.login as author_name,
                   m.filename as cover_image
            FROM articles a
            LEFT JOIN categories c ON a.category_id = c.id
            LEFT JOIN users u ON a.author_id = u.id
            LEFT JOIN media m ON a.cover_image_id = m.id
            WHERE a.status = 'published'
            ORDER BY a.published_at DESC
            LIMIT 5
        ";
        
        return Database::query($sql);
    }
}
