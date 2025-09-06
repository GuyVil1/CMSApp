<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - <?= htmlspecialchars($site_name) ?></title>
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
        
        .register-container {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 3rem;
            width: 100%;
            max-width: 500px;
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
        
        .form-group small {
            display: block;
            margin-top: 0.5rem;
            color: var(--text-muted);
            font-size: 0.8rem;
        }
        
        .checkbox-group {
            margin-bottom: 1.5rem;
        }
        
        .checkbox-label {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            cursor: pointer;
            font-size: 0.9rem;
            line-height: 1.4;
            color: var(--text);
        }
        
        .checkbox-label input[type="checkbox"] {
            margin: 0;
            width: 18px;
            height: 18px;
            accent-color: var(--belgium-yellow);
        }
        
        .checkbox-label a {
            color: var(--belgium-yellow);
            text-decoration: none;
            font-weight: 600;
        }
        
        .checkbox-label a:hover {
            color: #ffed4e;
            text-decoration: underline;
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
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
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
        
        .success {
            background: rgba(68, 255, 68, 0.1);
            border: 1px solid var(--success);
            color: var(--success);
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
            .register-container {
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
    
    <div class="register-container">
        <div class="logo">
            <div class="logo-icon">🎮</div>
            <h1><?= htmlspecialchars($site_name) ?></h1>
            <div class="tagline">🇧🇪 BELGIQUE</div>
        </div>
        
        <?php if (!empty($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="POST" action="/auth/register">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
            
            <div class="form-group">
                <label for="login">Nom d'utilisateur</label>
                <input type="text" id="login" name="login" required 
                       placeholder="Votre nom d'utilisateur"
                       value="<?= htmlspecialchars($_POST['login'] ?? '') ?>">
                <small>3-20 caractères, lettres et chiffres uniquement</small>
            </div>

            <div class="form-group">
                <label for="email">Adresse email</label>
                <input type="email" id="email" name="email" required 
                       placeholder="votre@email.be"
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                <small>Nous ne partagerons jamais votre email</small>
            </div>

            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" required 
                       placeholder="Votre mot de passe">
                <small>Minimum 8 caractères</small>
            </div>

            <div class="form-group">
                <label for="password_confirm">Confirmer le mot de passe</label>
                <input type="password" id="password_confirm" name="password_confirm" required 
                       placeholder="Confirmez votre mot de passe">
            </div>

            <div class="form-group checkbox-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="terms" required>
                    J'accepte les <a href="/terms" target="_blank">conditions d'utilisation</a> et la <a href="/privacy" target="_blank">politique de confidentialité</a>
                </label>
            </div>

            <button type="submit" class="btn">
                <span>🚀</span>
                Créer mon compte
            </button>
        </form>

        <div class="links">
            <a href="/auth/login">Déjà un compte ? Connectez-vous ici</a>
            <br><br>
            <a href="/">← Retour à l'accueil</a>
        </div>
    </div>
</body>
</html>
