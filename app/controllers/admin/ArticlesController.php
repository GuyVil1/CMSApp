<?php
declare(strict_types=1);

/**
 * Contrôleur de gestion des articles - Belgium Vidéo Gaming
 */

namespace Admin;

require_once __DIR__ . '/../../../core/Controller.php';
require_once __DIR__ . '/../../../core/Auth.php';
require_once __DIR__ . '/../../../core/Database.php';
require_once __DIR__ . '/../../models/Article.php';
require_once __DIR__ . '/../../models/Media.php'; // Added for cover image validation
require_once __DIR__ . '/../../container/ContainerFactory.php';
require_once __DIR__ . '/../../events/ArticleEvents.php';

class ArticlesController extends \Controller
{
    private $userRole;
    private $userId;
    private $eventDispatcher;
    
    public function __construct()
    {
        // Vérifier que l'utilisateur est connecté et a les droits admin/editor
        \Auth::requireRole(['admin', 'editor']);
        
        // Stocker le rôle de l'utilisateur pour les vérifications de permissions
        $this->userRole = \Auth::getUserRole();
        $this->userId = \Auth::getUserId();
        
        // Récupérer l'EventDispatcher depuis le container
        try {
            $this->eventDispatcher = \ContainerFactory::make('EventDispatcher');
        } catch (Exception $e) {
            // Fallback si le container n'est pas disponible
            $this->eventDispatcher = null;
        }
    }
    
    /**
     * Liste des articles
     */
    public function index(): void
    {
        $page = (int)($_GET['page'] ?? 1);
        $search = $_GET['search'] ?? '';
        $status = $_GET['status'] ?? '';
        $category = (int)($_GET['category'] ?? 0);
        
        $filters = [];
        if ($search) $filters['search'] = $search;
        if ($status) $filters['status'] = $status;
        if ($category) $filters['category_id'] = $category;
        
        // Les rédacteurs ne voient que leurs propres articles
        if ($this->userRole === 'editor') {
            $filters['author_id'] = $this->userId;
        }
        
        $result = \Article::findAll($page, 20, $filters);
        
        // Charger les catégories pour le filtre
        $categories = \Database::query("SELECT id, name FROM categories ORDER BY name");
        
        $this->render('admin/articles/index', [
            'articles' => $result['articles'],
            'pagination' => [
                'current' => $result['current_page'],
                'total' => $result['pages'],
                'total_items' => $result['total']
            ],
            'filters' => [
                'search' => $search,
                'status' => $status,
                'category' => $category
            ],
            'categories' => $categories,
            'user' => [
                'role' => $this->userRole,
                'id' => $this->userId
            ]
        ]);
    }
    
    /**
     * Formulaire de création d'article
     */
    public function create(): void
    {
        // Charger les données nécessaires
        $categories = \Database::query("SELECT id, name FROM categories ORDER BY name");
        $games = \Database::query("SELECT id, title FROM games ORDER BY title");
        $tags = \Database::query("SELECT id, name FROM tags ORDER BY name");
        
        $this->render('admin/articles/form', [
            'article' => null,
            'categories' => $categories,
            'games' => $games,
            'tags' => $tags,
            'featured_positions' => $this->getFeaturedPositions(),
            'user' => [
                'role' => $this->userRole,
                'id' => $this->userId
            ]
        ]);
    }
    
    /**
     * Traitement de la création d'article
     */
    public function store(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/articles');
            return;
        }
        
