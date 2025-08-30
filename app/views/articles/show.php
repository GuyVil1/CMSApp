<?php
/**
 * Vue d'affichage d'un article - Belgium Vidéo Gaming
 */
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($article->getTitle()) ?> - Belgium Vidéo Gaming</title>
    <meta name="description" content="<?= htmlspecialchars($article->getExcerpt() ?? '') ?>">
    
    <!-- Thème actuel -->
    <link rel="stylesheet" href="/themes/<?= htmlspecialchars($currentTheme['name']) ?>/style.css">
    
    <!-- Styles de base -->
    <link rel="stylesheet" href="/public/assets/css/main.css">
    
    <!-- Styles spécifiques aux articles -->
    <link rel="stylesheet" href="/public/assets/css/components/article-display.css">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/public/favicon.ico">
</head>
<body class="theme-<?= htmlspecialchars($currentTheme['name']) ?>">
    
    <!-- Header avec navigation -->
    <header class="site-header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <a href="/">
                        <img src="/themes/<?= htmlspecialchars($currentTheme['name']) ?>/left.png" alt="Belgium Vidéo Gaming">
                    </a>
                </div>
                
                <nav class="main-nav">
                    <ul>
                        <li><a href="/">Accueil</a></li>
                        <li><a href="/articles">Articles</a></li>
                        <li><a href="/games">Jeux</a></li>
                        <li><a href="/hardware">Hardware</a></li>
                        <?php if ($isLoggedIn): ?>
                            <li><a href="/admin">Administration</a></li>
                            <li><a href="/logout">Déconnexion</a></li>
                        <?php else: ?>
                            <li><a href="/login">Connexion</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </div>
    </header>
    
    <!-- Contenu principal -->
    <main class="main-content">
        <div class="container">
            <div class="article-layout">
                
                <!-- Article principal -->
                <article class="article-main">
                    
                    <!-- En-tête de l'article -->
                    <header class="article-header">
                        <?php if ($article->getCoverImageId()): ?>
                            <div class="article-cover">
                                <img src="/public/uploads/<?= htmlspecialchars($article->getCoverImage()['filename'] ?? '') ?>" 
                                     alt="<?= htmlspecialchars($article->getTitle()) ?>">
                            </div>
                        <?php endif; ?>
                        
                        <div class="article-meta">
                            <h1 class="article-title"><?= htmlspecialchars($article->getTitle()) ?></h1>
                            
                            <?php if ($article->getExcerpt()): ?>
                                <div class="article-excerpt">
                                    <?= htmlspecialchars($article->getExcerpt()) ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="article-info">
                                <span class="author">
                                    Par <strong><?= htmlspecialchars($article->getAuthor()['login'] ?? '') ?></strong>
                                </span>
                                
                                <?php if ($article->getCategoryId()): ?>
                                    <span class="category">
                                        dans <a href="/category/<?= htmlspecialchars($article->getCategory()['slug'] ?? '') ?>"
                                               style="color: <?= htmlspecialchars($article->getCategory()['color'] ?? '#007bff') ?>">
                                            <?= htmlspecialchars($article->getCategory()['name'] ?? '') ?>
                                        </a>
                                    </span>
                                <?php endif; ?>
                                
                                <?php if ($article->getGameId()): ?>
                                    <span class="game">
                                        sur <a href="/game/<?= htmlspecialchars($article->getGame()['slug'] ?? '') ?>">
                                            <?= htmlspecialchars($article->getGame()['title'] ?? '') ?>
                                        </a>
                                    </span>
                                <?php endif; ?>
                                
                                <span class="date">
                                    le <?= date('d/m/Y', strtotime($article->getPublishedAt() ?? $article->getCreatedAt())) ?>
                                </span>
                            </div>
                            
                            <?php if (!empty($article->getTags())): ?>
                                <div class="article-tags">
                                    <?php foreach ($article->getTags() as $tag): ?>
                                        <span class="tag"><?= htmlspecialchars($tag['name']) ?></span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </header>
                    
                    <!-- Contenu de l'article (modules) -->
                    <div class="article-content" id="articleContent">
                        <!-- Le contenu sera rendu par JavaScript -->
                        <div class="loading">Chargement de l'article...</div>
                    </div>
                    
                </article>
                
                <!-- Sidebar -->
                <aside class="article-sidebar">
                    
                    <!-- Articles liés -->
                    <?php if (!empty($relatedArticles)): ?>
                        <section class="sidebar-section">
                            <h3>Articles liés</h3>
                            <div class="related-articles">
                                <?php foreach ($relatedArticles as $related): ?>
                                    <article class="related-article">
                                        <?php if ($related['cover_image']): ?>
                                            <div class="related-cover">
                                                <a href="/article/<?= htmlspecialchars($related['id']) ?>">
                                                    <img src="/public/uploads/<?= htmlspecialchars($related['cover_image']) ?>" 
                                                         alt="<?= htmlspecialchars($related['title']) ?>">
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                        <div class="related-content">
                                            <h4>
                                                <a href="/article/<?= htmlspecialchars($related['id']) ?>">
                                                    <?= htmlspecialchars($related['title']) ?>
                                                </a>
                                            </h4>
                                            <div class="related-meta">
                                                <span class="category" style="color: <?= htmlspecialchars($related['category_color'] ?? '#007bff') ?>">
                                                    <?= htmlspecialchars($related['category_name'] ?? '') ?>
                                                </span>
                                                <span class="date">
                                                    <?= date('d/m/Y', strtotime($related['published_at'])) ?>
                                                </span>
                                            </div>
                                        </div>
                                    </article>
                                <?php endforeach; ?>
                            </div>
                        </section>
                    <?php endif; ?>
                    
                    <!-- Articles populaires -->
                    <?php if (!empty($popularArticles)): ?>
                        <section class="sidebar-section">
                            <h3>Articles populaires</h3>
                            <div class="popular-articles">
                                <?php foreach ($popularArticles as $popular): ?>
                                    <article class="popular-article">
                                        <?php if ($popular['cover_image']): ?>
                                            <div class="popular-cover">
                                                <a href="/article/<?= htmlspecialchars($popular['id']) ?>">
                                                    <img src="/public/uploads/<?= htmlspecialchars($popular['cover_image']) ?>" 
                                                         alt="<?= htmlspecialchars($popular['title']) ?>">
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                        <div class="popular-content">
                                            <h4>
                                                <a href="/article/<?= htmlspecialchars($popular['id']) ?>">
                                                    <?= htmlspecialchars($popular['title']) ?>
                                                </a>
                                            </h4>
                                            <div class="popular-meta">
                                                <span class="category" style="color: <?= htmlspecialchars($popular['category_color'] ?? '#007bff') ?>">
                                                    <?= htmlspecialchars($popular['category_name'] ?? '') ?>
                                                </span>
                                                <span class="date">
                                                    <?= date('d/m/Y', strtotime($popular['published_at'])) ?>
                                                </span>
                                            </div>
                                        </div>
                                    </article>
                                <?php endforeach; ?>
                            </div>
                        </section>
                    <?php endif; ?>
                    
                </aside>
                
            </div>
        </div>
    </main>
    
    <!-- Footer -->
    <footer class="site-footer">
        <div class="container">
            <div class="footer-content">
                <p>&copy; <?= date('Y') ?> Belgium Vidéo Gaming. Tous droits réservés.</p>
            </div>
        </div>
    </footer>
    
    <!-- Scripts -->
    <script>
        // Données de l'article pour le rendu des modules
        const articleData = {
            content: <?= json_encode($article->getContent()) ?>,
            id: <?= $article->getId() ?>,
            slug: '<?= htmlspecialchars($article->getSlug()) ?>'
        };
    </script>
    
    <!-- Script de rendu des modules -->
    <script src="/public/js/article-renderer.js"></script>
    
</body>
</html>
