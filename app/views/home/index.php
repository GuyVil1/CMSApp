<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($site_name) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --belgium-red: #FF0000;
            --belgium-yellow: #FFD700;
            --belgium-black: #000000;
            --primary: #1a1a1a;
            --secondary: #2d2d2d;
            --text: #ffffff;
            --text-muted: #a0a0a0;
            --border: #404040;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            min-height: 100vh;
            color: var(--text);
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        .header {
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .logo {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        
        .title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: var(--belgium-yellow);
        }
        
        .tagline {
            font-size: 1.2rem;
            color: var(--text-muted);
            margin-bottom: 2rem;
        }
        
        .status-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border);
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            backdrop-filter: blur(10px);
        }
        
        .status-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--belgium-yellow);
        }
        
        .status-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .info-item {
            background: rgba(255, 255, 255, 0.1);
            padding: 1rem;
            border-radius: 10px;
            border: 1px solid var(--border);
        }
        
        .info-label {
            font-size: 0.875rem;
            color: var(--text-muted);
            margin-bottom: 0.5rem;
        }
        
        .info-value {
            font-weight: 600;
            color: var(--text);
        }
        
        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background: var(--belgium-red);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            margin: 0.5rem;
        }
        
        .btn:hover {
            background: #cc0000;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 0, 0, 0.3);
        }
        
        .btn-secondary {
            background: transparent;
            border: 2px solid var(--belgium-yellow);
            color: var(--belgium-yellow);
        }
        
        .btn-secondary:hover {
            background: var(--belgium-yellow);
            color: var(--belgium-black);
        }
        
        .belgium-flag {
            display: flex;
            justify-content: center;
            gap: 2px;
            margin-bottom: 2rem;
        }
        
        .flag-stripe {
            width: 30px;
            height: 60px;
        }
        
        .flag-black { background: var(--belgium-black); }
        .flag-yellow { background: var(--belgium-yellow); }
        .flag-red { background: var(--belgium-red); }
        
        .phase-info {
            background: rgba(255, 215, 0, 0.1);
            border: 1px solid var(--belgium-yellow);
            border-radius: 10px;
            padding: 1.5rem;
            margin-top: 2rem;
        }
        
        .phase-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--belgium-yellow);
            margin-bottom: 1rem;
        }
        
        .phase-list {
            list-style: none;
        }
        
        .phase-list li {
            margin-bottom: 0.5rem;
            padding-left: 1.5rem;
            position: relative;
        }
        
        .phase-list li::before {
            content: '✅';
            position: absolute;
            left: 0;
        }
        
        .phase-list li.pending::before {
            content: '⏳';
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="belgium-flag">
                <div class="flag-stripe flag-black"></div>
                <div class="flag-stripe flag-yellow"></div>
                <div class="flag-stripe flag-red"></div>
            </div>
            
            <div class="logo">🎮</div>
            <h1 class="title"><?= htmlspecialchars($site_name) ?></h1>
            <p class="tagline"><?= htmlspecialchars($site_tagline) ?></p>
        </div>
        
        <div class="status-card">
            <h2 class="status-title">État de l'authentification</h2>
            
            <div class="status-info">
                <div class="info-item">
                    <div class="info-label">Statut de connexion</div>
                    <div class="info-value">
                        <?php if ($isLoggedIn): ?>
                            <span style="color: #44ff44;">✅ Connecté</span>
                        <?php else: ?>
                            <span style="color: #ff4444;">❌ Non connecté</span>
                        <?php endif; ?>
                    </div>
                </div>
                
                <?php if ($user): ?>
                <div class="info-item">
                    <div class="info-label">Utilisateur</div>
                    <div class="info-value"><?= htmlspecialchars($user['login']) ?></div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">Email</div>
                    <div class="info-value"><?= htmlspecialchars($user['email']) ?></div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">Rôle</div>
                    <div class="info-value"><?= htmlspecialchars($user['role_name']) ?></div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">Dernière connexion</div>
                    <div class="info-value">
                        <?= $user['last_login'] ? date('d/m/Y H:i', strtotime($user['last_login'])) : 'Jamais' ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            
            <div style="text-align: center;">
                <?php if ($isLoggedIn): ?>
                    <a href="/admin" class="btn">Accéder au back-office</a>
                    <a href="/logout" class="btn btn-secondary">Se déconnecter</a>
                <?php else: ?>
                    <a href="/login" class="btn">Se connecter</a>
                    <a href="/register" class="btn btn-secondary">Créer un compte</a>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="phase-info">
            <h3 class="phase-title">Phase 2 : Authentification et système de rôles</h3>
            <ul class="phase-list">
                <li>Classe Auth avec sessions sécurisées</li>
                <li>Système de rôles (admin, editor, author, member)</li>
                <li>Protection CSRF sur tous les formulaires</li>
                <li>Modèle User avec CRUD complet</li>
                <li>Contrôleur AuthController (login, logout, register)</li>
                <li>Pages d'erreur 404, 403, 500</li>
                <li>Vue de connexion avec design belge</li>
                <li class="pending">Tests de l'authentification</li>
            </ul>
        </div>
    </div>
</body>
</html>