        try {
            // Debug: Afficher les données reçues
            error_log("🔍 Données POST reçues: " . print_r($_POST, true));
            
            // Validation des données
            $title = trim($_POST['title'] ?? '');
            $excerpt = trim($_POST['excerpt'] ?? '');
            $content = trim($_POST['content'] ?? '');
            $status = trim($_POST['status'] ?? 'draft');
            
            // Validation avancée
            $errors = [];
            
            // Validation du titre
            $titleErrors = $this->validateArticleTitle($title);
            if (!empty($titleErrors)) {
                $errors = array_merge($errors, $titleErrors);
            }
            
            // Validation du contenu
            $contentErrors = $this->validateArticleContent($content);
            if (!empty($contentErrors)) {
                $errors = array_merge($errors, $contentErrors);
            }
            
            // Validation de l'extrait
            if (strlen($excerpt) > 500) {
                $errors[] = 'L\'extrait ne peut pas dépasser 500 caractères';
            }
            
            // Validation du statut
            if (!in_array($status, ['draft', 'published', 'archived'])) {
                $errors[] = 'Statut invalide';
            }
            
            // Les rédacteurs ne peuvent pas publier directement
            if ($this->userRole === 'editor' && $status === 'published') {
                $status = 'draft'; // Forcer en brouillon
                $_SESSION['warning'] = 'En tant que rédacteur, votre article a été sauvegardé en brouillon. Un administrateur devra le publier.';
            }
            
            if (!empty($errors)) {
                $_SESSION['error'] = implode('. ', $errors);
                $this->redirect('/admin/articles/create');
                return;
            }
            
            // Debug: Afficher les valeurs après trim
            error_log("🔍 Titre: '$title'");
            error_log("🔍 Contenu: '" . substr($content, 0, 100) . "...'");
            error_log("🔍 Longueur du contenu: " . strlen($content));
            $category_id = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null;
            $game_id = !empty($_POST['game_id']) ? (int)$_POST['game_id'] : null;
            $featured_position = !empty($_POST['featured_position']) ? (int)$_POST['featured_position'] : null;
            $cover_image_id = !empty($_POST['cover_image_id']) ? (int)$_POST['cover_image_id'] : null;
            $published_at = !empty($_POST['published_at']) ? $_POST['published_at'] : null;
            $tags = $_POST['tags'] ?? [];
            
            if (empty($title) || empty($content)) {
                throw new \Exception('Le titre et le contenu sont obligatoires');
            }
            
            // Validation du statut
            if (!in_array($status, ['draft', 'published', 'archived'])) {
                throw new \Exception('Statut invalide');
            }
            
            // Gestion de l'upload d'image d'illustration
            $uploadedImageId = null;
            if (isset($_FILES['cover_image_file']) && $_FILES['cover_image_file']['error'] === UPLOAD_ERR_OK) {
                $uploadedImageId = $this->handleImageUpload($_FILES['cover_image_file']);
            }
            
            // Validation de l'image de couverture
            if (empty($cover_image_id) && empty($uploadedImageId)) {
                // Si un jeu est sélectionné, essayer de récupérer sa cover
                if ($game_id) {
                    $game = \Database::queryOne("SELECT cover_image_id FROM games WHERE id = ?", [$game_id]);
                    if ($game && $game['cover_image_id']) {
                        $cover_image_id = $game['cover_image_id'];
                    } else {
                        throw new \Exception('L\'image de couverture est obligatoire. Veuillez sélectionner une image ou un jeu avec une cover.');
                    }
                } else {
                    throw new \Exception('L\'image de couverture est obligatoire');
                }
            }
            
            // Utiliser l'image uploadée si disponible
            if ($uploadedImageId) {
                $cover_image_id = $uploadedImageId;
            }
            
            // Vérifier que l'image existe
            $coverImage = \Media::find($cover_image_id);
            if (!$coverImage) {
                throw new \Exception('L\'image de couverture sélectionnée n\'existe pas');
            }
            
            // Vérifier la position en avant
            if ($featured_position) {
                if ($featured_position < 1 || $featured_position > 6) {
                    throw new \Exception('La position en avant doit être comprise entre 1 et 6');
                }
                // Pas de vérification de disponibilité - remplacement automatique autorisé
            }
            
            // Gestion de la date de publication
            if ($status === 'published' && $published_at) {
                $published_at = date('Y-m-d H:i:s', strtotime($published_at));
            } elseif ($status === 'published' && !$published_at) {
                $published_at = date('Y-m-d H:i:s'); // Publication immédiate
            } else {
                $published_at = null;
            }
            
            // Générer le slug
            $slug = \Article::generateSlug($title);
            
            // Créer l'article
            $articleData = [
                'title' => $title,
                'slug' => $slug,
                'excerpt' => $excerpt ?: null,
                'content' => $content,
                'status' => $status,
                'cover_image_id' => $cover_image_id,
                'author_id' => \Auth::getUser()['id'],
                'category_id' => $category_id,
                'game_id' => $game_id,
                'featured_position' => $featured_position,
                'published_at' => $published_at
            ];
            
            $articleId = \Article::create($articleData);
            
            if (!$articleId) {
                throw new \Exception('Erreur lors de la création de l\'article');
            }
            
            // Gérer le remplacement automatique si une position en avant est sélectionnée
            if ($featured_position) {
                \Article::replaceArticleInPosition($featured_position, $articleId);
            }
            
            // Ajouter les tags
            if (!empty($tags)) {
                $article = \Article::findById($articleId);
                if ($article) {
                    $article->addTags($tags);
                }
            }
            
            // Log de l'activité
            $statusText = $status === 'published' ? 'publié' : ($status === 'archived' ? 'archivé' : 'créé en brouillon');
            \Auth::logActivity(\Auth::getUserId(), "Article $statusText : $title");
            
            // Dispatcher l'événement de création d'article
            if ($this->eventDispatcher) {
                $event = new \ArticleCreatedEvent($articleId, [
                    'title' => $title,
                    'status' => $status,
                    'user_id' => $this->userId,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
                $this->eventDispatcher->dispatch($event);
            }
            
            $this->setFlash('success', "Article $statusText avec succès !");
            $this->redirect('/admin/articles');
            
        } catch (\Exception $e) {
            $this->setFlash('error', 'Erreur : ' . $e->getMessage());
            $this->redirect('/admin/articles/create');
        }
    }
    
    /**
     * Formulaire d'édition d'article
     */
    public function edit(int $id): void
    {
        $article = \Article::findById($id);
        if (!$article) {
            $this->setFlash('error', 'Article non trouvé');
            $this->redirect('/admin/articles');
            return;
        }
        
        // Vérifier les permissions : les rédacteurs ne peuvent éditer que leurs propres articles
        if ($this->userRole === 'editor' && $article['author_id'] != $this->userId) {
            $this->setFlash('error', 'Vous ne pouvez éditer que vos propres articles');
            $this->redirect('/admin/articles');
            return;
        }
        
        // Charger les données nécessaires
        $categories = \Database::query("SELECT id, name FROM categories ORDER BY name");
        $games = \Database::query("SELECT id, title FROM games ORDER BY title");
        $tags = \Database::query("SELECT id, name FROM tags ORDER BY name");
        
        // Récupérer les tags de l'article
        $articleTags = \Database::query(
            "SELECT tag_id FROM article_tag WHERE article_id = ?",
            [$id]
        );
        $articleTagIds = array_column($articleTags, 'tag_id');
        
        // Récupérer l'image de couverture existante
        $coverImage = null;
        if ($article->getCoverImageId()) {
            $coverImage = \Media::find($article->getCoverImageId());
        }
        
        $this->render('admin/articles/form', [
            'article' => $article,
            'categories' => $categories,
            'games' => $games,
            'tags' => $tags,
            'articleTagIds' => $articleTagIds,
            'featured_positions' => $this->getFeaturedPositions($id),
            'coverImage' => $coverImage,
            'user' => [
                'role' => $this->userRole,
                'id' => $this->userId
            ]
        ]);
    }
    
    /**
     * Traitement de la mise à jour d'article
     */
    public function update(int $id): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/articles');
            return;
        }
        
