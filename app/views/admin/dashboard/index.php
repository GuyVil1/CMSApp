<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord - Belgium Vidéo Gaming</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            color: white;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            margin-bottom: 40px;
        }
        .header h1 {
            color: #ffd700;
            margin: 0;
            font-size: 2.5em;
        }
        .header p {
            color: #ccc;
            margin: 10px 0;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        .stat-card {
            background: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .stat-number {
            font-size: 2.5em;
            color: #ffd700;
            margin: 0;
        }
        .stat-label {
            color: #ccc;
            margin: 10px 0 0 0;
        }
        .actions {
            text-align: center;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            margin: 0 10px;
            background: #e74c3c;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s;
        }
        .btn:hover {
            background: #c0392b;
        }
        .btn-secondary {
            background: #f39c12;
        }
        .btn-secondary:hover {
            background: #e67e22;
        }
        .user-info {
            background: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .user-info h3 {
            color: #ffd700;
            margin: 0 0 15px 0;
        }
        .user-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        .user-detail {
            background: rgba(0, 0, 0, 0.3);
            padding: 15px;
            border-radius: 5px;
        }
        .user-detail strong {
            color: #ffd700;
        }
    </style>
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
