<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance - Belgium Video Gaming</title>
    <meta name="description" content="Site temporairement en maintenance pour améliorer votre expérience">
    <meta name="robots" content="noindex, nofollow">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/public/favicon.ico">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --belgium-red: #E30613;
            --belgium-yellow: #FFD700;
            --belgium-black: #000000;
            --belgium-white: #FFFFFF;
            --gray-50: #F9FAFB;
            --gray-100: #F3F4F6;
            --gray-200: #E5E7EB;
            --gray-300: #D1D5DB;
            --gray-400: #9CA3AF;
            --gray-500: #6B7280;
            --gray-600: #4B5563;
            --gray-700: #374151;
            --gray-800: #1F2937;
            --gray-900: #111827;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, var(--belgium-red) 0%, #B8050E 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--belgium-white);
            overflow: hidden;
            position: relative;
        }
        
        /* Animation de fond */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 20% 80%, rgba(255, 215, 0, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(255, 215, 0, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(255, 215, 0, 0.05) 0%, transparent 50%);
            animation: backgroundPulse 8s ease-in-out infinite;
        }
        
        @keyframes backgroundPulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.8; }
        }
        
        .maintenance-container {
            text-align: center;
            max-width: 600px;
            padding: 2rem;
            position: relative;
            z-index: 1;
        }
        
        .logo {
            width: 120px;
            height: 120px;
            margin: 0 auto 2rem;
            background: var(--belgium-white);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            animation: logoFloat 3s ease-in-out infinite;
        }
        
        @keyframes logoFloat {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        .logo img {
            width: 80px;
            height: 80px;
            object-fit: contain;
        }
        
        .maintenance-title {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 1rem;
            background: linear-gradient(45deg, var(--belgium-yellow), #FFF8DC);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        }
        
        .maintenance-subtitle {
            font-size: 1.25rem;
            font-weight: 400;
            margin-bottom: 2rem;
            opacity: 0.9;
            line-height: 1.6;
        }
        
        .maintenance-message {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
        
        .maintenance-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            animation: iconSpin 2s linear infinite;
        }
        
        @keyframes iconSpin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        .maintenance-text {
            font-size: 1.1rem;
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }
        
        .maintenance-details {
            font-size: 0.95rem;
            opacity: 0.8;
            line-height: 1.5;
        }
        
        .progress-container {
            margin: 2rem 0;
        }
        
        .progress-bar {
            width: 100%;
            height: 6px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 3px;
            overflow: hidden;
            margin-bottom: 1rem;
        }
        
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--belgium-yellow), #FFF8DC);
            border-radius: 3px;
            animation: progressAnimation 3s ease-in-out infinite;
        }
        
        @keyframes progressAnimation {
            0% { width: 0%; }
            50% { width: 70%; }
            100% { width: 100%; }
        }
        
        .progress-text {
            font-size: 0.9rem;
            opacity: 0.8;
        }
        
        .social-links {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 2rem;
        }
        
        .social-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 50px;
            height: 50px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            color: var(--belgium-white);
            text-decoration: none;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }
        
        .social-link:hover {
            background: var(--belgium-yellow);
            color: var(--belgium-black);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(255, 215, 0, 0.3);
        }
        
        .admin-access {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }
        
        .admin-btn {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: var(--belgium-white);
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }
        
        .admin-btn:hover {
            background: var(--belgium-yellow);
            color: var(--belgium-black);
            transform: translateY(-1px);
        }
        
        .floating-elements {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            pointer-events: none;
            overflow: hidden;
        }
        
        .floating-element {
            position: absolute;
            background: rgba(255, 215, 0, 0.1);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }
        
        .floating-element:nth-child(1) {
            width: 80px;
            height: 80px;
            top: 20%;
            left: 10%;
            animation-delay: 0s;
        }
        
        .floating-element:nth-child(2) {
            width: 120px;
            height: 120px;
            top: 60%;
            right: 15%;
            animation-delay: 2s;
        }
        
        .floating-element:nth-child(3) {
            width: 60px;
            height: 60px;
            bottom: 20%;
            left: 20%;
            animation-delay: 4s;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .maintenance-container {
                padding: 1rem;
                max-width: 90%;
            }
            
            .maintenance-title {
                font-size: 2.5rem;
            }
            
            .maintenance-subtitle {
                font-size: 1.1rem;
            }
            
            .maintenance-message {
                padding: 1.5rem;
            }
            
            .admin-access {
                top: 10px;
                right: 10px;
            }
            
            .admin-btn {
                padding: 0.5rem 1rem;
                font-size: 0.8rem;
            }
        }
        
        @media (max-width: 480px) {
            .maintenance-title {
                font-size: 2rem;
            }
            
            .logo {
                width: 100px;
                height: 100px;
            }
            
            .logo img {
                width: 60px;
                height: 60px;
            }
        }
    </style>