        $article = \Article::findById($id);
        if (!$article) {
            $this->setFlash('error', 'Article non trouvé');
            $this->redirect('/admin/articles');
            return;
        }
        
        // Vérifier les permissions : les rédacteurs ne peuvent modifier que leurs propres articles
        if ($this->userRole === 'editor' && $article['author_id'] != $this->userId) {
            $this->setFlash('error', 'Vous ne pouvez modifier que vos propres articles');
            $this->redirect('/admin/articles');
            return;
        }
        
        try {
            // Validation des données
            $title = trim($_POST['title'] ?? '');
            $excerpt = trim($_POST['excerpt'] ?? '');
            $content = trim($_POST['content'] ?? '');
            $category_id = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null;
            $game_id = !empty($_POST['game_id']) ? (int)$_POST['game_id'] : null;
            $featured_position = !empty($_POST['featured_position']) ? (int)$_POST['featured_position'] : null;
            $cover_image_id = !empty($_POST['cover_image_id']) ? (int)$_POST['cover_image_id'] : null;
            $tags = $_POST['tags'] ?? [];
            
            if (empty($title) || empty($content)) {
                throw new \Exception('Le titre et le contenu sont obligatoires');
            }
            
            // Gestion de l'upload d'image d'illustration
            $uploadedImageId = null;
            if (isset($_FILES['cover_image_file']) && $_FILES['cover_image_file']['error'] === UPLOAD_ERR_OK) {
                $uploadedImageId = $this->handleImageUpload($_FILES['cover_image_file']);
            }

            // Déterminer l'ID de l'image de couverture finale
            $finalCoverImageId = null;

            // Priorité 1: Nouvelle image uploadée
            if ($uploadedImageId) {
                $finalCoverImageId = $uploadedImageId;
            }
            // Priorité 2: Image existante conservée (si l'article est en édition et qu'aucune nouvelle image n'est uploadée)
            else if (isset($_POST['current_cover_image_id']) && $_POST['current_cover_image_id'] !== '') {
                $finalCoverImageId = (int)$_POST['current_cover_image_id'];
            }
            // Priorité 3: Image de jeu sélectionnée
            else if ($game_id) {
                $game = \Database::queryOne("SELECT cover_image_id FROM games WHERE id = ?", [$game_id]);
                if ($game && $game['cover_image_id']) {
                    $finalCoverImageId = $game['cover_image_id'];
                }
            }

            // Validation de l'image de couverture
            if (empty($finalCoverImageId)) {
                throw new \Exception('L\'image de couverture est obligatoire');
            }
            $cover_image_id = $finalCoverImageId;
            
            // Vérifier que l'image existe
            $coverImage = \Media::find($cover_image_id);
            if (!$coverImage) {
                throw new \Exception('L\'image de couverture sélectionnée n\'existe pas');
            }
            
            // Vérifier la position en avant
            if ($featured_position) {
                if ($featured_position < 1 || $featured_position > 6) {
                    throw new \Exception('La position en avant doit être comprise entre 1 et 6');
                }
                // Pas de vérification de disponibilité - remplacement automatique autorisé
            }
            
            // Gérer le remplacement automatique si la position change
            $oldPosition = $article->getFeaturedPosition();
            if ($featured_position && $oldPosition !== $featured_position) {
                // Libérer l'ancienne position de cet article
                if ($oldPosition) {
                    \Article::freePosition($oldPosition);
                }
                // Remplacer l'article dans la nouvelle position
                \Article::replaceArticleInPosition($featured_position, $id);
            } elseif (!$featured_position && $oldPosition) {
                // Libérer la position si on retire l'article de la mise en avant
                \Article::freePosition($oldPosition);
            }
            
            // Générer le slug
            $slug = \Article::generateSlug($title, $id);
            
            // Gestion du statut avec restrictions pour les rédacteurs
            $status = trim($_POST['status'] ?? $article['status']);
            if ($this->userRole === 'editor' && $status === 'published') {
                $status = 'draft'; // Forcer en brouillon pour les rédacteurs
                $_SESSION['warning'] = 'En tant que rédacteur, votre article reste en brouillon. Un administrateur devra le publier.';
            }
            
            // Mettre à jour l'article
            $articleData = [
                'title' => $title,
                'slug' => $slug,
                'excerpt' => $excerpt ?: null,
                'content' => $content,
                'status' => $status,
                'cover_image_id' => $cover_image_id,
                'category_id' => $category_id,
                'game_id' => $game_id,
                'featured_position' => $featured_position
            ];
            
            if (!$article->update($articleData)) {
                throw new \Exception('Erreur lors de la mise à jour de l\'article');
            }
            
            // Mettre à jour les tags
            $article->addTags($tags);
            
            // Log de l'activité
            \Auth::logActivity(\Auth::getUserId(), "Article mis à jour : $title");
            
            // Dispatcher l'événement de mise à jour d'article
            if ($this->eventDispatcher) {
                // Convertir l'objet Article en array pour l'événement
                $oldData = [
                    'title' => $article->getTitle(),
                    'status' => $article->getStatus(),
                    'excerpt' => $article->getExcerpt(),
                    'content' => $article->getContent(),
                    'category_id' => $article->getCategoryId(),
                    'game_id' => $article->getGameId(),
                    'cover_image_id' => $article->getCoverImageId(),
                    'featured_position' => $article->getFeaturedPosition()
                ];
                
                $newData = [
                    'title' => $title,
                    'status' => $status,
                    'user_id' => $this->userId,
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                
                $event = new \ArticleUpdatedEvent($id, $oldData, $newData);
                $this->eventDispatcher->dispatch($event);
            }
            
            $this->setFlash('success', 'Article mis à jour avec succès !');
            $this->redirect('/admin/articles');
            
        } catch (\Exception $e) {
            $this->setFlash('error', 'Erreur : ' . $e->getMessage());
            $this->redirect("/admin/articles/edit/$id");
        }
    }
    
