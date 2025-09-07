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
    <style>
        .media-library-trigger {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 10px;
        }
        
        .selected-image-name {
            color: #666;
            font-style: italic;
            font-size: 14px;
        }
        
        .selected-image-name.success {
            color: #28a745;
            font-weight: 500;
        }
        
        #openMediaLibrary, #openMediaLibraryCreate {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
    </style>
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
                    <h2>🏢 Informations de l'équipe</h2>
                    
                    <div class="form-group">
                        <label for="developer" class="form-label">Développeur</label>
                        <input type="text" id="developer" name="developer" 
                               class="form-input" placeholder="Ex: Nintendo EPD"
                               value="<?= htmlspecialchars($_POST['developer'] ?? '') ?>">
                        <div class="form-help">Studio de développement du jeu</div>
                    </div>

                    <div class="form-group">
                        <label for="publisher" class="form-label">Éditeur</label>
                        <input type="text" id="publisher" name="publisher" 
                               class="form-input" placeholder="Ex: Nintendo"
                               value="<?= htmlspecialchars($_POST['publisher'] ?? '') ?>">
                        <div class="form-help">Éditeur du jeu</div>
                    </div>
                </div>

                <div class="form-section">
                    <h2>📊 Test et évaluation</h2>
                    
                    <div class="form-group">
                        <label class="form-label">
                            <input type="checkbox" id="is_tested" name="is_tested" value="1" 
                                   <?= isset($_POST['is_tested']) ? 'checked' : '' ?>>
                            Ce jeu a été testé par l'équipe
                        </label>
                        <div class="form-help">Cochez si le jeu a été testé et noté par l'équipe</div>
                    </div>

                    <div class="form-group" id="score-group" style="display: none;">
                        <label for="score" class="form-label">Note sur 10</label>
                        <input type="number" id="score" name="score" 
                               class="form-input" min="0" max="10" step="0.1" 
                               placeholder="Ex: 8.5"
                               value="<?= htmlspecialchars($_POST['score'] ?? '') ?>">
                        <div class="form-help">Note de 0.0 à 10.0 (ex: 8.5)</div>
                    </div>
                </div>

                <div class="form-section">
                    <h2>🔞 Classification</h2>
                    
                    <div class="form-group">
                        <label for="pegi_rating" class="form-label">Classification PEGI</label>
                        <select id="pegi_rating" name="pegi_rating" class="form-select">
                            <option value="">Non spécifié</option>
                            <option value="3" <?= ($_POST['pegi_rating'] ?? '') === '3' ? 'selected' : '' ?>>PEGI 3</option>
                            <option value="7" <?= ($_POST['pegi_rating'] ?? '') === '7' ? 'selected' : '' ?>>PEGI 7</option>
                            <option value="12" <?= ($_POST['pegi_rating'] ?? '') === '12' ? 'selected' : '' ?>>PEGI 12</option>
                            <option value="16" <?= ($_POST['pegi_rating'] ?? '') === '16' ? 'selected' : '' ?>>PEGI 16</option>
                            <option value="18" <?= ($_POST['pegi_rating'] ?? '') === '18' ? 'selected' : '' ?>>PEGI 18</option>
                        </select>
                        <div class="form-help">Classification d'âge PEGI</div>
                    </div>
                </div>

                <div class="form-section">
                    <h2>🛒 Liens d'achat</h2>
                    
                    <div class="form-group">
                        <label for="steam_url" class="form-label">🔵 Steam</label>
                        <input type="url" id="steam_url" name="steam_url" 
                               class="form-input" placeholder="https://store.steampowered.com/app/123456/GameName/"
                               value="<?= htmlspecialchars($_POST['steam_url'] ?? '') ?>">
                        <div class="form-help">Lien vers la page Steam du jeu</div>
                    </div>

                    <div class="form-group">
                        <label for="eshop_url" class="form-label">🔴 Nintendo eShop</label>
                        <input type="url" id="eshop_url" name="eshop_url" 
                               class="form-input" placeholder="https://www.nintendo.com/store/products/game-name-switch/"
                               value="<?= htmlspecialchars($_POST['eshop_url'] ?? '') ?>">
                        <div class="form-help">Lien vers la page Nintendo eShop du jeu</div>
                    </div>

                    <div class="form-group">
                        <label for="xbox_url" class="form-label">🟢 Xbox Store</label>
                        <input type="url" id="xbox_url" name="xbox_url" 
                               class="form-input" placeholder="https://www.xbox.com/en-us/games/store/game-name/9N1234567890"
                               value="<?= htmlspecialchars($_POST['xbox_url'] ?? '') ?>">
                        <div class="form-help">Lien vers la page Microsoft Store (Xbox) du jeu</div>
                    </div>

                    <div class="form-group">
                        <label for="psn_url" class="form-label">🔵 PlayStation Store</label>
                        <input type="url" id="psn_url" name="psn_url" 
                               class="form-input" placeholder="https://store.playstation.com/en-us/product/UP1234-CUSA12345_00-GAMENAME000000"
                               value="<?= htmlspecialchars($_POST['psn_url'] ?? '') ?>">
                        <div class="form-help">Lien vers la page PlayStation Store du jeu</div>
                    </div>

                    <div class="form-group">
                        <label for="epic_url" class="form-label">🟣 Epic Games Store</label>
                        <input type="url" id="epic_url" name="epic_url" 
                               class="form-input" placeholder="https://www.epicgames.com/store/en-US/p/game-name"
                               value="<?= htmlspecialchars($_POST['epic_url'] ?? '') ?>">
                        <div class="form-help">Lien vers la page Epic Games Store du jeu</div>
                    </div>

                    <div class="form-group">
                        <label for="gog_url" class="form-label">🟡 GOG.com</label>
                        <input type="url" id="gog_url" name="gog_url" 
                               class="form-input" placeholder="https://www.gog.com/game/game_name"
                               value="<?= htmlspecialchars($_POST['gog_url'] ?? '') ?>">
                        <div class="form-help">Lien vers la page GOG.com du jeu</div>
                    </div>
                </div>

                <div class="form-section">
                    <h2>🖼️ Image de couverture</h2>
                    
                    <div class="form-group">
                        <label class="form-label">Choisir une image de couverture *</label>
                        
                        <!-- Option 1: Upload d'une nouvelle image -->
                        <div class="form-group">
                            <label class="form-label">
                                <input type="radio" name="cover_option" value="upload" checked> 
                                📤 Uploader une nouvelle image
                            </label>
                            <div class="upload-area" id="uploadArea">
                                <div class="upload-icon">📷</div>
                                <div class="upload-text">Cliquez ou glissez une image ici</div>
                                <div class="upload-hint">Formats acceptés: JPG, PNG, GIF (max 15MB)</div>
                                <input type="file" id="cover_image" name="cover_image" accept="image/*" class="file-input">
                            </div>
                            <div id="previewContainer" class="preview-container" style="display: none;">
                                <img id="imagePreview" class="image-preview" alt="Aperçu">
                                <button type="button" id="removeImage" class="btn btn-sm btn-danger">Supprimer</button>
                            </div>
                        </div>
                        
                        <!-- Option 2: Sélectionner une image existante -->
                        <div class="form-group">
                            <label class="form-label">
                                <input type="radio" name="cover_option" value="existing"> 
                                🖼️ Utiliser une image existante
                            </label>
                            <div class="media-library-trigger">
                                <button type="button" id="openMediaLibraryCreate" class="btn btn-secondary" disabled>
                                    🖼️ Choisir dans la médiathèque
                                </button>
                                <span id="selectedImageNameCreate" class="selected-image-name">
                                    Aucune image sélectionnée
                                </span>
                            </div>
                            <input type="hidden" id="existing_cover_image" name="existing_cover_image" value="">
                        </div>
                        
                        <div class="form-help">Choisissez entre uploader une nouvelle image ou utiliser une image déjà disponible</div>
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

        // Gestion de l'affichage conditionnel du champ score
        const isTestedCheckbox = document.getElementById('is_tested');
        const scoreGroup = document.getElementById('score-group');
        const scoreInput = document.getElementById('score');

        isTestedCheckbox.addEventListener('change', () => {
            if (isTestedCheckbox.checked) {
                scoreGroup.style.display = 'block';
                scoreInput.required = true;
            } else {
                scoreGroup.style.display = 'none';
                scoreInput.required = false;
                scoreInput.value = '';
            }
        });

        // Initialiser l'état au chargement de la page
        if (isTestedCheckbox.checked) {
            scoreGroup.style.display = 'block';
            scoreInput.required = true;
        }

        // Gestion des options d'image de couverture
        const coverOptionUpload = document.querySelector('input[name="cover_option"][value="upload"]');
        const coverOptionExisting = document.querySelector('input[name="cover_option"][value="existing"]');
        const openMediaLibraryCreateBtn = document.getElementById('openMediaLibraryCreate');
        const existingCoverImageInput = document.getElementById('existing_cover_image');
        const selectedImageNameCreateSpan = document.getElementById('selectedImageNameCreate');

        function toggleCoverOptions() {
            if (coverOptionUpload.checked) {
                uploadArea.style.display = 'flex';
                fileInput.required = true;
                openMediaLibraryCreateBtn.disabled = true;
                existingCoverImageInput.required = false;
            } else {
                uploadArea.style.display = 'none';
                fileInput.required = false;
                openMediaLibraryCreateBtn.disabled = false;
                existingCoverImageInput.required = true;
            }
        }

        coverOptionUpload.addEventListener('change', toggleCoverOptions);
        coverOptionExisting.addEventListener('change', toggleCoverOptions);

        // Initialiser l'état au chargement
        toggleCoverOptions();

        // Intégration de la médiathèque pour la sélection d'image (création)
        openMediaLibraryCreateBtn.addEventListener('click', () => {
            // Ouvrir la médiathèque dans une popup
            const popup = window.open('/media.php?select_mode=1', 'mediaLibrary', 'width=1000,height=700,scrollbars=yes,resizable=yes');
            
            // Écouter les messages de la popup
            window.addEventListener('message', (event) => {
                if (event.origin !== window.location.origin) return;
                
                if (event.data.type === 'mediaSelected') {
                    const mediaData = event.data.media;
                    
                    // Mettre à jour le champ caché
                    existingCoverImageInput.value = mediaData.id;
                    
                    // Mettre à jour l'affichage
                    selectedImageNameCreateSpan.textContent = `Image sélectionnée: ${mediaData.original_name}`;
                    selectedImageNameCreateSpan.style.color = '#28a745';
                    
                    // Fermer la popup
                    popup.close();
                }
            });
        });

        function handleFileSelect(file) {
            // Vérifier le type de fichier
            if (!file.type.startsWith('image/')) {
                alert('Veuillez sélectionner une image valide');
                return;
            }

            // Vérifier la taille (15MB max)
            if (file.size > 15 * 1024 * 1024) {
                alert('L\'image ne doit pas dépasser 15MB');
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
