<?php
/**
 * Vue partielle - Page des jeux belges
 * Le header/footer sont gérés par le template public.php
 */
?>
    <style>
        .belgian-hero {
            background: linear-gradient(135deg, #ffd700, #ffed4e);
            color: #000;
            padding: 3rem 0;
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .belgian-hero h1 {
            font-size: 3rem;
            font-weight: bold;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }
        
        .belgian-hero p {
            font-size: 1.2rem;
            max-width: 600px;
            margin: 0 auto;
            opacity: 0.8;
        }
        
        .belgian-stats {
            display: flex;
            justify-content: center;
            gap: 2rem;
            margin: 2rem 0;
            flex-wrap: wrap;
        }
        
        .stat-item {
            text-align: center;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            min-width: 120px;
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            display: block;
        }
        
        .stat-label {
            font-size: 0.9rem;
            opacity: 0.8;
        }
        
        .games-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 2rem;
            margin: 2rem 0;
        }
        
        .game-card {
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .game-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        .game-cover {
            width: 100%;
            height: 200px;
            object-fit: cover;
            background: #f0f0f0;
        }
        
        .game-info {
            padding: 1.5rem;
        }
        
        .game-title {
            font-size: 1.2rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
            color: #333;
        }
        
        .game-developer {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }
        
        .game-platforms {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }
        
        .platform-badge {
            background: #ffd700;
            color: #000;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .belgian-badge {
            background: linear-gradient(135deg, #ffd700, #ffed4e);
            color: #000;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: bold;
            display: inline-block;
            margin-top: 1rem;
            box-shadow: 0 2px 4px rgba(255, 215, 0, 0.3);
        }
        
        .articles-section {
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 2px solid #ffd700;
        }
        
        .articles-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }
        
        .article-card {
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            display: block;
        }
        
        .article-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        .article-cover {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }
        
        .article-content {
            padding: 1rem;
        }
        
        .article-title {
            font-size: 1.1rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
            color: #333;
        }
        
        .article-meta {
            color: #666;
            font-size: 0.9rem;
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin: 2rem 0;
        }
        
        .pagination a, .pagination span {
            padding: 0.5rem 1rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            text-decoration: none;
            color: #333;
        }
        
        .pagination a:hover {
            background: #ffd700;
            color: #000;
        }
        
        .pagination .current {
            background: #ffd700;
            color: #000;
            font-weight: bold;
        }
        
        @media (max-width: 768px) {
            .belgian-hero h1 {
                font-size: 2rem;
            }
            
            .belgian-stats {
                gap: 1rem;
            }
            
            .games-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

<!-- Hero Section -->

    <!-- Hero Section -->
    <section class="belgian-hero">
        <div class="container">
            <h1>🇧🇪 Jeux Belges</h1>
            <p>Découvrez tous les jeux développés par des studios belges. Une sélection exclusive des créations made in Belgium qui font rayonner notre pays dans l'industrie du jeu vidéo.</p>
            
            <div class="belgian-stats">
                <div class="stat-item">
                    <span class="stat-number"><?= $totalBelgianGames ?></span>
                    <span class="stat-label">Jeux Belges</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number"><?= count($belgianArticles) ?></span>
                    <span class="stat-label">Articles</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number"><?= count(array_unique(array_column($belgianGames, 'developer'))) ?></span>
                    <span class="stat-label">Studios</span>
                </div>
            </div>
        </div>
    </section>

    <div class="container">
        <!-- Jeux Belges -->
        <section class="games-section">
            <h2 style="text-align: center; margin-bottom: 2rem; color: #333;">🎮 Nos Jeux Belges</h2>
            
            <?php if (!empty($belgianGames)): ?>
                <div class="games-grid">
                    <?php foreach ($belgianGames as $game): ?>
                        <div class="game-card">
                            <?php if ($game->getCoverImageUrl()): ?>
                                <img src="<?= htmlspecialchars($game->getCoverImageUrl()) ?>" 
                                     alt="<?= htmlspecialchars($game->getTitle()) ?>" 
                                     class="game-cover">
                            <?php else: ?>
                                <div class="game-cover" style="display: flex; align-items: center; justify-content: center; background: #f0f0f0; color: #999;">
                                    🎮
                                </div>
                            <?php endif; ?>
                            
                            <div class="game-info">
                                <h3 class="game-title"><?= htmlspecialchars($game->getTitle()) ?></h3>
                                
                                <?php if ($game->getDeveloper()): ?>
                                    <div class="game-developer">
                                        <strong>Développeur:</strong> <?= htmlspecialchars($game->getDeveloper()) ?>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($game->getPublisher()): ?>
                                    <div class="game-developer">
                                        <strong>Éditeur:</strong> <?= htmlspecialchars($game->getPublisher()) ?>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="game-platforms">
                                    <?php if ($game->isMultiPlatform()): ?>
                                        <?php foreach ($game->getPlatforms() as $platform): ?>
                                            <span class="platform-badge"><?= htmlspecialchars($platform->getName()) ?></span>
                                        <?php endforeach; ?>
                                    <?php elseif ($game->getHardwareName()): ?>
                                        <span class="platform-badge"><?= htmlspecialchars($game->getHardwareName()) ?></span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="belgian-badge">🇧🇪 Made in Belgium</div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <div class="pagination">
                        <?php if ($currentPage > 1): ?>
                            <a href="/belges?page=<?= $currentPage - 1 ?>">← Précédent</a>
                        <?php endif; ?>
                        
                        <?php for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++): ?>
                            <?php if ($i == $currentPage): ?>
                                <span class="current"><?= $i ?></span>
                            <?php else: ?>
                                <a href="/belges?page=<?= $i ?>"><?= $i ?></a>
                            <?php endif; ?>
                        <?php endfor; ?>
                        
                        <?php if ($currentPage < $totalPages): ?>
                            <a href="/belges?page=<?= $currentPage + 1 ?>">Suivant →</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div style="text-align: center; padding: 3rem; color: #666;">
                    <h3>Aucun jeu belge trouvé</h3>
                    <p>Revenez bientôt pour découvrir nos jeux made in Belgium !</p>
                </div>
            <?php endif; ?>
        </section>

        <!-- Articles liés -->
        <?php if (!empty($belgianArticles)): ?>
            <section class="articles-section">
                <h2 style="text-align: center; margin-bottom: 2rem; color: #333;">📰 Actualités des Jeux Belges</h2>
                
                <div class="articles-grid">
                    <?php foreach ($belgianArticles as $article): ?>
                        <a href="/article/<?= htmlspecialchars($article['slug']) ?>" class="article-card" style="text-decoration: none; color: inherit;">
                            <?php if (!empty($article['cover_filename'])): ?>
                                <img src="/image.php?file=<?= urlencode($article['cover_filename']) ?>" 
                                     alt="<?= htmlspecialchars($article['title']) ?>" 
                                     class="article-cover">
                            <?php else: ?>
                                <div class="article-cover" style="display: flex; align-items: center; justify-content: center; background: #f0f0f0; color: #999;">
                                    📰
                                </div>
                            <?php endif; ?>
                            
                            <div class="article-content">
                                <h3 class="article-title"><?= htmlspecialchars($article['title']) ?></h3>
                                <div class="article-meta">
                                    <span><?= date('d/m/Y', strtotime($article['published_at'])) ?></span>
                                    <?php if ($article['category_name']): ?>
                                        <span> • <?= htmlspecialchars($article['category_name']) ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>
    </div>
