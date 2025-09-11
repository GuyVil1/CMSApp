<?php
/**
 * Vue pour afficher une catégorie spécifique
 * Design adapté du design de référence des pages hardware
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
    <div class="hardware-info">
        <div class="hardware-header">
            <div class="hardware-title-section">
                <h1 class="hardware-name"><?= htmlspecialchars($category->getName()) ?></h1>
                <?php if ($category->getDescription()): ?>
                    <div class="hardware-description"><?= htmlspecialchars($category->getDescription()) ?></div>
                <?php else: ?>
                    <div class="hardware-description">Découvrez tous les articles de la catégorie <?= htmlspecialchars($category->getName()) ?>. Une sélection exclusive de contenu de qualité.</div>
                <?php endif; ?>
            </div>
            
            <div class="hardware-stats">
                <div class="stat-item">
                    <span class="stat-number"><?= count($articles) ?></span>
                    <span class="stat-label">Article<?= count($articles) > 1 ? 's' : '' ?></span>
                </div>
                <div class="stat-item">
                    <span class="stat-number"><?= count(array_unique(array_column($articles, 'author_name'))) ?></span>
                    <span class="stat-label">Auteur<?= count(array_unique(array_column($articles, 'author_name'))) > 1 ? 's' : '' ?></span>
                </div>
                <div class="stat-item">
                    <span class="stat-number"><?= count(array_filter($articles, function($article) { return !empty($article['cover_image']); })) ?></span>
                    <span class="stat-label">Avec images</span>
                </div>
            </div>
        </div>
        
        <!-- Barre de recherche dynamique -->
        <div class="search-section">
            <div class="search-container">
                <input type="text" id="hardware-search" class="search-input" placeholder="Rechercher des articles dans <?= htmlspecialchars($category->getName()) ?>...">
                <div class="search-icon">🔍</div>
            </div>
            <div id="search-results" class="search-results"></div>
        </div>
    </div>
</section>

<!-- Section Articles -->
<?php if (!empty($articles)): ?>
    <section class="section">
        <div class="section-header">
            <div class="section-line yellow"></div>
            <h2 class="section-title">📰 Articles <?= htmlspecialchars($category->getName()) ?></h2>
            <div class="section-line red"></div>
        </div>
        
        <div class="articles-layout">
            <div class="articles-main">
                <?php foreach ($articles as $index => $article): ?>
                    <?php if ($index < 6): // 6 premiers articles en grand format ?>
                        <div class="article-card-large" data-title="<?= htmlspecialchars(strtolower($article['title'])) ?>" data-excerpt="<?= htmlspecialchars(strtolower($article['excerpt'] ?? '')) ?>">
                            <a href="/article/<?= htmlspecialchars($article['slug']) ?>" style="text-decoration: none; color: inherit;">
                                <div class="article-image">
                                    <?php if (!empty($article['cover_image'])): ?>
                                        <img src="/uploads.php?file=<?= urlencode($article['cover_image']) ?>" 
                                             alt="<?= htmlspecialchars($article['title']) ?>" 
                                             loading="lazy">
                                    <?php else: ?>
                                        <div style="display: flex; align-items: center; justify-content: center; height: 100%; background: #f0f0f0; color: #999; font-size: 2rem;">
                                            📰
                                        </div>
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
                            </a>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
            
            <?php if (count($articles) > 6): ?>
                <!-- Articles supplémentaires en format plus petit -->
                <div class="articles-secondary">
                    <h3 class="secondary-title">Autres articles <?= htmlspecialchars($category->getName()) ?></h3>
                    <div class="articles-grid">
                        <?php foreach (array_slice($articles, 6) as $article): ?>
                            <div class="article-card-small" data-title="<?= htmlspecialchars(strtolower($article['title'])) ?>" data-excerpt="<?= htmlspecialchars(strtolower($article['excerpt'] ?? '')) ?>">
                                <a href="/article/<?= htmlspecialchars($article['slug']) ?>" style="text-decoration: none; color: inherit;">
                                    <div class="article-image">
                                        <?php if (!empty($article['cover_image'])): ?>
                                            <img src="/uploads.php?file=<?= urlencode($article['cover_image']) ?>" 
                                                 alt="<?= htmlspecialchars($article['title']) ?>" 
                                                 loading="lazy">
                                        <?php else: ?>
                                            <div style="display: flex; align-items: center; justify-content: center; height: 100%; background: #f0f0f0; color: #999; font-size: 1.5rem;">
                                                📰
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="article-content">
                                        <h3 class="article-title"><?= htmlspecialchars($article['title']) ?></h3>
                                        <span class="article-date">
                                            <?= date('d/m/Y', strtotime($article['published_at'])) ?>
                                        </span>
                                    </div>
                                </a>
                            </div>
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
/* Styles identiques aux pages hardware pour cohérence parfaite */

.hardware-info {
    background: #000000;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    border: 2px solid #DC2626;
}

.hardware-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.hardware-title-section {
    flex: 1;
}

.hardware-name {
    font-size: 24px;
    font-weight: 700;
    color: #FFFFFF;
    margin: 0 0 0.5rem 0;
    line-height: 1.2;
}