    /**
     * Suppression d'un article
     */
    public function delete(int $id): void
    {
        $article = \Article::findById($id);
        if (!$article) {
            $this->setFlash('error', 'Article non trouvé');
            $this->redirect('/admin/articles');
            return;
        }
        
        // Vérifier les permissions : les rédacteurs ne peuvent supprimer que leurs propres articles
        if ($this->userRole === 'editor' && $article['author_id'] != $this->userId) {
            $this->setFlash('error', 'Vous ne pouvez supprimer que vos propres articles');
            $this->redirect('/admin/articles');
            return;
        }
        
        try {
            // Libérer la position en avant si elle existe
            $featuredPosition = $article->getFeaturedPosition();
            if ($featuredPosition) {
                \Article::freePosition($featuredPosition);
            }
            
            // Supprimer l'article
            if (!$article->delete()) {
                throw new \Exception('Erreur lors de la suppression de l\'article');
            }
            
            // Log de l'activité
            \Auth::logActivity(\Auth::getUserId(), "Article supprimé : {$article->getTitle()}");
            
            // Dispatcher l'événement de suppression d'article
            if ($this->eventDispatcher) {
                // Convertir l'objet Article en array pour l'événement
                $articleData = [
                    'title' => $article->getTitle(),
                    'slug' => $article->getSlug(),
                    'status' => $article->getStatus(),
                    'excerpt' => $article->getExcerpt(),
                    'content' => $article->getContent(),
                    'category_id' => $article->getCategoryId(),
                    'game_id' => $article->getGameId(),
                    'cover_image_id' => $article->getCoverImageId(),
                    'author_id' => $article->getAuthorId(),
                    'featured_position' => $article->getFeaturedPosition(),
                    'created_at' => $article->getCreatedAt(),
                    'published_at' => $article->getPublishedAt()
                ];
                
                $event = new \ArticleDeletedEvent($id, $articleData);
                $this->eventDispatcher->dispatch($event);
            }
            
            $this->setFlash('success', 'Article supprimé avec succès !');
            $this->redirect('/admin/articles');
            
        } catch (\Exception $e) {
            $this->setFlash('error', 'Erreur : ' . $e->getMessage());
            $this->redirect('/admin/articles');
        }
    }
    
