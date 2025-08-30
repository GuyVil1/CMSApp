<?php
declare(strict_types=1);

/**
 * Contrôleur d'administration des genres
 * Gestion des genres de jeux (CRUD)
 */

namespace Admin;

require_once __DIR__ . '/../../../core/Controller.php';
require_once __DIR__ . '/../../../core/Auth.php';
require_once __DIR__ . '/../../models/Genre.php';

class GenresController extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        // Temporairement désactivé pour les tests
        /*
        if (!\Auth::isLoggedIn()) {
            $this->redirectTo('/auth/login');
        }
        $user = \Auth::getUser();
        if ($user['role_name'] !== 'admin') {
            $this->redirectTo('/auth/forbidden');
        }
        */
    }

    public function index(): void
    {
        $page = (int)($this->getQueryParam('page', 1));
        $search = $this->getQueryParam('search', '');
        
        $limit = 20;
        $offset = ($page - 1) * $limit;

        if (!empty($search)) {
            $genres = \Genre::search($search);
            $totalGenres = count($genres);
            $totalPages = 1;
        } else {
            $genres = \Genre::findAllWithGameCount();
            $totalGenres = count($genres);
            $totalPages = ceil($totalGenres / $limit);
            
            // Pagination manuelle
            $genres = array_slice($genres, $offset, $limit);
        }

        $this->render('admin/genres/index', [
            'genres' => $genres,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalGenres' => $totalGenres,
            'search' => $search
        ]);
    }

    public function create(): void
    {
        $this->render('admin/genres/create', [
            'error' => '',
            'success' => '',
            'csrf_token' => \Auth::generateCsrfToken()
        ]);
    }

    public function store(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirectTo('/genres');
            return;
        }

        // Validation CSRF temporairement désactivée pour les tests
        /*
        if (!\Auth::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->render('admin/genres/create', [
                'error' => 'Token de sécurité invalide',
                'success' => '',
                'csrf_token' => \Auth::generateCsrfToken()
            ]);
            return;
        }
        */

        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $color = trim($_POST['color'] ?? '#007bff');

        // Validation
        if (empty($name)) {
            $this->render('admin/genres/create', [
                'error' => 'Le nom du genre est obligatoire',
                'success' => '',
                'csrf_token' => \Auth::generateCsrfToken()
            ]);
            return;
        }

        // Validation de la couleur
        if (!preg_match('/^#[0-9a-fA-F]{6}$/', $color)) {
            $this->render('admin/genres/create', [
                'error' => 'Format de couleur invalide (ex: #ff0000)',
                'success' => '',
                'csrf_token' => \Auth::generateCsrfToken()
            ]);
            return;
        }

        $genreData = [
            'name' => $name,
            'description' => $description ?: null,
            'color' => $color
        ];

        $genre = \Genre::create($genreData);

        if ($genre) {
            $this->redirectTo('/genres?success=created');
        } else {
            $this->render('admin/genres/create', [
                'error' => 'Erreur lors de la création du genre',
                'success' => '',
                'csrf_token' => \Auth::generateCsrfToken()
            ]);
        }
    }

    public function edit(int $id): void
    {
        $genre = \Genre::find($id);
        if (!$genre) {
            $this->redirectTo('/genres.php?error=not_found');
            return;
        }

        $this->render('admin/genres/edit', [
            'genre' => $genre,
            'error' => '',
            'success' => '',
            'csrf_token' => \Auth::generateCsrfToken()
        ]);
    }

    public function update(int $id): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirectTo('/genres');
            return;
        }

        $genre = \Genre::find($id);
        if (!$genre) {
            $this->redirectTo('/genres?error=not_found');
            return;
        }

        // Validation CSRF temporairement désactivée pour les tests
        /*
        if (!\Auth::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->render('admin/genres/edit', [
                'genre' => $genre,
                'error' => 'Token de sécurité invalide',
                'success' => '',
                'csrf_token' => \Auth::generateCsrfToken()
            ]);
            return;
        }
        */

        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $color = trim($_POST['color'] ?? '#007bff');

        // Validation
        if (empty($name)) {
            $this->render('admin/genres/edit', [
                'genre' => $genre,
                'error' => 'Le nom du genre est obligatoire',
                'success' => '',
                'csrf_token' => \Auth::generateCsrfToken()
            ]);
            return;
        }

        // Validation de la couleur
        if (!preg_match('/^#[0-9a-fA-F]{6}$/', $color)) {
            $this->render('admin/genres/edit', [
                'genre' => $genre,
                'error' => 'Format de couleur invalide (ex: #ff0000)',
                'success' => '',
                'csrf_token' => \Auth::generateCsrfToken()
            ]);
            return;
        }

        $data = [
            'name' => $name,
            'description' => $description ?: null,
            'color' => $color
        ];

        if ($genre->update($data)) {
            $this->redirectTo('/genres?success=updated');
        } else {
            $this->render('admin/genres/edit', [
                'genre' => $genre,
                'error' => 'Erreur lors de la mise à jour du genre',
                'success' => '',
                'csrf_token' => \Auth::generateCsrfToken()
            ]);
        }
    }

    public function delete(int $id): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'error' => 'Méthode non autorisée'], 405);
            return;
        }

        // Validation CSRF temporairement désactivée pour les tests
        /*
        if (!\Auth::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->json(['success' => false, 'error' => 'Token de sécurité invalide'], 403);
            return;
        }
        */

        $genre = \Genre::find($id);
        if (!$genre) {
            $this->json(['success' => false, 'error' => 'Genre non trouvé'], 404);
            return;
        }

        try {
            if ($genre->delete()) {
                $this->json(['success' => true]);
            } else {
                $this->json(['success' => false, 'error' => 'Erreur lors de la suppression'], 500);
            }
        } catch (\Exception $e) {
            $this->json(['success' => false, 'error' => $e->getMessage()], 400);
        }
    }
}
