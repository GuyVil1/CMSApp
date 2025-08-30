<?php
/**
 * Vue d'administration - Créer un nouveau genre
 */
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveau Genre - Administration</title>
    <link rel="stylesheet" href="/admin.css">
</head>
<body>
    <div class="admin-container">
        <!-- Header -->
        <header class="admin-header">
            <div class="header-content">
                <h1>🎯 Nouveau Genre</h1>
                <div class="header-actions">
                    <a href="/genres.php" class="btn btn-secondary">← Retour à la liste</a>
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
            <form method="POST" action="/genres.php?action=store" class="form">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                
                <div class="form-section">
                    <h2>📝 Informations du genre</h2>
                    
                    <div class="form-group">
                        <label for="name" class="form-label required">Nom du genre *</label>
                        <input type="text" id="name" name="name" required 
                               class="form-input" placeholder="Ex: Action, RPG, Stratégie..."
                               value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
                        <div class="form-help">Le nom du genre de jeu</div>
                    </div>

                    <div class="form-group">
                        <label for="description" class="form-label">Description</label>
                        <textarea id="description" name="description" rows="4" 
                                  class="form-textarea" placeholder="Description du genre, caractéristiques..."
                                  ><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                        <div class="form-help">Description détaillée du genre (optionnel)</div>
                    </div>

                    <div class="form-group">
                        <label for="color" class="form-label">Couleur</label>
                        <div class="color-input-group">
                            <input type="color" id="color" name="color" 
                                   class="form-color" value="<?= htmlspecialchars($_POST['color'] ?? '#007bff') ?>">
                            <input type="text" id="colorText" 
                                   class="form-input" value="<?= htmlspecialchars($_POST['color'] ?? '#007bff') ?>"
                                   placeholder="#007bff">
                        </div>
                        <div class="form-help">Couleur d'identification du genre (format hexadécimal)</div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">💾 Créer le genre</button>
                    <a href="/genres.php" class="btn btn-secondary">❌ Annuler</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Synchronisation entre l'input couleur et le texte
        document.getElementById('color').addEventListener('input', function() {
            document.getElementById('colorText').value = this.value;
        });

        document.getElementById('colorText').addEventListener('input', function() {
            const color = this.value;
            if (/^#[0-9a-fA-F]{6}$/.test(color)) {
                document.getElementById('color').value = color;
            }
        });

        // Validation du formulaire
        document.querySelector('form').addEventListener('submit', function(e) {
            const name = document.getElementById('name').value.trim();
            const color = document.getElementById('colorText').value.trim();
            
            if (!name) {
                e.preventDefault();
                alert('Le nom du genre est obligatoire');
                return;
            }
            
            if (!/^#[0-9a-fA-F]{6}$/.test(color)) {
                e.preventDefault();
                alert('Format de couleur invalide. Utilisez le format #RRGGBB (ex: #ff0000)');
                return;
            }
        });
    </script>
</body>
</html>
