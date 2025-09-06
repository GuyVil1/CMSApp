<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <?php if (isset($seoMetaTags)): ?>
        <?= $seoMetaTags ?>
    <?php else: ?>
        <title><?= $pageTitle ?? 'Belgium Video Gaming - L\'actualité jeux vidéo en Belgique' ?></title>
        <meta name="description" content="<?= $pageDescription ?? 'On joue, on observe, on t\'éclaire. Pas de pub, pas de langue de bois — juste notre regard de passionnés, pour affiner le tien.' ?>">
        <meta name="keywords" content="gaming, jeux vidéo, belgique, actualité, tests, dossiers, passionnés">
        <meta name="author" content="Belgium Video Gaming">
        <meta name="robots" content="index, follow">
        
        <!-- Open Graph / Facebook -->
        <meta property="og:type" content="website">
        <meta property="og:url" content="https://belgium-video-gaming.be">
        <meta property="og:title" content="<?= $pageTitle ?? 'Belgium Video Gaming' ?>">
        <meta property="og:description" content="<?= $pageDescription ?? 'On joue, on observe, on t\'éclaire. Pas de pub, pas de langue de bois — juste notre regard de passionnés, pour affiner le tien.' ?>">
        <meta property="og:image" content="https://belgium-video-gaming.be/public/assets/images/default-featured.jpg">
        <meta property="og:site_name" content="Belgium Video Gaming">
        
        <!-- Twitter -->
        <meta property="twitter:card" content="summary_large_image">
        <meta property="twitter:url" content="https://belgium-video-gaming.be">
        <meta property="twitter:title" content="<?= $pageTitle ?? 'Belgium Video Gaming' ?>">
        <meta property="twitter:description" content="<?= $pageDescription ?? 'On joue, on observe, on t\'éclaire. Pas de pub, pas de langue de bois — juste notre regard de passionnés, pour affiner le tien.' ?>">
        <meta property="twitter:image" content="https://belgium-video-gaming.be/public/assets/images/default-featured.jpg">
        
        <!-- Canonical URL -->
        <link rel="canonical" href="https://belgium-video-gaming.be">
    <?php endif; ?>
    
    <link rel="icon" type="image/x-icon" href="/favicon.ico?v=<?= time() ?>">
    
    <!-- Google Fonts - Poppins pour les titres -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- CSS principal avec thème belge -->
    <link rel="stylesheet" href="/style.css">
    
    <!-- CSS pour les modules de contenu -->
    <link rel="stylesheet" href="/public/assets/css/components/content-modules.css">
    
    <!-- CSS pour la navbar -->
    <link rel="stylesheet" href="/public/assets/css/components/navbar.css">
    
    <!-- CSS additionnel spécifique à la page -->
    <?php if (isset($additionalCSS)): ?>
        <?php foreach ($additionalCSS as $css): ?>
            <link rel="stylesheet" href="<?= $css ?>">
        <?php endforeach; ?>
    <?php endif; ?>
    
    <style>
        /* Bannières thématiques dynamiques */
        .theme-banner-left {
            background: url('/theme-image.php?theme=<?php echo htmlspecialchars($currentTheme['name'] ?? 'defaut'); ?>&side=left') no-repeat center center !important;
        }
        
        .theme-banner-right {
            background: url('/theme-image.php?theme=<?php echo htmlspecialchars($currentTheme['name'] ?? 'defaut'); ?>&side=right') no-repeat center center !important;
        }
    </style>
