/**
 * API d'intégration pour le système de médias
 * Interface entre l'éditeur modulaire et la bibliothèque de médias
 */
class MediaLibraryAPI {
    constructor() {
        this.baseUrl = '/media.php';
        this.csrfToken = this.getCsrfToken();
    }

    /**
     * Récupérer le token CSRF
     */
    getCsrfToken() {
        const metaTag = document.querySelector('meta[name="csrf-token"]');
        return metaTag ? metaTag.getAttribute('content') : '';
    }

    /**
     * Rechercher des médias
     */
    async searchMedia(query = '', filters = {}) {
        try {
            const params = new URLSearchParams({
                action: 'search',
                q: query,
                limit: filters.limit || 20
            });

            if (filters.game_id) params.append('game_id', filters.game_id);
            if (filters.category) params.append('category', filters.category);
            if (filters.type) params.append('type', filters.type);

            const response = await fetch(`${this.baseUrl}?${params}`);
            const data = await response.json();

            if (data.success) {
                return data.medias || [];
            } else {
                throw new Error(data.error || 'Erreur lors de la recherche');
            }
        } catch (error) {
            console.error('Erreur recherche médias:', error);
            return [];
        }
    }

    /**
     * Rechercher des jeux pour l'autocomplétion
     */
    async searchGames(query = '', limit = 10) {
        try {
            const params = new URLSearchParams({
                action: 'search-games',
                q: query,
                limit: limit
            });

            const response = await fetch(`${this.baseUrl}?${params}`);
            const data = await response.json();

            if (data.success) {
                return data.games || [];
            } else {
                throw new Error(data.error || 'Erreur lors de la recherche de jeux');
            }
        } catch (error) {
            console.error('Erreur recherche jeux:', error);
            return [];
        }
    }

    /**
     * Obtenir un média par ID
     */
    async getMediaById(id) {
        try {
            const params = new URLSearchParams({
                action: 'get',
                id: id
            });

            const response = await fetch(`${this.baseUrl}?${params}`);
            const data = await response.json();

            if (data.success) {
                return data.media;
            } else {
                throw new Error(data.error || 'Média non trouvé');
            }
        } catch (error) {
            console.error('Erreur récupération média:', error);
            return null;
        }
    }

    /**
     * Obtenir tous les jeux pour les filtres
     */
    async getAllGames() {
        try {
            const params = new URLSearchParams({
                action: 'get-games',
                limit: 1000
            });

            const response = await fetch(`${this.baseUrl}?${params}`);
            const data = await response.json();

            if (data.success) {
                return data.games || [];
            } else {
                throw new Error(data.error || 'Erreur lors de la récupération des jeux');
            }
        } catch (error) {
            console.error('Erreur récupération jeux:', error);
            return [];
        }
    }

    /**
     * Ouvrir le sélecteur de médias
     */
    openMediaSelector(options = {}) {
        return new Promise((resolve, reject) => {
            const modal = this.createMediaSelectorModal(options);
            document.body.appendChild(modal);

            // Gestionnaire de sélection
            const handleSelection = (selectedMedia) => {
                document.body.removeChild(modal);
                resolve(selectedMedia);
            };

            // Gestionnaire d'annulation
            const handleCancel = () => {
                document.body.removeChild(modal);
                reject(new Error('Sélection annulée'));
            };

            // Écouter les événements
            modal.addEventListener('mediaSelected', (e) => {
                handleSelection(e.detail);
            });

            modal.addEventListener('mediaSelectorClosed', () => {
                handleCancel();
            });
        });
    }

    /**
     * Créer le modal de sélection de médias
     */
    createMediaSelectorModal(options = {}) {
        const allowMultiple = options.allowMultiple || false;
        const modal = document.createElement('div');
        modal.className = 'media-selector-modal';
        modal.innerHTML = `
            <div class="media-selector-overlay"></div>
            <div class="media-selector-container">
                <div class="media-selector-header">
                    <h3>📚 ${allowMultiple ? 'Sélectionner des médias' : 'Sélectionner un média'}</h3>
                    <button type="button" class="close-btn" data-action="close">✕</button>
                </div>
                
                <div class="media-selector-body">
                    <div class="search-section">
                        <div class="search-row">
                            <input type="text" class="search-input" placeholder="Rechercher des médias..." id="mediaSearch">
                            <select class="filter-select" id="gameFilter">
                                <option value="">Tous les jeux</option>
                            </select>
                            <select class="filter-select" id="categoryFilter">
                                <option value="">Toutes les catégories</option>
                                <option value="screenshots">Screenshots</option>
                                <option value="news">News</option>
                                <option value="events">Événements</option>
                                <option value="other">Autre</option>
                            </select>
                            <button type="button" class="search-btn" id="searchBtn">🔍</button>
                        </div>
                    </div>
                    
                    <div class="media-grid" id="mediaGrid">
                        <div class="loading">Chargement...</div>
                    </div>
                </div>
                
                <div class="media-selector-footer">
                    <button type="button" class="btn btn-secondary" data-action="cancel">Annuler</button>
                    <button type="button" class="btn btn-primary" data-action="select" disabled>
                        ${allowMultiple ? 'Sélectionner (0)' : 'Sélectionner'}
                    </button>
                </div>
            </div>
        `;

        // Ajouter les styles
        this.addMediaSelectorStyles();

        // Initialiser le sélecteur
        this.initMediaSelector(modal, options);

        return modal;
    }

