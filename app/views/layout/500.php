<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erreur serveur - <?= htmlspecialchars($site_name ?? 'Belgium Vidéo Gaming') ?></title>
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
        
        .error-container {
            text-align: center;
            max-width: 500px;
            padding: 2rem;
        }
        
        .error-code {
            font-size: 8rem;
            font-weight: 700;
            color: var(--belgium-red);
            line-height: 1;
            margin-bottom: 1rem;
            text-shadow: 0 0 20px rgba(255, 0, 0, 0.5);
        }
        
        .error-title {
            font-size: 2rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--belgium-yellow);
        }
        
        .error-message {
            font-size: 1.1rem;
            color: var(--text-muted);
            margin-bottom: 2rem;
            line-height: 1.6;
        }
        
        .btn {
            display: inline-block;
            padding: 0.875rem 2rem;
            background: var(--belgium-red);
            color: white;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            margin: 0 0.5rem;
        }
        
        .btn:hover {
            background: #cc0000;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(255, 0, 0, 0.3);
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
        
        .error-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">⚠️</div>
        <div class="error-code">500</div>
        <h1 class="error-title">Erreur serveur</h1>
        <p class="error-message">
            <?= htmlspecialchars($message ?? 'Une erreur interne s\'est produite. Veuillez réessayer plus tard.') ?>
        </p>
        <div>
            <a href="/" class="btn">Retour à l'accueil</a>
            <a href="javascript:location.reload()" class="btn btn-secondary">Actualiser</a>
        </div>
    </div>
</body>
</html>