</head>
<body>
    <!-- Header avec thème belge -->
    <header class="header">
        <div class="container">
            <div class="header-content">
                <a href="/" class="logo" title="Retour à l'accueil">
                    <div class="logo-icon">🎮</div>
                    <div class="logo-text">
                        <h1>GameNews</h1>
                        <div class="logo-subtitle">🇧🇪 BELGIQUE</div>
                    </div>
                </a>
                
                <h1 class="header-title">
                    L'actualité jeux vidéo en Belgique
                </h1>
                
                <?php if ($isLoggedIn): ?>
                    <div style="display: flex; gap: 0.5rem; align-items: center;">
                        <a href="/admin/dashboard" class="login-btn">Dashboard</a>
                        <a href="/auth/logout" class="logout-btn" onclick="return confirm('Êtes-vous sûr de vouloir vous déconnecter ?')">Se déconnecter</a>
                    </div>
                <?php else: ?>
                    <div style="display: flex; gap: 0.5rem; align-items: center;">
                        <a href="/auth/login" class="login-btn">Se connecter</a>
                        <a href="/auth/register" class="register-btn">S'inscrire</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- Navbar de navigation -->
    <?php 
    // Inclure le modèle Hardware pour la navbar
    require_once __DIR__ . '/../../models/Hardware.php';
    include __DIR__ . '/../components/navbar.php'; 
    ?>

    <!-- Bannières thématiques de fond -->
    <div class="theme-banner-left"></div>
    <div class="theme-banner-right"></div>

    <!-- Layout principal -->
    <div class="main-layout">
        <!-- Contenu principal -->
        <main class="main-content">
            <?php if (isset($article)): ?>
                <!-- Page article : inclure le contenu de l'article -->
                <?php include __DIR__ . '/../articles/show.php'; ?>
            <?php elseif (isset($featuredArticles)): ?>
                <!-- Page d'accueil : inclure le contenu de la page d'accueil -->
                <?php include __DIR__ . '/../home/index.php'; ?>
            <?php else: ?>
                <!-- Contenu générique -->
                <?= $content ?? 'Contenu non disponible' ?>
            <?php endif; ?>
        </main>
    </div>

    <!-- Footer avec thème belge -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-grid">
                    <!-- Colonne 1 - À propos -->
                    <div class="footer-column">
                        <h3 class="footer-title yellow">À propos de GameNews</h3>
                        <p class="footer-text">
                            Votre source #1 pour l'actualité jeux vidéo en Belgique. Reviews, tests, guides et tout l'univers gaming depuis 2020.
                        </p>
                        <div class="footer-buttons">
                            <button class="footer-btn">Twitter</button>
                            <button class="footer-btn">YouTube</button>
                            <button class="footer-btn">Discord</button>
                        </div>
                    </div>
                    
                    <!-- Colonne 2 - Navigation -->
                    <div class="footer-column">
                        <h3 class="footer-title red">Navigation</h3>
                        <ul class="footer-links">
                            <li><a href="/">Accueil</a></li>
                            <li><a href="/category/tests">Tests & Reviews</a></li>
                            <li><a href="/category/news">Actualités</a></li>
                            <li><a href="/category/guides">Guides</a></li>
                            <li><a href="/category/esports">eSports</a></li>
                            <li><a href="/category/materiel">Matériel</a></li>
                        </ul>
                    </div>
                    
                    <!-- Colonne 3 - Newsletter & Contact -->
                    <div class="footer-column">
                        <h3 class="footer-title">Restez connecté 🇧🇪</h3>
                        <p class="footer-text">
                            Recevez les dernières news gaming directement dans votre boîte mail !
                        </p>
                        <div class="footer-newsletter">
                            <input type="email" placeholder="Votre email...">
                            <button>S'abonner</button>
                        </div>
                        <div class="footer-contact">
                            <p>📧 contact@gamenews.be</p>
                            <p>📍 Bruxelles, Belgique</p>
                        </div>
                    </div>
                </div>
                
                <div class="footer-bottom">
                    <p>&copy; 2025 GameNews Belgium. Tous droits réservés. | Mentions légales | Politique de confidentialité</p>
                    <p>🇧🇪 Fièrement belge - Made in Belgium</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- JavaScript pour la navbar -->
    <script src="/public/assets/js/navbar.js"></script>
    
    <!-- JavaScript additionnel spécifique à la page -->
    <?php if (isset($additionalJS)): ?>
        <?php foreach ($additionalJS as $js): ?>
            <script src="<?= $js ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <!-- Script lightbox pour les galeries -->
    <script src="/public/assets/js/gallery-lightbox.js"></script>
</body>
</html>
