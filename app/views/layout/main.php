<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Belgium Vidéo Gaming' ?></title>
    <link rel="stylesheet" href="/public/assets/css/layout/main-layout.css">
    <?php if (isset($additionalCSS)): ?>
        <?php foreach ($additionalCSS as $css): ?>
            <link rel="stylesheet" href="<?= $css ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>
    <!-- Header principal -->
    <header class="header">
        <div class="header-content">
            <!-- Logo et titre -->
            <div class="logo">
                <div class="logo-icon">🎮</div>
                <div class="logo-text">
                    <div class="logo-title">Belgium Vidéo Gaming</div>
                    <div class="logo-subtitle">L'actualité gaming belge</div>
                </div>
            </div>

            <!-- Navigation principale -->
            <nav class="nav">
                <div class="nav-links">
                    <a href="/" class="nav-link">🏠 Accueil</a>
                    <a href="/games" class="nav-link">🎮 Jeux</a>
                    <a href="/articles" class="nav-link">📰 Articles</a>
                    <a href="/categories" class="nav-link">📂 Catégories</a>
                    <a href="/themes" class="nav-link">🎨 Thèmes</a>
                </div>
            </nav>

            <!-- Authentification -->
            <div class="auth-section">
                <?php if (Auth::isLoggedIn()): ?>
                    <div class="user-info">
                        <span class="user-name">👤 <?= htmlspecialchars(Auth::getUser()['login'] ?? 'Utilisateur') ?></span>
                        <a href="/admin.php" class="btn btn-secondary btn-sm">Admin</a>
                        <a href="/auth/logout" class="btn btn-danger btn-sm">Déconnexion</a>
                    </div>
                <?php else: ?>
                    <a href="/auth/login" class="btn btn-primary">Connexion</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- Contenu principal -->
    <main class="main-content">
        <?php 
        // Désactiver le theme-background sur les pages d'articles
        // car elles ont leur propre système de bannières thématiques
        $isArticlePage = strpos($_SERVER['REQUEST_URI'] ?? '', '/article/') !== false;
        
        if (!$isArticlePage && isset($currentTheme) && $currentTheme && isset($currentTheme['left_image']) && isset($currentTheme['right_image'])): 
        ?>
            <!-- Thème visuel (uniquement sur les pages non-articles) -->
            <div class="theme-background" style="background-image: url('/<?= $currentTheme['left_image'] ?>'), url('/<?= $currentTheme['right_image'] ?>');"></div>
        <?php endif; ?>

        <!-- Contenu de la page -->
        <div class="content-wrapper">
            <?= $content ?? '' ?>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-section">
                <h3>🎮 Belgium Vidéo Gaming</h3>
                <p>L'actualité gaming belge, tests, guides et plus encore.</p>
            </div>
            
            <div class="footer-section">
                <h3>🔗 Liens rapides</h3>
                <ul>
                    <li><a href="/">Accueil</a></li>
                    <li><a href="/games">Jeux</a></li>
                    <li><a href="/articles">Articles</a></li>
                    <li><a href="/categories">Catégories</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h3>📱 Suivez-nous</h3>
                <div class="social-links">
                    <a href="#" class="social-link">📘 Facebook</a>
                    <a href="#" class="social-link">🐦 Twitter</a>
                    <a href="#" class="social-link">📸 Instagram</a>
                    <a href="#" class="social-link">🎥 YouTube</a>
                </div>
            </div>
            
            <div class="footer-section">
                <h3>📧 Contact</h3>
                <p>contact@belgiumvideogaming.be</p>
                <p>© 2024 Belgium Vidéo Gaming</p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <?php if (isset($additionalJS)): ?>
        <?php foreach ($additionalJS as $js): ?>
            <script src="<?= $js ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
