<?php
declare(strict_types=1);

/**
 * Contrôleur d'administration des utilisateurs
 * Gestion des comptes utilisateurs (CRUD, statuts, rôles)
 */

namespace Admin;

require_once __DIR__ . '/../../../core/Controller.php';
require_once __DIR__ . '/../../../core/Auth.php';
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../models/Role.php';

class UsersController extends \Controller
{
    /**
     * Constructeur - Vérification des permissions admin
     */
    public function __construct()
    {
        parent::__construct();
        
        // Vérifier que l'utilisateur est connecté et a les droits admin
        if (!\Auth::isLoggedIn()) {
            $this->redirectTo('/auth/login');
        }
        
        $user = \Auth::getUser();
        if ($user['role_name'] !== 'admin') {
            $this->redirectTo('/auth/forbidden');
        }
    }
    
    /**
     * Liste des utilisateurs avec filtres et pagination
     */
    public function index(): void
    {
        $page = (int)($this->getQueryParam('page', 1));
        $search = $this->getQueryParam('search', '');
        $role = $this->getQueryParam('role', '');
        
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        // Construire les conditions de recherche
        $conditions = [];
        $params = [];
        
        if (!empty($search)) {
            $conditions[] = "(u.login LIKE ? OR u.email LIKE ?)";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }
        
        if (!empty($role)) {
            $conditions[] = "r.name = ?";
            $params[] = $role;
        }
        
        $whereClause = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';
        
        // Compter le total des utilisateurs
        $countQuery = "SELECT COUNT(*) as total FROM users u 
                      LEFT JOIN roles r ON u.role_id = r.id 
                      {$whereClause}";
        
        $totalUsers = \User::countWithConditions($countQuery, $params);
        $totalPages = ceil($totalUsers / $limit);
        
        // Récupérer les utilisateurs
        $query = "SELECT u.*, r.name as role_name 
                  FROM users u 
                  LEFT JOIN roles r ON u.role_id = r.id 
                  {$whereClause}
                  ORDER BY u.created_at DESC 
                  LIMIT {$limit} OFFSET {$offset}";
        
        $users = \User::findWithQuery($query, $params);
        
        // Statistiques
        $adminUsers = \User::countWithConditions("SELECT COUNT(*) as total FROM users u LEFT JOIN roles r ON u.role_id = r.id WHERE r.name = 'admin'");
        
        $this->render('admin/users/index', [
            'users' => $users,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalUsers' => $totalUsers,
            'adminUsers' => $adminUsers,
            'search' => $search,
            'role' => $role
        ]);
    }
    
    /**
     * Afficher le formulaire de création
     */
    public function create(): void
    {
        $roles = \Role::findAll();
        
        $this->render('admin/users/create', [
            'roles' => $roles,
            'error' => '',
            'success' => '',
            'csrf_token' => \Auth::generateCsrfToken()
        ]);
    }
    
