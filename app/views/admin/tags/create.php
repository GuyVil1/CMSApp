<?php
/**
 * Vue : Créer un nouveau tag
 */
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un Tag - Admin</title>
    <link rel="stylesheet" href="/admin.css">
</head>
<body>
    <div class="admin-container">
        <!-- Header -->
        <div class="admin-header">
            <div class="header-content">
                <h1>Créer un Nouveau Tag</h1>
                <div class="header-actions">
                    <a href="/tags.php" class="btn btn-secondary">← Retour Liste</a>
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

        <!-- Formulaire de création -->
        <div class="form-container">
            <form method="POST" action="/tags.php?action=store" class="form">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                
                <div class="form-section">
                    <h3>Informations du tag</h3>
                    
                    <div class="form-group">
                        <label for="name" class="form-label">Nom du tag *</label>
                        <input type="text" id="name" name="name" class="form-input" 
                               value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" 
                               required maxlength="80">
                        <small>Maximum 80 caractères</small>
                    </div>

                    <div class="form-group">
                        <label for="slug" class="form-label">Slug *</label>
                        <input type="text" id="slug" name="slug" class="form-input" 
                               value="<?= htmlspecialchars($_POST['slug'] ?? '') ?>" 
                               required maxlength="120" pattern="[a-z0-9-]+">
                        <small>URL-friendly, uniquement lettres minuscules, chiffres et tirets</small>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-success">Créer le tag</button>
                    <a href="/tags.php" class="btn btn-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Génération automatique du slug
        document.addEventListener('DOMContentLoaded', function() {
            const nameInput = document.getElementById('name');
            const slugInput = document.getElementById('slug');
            
            nameInput.addEventListener('input', function() {
                if (slugInput.value === '') {
                    // Générer le slug seulement si le champ slug est vide
                    const name = this.value;
                    const slug = name.toLowerCase()
                        .replace(/[^a-z0-9\s-]/g, '')
                        .replace(/[\s-]+/g, '-')
                        .trim('-');
                    slugInput.value = slug;
                }
            });
            
            // Bouton pour régénérer le slug
            const regenerateSlug = () => {
                const name = nameInput.value;
                const slug = name.toLowerCase()
                    .replace(/[^a-z0-9\s-]/g, '')
                    .replace(/[\s-]+/g, '-')
                    .trim('-');
                slugInput.value = slug;
            };
            
            // Ajouter un bouton pour régénérer le slug
            const slugGroup = slugInput.parentElement;
            const regenerateBtn = document.createElement('button');
            regenerateBtn.type = 'button';
            regenerateBtn.textContent = '🔄 Régénérer';
            regenerateBtn.className = 'btn btn-sm btn-secondary';
            regenerateBtn.style.marginTop = '8px';
            regenerateBtn.onclick = regenerateSlug;
            slugGroup.appendChild(regenerateBtn);
            
            // Validation côté client
            const form = document.querySelector('.form');
            const nameInput = document.getElementById('name');
            const slugInput = document.getElementById('slug');

            form.addEventListener('submit', function(e) {
                let isValid = true;
                let errorMessage = '';

                // Validation du nom
                if (nameInput.value.length < 2) {
                    errorMessage += 'Le nom du tag doit contenir au moins 2 caractères.\n';
                    isValid = false;
                }

                if (nameInput.value.length > 80) {
                    errorMessage += 'Le nom du tag ne peut pas dépasser 80 caractères.\n';
                    isValid = false;
                }

                // Validation du slug
                if (slugInput.value.length < 2) {
                    errorMessage += 'Le slug doit contenir au moins 2 caractères.\n';
                    isValid = false;
                }

                if (slugInput.value.length > 120) {
                    errorMessage += 'Le slug ne peut pas dépasser 120 caractères.\n';
                    isValid = false;
                }

                if (!/^[a-z0-9-]+$/.test(slugInput.value)) {
                    errorMessage += 'Le slug ne peut contenir que des lettres minuscules, chiffres et tirets.\n';
                    isValid = false;
                }

                if (!isValid) {
                    e.preventDefault();
                    alert('Erreurs de validation:\n' + errorMessage);
                }
            });
        });
    </script>
</body>
</html>
