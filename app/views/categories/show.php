<?php
/**
 * Vue pour afficher une catégorie spécifique
 * Affiche tous les articles de cette catégorie
 */
?>

<!-- Section Catégorie -->
<section class="section">
    <div class="section-header">
        <div class="section-line yellow"></div>
        <h2 class="section-title"><?= htmlspecialchars($category->getName()) ?></h2>
        <div class="section-line red"></div>
    </div>

    <!-- Informations de la catégorie -->
    <div class="category-info">
        <div class="category-details">
            <div class="category-badge category-<?= $category->getSlug() ?>">
                <?= htmlspecialchars($category->getName()) ?>
            </div>
            
            <?php if ($category->getDescription()): ?>
                <div class="category-description">
                    <p><?= htmlspecialchars($category->getDescription()) ?></p>
                </div>
            <?php endif; ?>
            
            <div class="category-stats">
                <span class="articles-count"><?= count($articles) ?> article<?= count($articles) > 1 ? 's' : '' ?></span>
            </div>
        </div>
    </div>
</section>

<!-- Section Articles -->
<?php if (!empty($articles)): ?>
<section class="section">
    <div class="section-header">
        <div class="section-line red"></div>
        <h2 class="section-title">Articles <?= htmlspecialchars($category->getName()) ?></h2>
        <div class="section-line yellow"></div>
    </div>

    <div class="articles-layout">
        <!-- Articles principaux -->
        <div class="articles-main">
            <?php foreach ($articles as $index => $article): ?>
                <?php if ($index < 6): // 6 premiers articles en grand format ?>
                    <div class="article-card-large" onclick="window.location.href='/article/<?= $article['slug'] ?>'" style="cursor: pointer;">
                        <div class="article-image">
                            <?php if ($article['cover_image']): ?>
                                <img src="/image.php?file=<?= urlencode($article['cover_image']) ?>" 
                                     alt="<?= htmlspecialchars($article['title']) ?>">
                            <?php else: ?>
                                <img src="/assets/images/default-article.jpg" 
                                     alt="<?= htmlspecialchars($article['title']) ?>">
                            <?php endif; ?>
                        </div>
                        <div class="article-content">
                            <div class="article-header">
                                <span class="article-badge category-<?= $article['category_slug'] ?>">
                                    <?= htmlspecialchars($article['category_name']) ?>
                                </span>
                                <span class="article-date">
                                    <?= date('d/m/Y', strtotime($article['published_at'])) ?>
                                </span>
                            </div>
                            <h3 class="article-title"><?= htmlspecialchars($article['title']) ?></h3>
                            <?php if ($article['excerpt']): ?>
                                <p class="article-excerpt"><?= htmlspecialchars($article['excerpt']) ?></p>
                            <?php endif; ?>
                            <div class="article-meta">
                                <span class="article-author">Par <?= htmlspecialchars($article['author_name']) ?></span>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>

        <!-- Articles secondaires -->
        <?php if (count($articles) > 6): ?>
            <div class="articles-secondary">
                <h3 class="secondary-title">Autres articles</h3>
                <div class="articles-grid">
                    <?php foreach ($articles as $index => $article): ?>
                        <?php if ($index >= 6): // Articles restants en petit format ?>
                            <div class="article-card-small" onclick="window.location.href='/article/<?= $article['slug'] ?>'" style="cursor: pointer;">
                                <div class="article-image">
                                    <?php if ($article['cover_image']): ?>
                                        <img src="/image.php?file=<?= urlencode($article['cover_image']) ?>" 
                                             alt="<?= htmlspecialchars($article['title']) ?>">
                                    <?php else: ?>
                                        <img src="/assets/images/default-article.jpg" 
                                             alt="<?= htmlspecialchars($article['title']) ?>">
                                    <?php endif; ?>
                                </div>
                                <div class="article-content">
                                    <h4 class="article-title"><?= htmlspecialchars($article['title']) ?></h4>
                                    <span class="article-date">
                                        <?= date('d/m/Y', strtotime($article['published_at'])) ?>
                                    </span>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php else: ?>
<!-- Message si aucun article -->
<section class="section">
    <div class="empty-state">
        <div class="empty-icon">📰</div>
        <h3>Aucun article disponible</h3>
        <p>Il n'y a pas encore d'articles dans la catégorie <?= htmlspecialchars($category->getName()) ?>.</p>
        <p>Revenez bientôt pour découvrir du nouveau contenu !</p>
    </div>
</section>
<?php endif; ?>

