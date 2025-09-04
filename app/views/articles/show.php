<?php
/**
 * Contenu de la page article - Utilise le template unifié public.php
 * Le header, bannières thématiques et footer sont gérés par le template
 */

// Charger le helper d'images
require_once __DIR__ . '/../../helpers/image_helper.php';

// Nettoyer le contenu HTML
$cleanedContent = ImageHelper::cleanArticleContent($article->getContent());

// Définir les métadonnées de la page
$pageTitle = htmlspecialchars($article->getTitle()) . ' - GameNews Belgium';
$pageDescription = htmlspecialchars($article->getExcerpt() ?? 'Découvrez cet article sur GameNews, votre source gaming belge.');
?>

<!-- Métadonnées de l'article (au-dessus de l'image) -->
<div class="article-meta-section">
    <div class="article-meta-grid">
        <!-- Auteur -->
        <div class="meta-item">
            <span class="meta-icon">👤</span>
            <span class="meta-label">AUTEUR</span>
            <span class="meta-value"><?= htmlspecialchars($article->getAuthorName() ?? 'Auteur') ?></span>
        </div>
        
        <!-- Date de publication -->
        <div class="meta-item">
            <span class="meta-icon">📅</span>
            <span class="meta-label">PUBLIÉ LE</span>
            <span class="meta-value"><?= date('d/m/Y', strtotime($article->getPublishedAt() ?? $article->getCreatedAt())) ?></span>
        </div>
        
        <!-- Statut -->
        <div class="meta-item">
            <span class="meta-icon"><?= $article->getStatus() === 'published' ? '✅' : '📝' ?></span>
            <span class="meta-label">STATUT</span>
            <span class="meta-value"><?= $article->getStatus() === 'published' ? 'Publié' : 'Brouillon' ?></span>
        </div>
        
        <!-- Jeu associé -->
        <?php if ($article->getGameId()): ?>
            <div class="meta-item">
                <span class="meta-icon">🎮</span>
                <span class="meta-label">JEU</span>
                <span class="meta-value"><?= htmlspecialchars($article->getGameName() ?? 'Jeu') ?></span>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Bloc unifié : Image Hero + Extrait -->
<div class="article-hero-unified">
    <!-- Image de couverture en arrière-plan -->
    <div class="article-hero-background" style="background-image: url('<?= $article->getCoverImageUrl() ?: '/public/assets/images/default-article.jpg' ?>');"></div>
    
    <!-- Overlay progressif (commence en bas) -->
    <div class="article-hero-overlay-unified"></div>
    
    <!-- Contenu superposé sur l'image -->
    <div class="article-hero-content">
        <!-- Catégorie de l'article (en haut, à gauche) -->
        <?php if ($article->getCategoryId()): ?>
            <div class="article-hero-category">
                <?= htmlspecialchars($article->getCategoryName() ?? 'Catégorie') ?>
            </div>
        <?php endif; ?>
        
        <!-- Titre de l'article (en bas, à gauche) -->
        <h1 class="article-hero-title"><?= htmlspecialchars($article->getTitle()) ?></h1>
    </div>
    
    <!-- Extrait intégré dans le bloc unifié -->
    <?php if ($article->getExcerpt()): ?>
        <div class="article-excerpt-unified">
            <p><?= htmlspecialchars($article->getExcerpt()) ?></p>
        </div>
    <?php endif; ?>
</div>

<!-- Navigation des chapitres (pour les dossiers) -->
<?php if (isset($isDossier) && $isDossier && !empty($dossierChapters)): ?>
    <div class="dossier-chapters-navigation">
        <div class="chapters-navigation-header">
            <h2 class="chapters-title">📚 Chapitres du dossier</h2>
            <p class="chapters-subtitle">Découvrez tous les chapitres de ce dossier</p>
        </div>
        
        <div class="chapters-grid">
            <?php foreach ($dossierChapters as $index => $chapter): ?>
                <div class="chapter-card">
                    <div class="chapter-number"><?= $index + 1 ?></div>
                    <div class="chapter-content">
                        <h3 class="chapter-title">
                            <a href="/article/<?= htmlspecialchars($article->getSlug()) ?>/<?= htmlspecialchars($chapter['slug']) ?>" 
                               class="chapter-link">
                                <?= htmlspecialchars($chapter['title']) ?>
                            </a>
                        </h3>
                        <div class="chapter-meta">
                            <span class="chapter-status published">Publié</span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="chapters-navigation-actions">
            <a href="/article/<?= htmlspecialchars($article->getSlug()) ?>/<?= htmlspecialchars($dossierChapters[0]['slug']) ?>" 
               class="btn btn-primary">
                Commencer la lecture
            </a>
        </div>
    </div>
<?php endif; ?>

<!-- Contenu de l'article -->
<div class="article-content">
    <?php if ($cleanedContent): ?>
        <!-- Contenu modulaire de l'éditeur nettoyé -->
        <?= $cleanedContent ?>
    <?php else: ?>
        <!-- Article sans contenu -->
        <div class="no-content">
            <p>Aucun contenu disponible pour cet article.</p>
        </div>
    <?php endif; ?>
</div>

<!-- Actions de l'article (pour les admins/éditeurs) -->
<?php if (Auth::hasAnyRole(['admin', 'editor'])): ?>
    <div class="article-actions">
        <a href="/admin/articles/edit/<?= $article->getId() ?>" class="btn btn-primary">Modifier l'article</a>
        <a href="/admin/articles" class="btn btn-secondary">Retour à la liste</a>
    </div>
<?php endif; ?>
