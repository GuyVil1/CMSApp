<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des articles - GameNews Belgium</title>
    <link rel="stylesheet" href="/admin.css">

</head>
<body>
    <div class="container">
        <div class="header-nav">
            <h1>📝 Gestion des articles</h1>
            <div class="actions">
                <a href="/" class="btn btn-info">🏠 Accueil</a>
                <a href="/admin/dashboard" class="btn btn-warning">📊 Tableau de bord</a>
                <a href="/admin/articles/create" class="btn btn-success">➕ Nouvel article</a>
            </div>
        </div>

        <!-- Messages flash -->
        <?php if (isset($_SESSION['flash'])): ?>
            <?php foreach ($_SESSION['flash'] as $type => $message): ?>
                <div class="flash flash-<?= $type ?>">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endforeach; ?>
            <?php unset($_SESSION['flash']); ?>
        <?php endif; ?>

        <!-- Filtres -->
        <div class="filters">
            <form method="GET" action="/admin/articles">
                <div class="form-group">
                    <label for="search">Recherche</label>
                    <input type="text" id="search" name="search" value="<?= htmlspecialchars($filters['search']) ?>" 
                           placeholder="Titre, extrait, contenu...">
                </div>
                
                <div class="form-group">
                    <label for="status">Statut</label>
                    <select id="status" name="status">
                        <option value="">Tous les statuts</option>
                        <option value="draft" <?= $filters['status'] === 'draft' ? 'selected' : '' ?>>Brouillon</option>
                        <option value="published" <?= $filters['status'] === 'published' ? 'selected' : '' ?>>Publié</option>
                        <option value="archived" <?= $filters['status'] === 'archived' ? 'selected' : '' ?>>Archivé</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="category">Catégorie</label>
                    <select id="category" name="category">
                        <option value="">Toutes les catégories</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= $category['id'] ?>" <?= $filters['category'] == $category['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($category['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-info">🔍 Filtrer</button>
                    <a href="/admin/articles" class="btn btn-warning">🔄 Réinitialiser</a>
                </div>
            </form>
        </div>

        <!-- Tableau des articles -->
        <div class="table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Statut</th>
                        <th>Position en avant</th>
                        <th>Catégorie</th>
                        <th>Auteur</th>
                        <th>Date création</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($articles)): ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 40px;">
                                Aucun article trouvé
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($articles as $article): ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($article['title']) ?></strong>
                                    <?php if ($article['excerpt']): ?>
                                        <br><small style="color: #ccc;"><?= htmlspecialchars(substr($article['excerpt'], 0, 100)) ?>...</small>
                                    <?php endif; ?>
                                </td>
                                
                                <td>
                                    <span class="status status-<?= $article['status'] ?>">
                                        <?= ucfirst($article['status']) ?>
                                    </span>
                                </td>
                                
                                <td>
                                    <?php if ($article['featured_position']): ?>
                                        <span class="featured-position" title="Position en avant">
                                            <?= $article['featured_position'] ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="featured-position none" title="Pas en avant">-</span>
                                    <?php endif; ?>
                                </td>
                                
                                <td>
                                    <?= $article['category_name'] ? htmlspecialchars($article['category_name']) : '-' ?>
                                </td>
                                
                                <td>
                                    <?= htmlspecialchars($article['author_login']) ?>
                                </td>
                                
                                <td>
                                    <?= date('d/m/Y H:i', strtotime($article['created_at'])) ?>
                                </td>
                                
                                <td>
                                    <div class="actions">
                                        <?php 
                                        // Vérifier que la variable $user existe
                                        if (!isset($user) || !is_array($user)) {
                                            $user = ['role' => 'guest', 'id' => 0];
                                        }
                                        
                                        $canEdit = ($user['role'] === 'admin') || 
                                                  ($user['role'] === 'editor' && $article['author_id'] == $user['id']);
                                        $canPublish = ($user['role'] === 'admin');
                                        $canDelete = ($user['role'] === 'admin') || 
                                                   ($user['role'] === 'editor' && $article['author_id'] == $user['id']);
                                        ?>
                                        
                                        <?php if ($canEdit): ?>
                                            <a href="/admin/articles/edit/<?= $article['id'] ?>" class="btn btn-info btn-sm">✏️</a>
                                        <?php endif; ?>
                                        
                                        <?php if ($canPublish): ?>
                                            <?php if ($article['status'] === 'draft'): ?>
                                                <form method="POST" action="/admin/articles/publish/<?= $article['id'] ?>" style="display: inline;">
                                                    <button type="submit" class="btn btn-success btn-sm" title="Publier">📤</button>
                                                </form>
                                            <?php elseif ($article['status'] === 'published'): ?>
                                                <form method="POST" action="/admin/articles/draft/<?= $article['id'] ?>" style="display: inline;">
                                                    <button type="submit" class="btn btn-warning btn-sm" title="Mettre en brouillon">📝</button>
                                                </form>
                                            <?php endif; ?>
                                            
                                            <?php if ($article['status'] !== 'archived'): ?>
                                                <form method="POST" action="/admin/articles/archive/<?= $article['id'] ?>" style="display: inline;">
                                                    <button type="submit" class="btn btn-warning btn-sm" title="Archiver">📁</button>
                                                </form>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                        
                                        <?php if ($canDelete): ?>
                                            <form method="POST" action="/admin/articles/delete/<?= $article['id'] ?>" style="display: inline;" 
                                                  onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet article ?')">
                                                <button type="submit" class="btn btn-sm" title="Supprimer">🗑️</button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($pagination['total'] > 1): ?>
            <div class="pagination">
                <?php if ($pagination['current'] > 1): ?>
                    <a href="?page=<?= $pagination['current'] - 1 ?>&search=<?= urlencode($filters['search']) ?>&status=<?= urlencode($filters['status']) ?>&category=<?= $filters['category'] ?>">
                        ← Précédent
                    </a>
                <?php endif; ?>
                
                <?php for ($i = 1; $i <= $pagination['total']; $i++): ?>
                    <?php if ($i == $pagination['current']): ?>
                        <span class="current"><?= $i ?></span>
                    <?php else: ?>
                        <a href="?page=<?= $i ?>&search=<?= urlencode($filters['search']) ?>&status=<?= urlencode($filters['status']) ?>&category=<?= $filters['category'] ?>">
                            <?= $i ?>
                        </a>
                    <?php endif; ?>
                <?php endfor; ?>
                
                <?php if ($pagination['current'] < $pagination['total']): ?>
                    <a href="?page=<?= $pagination['current'] + 1 ?>&search=<?= urlencode($filters['search']) ?>&status=<?= urlencode($filters['status']) ?>&category=<?= $filters['category'] ?>">
                        Suivant →
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- Statistiques -->
        <div style="margin-top: 30px; text-align: center; color: #ccc;">
            <?= $pagination['total_items'] ?> article(s) au total
        </div>
        
        <div style="margin-top: 20px; text-align: center;">
            <a href="/admin" class="btn btn-warning">🏠 Retour au tableau de bord</a>
        </div>
    </div>
</body>
</html>