    /**
     * Publier un article
     */
    public function publish(int $id): void
    {
        // Seuls les admins peuvent publier
        if ($this->userRole !== 'admin') {
            $this->setFlash('error', 'Seuls les administrateurs peuvent publier des articles');
            $this->redirect('/admin/articles');
            return;
        }
        
        $article = \Article::findById($id);
        if (!$article) {
            $this->setFlash('error', 'Article non trouvé');
            $this->redirect('/admin/articles');
            return;
        }
        
        try {
            if (!$article->publish()) {
                throw new \Exception('Erreur lors de la publication');
            }
            
            $this->setFlash('success', 'Article publié avec succès !');
            $this->redirect('/admin/articles');
            
        } catch (\Exception $e) {
            $this->setFlash('error', 'Erreur : ' . $e->getMessage());
            $this->redirect('/admin/articles');
        }
    }
    
    /**
     * Mettre en brouillon
     */
    public function draft(int $id): void
    {
        // Seuls les admins peuvent mettre en brouillon
        if ($this->userRole !== 'admin') {
            $this->setFlash('error', 'Seuls les administrateurs peuvent modifier le statut des articles');
            $this->redirect('/admin/articles');
            return;
        }
        
        $article = \Article::findById($id);
        if (!$article) {
            $this->setFlash('error', 'Article non trouvé');
            $this->redirect('/admin/articles');
            return;
        }
        
        try {
            if (!$article->draft()) {
                throw new \Exception('Erreur lors de la mise en brouillon');
            }
            
            $this->setFlash('success', 'Article mis en brouillon !');
            $this->redirect('/admin/articles');
            
        } catch (\Exception $e) {
            $this->setFlash('error', 'Erreur : ' . $e->getMessage());
            $this->redirect('/admin/articles');
        }
    }
    
