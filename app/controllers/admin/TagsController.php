<?php
declare(strict_types=1);

/**
 * Contrôleur d'administration des tags
 * Gestion des tags (CRUD)
 */

namespace Admin;

require_once __DIR__ . '/../../../core/Controller.php';
require_once __DIR__ . '/../../../core/Auth.php';
require_once __DIR__ . '/../../models/Tag.php';

class TagsController extends \Controller
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
     * Liste des tags avec filtres et pagination
     */
    public function index(): void
    {
        $page = (int)($this->getQueryParam('page', 1));
        $search = $this->getQueryParam('search', '');
        
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        // Construire les conditions de recherche
        $conditions = [];
        $params = [];
        
        if (!empty($search)) {
            $conditions[] = "name LIKE ?";
            $params[] = "%{$search}%";
        }
        
        $whereClause = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';
        
        // Compter le total des tags
        $countQuery = "SELECT COUNT(*) as total FROM tags {$whereClause}";
        $totalTags = \Tag::countWithConditions($countQuery, $params);
        $totalPages = ceil($totalTags / $limit);
        
        // Récupérer les tags
        $query = "SELECT t.*, COUNT(at.article_id) as article_count 
                  FROM tags t 
                  LEFT JOIN article_tag at ON t.id = at.tag_id 
                  {$whereClause}
                  GROUP BY t.id 
                  ORDER BY t.name ASC 
                  LIMIT {$limit} OFFSET {$offset}";
        
        $tags = \Tag::findWithQuery($query, $params);
        
        $this->render('admin/tags/index', [
            'tags' => $tags,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalTags' => $totalTags,
            'search' => $search
        ]);
    }
    
    /**
     * Afficher le formulaire de création
     */
    public function create(): void
    {
        $this->render('admin/tags/create', [
            'error' => '',
            'success' => '',
            'csrf_token' => \Auth::generateCsrfToken()
        ]);
    }
    
    /**
     * Traiter la création d'un tag
     */
    public function store(): void
    {
        if (!$this->isPost()) {
            $this->redirectTo('/admin/tags');
        }
        
        // Validation CSRF
        if (!\Auth::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->render('admin/tags/create', [
                'error' => 'Token de sécurité invalide',
                'success' => '',
                'csrf_token' => \Auth::generateCsrfToken()
            ]);
            return;
        }
        
        $data = [
            'name' => trim($_POST['name'] ?? ''),
            'slug' => trim($_POST['slug'] ?? '')
        ];
        
        // Validation
        $errors = [];
        if (empty($data['name'])) $errors[] = 'Le nom du tag est requis';
        if (empty($data['slug'])) $errors[] = 'Le slug est requis';
        if (strlen($data['name']) > 80) $errors[] = 'Le nom ne peut pas dépasser 80 caractères';
        if (strlen($data['slug']) > 120) $errors[] = 'Le slug ne peut pas dépasser 120 caractères';
        
        if (!empty($errors)) {
            $this->render('admin/tags/create', [
                'error' => implode(', ', $errors),
                'success' => '',
                'csrf_token' => \Auth::generateCsrfToken()
            ]);
            return;
        }
        
        // Vérifier si le slug existe déjà
        if (\Tag::slugExists($data['slug'])) {
            $this->render('admin/tags/create', [
                'error' => 'Ce slug existe déjà, veuillez en choisir un autre',
                'success' => '',
                'csrf_token' => \Auth::generateCsrfToken()
            ]);
            return;
        }
        
        // Créer le tag
        $tag = \Tag::create($data);
        
        if ($tag) {
            $this->redirectTo('/admin/tags?success=created');
        } else {
            $this->render('admin/tags/create', [
                'error' => 'Erreur lors de la création du tag',
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
        $tag = \Tag::findById($id);
        if (!$tag) {
            $this->redirectTo('/admin/tags?error=not_found');
        }
        
        $this->render('admin/tags/edit', [
            'tag' => $tag,
            'error' => '',
            'success' => '',
            'csrf_token' => \Auth::generateCsrfToken()
        ]);
    }
    
    /**
     * Traiter la mise à jour d'un tag
     */
    public function update(int $id): void
    {
        if (!$this->isPost()) {
            $this->redirectTo('/admin/tags');
        }
        
        $tag = \Tag::findById($id);
        if (!$tag) {
            $this->redirectTo('/admin/tags?error=not_found');
        }
        
        // Validation CSRF
        if (!\Auth::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->render('admin/tags/edit', [
                'tag' => $tag,
                'error' => 'Token de sécurité invalide',
                'success' => '',
                'csrf_token' => \Auth::generateCsrfToken()
            ]);
            return;
        }
        
        $data = [
            'name' => trim($_POST['name'] ?? ''),
            'slug' => trim($_POST['slug'] ?? '')
        ];
        
        // Validation
        $errors = [];
        if (empty($data['name'])) $errors[] = 'Le nom du tag est requis';
        if (empty($data['slug'])) $errors[] = 'Le slug est requis';
        if (strlen($data['name']) > 80) $errors[] = 'Le nom ne peut pas dépasser 80 caractères';
        if (strlen($data['slug']) > 120) $errors[] = 'Le slug ne peut pas dépasser 120 caractères';
        
        if (!empty($errors)) {
            $this->render('admin/tags/edit', [
                'tag' => $tag,
                'error' => implode(', ', $errors),
                'success' => '',
                'csrf_token' => \Auth::generateCsrfToken()
            ]);
            return;
        }
        
        // Vérifier si le slug existe déjà (sauf pour ce tag)
        if (\Tag::slugExists($data['slug'], $id)) {
            $this->render('admin/tags/edit', [
                'tag' => $tag,
                'error' => 'Ce slug existe déjà, veuillez en choisir un autre',
                'success' => '',
                'csrf_token' => \Auth::generateCsrfToken()
            ]);
            return;
        }
        
        // Mettre à jour le tag
        if (\Tag::update($id, $data)) {
            $this->redirectTo('/admin/tags?success=updated');
        } else {
            $this->render('admin/tags/edit', [
                'tag' => $tag,
                'error' => 'Erreur lors de la mise à jour du tag',
                'success' => '',
                'csrf_token' => \Auth::generateCsrfToken()
            ]);
        }
    }
    
    /**
     * Supprimer un tag
     */
    public function delete(int $id): void
    {
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
        
        $tag = \Tag::findById($id);
        if (!$tag) {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'Tag non trouvé']);
            return;
        }
        
        if (\Tag::delete($id)) {
            echo json_encode(['success' => true]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Erreur lors de la suppression']);
        }
    }
}
