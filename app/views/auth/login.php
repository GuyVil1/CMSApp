<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - <?= htmlspecialchars($site_name) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --belgium-red: #FF0000;
            --belgium-yellow: #FFD700;
            --belgium-black: #000000;
            --primary: #1a1a1a;
            --secondary: #2d2d2d;
            --accent: #FFD700;
            --text: #ffffff;
            --text-muted: #a0a0a0;
            --border: #404040;
            --error: #ff4444;
            --success: #44ff44;
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
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text);
        }
        
        .login-container {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 3rem;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }
        
        .logo {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .logo-icon {
            width: 60px;
            height: 60px;
            background: var(--belgium-yellow);
            border: 3px solid var(--belgium-black);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 2rem;
        }
        
        .logo h1 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .logo .tagline {
            color: var(--belgium-yellow);
            font-size: 0.875rem;
            font-weight: 600;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--text);
        }
        
        .form-group input {
            width: 100%;
            padding: 0.75rem 1rem;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid var(--border);
            border-radius: 10px;
            color: var(--text);
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: var(--belgium-yellow);
            background: rgba(255, 255, 255, 0.15);
            box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.1);
        }
        
        .form-group input::placeholder {
            color: var(--text-muted);
        }
        
        .btn {
            width: 100%;
            padding: 0.875rem 1.5rem;
            background: var(--belgium-red);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 1rem;
        }
        
        .btn:hover {
            background: #cc0000;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(255, 0, 0, 0.3);
        }
        
        .btn:active {
            transform: translateY(0);
        }
        
        .error {
            background: rgba(255, 68, 68, 0.1);
            border: 1px solid var(--error);
            color: var(--error);
            padding: 0.75rem 1rem;
            border-radius: 10px;
            margin-bottom: 1rem;
            font-size: 0.875rem;
        }
        
        .links {
            text-align: center;
            margin-top: 1.5rem;
        }
        
        .links a {
            color: var(--belgium-yellow);
            text-decoration: none;
            font-size: 0.875rem;
            transition: color 0.3s ease;
        }
        
        .links a:hover {
            color: #ffed4e;
            text-decoration: underline;
        }
        
        .divider {
            margin: 1rem 0;
            text-align: center;
            position: relative;
        }
        
        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: var(--border);
        }
        
        .divider span {
            background: var(--primary);
            padding: 0 1rem;
            color: var(--text-muted);
            font-size: 0.875rem;
        }
        
        .belgium-flag {
            position: absolute;
            top: 2rem;
            right: 2rem;
            display: flex;
            gap: 2px;
        }
        
        .flag-stripe {
            width: 20px;
            height: 40px;
        }
        
        .flag-black { background: var(--belgium-black); }
        .flag-yellow { background: var(--belgium-yellow); }
        .flag-red { background: var(--belgium-red); }
        
        @media (max-width: 480px) {
            .login-container {
                margin: 1rem;
                padding: 2rem;
            }
            
            .belgium-flag {
                top: 1rem;
                right: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="belgium-flag">
        <div class="flag-stripe flag-black"></div>
        <div class="flag-stripe flag-yellow"></div>
        <div class="flag-stripe flag-red"></div>
    </div>
    
    <div class="login-container">
        <div class="logo">
            <div class="logo-icon">🎮</div>
            <h1><?= htmlspecialchars($site_name) ?></h1>
            <div class="tagline">🇧🇪 BELGIQUE</div>
        </div>
        
        <?php if (!empty($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <form method="POST" action="/login">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
            
            <div class="form-group">
                <label for="login">Login ou Email</label>
                <input type="text" id="login" name="login" required 
                       placeholder="Votre login ou email"
                       value="<?= htmlspecialchars($_POST['login'] ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" required 
                       placeholder="Votre mot de passe">
            </div>
            
            <button type="submit" class="btn">Se connecter</button>
        </form>
        
        <div class="divider">
            <span>ou</span>
        </div>
        
        <div class="links">
            <a href="/register">Créer un compte</a>
            <br><br>
            <a href="/">← Retour à l'accueil</a>
        </div>
    </div>
</body>
</html>
