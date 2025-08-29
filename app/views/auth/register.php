<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - GameNews Belgium</title>
    <meta name="description" content="Créez votre compte sur GameNews Belgium pour accéder à l'actualité gaming exclusive">
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="stylesheet" href="/style.css">
</head>
<body>
    <!-- Header avec thème belge -->
    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <div class="logo-icon">🎮</div>
                    <div class="logo-text">
                        <h1>GameNews</h1>
                        <div class="logo-subtitle">🇧🇪 BELGIQUE</div>
                    </div>
                </div>
                
                <h1 class="header-title">
                    Rejoignez la communauté gaming belge
                </h1>
                
                <div style="display: flex; gap: 0.5rem; align-items: center;">
                    <a href="/" class="home-btn">🏠 Accueil</a>
                    <a href="/auth/login" class="login-btn">Se connecter</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Bannières thématiques de fond -->
    <div class="theme-banner-left"></div>
    <div class="theme-banner-right"></div>

    <!-- Layout principal -->
    <div class="main-layout">
        <main class="main-content">
            <div class="auth-container">
                <div class="auth-card">
                    <div class="auth-header">
                        <div class="auth-icon">👤</div>
                        <h2>Créer un compte</h2>
                        <p>Rejoignez la communauté GameNews Belgium</p>
                    </div>

                    <?php if (!empty($error)): ?>
                        <div class="auth-message error">
                            <span class="message-icon">⚠️</span>
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($success)): ?>
                        <div class="auth-message success">
                            <span class="message-icon">✅</span>
                            <?= htmlspecialchars($success) ?>
                        </div>
                    <?php endif; ?>

                    <form class="auth-form" method="POST" action="/auth/register">
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
                                <span class="checkmark"></span>
                                J'accepte les <a href="/terms" target="_blank">conditions d'utilisation</a> et la <a href="/privacy" target="_blank">politique de confidentialité</a>
                            </label>
                        </div>

                        <button type="submit" class="auth-submit">
                            <span class="submit-icon">🚀</span>
                            Créer mon compte
                        </button>
                    </form>

                    <div class="auth-footer">
                        <p>Déjà un compte ? <a href="/auth/login">Connectez-vous ici</a></p>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <style>
        .auth-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 60vh;
            padding: 2rem 0;
        }

        .auth-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 2.5rem;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            max-width: 500px;
            width: 100%;
        }

        .auth-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .auth-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .auth-header h2 {
            color: var(--belgium-black);
            font-size: 2rem;
            margin-bottom: 0.5rem;
            font-weight: 700;
        }

        .auth-header p {
            color: var(--belgium-red);
            font-size: 1.1rem;
            margin: 0;
        }

        .auth-message {
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .auth-message.error {
            background: rgba(231, 76, 60, 0.1);
            border: 1px solid rgba(231, 76, 60, 0.3);
            color: #c0392b;
        }

        .auth-message.success {
            background: rgba(39, 174, 96, 0.1);
            border: 1px solid rgba(39, 174, 96, 0.3);
            color: #27ae60;
        }

        .message-icon {
            font-size: 1.2rem;
        }

        .auth-form {
            margin-bottom: 1.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--belgium-black);
            font-weight: 600;
            font-size: 0.95rem;
        }

        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group input[type="password"] {
            width: 100%;
            padding: 0.875rem 1rem;
            border: 2px solid #e1e8ed;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: white;
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--belgium-yellow);
            box-shadow: 0 0 0 3px rgba(230, 184, 0, 0.1);
        }

        .form-group small {
            display: block;
            margin-top: 0.5rem;
            color: #666;
            font-size: 0.85rem;
        }

        .checkbox-group {
            margin-bottom: 2rem;
        }

        .checkbox-label {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            cursor: pointer;
            font-size: 0.9rem;
            line-height: 1.4;
        }

        .checkbox-label input[type="checkbox"] {
            margin: 0;
            width: 18px;
            height: 18px;
            accent-color: var(--belgium-yellow);
        }

        .checkbox-label a {
            color: var(--belgium-red);
            text-decoration: none;
            font-weight: 600;
        }

        .checkbox-label a:hover {
            text-decoration: underline;
        }

        .auth-submit {
            width: 100%;
            padding: 1rem 2rem;
            background: linear-gradient(135deg, var(--belgium-yellow) 0%, #ffd700 100%);
            color: var(--belgium-black);
            border: none;
            border-radius: 25px;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .auth-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(230, 184, 0, 0.4);
        }

        .submit-icon {
            font-size: 1.2rem;
        }

        .auth-footer {
            text-align: center;
            padding-top: 1.5rem;
            border-top: 1px solid #e1e8ed;
        }

        .auth-footer p {
            margin: 0;
            color: #666;
        }

        .auth-footer a {
            color: var(--belgium-red);
            text-decoration: none;
            font-weight: 600;
        }

        .auth-footer a:hover {
            text-decoration: underline;
        }

        .home-btn {
            background: var(--belgium-red);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .home-btn:hover {
            background: #c0392b;
            transform: translateY(-1px);
        }

        .register-btn {
            background: var(--belgium-yellow);
            color: var(--belgium-black);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .register-btn:hover {
            background: #d4a700;
            transform: translateY(-1px);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .auth-card {
                margin: 1rem;
                padding: 2rem;
            }

            .auth-header h2 {
                font-size: 1.75rem;
            }

            .form-group input[type="text"],
            .form-group input[type="email"],
            .form-group input[type="password"] {
                padding: 0.75rem;
            }
        }
    </style>
</body>
</html>
