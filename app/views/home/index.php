<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GameNews - L'actualité jeux vidéo en Belgique</title>
    <meta name="description" content="Votre source #1 pour l'actualité jeux vidéo en Belgique. Reviews, tests, guides et tout l'univers gaming depuis 2020.">
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    
    <!-- CSS avec thème belge -->
    <style>
        :root {
            --belgium-red: #CC0000;
            --belgium-yellow: #E6B800;
            --belgium-black: #000000;
            --primary: #1a1a1a;
            --secondary: #2d2d2d;
            --tertiary: #404040;
            --border: #e5e5e5;
            --muted: #f5f5f5;
            --background: #ffffff;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: var(--primary);
            background: var(--background);
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }
        
        /* Header avec thème belge - couleurs correctes et pleine largeur */
        .header {
            background: linear-gradient(135deg, var(--belgium-black) 0%, var(--belgium-yellow) 35%, var(--belgium-red) 100%);
            border-bottom: 3px solid var(--belgium-yellow);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            width: 100vw; /* Pleine largeur de l'écran */
            position: relative;
            left: 50%;
            right: 50%;
            margin-left: -50vw;
            margin-right: -50vw;
            z-index: 10; /* Au-dessus des bannières latérales */
        }
        
        .header-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 0;
        }
        
        .logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .logo-icon {
            width: 48px;
            height: 48px;
            background: var(--belgium-yellow);
            border: 2px solid var(--belgium-black);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: bold;
        }
        
        .logo-text h1 {
            color: white;
            font-size: 1.5rem;
            font-weight: bold;
        }
        
        .logo-subtitle {
            color: var(--belgium-yellow);
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .header-title {
            color: white;
            font-size: 1.5rem;
            font-weight: bold;
            text-align: center;
            display: none;
        }
        
        @media (min-width: 768px) {
            .header-title {
                display: block;
            }
        }
        
        .login-btn {
            background: var(--belgium-red);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }
        
        .login-btn:hover {
            background: #990000;
        }
        
        /* Layout principal avec bannières de fond */
        .main-layout {
            position: relative;
            min-height: calc(100vh - 200px);
        }
        
        /* Bannières thématiques de fond - 28% total (14% chaque côté) */
        .theme-banner-left {
            position: fixed;
            left: 0;
            top: 0;
            width: 14vw; /* 14% de la largeur de l'écran */
            height: 1080px; /* Hauteur fixe pour les images */
            background: url('/theme-image.php?theme=<?php echo htmlspecialchars($currentTheme['name']); ?>&side=left') no-repeat center center;
            background-size: cover;
            background-attachment: fixed; /* Effet parallax */
            z-index: 1;
        }
        
        .theme-banner-right {
            position: fixed;
            right: 0;
            top: 0;
            width: 14vw; /* 14% de la largeur de l'écran */
            height: 1080px; /* Hauteur fixe pour les images */
            background: url('/theme-image.php?theme=<?php echo htmlspecialchars($currentTheme['name']); ?>&side=right') no-repeat center center;
            background-size: cover;
            background-attachment: fixed; /* Effet parallax */
            z-index: 1;
        }
        
        .main-content {
            position: relative;
            z-index: 2;
            width: 68vw; /* 68% de la largeur de l'écran */
            margin: 0 auto;
            padding: 1.5rem 2rem;
            background: white;
            min-height: calc(100vh - 200px);
            box-shadow: 0 0 20px rgba(0,0,0,0.1); /* Ombre pour séparer du fond */
            border-radius: 8px 8px 0 0; /* Coins arrondis en haut */
            /* Centrer parfaitement le contenu */
            margin-left: 16vw; /* 14% + 2% d'espace */
            margin-right: 16vw; /* 14% + 2% d'espace */
        }
        
        /* Responsive - masquer les bannières sur mobile */
        @media (max-width: 1400px) {
            .theme-banner-left,
            .theme-banner-right {
                display: none;
            }
            
            .main-content {
                width: 100%;
                padding: 1.5rem 1rem;
            }
        }
        
        /* Sections */
        .section {
            margin-bottom: 2rem;
        }
        
        .section-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
        }
        
        .section-line {
            width: 4px;
            height: 32px;
        }
        
        .section-line.yellow {
            background: var(--belgium-yellow);
        }
        
        .section-line.red {
            background: var(--belgium-red);
        }
        
        .section-title {
            font-size: 1.875rem;
            font-weight: bold;
            color: var(--primary);
        }
        
        /* Articles en vedette */
        .featured-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            max-height: 80vh;
            border-radius: 8px;
            overflow: hidden;
            border: 2px solid var(--border);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .featured-left {
            display: grid;
            grid-template-rows: 2fr 1fr;
            height: 60vh;
        }
        
        .featured-main {
            position: relative;
            overflow: hidden;
            cursor: pointer;
            transition: box-shadow 0.3s;
        }
        
        .featured-main:hover {
            box-shadow: 0 8px 24px rgba(0,0,0,0.15);
        }
        
        .featured-main img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .featured-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.8) 0%, rgba(0,0,0,0.2) 50%, transparent 100%);
        }
        
        .featured-content {
            position: absolute;
            bottom: 1rem;
            left: 1rem;
            right: 1rem;
        }
        
        .featured-badge {
            background: var(--belgium-yellow);
            color: var(--belgium-black);
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 0.5rem;
        }
        
        .featured-title {
            color: white;
            font-size: 1.125rem;
            font-weight: bold;
            margin-bottom: 0.25rem;
        }
        
        .featured-excerpt {
            color: #e5e5e5;
            font-size: 0.75rem;
        }
        
        .featured-bottom {
            display: grid;
            grid-template-columns: 1fr 1fr;
            border-top: 2px solid var(--border);
        }
        
        .featured-small {
            position: relative;
            overflow: hidden;
            cursor: pointer;
            transition: box-shadow 0.3s;
        }
        
        .featured-small:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        .featured-small:first-child {
            border-right: 2px solid var(--border);
        }
        
        .featured-small img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .featured-small .featured-overlay {
            background: linear-gradient(to top, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0.2) 50%, transparent 100%);
        }
        
        .featured-small .featured-content {
            bottom: 0.5rem;
            left: 0.5rem;
            right: 0.5rem;
        }
        
        .featured-small .featured-title {
            font-size: 0.75rem;
            line-height: 1.2;
        }
        
                 .featured-right {
             display: grid;
             grid-template-rows: 1fr 1fr 1fr;
             height: 60vh;
             max-height: 60vh;
         }
        
        .featured-right .featured-small {
            border-bottom: 2px solid var(--border);
        }
        
        .featured-right .featured-small:last-child {
            border-bottom: none;
        }
        
        /* News section */
        .news-layout {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 1.5rem;
        }
        
        .news-tabs {
            background: var(--muted);
            border: 1px solid var(--border);
            border-radius: 4px;
            margin-bottom: 0.75rem;
        }
        
        .tabs-list {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
        }
        
        .tab-trigger {
            padding: 0.75rem;
            background: transparent;
            border: none;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .tab-trigger.active {
            background: var(--belgium-yellow);
            color: var(--belgium-black);
        }
        
        .tab-content {
            display: none;
            padding: 1rem;
        }
        
        .tab-content.active {
            display: block;
        }
        
        .article-card {
            display: flex;
            border: 1px solid var(--border);
            border-radius: 8px;
            overflow: hidden;
            cursor: pointer;
            transition: box-shadow 0.3s;
            margin-bottom: 0.75rem;
        }
        
        .article-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .article-image {
            width: 112px;
            height: 96px;
            flex-shrink: 0;
        }
        
        .article-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .article-content {
            flex: 1;
            padding: 1rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        
        .article-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 0.5rem;
        }
        
        .article-badge {
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        
        .badge-test {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }
        
        .badge-news {
            background: #dbeafe;
            color: #1e40af;
            border: 1px solid #bfdbfe;
        }
        
        .badge-guide {
            background: #f3e8ff;
            color: #7c3aed;
            border: 1px solid #ddd6fe;
        }
        
        .article-date {
            font-size: 0.75rem;
            color: #666;
        }
        
        .article-title {
            font-size: 1.125rem;
            font-weight: 600;
            line-height: 1.3;
            margin-bottom: 0.5rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .article-excerpt {
            font-size: 0.875rem;
            color: #666;
        }
        
        /* Trailers section */
        .trailers-header {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }
        
        .trailers-icon {
            width: 20px;
            height: 20px;
            color: var(--belgium-red);
        }
        
        .trailers-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--primary);
        }
        
        .trailers-container {
            border: 2px solid var(--border);
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .trailer-item {
            position: relative;
            height: 64px;
            cursor: pointer;
            transition: box-shadow 0.3s;
        }
        
        .trailer-item:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        .trailer-item:not(:last-child) {
            border-bottom: 1px solid var(--border);
        }
        
        .trailer-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .trailer-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.8) 0%, rgba(0,0,0,0.4) 50%, transparent 100%);
        }
        
        .trailer-play {
            position: absolute;
            inset: 0;
            background: rgba(0,0,0,0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s;
        }
        
        .trailer-item:hover .trailer-play {
            opacity: 1;
        }
        
        .trailer-play-icon {
            width: 24px;
            height: 24px;
            color: var(--belgium-yellow);
        }
        
        .trailer-duration {
            position: absolute;
            bottom: 0.25rem;
            right: 0.25rem;
            background: var(--belgium-red);
            color: white;
            font-size: 0.75rem;
            padding: 0.125rem 0.375rem;
            border-radius: 2px;
            font-weight: 600;
        }
        
        .trailer-title {
            position: absolute;
            bottom: 0.25rem;
            left: 0.25rem;
            right: 3rem;
            color: white;
            font-size: 0.75rem;
            font-weight: 600;
            line-height: 1.2;
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        /* Bannières thématiques de fond - styles pour PNG */

        
        /* Footer avec thème belge - pleine largeur */
        .footer {
            background: linear-gradient(135deg, var(--belgium-black) 0%, var(--belgium-yellow) 35%, var(--belgium-red) 100%);
            color: white;
            margin-top: 4rem;
            width: 100vw; /* Pleine largeur de l'écran */
            position: relative;
            left: 50%;
            right: 50%;
            margin-left: -50vw;
            margin-right: -50vw;
            z-index: 10; /* Au-dessus des bannières latérales */
        }
        
        .footer-content {
            padding: 3rem 0;
        }
        
        .footer-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 2rem;
        }
        
        @media (min-width: 768px) {
            .footer-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }
        
        .footer-column {
            border-left: 4px solid var(--belgium-yellow);
            padding-left: 1rem;
        }
        
        .footer-column:nth-child(2) {
            border-left-color: var(--belgium-red);
        }
        
        .footer-column:nth-child(3) {
            border-left-color: white;
        }
        
        .footer-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }
        
        .footer-title.yellow {
            color: var(--belgium-yellow);
        }
        
        .footer-title.red {
            color: var(--belgium-red);
        }
        
        .footer-text {
            font-size: 0.875rem;
            opacity: 0.9;
            margin-bottom: 1rem;
        }
        
        .footer-links {
            list-style: none;
        }
        
        .footer-links li {
            margin-bottom: 0.5rem;
        }
        
        .footer-links a {
            color: white;
            text-decoration: none;
            opacity: 0.9;
            transition: all 0.3s;
        }
        
        .footer-links a:hover {
            opacity: 1;
            color: var(--belgium-yellow);
            text-decoration: underline;
        }
        
        .footer-buttons {
            display: flex;
            gap: 1rem;
            margin-bottom: 1rem;
        }
        
        .footer-btn {
            background: transparent;
            color: white;
            border: 1px solid rgba(255,255,255,0.2);
            padding: 0.5rem 1rem;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 0.875rem;
        }
        
        .footer-btn:hover {
            background: var(--belgium-yellow);
            color: var(--belgium-black);
            border-color: var(--belgium-yellow);
        }
        
        .footer-newsletter input {
            width: 100%;
            padding: 0.5rem 0.75rem;
            border-radius: 4px;
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.2);
            color: white;
            margin-bottom: 0.75rem;
        }
        
        .footer-newsletter input::placeholder {
            color: rgba(255,255,255,0.6);
        }
        
        .footer-newsletter button {
            width: 100%;
            background: var(--belgium-red);
            color: white;
            border: none;
            padding: 0.5rem;
            border-radius: 4px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }
        
        .footer-newsletter button:hover {
            background: #990000;
        }
        
        .footer-contact {
            font-size: 0.75rem;
            opacity: 0.75;
        }
        
        .footer-bottom {
            border-top: 1px solid rgba(255,255,255,0.2);
            padding-top: 1.5rem;
            text-align: center;
            font-size: 0.875rem;
            opacity: 0.75;
        }
        
        .footer-bottom p:last-child {
            margin-top: 0.5rem;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .main-content {
                width: 100%;
            }
            
            .featured-grid {
                grid-template-columns: 1fr;
                max-height: none;
            }
            
            .featured-left {
                height: auto;
            }
            
            .featured-right {
                height: auto;
            }
            
            .news-layout {
                grid-template-columns: 1fr;
            }
        }
    </style>
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
                    L'actualité jeux vidéo en Belgique
                </h1>
                
                <?php if ($isLoggedIn): ?>
                    <a href="/admin/dashboard" class="login-btn">Dashboard</a>
                <?php else: ?>
                    <a href="/auth/login" class="login-btn">Se connecter</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- Bannières thématiques de fond -->
    <div class="theme-banner-left"></div>
    <div class="theme-banner-right"></div>

    <!-- Layout principal -->
    <div class="main-layout">
        <!-- Contenu principal -->
        <main class="main-content">
            <!-- Section Articles en avant -->
            <section class="section">
                <div class="section-header">
                    <div class="section-line yellow"></div>
                    <h2 class="section-title">Articles en avant</h2>
                    <div class="section-line red"></div>
                </div>
                
                <div class="featured-grid">
                    <!-- Colonne gauche (2/3) -->
                    <div class="featured-left">
                        <?php if (!empty($featuredArticles)): ?>
                            <!-- Article principal -->
                            <div class="featured-main">
                                                                 <img src="/image.php?file=<?php echo urlencode($featuredArticles[0]['cover_image'] ?? 'default.jpg'); ?>" 
                                     alt="<?php echo htmlspecialchars($featuredArticles[0]['title']); ?>">
                                <div class="featured-overlay"></div>
                                <div class="featured-content">
                                    <span class="featured-badge">À la une</span>
                                    <h3 class="featured-title"><?php echo htmlspecialchars($featuredArticles[0]['title']); ?></h3>
                                    <p class="featured-excerpt"><?php echo htmlspecialchars($featuredArticles[0]['excerpt'] ?? 'Découvrez cet article...'); ?></p>
                                </div>
                            </div>
                            
                                                         <!-- Articles secondaires -->
                             <div class="featured-bottom">
                                 <?php for ($i = 1; $i < min(3, count($featuredArticles)); $i++): ?>
                                     <div class="featured-small">
                                         <img src="/image.php?file=<?php echo urlencode($featuredArticles[$i]['cover_image'] ?? 'default.jpg'); ?>" 
                                              alt="<?php echo htmlspecialchars($featuredArticles[$i]['title']); ?>">
                                         <div class="featured-overlay"></div>
                                         <div class="featured-content">
                                             <h4 class="featured-title"><?php echo htmlspecialchars($featuredArticles[$i]['title']); ?></h4>
                                         </div>
                                     </div>
                                 <?php endfor; ?>
                                 
                                 <?php if (count($featuredArticles) < 3): ?>
                                     <!-- Remplir avec du contenu par défaut pour avoir exactement 2 cases -->
                                     <?php for ($i = max(1, count($featuredArticles)); $i < 3; $i++): ?>
                                         <div class="featured-small">
                                             <img src="/assets/images/default-article.jpg" alt="Article par défaut">
                                             <div class="featured-overlay"></div>
                                             <div class="featured-content">
                                                 <h4 class="featured-title">Article à venir...</h4>
                                             </div>
                                         </div>
                                     <?php endfor; ?>
                                 <?php endif; ?>
                             </div>
                                                 <?php else: ?>
                             <!-- Contenu par défaut si pas d'articles -->
                             <div class="featured-main">
                                 <img src="/assets/images/default-featured.jpg" alt="Article par défaut">
                                 <div class="featured-overlay"></div>
                                 <div class="featured-content">
                                     <span class="featured-badge">À la une</span>
                                     <h3 class="featured-title">Bienvenue sur GameNews</h3>
                                     <p class="featured-excerpt">Découvrez l'actualité gaming en Belgique</p>
                                 </div>
                             </div>
                             
                             <!-- Articles secondaires par défaut -->
                             <div class="featured-bottom">
                                 <?php for ($i = 0; $i < 2; $i++): ?>
                                     <div class="featured-small">
                                         <img src="/assets/images/default-article.jpg" alt="Article par défaut">
                                         <div class="featured-overlay"></div>
                                         <div class="featured-content">
                                             <h4 class="featured-title">Article à venir...</h4>
                                         </div>
                                     </div>
                                 <?php endfor; ?>
                             </div>
                         <?php endif; ?>
                    </div>
                    
                                         <!-- Colonne droite (1/3) -->
                     <div class="featured-right">
                         <?php for ($i = 3; $i < min(6, count($featuredArticles)); $i++): ?>
                             <div class="featured-small">
                                                                                                    <img src="/image.php?file=<?php echo urlencode($featuredArticles[$i]['cover_image'] ?? 'default.jpg'); ?>" 
                                      alt="<?php echo htmlspecialchars($featuredArticles[$i]['title']); ?>">
                                 <div class="featured-overlay"></div>
                                 <div class="featured-content">
                                     <h4 class="featured-title"><?php echo htmlspecialchars($featuredArticles[$i]['title']); ?></h4>
                                 </div>
                             </div>
                         <?php endfor; ?>
                         
                         <?php if (count($featuredArticles) < 6): ?>
                             <!-- Remplir avec du contenu par défaut pour avoir exactement 3 cases -->
                             <?php for ($i = max(3, count($featuredArticles)); $i < 6; $i++): ?>
                                 <div class="featured-small">
                                     <img src="/assets/images/default-article.jpg" alt="Article par défaut">
                                     <div class="featured-overlay"></div>
                                     <div class="featured-content">
                                         <h4 class="featured-title">Article à venir...</h4>
                                     </div>
                                 </div>
                             <?php endfor; ?>
                         <?php endif; ?>
                     </div>
                </div>
            </section>

            <!-- Section Dernières news -->
            <section class="section">
                <div class="section-header">
                    <div class="section-line red"></div>
                    <h2 class="section-title">Dernières news</h2>
                    <div class="section-line yellow"></div>
                </div>
                
                <div class="news-layout">
                    <!-- Colonne articles -->
                    <div class="news-articles">
                        <div class="news-tabs">
                            <div class="tabs-list">
                                <button class="tab-trigger active" onclick="showTab('page1')">Articles 1-10</button>
                                <button class="tab-trigger" onclick="showTab('page2')">Articles 11-20</button>
                                <button class="tab-trigger" onclick="showTab('page3')">Articles 21-30</button>
                            </div>
                        </div>
                        
                        <?php for ($page = 1; $page <= 3; $page++): ?>
                            <div id="page<?php echo $page; ?>" class="tab-content <?php echo $page === 1 ? 'active' : ''; ?>">
                                <?php 
                                $start = ($page - 1) * 10;
                                $end = $start + 10;
                                $pageArticles = array_slice($latestArticles, $start, 10);
                                ?>
                                
                                <?php foreach ($pageArticles as $article): ?>
                                    <div class="article-card" onclick="window.location.href='/articles/<?php echo $article['slug']; ?>'">
                                        <div class="article-image">
                                                                                         <img src="/image.php?file=<?php echo urlencode($article['cover_image'] ?? 'default.jpg'); ?>" 
                                                  alt="<?php echo htmlspecialchars($article['title']); ?>">
                                        </div>
                                        <div class="article-content">
                                            <div class="article-header">
                                                <span class="article-badge badge-<?php echo strtolower($article['category_name'] ?? 'news'); ?>">
                                                    <?php echo htmlspecialchars($article['category_name'] ?? 'NEWS'); ?>
                                                </span>
                                                <span class="article-date">
                                                    <?php echo date('d/m/Y', strtotime($article['published_at'])); ?>
                                                </span>
                                            </div>
                                            <h3 class="article-title"><?php echo htmlspecialchars($article['title']); ?></h3>
                                            <p class="article-excerpt"><?php echo htmlspecialchars($article['excerpt'] ?? ''); ?></p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                                
                                <?php if (empty($pageArticles)): ?>
                                    <p style="text-align: center; color: #666; padding: 2rem;">Aucun article disponible</p>
                                <?php endif; ?>
                            </div>
                        <?php endfor; ?>
                    </div>
                    
                    <!-- Colonne trailers -->
                    <div class="news-trailers">
                        <div class="trailers-header">
                            <svg class="trailers-icon" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M8 5v14l11-7z"/>
                            </svg>
                            <h3 class="trailers-title">Derniers trailers</h3>
                        </div>
                        
                        <div class="trailers-container">
                            <?php if (!empty($trailers)): ?>
                                <?php foreach ($trailers as $trailer): ?>
                                    <div class="trailer-item" onclick="window.location.href='/articles/<?php echo $trailer['slug']; ?>'">
                                                                                 <img src="/image.php?file=<?php echo urlencode($trailer['cover_image'] ?? 'default.jpg'); ?>" 
                                              alt="<?php echo htmlspecialchars($trailer['title']); ?>" class="trailer-image">
                                        <div class="trailer-overlay"></div>
                                        <div class="trailer-play">
                                            <svg class="trailer-play-icon" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M8 5v14l11-7z"/>
                                            </svg>
                                        </div>
                                        <div class="trailer-duration">2:34</div>
                                        <div class="trailer-title"><?php echo htmlspecialchars($trailer['title']); ?></div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <!-- Trailers par défaut -->
                                <?php 
                                $defaultTrailers = [
                                    ['title' => 'Final Fantasy XVII - Trailer officiel', 'duration' => '2:34'],
                                    ['title' => 'Call of Duty 2025 - Gameplay', 'duration' => '4:12'],
                                    ['title' => 'Mario Kart Ultimate - Annonce', 'duration' => '1:58'],
                                    ['title' => 'Zelda: Echoes of Time - Teaser', 'duration' => '3:21'],
                                    ['title' => 'Assassin\'s Creed Origins - Bande-annonce', 'duration' => '2:45']
                                ];
                                ?>
                                <?php foreach ($defaultTrailers as $trailer): ?>
                                    <div class="trailer-item">
                                        <img src="/assets/images/default-trailer.jpg" alt="<?php echo $trailer['title']; ?>" class="trailer-image">
                                        <div class="trailer-overlay"></div>
                                        <div class="trailer-play">
                                            <svg class="trailer-play-icon" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M8 5v14l11-7z"/>
                                            </svg>
                                        </div>
                                        <div class="trailer-duration"><?php echo $trailer['duration']; ?></div>
                                        <div class="trailer-title"><?php echo $trailer['title']; ?></div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </section>
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

    <script>
        function showTab(tabName) {
            // Masquer tous les contenus
            const tabContents = document.querySelectorAll('.tab-content');
            tabContents.forEach(content => content.classList.remove('active'));
            
            // Désactiver tous les triggers
            const tabTriggers = document.querySelectorAll('.tab-trigger');
            tabTriggers.forEach(trigger => trigger.classList.remove('active'));
            
            // Afficher le contenu sélectionné
            document.getElementById(tabName).classList.add('active');
            
            // Activer le trigger sélectionné
            event.target.classList.add('active');
        }
    </script>
</body>
</html>
