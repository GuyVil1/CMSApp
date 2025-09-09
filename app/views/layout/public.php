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
    
    <!-- Google Fonts - Luckiest Guy pour les titres -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Luckiest+Guy&display=swap" rel="stylesheet">
    
    <!-- CSS principal avec thème belge -->
    <link rel="stylesheet" href="/style.css">
    
    <!-- CSS main avec home.css (doit être chargé APRÈS style.css) -->
    <link rel="stylesheet" href="/public/assets/css/main.css">
    
    <!-- CSS pour les modules de contenu -->
    <link rel="stylesheet" href="/public/assets/css/components/content-modules.css">
    
    <!-- CSS pour la navbar -->
    <link rel="stylesheet" href="/public/assets/css/components/navbar.css">
    
    <!-- CSS pour le lazy loading -->
    <link rel="stylesheet" href="/public/assets/css/components/lazy-loading.css">
    
    <!-- CSS responsive pour les articles -->
    <link rel="stylesheet" href="/public/assets/css/components/article-responsive.css">
    
    <!-- CSS pour les pages légales -->
    <link rel="stylesheet" href="/public/assets/css/pages/legal.css">
    
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
        
        /* Mode sombre */
        <?php if (isset($darkMode) && $darkMode): ?>
        body {
            background-color: #1a1a1a !important;
            color: #ffffff !important;
        }
        
        .main-layout {
            background-color: #1a1a1a !important;
        }
        
        .main-content {
            background-color: #1a1a1a !important;
            color: #ffffff !important;
        }
        
        .section {
            background-color: #2d2d2d !important;
            color: #ffffff !important;
        }
        
        .section-title {
            color: #ffffff !important;
        }
        
        .featured-title, .trailer-title {
            color: #ffffff !important;
        }
        
        .footer {
            background-color: #1a1a1a !important;
            color: #ffffff !important;
        }
        
        .footer-text {
            color: #cccccc !important;
        }
        
        .header {
            background-color: #2d2d2d !important;
        }
        
        .header-title {
            color: #ffffff !important;
        }
        
        .logo-text h1 {
            color: #ffffff !important;
            text-shadow: 2px 2px 0px #000000, -2px -2px 0px #000000, 2px -2px 0px #000000, -2px 2px 0px #000000;
            -webkit-text-stroke: 1px #000000;
        }
        <?php endif; ?>
    </style>