</head>
<body>
    <!-- Éléments flottants décoratifs -->
    <div class="floating-elements">
        <div class="floating-element"></div>
        <div class="floating-element"></div>
        <div class="floating-element"></div>
    </div>
    
    <!-- Accès admin (si connecté) -->
    <?php if (isset($isAdmin) && $isAdmin): ?>
    <div class="admin-access">
        <a href="/admin" class="admin-btn">Accès Admin</a>
    </div>
    <?php endif; ?>
    
    <div class="maintenance-container">
        <!-- Logo -->
        <div class="logo">
            <img src="/public/assets/images/logo.png" alt="Belgium Video Gaming" onerror="this.style.display='none'">
        </div>
        
        <!-- Titre principal -->
        <h1 class="maintenance-title">Maintenance</h1>
        <p class="maintenance-subtitle">Nous améliorons votre expérience</p>
        
        <!-- Message de maintenance -->
        <div class="maintenance-message">
            <div class="maintenance-icon">🔧</div>
            <p class="maintenance-text">
                Notre site est temporairement en maintenance pour vous offrir une expérience encore meilleure.
            </p>
            <p class="maintenance-details">
                Nous travaillons activement sur des améliorations et de nouvelles fonctionnalités. 
                Le site sera bientôt de retour avec des surprises !
            </p>
        </div>
        
        <!-- Barre de progression animée -->
        <div class="progress-container">
            <div class="progress-bar">
                <div class="progress-fill"></div>
            </div>
            <p class="progress-text">Travaux en cours...</p>
        </div>
        
        <!-- Liens sociaux -->
        <div class="social-links">
            <a href="#" class="social-link" title="Twitter">
                <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                </svg>
            </a>
            <a href="#" class="social-link" title="Facebook">
                <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                </svg>
            </a>
            <a href="#" class="social-link" title="Discord">
                <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M20.317 4.37a19.791 19.791 0 0 0-4.885-1.515.074.074 0 0 0-.079.037c-.21.375-.444.864-.608 1.25a18.27 18.27 0 0 0-5.487 0 12.64 12.64 0 0 0-.617-1.25.077.077 0 0 0-.079-.037A19.736 19.736 0 0 0 3.677 4.37a.07.07 0 0 0-.032.027C.533 9.046-.32 13.58.099 18.057a.082.082 0 0 0 .031.057 19.9 19.9 0 0 0 5.993 3.03.078.078 0 0 0 .084-.028c.462-.63.874-1.295 1.226-1.994a.076.076 0 0 0-.041-.106 13.107 13.107 0 0 1-1.872-.892.077.077 0 0 1-.008-.128 10.2 10.2 0 0 0 .372-.292.074.074 0 0 1 .077-.01c3.928 1.793 8.18 1.793 12.062 0a.074.074 0 0 1 .078.01c.12.098.246.198.373.292a.077.077 0 0 1-.006.127 12.299 12.299 0 0 1-1.873.892.077.077 0 0 0-.041.107c.36.698.772 1.362 1.225 1.993a.076.076 0 0 0 .084.028 19.839 19.839 0 0 0 6.002-3.03.077.077 0 0 0 .032-.054c.5-5.177-.838-9.674-3.549-13.66a.061.061 0 0 0-.031-.03zM8.02 15.33c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.956-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.956 2.418-2.157 2.418zm7.975 0c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.955-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.946 2.418-2.157 2.418z"/>
                </svg>
            </a>
        </div>
    </div>
    
    <script>
        // Animation de la barre de progression
        document.addEventListener('DOMContentLoaded', function() {
            const progressFill = document.querySelector('.progress-fill');
            let progress = 0;
            
            const updateProgress = () => {
                progress += Math.random() * 2;
                if (progress > 100) progress = 100;
                
                progressFill.style.width = progress + '%';
                
                if (progress < 100) {
                    setTimeout(updateProgress, 200 + Math.random() * 300);
                }
            };
            
            setTimeout(updateProgress, 1000);
        });
        
        // Vérification périodique si le site est de nouveau disponible
        setInterval(() => {
            fetch(window.location.origin, { method: 'HEAD' })
                .then(response => {
                    if (response.status === 200) {
                        // Le site est de nouveau disponible, rediriger
                        window.location.reload();
                    }
                })
                .catch(() => {
                    // Site toujours en maintenance, continuer
                });
        }, 30000); // Vérifier toutes les 30 secondes
    </script>
</body>
</html>
