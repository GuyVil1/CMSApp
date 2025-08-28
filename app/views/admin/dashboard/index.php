<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord - GameNews Belgium</title>
    <link rel="stylesheet" href="/admin.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎮 Tableau de bord</h1>
            <p>Bienvenue dans l'administration de Belgium Vidéo Gaming</p>
        </div>

        <div class="user-info">
            <h3>👤 Informations utilisateur</h3>
            <div class="user-details">
                <div class="user-detail">
                    <strong>Login :</strong> <?= htmlspecialchars($user['login']) ?>
                </div>
                <div class="user-detail">
                    <strong>Email :</strong> <?= htmlspecialchars($user['email']) ?>
                </div>
                <div class="user-detail">
                    <strong>Rôle :</strong> <?= htmlspecialchars($user['role_name']) ?>
                </div>
                <div class="user-detail">
                    <strong>Dernière connexion :</strong> 
                    <?= $user['last_login'] ? date('d/m/Y H:i', strtotime($user['last_login'])) : 'Jamais' ?>
                </div>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?= $stats['articles'] ?></div>
                <div class="stat-label">Articles</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $stats['users'] ?></div>
                <div class="stat-label">Utilisateurs</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $stats['games'] ?></div>
                <div class="stat-label">Jeux</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $stats['categories'] ?></div>
                <div class="stat-label">Catégories</div>
            </div>
        </div>

        <div class="actions">
            <a href="/admin/articles" class="btn">📝 Gérer les articles</a>
            <a href="/admin/articles/create" class="btn btn-success">➕ Nouvel article</a>
            <a href="/admin/media" class="btn btn-secondary">🖼️ Gérer les médias</a>
            <a href="/admin/themes" class="btn btn-secondary">🎨 Gérer les thèmes</a>
            <a href="/admin/users" class="btn btn-secondary">👥 Gérer les utilisateurs</a>
            <a href="/admin/games" class="btn btn-secondary">🎮 Gérer les jeux</a>
            <a href="/" class="btn">🏠 Retour au site</a>
            <a href="/logout" class="btn">🚪 Déconnexion</a>
        </div>
    </div>
</body>
</html>
