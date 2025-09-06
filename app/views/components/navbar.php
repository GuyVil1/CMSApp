<?php
/**
 * Composant Navbar
 * Menu de navigation principal avec menu déroulant hardware
 */

// Récupérer les hardwares pour le menu déroulant
$hardwares = Hardware::getAllForMenu();

// Définir les catégories principales
$categories = [
    'actualités' => ['name' => 'ACTUALITÉS', 'slug' => 'actu'],
    'tests' => ['name' => 'TESTS', 'slug' => 'test'],
    'dossiers' => ['name' => 'DOSSIERS', 'slug' => 'dossiers'],
    'trailers' => ['name' => 'TRAILERS', 'slug' => 'trailers']
];
?>

<nav class="main-navbar">
    <div class="navbar-container">
        <!-- Menu Hardware avec dropdown -->
        <div class="nav-item dropdown">
            <a href="/hardwares" class="nav-button" aria-expanded="false" aria-haspopup="true">
                HARDWARE
                <svg class="dropdown-arrow" width="12" height="8" viewBox="0 0 12 8" fill="none">
                    <path d="M1 1.5L6 6.5L11 1.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </a>
            <div class="dropdown-menu" role="menu">
                <a href="/hardwares" class="dropdown-item" role="menuitem">
                    <strong>Tous les Hardwares</strong>
                </a>
                <div class="dropdown-divider"></div>
                <?php if (!empty($hardwares)): ?>
                    <?php foreach ($hardwares as $hardware): ?>
                        <a href="/hardwares/<?= $hardware->getSlug() ?>" class="dropdown-item" role="menuitem">
                            <?= htmlspecialchars($hardware->getFullName()) ?>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="dropdown-item disabled">Aucun hardware disponible</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Boutons de catégories -->
        <?php foreach ($categories as $key => $category): ?>
            <div class="nav-item">
                <a href="/category/<?= $category['slug'] ?>" class="nav-button">
                    <?= $category['name'] ?>
                </a>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Menu hamburger pour mobile -->
    <button class="navbar-toggle" aria-label="Ouvrir le menu">
        <span class="hamburger-line"></span>
        <span class="hamburger-line"></span>
        <span class="hamburger-line"></span>
    </button>
</nav>

<!-- Menu mobile -->
<div class="navbar-mobile" id="navbar-mobile">
    <div class="navbar-mobile-content">
        <!-- Hardware mobile -->
        <div class="mobile-section">
            <h3 class="mobile-section-title">HARDWARE</h3>
            <div class="mobile-items">
                <?php if (!empty($hardwares)): ?>
                    <?php foreach ($hardwares as $hardware): ?>
                        <a href="<?= $hardware->getUrl() ?>" class="mobile-item">
                            <?= htmlspecialchars($hardware->getFullName()) ?>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Catégories mobile -->
        <div class="mobile-section">
            <h3 class="mobile-section-title">CATÉGORIES</h3>
            <div class="mobile-items">
                <?php foreach ($categories as $key => $category): ?>
                    <a href="/category/<?= $category['slug'] ?>" class="mobile-item">
                        <?= $category['name'] ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
