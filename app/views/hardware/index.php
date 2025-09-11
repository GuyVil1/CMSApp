<?php
/**
 * Vue pour afficher la liste de tous les hardwares
 * Page publique de listing des matériels
 */
?>

<!-- Section Hardware -->
<section class="section">
    <div class="section-header">
        <div class="section-line yellow"></div>
        <h2 class="section-title">Hardware Gaming</h2>
        <div class="section-line red"></div>
    </div>

    <!-- Description -->
    <div class="hardware-intro">
        <p>Découvrez tous les matériels de gaming disponibles sur notre site : consoles, PC, accessoires et plus encore.</p>
    </div>
</section>

<!-- Section Liste des Hardwares -->
<?php if (!empty($hardwares)): ?>
<section class="section">
    <div class="section-header">
        <div class="section-line red"></div>
        <h2 class="section-title">Nos Matériels</h2>
        <div class="section-line yellow"></div>
    </div>

    <div class="hardware-grid">
        <?php foreach ($hardwares as $hardware): ?>
            <div class="hardware-card" onclick="window.location.href='/hardwares/<?= $hardware->getSlug() ?>'" style="cursor: pointer;">
                <!-- Image du hardware -->
                <?php if ($hardware->getImage()): ?>
                    <div class="hardware-image">
                        <img src="/uploads.php?file=hardware/<?= htmlspecialchars($hardware->getImage()) ?>" 
                             alt="<?= htmlspecialchars($hardware->getFullName()) ?>" 
                             loading="lazy">
                    </div>
                <?php else: ?>
                    <div class="hardware-image-placeholder">
                        <div class="placeholder-icon">🖥️</div>
                        <span class="placeholder-text"><?= htmlspecialchars($hardware->getFullName()) ?></span>
                    </div>
                <?php endif; ?>
                
                <div class="hardware-header">
                    <div class="hardware-type-badge type-<?= $hardware->getType() ?>">
                        <?= htmlspecialchars($hardware->getTypeName()) ?>
                    </div>
                    <?php if ($hardware->getReleaseYear()): ?>
                        <div class="hardware-year">
                            <?= $hardware->getReleaseYear() ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="hardware-content">
                    <h3 class="hardware-name"><?= htmlspecialchars($hardware->getFullName()) ?></h3>
                    
                    <?php if ($hardware->getDescription()): ?>
                        <p class="hardware-description"><?= htmlspecialchars($hardware->getDescription()) ?></p>
                    <?php endif; ?>
                    
                    <div class="hardware-stats">
                        <div class="stat-item">
                            <span class="stat-icon">🎮</span>
                            <span class="stat-text"><?= $hardware->getGamesCount() ?> jeu<?= $hardware->getGamesCount() > 1 ? 'x' : '' ?></span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-icon">📰</span>
                            <span class="stat-text"><?= $hardware->getArticlesCount() ?> article<?= $hardware->getArticlesCount() > 1 ? 's' : '' ?></span>
                        </div>
                    </div>
                </div>
                
                <div class="hardware-footer">
                    <span class="view-more">Voir les détails →</span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>
<?php else: ?>
<!-- Message si aucun hardware -->
<section class="section">
    <div class="empty-state">
        <div class="empty-icon">🎮</div>
        <h3>Aucun hardware disponible</h3>
        <p>Il n'y a pas encore de matériel de gaming disponible.</p>
        <p>Revenez bientôt pour découvrir du nouveau contenu !</p>
    </div>
</section>
<?php endif; ?>

<style>
/* Styles spécifiques pour la page hardware */
.hardware-intro {
    background: linear-gradient(135deg, #1F2937 0%, #374151 100%);
    border-radius: 12px;
    padding: 2rem;
    margin-bottom: 2rem;
    border: 2px solid #DC2626;
    text-align: center;
}

.hardware-intro p {
    color: #E5E7EB;
    font-size: 16px;
    line-height: 1.6;
    margin: 0;
}

.hardware-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 2rem;
    margin-top: 2rem;
}

.hardware-card {
    background: #FFFFFF;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.hardware-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    border-color: #DC2626;
}

.hardware-image {
    width: 100%;
    height: 200px;
    overflow: hidden;
    background: linear-gradient(135deg, #F3F4F6 0%, #E5E7EB 100%);
    position: relative;
}

.hardware-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.hardware-card:hover .hardware-image img {
    transform: scale(1.05);
}

.hardware-image-placeholder {
    width: 100%;
    height: 200px;
    background: linear-gradient(135deg, #F3F4F6 0%, #E5E7EB 100%);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #6B7280;
    border-bottom: 1px solid #E5E7EB;
}

.placeholder-icon {
    font-size: 3rem;
    margin-bottom: 0.5rem;
    opacity: 0.7;
}

.placeholder-text {
    font-size: 14px;
    font-weight: 600;
    text-align: center;
    padding: 0 1rem;
    line-height: 1.3;
}

.hardware-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem 1.5rem 0 1.5rem;
}

.hardware-type-badge {
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.hardware-type-badge.type-console {
    background-color: #3B82F6;
    color: #FFFFFF;
}

.hardware-type-badge.type-pc {
    background-color: #10B981;
    color: #FFFFFF;
}

.hardware-type-badge.type-other {
    background-color: #8B5CF6;
    color: #FFFFFF;
}

.hardware-year {
    background-color: #F3F4F6;
    color: #6B7280;
    padding: 6px 12px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
}

.hardware-content {
    padding: 1.5rem;
}

.hardware-name {
    font-size: 20px;
    font-weight: 700;
    color: #1F2937;
    margin-bottom: 1rem;
    line-height: 1.3;
}

.hardware-description {
    font-size: 14px;
    color: #6B7280;
    line-height: 1.5;
    margin-bottom: 1.5rem;
}

.hardware-stats {
    display: flex;
    gap: 1rem;
    margin-bottom: 1rem;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background-color: #F9FAFB;
    padding: 8px 12px;
    border-radius: 8px;
    font-size: 12px;
    color: #6B7280;
}

.stat-icon {
    font-size: 14px;
}

.stat-text {
    font-weight: 600;
}

.hardware-footer {
    padding: 1rem 1.5rem;
    background-color: #F9FAFB;
    border-top: 1px solid #E5E7EB;
}

.view-more {
    color: #DC2626;
    font-weight: 600;
    font-size: 14px;
    transition: color 0.3s ease;
}

.hardware-card:hover .view-more {
    color: #FCD34D;
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
    .hardware-intro {
        padding: 1.5rem;
    }
    
    .hardware-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    
    .hardware-card {
        margin: 0 1rem;
    }
    
    .hardware-header {
        padding: 1rem 1rem 0 1rem;
    }
    
    .hardware-content {
        padding: 1rem;
    }
    
    .hardware-footer {
        padding: 0.75rem 1rem;
    }
}
</style>
