<?php
declare(strict_types=1);

/**
 * Contrôleur de gestion des articles - Belgium Vidéo Gaming
 */

require_once __DIR__ . '/../../../core/Controller.php';
require_once __DIR__ . '/../../../core/Auth.php';
require_once __DIR__ . '/../../../core/Database.php';
require_once __DIR__ . '/../../models/Article.php';
require_once __DIR__ . '/../../models/Media.php'; // Added for cover image validation

class ArticlesController extends Controller
{
    public function __construct()
    {
        // Vérifier que l'utilisateur est connecté et a les droits admin/editor
        Auth::requireRole(['admin', 'editor']);
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
        
        $result = Article::findAll($page, 20, $filters);
        
        // Charger les catégories pour le filtre
        $categories = Database::query("SELECT id, name FROM categories ORDER BY name");
        
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
            'categories' => $categories
        ]);
    }
    
    /**
     * Formulaire de création d'article
     */
    public function create(): void
    {
        // Charger les données nécessaires
        $categories = Database::query("SELECT id, name FROM categories ORDER BY name");
        $games = Database::query("SELECT id, title FROM games ORDER BY title");
        $tags = Database::query("SELECT id, name FROM tags ORDER BY name");
        
        $this->render('admin/articles/form', [
            'article' => null,
            'categories' => $categories,
            'games' => $games,
            'tags' => $tags,
            'featured_positions' => $this->getFeaturedPositions()
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
            // Validation des données
            $title = trim($_POST['title'] ?? '');
            $excerpt = trim($_POST['excerpt'] ?? '');
            $content = trim($_POST['content'] ?? '');
            $status = trim($_POST['status'] ?? 'draft');
            $category_id = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null;
            $game_id = !empty($_POST['game_id']) ? (int)$_POST['game_id'] : null;
            $featured_position = !empty($_POST['featured_position']) ? (int)$_POST['featured_position'] : null;
            $cover_image_id = !empty($_POST['cover_image_id']) ? (int)$_POST['cover_image_id'] : null;
            $published_at = !empty($_POST['published_at']) ? $_POST['published_at'] : null;
            $tags = $_POST['tags'] ?? [];
            
            if (empty($title) || empty($content)) {
                throw new Exception('Le titre et le contenu sont obligatoires');
            }
            
            // Validation du statut
            if (!in_array($status, ['draft', 'published', 'archived'])) {
                throw new Exception('Statut invalide');
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
                    $game = Database::queryOne("SELECT cover_image_id FROM games WHERE id = ?", [$game_id]);
                    if ($game && $game['cover_image_id']) {
                        $cover_image_id = $game['cover_image_id'];
                    } else {
                        throw new Exception('L\'image de couverture est obligatoire. Veuillez sélectionner une image ou un jeu avec une cover.');
                    }
                } else {
                    throw new Exception('L\'image de couverture est obligatoire');
                }
            }
            
            // Utiliser l'image uploadée si disponible
            if ($uploadedImageId) {
                $cover_image_id = $uploadedImageId;
            }
            
            // Vérifier que l'image existe
            $coverImage = Media::find($cover_image_id);
            if (!$coverImage) {
                throw new Exception('L\'image de couverture sélectionnée n\'existe pas');
            }
            
            // Vérifier la position en avant
            if ($featured_position) {
                if ($featured_position < 1 || $featured_position > 6) {
                    throw new Exception('La position en avant doit être comprise entre 1 et 6');
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
            $slug = Article::generateSlug($title);
            
            // Créer l'article
            $articleData = [
                'title' => $title,
                'slug' => $slug,
                'excerpt' => $excerpt ?: null,
                'content' => $content,
                'status' => $status,
                'cover_image_id' => $cover_image_id,
                'author_id' => Auth::getUser()['id'],
                'category_id' => $category_id,
                'game_id' => $game_id,
                'featured_position' => $featured_position,
                'published_at' => $published_at
            ];
            
            $articleId = Article::create($articleData);
            
            if (!$articleId) {
                throw new Exception('Erreur lors de la création de l\'article');
            }
            
            // Gérer le remplacement automatique si une position en avant est sélectionnée
            if ($featured_position) {
                Article::replaceArticleInPosition($featured_position, $articleId);
            }
            
            // Ajouter les tags
            if (!empty($tags)) {
                $article = Article::findById($articleId);
                if ($article) {
                    $article->addTags($tags);
                }
            }
            
            // Log de l'activité
            $statusText = $status === 'published' ? 'publié' : ($status === 'archived' ? 'archivé' : 'créé en brouillon');
            Auth::logActivity(Auth::getUserId(), "Article $statusText : $title");
            
            $this->setFlash('success', "Article $statusText avec succès !");
            $this->redirect('/admin/articles');
            
        } catch (Exception $e) {
            $this->setFlash('error', 'Erreur : ' . $e->getMessage());
            $this->redirect('/admin/articles/create');
        }
    }
    
    /**
     * Formulaire d'édition d'article
     */
    public function edit(int $id): void
    {
        $article = Article::findById($id);
        if (!$article) {
            $this->setFlash('error', 'Article non trouvé');
            $this->redirect('/admin/articles');
            return;
        }
        
        // Charger les données nécessaires
        $categories = Database::query("SELECT id, name FROM categories ORDER BY name");
        $games = Database::query("SELECT id, title FROM games ORDER BY title");
        $tags = Database::query("SELECT id, name FROM tags ORDER BY name");
        
        // Récupérer les tags de l'article
        $articleTags = Database::query(
            "SELECT tag_id FROM article_tag WHERE article_id = ?",
            [$id]
        );
        $articleTagIds = array_column($articleTags, 'tag_id');
        
        $this->render('admin/articles/form', [
            'article' => $article,
            'categories' => $categories,
            'games' => $games,
            'tags' => $tags,
            'articleTagIds' => $articleTagIds,
            'featured_positions' => $this->getFeaturedPositions($id)
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
        
        $article = Article::findById($id);
        if (!$article) {
            $this->setFlash('error', 'Article non trouvé');
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
                throw new Exception('Le titre et le contenu sont obligatoires');
            }
            
            // Validation de l'image de couverture
            if (empty($cover_image_id)) {
                throw new Exception('L\'image de couverture est obligatoire');
            }
            
            // Vérifier que l'image existe
            $coverImage = Media::find($cover_image_id);
            if (!$coverImage) {
                throw new Exception('L\'image de couverture sélectionnée n\'existe pas');
            }
            
            // Vérifier la position en avant
            if ($featured_position) {
                if ($featured_position < 1 || $featured_position > 6) {
                    throw new Exception('La position en avant doit être comprise entre 1 et 6');
                }
                // Pas de vérification de disponibilité - remplacement automatique autorisé
            }
            
            // Gérer le remplacement automatique si la position change
            $oldPosition = $article->getFeaturedPosition();
            if ($featured_position && $oldPosition !== $featured_position) {
                // Libérer l'ancienne position de cet article
                if ($oldPosition) {
                    Article::freePosition($oldPosition);
                }
                // Remplacer l'article dans la nouvelle position
                Article::replaceArticleInPosition($featured_position, $id);
            } elseif (!$featured_position && $oldPosition) {
                // Libérer la position si on retire l'article de la mise en avant
                Article::freePosition($oldPosition);
            }
            
            // Générer le slug
            $slug = Article::generateSlug($title, $id);
            
            // Mettre à jour l'article
            $articleData = [
                'title' => $title,
                'slug' => $slug,
                'excerpt' => $excerpt ?: null,
                'content' => $content,
                'cover_image_id' => $cover_image_id,
                'category_id' => $category_id,
                'game_id' => $game_id,
                'featured_position' => $featured_position
            ];
            
            if (!$article->update($articleData)) {
                throw new Exception('Erreur lors de la mise à jour de l\'article');
            }
            
            // Mettre à jour les tags
            $article->addTags($tags);
            
            // Log de l'activité
            Auth::logActivity('article_updated', "Article mis à jour : $title");
            
            $this->setFlash('success', 'Article mis à jour avec succès !');
            $this->redirect('/admin/articles');
            
        } catch (Exception $e) {
            $this->setFlash('error', 'Erreur : ' . $e->getMessage());
            $this->redirect("/admin/articles/edit/$id");
        }
    }
    
    /**
     * Suppression d'un article
     */
    public function delete(int $id): void
    {
        $article = Article::findById($id);
        if (!$article) {
            $this->setFlash('error', 'Article non trouvé');
            $this->redirect('/admin/articles');
            return;
        }
        
        try {
            // Libérer la position en avant si elle existe
            $featuredPosition = $article->getFeaturedPosition();
            if ($featuredPosition) {
                Article::freePosition($featuredPosition);
            }
            
            // Supprimer l'article
            if (!$article->delete()) {
                throw new Exception('Erreur lors de la suppression de l\'article');
            }
            
            // Log de l'activité
            Auth::logActivity('article_deleted', "Article supprimé : {$article->getTitle()}");
            
            $this->setFlash('success', 'Article supprimé avec succès !');
            $this->redirect('/admin/articles');
            
        } catch (Exception $e) {
            $this->setFlash('error', 'Erreur : ' . $e->getMessage());
            $this->redirect('/admin/articles');
        }
    }
    
    /**
     * Publier un article
     */
    public function publish(int $id): void
    {
        $article = Article::findById($id);
        if (!$article) {
            $this->setFlash('error', 'Article non trouvé');
            $this->redirect('/admin/articles');
            return;
        }
        
        try {
            if (!$article->publish()) {
                throw new Exception('Erreur lors de la publication');
            }
            
            $this->setFlash('success', 'Article publié avec succès !');
            $this->redirect('/admin/articles');
            
        } catch (Exception $e) {
            $this->setFlash('error', 'Erreur : ' . $e->getMessage());
            $this->redirect('/admin/articles');
        }
    }
    
    /**
     * Mettre en brouillon
     */
    public function draft(int $id): void
    {
        $article = Article::findById($id);
        if (!$article) {
            $this->setFlash('error', 'Article non trouvé');
            $this->redirect('/admin/articles');
            return;
        }
        
        try {
            if (!$article->draft()) {
                throw new Exception('Erreur lors de la mise en brouillon');
            }
            
            $this->setFlash('success', 'Article mis en brouillon !');
            $this->redirect('/admin/articles');
            
        } catch (Exception $e) {
            $this->setFlash('error', 'Erreur : ' . $e->getMessage());
            $this->redirect('/admin/articles');
        }
    }
    
    /**
     * Archiver un article
     */
    public function archive(int $id): void
    {
        $article = Article::findById($id);
        if (!$article) {
            $this->setFlash('error', 'Article non trouvé');
            $this->redirect('/admin/articles');
            return;
        }
        
        try {
            if ($article->archive()) {
                Auth::logActivity('article_archived', "Article archivé : " . $article->getTitle());
                $this->setFlash('success', 'Article archivé !');
            } else {
                throw new Exception('Erreur lors de l\'archivage');
            }
        } catch (Exception $e) {
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
            $currentArticle = Article::getArticleInPosition($i);
            
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
     * Gérer l'upload d'une image
     */
    private function handleImageUpload(array $file): int
    {
        // Validation du fichier
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
        $maxSize = 4 * 1024 * 1024; // 4MB
        
        if (!in_array($file['type'], $allowedTypes)) {
            throw new Exception('Type de fichier non autorisé. Utilisez JPG, PNG ou WebP.');
        }
        
        if ($file['size'] > $maxSize) {
            throw new Exception('Fichier trop volumineux. Taille maximum : 4MB.');
        }
        
        // Vérifier le MIME type réel
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mimeType, $allowedTypes)) {
            throw new Exception('Type de fichier invalide détecté.');
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
            throw new Exception('Erreur lors de l\'upload du fichier.');
        }
        
        // Enregistrer dans la base de données
        $sql = "INSERT INTO media (filename, original_name, mime_type, size, uploaded_by) VALUES (?, ?, ?, ?, ?)";
        $params = [
            $filename,
            $file['name'],
            $mimeType,
            $file['size'],
            Auth::getUser()['id']
        ];
        
        Database::query($sql, $params);
        $mediaId = (int)Database::lastInsertId();
        
        if (!$mediaId) {
            // Supprimer le fichier si l'insertion échoue
            unlink($filepath);
            throw new Exception('Erreur lors de l\'enregistrement en base de données.');
        }
        
        return $mediaId;
    }
}
