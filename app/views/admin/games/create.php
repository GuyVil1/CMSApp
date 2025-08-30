<?php
/**
 * Vue d'administration - Créer un nouveau jeu
 */
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveau Jeu - Administration</title>
    <link rel="stylesheet" href="/admin.css">
</head>
<body>
    <div class="admin-container">
        <!-- Header -->
        <header class="admin-header">
            <div class="header-content">
                <h1>🎮 Nouveau Jeu</h1>
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

        <!-- Formulaire -->
        <div class="form-container">
            <form method="POST" action="/games.php?action=store" class="form" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                
                <div class="form-section">
                    <h2>📝 Informations de base</h2>
                    
                    <div class="form-group">
                        <label for="title" class="form-label required">Titre du jeu *</label>
                        <input type="text" id="title" name="title" required 
                               class="form-input" placeholder="Ex: The Legend of Zelda: Breath of the Wild"
                               value="<?= htmlspecialchars($_POST['title'] ?? '') ?>">
                        <div class="form-help">Le titre officiel du jeu</div>
                    </div>

                    <div class="form-group">
                        <label for="slug" class="form-label">Slug</label>
                        <input type="text" id="slug" name="slug" 
                               class="form-input" placeholder="Ex: legend-of-zelda-breath-of-the-wild"
                               value="<?= htmlspecialchars($_POST['slug'] ?? '') ?>">
                        <div class="form-help">URL-friendly (généré automatiquement si vide)</div>
                    </div>

                    <div class="form-group">
                        <label for="description" class="form-label">Description</label>
                        <textarea id="description" name="description" rows="4" 
                                  class="form-textarea" placeholder="Description du jeu, synopsis, gameplay..."
                                  ><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
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
                                <?php foreach ($hardware as $hw): ?>
                                    <option value="<?= $hw->getId() ?>" <?= ($_POST['hardware_id'] ?? '') == $hw->getId() ? 'selected' : '' ?>>
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
                                <?php foreach ($genres as $genre): ?>
                                    <option value="<?= $genre->getId() ?>" <?= ($_POST['genre_id'] ?? '') == $genre->getId() ? 'selected' : '' ?>>
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
                               value="<?= htmlspecialchars($_POST['release_date'] ?? '') ?>">
                        <div class="form-help">Date de sortie officielle du jeu</div>
                    </div>
                </div>

                <div class="form-section">
                    <h2>🖼️ Image de couverture</h2>
                    
                    <div class="form-group">
                        <label for="cover_image" class="form-label">Cover du jeu *</label>
                        <div class="upload-area" id="uploadArea">
                            <div class="upload-icon">📷</div>
                            <div class="upload-text">Cliquez ou glissez une image ici</div>
                            <div class="upload-hint">Formats acceptés: JPG, PNG, GIF (max 5MB)</div>
                            <input type="file" id="cover_image" name="cover_image" accept="image/*" class="file-input" required>
                        </div>
                        <div id="previewContainer" class="preview-container" style="display: none;">
                            <img id="imagePreview" class="image-preview" alt="Aperçu">
                            <button type="button" id="removeImage" class="btn btn-sm btn-danger">Supprimer</button>
                        </div>
                        <div class="form-help">Cette image sera renommée en "cover.jpg" et servira de couverture pour le jeu</div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">🎮 Créer le jeu</button>
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

        // Gestion de l'upload d'image
        const uploadArea = document.getElementById('uploadArea');
        const fileInput = document.getElementById('cover_image');
        const previewContainer = document.getElementById('previewContainer');
        const imagePreview = document.getElementById('imagePreview');
        const removeButton = document.getElementById('removeImage');

        // Clic sur la zone d'upload
        uploadArea.addEventListener('click', () => fileInput.click());

        // Drag & drop
        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.classList.add('dragover');
        });

        uploadArea.addEventListener('dragleave', () => {
            uploadArea.classList.remove('dragover');
        });

        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                handleFileSelect(files[0]);
            }
        });

        // Sélection de fichier
        fileInput.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                handleFileSelect(e.target.files[0]);
            }
        });

        // Supprimer l'image
        removeButton.addEventListener('click', () => {
            fileInput.value = '';
            previewContainer.style.display = 'none';
            uploadArea.style.display = 'flex';
        });

        function handleFileSelect(file) {
            // Vérifier le type de fichier
            if (!file.type.startsWith('image/')) {
                alert('Veuillez sélectionner une image valide');
                return;
            }

            // Vérifier la taille (5MB max)
            if (file.size > 5 * 1024 * 1024) {
                alert('L\'image ne doit pas dépasser 5MB');
                return;
            }

            // Afficher la prévisualisation
            const reader = new FileReader();
            reader.onload = (e) => {
                imagePreview.src = e.target.result;
                previewContainer.style.display = 'block';
                uploadArea.style.display = 'none';
            };
            reader.readAsDataURL(file);
        }

        // Validation du formulaire
        document.querySelector('form').addEventListener('submit', function(e) {
            const title = document.getElementById('title').value.trim();
            const coverImage = document.getElementById('cover_image').files[0];
            
            if (title === '') {
                e.preventDefault();
                alert('Le titre du jeu est obligatoire');
                document.getElementById('title').focus();
                return false;
            }

            if (!coverImage) {
                e.preventDefault();
                alert('Veuillez sélectionner une image de couverture');
                return false;
            }
        });
    </script>
</body>
</html>
