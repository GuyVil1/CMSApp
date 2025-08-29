<?php
/**
 * Vue d'administration - Créer un nouveau hardware
 */
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveau Hardware - Administration</title>
    <link rel="stylesheet" href="/admin.css">
</head>
<body>
    <div class="admin-container">
        <!-- Header -->
        <header class="admin-header">
            <div class="header-content">
                <h1>🖥️ Nouveau Hardware</h1>
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

        <!-- Formulaire -->
        <div class="form-container">
            <form method="POST" action="/hardware.php?action=store" class="form">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                
                <div class="form-section">
                    <h2>📝 Informations de base</h2>
                    
                    <div class="form-group">
                        <label for="name" class="form-label required">Nom du hardware *</label>
                        <input type="text" id="name" name="name" required 
                               class="form-input" placeholder="Ex: PlayStation 5"
                               value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
                        <div class="form-help">Le nom officiel du hardware</div>
                    </div>

                    <div class="form-group">
                        <label for="slug" class="form-label">Slug</label>
                        <input type="text" id="slug" name="slug" 
                               class="form-input" placeholder="Ex: playstation-5"
                               value="<?= htmlspecialchars($_POST['slug'] ?? '') ?>">
                        <div class="form-help">URL-friendly (généré automatiquement si vide)</div>
                    </div>

                    <div class="form-group">
                        <label for="type" class="form-label required">Type *</label>
                        <select id="type" name="type" required class="form-select">
                            <option value="">Sélectionner un type</option>
                            <?php foreach ($types as $typeKey => $typeName): ?>
                                <option value="<?= $typeKey ?>" <?= ($_POST['type'] ?? '') === $typeKey ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($typeName) ?>
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
                               value="<?= htmlspecialchars($_POST['manufacturer'] ?? '') ?>">
                        <div class="form-help">Le fabricant du hardware</div>
                    </div>

                    <div class="form-group">
                        <label for="release_year" class="form-label">Année de sortie</label>
                        <input type="number" id="release_year" name="release_year" 
                               class="form-input" placeholder="Ex: 2020"
                               min="1970" max="2030"
                               value="<?= htmlspecialchars($_POST['release_year'] ?? '') ?>">
                        <div class="form-help">Année de sortie du hardware</div>
                    </div>
                </div>

                <div class="form-section">
                    <h2>📋 Description et paramètres</h2>
                    
                    <div class="form-group">
                        <label for="description" class="form-label">Description</label>
                        <textarea id="description" name="description" rows="4" 
                                  class="form-textarea" placeholder="Description du hardware, caractéristiques techniques..."
                                  ><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                        <div class="form-help">Description détaillée du hardware</div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="sort_order" class="form-label">Ordre de tri</label>
                            <input type="number" id="sort_order" name="sort_order" 
                                   class="form-input" placeholder="0"
                                   min="0" max="999"
                                   value="<?= htmlspecialchars($_POST['sort_order'] ?? '0') ?>">
                            <div class="form-help">Ordre d'affichage (plus petit = en premier)</div>
                        </div>

                        <div class="form-group">
                            <div class="form-checkbox">
                                <input type="checkbox" id="is_active" name="is_active" 
                                       <?= isset($_POST['is_active']) ? 'checked' : 'checked' ?>>
                                <label for="is_active">Hardware actif</label>
                            </div>
                            <div class="form-help">Cocher pour rendre ce hardware disponible</div>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">🖥️ Créer le hardware</button>
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