<style>
/* Styles spécifiques pour la page catégorie */
.category-info {
    background: linear-gradient(135deg, #1F2937 0%, #374151 100%);
    border-radius: 12px;
    padding: 2rem;
    margin-bottom: 2rem;
    border: 2px solid #DC2626;
}

.category-details {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    align-items: center;
    text-align: center;
}

.category-badge {
    display: inline-block;
    padding: 12px 24px;
    border-radius: 25px;
    font-size: 16px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: #FFFFFF;
    margin-bottom: 1rem;
}

/* Couleurs par catégorie */
.category-badge.category-actu {
    background: linear-gradient(135deg, #DC2626 0%, #B91C1C 100%);
}

.category-badge.category-test {
    background: linear-gradient(135deg, #059669 0%, #047857 100%);
}

.category-badge.category-dossiers {
    background: linear-gradient(135deg, #7C3AED 0%, #6D28D9 100%);
}

.category-badge.category-trailers {
    background: linear-gradient(135deg, #EA580C 0%, #DC2626 100%);
}

/* Couleurs pour les badges d'articles */
.article-badge.category-actu {
    background: linear-gradient(135deg, #DC2626 0%, #B91C1C 100%);
}

.article-badge.category-test {
    background: linear-gradient(135deg, #059669 0%, #047857 100%);
}

.article-badge.category-dossiers {
    background: linear-gradient(135deg, #7C3AED 0%, #6D28D9 100%);
}

.article-badge.category-trailers {
    background: linear-gradient(135deg, #EA580C 0%, #DC2626 100%);
}

.category-description {
    color: #E5E7EB;
    line-height: 1.6;
    max-width: 600px;
}

.category-stats {
    margin-top: 1rem;
}

.articles-count {
    background-color: #DC2626;
    color: #FCD34D;
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 14px;
    font-weight: 600;
}

.articles-layout {
    display: flex;
    flex-direction: column;
    gap: 3rem;
}

.articles-main {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 1.5rem;
}

.article-card-large {
    background: #FFFFFF;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid rgba(0, 0, 0, 0.05);
    position: relative;
    width: 100%;
    max-width: 100%;
    box-sizing: border-box;
}

.article-card-large:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    border-color: rgba(220, 38, 38, 0.2);
}

.article-card-large .article-image {
    height: 200px;
    overflow: hidden;
    position: relative;
    background-color: #F3F4F6;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    max-width: 100%;
}

.article-card-large .article-image::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(180deg, transparent 0%, rgba(0, 0, 0, 0.1) 100%);
    z-index: 1;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.article-card-large:hover .article-image::before {
    opacity: 1;
}

.article-card-large .article-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center;
    transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    background-color: #F3F4F6;
    display: block;
    max-width: 100%;
    max-height: 100%;
}

.article-card-large:hover .article-image img {
    transform: scale(1.08);
}

.article-card-large .article-content {
    padding: 1.5rem;
    position: relative;
}

.article-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.article-badge {
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    color: #FFFFFF;
    letter-spacing: 0.5px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    transition: all 0.3s ease;
}

.article-badge:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

.article-date {
    font-size: 12px;
    color: #9CA3AF;
    background-color: #F3F4F6;
    padding: 4px 12px;
    border-radius: 12px;
    font-weight: 500;
}

.article-title {
    font-size: 16px;
    font-weight: 700;
    color: #1F2937;
    margin-bottom: 0.75rem;
    line-height: 1.3;
    transition: color 0.3s ease;
}

.article-card-large:hover .article-title {
    color: #DC2626;
}

.article-excerpt {
    font-size: 13px;
    color: #6B7280;
    line-height: 1.5;
    margin-bottom: 1rem;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.article-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 12px;
    color: #9CA3AF;
}

.articles-secondary {
    background: #F9FAFB;
    border-radius: 12px;
    padding: 2rem;
    border: 2px solid #E5E7EB;
}

.secondary-title {
    color: #1F2937;
    font-size: 20px;
    font-weight: 600;
    margin-bottom: 1.5rem;
    text-align: center;
}

.articles-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 1.5rem;
}

.article-card-small {
    background: #FFFFFF;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.06);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid rgba(0, 0, 0, 0.04);
}

.article-card-small:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.12);
    border-color: rgba(220, 38, 38, 0.15);
}

.article-card-small .article-image {
    height: 160px;
    overflow: hidden;
    position: relative;
}

.article-card-small .article-image::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(180deg, transparent 0%, rgba(0, 0, 0, 0.05) 100%);
    z-index: 1;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.article-card-small:hover .article-image::before {
    opacity: 1;
}

.article-card-small .article-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.article-card-small:hover .article-image img {
    transform: scale(1.06);
}

.article-card-small .article-content {
    padding: 1.25rem;
}

.article-card-small .article-title {
    font-size: 15px;
    font-weight: 700;
    color: #1F2937;
    margin-bottom: 0.75rem;
    line-height: 1.3;
    transition: color 0.3s ease;
}

.article-card-small:hover .article-title {
    color: #DC2626;
}

.article-card-small .article-date {
    font-size: 11px;
    color: #9CA3AF;
    background-color: #F9FAFB;
    padding: 3px 8px;
    border-radius: 8px;
    font-weight: 500;
    display: inline-block;
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    background: #F9FAFB;
    border-radius: 12px;
    border: 2px dashed #D1D5DB;
}

.empty-icon {
    font-size: 4rem;
    margin-bottom: 1rem;
}

.empty-state h3 {
    color: #1F2937;
    margin-bottom: 1rem;
}

.empty-state p {
    color: #6B7280;
    margin-bottom: 0.5rem;
}

/* Contraintes globales pour les cartes */
.article-card-large * {
    max-width: 100%;
    box-sizing: border-box;
}

/* Responsive */
@media (max-width: 768px) {
    .category-info {
        padding: 1.5rem;
    }
    
    .articles-main {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    
    .article-card-large .article-content {
        padding: 1.5rem;
    }
    
    .articles-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .articles-secondary {
        padding: 1.5rem;
    }
}
</style>