.hardware-description {
    font-size: 16px;
    color: #FCD34D;
    font-weight: 600;
}

.hardware-stats {
    display: flex;
    gap: 1rem;
}

.stat-item {
    background-color: #DC2626;
    color: #FCD34D;
    padding: 0.75rem 1rem;
    border-radius: 8px;
    text-align: center;
    min-width: 80px;
}

.stat-number {
    display: block;
    font-size: 18px;
    font-weight: 700;
    margin-bottom: 0.25rem;
}

.stat-label {
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Barre de recherche */
.search-section {
    margin-top: 1rem;
}

.search-container {
    position: relative;
    max-width: 500px;
}

.search-input {
    width: 100%;
    padding: 12px 40px 12px 16px;
    border: 2px solid #DC2626;
    border-radius: 25px;
    background-color: #FFFFFF;
    color: #1F2937;
    font-size: 14px;
    transition: all 0.3s ease;
}

.search-input:focus {
    outline: none;
    border-color: #FCD34D;
    box-shadow: 0 0 0 3px rgba(252, 211, 77, 0.1);
}

.search-icon {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #DC2626;
    font-size: 16px;
    pointer-events: none;
}

.search-results {
    margin-top: 1rem;
    max-height: 300px;
    overflow-y: auto;
    background-color: #FFFFFF;
    border-radius: 8px;
    border: 1px solid #E5E7EB;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    display: none;
}

.search-results.show {
    display: block;
}

.search-result-item {
    padding: 12px 16px;
    border-bottom: 1px solid #F3F4F6;
    cursor: pointer;
    transition: background-color 0.2s ease;
}

.search-result-item:hover {
    background-color: #F9FAFB;
}

.search-result-item:last-child {
    border-bottom: none;
}

.search-result-title {
    font-weight: 600;
    color: #1F2937;
    margin-bottom: 4px;
}

.search-result-excerpt {
    font-size: 12px;
    color: #6B7280;
    line-height: 1.4;
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

/* Couleurs par catégorie */
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

.article-badge.category-demos {
    background: linear-gradient(135deg, #0891B2 0%, #0284C7 100%);
}

.article-badge.category-guides {
    background: linear-gradient(135deg, #CA8A04 0%, #D97706 100%);
}

.article-badge.category-sponso {
    background: linear-gradient(135deg, #9333EA 0%, #7C3AED 100%);
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
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.article-card-small:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.article-card-small .article-image {
    height: 150px;
    overflow: hidden;
}

.article-card-small .article-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.article-card-small:hover .article-image img {
    transform: scale(1.05);
}

.article-card-small .article-content {
    padding: 1rem;
}

.article-card-small .article-title {
    font-size: 14px;
    font-weight: 600;
    color: #1F2937;
    margin-bottom: 0.5rem;
    line-height: 1.3;
}

.article-card-small .article-date {
    font-size: 11px;
    color: #6B7280;
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

/* Responsive */
@media (max-width: 768px) {
    .hardware-info {
        padding: 1.5rem;
    }
    
    .hardware-stats {
        flex-direction: column;
        gap: 1rem;
        align-items: center;
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

<script>
// Recherche dynamique des articles (identique aux pages hardware)
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('hardware-search');
    const searchResults = document.getElementById('search-results');
    const categoryName = '<?= addslashes($category->getName()) ?>';
    
    let searchTimeout;
    
    searchInput.addEventListener('input', function() {
        const query = this.value.trim();
        
        // Clear previous timeout
        clearTimeout(searchTimeout);
        
        if (query.length < 2) {
            searchResults.classList.remove('show');
            return;
        }
        
        // Debounce search
        searchTimeout = setTimeout(() => {
            searchArticles(query);
        }, 300);
    });
    
    function searchArticles(query) {
        // Simuler une recherche (en réalité, ce serait une requête AJAX)
        const articles = <?= json_encode($articles) ?>;
        const filteredArticles = articles.filter(article => 
            article.title.toLowerCase().includes(query.toLowerCase()) ||
            (article.excerpt && article.excerpt.toLowerCase().includes(query.toLowerCase()))
        );
        
        displaySearchResults(filteredArticles, query);
    }
    
    function displaySearchResults(articles, query) {
        if (articles.length === 0) {
            searchResults.innerHTML = '<div class="search-result-item">Aucun article trouvé pour "' + query + '"</div>';
        } else {
            searchResults.innerHTML = articles.map(article => `
                <div class="search-result-item" onclick="window.location.href='/article/${article.slug}'">
                    <div class="search-result-title">${highlightText(article.title, query)}</div>
                    <div class="search-result-excerpt">${highlightText(article.excerpt || '', query)}</div>
                </div>
            `).join('');
        }
        
        searchResults.classList.add('show');
    }
    
    function highlightText(text, query) {
        if (!text) return '';
        const regex = new RegExp(`(${query})`, 'gi');
        return text.replace(regex, '<strong style="color: #DC2626;">$1</strong>');
    }
    
    // Fermer les résultats en cliquant ailleurs
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
            searchResults.classList.remove('show');
        }
    });
});
</script>