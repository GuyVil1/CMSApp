<?php
/**
 * Vue : Liste des tags
 */
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Tags - Admin</title>
    <link rel="stylesheet" href="/admin.css">
</head>
<body>
    <div class="admin-container">
        <!-- Header -->
        <div class="admin-header">
            <div class="header-content">
                <h1>Gestion des Tags</h1>
                <div class="header-actions">
                    <a href="/admin.php" class="btn btn-secondary">← Retour Dashboard</a>
                    <a href="/tags.php?action=create" class="btn btn-success">+ Nouveau Tag</a>
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
                        echo 'Tag non trouvé';
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
                        echo 'Tag créé avec succès';
                        break;
                    case 'updated':
                        echo 'Tag mis à jour avec succès';
                        break;
                    case 'deleted':
                        echo 'Tag supprimé avec succès';
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
                <div class="stat-number"><?= $totalTags ?></div>
                <div class="stat-label">Total Tags</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= array_sum(array_column($tags, 'article_count')) ?></div>
                <div class="stat-label">Total Articles Taggués</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= count(array_filter($tags, fn($tag) => $tag['article_count'] > 0)) ?></div>
                <div class="stat-label">Tags Utilisés</div>
            </div>
        </div>

        <!-- Filtres -->
        <div class="filters">
            <form method="GET" action="/tags.php" class="filter-form">
                <div class="form-group">
                    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" 
                           placeholder="Rechercher un tag..." class="form-input">
                </div>

                <button type="submit" class="btn btn-primary">Filtrer</button>
                <a href="/tags.php" class="btn btn-secondary">Réinitialiser</a>
            </form>
        </div>

        <!-- Liste des tags -->
        <div class="table-container">
            <table class="tags-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Slug</th>
                        <th>Articles</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($tags)): ?>
                        <tr>
                            <td colspan="5" class="text-center">Aucun tag trouvé</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($tags as $tag): ?>
                            <tr class="tag-row">
                                <td><strong>#<?= $tag['id'] ?></strong></td>
                                <td>
                                    <div style="font-weight: 600; color: var(--admin-primary);">
                                        <?= htmlspecialchars($tag['name']) ?>
                                    </div>
                                </td>
                                <td><code><?= htmlspecialchars($tag['slug']) ?></code></td>
                                <td>
                                    <?php if ($tag['article_count'] > 0): ?>
                                        <span class="badge badge-success">
                                            <?= $tag['article_count'] ?> article(s)
                                        </span>
                                    <?php else: ?>
                                        <span class="badge badge-muted">
                                            0 article
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="actions">
                                    <a href="/tags.php?action=edit&id=<?= $tag['id'] ?>" class="btn btn-sm btn-info">
                                        ✏️ Modifier
                                    </a>
                                    <button class="btn btn-sm btn-danger delete-tag" data-id="<?= $tag['id'] ?>" 
                                            data-name="<?= htmlspecialchars($tag['name']) ?>">
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
            document.querySelectorAll('.delete-tag').forEach(button => {
                button.addEventListener('click', function() {
                    const tagId = this.dataset.id;
                    const tagName = this.dataset.name;
                    
                    if (confirm(`Êtes-vous sûr de vouloir supprimer le tag "${tagName}" ? Cette action est irréversible.`)) {
                        // Générer un nouveau token CSRF pour cette requête
                        const csrfToken = '<?= htmlspecialchars(Auth::generateCsrfToken()) ?>';
                        
                        fetch('/tags.php?action=delete&id=' + tagId, {
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
