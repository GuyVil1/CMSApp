<?php
/**
 * Vue d'administration - Liste des hardware
 */
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Hardware - Administration</title>
    <link rel="stylesheet" href="/admin.css">
</head>
<body>
    <div class="admin-container">
        <!-- Header -->
        <header class="admin-header">
            <div class="header-content">
                <h1>🖥️ Gestion des Hardware</h1>
                <div class="header-actions">
                    <a href="/hardware.php?action=create" class="btn btn-primary">➕ Nouveau Hardware</a>
                    <a href="/admin.php" class="btn btn-secondary">← Retour au tableau de bord</a>
                </div>
            </div>
        </header>

        <!-- Messages -->
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">
                ✅ <?= $_GET['success'] === 'created' ? 'Hardware créé avec succès' : 
                       ($_GET['success'] === 'updated' ? 'Hardware mis à jour avec succès' : 'Opération réussie') ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-error">
                ❌ <?= $_GET['error'] === 'not_found' ? 'Hardware non trouvé' : 'Erreur lors de l\'opération' ?>
            </div>
        <?php endif; ?>

        <!-- Statistiques -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?= $totalHardware ?></div>
                <div class="stat-label">Total Hardware</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $hardware ? count(array_filter($hardware, fn($h) => $h->isActive())) : 0 ?></div>
                <div class="stat-label">Hardware Actifs</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $hardware ? count(array_filter($hardware, fn($h) => $h->getGamesCount() > 0)) : 0 ?></div>
                <div class="stat-label">Avec Jeux</div>
            </div>
        </div>

        <!-- Filtres -->
        <div class="filters">
            <form method="GET" action="/hardware.php" class="filter-form">
                <div class="form-group">
                    <label for="search">Rechercher</label>
                    <input type="text" id="search" name="search" 
                           value="<?= htmlspecialchars($search) ?>" 
                           placeholder="Nom ou fabricant...">
                </div>
                
                <div class="form-group">
                    <label for="type">Type</label>
                    <select id="type" name="type" class="form-select">
                        <option value="">Tous les types</option>
                        <?php foreach ($types as $typeKey => $typeName): ?>
                            <option value="<?= $typeKey ?>" <?= $type === $typeKey ? 'selected' : '' ?>>
                                <?= htmlspecialchars($typeName) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">🔍 Filtrer</button>
                    <a href="/hardware.php" class="btn btn-secondary">🔄 Réinitialiser</a>
                </div>
            </form>
        </div>

        <!-- Tableau des hardware -->
        <div class="table-container">
            <table class="hardware-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Type</th>
                        <th>Fabricant</th>
                        <th>Année</th>
                        <th>Statut</th>
                        <th>Jeux</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($hardware)): ?>
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 2rem; color: var(--admin-text-muted);">
                                Aucun hardware trouvé
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($hardware as $hw): ?>
                            <tr>
                                <td>
                                    <code><?= $hw->getId() ?></code>
                                </td>
                                <td>
                                    <div class="hardware-info">
                                        <div class="hardware-name"><?= htmlspecialchars($hw->getName()) ?></div>
                                        <div class="hardware-slug"><?= htmlspecialchars($hw->getSlug()) ?></div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-type-<?= $hw->getType() ?>">
                                        <?= htmlspecialchars($hw->getTypeName()) ?>
                                    </span>
                                </td>
                                <td>
                                    <?= $hw->getManufacturer() ? htmlspecialchars($hw->getManufacturer()) : '<span class="text-muted">-</span>' ?>
                                </td>
                                <td>
                                    <?= $hw->getReleaseYear() ? $hw->getReleaseYear() : '<span class="text-muted">-</span>' ?>
                                </td>
                                <td>
                                    <?php if ($hw->isActive()): ?>
                                        <span class="badge badge-success">Actif</span>
                                    <?php else: ?>
                                        <span class="badge badge-muted">Inactif</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="article-count"><?= $hw->getGamesCount() ?> jeu(x)</span>
                                </td>
                                <td>
                                    <div class="actions">
                                        <a href="/hardware.php?action=edit&id=<?= $hw->getId() ?>" 
                                           class="btn btn-sm btn-info">✏️ Modifier</a>
                                        <button class="btn btn-sm btn-danger delete-hardware" 
                                                data-id="<?= $hw->getId() ?>"
                                                data-name="<?= htmlspecialchars($hw->getName()) ?>"
                                                data-games="<?= $hw->getGamesCount() ?>">
                                            🗑️ Supprimer
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
                <div class="page-info">
                    Page <?= $currentPage ?> sur <?= $totalPages ?>
                </div>
                
                <?php if ($currentPage > 1): ?>
                    <a href="/hardware.php?page=<?= $currentPage - 1 ?>&search=<?= urlencode($search) ?>&type=<?= urlencode($type) ?>" 
                       class="btn btn-sm">← Précédent</a>
                <?php endif; ?>
                
                <?php for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++): ?>
                    <a href="/hardware.php?page=<?= $i ?>&search=<?= urlencode($search) ?>&type=<?= urlencode($type) ?>" 
                       class="btn btn-sm <?= $i === $currentPage ? 'btn-primary' : '' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
                
                <?php if ($currentPage < $totalPages): ?>
                    <a href="/hardware.php?page=<?= $currentPage + 1 ?>&search=<?= urlencode($search) ?>&type=<?= urlencode($type) ?>" 
                       class="btn btn-sm">Suivant →</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Gestion des actions AJAX
        document.addEventListener('DOMContentLoaded', function() {
            // Suppression
            document.querySelectorAll('.delete-hardware').forEach(button => {
                button.addEventListener('click', function() {
                    const hardwareId = this.dataset.id;
                    const hardwareName = this.dataset.name;
                    const gamesCount = parseInt(this.dataset.games);
                    
                    let message = `Êtes-vous sûr de vouloir supprimer le hardware "${hardwareName}" ?`;
                    if (gamesCount > 0) {
                        message += `\n\n⚠️ ATTENTION : Ce hardware est lié à ${gamesCount} jeu(x). La suppression n'est pas recommandée.`;
                    }
                    message += '\n\nCette action est irréversible.';
                    
                    if (confirm(message)) {
                        // Générer un nouveau token CSRF pour cette requête
                        const csrfToken = '<?= htmlspecialchars(\Auth::generateCsrfToken()) ?>';
                        
                        fetch('/hardware.php?action=delete&id=' + hardwareId, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: 'csrf_token=' + csrfToken
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Erreur HTTP: ' + response.status);
                            }
                            return response.json();
                        })
                        .then(data => {
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