    /**
     * Archiver un article
     */
    public function archive(int $id): void
    {
        // Seuls les admins peuvent archiver
        if ($this->userRole !== 'admin') {
            $this->setFlash('error', 'Seuls les administrateurs peuvent archiver des articles');
            $this->redirect('/admin/articles');
            return;
        }
        
        $article = \Article::findById($id);
        if (!$article) {
            $this->setFlash('error', 'Article non trouvé');
            $this->redirect('/admin/articles');
            return;
        }
        
        try {
            if ($article->archive()) {
                \Auth::logActivity(\Auth::getUserId(), "Article archivé : " . $article->getTitle());
                $this->setFlash('success', 'Article archivé !');
            } else {
                throw new \Exception('Erreur lors de l\'archivage');
            }
        } catch (\Exception $e) {
            $this->setFlash('error', 'Erreur : ' . $e->getMessage());
        }
        
        $this->redirect('/admin/articles');
    }
    
    /**
     * Obtenir les positions en avant disponibles
     * Toutes les positions sont disponibles pour permettre le remplacement automatique
     */
    private function getFeaturedPositions(?int $excludeArticleId = null): array
    {
        $positions = [];
        
        // Option "Pas en avant"
        $positions[] = [
            'position' => null,
            'label' => 'Pas en avant',
            'available' => true,
            'current_article' => null,
            'description' => 'Article non mis en avant'
        ];
        
        // Positions 1 à 6
        for ($i = 1; $i <= 6; $i++) {
            $currentArticle = \Article::getArticleInPosition($i);
            
            $positions[] = [
                'position' => $i,
                'label' => "Position $i",
                'available' => true, // Toujours disponible
                'current_article' => $currentArticle,
                'description' => $this->getPositionDescription($i)
            ];
        }
        
        return $positions;
    }
    
    /**
     * Obtenir la description d'une position pour l'interface
     */
    private function getPositionDescription(int $position): string
    {
        switch ($position) {
            case 1:
                return 'Article principal (2/3 largeur, 2/3 hauteur)';
            case 2:
                return 'Article moyen gauche (1/3 hauteur)';
            case 3:
                return 'Article moyen droit (1/3 hauteur)';
            case 4:
                return 'Article petit haut (1/3 largeur, 1/3 hauteur)';
            case 5:
                return 'Article petit milieu (1/3 largeur, 1/3 hauteur)';
            case 6:
                return 'Article petit bas (1/3 largeur, 1/3 hauteur)';
            default:
                return "Position $position";
        }
    }
    
    /**
     * Obtenir le label d'une position
     */
    private function getPositionLabel(int $position): string
    {
        switch ($position) {
            case 1:
                return 'Position 1 - Article principal (2/3 largeur, 2/3 hauteur)';
            case 2:
                return 'Position 2 - Article moyen gauche (1/3 hauteur)';
            case 3:
                return 'Position 3 - Article moyen droit (1/3 hauteur)';
            case 4:
                return 'Position 4 - Article petit haut (1/3 largeur, 1/3 hauteur)';
            case 5:
                return 'Position 5 - Article petit milieu (1/3 largeur, 1/3 hauteur)';
            case 6:
                return 'Position 6 - Article petit bas (1/3 largeur, 1/3 hauteur)';
            default:
                return "Position $position";
        }
    }

    /**
     * Sauvegarder les chapitres d'un dossier
     */
    public function saveChapters(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Méthode non autorisée']);
            return;
        }
        
