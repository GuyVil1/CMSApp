<?php
declare(strict_types=1);

/**
 * Contrôleur d'administration des jeux
 * Gestion des jeux vidéo (CRUD)
 */

namespace Admin;

require_once __DIR__ . '/../../../core/Controller.php';
require_once __DIR__ . '/../../../core/Auth.php';
require_once __DIR__ . '/../../models/Game.php';
require_once __DIR__ . '/../../models/Media.php';
require_once __DIR__ . '/../../models/Hardware.php';
require_once __DIR__ . '/../../models/Genre.php';

class GamesController extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!\Auth::isLoggedIn()) {
            $this->redirectTo('/auth/login');
        }
        $user = \Auth::getUser();
        if ($user['role_name'] !== 'admin') {
            $this->redirectTo('/auth/forbidden');
        }
    }

    public function index(): void
    {
        $page = (int)($this->getQueryParam('page', 1));
        $search = $this->getQueryParam('search', '');
        $platform = $this->getQueryParam('platform', '');
        $genre = $this->getQueryParam('genre', '');

        $limit = 20;
        $offset = ($page - 1) * $limit;

        $conditions = [];
        $params = [];

        if (!empty($search)) {
            $conditions[] = "g.title LIKE ?";
            $params[] = "%{$search}%";
        }

        if (!empty($platform)) {
            $conditions[] = "g.platform LIKE ?";
            $params[] = "%{$platform}%";
        }

        if (!empty($genre)) {
            $conditions[] = "g.genre_id = ?";
            $params[] = (int)$genre;
        }

        $whereClause = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';

        // Compter le total
        $countQuery = "SELECT COUNT(*) as total FROM games g {$whereClause}";
        $totalGames = \Game::countWithConditions($countQuery, $params);
        $totalPages = ceil($totalGames / $limit);

        // Récupérer les jeux avec pagination
        $query = "SELECT g.*, m.filename as cover_image, 
                         h.name as hardware_name, h.type as hardware_type,
                         gr.name as genre_name, gr.color as genre_color,
                         COUNT(a.id) as article_count
                  FROM games g
                  LEFT JOIN media m ON g.cover_image_id = m.id
                  LEFT JOIN hardware h ON g.hardware_id = h.id
                  LEFT JOIN genres gr ON g.genre_id = gr.id
                  LEFT JOIN articles a ON g.id = a.game_id AND a.status = 'published'
                  {$whereClause}
                  GROUP BY g.id
                  ORDER BY g.created_at DESC
                  LIMIT {$limit} OFFSET {$offset}";

        $games = \Game::findWithQuery($query, $params);

        // Récupérer les plateformes et genres pour les filtres
        $platforms = \Game::getPlatforms();
        $genres = \Game::getGenres();

        $this->render('admin/games/index', [
            'games' => $games,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalGames' => $totalGames,
            'search' => $search,
            'platform' => $platform,
            'genre' => $genre,
            'platforms' => $platforms,
            'genres' => $genres
        ]);
    }

    public function create(): void
    {
        // Récupérer la liste des hardware actifs
        $hardwareList = \Hardware::findAllActive();
        
        // Récupérer la liste des genres
        $genres = \Genre::findAll();
        
        $this->render('admin/games/create', [
            'error' => '',
            'success' => '',
            'csrf_token' => \Auth::generateCsrfToken(),
            'hardware' => $hardwareList,
            'genres' => $genres
        ]);
    }

    public function store(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirectTo('/games.php');
            return;
        }

        // Validation CSRF
        if (!\Auth::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->render('admin/games/create', [
                'error' => 'Token de sécurité invalide',
                'success' => '',
                'csrf_token' => \Auth::generateCsrfToken(),
                'hardware' => \Hardware::findAllActive(),
                'genres' => \Genre::findAll()
            ]);
            return;
        }

        $title = trim($_POST['title'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $platform = trim($_POST['platform'] ?? '');
        $genreId = !empty($_POST['genre_id']) ? (int)$_POST['genre_id'] : null;
        $hardwareId = !empty($_POST['hardware_id']) ? (int)$_POST['hardware_id'] : null;
        $releaseDate = trim($_POST['release_date'] ?? '');

        // Validation
        if (empty($title)) {
            $this->render('admin/games/create', [
                'error' => 'Le titre du jeu est obligatoire',
                'success' => '',
                'csrf_token' => \Auth::generateCsrfToken(),
                'hardware' => \Hardware::findAllActive(),
                'genres' => \Genre::findAll()
            ]);
            return;
        }

        if (empty($slug)) {
            $slug = \Game::generateSlug($title);
        }

        // Vérifier si le slug existe déjà
        if (\Game::slugExists($slug)) {
            $this->render('admin/games/create', [
                'error' => 'Ce slug existe déjà, veuillez en choisir un autre',
                'success' => '',
                'csrf_token' => \Auth::generateCsrfToken(),
                'hardware' => \Hardware::findAllActive(),
                'genres' => \Genre::findAll()
            ]);
            return;
        }

        // Validation de l'image de couverture
        if (!isset($_FILES['cover_image']) || $_FILES['cover_image']['error'] !== UPLOAD_ERR_OK) {
            $this->render('admin/games/create', [
                'error' => 'Veuillez sélectionner une image de couverture',
                'success' => '',
                'csrf_token' => \Auth::generateCsrfToken(),
                'hardware' => \Hardware::findAllActive(),
                'genres' => \Genre::findAll()
            ]);
            return;
        }

        $coverFile = $_FILES['cover_image'];
        
        // Vérifier le type de fichier
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        if (!in_array($coverFile['type'], $allowedTypes)) {
            $this->render('admin/games/create', [
                'error' => 'Format d\'image non supporté. Utilisez JPG, PNG ou GIF',
                'success' => '',
                'csrf_token' => \Auth::generateCsrfToken(),
                'hardware' => \Hardware::findAllActive(),
                'genres' => \Genre::findAll()
            ]);
            return;
        }

        // Vérifier la taille (5MB max)
        if ($coverFile['size'] > 5 * 1024 * 1024) {
            $this->render('admin/games/create', [
                'error' => 'L\'image ne doit pas dépasser 5MB',
                'success' => '',
                'csrf_token' => \Auth::generateCsrfToken(),
                'hardware' => \Hardware::findAllActive(),
                'genres' => \Genre::findAll()
            ]);
            return;
        }

        // Créer le jeu d'abord
        $gameData = [
            'title' => $title,
            'slug' => $slug,
            'description' => $description ?: null,
            'platform' => $platform ?: null,
            'genre_id' => $genreId,
            'hardware_id' => $hardwareId,
            'release_date' => $releaseDate ?: null,
            'cover_image_id' => null // Sera mis à jour après l'upload
        ];

        $game = \Game::create($gameData);

        if (!$game) {
            $this->render('admin/games/create', [
                'error' => 'Erreur lors de la création du jeu',
                'success' => '',
                'csrf_token' => \Auth::generateCsrfToken(),
                'hardware' => \Hardware::findAllActive(),
                'genres' => \Genre::findAll()
            ]);
            return;
        }

        // Créer le dossier pour le jeu
        $gameDir = \Media::createGameDirectory($slug);
        
        // Générer le nom de fichier (cover.jpg)
        $extension = pathinfo($coverFile['name'], PATHINFO_EXTENSION);
        $filename = 'cover.' . strtolower($extension);
        $filepath = $gameDir . '/' . $filename;

        // Déplacer le fichier uploadé
        if (!move_uploaded_file($coverFile['tmp_name'], $filepath)) {
            // Supprimer le jeu si l'upload échoue
            $game->delete();
            $this->render('admin/games/create', [
                'error' => 'Erreur lors de l\'upload de l\'image',
                'success' => '',
                'csrf_token' => \Auth::generateCsrfToken(),
                'hardware' => \Hardware::findAllActive(),
                'genres' => \Genre::findAll()
            ]);
            return;
        }

        // Créer l'entrée dans la table media
        $mediaData = [
            'filename' => 'games/' . $slug . '/' . $filename,
            'original_name' => $coverFile['name'],
            'mime_type' => $coverFile['type'],
            'size' => $coverFile['size'],
            'uploaded_by' => \Auth::getUser()['id'],
            'game_id' => $game->getId(),
            'media_type' => 'cover'
        ];

        $media = \Media::create($mediaData);

        if ($media) {
            // Mettre à jour le jeu avec l'ID de la cover
            $game->update(['cover_image_id' => $media->getId()]);
            $this->redirectTo('/games.php?success=created');
        } else {
            // Supprimer le jeu et le fichier si l'enregistrement échoue
            $game->delete();
            if (file_exists($filepath)) {
                unlink($filepath);
            }
            $this->render('admin/games/create', [
                'error' => 'Erreur lors de l\'enregistrement de l\'image',
                'success' => '',
                'csrf_token' => \Auth::generateCsrfToken(),
                'hardware' => \Hardware::findAllActive(),
                'genres' => \Genre::findAll()
            ]);
        }
    }

    public function edit(int $id): void
    {
        $game = \Game::find($id);
        if (!$game) {
            $this->redirectTo('/games.php?error=not_found');
            return;
        }

        // Récupérer la liste des hardware actifs
        $hardwareList = \Hardware::findAllActive();
        
        // Récupérer la liste des genres
        $genres = \Genre::findAll();

        $this->render('admin/games/edit', [
            'game' => $game,
            'error' => '',
            'success' => '',
            'csrf_token' => \Auth::generateCsrfToken(),
            'hardware' => $hardwareList,
            'genres' => $genres
        ]);
    }

    public function update(int $id): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirectTo('/games.php');
            return;
        }

        $game = \Game::find($id);
        if (!$game) {
            $this->redirectTo('/games.php?error=not_found');
            return;
        }

        // Validation CSRF
        if (!\Auth::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->render('admin/games/edit', [
                'game' => $game,
                'error' => 'Token de sécurité invalide',
                'success' => '',
                'csrf_token' => \Auth::generateCsrfToken(),
                'hardware' => \Hardware::findAllActive(),
                'genres' => \Genre::findAll()
            ]);
            return;
        }

        $title = trim($_POST['title'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $platform = trim($_POST['platform'] ?? '');
        $genreId = !empty($_POST['genre_id']) ? (int)$_POST['genre_id'] : null;
        $hardwareId = !empty($_POST['hardware_id']) ? (int)$_POST['hardware_id'] : null;
        $releaseDate = trim($_POST['release_date'] ?? '');
        $coverImageId = !empty($_POST['cover_image_id']) ? (int)$_POST['cover_image_id'] : null;

        // Validation
        if (empty($title)) {
            $this->render('admin/games/edit', [
                'game' => $game,
                'error' => 'Le titre du jeu est obligatoire',
                'success' => '',
                'csrf_token' => \Auth::generateCsrfToken(),
                'hardware' => \Hardware::findAllActive(),
                'genres' => \Genre::findAll()
            ]);
            return;
        }

        if (empty($slug)) {
            $slug = \Game::generateSlug($title);
        }

        // Vérifier si le slug existe déjà (sauf pour ce jeu)
        if (\Game::slugExists($slug, $id)) {
            $this->render('admin/games/edit', [
                'game' => $game,
                'error' => 'Ce slug existe déjà, veuillez en choisir un autre',
                'success' => '',
                'csrf_token' => \Auth::generateCsrfToken(),
                'hardware' => \Hardware::findAllActive(),
                'genres' => \Genre::findAll()
            ]);
            return;
        }

        $data = [
            'title' => $title,
            'slug' => $slug,
            'description' => $description ?: null,
            'platform' => $platform ?: null,
            'genre_id' => $genreId,
            'hardware_id' => $hardwareId,
            'release_date' => $releaseDate ?: null,
            'cover_image_id' => $coverImageId
        ];

        // Mettre à jour le jeu
        if ($game->update($data)) {
            $this->redirectTo('/games.php?success=updated');
        } else {
            $this->render('admin/games/edit', [
                'game' => $game,
                'error' => 'Erreur lors de la mise à jour du jeu',
                'success' => '',
                'csrf_token' => \Auth::generateCsrfToken(),
                'hardware' => \Hardware::findAllActive(),
                'genres' => \Genre::findAll()
            ]);
        }
    }

    /**
     * Récupérer les informations d'un jeu (API)
     * Utilisé par le formulaire d'article pour récupérer la cover automatiquement
     */
    public function get(int $id): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            $this->json(['success' => false, 'error' => 'Méthode non autorisée'], 405);
            return;
        }

        $game = \Game::find($id);
        if (!$game) {
            $this->json(['success' => false, 'error' => 'Jeu non trouvé'], 404);
            return;
        }

        // Récupérer les informations de la cover si elle existe
        $coverImage = null;
        if ($game->getCoverImageId()) {
            $media = \Media::find($game->getCoverImageId());
            if ($media) {
                $coverImage = [
                    'id' => $media->getId(),
                    'url' => $media->getUrl(),
                    'filename' => $media->getFilename()
                ];
            }
        }

        $this->json([
            'success' => true,
            'game' => [
                'id' => $game->getId(),
                'title' => $game->getTitle(),
                'slug' => $game->getSlug(),
                'description' => $game->getDescription(),
                'platform' => $game->getPlatform(),
                'cover_image' => $coverImage
            ]
        ]);
    }

    public function delete(int $id): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'error' => 'Méthode non autorisée'], 405);
            return;
        }

        // Validation CSRF
        if (!\Auth::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->json(['success' => false, 'error' => 'Token de sécurité invalide'], 403);
            return;
        }

        $game = \Game::find($id);
        if (!$game) {
            $this->json(['success' => false, 'error' => 'Jeu non trouvé'], 404);
            return;
        }

        try {
            if ($game->delete()) {
                $this->json(['success' => true]);
            } else {
                $this->json(['success' => false, 'error' => 'Erreur lors de la suppression'], 500);
            }
        } catch (\Exception $e) {
            $this->json(['success' => false, 'error' => $e->getMessage()], 400);
        }
    }
}
