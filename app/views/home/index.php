<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GameNews - L'actualité jeux vidéo en Belgique</title>
    <meta name="description" content="Votre source #1 pour l'actualité jeux vidéo en Belgique. Reviews, tests, guides et tout l'univers gaming depuis 2020.">
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    
    <!-- CSS temporaire avec thème belge -->
    <link rel="stylesheet" href="/style.css">
    <style>
        /* Bannières thématiques dynamiques */
        .theme-banner-left {
            background: url('/theme-image.php?theme=<?php echo htmlspecialchars($currentTheme['name']); ?>&side=left') no-repeat center center !important;
        }
        
        .theme-banner-right {
            background: url('/theme-image.php?theme=<?php echo htmlspecialchars($currentTheme['name']); ?>&side=right') no-repeat center center !important;
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
                    <div style="display: flex; gap: 0.5rem; align-items: center;">
                        <a href="/admin/dashboard" class="login-btn">Dashboard</a>
                        <a href="/auth/logout" class="logout-btn" onclick="return confirm('Êtes-vous sûr de vouloir vous déconnecter ?')">Se déconnecter</a>
                    </div>
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
