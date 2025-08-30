<?php
/**
 * Vue d'administration - Liste des genres
 */
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Genres - Administration</title>
    <link rel="stylesheet" href="/admin.css">
</head>
<body>
    <div class="admin-container">
        <!-- Header -->
        <header class="admin-header">
            <div class="header-content">
                <h1>🎯 Gestion des Genres</h1>
                <div class="header-actions">
                    <a href="/admin.php" class="btn btn-secondary">← Retour au tableau de bord</a>
                    <a href="/genres.php?action=create" class="btn btn-primary">+ Nouveau genre</a>
                </div>
            </div>
        </header>

        <!-- Messages -->
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">
                <?php if ($_GET['success'] === 'created'): ?>
                    ✅ Genre créé avec succès !
                <?php elseif ($_GET['success'] === 'updated'): ?>
                    ✅ Genre mis à jour avec succès !
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-error">
                <?php if ($_GET['error'] === 'not_found'): ?>
                    ❌ Genre non trouvé
                <?php else: ?>
                    ❌ Erreur : <?= htmlspecialchars($_GET['error']) ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- Statistiques -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">🎯</div>
                <div class="stat-content">
                    <div class="stat-number"><?= $totalGenres ?></div>
                    <div class="stat-label">Total Genres</div>
                </div>
            </div>
        </div>

        <!-- Filtres et recherche -->
        <div class="filters-section">
            <form method="GET" action="/genres.php" class="filters-form">
                <div class="filters-row">
                    <div class="filter-group">
                        <label for="search">Rechercher :</label>
                        <input type="text" id="search" name="search" value="<?= htmlspecialchars($search) ?>" 
                               placeholder="Nom du genre..." class="form-input">
                    </div>
                    
                    <div class="filter-actions">
                        <button type="submit" class="btn btn-primary">🔍 Rechercher</button>
                        <a href="/genres.php" class="btn btn-secondary">🔄 Réinitialiser</a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Tableau des genres -->
        <div class="table-container">
            <table class="genres-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Couleur</th>
                        <th>Nom</th>
                        <th>Description</th>
                        <th>Jeux associés</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($genres)): ?>
                        <tr>
                            <td colspan="6" class="text-center">
                                <div class="empty-state">
                                    <div class="empty-icon">🎯</div>
                                    <h3>Aucun genre trouvé</h3>
                                    <p>Commencez par ajouter votre premier genre !</p>
                                    <a href="/genres.php?action=create" class="btn btn-primary">+ Ajouter un genre</a>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($genres as $genre): ?>
                            <tr>
                                <td>
                                    <code><?= $genre['id'] ?></code>
                                </td>
                                <td>
                                    <div class="color-preview" style="background-color: <?= htmlspecialchars($genre['color']) ?>"></div>
                                    <code><?= htmlspecialchars($genre['color']) ?></code>
                                </td>
                                <td>
                                    <div class="genre-info">
                                        <div class="genre-name"><?= htmlspecialchars($genre['name']) ?></div>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($genre['description']): ?>
                                        <div class="genre-description"><?= htmlspecialchars($genre['description']) ?></div>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge badge-count"><?= $genre['game_count'] ?? 0 ?></span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="/genres.php?action=edit&id=<?= $genre['id'] ?>" 
                                           class="btn btn-sm btn-secondary" title="Modifier">
                                            ✏️
                                        </a>
                                        <button type="button" 
                                                class="btn btn-sm btn-danger delete-genre" 
                                                data-id="<?= $genre['id'] ?>"
                                                data-name="<?= htmlspecialchars($genre['name']) ?>"
                                                title="Supprimer">
                                            🗑️
                                        </button>
                                    </div>
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
                    <a href="?page=<?= $currentPage - 1 ?>&search=<?= urlencode($search) ?>" class="btn btn-secondary">← Précédent</a>
                <?php endif; ?>
                
                <span class="page-info">Page <?= $currentPage ?> sur <?= $totalPages ?></span>
                
                <?php if ($currentPage < $totalPages): ?>
                    <a href="?page=<?= $currentPage + 1 ?>&search=<?= urlencode($search) ?>" class="btn btn-secondary">Suivant →</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Modal de confirmation de suppression -->
    <div id="deleteModal" class="modal" style="display: none;">
        <div class="modal-content">
            <h3>🗑️ Confirmer la suppression</h3>
            <p>Êtes-vous sûr de vouloir supprimer le genre "<span id="genreName"></span>" ?</p>
            <p class="warning">⚠️ Cette action est irréversible !</p>
            
            <form id="deleteForm" method="POST">
                <input type="hidden" name="csrf_token" value="<?= \Auth::generateCsrfToken() ?>">
                <div class="modal-actions">
                    <button type="submit" class="btn btn-danger">🗑️ Supprimer</button>
                    <button type="button" class="btn btn-secondary" onclick="closeDeleteModal()">❌ Annuler</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Gestion de la suppression des genres
        document.querySelectorAll('.delete-genre').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.dataset.id;
                const name = this.dataset.name;
                
                document.getElementById('genreName').textContent = name;
                document.getElementById('deleteForm').action = `/genres.php?action=delete&id=${id}`;
                
                document.getElementById('deleteModal').style.display = 'block';
            });
        });

        function closeDeleteModal() {
            document.getElementById('deleteModal').style.display = 'none';
        }

        // Fermer le modal en cliquant à l'extérieur
        window.addEventListener('click', function(event) {
            const modal = document.getElementById('deleteModal');
            if (event.target === modal) {
                closeDeleteModal();
            }
        });
    </script>
</body>
</html>