    /**
     * Initialiser le sélecteur de médias
     */
    async initMediaSelector(modal, options) {
        const allowMultiple = options.allowMultiple || false;
        const searchInput = modal.querySelector('#mediaSearch');
        const gameFilter = modal.querySelector('#gameFilter');
        const categoryFilter = modal.querySelector('#categoryFilter');
        const searchBtn = modal.querySelector('#searchBtn');
        const mediaGrid = modal.querySelector('#mediaGrid');
        const selectBtn = modal.querySelector('[data-action="select"]');
        const closeBtn = modal.querySelector('[data-action="close"]');
        const cancelBtn = modal.querySelector('[data-action="cancel"]');

        let selectedMedia = allowMultiple ? [] : null;
        let currentMedias = [];

        // Charger les jeux pour le filtre
        const games = await this.getAllGames();
        games.forEach(game => {
            const option = document.createElement('option');
            option.value = game.id;
            option.textContent = game.title;
            gameFilter.appendChild(option);
        });

        // Fonction de recherche
        const performSearch = async () => {
            const query = searchInput.value;
            const gameId = gameFilter.value;
            const category = categoryFilter.value;

            mediaGrid.innerHTML = '<div class="loading">Recherche en cours...</div>';

            const medias = await this.searchMedia(query, {
                game_id: gameId || null,
                category: category || null,
                limit: 20
            });

            currentMedias = medias;
            this.renderMediaGrid(mediaGrid, medias, selectedMedia, allowMultiple, (newSelectedMedia) => {
                selectedMedia = newSelectedMedia;
                this.updateSelectButton(selectedMedia, allowMultiple);
            });
        };

        // Événements de recherche
        searchInput.addEventListener('input', debounce(performSearch, 300));
        gameFilter.addEventListener('change', performSearch);
        categoryFilter.addEventListener('change', performSearch);
        searchBtn.addEventListener('click', performSearch);

        // Événements de fermeture
        closeBtn.addEventListener('click', () => {
            modal.dispatchEvent(new CustomEvent('mediaSelectorClosed'));
        });

        cancelBtn.addEventListener('click', () => {
            modal.dispatchEvent(new CustomEvent('mediaSelectorClosed'));
        });

        // Événement de sélection
        selectBtn.addEventListener('click', () => {
            console.log('🔘 Bouton sélectionner cliqué, selectedMedia:', selectedMedia);
            if (selectedMedia) {
                console.log('✅ Déclenchement de l\'événement mediaSelected');
                modal.dispatchEvent(new CustomEvent('mediaSelected', {
                    detail: selectedMedia
                }));
            } else {
                console.log('❌ Aucun média sélectionné');
            }
        });

        // Recherche initiale
        await performSearch();
    }

    /**
     * Rendre la grille de médias
     */
    renderMediaGrid(container, medias, selectedMedia, allowMultiple = false, onSelectionChange = null) {
        if (medias.length === 0) {
            container.innerHTML = '<div class="no-results">Aucun média trouvé</div>';
            return;
        }

        container.innerHTML = medias.map(media => {
            const isSelected = allowMultiple 
                ? selectedMedia.some(m => m.id === media.id)
                : selectedMedia?.id === media.id;
            
            return `
                <div class="media-item ${isSelected ? 'selected' : ''}" 
                     data-media-id="${media.id}">
                    <div class="media-preview">
                        <img src="/public/uploads.php?file=${encodeURIComponent(media.filename)}" 
                             alt="${media.original_name}" 
                             class="media-thumbnail">
                        ${allowMultiple ? '<div class="selection-indicator">✓</div>' : ''}
                    </div>
                    <div class="media-info">
                        <div class="media-name">${media.original_name}</div>
                        <div class="media-details">
                            ${media.formatted_size} • ${media.mime_type}
                        </div>
                    </div>
                </div>
            `;
        }).join('');

        // Événements de sélection
        container.querySelectorAll('.media-item').forEach(item => {
            item.addEventListener('click', () => {
                const mediaId = item.dataset.mediaId;
                const media = medias.find(m => m.id == mediaId);
                
                if (media) {
                    console.log('🎯 Média sélectionné:', media);
                    
                    if (allowMultiple) {
                        // Sélection multiple
                        const index = selectedMedia.findIndex(m => m.id === media.id);
                        if (index > -1) {
                            selectedMedia.splice(index, 1); // Désélectionner
                        } else {
                            selectedMedia.push(media); // Sélectionner
                        }
                        this.updateMediaSelection(container, selectedMedia, allowMultiple);
                    } else {
                        // Sélection unique
                        selectedMedia = media;
                        this.updateMediaSelection(container, media);
                    }
                    
                    // Appeler le callback si fourni
                    if (onSelectionChange) {
                        onSelectionChange(selectedMedia);
                    }
                }
            });
        });
    }

