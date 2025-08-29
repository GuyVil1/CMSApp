<?php
/**
 * Vue : Édition d'une catégorie
 */
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Catégorie - Admin</title>
    <link rel="stylesheet" href="/admin.css">
</head>
<body>
    <div class="admin-container">
        <!-- Header -->
        <div class="admin-header">
            <div class="header-content">
                <h1>Modifier Catégorie</h1>
                <div class="header-actions">
                    <a href="/categories.php" class="btn btn-secondary">← Retour Liste</a>
                </div>
            </div>
        </div>

        <!-- Messages d'erreur/succès -->
        <?php if (!empty($error)): ?>
            <div class="alert alert-error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <!-- Informations système -->
        <div class="info-card">
            <h3>Informations système</h3>
            <div class="info-grid">
                <div class="info-item">
                    <strong>ID de la catégorie :</strong>
                    <span>#<?= $category->getId() ?></span>
                </div>
                <div class="info-item">
                    <strong>Slug actuel :</strong>
                    <code><?= htmlspecialchars($category->getSlug()) ?></code>
                </div>
                <div class="info-item">
                    <strong>Créée le :</strong>
                    <span><?= date('d/m/Y à H:i', strtotime($category->getCreatedAt())) ?></span>
                </div>
                <div class="info-item">
                    <strong>Articles associés :</strong>
                    <span><?= $category->getArticlesCount() ?> article(s)</span>
                </div>
            </div>
        </div>

        <!-- Formulaire d'édition -->
        <div class="form-container">
            <form method="POST" action="/categories.php?action=update&id=<?= $category->getId() ?>" class="form">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

                <div class="form-section">
                    <h3>Informations de la catégorie</h3>

                    <div class="form-group">
                        <label for="name" class="form-label">Nom de la catégorie *</label>
                        <input type="text" id="name" name="name" class="form-input"
                               value="<?= htmlspecialchars($_POST['name'] ?? $category->getName()) ?>"
                               required maxlength="80">
                        <small>Maximum 80 caractères</small>
                    </div>

                    <div class="form-group">
                        <label for="slug" class="form-label">Slug *</label>
                        <input type="text" id="slug" name="slug" class="form-input"
                               value="<?= htmlspecialchars($_POST['slug'] ?? $category->getSlug()) ?>"
                               required maxlength="120" pattern="[a-z0-9-]+">
                        <small>URL-friendly, uniquement lettres minuscules, chiffres et tirets</small>
                    </div>

                    <div class="form-group">
                        <label for="description" class="form-label">Description</label>
                        <textarea id="description" name="description" class="form-textarea" rows="4"
                                  maxlength="500"><?= htmlspecialchars($_POST['description'] ?? $category->getDescription() ?? '') ?></textarea>
                        <small>Maximum 500 caractères. Optionnel.</small>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-success">Mettre à jour</button>
                    <a href="/categories.php" class="btn btn-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const nameInput = document.getElementById('name');
            const slugInput = document.getElementById('slug');
            const descriptionInput = document.getElementById('description');

            // Génération automatique du slug (seulement si le slug est vide)
            nameInput.addEventListener('input', function() {
                if (slugInput.value === '') {
                    const name = this.value;
                    const slug = name.toLowerCase()
                        .replace(/[^a-z0-9\s-]/g, '')
                        .replace(/[\s-]+/g, '-')
                        .trim('-');
                    slugInput.value = slug;
                }
            });

            // Validation côté client
            const form = document.querySelector('.form');
            form.addEventListener('submit', function(e) {
                const name = nameInput.value.trim();
                const slug = slugInput.value.trim();
                const description = descriptionInput.value.trim();

                // Validation du nom
                if (name.length === 0) {
                    e.preventDefault();
                    alert('Le nom de la catégorie est obligatoire');
                    nameInput.focus();
                    return;
                }

                if (name.length > 80) {
                    e.preventDefault();
                    alert('Le nom de la catégorie ne peut pas dépasser 80 caractères');
                    nameInput.focus();
                    return;
                }

                // Validation du slug
                if (slug.length === 0) {
                    e.preventDefault();
                    alert('Le slug est obligatoire');
                    slugInput.focus();
                    return;
                }

                if (!/^[a-z0-9-]+$/.test(slug)) {
                    e.preventDefault();
                    alert('Le slug ne peut contenir que des lettres minuscules, chiffres et tirets');
                    slugInput.focus();
                    return;
                }

                if (slug.length > 120) {
                    e.preventDefault();
                    alert('Le slug ne peut pas dépasser 120 caractères');
                    slugInput.focus();
                    return;
                }

                // Validation de la description
                if (description.length > 500) {
                    e.preventDefault();
                    alert('La description ne peut pas dépasser 500 caractères');
                    descriptionInput.focus();
                    return;
                }
            });

            // Compteur de caractères pour la description
            descriptionInput.addEventListener('input', function() {
                const maxLength = 500;
                const currentLength = this.value.length;
                const remaining = maxLength - currentLength;
                
                const counter = this.parentNode.querySelector('small');
                if (counter) {
                    counter.textContent = `${remaining} caractères restants`;
                    
                    if (remaining < 0) {
                        counter.style.color = 'var(--admin-danger)';
                    } else if (remaining < 50) {
                        counter.style.color = 'var(--admin-warning)';
                    } else {
                        counter.style.color = 'var(--admin-text-muted)';
                    }
                }
            });

            // Initialiser le compteur au chargement
            descriptionInput.dispatchEvent(new Event('input'));
        });
    </script>
</body>
</html>
