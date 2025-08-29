<?php
/**
 * Vue : Liste des utilisateurs
 */
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Utilisateurs - Admin</title>
    <link rel="stylesheet" href="/admin.css">
</head>
<body>
    <div class="admin-container">
        <!-- Header -->
        <div class="admin-header">
            <div class="header-content">
                <h1>Gestion des Utilisateurs</h1>
                <div class="header-actions">
                    <a href="/admin.php" class="btn btn-secondary">← Retour Dashboard</a>
                    <a href="/users.php?action=create" class="btn btn-success">+ Nouvel Utilisateur</a>
                </div>
            </div>
        </div>

        <!-- Statistiques -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?= $totalUsers ?></div>
                <div class="stat-label">Total Utilisateurs</div>
            </div>

            <div class="stat-card">
                <div class="stat-number"><?= $adminUsers ?></div>
                <div class="stat-label">Administrateurs</div>
            </div>
        </div>

        <!-- Filtres -->
        <div class="filters">
            <form method="GET" action="/admin/users" class="filter-form">
                <div class="form-group">
                    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" 
                           placeholder="Rechercher par login ou email..." class="form-input">
                </div>
                <div class="form-group">
                    <select name="role" class="form-select">
                        <option value="">Tous les rôles</option>
                        <option value="admin" <?= $role === 'admin' ? 'selected' : '' ?>>Admin</option>
                        <option value="editor" <?= $role === 'editor' ? 'selected' : '' ?>>Editor</option>
                        <option value="author" <?= $role === 'author' ? 'selected' : '' ?>>Author</option>
                        <option value="member" <?= $role === 'member' ? 'selected' : '' ?>>Member</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Filtrer</button>
                <a href="/admin/users" class="btn btn-secondary">Réinitialiser</a>
            </form>
        </div>

        <!-- Liste des utilisateurs -->
        <div class="table-container">
            <table class="users-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Login</th>
                        <th>Email</th>
                        <th>Rôle</th>
                        <th>Créé le</th>
                        <th>Dernière connexion</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="7" class="text-center">Aucun utilisateur trouvé</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $user): ?>
                            <tr class="user-row">
                                <td><?= $user['id'] ?></td>
                                <td><?= htmlspecialchars($user['login']) ?></td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td>
                                    <span class="role-badge role-<?= $user['role_name'] ?>">
                                        <?= ucfirst($user['role_name']) ?>
                                    </span>
                                </td>
                                <td><?= date('d/m/Y H:i', strtotime($user['created_at'])) ?></td>
                                <td>
                                    <?= $user['last_login'] ? date('d/m/Y H:i', strtotime($user['last_login'])) : 'Jamais' ?>
                                </td>
                                <td class="actions">
                                    <a href="/users.php?action=edit&id=<?= $user['id'] ?>" class="btn btn-sm btn-info">Modifier</a>
                                    <button class="btn btn-sm btn-danger delete-user" data-id="<?= $user['id'] ?>">
                                        Supprimer
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
                    <a href="?page=<?= $currentPage - 1 ?>&search=<?= urlencode($search) ?>&role=<?= urlencode($role) ?>" 
                       class="btn btn-secondary">← Précédent</a>
                <?php endif; ?>
                
                                        <span class="page-info">Page <?= $currentPage ?> sur <?= $totalPages ?></span>
                
                <?php if ($currentPage < $totalPages): ?>
                    <a href="?page=<?= $currentPage + 1 ?>&search=<?= urlencode($search) ?>&role=<?= urlencode($role) ?>" 
                       class="btn btn-secondary">Suivant →</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Gestion des actions AJAX
        document.addEventListener('DOMContentLoaded', function() {
            // Suppression
            document.querySelectorAll('.delete-user').forEach(button => {
                button.addEventListener('click', function() {
                    const userId = this.dataset.id;
                    
                    if (confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ? Cette action est irréversible.')) {
                        fetch('/users.php?action=delete&id=' + userId, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: 'csrf_token=<?= Auth::generateCsrfToken() ?>'
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
