<?php
/**
 * Contenu de la page chapitre - Utilise le template unifié public.php
 * Le header, bannières thématiques et footer sont gérés par le template
 */

// Charger le helper d'images
require_once __DIR__ . '/../../helpers/image_helper.php';

// Nettoyer le contenu HTML du chapitre
$cleanedContent = ImageHelper::cleanArticleContent($chapter['content']);
?>

<!-- Métadonnées du chapitre (au-dessus de l'image) -->
<div class="article-meta-section">
    <div class="article-meta-grid">
        <!-- Retour au dossier -->
        <div class="meta-item">
            <span class="meta-icon">📚</span>
            <span class="meta-label">DOSSIER</span>
            <span class="meta-value">
                <a href="/article/<?= htmlspecialchars($dossier->getSlug()) ?>" class="dossier-link">
                    <?= htmlspecialchars($dossier->getTitle()) ?>
                </a>
            </span>
        </div>
        
        <!-- Numéro du chapitre -->
        <div class="meta-item">
            <span class="meta-icon">📖</span>
            <span class="meta-label">CHAPITRE</span>
            <span class="meta-value"><?= $currentChapterIndex + 1 ?> sur <?= count($allChapters) ?></span>
        </div>
        
        <!-- Date de publication -->
        <div class="meta-item">
            <span class="meta-icon">📅</span>
            <span class="meta-label">PUBLIÉ LE</span>
            <span class="meta-value"><?= date('d/m/Y', strtotime($chapter['updated_at'] ?? $chapter['created_at'])) ?></span>
        </div>
        
        <!-- Statut -->
        <div class="meta-item">
            <span class="meta-icon">✅</span>
            <span class="meta-label">STATUT</span>
            <span class="meta-value">Publié</span>
        </div>
    </div>
</div>

<!-- Bloc unifié : Image Hero + Titre du chapitre -->
<div class="article-hero-unified">
    <!-- Image de couverture en arrière-plan -->
    <div class="article-hero-background" style="background-image: url('<?= $dossier->getCoverImageUrl() ?: '/public/assets/images/default-article.jpg' ?>');"></div>
    
    <!-- Overlay progressif (commence en bas) -->
    <div class="article-hero-overlay-unified"></div>
    
    <!-- Contenu superposé sur l'image -->
    <div class="article-hero-content">
        <!-- Retour au dossier (en haut, à gauche) -->
        <div class="article-hero-category">
            <a href="/article/<?= htmlspecialchars($dossier->getSlug()) ?>" class="dossier-back-link">
                ← Retour au dossier
            </a>
        </div>
        
        <!-- Titre du chapitre (en bas, à gauche) -->
        <h1 class="article-hero-title"><?= htmlspecialchars($chapter['title']) ?></h1>
    </div>
</div>

<!-- Navigation séquentielle des chapitres -->
<div class="chapter-sequential-navigation">
    <div class="navigation-controls">
        <?php if ($previousChapter): ?>
            <a href="/article/<?= htmlspecialchars($dossier->getSlug()) ?>/<?= htmlspecialchars($previousChapter['slug']) ?>" 
               class="nav-btn nav-prev">
                <span class="nav-icon">←</span>
                <span class="nav-text">
                    <span class="nav-label">Chapitre précédent</span>
                    <span class="nav-title"><?= htmlspecialchars($previousChapter['title']) ?></span>
                </span>
            </a>
        <?php else: ?>
            <div class="nav-btn nav-prev disabled">
                <span class="nav-icon">←</span>
                <span class="nav-text">
                    <span class="nav-label">Chapitre précédent</span>
                    <span class="nav-title">Début du dossier</span>
                </span>
            </div>
        <?php endif; ?>
        
        <?php if ($nextChapter): ?>
            <a href="/article/<?= htmlspecialchars($dossier->getSlug()) ?>/<?= htmlspecialchars($nextChapter['slug']) ?>" 
               class="nav-btn nav-next">
                <span class="nav-text">
                    <span class="nav-label">Chapitre suivant</span>
                    <span class="nav-title"><?= htmlspecialchars($nextChapter['title']) ?></span>
                </span>
                <span class="nav-icon">→</span>
            </a>
        <?php else: ?>
            <div class="nav-btn nav-next disabled">
                <span class="nav-text">
                    <span class="nav-label">Chapitre suivant</span>
                    <span class="nav-title">Fin du dossier</span>
                </span>
                <span class="nav-icon">→</span>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Bouton retour au dossier -->
    <div class="navigation-actions">
        <a href="/article/<?= htmlspecialchars($dossier->getSlug()) ?>" class="btn btn-secondary">
            📚 Retour au dossier
        </a>
    </div>
</div>

<!-- Contenu du chapitre -->
<div class="article-content">
    <?php if ($cleanedContent): ?>
        <!-- Contenu modulaire de l'éditeur nettoyé -->
        <?= $cleanedContent ?>
    <?php else: ?>
        <!-- Chapitre sans contenu -->
        <div class="no-content">
            <p>Aucun contenu disponible pour ce chapitre.</p>
        </div>
    <?php endif; ?>
</div>

<!-- Navigation des chapitres en fin de page -->
<div class="dossier-chapters-navigation-bottom">
    <div class="chapters-navigation-header">
        <h2 class="chapters-title">📚 Navigation des chapitres</h2>
    </div>
    
    <div class="chapters-navigation-controls">
        <div class="chapters-list">
            <?php foreach ($allChapters as $index => $ch): ?>
                <div class="chapter-item <?= $ch['id'] == $chapter['id'] ? 'current-chapter' : '' ?>">
                    <span class="chapter-number"><?= $index + 1 ?></span>
                    <a href="/article/<?= htmlspecialchars($dossier->getSlug()) ?>/<?= htmlspecialchars($ch['slug']) ?>" 
                       class="chapter-link">
                        <?= htmlspecialchars($ch['title']) ?>
                    </a>
                    <?php if ($ch['id'] == $chapter['id']): ?>
                        <span class="current-indicator">← Vous êtes ici</span>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <!-- Navigation séquentielle en bas -->
    <div class="bottom-navigation-controls">
        <?php if ($previousChapter): ?>
            <a href="/article/<?= htmlspecialchars($dossier->getSlug()) ?>/<?= htmlspecialchars($previousChapter['slug']) ?>" 
               class="btn btn-secondary">
                ← Chapitre précédent
            </a>
        <?php endif; ?>
        
        <a href="/article/<?= htmlspecialchars($dossier->getSlug()) ?>" class="btn btn-primary">
            📚 Retour au dossier
        </a>
        
        <?php if ($nextChapter): ?>
            <a href="/article/<?= htmlspecialchars($dossier->getSlug()) ?>/<?= htmlspecialchars($nextChapter['slug']) ?>" 
               class="btn btn-secondary">
                Chapitre suivant →
            </a>
        <?php endif; ?>
    </div>
</div>

<!-- Actions de l'article (pour les admins/éditeurs) -->
<?php if (Auth::hasAnyRole(['admin', 'editor'])): ?>
    <div class="article-actions">
        <a href="/admin/articles/edit/<?= $dossier->getId() ?>" class="btn btn-primary">Modifier le dossier</a>
        <a href="/admin/articles" class="btn btn-secondary">Retour à la liste</a>
    </div>
<?php endif; ?>