    /**
     * Traiter la création d'un utilisateur
     */
    public function store(): void
    {
        if (!$this->isPost()) {
            $this->redirectTo('/admin/users');
        }
        
        // Validation CSRF
        if (!\Auth::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->render('admin/users/create', [
                'roles' => \Role::findAll(),
                'error' => 'Token de sécurité invalide',
                'success' => '',
                'csrf_token' => \Auth::generateCsrfToken()
            ]);
            return;
        }
        
        $data = [
            'login' => trim($_POST['login'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'role_id' => (int)($_POST['role_id'] ?? 4)
        ];
        
        // Validation
        $errors = [];
        if (empty($data['login'])) $errors[] = 'Le nom d\'utilisateur est requis';
        if (empty($data['email'])) $errors[] = 'L\'email est requis';
        if (empty($data['password'])) $errors[] = 'Le mot de passe est requis';
        if (strlen($data['password']) < 8) $errors[] = 'Le mot de passe doit contenir au moins 8 caractères';
        
        if (!empty($errors)) {
            $this->render('admin/users/create', [
                'roles' => Role::findAll(),
                'error' => implode(', ', $errors),
                'success' => '',
                'csrf_token' => Auth::generateCsrfToken()
            ]);
            return;
        }
        
        // Créer l'utilisateur
        $user = \User::create($data);
        
        if ($user) {
            $this->redirectTo('/admin/users?success=created');
        } else {
            $this->render('admin/users/create', [
                'roles' => \Role::findAll(),
                'error' => 'Erreur lors de la création de l\'utilisateur',
                'success' => '',
                'csrf_token' => \Auth::generateCsrfToken()
            ]);
        }
    }
    
    /**
     * Afficher le formulaire d'édition
     */
    public function edit(int $id): void
    {
        $user = \User::findById($id);
        if (!$user) {
            $this->redirectTo('/admin/users?error=not_found');
        }
        
        $roles = \Role::findAll();
        
        $this->render('admin/users/edit', [
            'user' => $user,
            'roles' => $roles,
            'error' => '',
            'success' => '',
            'csrf_token' => \Auth::generateCsrfToken()
        ]);
    }
    
    /**
     * Traiter la mise à jour d'un utilisateur
     */
    public function update(int $id): void
    {
        if (!$this->isPost()) {
            $this->redirectTo('/admin/users');
        }
        
        $user = \User::findById($id);
        if (!$user) {
            $this->redirectTo('/admin/users?error=not_found');
        }
        
        // Validation CSRF
        if (!\Auth::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->render('admin/users/edit', [
                'user' => $user,
                'roles' => \Role::findAll(),
                'error' => 'Token de sécurité invalide',
                'success' => '',
                'csrf_token' => \Auth::generateCsrfToken()
            ]);
            return;
        }
        
        $data = [
            'login' => trim($_POST['login'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'role_id' => (int)($_POST['role_id'] ?? $user['role_id'])
        ];
        
        // Mot de passe optionnel
        if (!empty($_POST['password'])) {
            $data['password'] = $_POST['password'];
            if (strlen($data['password']) < 8) {
                $this->render('admin/users/edit', [
                    'user' => $user,
                    'roles' => Role::findAll(),
                    'error' => 'Le mot de passe doit contenir au moins 8 caractères',
                    'success' => '',
                    'csrf_token' => Auth::generateCsrfToken()
                ]);
                return;
            }
        }
        
        // Validation
        $errors = [];
        if (empty($data['login'])) $errors[] = 'Le nom d\'utilisateur est requis';
        if (empty($data['email'])) $errors[] = 'L\'email est requis';
        
        if (!empty($errors)) {
            $this->render('admin/users/edit', [
                'user' => $user,
                'roles' => Role::findAll(),
                'error' => implode(', ', $errors),
                'success' => '',
                'csrf_token' => Auth::generateCsrfToken()
            ]);
            return;
        }
        
        // Mettre à jour l'utilisateur
        if (\User::update($id, $data)) {
            $this->redirectTo('/admin/users?success=updated');
        } else {
            $this->render('admin/users/edit', [
                'user' => $user,
                'roles' => \Role::findAll(),
                'error' => 'Erreur lors de la mise à jour de l\'utilisateur',
                'success' => '',
                'csrf_token' => \Auth::generateCsrfToken()
            ]);
        }
    }
    
    /**
     * Supprimer un utilisateur
     */
    public function delete(int $id): void
    {
        // Debug: vérifier la méthode HTTP
        error_log("Méthode HTTP: " . $_SERVER['REQUEST_METHOD']);
        error_log("POST data: " . print_r($_POST, true));
        
        if (!$this->isPost()) {
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Méthode non autorisée']);
            return;
        }
        
        // Validation CSRF
        if (!\Auth::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Token de sécurité invalide']);
            return;
        }
        
        $user = \User::findById($id);
        if (!$user) {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'Utilisateur non trouvé']);
            return;
        }
        
        // Empêcher la suppression de son propre compte
        $currentUser = \Auth::getUser();
        if ($user['id'] == $currentUser['id']) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Vous ne pouvez pas supprimer votre propre compte']);
            return;
        }
        
        if (\User::delete($id)) {
            echo json_encode(['success' => true]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Erreur lors de la suppression']);
        }
    }
}
