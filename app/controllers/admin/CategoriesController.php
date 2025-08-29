<?php
declare(strict_types=1);

/**
 * Contrôleur d'administration des catégories
 * Gestion des catégories (CRUD)
 */

namespace Admin;

require_once __DIR__ . '/../../../core/Controller.php';
require_once __DIR__ . '/../../../core/Auth.php';
require_once __DIR__ . '/../../models/Category.php';

class CategoriesController extends \Controller
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

        $limit = 20;
        $offset = ($page - 1) * $limit;

        $conditions = [];
        $params = [];

        if (!empty($search)) {
            $conditions[] = "name LIKE ?";
            $params[] = "%{$search}%";
        }

        $whereClause = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';

        $countQuery = "SELECT COUNT(*) as total FROM categories {$whereClause}";
        $totalCategories = \Category::countWithConditions($countQuery, $params);
        $totalPages = ceil($totalCategories / $limit);

        $query = "SELECT c.*, COUNT(a.id) as article_count
                  FROM categories c
                  LEFT JOIN articles a ON c.id = a.category_id AND a.status = 'published'
                  {$whereClause}
                  GROUP BY c.id
                  ORDER BY c.name ASC
                  LIMIT {$limit} OFFSET {$offset}";

        $categories = \Category::findWithQuery($query, $params);

        $this->render('admin/categories/index', [
            'categories' => $categories,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalCategories' => $totalCategories,
            'search' => $search
        ]);
    }

    public function create(): void
    {
        $this->render('admin/categories/create', [
            'error' => '',
            'success' => '',
            'csrf_token' => \Auth::generateCsrfToken()
        ]);
    }

    public function store(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirectTo('/categories.php');
            return;
        }

        // Validation CSRF
        if (!\Auth::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->render('admin/categories/create', [
                'error' => 'Token de sécurité invalide',
                'success' => '',
                'csrf_token' => \Auth::generateCsrfToken()
            ]);
            return;
        }

        $name = trim($_POST['name'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $description = trim($_POST['description'] ?? '');

        // Validation
        if (empty($name)) {
            $this->render('admin/categories/create', [
                'error' => 'Le nom de la catégorie est obligatoire',
                'success' => '',
                'csrf_token' => \Auth::generateCsrfToken()
            ]);
            return;
        }

        if (empty($slug)) {
            $slug = \Category::generateSlug($name);
        }

        // Vérifier si le slug existe déjà
        if (\Category::slugExists($slug)) {
            $this->render('admin/categories/create', [
                'error' => 'Ce slug existe déjà, veuillez en choisir un autre',
                'success' => '',
                'csrf_token' => \Auth::generateCsrfToken()
            ]);
            return;
        }

        $data = [
            'name' => $name,
            'slug' => $slug,
            'description' => $description ?: null
        ];

        // Créer la catégorie
        $category = \Category::create($data);

        if ($category) {
            $this->redirectTo('/categories.php?success=created');
        } else {
            $this->render('admin/categories/create', [
                'error' => 'Erreur lors de la création de la catégorie',
                'success' => '',
                'csrf_token' => \Auth::generateCsrfToken()
            ]);
        }
    }

    public function edit(int $id): void
    {
        $category = \Category::find($id);
        if (!$category) {
            $this->redirectTo('/categories.php?error=not_found');
            return;
        }

        $this->render('admin/categories/edit', [
            'category' => $category,
            'error' => '',
            'success' => '',
            'csrf_token' => \Auth::generateCsrfToken()
        ]);
    }

    public function update(int $id): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirectTo('/categories.php');
            return;
        }

        $category = \Category::find($id);
        if (!$category) {
            $this->redirectTo('/categories.php?error=not_found');
            return;
        }

        // Validation CSRF
        if (!\Auth::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->render('admin/categories/edit', [
                'category' => $category,
                'error' => 'Token de sécurité invalide',
                'success' => '',
                'csrf_token' => \Auth::generateCsrfToken()
            ]);
            return;
        }

        $name = trim($_POST['name'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $description = trim($_POST['description'] ?? '');

        // Validation
        if (empty($name)) {
            $this->render('admin/categories/edit', [
                'category' => $category,
                'error' => 'Le nom de la catégorie est obligatoire',
                'success' => '',
                'csrf_token' => \Auth::generateCsrfToken()
            ]);
            return;
        }

        if (empty($slug)) {
            $slug = \Category::generateSlug($name);
        }

        // Vérifier si le slug existe déjà (sauf pour cette catégorie)
        if (\Category::slugExists($slug, $id)) {
            $this->render('admin/categories/edit', [
                'category' => $category,
                'error' => 'Ce slug existe déjà, veuillez en choisir un autre',
                'success' => '',
                'csrf_token' => \Auth::generateCsrfToken()
            ]);
            return;
        }

        $data = [
            'name' => $name,
            'slug' => $slug,
            'description' => $description ?: null
        ];

        // Mettre à jour la catégorie
        if ($category->update($data)) {
            $this->redirectTo('/categories.php?success=updated');
        } else {
            $this->render('admin/categories/edit', [
                'category' => $category,
                'error' => 'Erreur lors de la mise à jour de la catégorie',
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

        // Validation CSRF
        if (!\Auth::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->json(['success' => false, 'error' => 'Token de sécurité invalide'], 403);
            return;
        }

        $category = \Category::find($id);
        if (!$category) {
            $this->json(['success' => false, 'error' => 'Catégorie non trouvée'], 404);
            return;
        }

        try {
            if ($category->delete()) {
                $this->json(['success' => true]);
            } else {
                $this->json(['success' => false, 'error' => 'Erreur lors de la suppression'], 500);
            }
        } catch (\Exception $e) {
            $this->json(['success' => false, 'error' => $e->getMessage()], 400);
        }
    }

}
