<?php
/**
 * Vue de gestion des médias - Admin
 */
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des médias - GameNews Belgium</title>
    <link rel="stylesheet" href="/admin.css">
    <style>
        .upload-form {
            background: var(--admin-card-bg);
            border: 1px solid var(--admin-border);
            border-radius: 12px;
            padding: var(--admin-spacing-lg);
            margin-bottom: var(--admin-spacing-lg);
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: var(--admin-spacing-md);
            margin-bottom: var(--admin-spacing-md);
        }
        
        .game-selector {
            position: relative;
        }
        
        .game-search {
            width: 100%;
            padding: 12px;
            background: var(--admin-input-bg);
            border: 1px solid var(--admin-border);
            border-radius: 5px;
            color: var(--admin-text);
            font-size: 1rem;
        }
        
        .game-search:focus {
            outline: none;
            border-color: var(--admin-primary);
            box-shadow: 0 0 0 2px rgba(255, 215, 0, 0.2);
        }
        
        .games-dropdown {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: var(--admin-light);
            border: 1px solid var(--admin-border);
            border-radius: 5px;
            max-height: 200px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
        }
        
        .game-option {
            padding: 10px 12px;
            cursor: pointer;
            border-bottom: 1px solid var(--admin-border);
            transition: background 0.2s;
        }
        
        .game-option:hover {
            background: rgba(255, 215, 0, 0.1);
        }
        
        .game-option.selected {
            background: var(--admin-primary);
            color: var(--admin-dark);
        }
        
        .game-info {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }
        
        .game-title {
            font-weight: 600;
            font-size: 0.9em;
        }
        
        .game-details {
            font-size: 0.8em;
            color: var(--admin-text-muted);
        }
        
        .category-selector {
            display: flex;
            gap: var(--admin-spacing-sm);
            flex-wrap: wrap;
        }
        
        .category-option {
            padding: 8px 16px;
            background: var(--admin-input-bg);
            border: 1px solid var(--admin-border);
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 0.9em;
        }
        
        .category-option:hover {
            background: rgba(255, 215, 0, 0.1);
        }
        
        .category-option.selected {
            background: var(--admin-primary);
            color: var(--admin-dark);
            border-color: var(--admin-primary);
        }
        
        .upload-preview {
            margin-top: var(--admin-spacing-md);
            text-align: center;
        }
        
        .preview-image {
            max-width: 200px;
            max-height: 150px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid var(--admin-border);
        }
        
        .upload-status {
            margin-top: var(--admin-spacing-sm);
            padding: var(--admin-spacing-sm);
            border-radius: 5px;
            font-size: 0.9em;
        }
        
        .upload-status.success {
            background: rgba(39, 174, 96, 0.1);
            border: 1px solid var(--admin-success);
            color: #51cf66;
        }
        
        .upload-status.error {
            background: rgba(231, 76, 60, 0.1);
            border: 1px solid var(--admin-secondary);
            color: #ff6b6b;
        }
        
        .media-filters {
            background: var(--admin-card-bg);
            border: 1px solid var(--admin-border);
            border-radius: 12px;
            padding: var(--admin-spacing-lg);
            margin-bottom: var(--admin-spacing-lg);
        }
        
        .filter-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: var(--admin-spacing-md);
            align-items: end;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Header -->
        <header class="admin-header">
            <div class="header-content">
                <h1>🖼️ Gestion des Médias</h1>
                <div class="header-actions">
                    <a href="/admin.php" class="btn btn-secondary">← Retour au tableau de bord</a>
                </div>
            </div>
        </header>

        <!-- Formulaire d'upload amélioré -->
        <div class="upload-form">
            <h3 style="color: var(--admin-primary); margin-bottom: var(--admin-spacing-md);">📁 Upload de média</h3>
            
            <form id="uploadForm" enctype="multipart/form-data">
                <div class="form-row">
                    <!-- Sélection de jeu -->
                    <div class="form-group">
                        <label for="gameSearch" class="form-label">🎮 Jeu associé (optionnel)</label>
                        <div class="game-selector">
                            <input type="text" id="gameSearch" class="game-search" 
                                   placeholder="Rechercher un jeu..." autocomplete="off">
                            <input type="hidden" id="gameId" name="game_id" value="">
                            <div class="games-dropdown" id="gamesDropdown"></div>
                        </div>
                        <div class="form-help">Laissez vide pour un média général</div>
                    </div>
                    
                    <!-- Catégorie -->
                    <div class="form-group">
                        <label class="form-label">📂 Catégorie</label>
                        <div class="category-selector">
                            <div class="category-option selected" data-category="screenshots">Screenshots</div>
                            <div class="category-option" data-category="news">News</div>
                            <div class="category-option" data-category="events">Événements</div>
                            <div class="category-option" data-category="other">Autre</div>
                        </div>
                        <input type="hidden" id="category" name="category" value="screenshots">
                    </div>
                </div>
                
                <!-- Zone d'upload -->
                <div class="upload-area" id="uploadArea">
                    <div class="upload-icon">📁</div>
                    <div class="upload-text">Glissez-déposez vos fichiers ici</div>
                    <div class="upload-hint">ou cliquez pour sélectionner des fichiers</div>
                    <input type="file" id="fileInput" class="file-input" accept="image/*" multiple>
                </div>
                
                <!-- Prévisualisation -->
                <div class="upload-preview" id="uploadPreview" style="display: none;">
                    <img id="previewImage" class="preview-image" alt="Aperçu">
                    <div class="upload-status" id="uploadStatus"></div>
                </div>
                
                <div class="upload-hint" style="text-align: center; margin-top: var(--admin-spacing-sm);">
                    Formats acceptés : JPG, PNG, WebP, GIF • Taille maximale : 4MB par fichier
                </div>
            </form>
        </div>

        <!-- Filtres -->
        <div class="media-filters">
            <h3 style="color: var(--admin-primary); margin-bottom: var(--admin-spacing-md);">🔍 Filtres</h3>
            <div class="filter-row">
                <div class="form-group">
                    <label for="filterSearch" class="form-label">Rechercher</label>
                    <input type="text" id="filterSearch" class="form-input" placeholder="Nom du fichier...">
                </div>
                <div class="form-group">
                    <label for="filterGame" class="form-label">Jeu</label>
                    <select id="filterGame" class="form-select">
                        <option value="">Tous les jeux</option>
                        <?php foreach ($games as $game): ?>
                            <option value="<?= $game->getId() ?>"><?= htmlspecialchars($game->getTitle()) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="filterCategory" class="form-label">Catégorie</label>
                    <select id="filterCategory" class="form-select">
                        <option value="">Toutes les catégories</option>
                        <option value="screenshots">Screenshots</option>
                        <option value="news">News</option>
                        <option value="events">Événements</option>
                        <option value="other">Autre</option>
                    </select>
                </div>
                <div class="form-group">
                    <button type="button" id="applyFilters" class="btn btn-primary">🔍 Appliquer</button>
                    <button type="button" id="resetFilters" class="btn btn-secondary">🔄 Réinitialiser</button>
                </div>
            </div>
        </div>

        <!-- Statistiques -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">🖼️</div>
                <div class="stat-content">
                    <div class="stat-number"><?= $totalMedias ?></div>
                    <div class="stat-label">Total des médias</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">🎮</div>
                <div class="stat-content">
                    <div class="stat-number"><?= count($games) ?></div>
                    <div class="stat-label">Jeux disponibles</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">📂</div>
                <div class="stat-content">
                    <div class="stat-number"><?= $currentPage ?></div>
                    <div class="stat-label">Page actuelle</div>
                </div>
            </div>
        </div>

        <!-- Liste des médias -->
        <div class="table-container">
            <h3 style="color: var(--admin-primary); margin-bottom: var(--admin-spacing-md);">📋 Liste des médias</h3>
            <div class="media-grid" id="mediaGrid">
                <?php foreach ($medias as $media): ?>
                <div class="media-card" data-id="<?= $media->getId() ?>" 
                     data-game="<?= $media->getGameId() ?>" 
                     data-category="<?= $media->getMediaType() ?>">
                    <div class="media-preview">
                        <?php if ($media->isImage()): ?>
                            <img src="/public/uploads.php?file=<?= urlencode($media->getFilename()) ?>" 
                                 alt="<?= htmlspecialchars($media->getOriginalName()) ?>" 
                                 class="media-image">
                        <?php else: ?>
                            <div class="icon">🎥</div>
                        <?php endif; ?>
                    </div>
                    <div class="media-info">
                        <div class="media-name"><?= htmlspecialchars($media->getOriginalName()) ?></div>
                        <div class="media-details">
                            <?= $media->getFormattedSize() ?> • <?= $media->getMimeType() ?><br>
                            <?php if ($media->getGameId()): ?>
                                <span class="badge badge-platform">Jeu associé</span><br>
                            <?php endif; ?>
                            Ajouté le <?= date('d/m/Y H:i', strtotime($media->getCreatedAt())) ?>
                        </div>
                        <div class="media-actions">
                            <button class="btn btn-sm" onclick="copyUrl('<?= $media->getUrl() ?>')">Copier URL</button>
                            <button class="btn btn-sm btn-danger" onclick="deleteMedia(<?= $media->getId() ?>)">Supprimer</button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php if ($currentPage > 1): ?>
                <a href="?page=<?= $currentPage - 1 ?>" class="page-link">← Précédent</a>
            <?php endif; ?>
            
            <?php for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++): ?>
                <a href="?page=<?= $i ?>" class="page-link <?= $i === $currentPage ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
            
            <?php if ($currentPage < $totalPages): ?>
                <a href="?page=<?= $currentPage + 1 ?>" class="page-link">Suivant →</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Loading -->
    <div class="loading" id="loading">
        <div class="upload-icon">⏳</div>
        <div class="upload-text">Upload en cours...</div>
    </div>

    <!-- Toast -->
    <div class="toast" id="toast"></div>

    <script>
        // Variables globales
        const csrfToken = '<?= Auth::generateCsrfToken() ?>';
        let selectedGame = null;
        let selectedCategory = 'screenshots';
        
        // Éléments DOM
        const uploadForm = document.getElementById('uploadForm');
        const gameSearch = document.getElementById('gameSearch');
        const gameId = document.getElementById('gameId');
        const gamesDropdown = document.getElementById('gamesDropdown');
        const categoryOptions = document.querySelectorAll('.category-option');
        const categoryInput = document.getElementById('category');
        const uploadArea = document.getElementById('uploadArea');
        const fileInput = document.getElementById('fileInput');
        const uploadPreview = document.getElementById('uploadPreview');
        const previewImage = document.getElementById('previewImage');
        const uploadStatus = document.getElementById('uploadStatus');
        const mediaGrid = document.getElementById('mediaGrid');
        const loading = document.getElementById('loading');
        const toast = document.getElementById('toast');
        
        // Gestion de la recherche de jeux
        let searchTimeout;
        gameSearch.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim();
            
            if (query.length < 2) {
                gamesDropdown.style.display = 'none';
                return;
            }
            
            searchTimeout = setTimeout(() => {
                searchGames(query);
            }, 300);
        });
        
        // Recherche de jeux
        async function searchGames(query) {
            try {
                const response = await fetch(`/media.php?action=search-games&q=${encodeURIComponent(query)}&limit=10`);
                const data = await response.json();
                
                if (data.success) {
                    displayGamesDropdown(data.games);
                }
            } catch (error) {
                console.error('Erreur recherche jeux:', error);
            }
        }
        
        // Afficher le dropdown des jeux
        function displayGamesDropdown(games) {
            gamesDropdown.innerHTML = '';
            
            if (games.length === 0) {
                gamesDropdown.innerHTML = '<div class="game-option">Aucun jeu trouvé</div>';
            } else {
                games.forEach(game => {
                    const option = document.createElement('div');
                    option.className = 'game-option';
                    option.innerHTML = `
                        <div class="game-info">
                            <div class="game-title">${game.title}</div>
                            <div class="game-details">${game.hardware || 'Aucun hardware'}</div>
                        </div>
                    `;
                    option.addEventListener('click', () => selectGame(game));
                    gamesDropdown.appendChild(option);
                });
            }
            
            gamesDropdown.style.display = 'block';
        }
        
        // Sélectionner un jeu
        function selectGame(game) {
            selectedGame = game;
            gameSearch.value = game.title;
            gameId.value = game.id;
            gamesDropdown.style.display = 'none';
            
            showToast(`Jeu sélectionné : ${game.title}`, 'success');
        }
        
        // Gestion des catégories
        categoryOptions.forEach(option => {
            option.addEventListener('click', function() {
                categoryOptions.forEach(opt => opt.classList.remove('selected'));
                this.classList.add('selected');
                selectedCategory = this.dataset.category;
                categoryInput.value = selectedCategory;
            });
        });
        
        // Gestion du drag & drop
        uploadArea.addEventListener('click', () => fileInput.click());
        
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
            handleFiles(files);
        });
        
        fileInput.addEventListener('change', (e) => {
            handleFiles(e.target.files);
        });
        
        // Gestion des fichiers
        function handleFiles(files) {
            Array.from(files).forEach(file => {
                if (file.type.startsWith('image/')) {
                    showPreview(file);
                    uploadFile(file);
                } else {
                    showToast('Format de fichier non supporté', 'error');
                }
            });
        }
        
        // Afficher la prévisualisation
        function showPreview(file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImage.src = e.target.result;
                uploadPreview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
        
        // Upload d'un fichier
        async function uploadFile(file) {
            const formData = new FormData();
            formData.append('file', file);
            formData.append('csrf_token', csrfToken);
            formData.append('game_id', gameId.value);
            formData.append('category', categoryInput.value);
            
            loading.classList.add('show');
            uploadStatus.textContent = 'Upload en cours...';
            uploadStatus.className = 'upload-status';
            
            try {
                const response = await fetch('/media.php?action=upload', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    uploadStatus.textContent = 'Upload réussi !';
                    uploadStatus.className = 'upload-status success';
                    showToast('Fichier uploadé avec succès !', 'success');
                    
                    // Recharger la page après un délai
                    setTimeout(() => location.reload(), 1500);
                } else {
                    uploadStatus.textContent = result.error || 'Erreur lors de l\'upload';
                    uploadStatus.className = 'upload-status error';
                    showToast(result.error || 'Erreur lors de l\'upload', 'error');
                }
            } catch (error) {
                uploadStatus.textContent = 'Erreur de connexion';
                uploadStatus.className = 'upload-status error';
                showToast('Erreur de connexion', 'error');
            } finally {
                loading.classList.remove('show');
            }
        }
        
        // Supprimer un média
        async function deleteMedia(id) {
            if (!confirm('Êtes-vous sûr de vouloir supprimer ce média ?')) {
                return;
            }
            
            const formData = new FormData();
            formData.append('csrf_token', csrfToken);
            
            try {
                const response = await fetch(`/media.php?action=delete&id=${id}`, {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showToast('Média supprimé avec succès !', 'success');
                    document.querySelector(`[data-id="${id}"]`).remove();
                } else {
                    showToast(result.error || 'Erreur lors de la suppression', 'error');
                }
            } catch (error) {
                showToast('Erreur de connexion', 'error');
            }
        }
        
        // Copier l'URL d'un média
        function copyUrl(url) {
            navigator.clipboard.writeText(url).then(() => {
                showToast('URL copiée dans le presse-papiers !', 'success');
            }).catch(() => {
                showToast('Erreur lors de la copie', 'error');
            });
        }
        
        // Filtres
        document.getElementById('applyFilters').addEventListener('click', applyFilters);
        document.getElementById('resetFilters').addEventListener('click', resetFilters);
        
        function applyFilters() {
            const search = document.getElementById('filterSearch').value.toLowerCase();
            const game = document.getElementById('filterGame').value;
            const category = document.getElementById('filterCategory').value;
            
            const mediaCards = document.querySelectorAll('.media-card');
            
            mediaCards.forEach(card => {
                const name = card.querySelector('.media-name').textContent.toLowerCase();
                const cardGame = card.dataset.game;
                const cardCategory = card.dataset.category;
                
                let show = true;
                
                if (search && !name.includes(search)) show = false;
                if (game && cardGame !== game) show = false;
                if (category && cardCategory !== category) show = false;
                
                card.style.display = show ? 'block' : 'none';
            });
        }
        
        function resetFilters() {
            document.getElementById('filterSearch').value = '';
            document.getElementById('filterGame').value = '';
            document.getElementById('filterCategory').value = '';
            
            const mediaCards = document.querySelectorAll('.media-card');
            mediaCards.forEach(card => card.style.display = 'block');
        }
        
        // Afficher un toast
        function showToast(message, type = 'success') {
            toast.textContent = message;
            
            if (type === 'success') {
                toast.style.background = 'rgba(39, 174, 96, 0.9)';
                toast.style.border = '1px solid var(--admin-success)';
            } else if (type === 'error') {
                toast.style.background = 'rgba(231, 76, 60, 0.9)';
                toast.style.border = '1px solid var(--admin-secondary)';
            } else {
                toast.style.background = 'rgba(52, 152, 219, 0.9)';
                toast.style.border = '1px solid var(--admin-info)';
            }
            
            toast.style.opacity = '1';
            toast.style.visibility = 'visible';
            
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.visibility = 'hidden';
            }, 3000);
        }
        
        // Fermer le dropdown en cliquant ailleurs
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.game-selector')) {
                gamesDropdown.style.display = 'none';
            }
        });
    </script>
</body>
</html>
