<?php
/**
 * Vue d'administration - Éditer un jeu
 */
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier le Jeu - Administration</title>
    <link rel="stylesheet" href="/admin.css">
</head>
<body>
    <div class="admin-container">
        <!-- Header -->
        <header class="admin-header">
            <div class="header-content">
                <h1>🎮 Modifier le Jeu</h1>
                <div class="header-actions">
                    <a href="/games.php" class="btn btn-secondary">← Retour à la liste</a>
                </div>
            </div>
        </header>

        <!-- Messages d'erreur -->
        <?php if (!empty($error)): ?>
            <div class="alert alert-error">
                ❌ <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <!-- Informations du jeu -->
        <div class="info-card">
            <h3>📋 Informations du jeu</h3>
            <div class="info-grid">
                <div class="info-item">
                    <strong>ID du jeu :</strong>
                    <code><?= $game->getId() ?></code>
                </div>
                <div class="info-item">
                    <strong>Slug actuel :</strong>
                    <code><?= htmlspecialchars($game->getSlug()) ?></code>
                </div>
                <div class="info-item">
                    <strong>Date de création :</strong>
                    <span><?= date('d/m/Y H:i', strtotime($game->getCreatedAt())) ?></span>
                </div>
                <div class="info-item">
                    <strong>Articles liés :</strong>
                    <span><?= $game->getArticlesCount() ?> article(s)</span>
                </div>
            </div>
        </div>

        <!-- Formulaire -->
        <div class="form-container">
            <form method="POST" action="/games.php?action=update&id=<?= $game->getId() ?>" class="form">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                
                <div class="form-section">
                    <h2>📝 Informations de base</h2>
                    
                    <div class="form-group">
                        <label for="title" class="form-label required">Titre du jeu *</label>
                        <input type="text" id="title" name="title" required 
                               class="form-input" placeholder="Ex: The Legend of Zelda: Breath of the Wild"
                               value="<?= htmlspecialchars($_POST['title'] ?? $game->getTitle()) ?>">
                        <div class="form-help">Le titre officiel du jeu</div>
                    </div>

                    <div class="form-group">
                        <label for="slug" class="form-label">Slug</label>
                        <input type="text" id="slug" name="slug" 
                               class="form-input" placeholder="Ex: legend-of-zelda-breath-of-the-wild"
                               value="<?= htmlspecialchars($_POST['slug'] ?? $game->getSlug()) ?>">
                        <div class="form-help">URL-friendly (généré automatiquement si vide)</div>
                    </div>

                    <div class="form-group">
                        <label for="description" class="form-label">Description</label>
                        <textarea id="description" name="description" rows="4" 
                                  class="form-textarea" placeholder="Description du jeu, synopsis, gameplay..."
                                  ><?= htmlspecialchars($_POST['description'] ?? $game->getDescription() ?? '') ?></textarea>
                        <div class="form-help">Description détaillée du jeu</div>
                    </div>
                </div>

                <div class="form-section">
                    <h2>🎯 Classification</h2>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="hardware_id" class="form-label">Hardware</label>
                            <select id="hardware_id" name="hardware_id" class="form-select">
                                <option value="">Sélectionner un hardware</option>
                                <?php 
                                $currentHardwareId = $_POST['hardware_id'] ?? $game->getHardwareId();
                                foreach ($hardware as $hw): ?>
                                    <option value="<?= $hw->getId() ?>" <?= $currentHardwareId == $hw->getId() ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($hw->getName()) ?> (<?= htmlspecialchars($hw->getTypeName()) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-help">Plateforme de jeu (optionnel)</div>
                        </div>

                        <div class="form-group">
                            <label for="genre_id" class="form-label">Genre</label>
                            <select id="genre_id" name="genre_id" class="form-select">
                                <option value="">Sélectionner un genre</option>
                                <?php 
                                $currentGenreId = $_POST['genre_id'] ?? $game->getGenreId();
                                foreach ($genres as $genre): ?>
                                    <option value="<?= $genre->getId() ?>" <?= $currentGenreId == $genre->getId() ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($genre->getName()) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-help">Genre principal du jeu</div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="release_date" class="form-label">Date de sortie</label>
                        <input type="date" id="release_date" name="release_date" 
                               class="form-input" 
                               value="<?= htmlspecialchars($_POST['release_date'] ?? $game->getReleaseDate() ?? '') ?>">
                        <div class="form-help">Date de sortie officielle du jeu</div>
                    </div>
                </div>

                <div class="form-section">
                    <h2>🖼️ Image de couverture</h2>
                    
                    <div class="form-group">
                        <label for="cover_image_id" class="form-label">Cover du jeu</label>
                        <select id="cover_image_id" name="cover_image_id" class="form-select">
                            <option value="">Aucune image</option>
                            <!-- TODO: Charger les images disponibles depuis la base de données -->
                        </select>
                        <div class="form-help">Sélectionner une image de couverture pour le jeu</div>
                    </div>

                    <?php if ($game->getCoverImageUrl()): ?>
                        <div class="current-cover">
                            <h4>Cover actuelle :</h4>
                            <img src="/public/uploads.php?file=<?= urlencode($game->getCoverImageUrl()) ?>" 
                                 alt="Cover actuelle" class="current-cover-image">
                        </div>
                    <?php endif; ?>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">💾 Sauvegarder les modifications</button>
                    <a href="/games.php" class="btn btn-secondary">❌ Annuler</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Génération automatique du slug
        document.getElementById('title').addEventListener('input', function() {
            const title = this.value;
            const slugField = document.getElementById('slug');
            
            if (slugField.value === '') {
                const slug = title.toLowerCase()
                    .replace(/[^a-z0-9\s-]/g, '')
                    .replace(/[\s-]+/g, '-')
                    .trim('-');
                slugField.value = slug;
            }
        });

        // Validation du formulaire
        document.querySelector('form').addEventListener('submit', function(e) {
            const title = document.getElementById('title').value.trim();
            
            if (title === '') {
                e.preventDefault();
                alert('Le titre du jeu est obligatoire');
                document.getElementById('title').focus();
                return false;
            }
        });
    </script>
</body>
</html>
