<?php
/**
 * Vue d'administration - Modifier un hardware
 */
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Hardware - Administration</title>
    <link rel="stylesheet" href="/admin.css">
</head>
<body>
    <div class="admin-container">
        <!-- Header -->
        <header class="admin-header">
            <div class="header-content">
                <h1>🖥️ Modifier Hardware</h1>
                <div class="header-actions">
                    <a href="/hardware.php" class="btn btn-secondary">← Retour à la liste</a>
                </div>
            </div>
        </header>

        <!-- Messages d'erreur -->
        <?php if (!empty($error)): ?>
            <div class="alert alert-error">
                ❌ <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <!-- Informations système -->
        <div class="info-card">
            <h3>📊 Informations système</h3>
            <div class="info-grid">
                <div class="info-item">
                    <strong>ID Hardware:</strong>
                    <code><?= $hardware->getId() ?></code>
                </div>
                <div class="info-item">
                    <strong>Slug actuel:</strong>
                    <code><?= htmlspecialchars($hardware->getSlug()) ?></code>
                </div>
                <div class="info-item">
                    <strong>Créé le:</strong>
                    <span><?= date('d/m/Y H:i', strtotime($hardware->getCreatedAt())) ?></span>
                </div>
                <div class="info-item">
                    <strong>Jeux associés:</strong>
                    <span><?= $hardware->getGamesCount() ?> jeu(x)</span>
                </div>
                <?php if ($hardware->getUpdatedAt()): ?>
                    <div class="info-item">
                        <strong>Modifié le:</strong>
                        <span><?= date('d/m/Y H:i', strtotime($hardware->getUpdatedAt())) ?></span>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Formulaire -->
        <div class="form-container">
            <form method="POST" action="/hardware.php?action=update&id=<?= $hardware->getId() ?>" class="form">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                
                <div class="form-section">
                    <h2>📝 Informations de base</h2>
                    
                    <div class="form-group">
                        <label for="name" class="form-label required">Nom du hardware *</label>
                        <input type="text" id="name" name="name" required 
                               class="form-input" placeholder="Ex: PlayStation 5"
                               value="<?= htmlspecialchars($_POST['name'] ?? $hardware->getName()) ?>">
                        <div class="form-help">Le nom officiel du hardware</div>
                    </div>

                    <div class="form-group">
                        <label for="slug" class="form-label">Slug</label>
                        <input type="text" id="slug" name="slug" 
                               class="form-input" placeholder="Ex: playstation-5"
                               value="<?= htmlspecialchars($_POST['slug'] ?? $hardware->getSlug()) ?>">
                        <div class="form-help">URL-friendly (généré automatiquement si vide)</div>
                    </div>

                    <div class="form-group">
                        <label for="type" class="form-label required">Type *</label>
                        <select id="type" name="type" required class="form-select">
                            <option value="">Sélectionner un type</option>
                            <?php 
                            $currentType = $_POST['type'] ?? $hardware->getType();
                            foreach ($types as $type): 
                            ?>
                                <option value="<?= $type ?>" <?= $currentType === $type ? 'selected' : '' ?>>
                                    <?= htmlspecialchars(ucfirst($type)) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-help">Catégorie du hardware</div>
                    </div>
                </div>

                <div class="form-section">
                    <h2>🏭 Informations fabricant</h2>
                    
                    <div class="form-group">
                        <label for="manufacturer" class="form-label">Fabricant</label>
                        <input type="text" id="manufacturer" name="manufacturer" 
                               class="form-input" placeholder="Ex: Sony, Microsoft, Nintendo"
                               value="<?= htmlspecialchars($_POST['manufacturer'] ?? $hardware->getManufacturer() ?? '') ?>">
                        <div class="form-help">Le fabricant du hardware</div>
                    </div>

                    <div class="form-group">
                        <label for="release_year" class="form-label">Année de sortie</label>
                        <input type="number" id="release_year" name="release_year" 
                               class="form-input" placeholder="Ex: 2020"
                               min="1970" max="2030"
                               value="<?= htmlspecialchars($_POST['release_year'] ?? $hardware->getReleaseYear() ?? '') ?>">
                        <div class="form-help">Année de sortie du hardware</div>
                    </div>
                </div>

                <div class="form-section">
                    <h2>📋 Description et paramètres</h2>
                    
                    <div class="form-group">
                        <label for="description" class="form-label">Description</label>
                        <textarea id="description" name="description" rows="4" 
                                  class="form-textarea" placeholder="Description du hardware, caractéristiques techniques..."
                                  ><?= htmlspecialchars($_POST['description'] ?? $hardware->getDescription() ?? '') ?></textarea>
                        <div class="form-help">Description détaillée du hardware</div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="sort_order" class="form-label">Ordre de tri</label>
                            <input type="number" id="sort_order" name="sort_order" 
                                   class="form-input" placeholder="0"
                                   min="0" max="999"
                                   value="<?= htmlspecialchars($_POST['sort_order'] ?? $hardware->getSortOrder()) ?>">
                            <div class="form-help">Ordre d'affichage (plus petit = en premier)</div>
                        </div>

                        <div class="form-group">
                            <div class="form-checkbox">
                                <input type="checkbox" id="is_active" name="is_active" 
                                       <?= isset($_POST['is_active']) ? 'checked' : ($hardware->isActive() ? 'checked' : '') ?>>
                                <label for="is_active">Hardware actif</label>
                            </div>
                            <div class="form-help">Cocher pour rendre ce hardware disponible</div>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">💾 Sauvegarder les modifications</button>
                    <a href="/hardware.php" class="btn btn-secondary">❌ Annuler</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Génération automatique du slug
        document.getElementById('name').addEventListener('input', function() {
            const name = this.value;
            const slugField = document.getElementById('slug');
            
            if (slugField.value === '') {
                const slug = name.toLowerCase()
                    .replace(/[^a-z0-9\s-]/g, '')
                    .replace(/[\s-]+/g, '-')
                    .trim('-');
                slugField.value = slug;
            }
        });

        // Validation du formulaire
        document.querySelector('form').addEventListener('submit', function(e) {
            const name = document.getElementById('name').value.trim();
            const type = document.getElementById('type').value;
            
            if (name === '') {
                e.preventDefault();
                alert('Le nom du hardware est obligatoire');
                document.getElementById('name').focus();
                return false;
            }

            if (type === '') {
                e.preventDefault();
                alert('Le type de hardware est obligatoire');
                document.getElementById('type').focus();
                return false;
            }
        });
    </script>
</body>
</html>