    /**
     * Mettre à jour la sélection
     */
    updateMediaSelection(container, selectedMedia, allowMultiple = false) {
        container.querySelectorAll('.media-item').forEach(item => {
            const mediaId = item.dataset.mediaId;
            let isSelected = false;
            
            if (allowMultiple) {
                isSelected = selectedMedia.some(m => m.id == mediaId);
            } else {
                isSelected = selectedMedia?.id == mediaId;
            }
            
            item.classList.toggle('selected', isSelected);
        });
    }

    /**
     * Mettre à jour le bouton de sélection
     */
    updateSelectButton(selectedMedia, allowMultiple = false) {
        const selectBtn = document.querySelector('.media-selector-modal [data-action="select"]');
        if (selectBtn) {
            if (allowMultiple) {
                const count = selectedMedia.length;
                selectBtn.textContent = `Sélectionner (${count})`;
                selectBtn.disabled = count === 0;
            } else {
                selectBtn.textContent = 'Sélectionner';
                selectBtn.disabled = !selectedMedia;
            }
        }
    }

    /**
     * Ajouter les styles CSS
     */
    addMediaSelectorStyles() {
        if (document.getElementById('media-selector-styles')) return;

        const style = document.createElement('style');
        style.id = 'media-selector-styles';
        style.textContent = `
            .media-selector-modal {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                z-index: 10000;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .media-selector-overlay {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.7);
            }

            .media-selector-container {
                position: relative;
                width: 90%;
                max-width: 800px;
                max-height: 80vh;
                background: white;
                border-radius: 12px;
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
                display: flex;
                flex-direction: column;
            }

            .media-selector-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 20px;
                border-bottom: 1px solid #eee;
            }

            .media-selector-header h3 {
                margin: 0;
                color: #333;
            }

            .close-btn {
                background: none;
                border: none;
                font-size: 20px;
                cursor: pointer;
                padding: 5px;
                border-radius: 5px;
            }

            .close-btn:hover {
                background: #f5f5f5;
            }

            .media-selector-body {
                flex: 1;
                padding: 20px;
                overflow-y: auto;
            }

            .search-section {
                margin-bottom: 20px;
            }

            .search-row {
                display: grid;
                grid-template-columns: 1fr auto auto auto;
                gap: 10px;
                align-items: center;
            }

            .search-input {
                padding: 10px;
                border: 1px solid #ddd;
                border-radius: 5px;
                font-size: 14px;
            }

            .filter-select {
                padding: 10px;
                border: 1px solid #ddd;
                border-radius: 5px;
                font-size: 14px;
                min-width: 150px;
            }

            .search-btn {
                padding: 10px 15px;
                background: #007bff;
                color: white;
                border: none;
                border-radius: 5px;
                cursor: pointer;
            }

            .search-btn:hover {
                background: #0056b3;
            }

            .media-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
                gap: 15px;
            }

            .media-item {
                border: 2px solid #eee;
                border-radius: 8px;
                padding: 10px;
                cursor: pointer;
                transition: all 0.2s;
            }

            .media-item:hover {
                border-color: #007bff;
                transform: translateY(-2px);
            }

            .media-item.selected {
                border-color: #28a745;
                background: #f8fff9;
            }

            .media-item.selected .selection-indicator {
                display: block;
            }

            .selection-indicator {
                position: absolute;
                top: 5px;
                right: 5px;
                width: 20px;
                height: 20px;
                background: #28a745;
                color: white;
                border-radius: 50%;
                display: none;
                align-items: center;
                justify-content: center;
                font-size: 12px;
                font-weight: bold;
            }

            .media-preview {
                margin-bottom: 10px;
                position: relative;
            }

            .media-thumbnail {
                width: 100%;
                height: 120px;
                object-fit: cover;
                border-radius: 5px;
            }

            .media-info {
                text-align: center;
            }

            .media-name {
                font-weight: 600;
                margin-bottom: 5px;
                font-size: 12px;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .media-details {
                font-size: 11px;
                color: #666;
            }

            .media-selector-footer {
                display: flex;
                justify-content: flex-end;
                gap: 10px;
                padding: 20px;
                border-top: 1px solid #eee;
            }

            .btn {
                padding: 10px 20px;
                border: none;
                border-radius: 5px;
                cursor: pointer;
                font-size: 14px;
            }

            .btn-primary {
                background: #007bff;
                color: white;
            }

            .btn-primary:hover:not(:disabled) {
                background: #0056b3;
            }

            .btn-primary:disabled {
                background: #ccc;
                cursor: not-allowed;
            }

            .btn-secondary {
                background: #6c757d;
                color: white;
            }

            .btn-secondary:hover {
                background: #545b62;
            }

            .loading {
                text-align: center;
                padding: 40px;
                color: #666;
            }

            .no-results {
                text-align: center;
                padding: 40px;
                color: #666;
            }
        `;

        document.head.appendChild(style);
    }
}

/**
 * Fonction utilitaire pour debounce
 */
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Rendre disponible globalement
window.MediaLibraryAPI = MediaLibraryAPI;