        try {
            // Vérifier que l'utilisateur est connecté
            if (!\Auth::isLoggedIn()) {
                http_response_code(401);
                echo json_encode(['error' => 'Non autorisé']);
                return;
            }
            
            // Récupérer les données
            $articleId = (int)($_POST['article_id'] ?? 0);
            $chaptersJson = $_POST['chapters'] ?? '';
            
            if (!$articleId) {
                throw new \Exception('ID de l\'article manquant');
            }
            
            if (empty($chaptersJson)) {
                throw new \Exception('Aucun chapitre à sauvegarder');
            }
            
            // Décoder la chaîne JSON en tableau
            $chapters = json_decode($chaptersJson, true);
            if ($chapters === null) {
                throw new \Exception('Format des chapitres invalide (JSON invalide)');
            }
            
            if (!is_array($chapters)) {
                throw new \Exception('Format des chapitres invalide (doit être un tableau)');
            }
            
            // Vérifier que l'article existe et est bien un dossier
            $article = \Article::findById($articleId);
            if (!$article) {
                throw new \Exception('Article non trouvé');
            }
            
            if ($article->getCategoryId() != 10) { // ID 10 = Dossiers
                throw new \Exception('Cet article n\'est pas un dossier');
            }
            
            // Commencer une transaction
            \Database::beginTransaction();
            
            try {
                // Supprimer les anciens chapitres
                \Database::query("DELETE FROM dossier_chapters WHERE dossier_id = ?", [$articleId]);
                
                // Insérer les nouveaux chapitres
                $insertSql = "INSERT INTO dossier_chapters (dossier_id, title, slug, content, cover_image_id, status, chapter_order, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
                
                foreach ($chapters as $index => $chapter) {
                    $title = trim($chapter['title'] ?? '');
                    $slug = trim($chapter['slug'] ?? '');
                    $content = trim($chapter['content'] ?? '');
                    $coverImageId = !empty($chapter['cover_image_id']) ? (int)$chapter['cover_image_id'] : null;
                    $status = trim($chapter['status'] ?? 'draft');
                    
                    if (empty($title)) {
                        continue; // Ignorer les chapitres sans titre
                    }
                    
                    // Générer un slug si vide
                    if (empty($slug)) {
                        $slug = \Article::generateSlug($title);
                    }
                    
                    // Valider le statut
                    if (!in_array($status, ['draft', 'published'])) {
                        $status = 'draft';
                    }
                    
                    \Database::query($insertSql, [
                        $articleId,
                        $title,
                        $slug,
                        $content,
                        $coverImageId,
                        $status,
                        $index + 1
                    ]);
                }
                
                // Mettre à jour le progrès du dossier
                $totalChapters = count(array_filter($chapters, function($c) { return !empty(trim($c['title'] ?? '')); }));
                $publishedChapters = count(array_filter($chapters, function($c) { return trim($c['status'] ?? '') === 'published'; }));
                
                $progress = $totalChapters > 0 ? round(($publishedChapters / $totalChapters) * 100) : 0;
                
                \Database::query(
                    "UPDATE articles SET dossier_progress = ?, updated_at = NOW() WHERE id = ?",
                    [$progress, $articleId]
                );
                
                // Valider la transaction
                \Database::commit();
                
                // Log de l'activité
                \Auth::logActivity(\Auth::getUserId(), "Chapitres sauvegardés pour le dossier : " . $article->getTitle());
                
                // Définir le bon Content-Type
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => 'Chapitres sauvegardés avec succès',
                    'total_chapters' => $totalChapters,
                    'published_chapters' => $publishedChapters,
                    'progress' => $progress
                ]);
                
            } catch (\Exception $e) {
                \Database::rollback();
                throw $e;
            }
            
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Erreur : ' . $e->getMessage()]);
        }
    }
    
    /**
     * Charger les chapitres d'un dossier
     */
    public function loadChapters(int $articleId): void
    {
        try {
            // Vérifier que l'utilisateur est connecté
            if (!\Auth::isLoggedIn()) {
                http_response_code(401);
                echo json_encode(['error' => 'Non autorisé']);
                return;
            }
            
            // Vérifier que l'article existe et est bien un dossier
            $article = \Article::findById($articleId);
            if (!$article) {
                throw new \Exception('Article non trouvé');
            }
            
            if ($article->getCategoryId() != 10) { // ID 10 = Dossiers
                throw new \Exception('Cet article n\'est pas un dossier');
            }
            
            // Récupérer les chapitres
            $chapters = \Database::query(
                "SELECT id, title, slug, content, cover_image_id, status, chapter_order, created_at, updated_at FROM dossier_chapters WHERE dossier_id = ? ORDER BY chapter_order ASC",
                [$articleId]
            );
            
            // Définir le bon Content-Type
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'chapters' => $chapters
            ]);
            
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Erreur : ' . $e->getMessage()]);
        }
    }

    /**
     * Mettre à jour le statut d'un chapitre
     */
    public function updateChapterStatus(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
            return;
        }
        
        try {
            $chapterId = (int)($_POST['chapter_id'] ?? 0);
            $status = trim($_POST['status'] ?? '');
            
            if (!$chapterId) {
                throw new \Exception('ID de chapitre manquant');
            }
            
            if (!in_array($status, ['draft', 'published'])) {
                throw new \Exception('Statut invalide');
            }
            
            // Mettre à jour le statut du chapitre
            $sql = "UPDATE dossier_chapters SET status = ?, updated_at = NOW() WHERE id = ?";
            $result = \Database::execute($sql, [$status, $chapterId]);
            
            if ($result === false) {
                throw new \Exception('Erreur lors de la mise à jour du statut');
            }
            
            // Log de l'activité
            $statusText = $status === 'published' ? 'publié' : 'mis en brouillon';
            \Auth::logActivity(\Auth::getUserId(), "Chapitre $statusText (ID: $chapterId)");
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Statut du chapitre mis à jour avec succès',
                'status' => $status
            ]);
            
        } catch (\Exception $e) {
            error_log("❌ Erreur lors de la mise à jour du statut du chapitre: " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Erreur : ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Supprimer un chapitre
     */
    public function deleteChapter(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
            return;
        }
        
        try {
            $chapterId = (int)($_POST['chapter_id'] ?? 0);
            
            if (!$chapterId) {
                throw new \Exception('ID de chapitre manquant');
            }
            
            // Récupérer les informations du chapitre avant suppression
            $chapter = \Database::queryOne("SELECT title, dossier_id FROM dossier_chapters WHERE id = ?", [$chapterId]);
            if (!$chapter) {
                throw new \Exception('Chapitre non trouvé');
            }
            
            // Supprimer le chapitre
            $sql = "DELETE FROM dossier_chapters WHERE id = ?";
            $result = \Database::execute($sql, [$chapterId]);
            
            if ($result === false) {
                throw new \Exception('Erreur lors de la suppression du chapitre');
            }
            
            // Log de l'activité
            \Auth::logActivity(\Auth::getUserId(), "Chapitre supprimé : {$chapter['title']} (ID: $chapterId)");
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Chapitre supprimé avec succès'
            ]);
            
        } catch (\Exception $e) {
            error_log("❌ Erreur lors de la suppression du chapitre: " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Erreur : ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Gérer l'upload d'une image
     */
    private function handleImageUpload(array $file): int
    {
        // Validation du fichier
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
        $maxSize = 4 * 1024 * 1024; // 4MB
        
        if (!in_array($file['type'], $allowedTypes)) {
            throw new \Exception('Type de fichier non autorisé. Utilisez JPG, PNG ou WebP.');
        }
        
        if ($file['size'] > $maxSize) {
            throw new \Exception('Fichier trop volumineux. Taille maximum : 4MB.');
        }
        
        // Vérifier le MIME type réel
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mimeType, $allowedTypes)) {
            throw new \Exception('Type de fichier invalide détecté.');
        }
        
        // Générer un nom de fichier unique
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '_' . time() . '.' . $extension;
        
        // Chemin de destination
        $uploadDir = __DIR__ . '/../../../public/uploads/';
        $filepath = $uploadDir . $filename;
        
        // Créer le dossier s'il n'existe pas
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // Déplacer le fichier
        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            throw new \Exception('Erreur lors de l\'upload du fichier.');
        }
        
        // Enregistrer dans la base de données
        $sql = "INSERT INTO media (filename, original_name, mime_type, size, uploaded_by) VALUES (?, ?, ?, ?, ?)";
        $params = [
            $filename,
            $file['name'],
            $mimeType,
            $file['size'],
            \Auth::getUser()['id']
        ];
        
        \Database::query($sql, $params);
        $mediaId = (int)\Database::lastInsertId();
        
        if (!$mediaId) {
            // Supprimer le fichier si l'insertion échoue
            unlink($filepath);
            throw new \Exception('Erreur lors de l\'enregistrement en base de données.');
        }
        
        return $mediaId;
    }
}