</head>
<body<?php if (isset($darkMode) && $darkMode): ?> class="dark-mode"<?php endif; ?>>
    <!-- Header avec thème belge -->
    <header class="header">
        <div class="container">
            <div class="header-content">
                <a href="/" class="logo" title="Retour à l'accueil">
                    <div class="logo-icon">
                        <?php 
                        // Charger le logo configuré
                        if (!class_exists('Setting')) {
                            require_once __DIR__ . '/../../app/models/Setting.php';
                        }
                        $headerLogo = \Setting::get('header_logo', 'Logo.svg');
                        ?>
                        <img src="/assets/images/logos/<?= htmlspecialchars($headerLogo) ?>" 
                             alt="Belgium Video Gaming" 
                             style="width: 100%; height: 100%; object-fit: contain;">
                    </div>
                    <div class="logo-text">
                        <h1>Belgium<br>Video Gaming</h1>
                    </div>
                </a>
                
                <h1 class="header-title">
                    L'actualité jeux vidéo en Belgique
                </h1>
                
                <!-- Boutons d'authentification -->
                <div class="header-auth">
                    <?php if ($isLoggedIn): ?>
                        <a href="/admin/dashboard" class="login-btn">Dashboard</a>
                        <a href="/auth/logout" class="logout-btn" onclick="return confirm('Êtes-vous sûr de vouloir vous déconnecter ?')">Se déconnecter</a>
                    <?php else: ?>
                        <a href="/auth/login" class="login-btn">Se connecter</a>
                        <?php if ($allowRegistration ?? true): ?>
                            <a href="/auth/register" class="register-btn">S'inscrire</a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
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
                        <h3 class="footer-title yellow">Belgium Video Gaming</h3>
                        <p class="footer-text">
                            <?php 
                            // Charger les paramètres footer
                            if (!class_exists('Setting')) {
                                require_once __DIR__ . '/../../app/models/Setting.php';
                            }
                            $footerTagline = \Setting::get('footer_tagline', 'Votre source #1 pour l\'actualité jeux vidéo en Belgique. Reviews, tests, guides et tout l\'univers gaming depuis 2020.');
                            echo htmlspecialchars($footerTagline);
                            ?>
                        </p>
                        <div class="footer-buttons">
                            <?php 
                            // Récupérer les URLs des réseaux sociaux
                            $socialTwitter = \Setting::get('social_twitter', '');
                            $socialFacebook = \Setting::get('social_facebook', '');
                            $socialYoutube = \Setting::get('social_youtube', '');
                            ?>
                            
                            <?php if (!empty($socialTwitter)): ?>
                                <a href="<?= htmlspecialchars($socialTwitter) ?>" target="_blank" rel="noopener noreferrer" class="footer-btn">Twitter</a>
                            <?php else: ?>
                                <span class="footer-btn disabled">Twitter</span>
                            <?php endif; ?>
                            
                            <?php if (!empty($socialFacebook)): ?>
                                <a href="<?= htmlspecialchars($socialFacebook) ?>" target="_blank" rel="noopener noreferrer" class="footer-btn">Facebook</a>
                            <?php else: ?>
                                <span class="footer-btn disabled">Facebook</span>
                            <?php endif; ?>
                            
                            <?php if (!empty($socialYoutube)): ?>
                                <a href="<?= htmlspecialchars($socialYoutube) ?>" target="_blank" rel="noopener noreferrer" class="footer-btn">YouTube</a>
                            <?php else: ?>
                                <span class="footer-btn disabled">YouTube</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Colonne 2 - Navigation -->
                    <div class="footer-column">
                        <h3 class="footer-title red">Navigation</h3>
                        <ul class="footer-links">
                            <?php 
                            // Charger le helper de navigation (une seule fois)
                            if (!class_exists('NavigationHelper')) {
                                require_once __DIR__ . '/../../helpers/navigation_helper.php';
                            }
                            $footerMenus = NavigationHelper::getFooterMenus();
                            
                            foreach ($footerMenus as $menu): ?>
                                <li><a href="<?= htmlspecialchars($menu['url']) ?>"><?= htmlspecialchars($menu['name']) ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    
                    <!-- Colonne 3 - Contact -->
                    <div class="footer-column">
                        <!-- Logo du footer -->
                        <div class="footer-logo" style="text-align: center; margin-bottom: 1.5rem;">
                            <?php 
                            $footerLogo = \Setting::get('footer_logo', 'Logo_neutre_500px.png');
                            ?>
                            <img src="/assets/images/logos/<?= htmlspecialchars($footerLogo) ?>" 
                                 alt="Belgium Video Gaming" 
                                 style="width: 250px; height: 250px; object-fit: contain; opacity: 0.8;">
                        </div>
                        
                        <h3 class="footer-title">RESTEZ CONNECTÉ</h3>
                        
                        <div class="footer-contact">
                            <p>📧 contact@belgium-video-gaming.be</p>
                            <p>📍 Bruxelles, Belgique</p>
                        </div>
                    </div>
                </div>
                
                <div class="footer-bottom">
                    <p>&copy; 2025 Belgium Video Gaming. Tous droits réservés. | <a href="/mentions-legales">Mentions légales</a> | <a href="/politique-confidentialite">Politique de confidentialité</a> | <a href="/cgu">CGU</a> | <a href="/cookies">Cookies</a></p>
                    <p>🇧🇪 Fièrement belge - Made in Belgium</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- JavaScript pour la navbar -->
    <script src="/public/assets/js/navbar.js"></script>
    
    <!-- JavaScript pour le lazy loading -->
    <script src="/public/js/lazy-loading.js"></script>
    
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
