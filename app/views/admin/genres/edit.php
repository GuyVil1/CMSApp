<?php
/**
 * Vue d'administration - Modifier un genre
 */
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier le Genre - Administration</title>
    <link rel="stylesheet" href="/admin.css">
</head>
<body>
    <div class="admin-container">
        <!-- Header -->
        <header class="admin-header">
            <div class="header-content">
                <h1>✏️ Modifier le Genre</h1>
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
            <form method="POST" action="/genres.php?action=update&id=<?= $genre->getId() ?>" class="form">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                
                <div class="form-section">
                    <h2>📝 Informations du genre</h2>
                    
                    <div class="form-group">
                        <label for="name" class="form-label required">Nom du genre *</label>
                        <input type="text" id="name" name="name" required 
                               class="form-input" placeholder="Ex: Action, RPG, Stratégie..."
                               value="<?= htmlspecialchars($_POST['name'] ?? $genre->getName()) ?>">
                        <div class="form-help">Le nom du genre de jeu</div>
                    </div>

                    <div class="form-group">
                        <label for="description" class="form-label">Description</label>
                        <textarea id="description" name="description" rows="4" 
                                  class="form-textarea" placeholder="Description du genre, caractéristiques..."
                                  ><?= htmlspecialchars($_POST['description'] ?? $genre->getDescription() ?? '') ?></textarea>
                        <div class="form-help">Description détaillée du genre (optionnel)</div>
                    </div>

                    <div class="form-group">
                        <label for="color" class="form-label">Couleur</label>
                        <div class="color-input-group">
                            <input type="color" id="color" name="color" 
                                   class="form-color" value="<?= htmlspecialchars($_POST['color'] ?? $genre->getColor()) ?>">
                            <input type="text" id="colorText" 
                                   class="form-input" value="<?= htmlspecialchars($_POST['color'] ?? $genre->getColor()) ?>"
                                   placeholder="#007bff">
                        </div>
                        <div class="form-help">Couleur d'identification du genre (format hexadécimal)</div>
                    </div>
                </div>

                <div class="form-section">
                    <h2>ℹ️ Informations système</h2>
                    
                    <div class="info-grid">
                        <div class="info-item">
                            <label>ID du genre :</label>
                            <span class="info-value"><?= $genre->getId() ?></span>
                        </div>
                        <div class="info-item">
                            <label>Créé le :</label>
                            <span class="info-value"><?= date('d/m/Y à H:i', strtotime($genre->getCreatedAt())) ?></span>
                        </div>
                        <div class="info-item">
                            <label>Modifié le :</label>
                            <span class="info-value"><?= date('d/m/Y à H:i', strtotime($genre->getUpdatedAt())) ?></span>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">💾 Sauvegarder les modifications</button>
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
