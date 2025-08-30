<?php
/**
 * Vue d'administration - Liste des jeux
 */
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Jeux - Administration</title>
    <link rel="stylesheet" href="/admin.css">
</head>
<body>
    <div class="admin-container">
        <!-- Header -->
        <header class="admin-header">
            <div class="header-content">
                <h1>🎮 Gestion des Jeux</h1>
                <div class="header-actions">
                    <a href="/admin.php" class="btn btn-secondary">← Retour au tableau de bord</a>
                    <a href="/games?action=create" class="btn btn-primary">+ Nouveau jeu</a>
                </div>
            </div>
        </header>

        <!-- Messages -->
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">
                <?php if ($_GET['success'] === 'created'): ?>
                    ✅ Jeu créé avec succès !
                <?php elseif ($_GET['success'] === 'updated'): ?>
                    ✅ Jeu mis à jour avec succès !
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-error">
                <?php if ($_GET['error'] === 'not_found'): ?>
                    ❌ Jeu non trouvé
                <?php else: ?>
                    ❌ Erreur : <?= htmlspecialchars($_GET['error']) ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- Statistiques -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">🎮</div>
                <div class="stat-content">
                    <div class="stat-number"><?= $totalGames ?></div>
                    <div class="stat-label">Total Jeux</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">📱</div>
                <div class="stat-content">
                    <div class="stat-number"><?= count($platforms) ?></div>
                    <div class="stat-label">Plateformes</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">🎯</div>
                <div class="stat-content">
                    <div class="stat-number"><?= count($genres) ?></div>
                    <div class="stat-label">Genres</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">📰</div>
                <div class="stat-content">
                    <div class="stat-number"><?= array_sum(array_column($games, 'article_count')) ?></div>
                    <div class="stat-label">Articles liés</div>
                </div>
            </div>
        </div>

        <!-- Filtres et recherche -->
        <div class="filters-section">
            <form method="GET" action="/games.php" class="filters-form">
                <div class="filters-row">
                    <div class="filter-group">
                        <label for="search">Rechercher :</label>
                        <input type="text" id="search" name="search" value="<?= htmlspecialchars($search) ?>" 
                               placeholder="Titre du jeu..." class="form-input">
                    </div>
                    
                    <div class="filter-group">
                        <label for="platform">Plateforme :</label>
                        <select id="platform" name="platform" class="form-select">
                            <option value="">Toutes les plateformes</option>
                            <?php foreach ($platforms as $plat): ?>
                                <option value="<?= htmlspecialchars($plat) ?>" 
                                        <?= $platform === $plat ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($plat) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label for="genre">Genre :</label>
                        <select id="genre" name="genre" class="form-select">
                            <option value="">Tous les genres</option>
                            <?php foreach ($genres as $gen): ?>
                                <option value="<?= $gen['id'] ?>" 
                                        <?= $genre == $gen['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($gen['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="filter-actions">
                        <button type="submit" class="btn btn-primary">🔍 Filtrer</button>
                        <a href="/games.php" class="btn btn-secondary">🔄 Réinitialiser</a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Tableau des jeux -->
        <div class="table-container">
            <table class="games-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cover</th>
                        <th>Titre</th>
                        <th>Hardware</th>
                        <th>Genre</th>
                        <th>Date de sortie</th>
                        <th>Articles</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($games)): ?>
                        <tr>
                            <td colspan="8" class="text-center">
                                <div class="empty-state">
                                    <div class="empty-icon">🎮</div>
                                    <h3>Aucun jeu trouvé</h3>
                                    <p>Commencez par ajouter votre premier jeu !</p>
                                    <a href="/games.php?action=create" class="btn btn-primary">+ Ajouter un jeu</a>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($games as $game): ?>
                            <tr>
                                <td>
                                    <code><?= $game->getId() ?></code>
                                </td>
                                <td>
                                    <?php if ($game->getCoverImageUrl()): ?>
                                        <img src="<?= htmlspecialchars($game->getCoverImageUrl()) ?>" 
                                             alt="Cover" class="game-cover" 
                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                        <div class="no-cover" style="display: none;">📷</div>
                                    <?php else: ?>
                                        <div class="no-cover">📷</div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="game-info">
                                        <div class="game-title"><?= htmlspecialchars($game->getTitle()) ?></div>
                                        <div class="game-slug"><?= htmlspecialchars($game->getSlug()) ?></div>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($game->getHardwareName()): ?>
                                        <span class="badge badge-platform"><?= htmlspecialchars($game->getHardwareName()) ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php 
                                    $genre = $game->getGenre();
                                    if ($genre): ?>
                                        <span class="badge badge-genre" style="background-color: <?= htmlspecialchars($genre->getColor()) ?>">
                                            <?= htmlspecialchars($genre->getName()) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($game->getReleaseDate()): ?>
                                        <div class="release-info">
                                            <div class="release-date"><?= date('d/m/Y', strtotime($game->getReleaseDate())) ?></div>
                                            <?php 
                                            $releaseTime = strtotime($game->getReleaseDate());
                                            $now = time();
                                            if ($releaseTime <= $now): ?>
                                                <span class="badge badge-released">Sorti</span>
                                            <?php else: ?>
                                                <span class="badge badge-upcoming">À venir</span>
                                            <?php endif; ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="article-count">
                                        <?= $game->getArticlesCount() ?> article(s)
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="/games?action=edit&id=<?= $game->getId() ?>" 
                                           class="btn btn-sm btn-secondary" title="Modifier">
                                            ✏️
                                        </a>
                                        <button class="btn btn-sm btn-danger delete-game" 
                                                data-id="<?= $game->getId() ?>" 
                                                data-title="<?= htmlspecialchars($game->getTitle()) ?>"
                                                data-articles="<?= $game->getArticlesCount() ?>"
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
                    <a href="?page=<?= $currentPage - 1 ?>&search=<?= urlencode($search) ?>&platform=<?= urlencode($platform) ?>&genre=<?= urlencode($genre) ?>" 
                       class="page-link">← Précédent</a>
                <?php endif; ?>
                
                <?php for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++): ?>
                    <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&platform=<?= urlencode($platform) ?>&genre=<?= urlencode($genre) ?>" 
                       class="page-link <?= $i === $currentPage ? 'active' : '' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
                
                <?php if ($currentPage < $totalPages): ?>
                    <a href="?page=<?= $currentPage + 1 ?>&search=<?= urlencode($search) ?>&platform=<?= urlencode($platform) ?>&genre=<?= urlencode($genre) ?>" 
                       class="page-link">Suivant →</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Gestion des actions AJAX
        document.addEventListener('DOMContentLoaded', function() {
            // Suppression
            document.querySelectorAll('.delete-game').forEach(button => {
                button.addEventListener('click', function() {
                    const gameId = this.dataset.id;
                    const gameTitle = this.dataset.title;
                    const articlesCount = parseInt(this.dataset.articles);
                    
                    let message = `Êtes-vous sûr de vouloir supprimer le jeu "${gameTitle}" ?`;
                    if (articlesCount > 0) {
                        message += `\n\n⚠️ ATTENTION : Ce jeu est lié à ${articlesCount} article(s). La suppression n'est pas recommandée.`;
                    }
                    message += '\n\nCette action est irréversible.';
                    
                    if (confirm(message)) {
                        // Générer un nouveau token CSRF pour cette requête
                        const csrfToken = '<?= htmlspecialchars(Auth::generateCsrfToken()) ?>';
                        
                        fetch('/games.php?action=delete&id=' + gameId, {
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
