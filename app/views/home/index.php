<?php
/**
 * Contenu de la page d'accueil - Utilise le template unifié public.php
 * Le header, bannières thématiques et footer sont gérés par le template
 */

// Définir les métadonnées de la page
$pageTitle = 'Belgium Video Gaming - L\'actualité jeux vidéo en Belgique';
$pageDescription = 'On joue, on observe, on t\'éclaire. Pas de pub, pas de langue de bois — juste notre regard de passionnés, pour affiner le tien.';
?>

<!-- Section Articles en avant -->
<section class="section">
    <div class="section-header">
        <div class="section-line yellow"></div>
        <h2 class="section-title">Articles en avant</h2>
        <div class="section-line red"></div>
    </div>
    
    <div class="featured-grid">
        <?php if (!empty($featuredArticles)): ?>
            <!-- Article principal - occupe A1, A2, B1, B2 (2x2) -->
            <div class="featured-main" onclick="window.location.href='/article/<?php echo $featuredArticles[0]['slug']; ?>'">
                <img src="/image.php?file=<?php echo urlencode($featuredArticles[0]['cover_image'] ?? 'default.jpg'); ?>" 
                     alt="<?php echo htmlspecialchars($featuredArticles[0]['title']); ?>"
                     loading="lazy"
                     class="lazy-responsive">
                <div class="featured-overlay"></div>
                <div class="featured-content">
                    <span class="featured-badge">À la une</span>
                    <?php if (!empty($featuredArticles[0]['category_name'])): ?>
                        <span class="featured-category"><?php echo htmlspecialchars($featuredArticles[0]['category_name']); ?></span>
                    <?php endif; ?>
                    <h3 class="featured-title"><?php echo htmlspecialchars($featuredArticles[0]['title']); ?></h3>
                </div>
            </div>
            
            <!-- Article C1 (colonne 3, ligne 1) -->
            <?php if (isset($featuredArticles[1])): ?>
                <div class="featured-small featured-c1" onclick="window.location.href='/article/<?php echo $featuredArticles[1]['slug']; ?>'">
                    <img src="/image.php?file=<?php echo urlencode($featuredArticles[1]['cover_image'] ?? 'default.jpg'); ?>" 
                         alt="<?php echo htmlspecialchars($featuredArticles[1]['title']); ?>"
                         loading="lazy"
                         class="lazy-responsive">
                    <div class="featured-overlay"></div>
                    <div class="featured-content">
                        <?php if (!empty($featuredArticles[1]['category_name'])): ?>
                            <span class="featured-category"><?php echo htmlspecialchars($featuredArticles[1]['category_name']); ?></span>
                        <?php endif; ?>
                        <h4 class="featured-title"><?php echo htmlspecialchars($featuredArticles[1]['title']); ?></h4>
                    </div>
                </div>
            <?php else: ?>
                <div class="featured-small featured-c1">
                    <img src="/assets/images/default-article.jpg" alt="Article par défaut">
                    <div class="featured-overlay"></div>
                    <div class="featured-content">
                        <h4 class="featured-title">Article à venir...</h4>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Article C2 (colonne 3, ligne 2) -->
            <?php if (isset($featuredArticles[2])): ?>
                <div class="featured-small featured-c2" onclick="window.location.href='/article/<?php echo $featuredArticles[2]['slug']; ?>'">
                    <img src="/image.php?file=<?php echo urlencode($featuredArticles[2]['cover_image'] ?? 'default.jpg'); ?>" 
                         alt="<?php echo htmlspecialchars($featuredArticles[2]['title']); ?>"
                         loading="lazy"
                         class="lazy-responsive">
                    <div class="featured-overlay"></div>
                    <div class="featured-content">
                        <?php if (!empty($featuredArticles[2]['category_name'])): ?>
                            <span class="featured-category"><?php echo htmlspecialchars($featuredArticles[2]['category_name']); ?></span>
                        <?php endif; ?>
                        <h4 class="featured-title"><?php echo htmlspecialchars($featuredArticles[2]['title']); ?></h4>
                    </div>
                </div>
            <?php else: ?>
                <div class="featured-small featured-c2">
                    <img src="/assets/images/default-article.jpg" alt="Article par défaut">
                    <div class="featured-overlay"></div>
                    <div class="featured-content">
                        <h4 class="featured-title">Article à venir...</h4>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Article D1 (colonne 1, ligne 3) -->
            <?php if (isset($featuredArticles[3])): ?>
                <div class="featured-small featured-d1" onclick="window.location.href='/article/<?php echo $featuredArticles[3]['slug']; ?>'">
                    <img src="/image.php?file=<?php echo urlencode($featuredArticles[3]['cover_image'] ?? 'default.jpg'); ?>" 
                         alt="<?php echo htmlspecialchars($featuredArticles[3]['title']); ?>"
                         loading="lazy"
                         class="lazy-responsive">
                    <div class="featured-overlay"></div>
                    <div class="featured-content">
                        <?php if (!empty($featuredArticles[3]['category_name'])): ?>
                            <span class="featured-category"><?php echo htmlspecialchars($featuredArticles[3]['category_name']); ?></span>
                        <?php endif; ?>
                        <h4 class="featured-title"><?php echo htmlspecialchars($featuredArticles[3]['title']); ?></h4>
                    </div>
                </div>
            <?php else: ?>
                <div class="featured-small featured-d1">
                    <img src="/assets/images/default-article.jpg" alt="Article par défaut">
                    <div class="featured-overlay"></div>
                    <div class="featured-content">
                        <h4 class="featured-title">Article à venir...</h4>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Article D2 (colonne 2, ligne 3) -->
            <?php if (isset($featuredArticles[4])): ?>
                <div class="featured-small featured-d2" onclick="window.location.href='/article/<?php echo $featuredArticles[4]['slug']; ?>'">
                    <img src="/image.php?file=<?php echo urlencode($featuredArticles[4]['cover_image'] ?? 'default.jpg'); ?>" 
                         alt="<?php echo htmlspecialchars($featuredArticles[4]['title']); ?>"
                         loading="lazy"
                         class="lazy-responsive">
                    <div class="featured-overlay"></div>
                    <div class="featured-content">
                        <?php if (!empty($featuredArticles[4]['category_name'])): ?>
                            <span class="featured-category"><?php echo htmlspecialchars($featuredArticles[4]['category_name']); ?></span>
                        <?php endif; ?>
                        <h4 class="featured-title"><?php echo htmlspecialchars($featuredArticles[4]['title']); ?></h4>
                    </div>
                </div>
            <?php else: ?>
                <div class="featured-small featured-d2">
                    <img src="/assets/images/default-article.jpg" alt="Article par défaut">
                    <div class="featured-overlay"></div>
                    <div class="featured-content">
                        <h4 class="featured-title">Article à venir...</h4>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Article D3 (colonne 3, ligne 3) -->
            <?php if (isset($featuredArticles[5])): ?>
                <div class="featured-small featured-d3" onclick="window.location.href='/article/<?php echo $featuredArticles[5]['slug']; ?>'">
                    <img src="/image.php?file=<?php echo urlencode($featuredArticles[5]['cover_image'] ?? 'default.jpg'); ?>" 
                         alt="<?php echo htmlspecialchars($featuredArticles[5]['title']); ?>"
                         loading="lazy"
                         class="lazy-responsive">
                    <div class="featured-overlay"></div>
                    <div class="featured-content">
                        <?php if (!empty($featuredArticles[5]['category_name'])): ?>
                            <span class="featured-category"><?php echo htmlspecialchars($featuredArticles[5]['category_name']); ?></span>
                        <?php endif; ?>
                        <h4 class="featured-title"><?php echo htmlspecialchars($featuredArticles[5]['title']); ?></h4>
                    </div>
                </div>
            <?php else: ?>
                <div class="featured-small featured-d3">
                    <img src="/assets/images/default-article.jpg" alt="Article par défaut">
                    <div class="featured-overlay"></div>
                    <div class="featured-content">
                        <h4 class="featured-title">Article à venir...</h4>
                    </div>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <!-- Contenu par défaut -->
            <div class="featured-main">
                <img src="/assets/images/default-featured.jpg" alt="Article par défaut">
                <div class="featured-overlay"></div>
                <div class="featured-content">
                    <span class="featured-badge">À la une</span>
                    <h3 class="featured-title">Bienvenue sur GameNews</h3>
                    <p class="featured-excerpt">Découvrez l'actualité gaming en Belgique</p>
                </div>
            </div>
            
            <!-- Articles par défaut -->
            <div class="featured-small featured-c1">
                <img src="/assets/images/default-article.jpg" alt="Article par défaut">
                <div class="featured-overlay"></div>
                <div class="featured-content">
                    <h4 class="featured-title">Article à venir...</h4>
                </div>
            </div>
            
            <div class="featured-small featured-c2">
                <img src="/assets/images/default-article.jpg" alt="Article par défaut">
                <div class="featured-overlay"></div>
                <div class="featured-content">
                    <h4 class="featured-title">Article à venir...</h4>
                </div>
            </div>
            
            <div class="featured-small featured-d1">
                <img src="/assets/images/default-article.jpg" alt="Article par défaut">
                <div class="featured-overlay"></div>
                <div class="featured-content">
                    <h4 class="featured-title">Article à venir...</h4>
                </div>
            </div>
            
            <div class="featured-small featured-d2">
                <img src="/assets/images/default-article.jpg" alt="Article par défaut">
                <div class="featured-overlay"></div>
                <div class="featured-content">
                    <h4 class="featured-title">Article à venir...</h4>
                </div>
            </div>
            
            <div class="featured-small featured-d3">
                <img src="/assets/images/default-article.jpg" alt="Article par défaut">
                <div class="featured-overlay"></div>
                <div class="featured-content">
                    <h4 class="featured-title">Article à venir...</h4>
                </div>
            </div>
        <?php endif; ?>
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
                        <div class="article-card" onclick="window.location.href='/article/<?php echo $article['slug']; ?>'" style="cursor: pointer;">
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
                                        <?php echo date('d/m/Y', strtotime($article['published_at'] ?? $article['created_at'])); ?>
                                    </span>
                                </div>
                                <h3 class="article-title"><?php echo htmlspecialchars($article['title']); ?></h3>
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
                        <div class="trailer-item" onclick="window.location.href='/article/<?php echo $trailer['slug']; ?>'" style="cursor: pointer;">
                            <img src="/image.php?file=<?php echo urlencode($trailer['cover_image'] ?? 'default.jpg'); ?>" 
                                 alt="<?php echo htmlspecialchars($trailer['title']); ?>" 
                                 loading="lazy"
                                 class="trailer-image">
                            <div class="trailer-overlay"></div>
                            <div class="trailer-play">
                                <svg class="trailer-play-icon" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M8 5v14l11-7z"/>
                                </svg>
                            </div>
                            <div class="trailer-duration">2:34</div>
                            <div class="trailer-title"><?php echo htmlspecialchars($trailer['title']); ?></div>
                            <?php if (!empty($trailer['category_name'])): ?>
                                <span class="trailer-category"><?php echo htmlspecialchars($trailer['category_name']); ?></span>
                            <?php endif; ?>
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

