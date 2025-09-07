<?php
declare(strict_types=1);

/**
 * Contrôleur Home - Page d'accueil du site
 */

require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../core/Database.php';
require_once __DIR__ . '/../helpers/seo_helper.php';
require_once __DIR__ . '/../services/ArticleService.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/Game.php';
require_once __DIR__ . '/../models/Media.php';

class HomeController extends Controller
{
    private ArticleService $articleService;
    
    public function __construct()
    {
        parent::__construct();
        $this->articleService = new ArticleService();
    }
    
    /**
     * Page d'accueil avec articles en vedette et dernières news
     */
    public function index(): void
    {
        try {
            // Récupérer le thème actuel
            $currentTheme = $this->getCurrentTheme();
            
            // Récupérer l'état du mode sombre
            $darkMode = $this->isDarkModeEnabled();
            
            // Récupérer l'état des inscriptions
            $allowRegistration = $this->isRegistrationEnabled();
            
            // Récupérer les articles en vedette via le service
            $featuredArticles = $this->articleService->getFeaturedArticles(6);
            
            // Récupérer les dernières news via le service
            $latestArticles = $this->articleService->getRecentArticles(1, 10);
            
            // Récupérer les catégories populaires
            $popularCategories = $this->getPopularCategories();
            
            // Récupérer les jeux populaires
            $popularGames = $this->getPopularGames();
            
            // Récupérer les trailers (articles avec vidéos)
            $trailers = $this->getTrailers();
            
            // Générer les meta tags SEO pour la page d'accueil
            $seoController = new \SeoController();
            $seoMetaTags = $seoController->homeMetaTags();
            
            $this->render('layout/public', [
                'pageTitle' => 'Belgium Video Gaming - L\'actualité jeux vidéo en Belgique',
                'pageDescription' => 'On joue, on observe, on t\'éclaire. Pas de pub, pas de langue de bois — juste notre regard de passionnés, pour affiner le tien.',
                'seoMetaTags' => $seoMetaTags,
                'currentTheme' => $currentTheme,
                'darkMode' => $darkMode,
                'allowRegistration' => $allowRegistration,
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
                'pageTitle' => 'Belgium Video Gaming - L\'actualité jeux vidéo en Belgique',
                'pageDescription' => 'On joue, on observe, on t\'éclaire. Pas de pub, pas de langue de bois — juste notre regard de passionnés, pour affiner le tien.',
                'darkMode' => $this->isDarkModeEnabled(),
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
                a.id, a.title, a.slug, a.excerpt, a.status, a.published_at, a.created_at,
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
                a.id, a.title, a.slug, a.excerpt, a.status, a.published_at, a.created_at,
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
     * Récupérer les trailers (articles de la catégorie Trailers)
     */
    private function getTrailers(): array
    {
        $sql = "
            SELECT 
                a.id, a.title, a.slug, a.excerpt, a.published_at, a.created_at,
                c.name as category_name,
                u.login as author_name,
                m.filename as cover_image
            FROM articles a
            LEFT JOIN categories c ON a.category_id = c.id
            LEFT JOIN users u ON a.author_id = u.id
            LEFT JOIN media m ON a.cover_image_id = m.id
            WHERE a.status = 'published'
            AND c.name = 'Trailers'
            ORDER BY a.published_at DESC
            LIMIT 10
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
     * Vérifier si le mode sombre est activé
     */
    private function isDarkModeEnabled(): bool
    {
        try {
            require_once __DIR__ . '/../models/Setting.php';
            return \Setting::isEnabled('dark_mode');
        } catch (\Exception $e) {
            return false;
        }
    }
    
    /**
     * Vérifier si les inscriptions sont autorisées
     */
    private function isRegistrationEnabled(): bool
    {
        try {
            require_once __DIR__ . '/../models/Setting.php';
            return \Setting::isEnabled('allow_registration');
        } catch (\Exception $e) {
            return true; // Par défaut, on autorise les inscriptions
        }
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
            
            // Récupérer l'état du mode sombre
            $darkMode = $this->isDarkModeEnabled();
            
            // Récupérer les articles liés (même catégorie ou jeu)
            $relatedArticles = $this->getRelatedArticles($article);
            
            // Récupérer les articles populaires pour la sidebar
            $popularArticles = $this->getPopularArticles();
            
            // Vérifier si c'est un dossier et charger ses chapitres
            $dossierChapters = null;
            $isDossier = false;
            if ($article->getCategoryId() == 10) { // ID de la catégorie "Dossiers"
                $isDossier = true;
                $dossierChapters = $this->getDossierChapters($article->getId());
                error_log("📚 Dossier détecté avec " . count($dossierChapters) . " chapitres");
            }
            
            // Debug complet pour comprendre le problème
            error_log("🔍 DEBUG DOSSIER:");
            error_log("🔍 Article ID: " . $article->getId());
            error_log("🔍 Article Slug: " . $article->getSlug());
            error_log("🔍 Category ID: " . $article->getCategoryId());
            error_log("🔍 Category Name: " . $article->getCategoryName());
            error_log("🔍 Is Dossier: " . ($isDossier ? 'OUI' : 'NON'));
            error_log("🔍 Nombre de chapitres: " . ($dossierChapters ? count($dossierChapters) : 'NULL'));
            
            if ($isDossier && $dossierChapters) {
                error_log("🔍 Chapitres trouvés:");
                foreach ($dossierChapters as $index => $chapter) {
                    error_log("🔍 Chapitre " . ($index + 1) . ": " . $chapter['title'] . " (ID: " . $chapter['id'] . ")");
                }
            }
            
            error_log("🎨 Rendu de l'article: " . $article->getTitle());
            
            // Générer les meta tags SEO pour l'article
            $seoMetaTags = SeoHelper::generateArticleMetaTags($article);
            
            // Utiliser le template unifié public
            $this->render('layout/public', [
                'pageTitle' => $article->getTitle() . ' - Belgium Video Gaming',
                'pageDescription' => $article->getExcerpt() ?? 'Découvrez cet article sur Belgium Video Gaming, votre source gaming belge.',
                'seoMetaTags' => $seoMetaTags,
                'currentTheme' => $currentTheme,
                'darkMode' => $darkMode,
                'article' => $article,
                'relatedArticles' => $relatedArticles,
                'popularArticles' => $popularArticles,
                'isDossier' => $isDossier,
                'dossierChapters' => $dossierChapters,
                'isLoggedIn' => Auth::isLoggedIn(),
                'user' => Auth::getUser(),
                'additionalCSS' => [
                    '/public/assets/css/components/article-display.css',
                    '/public/assets/css/components/content-modules.css',
                    '/public/assets/css/components/article-hero.css',
                    '/public/assets/css/components/article-meta.css',
                    '/public/assets/css/components/dossier-chapters.css'
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
            SELECT a.id, a.title, a.slug, a.excerpt, a.published_at, a.created_at,
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
    
    /**
     * Récupérer les chapitres d'un dossier
     */
    private function getDossierChapters(int $dossierId): array
    {
        try {
            error_log("🔍 getDossierChapters appelé avec dossier ID: " . $dossierId);
            
            $sql = "
                SELECT 
                    dc.id, dc.title, dc.slug, dc.content, dc.excerpt, dc.chapter_order, 
                    dc.reading_time, dc.created_at, dc.updated_at,
                    m.filename as cover_image,
                    m.original_name as cover_image_name
                FROM dossier_chapters dc
                LEFT JOIN media m ON dc.cover_image_id = m.id
                WHERE dc.dossier_id = ? AND dc.status = 'published'
                ORDER BY dc.chapter_order ASC
            ";
            
            $chapters = Database::query($sql, [$dossierId]);
            error_log("🔍 Requête SQL exécutée: " . $sql);
            error_log("🔍 Paramètres: [" . $dossierId . "]");
            error_log("🔍 Résultat: " . count($chapters) . " chapitres trouvés");
            
            if (empty($chapters)) {
                error_log("🔍 Aucun chapitre trouvé - vérifions la table:");
                $checkSql = "SELECT COUNT(*) as total FROM dossier_chapters WHERE dossier_id = ?";
                $totalResult = Database::queryOne($checkSql, [$dossierId]);
                error_log("🔍 Total chapitres dans la table: " . ($totalResult['total'] ?? 'ERREUR'));
                
                $allChaptersSql = "SELECT id, title, status FROM dossier_chapters WHERE dossier_id = ?";
                $allChapters = Database::query($allChaptersSql, [$dossierId]);
                error_log("🔍 Tous les chapitres (tous statuts): " . count($allChapters));
                foreach ($allChapters as $ch) {
                    error_log("🔍 - Chapitre ID " . $ch['id'] . ": " . $ch['title'] . " (statut: " . $ch['status'] . ")");
                }
            }
            
            return $chapters;
        } catch (Exception $e) {
            error_log("❌ Erreur lors du chargement des chapitres du dossier: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Afficher un chapitre individuel d'un dossier
     */
    public function showChapter(string $dossierSlug, string $chapterSlug): void
    {
        try {
            error_log("🔍 HomeController::showChapter() appelé avec dossier: {$dossierSlug}, chapitre: {$chapterSlug}");
            
            // Récupérer le dossier par slug
            $dossier = \Article::findBySlug($dossierSlug);
            
            if (!$dossier) {
                error_log("❌ Dossier non trouvé pour slug: " . $dossierSlug);
                http_response_code(404);
                $this->render('layout/404', [
                    'message' => 'Dossier non trouvé',
                    'isLoggedIn' => Auth::isLoggedIn(),
                    'user' => Auth::getUser()
                ]);
                return;
            }
            
            // Vérifier que c'est bien un dossier
            if ($dossier->getCategoryId() != 10) {
                error_log("❌ Article non-dossier pour slug: " . $dossierSlug);
                http_response_code(404);
                $this->render('layout/404', [
                    'message' => 'Page non trouvée',
                    'isLoggedIn' => Auth::isLoggedIn(),
                    'user' => Auth::getUser()
                ]);
                return;
            }
            
            // Vérifier que le dossier est publié
            if ($dossier->getStatus() !== 'published' && !Auth::hasRole(['admin', 'editor'])) {
                error_log("🚫 Dossier non publié, statut: " . $dossier->getStatus());
                http_response_code(403);
                $this->render('layout/403', [
                    'message' => 'Ce dossier n\'est pas encore publié',
                    'isLoggedIn' => Auth::isLoggedIn(),
                    'user' => Auth::getUser()
                ]);
                return;
            }
            
            // Récupérer le chapitre par slug
            $chapter = $this->getDossierChapterBySlug($dossier->getId(), $chapterSlug);
            
            if (!$chapter) {
                error_log("❌ Chapitre non trouvé pour slug: " . $chapterSlug);
                http_response_code(404);
                $this->render('layout/404', [
                    'message' => 'Chapitre non trouvé',
                    'isLoggedIn' => Auth::isLoggedIn(),
                    'user' => Auth::getUser()
                ]);
                return;
            }
            
            // Vérifier que le chapitre est publié
            if ($chapter['status'] !== 'published' && !Auth::hasRole(['admin', 'editor'])) {
                error_log("🚫 Chapitre non publié, statut: " . $chapter['status']);
                http_response_code(403);
                $this->render('layout/403', [
                    'message' => 'Ce chapitre n\'est pas encore publié',
                    'isLoggedIn' => Auth::isLoggedIn(),
                    'user' => Auth::getUser()
                ]);
                return;
            }
            
            // Récupérer tous les chapitres du dossier pour la navigation
            $allChapters = $this->getDossierChapters($dossier->getId());
            
            // Trouver la position du chapitre actuel
            $currentChapterIndex = -1;
            foreach ($allChapters as $index => $ch) {
                if ($ch['id'] == $chapter['id']) {
                    $currentChapterIndex = $index;
                    break;
                }
            }
            
            // Déterminer les chapitres précédent et suivant
            $previousChapter = null;
            $nextChapter = null;
            
            if ($currentChapterIndex > 0) {
                $previousChapter = $allChapters[$currentChapterIndex - 1];
            }
            
            if ($currentChapterIndex < count($allChapters) - 1) {
                $nextChapter = $allChapters[$currentChapterIndex + 1];
            }
            
            // Récupérer le thème actuel
            $currentTheme = $this->getCurrentTheme();
            
            // Récupérer l'état du mode sombre
            $darkMode = $this->isDarkModeEnabled();
            
            error_log("🎨 Rendu du chapitre: " . $chapter['title']);
            
            // Utiliser le template unifié public
            $this->render('layout/public', [
                'pageTitle' => $chapter['title'] . ' - ' . $dossier->getTitle() . ' - GameNews Belgium',
                'pageDescription' => 'Chapitre du dossier ' . $dossier->getTitle() . ' sur GameNews, votre source gaming belge.',
                'currentTheme' => $currentTheme,
                'darkMode' => $darkMode,
                'dossier' => $dossier,
                'chapter' => $chapter,
                'allChapters' => $allChapters,
                'currentChapterIndex' => $currentChapterIndex,
                'previousChapter' => $previousChapter,
                'nextChapter' => $nextChapter,
                'isDossier' => true,
                'isChapter' => true,
                'isLoggedIn' => Auth::isLoggedIn(),
                'user' => Auth::getUser(),
                'additionalCSS' => [
                    '/public/assets/css/components/article-display.css',
                    '/public/assets/css/components/content-modules.css',
                    '/public/assets/css/components/article-hero.css',
                    '/public/assets/css/components/article-meta.css',
                    '/public/assets/css/components/dossier-chapters.css',
                    '/public/assets/css/components/chapter-navigation.css'
                ],
                'additionalJS' => [],
                'content' => $this->renderPartial('chapters/show', [
                    'dossier' => $dossier,
                    'chapter' => $chapter,
                    'allChapters' => $allChapters,
                    'currentChapterIndex' => $currentChapterIndex,
                    'previousChapter' => $previousChapter,
                    'nextChapter' => $nextChapter
                ])
            ]);
            
        } catch (Exception $e) {
            error_log("❌ Erreur dans HomeController::showChapter(): " . $e->getMessage());
            http_response_code(500);
            $this->render('layout/500', [
                'message' => 'Erreur lors du chargement du chapitre',
                'error' => Config::isLocal() ? $e->getMessage() : null,
                'isLoggedIn' => Auth::isLoggedIn(),
                'user' => Auth::getUser()
            ]);
        }
    }
    
    /**
     * Récupérer un chapitre spécifique par slug
     */
    private function getDossierChapterBySlug(int $dossierId, string $chapterSlug): ?array
    {
        try {
            $sql = "
                SELECT id, title, slug, content, cover_image_id, status, chapter_order, created_at, updated_at
                FROM dossier_chapters 
                WHERE dossier_id = ? AND slug = ? AND status = 'published'
                LIMIT 1
            ";
            
            $result = Database::queryOne($sql, [$dossierId, $chapterSlug]);
            return $result ?: null;
        } catch (Exception $e) {
            error_log("❌ Erreur lors du chargement du chapitre par slug: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Afficher la page d'un hardware spécifique
     */
    public function hardware(string $slug): void
    {
        try {
            error_log("🔍 HomeController::hardware() appelé avec slug: " . $slug);
            
            // Récupérer le hardware
            $hardware = Hardware::findBySlug($slug);
            
            if (!$hardware) {
                error_log("❌ Hardware non trouvé: " . $slug);
                http_response_code(404);
                $this->render('layout/404', [
                    'pageTitle' => 'Hardware non trouvé - Belgium Video Gaming',
                    'pageDescription' => 'Le hardware demandé n\'existe pas ou n\'est plus disponible.'
                ]);
                return;
            }
            
            error_log("✅ Hardware trouvé: " . $hardware->getName());
            
            // Récupérer les articles liés à ce hardware
            $articles = $this->getArticlesByHardware($hardware->getId());
            
            // Récupérer les jeux liés à ce hardware
            $games = $this->getGamesByHardware($hardware->getId());
            
            // Récupérer le thème actuel
            $currentTheme = $this->getCurrentTheme();
            
            // Récupérer l'état du mode sombre
            $darkMode = $this->isDarkModeEnabled();
            
            // Générer les meta tags SEO
            $seoMetaTags = SeoHelper::generateMetaTags([
                'title' => $hardware->getFullName() . ' - Belgium Video Gaming',
                'description' => 'Découvrez tous les articles et jeux pour ' . $hardware->getFullName() . ' sur Belgium Video Gaming.',
                'keywords' => strtolower($hardware->getName()) . ', ' . strtolower($hardware->getType()) . ', gaming, jeux vidéo, belgique',
                'url' => 'https://belgium-video-gaming.be/hardware/' . $hardware->getSlug(),
                'type' => 'website'
            ]);
            
            // Utiliser le template unifié public
            $this->render('layout/public', [
                'seoMetaTags' => $seoMetaTags,
                'currentTheme' => $currentTheme,
                'darkMode' => $darkMode,
                'hardware' => $hardware,
                'articles' => $articles,
                'games' => $games,
                'isLoggedIn' => Auth::isLoggedIn(),
                'user' => Auth::getUser(),
                'content' => $this->renderPartial('hardware/show', [
                    'hardware' => $hardware,
                    'articles' => $articles,
                    'games' => $games
                ])
            ]);
            
        } catch (Exception $e) {
            error_log("❌ Erreur dans HomeController::hardware(): " . $e->getMessage());
            http_response_code(500);
            $this->render('layout/500', [
                'pageTitle' => 'Erreur serveur - Belgium Video Gaming',
                'pageDescription' => 'Une erreur est survenue lors du chargement de la page.'
            ]);
        }
    }
    
    /**
     * Récupérer les articles liés à un hardware (via les jeux)
     */
    private function getArticlesByHardware(int $hardwareId): array
    {
        try {
            $sql = "
                SELECT a.*, c.name as category_name, c.slug as category_slug,
                       u.login as author_name, m.filename as cover_image,
                       g.title as game_title, g.slug as game_slug
                FROM articles a
                LEFT JOIN categories c ON a.category_id = c.id
                LEFT JOIN users u ON a.author_id = u.id
                LEFT JOIN media m ON a.cover_image_id = m.id
                LEFT JOIN games g ON a.game_id = g.id
                WHERE g.hardware_id = ? AND a.status = 'published'
                ORDER BY a.published_at DESC
                LIMIT 20
            ";
            
            return Database::query($sql, [$hardwareId]);
        } catch (Exception $e) {
            error_log("❌ Erreur lors de la récupération des articles par hardware: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Récupérer les jeux liés à un hardware
     */
    private function getGamesByHardware(int $hardwareId): array
    {
        try {
            $sql = "
                SELECT g.*, m.filename as cover_image
                FROM games g
                LEFT JOIN media m ON g.cover_image_id = m.id
                WHERE g.hardware_id = ? AND g.status = 'published'
                ORDER BY g.release_date DESC
                LIMIT 12
            ";
            
            return Database::query($sql, [$hardwareId]);
        } catch (Exception $e) {
            error_log("❌ Erreur lors de la récupération des jeux par hardware: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Afficher la page d'une catégorie spécifique
     */
    public function category(string $slug): void
    {
        try {
            error_log("🔍 HomeController::category() appelé avec slug: " . $slug);
            
            // Récupérer la catégorie
            $category = Category::findBySlug($slug);
            
            if (!$category) {
                error_log("❌ Catégorie non trouvée: " . $slug);
                http_response_code(404);
                $this->render('layout/404', [
                    'pageTitle' => 'Catégorie non trouvée - Belgium Video Gaming',
                    'pageDescription' => 'La catégorie demandée n\'existe pas ou n\'est plus disponible.'
                ]);
                return;
            }
            
            error_log("✅ Catégorie trouvée: " . $category->getName());
            
            // Récupérer les articles de cette catégorie
            $articles = $this->getArticlesByCategory($category->getId());
            
            // Récupérer le thème actuel
            $currentTheme = $this->getCurrentTheme();
            
            // Récupérer l'état du mode sombre
            $darkMode = $this->isDarkModeEnabled();
            
            // Générer les meta tags SEO
            $seoMetaTags = SeoHelper::generateMetaTags([
                'title' => $category->getName() . ' - Belgium Video Gaming',
                'description' => 'Découvrez tous les articles de la catégorie ' . $category->getName() . ' sur Belgium Video Gaming.',
                'keywords' => strtolower($category->getName()) . ', gaming, jeux vidéo, belgique, actualité',
                'url' => 'https://belgium-video-gaming.be/category/' . $category->getSlug(),
                'type' => 'website'
            ]);
            
            // Utiliser le template unifié public
            $this->render('layout/public', [
                'seoMetaTags' => $seoMetaTags,
                'currentTheme' => $currentTheme,
                'darkMode' => $darkMode,
                'category' => $category,
                'articles' => $articles,
                'isLoggedIn' => Auth::isLoggedIn(),
                'user' => Auth::getUser(),
                'content' => $this->renderPartial('categories/show', [
                    'category' => $category,
                    'articles' => $articles
                ])
            ]);
            
        } catch (Exception $e) {
            error_log("❌ Erreur dans HomeController::category(): " . $e->getMessage());
            http_response_code(500);
            $this->render('layout/500', [
                'pageTitle' => 'Erreur serveur - Belgium Video Gaming',
                'pageDescription' => 'Une erreur est survenue lors du chargement de la page.'
            ]);
        }
    }
    
    /**
     * Récupérer les articles d'une catégorie
     */
    private function getArticlesByCategory(int $categoryId): array
    {
        try {
            $sql = "
                SELECT a.*, c.name as category_name, c.slug as category_slug,
                       u.login as author_name, m.filename as cover_image
                FROM articles a
                LEFT JOIN categories c ON a.category_id = c.id
                LEFT JOIN users u ON a.author_id = u.id
                LEFT JOIN media m ON a.cover_image_id = m.id
                WHERE a.category_id = ? AND a.status = 'published'
                ORDER BY a.published_at DESC
                LIMIT 50
            ";
            
            return Database::query($sql, [$categoryId]);
        } catch (Exception $e) {
            error_log("❌ Erreur lors de la récupération des articles par catégorie: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Afficher la page de listing de tous les hardwares
     */
    public function hardwareList(): void
    {
        try {
            error_log("🔍 HomeController::hardwareList() appelé");
            
            // Récupérer tous les hardwares actifs
            $hardwares = Hardware::getAllActive();
            
            // Récupérer le thème actuel
            $currentTheme = $this->getCurrentTheme();
            
            // Récupérer l'état du mode sombre
            $darkMode = $this->isDarkModeEnabled();
            
            // Générer les meta tags SEO
            $seoMetaTags = SeoHelper::generateMetaTags([
                'title' => 'Hardware - Belgium Video Gaming',
                'description' => 'Découvrez tous les matériels de gaming : consoles, PC, accessoires sur Belgium Video Gaming.',
                'keywords' => 'hardware, gaming, consoles, pc, jeux vidéo, belgique, matériel',
                'url' => 'https://belgium-video-gaming.be/hardware',
                'type' => 'website'
            ]);
            
            // Utiliser le template unifié public
            $this->render('layout/public', [
                'seoMetaTags' => $seoMetaTags,
                'currentTheme' => $currentTheme,
                'darkMode' => $darkMode,
                'hardwares' => $hardwares,
                'isLoggedIn' => Auth::isLoggedIn(),
                'user' => Auth::getUser(),
                'content' => $this->renderPartial('hardware/index', [
                    'hardwares' => $hardwares
                ])
            ]);
            
        } catch (Exception $e) {
            error_log("❌ Erreur dans HomeController::hardwareList(): " . $e->getMessage());
            http_response_code(500);
            $this->render('layout/500', [
                'pageTitle' => 'Erreur serveur - Belgium Video Gaming',
                'pageDescription' => 'Une erreur est survenue lors du chargement de la page.'
            ]);
        }
    }
}
