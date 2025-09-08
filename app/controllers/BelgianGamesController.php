<?php
declare(strict_types=1);

/**
 * Contrôleur pour les jeux belges
 */

require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../models/Game.php';
require_once __DIR__ . '/../models/Article.php';

class BelgianGamesController extends Controller
{
    public function index(): void
    {
        $page = (int)($this->getQueryParam('page', 1));
        $limit = 12;
        $offset = ($page - 1) * $limit;

        // Récupérer les jeux belges
        $belgianGames = \Game::findBelgianGames($limit, $offset);
        $totalBelgianGames = \Game::countBelgianGames();
        $totalPages = ceil($totalBelgianGames / $limit);

        // Récupérer les articles liés aux jeux belges
        $belgianArticles = $this->getBelgianArticles();

        $this->render('layout/public', [
            'pageTitle' => '🇧🇪 Jeux Belges - ' . ($this->data['site_name'] ?? 'Belgium Video Gaming'),
            'pageDescription' => 'Découvrez tous les jeux développés par des studios belges. Une sélection exclusive des créations made in Belgium.',
            'isLoggedIn' => \Auth::isLoggedIn(),
            'user' => \Auth::getUser(),
            'content' => $this->renderPartial('belgian-games/index', [
                'belgianGames' => $belgianGames,
                'belgianArticles' => $belgianArticles,
                'currentPage' => $page,
                'totalPages' => $totalPages,
                'totalBelgianGames' => $totalBelgianGames
            ])
        ]);
    }

    /**
     * Récupérer les articles liés aux jeux belges
     */
    private function getBelgianArticles(): array
    {
        // Récupérer les articles récents (on peut filtrer plus tard si nécessaire)
        $filters = [
            'status' => 'published'
        ];
        
        $articles = \Article::findAll(1, 6, $filters);
        return $articles['articles'] ?? [];
    }
}
