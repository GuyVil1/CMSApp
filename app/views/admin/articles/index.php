<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des articles - Belgium Vidéo Gaming</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            color: white;
        }
        .container {
            max-width: 1400px;
            margin: 0 auto;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #ffd700;
            margin: 0;
            font-size: 2.5em;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #e74c3c;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }
        .btn:hover {
            background: #c0392b;
        }
        .btn-success {
            background: #27ae60;
        }
        .btn-success:hover {
            background: #229954;
        }
        .btn-warning {
            background: #f39c12;
        }
        .btn-warning:hover {
            background: #e67e22;
        }
        .btn-info {
            background: #3498db;
        }
        .btn-info:hover {
            background: #2980b9;
        }
        .btn-sm {
            padding: 6px 12px;
            font-size: 12px;
        }
        
        /* Filtres */
        .filters {
            background: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .filters form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            align-items: end;
        }
        .form-group {
            display: flex;
            flex-direction: column;
        }
        .form-group label {
            margin-bottom: 5px;
            color: #ffd700;
            font-weight: bold;
        }
        .form-group input,
        .form-group select {
            padding: 8px 12px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 5px;
            background: rgba(0, 0, 0, 0.3);
            color: white;
            font-size: 14px;
        }
        .form-group input::placeholder {
            color: #999;
        }
        
        /* Tableau */
        .table-container {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .articles-table {
            width: 100%;
            border-collapse: collapse;
        }
        .articles-table th,
        .articles-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .articles-table th {
            background: rgba(0, 0, 0, 0.3);
            color: #ffd700;
            font-weight: bold;
        }
        .articles-table tr:hover {
            background: rgba(255, 255, 255, 0.05);
        }
        
        /* Statuts */
        .status {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-draft {
            background: #f39c12;
            color: #000;
        }
        .status-published {
            background: #27ae60;
            color: white;
        }
        .status-archived {
            background: #95a5a6;
            color: white;
        }
        
        /* Position en avant */
        .featured-position {
            display: inline-block;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: #e74c3c;
            color: white;
            text-align: center;
            line-height: 24px;
            font-size: 12px;
            font-weight: bold;
        }
        .featured-position.none {
            background: #95a5a6;
        }
        
        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 30px;
        }
        .pagination a,
        .pagination span {
            padding: 8px 12px;
            border-radius: 5px;
            text-decoration: none;
            color: white;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .pagination a:hover {
            background: rgba(255, 255, 255, 0.2);
        }
        .pagination .current {
            background: #ffd700;
            color: #000;
            border-color: #ffd700;
        }
        
        /* Actions */
        .actions {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
        }
        
        /* Messages flash */
        .flash {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid;
        }
        .flash-success {
            background: rgba(39, 174, 96, 0.2);
            border-color: #27ae60;
            color: #27ae60;
        }
        .flash-error {
            background: rgba(231, 76, 60, 0.2);
            border-color: #e74c3c;
            color: #e74c3c;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                gap: 20px;
                text-align: center;
            }
            .filters form {
                grid-template-columns: 1fr;
            }
            .articles-table {
                font-size: 12px;
            }
            .articles-table th,
            .articles-table td {
                padding: 8px 6px;
            }
            .actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📝 Gestion des articles</h1>
            <div style="display: flex; gap: 10px; align-items: center;">
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
            <table class="articles-table">
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
                                        <a href="/admin/articles/edit/<?= $article['id'] ?>" class="btn btn-info btn-sm">✏️</a>
                                        
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
                                        
                                        <form method="POST" action="/admin/articles/delete/<?= $article['id'] ?>" style="display: inline;" 
                                              onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet article ?')">
                                            <button type="submit" class="btn btn-sm" title="Supprimer">🗑️</button>
                                        </form>
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
