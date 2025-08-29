<?php
/**
 * Vue : Liste des catégories
 */
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Catégories - Admin</title>
    <link rel="stylesheet" href="/admin.css">
</head>
<body>
    <div class="admin-container">
        <!-- Header -->
        <div class="admin-header">
            <div class="header-content">
                <h1>Gestion des Catégories</h1>
                <div class="header-actions">
                    <a href="/admin.php" class="btn btn-secondary">← Retour Dashboard</a>
                    <a href="/categories.php?action=create" class="btn btn-success">+ Nouvelle Catégorie</a>
                </div>
            </div>
        </div>

        <!-- Messages d'erreur/succès -->
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-error">
                <?php
                $error = $_GET['error'];
                switch ($error) {
                    case 'not_found':
                        echo 'Catégorie non trouvée';
                        break;
                    default:
                        echo htmlspecialchars($error);
                }
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">
                <?php
                $success = $_GET['success'];
                switch ($success) {
                    case 'created':
                        echo 'Catégorie créée avec succès';
                        break;
                    case 'updated':
                        echo 'Catégorie mise à jour avec succès';
                        break;
                    case 'deleted':
                        echo 'Catégorie supprimée avec succès';
                        break;
                    default:
                        echo htmlspecialchars($success);
                }
                ?>
            </div>
        <?php endif; ?>

        <!-- Statistiques -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?= $totalCategories ?></div>
                <div class="stat-label">Total Catégories</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= array_sum(array_column($categories, 'article_count')) ?></div>
                <div class="stat-label">Total Articles Catégorisés</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= count(array_filter($categories, fn($category) => $category['article_count'] > 0)) ?></div>
                <div class="stat-label">Catégories Utilisées</div>
            </div>
        </div>

        <!-- Filtres -->
        <div class="filters">
            <form method="GET" action="/categories.php" class="filter-form">
                <div class="form-group">
                    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" 
                           placeholder="Rechercher une catégorie..." class="form-input">
                </div>

                <button type="submit" class="btn btn-primary">Filtrer</button>
                <a href="/categories.php" class="btn btn-secondary">Réinitialiser</a>
            </form>
        </div>

        <!-- Liste des catégories -->
        <div class="table-container">
            <table class="categories-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Slug</th>
                        <th>Description</th>
                        <th>Articles</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($categories)): ?>
                        <tr>
                            <td colspan="6" class="text-center">Aucune catégorie trouvée</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($categories as $category): ?>
                            <tr class="category-row">
                                <td><strong>#<?= $category['id'] ?></strong></td>
                                <td>
                                    <div style="font-weight: 600; color: var(--admin-primary);">
                                        <?= htmlspecialchars($category['name']) ?>
                                    </div>
                                </td>
                                <td><code><?= htmlspecialchars($category['slug']) ?></code></td>
                                <td>
                                    <?php if ($category['description']): ?>
                                        <span class="description-text">
                                            <?= htmlspecialchars(substr($category['description'], 0, 50)) ?>
                                            <?= strlen($category['description']) > 50 ? '...' : '' ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted">Aucune description</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($category['article_count'] > 0): ?>
                                        <span class="badge badge-success">
                                            <?= $category['article_count'] ?> article(s)
                                        </span>
                                    <?php else: ?>
                                        <span class="badge badge-muted">
                                            0 article
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="actions">
                                    <a href="/categories.php?action=edit&id=<?= $category['id'] ?>" class="btn btn-sm btn-info">
                                        ✏️ Modifier
                                    </a>
                                    <button class="btn btn-sm btn-danger delete-category" data-id="<?= $category['id'] ?>" 
                                            data-name="<?= htmlspecialchars($category['name']) ?>"
                                            data-articles="<?= $category['article_count'] ?>">
                                        🗑️ Supprimer
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php if ($currentPage > 1): ?>
                    <a href="?page=<?= $currentPage - 1 ?>&search=<?= urlencode($search) ?>" 
                       class="btn btn-secondary">← Précédent</a>
                <?php endif; ?>
                
                <span class="page-info">Page <?= $currentPage ?> sur <?= $totalPages ?></span>
                
                <?php if ($currentPage < $totalPages): ?>
                    <a href="?page=<?= $currentPage + 1 ?>&search=<?= urlencode($search) ?>" 
                       class="btn btn-secondary">Suivant →</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Gestion des actions AJAX
        document.addEventListener('DOMContentLoaded', function() {
            // Suppression
            document.querySelectorAll('.delete-category').forEach(button => {
                button.addEventListener('click', function() {
                    const categoryId = this.dataset.id;
                    const categoryName = this.dataset.name;
                    const articlesCount = parseInt(this.dataset.articles);
                    
                    let message = `Êtes-vous sûr de vouloir supprimer la catégorie "${categoryName}" ?`;
                    if (articlesCount > 0) {
                        message += `\n\n⚠️ ATTENTION : Cette catégorie contient ${articlesCount} article(s). La suppression n'est pas autorisée tant qu'il y a des articles associés.`;
                    }
                    message += '\n\nCette action est irréversible.';
                    
                    if (confirm(message)) {
                        if (articlesCount > 0) {
                            alert('Impossible de supprimer cette catégorie car elle contient des articles. Veuillez d\'abord déplacer ou supprimer les articles associés.');
                            return;
                        }
                        
                        // Générer un nouveau token CSRF pour cette requête
                        const csrfToken = '<?= htmlspecialchars(Auth::generateCsrfToken()) ?>';
                        
                        fetch('/categories.php?action=delete&id=' + categoryId, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: 'csrf_token=' + csrfToken
                        })
                        .then(response => {
                            console.log('Response status:', response.status);
                            console.log('Response headers:', response.headers);
                            
                            if (!response.ok) {
                                throw new Error('Erreur HTTP: ' + response.status);
                            }
                            
                            return response.text().then(text => {
                                console.log('Response text:', text);
                                try {
                                    return JSON.parse(text);
                                } catch (e) {
                                    console.error('Erreur parsing JSON:', e);
                                    throw new Error('Réponse invalide du serveur');
                                }
                            });
                        })
                        .then(data => {
                            console.log('Parsed data:', data);
                            if (data.success) {
                                location.reload();
                            } else {
                                alert('Erreur: ' + (data.error || 'Erreur inconnue'));
                            }
                        })
                        .catch(error => {
                            console.error('Erreur:', error);
                            alert('Erreur lors de la suppression: ' + error.message);
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>
