<?php
declare(strict_types=1);

/**
 * Contrôleur d'administration des hardware
 * Gestion des plateformes de jeux (CRUD)
 */

namespace Admin;

require_once __DIR__ . '/../../../core/Controller.php';
require_once __DIR__ . '/../../../core/Auth.php';
require_once __DIR__ . '/../../models/Hardware.php';

class HardwareController extends \Controller
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
        $type = $this->getQueryParam('type', '');

        $limit = 20;
        $offset = ($page - 1) * $limit;

        $conditions = [];
        $params = [];

        if (!empty($search)) {
            $conditions[] = "h.name LIKE ? OR h.manufacturer LIKE ?";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }

        if (!empty($type)) {
            $conditions[] = "h.type = ?";
            $params[] = $type;
        }

        $whereClause = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';

        // Compter le total avec conditions
        $totalHardware = \Hardware::countWithConditions($conditions, $params);
        $totalPages = ceil($totalHardware / $limit);

        // Récupérer les hardware avec pagination
        $hardwares = \Hardware::findAllWithPagination($limit, $offset, $conditions, $params);

        // Récupérer les types pour les filtres
        $types = \Hardware::getTypes();

        $this->render('admin/hardware/index', [
            'hardware' => $hardwares,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalHardware' => $totalHardware,
            'search' => $search,
            'type' => $type,
            'types' => $types
        ]);
    }

    public function create(): void
    {
        $types = \Hardware::getTypes();
        
        $this->render('admin/hardware/create', [
            'error' => '',
            'success' => '',
            'csrf_token' => \Auth::generateCsrfToken(),
            'types' => $types
        ]);
    }

    public function store(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirectTo('/hardware.php');
            return;
        }

        // Validation CSRF
        if (!\Auth::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->render('admin/hardware/create', [
                'error' => 'Token de sécurité invalide',
                'success' => '',
                'csrf_token' => \Auth::generateCsrfToken(),
                'types' => \Hardware::getTypes()
            ]);
            return;
        }

        $name = trim($_POST['name'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $type = trim($_POST['type'] ?? '');
        $manufacturer = trim($_POST['manufacturer'] ?? '');
        $releaseYear = !empty($_POST['release_year']) ? (int)$_POST['release_year'] : null;
        $description = trim($_POST['description'] ?? '');
        $isActive = isset($_POST['is_active']);
        $sortOrder = !empty($_POST['sort_order']) ? (int)$_POST['sort_order'] : 0;

        // Validation
        if (empty($name)) {
            $this->render('admin/hardware/create', [
                'error' => 'Le nom du hardware est obligatoire',
                'success' => '',
                'csrf_token' => \Auth::generateCsrfToken(),
                'types' => \Hardware::getTypes()
            ]);
            return;
        }

        if (empty($type) || !array_key_exists($type, \Hardware::getTypes())) {
            $this->render('admin/hardware/create', [
                'error' => 'Le type de hardware est obligatoire',
                'success' => '',
                'csrf_token' => \Auth::generateCsrfToken(),
                'types' => \Hardware::getTypes()
            ]);
            return;
        }

        if (empty($slug)) {
            $slug = \Hardware::generateSlug($name);
        }

        // Vérifier si le slug existe déjà
        if (\Hardware::slugExists($slug)) {
            $this->render('admin/hardware/create', [
                'error' => 'Ce slug existe déjà, veuillez en choisir un autre',
                'success' => '',
                'csrf_token' => \Auth::generateCsrfToken(),
                'types' => \Hardware::getTypes()
            ]);
            return;
        }

        $data = [
            'name' => $name,
            'slug' => $slug,
            'type' => $type,
            'manufacturer' => $manufacturer ?: null,
            'release_year' => $releaseYear,
            'description' => $description ?: null,
            'is_active' => $isActive,
            'sort_order' => $sortOrder
        ];

        // Créer le hardware
        $hardware = \Hardware::create($data);

        if ($hardware) {
            $this->redirectTo('/hardware.php?success=created');
        } else {
            $this->render('admin/hardware/create', [
                'error' => 'Erreur lors de la création du hardware',
                'success' => '',
                'csrf_token' => \Auth::generateCsrfToken(),
                'types' => \Hardware::getTypes()
            ]);
        }
    }

    public function edit(int $id): void
    {
        $hardware = \Hardware::find($id);
        if (!$hardware) {
            $this->redirectTo('/hardware.php?error=not_found');
            return;
        }

        $types = \Hardware::getTypes();

        $this->render('admin/hardware/edit', [
            'hardware' => $hardware,
            'error' => '',
            'success' => '',
            'csrf_token' => \Auth::generateCsrfToken(),
            'types' => $types
        ]);
    }

    public function update(int $id): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirectTo('/hardware.php');
            return;
        }

        $hardware = \Hardware::find($id);
        if (!$hardware) {
            $this->redirectTo('/hardware.php?error=not_found');
            return;
        }

        // Validation CSRF
        if (!\Auth::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->render('admin/hardware/edit', [
                'hardware' => $hardware,
                'error' => 'Token de sécurité invalide',
                'success' => '',
                'csrf_token' => \Auth::generateCsrfToken(),
                'types' => \Hardware::getTypes()
            ]);
            return;
        }

        $name = trim($_POST['name'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $type = trim($_POST['type'] ?? '');
        $manufacturer = trim($_POST['manufacturer'] ?? '');
        $releaseYear = !empty($_POST['release_year']) ? (int)$_POST['release_year'] : null;
        $description = trim($_POST['description'] ?? '');
        $isActive = isset($_POST['is_active']);
        $sortOrder = !empty($_POST['sort_order']) ? (int)$_POST['sort_order'] : 0;

        // Validation
        if (empty($name)) {
            $this->render('admin/hardware/edit', [
                'hardware' => $hardware,
                'error' => 'Le nom du hardware est obligatoire',
                'success' => '',
                'csrf_token' => \Auth::generateCsrfToken(),
                'types' => \Hardware::getTypes()
            ]);
            return;
        }

        if (empty($type) || !array_key_exists($type, \Hardware::getTypes())) {
            $this->render('admin/hardware/edit', [
                'hardware' => $hardware,
                'error' => 'Le type de hardware est obligatoire',
                'success' => '',
                'csrf_token' => \Auth::generateCsrfToken(),
                'types' => \Hardware::getTypes()
            ]);
            return;
        }

        if (empty($slug)) {
            $slug = \Hardware::generateSlug($name);
        }

        // Vérifier si le slug existe déjà (sauf pour ce hardware)
        if (\Hardware::slugExists($slug, $id)) {
            $this->render('admin/hardware/edit', [
                'hardware' => $hardware,
                'error' => 'Ce slug existe déjà, veuillez en choisir un autre',
                'success' => '',
                'csrf_token' => \Auth::generateCsrfToken(),
                'types' => \Hardware::getTypes()
            ]);
            return;
        }

        $data = [
            'name' => $name,
            'slug' => $slug,
            'type' => $type,
            'manufacturer' => $manufacturer ?: null,
            'release_year' => $releaseYear,
            'description' => $description ?: null,
            'is_active' => $isActive,
            'sort_order' => $sortOrder
        ];

        // Mettre à jour le hardware
        if ($hardware->update($data)) {
            $this->redirectTo('/hardware.php?success=updated');
        } else {
            $this->render('admin/hardware/edit', [
                'hardware' => $hardware,
                'error' => 'Erreur lors de la mise à jour du hardware',
                'success' => '',
                'csrf_token' => \Auth::generateCsrfToken(),
                'types' => \Hardware::getTypes()
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

        $hardware = \Hardware::find($id);
        if (!$hardware) {
            $this->json(['success' => false, 'error' => 'Hardware non trouvé'], 404);
            return;
        }

        try {
            if ($hardware->delete()) {
                $this->json(['success' => true]);
            } else {
                $this->json(['success' => false, 'error' => 'Erreur lors de la suppression'], 500);
            }
        } catch (\Exception $e) {
            $this->json(['success' => false, 'error' => $e->getMessage()], 400);
        }
    }
}
