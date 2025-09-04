/**
 * API d'intégration pour le système de médias - Version améliorée
 * Interface entre l'éditeur modulaire et la bibliothèque de médias
 * Avec gestion d'erreurs avancée et notifications contextuelles
 */
class MediaLibraryAPI {
    constructor() {
        this.baseUrl = '/media.php';
        this.csrfToken = this.getCsrfToken();
        this.errorHandler = new MediaErrorHandler();
        this.notificationManager = new NotificationManager();
    }

    /**
     * Récupérer le token CSRF
     */
    getCsrfToken() {
        const metaTag = document.querySelector('meta[name="csrf-token"]');
        return metaTag ? metaTag.getAttribute('content') : '';
    }

    /**
     * Rechercher des médias avec gestion d'erreurs avancée
     */
    async searchMedia(query = '', filters = {}) {
        try {
            const params = new URLSearchParams({
                action: 'search',
                q: query,
                limit: filters.limit || 100
            });

            if (filters.game_id) params.append('game_id', filters.game_id);
            if (filters.category) params.append('category', filters.category);
            if (filters.type) params.append('type', filters.type);

            console.log('🔍 Recherche de médias avec paramètres:', Object.fromEntries(params));
            
            const response = await this.makeRequest(`${this.baseUrl}?${params}`);
            
            if (response.success) {
                console.log(`✅ ${response.medias.length} médias trouvés sur ${response.total} total`);
                return response.medias || [];
            } else {
                throw new Error(response.error?.message || 'Erreur lors de la recherche');
            }
        } catch (error) {
            console.error('❌ Erreur recherche médias:', error);
            this.errorHandler.handleError(error, 'search');
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

            const response = await this.makeRequest(`${this.baseUrl}?${params}`);
            
            if (response.success) {
                return response.games || [];
            } else {
                throw new Error(response.error?.message || 'Erreur lors de la recherche de jeux');
            }
        } catch (error) {
            console.error('❌ Erreur recherche jeux:', error);
            this.errorHandler.handleError(error, 'game_search');
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

            const response = await this.makeRequest(`${this.baseUrl}?${params}`);
            
            if (response.success) {
                return response.media;
            } else {
                throw new Error(response.error?.message || 'Média non trouvé');
            }
        } catch (error) {
            console.error('❌ Erreur récupération média:', error);
            this.errorHandler.handleError(error, 'get_media');
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

            const response = await this.makeRequest(`${this.baseUrl}?${params}`);
            
            if (response.success) {
                return response.games || [];
            } else {
                throw new Error(response.error?.message || 'Erreur lors de la récupération des jeux');
            }
        } catch (error) {
            console.error('❌ Erreur récupération jeux:', error);
            this.errorHandler.handleError(error, 'get_games');
            return [];
        }
    }

    /**
     * Effectuer une requête HTTP avec gestion d'erreurs
     */
    async makeRequest(url, options = {}) {
        try {
            const response = await fetch(url, {
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                ...options
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }

            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                throw new Error('Réponse non-JSON reçue du serveur');
            }

            return await response.json();
        } catch (error) {
            if (error.name === 'TypeError' && error.message.includes('fetch')) {
                throw new Error('Erreur de connexion au serveur');
            }
            throw error;
        }
    }

    /**
     * Ouvrir le sélecteur de médias avec gestion d'erreurs
     */
    openMediaSelector(options = {}) {
        return new Promise((resolve, reject) => {
            try {
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

                // Gestionnaire d'erreur
                const handleError = (error) => {
                    document.body.removeChild(modal);
                    this.errorHandler.handleError(error, 'media_selector');
                    reject(error);
                };

                // Écouter les événements
                modal.addEventListener('mediaSelected', (e) => {
                    handleSelection(e.detail);
                });

                modal.addEventListener('mediaSelectorClosed', () => {
                    handleCancel();
                });

                modal.addEventListener('mediaSelectorError', (e) => {
                    handleError(e.detail);
                });

            } catch (error) {
                this.errorHandler.handleError(error, 'modal_creation');
                reject(error);
            }
        });
    }

    /**
     * Créer le modal de sélection de médias amélioré
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
                        <div class="search-help">
                            <i class="fas fa-info-circle"></i>
                            Utilisez les filtres pour affiner votre recherche
                        </div>
                    </div>
                    
                    <div class="media-grid" id="mediaGrid">
                        <div class="loading">
                            <div class="loading-spinner"></div>
                            <div>Chargement...</div>
                        </div>
                    </div>
                </div>
                
                <div class="media-selector-footer">
                    <div class="media-count">
                        <span id="mediaCount">0</span> médias affichés
                    </div>
                    <div class="footer-actions">
                        <button type="button" class="btn btn-secondary" id="loadMoreBtn" style="display: none;">
                            📥 Charger plus
                        </button>
                        <button type="button" class="btn btn-secondary" data-action="cancel">Annuler</button>
                        <button type="button" class="btn btn-primary" data-action="select" disabled>
                            ${allowMultiple ? 'Sélectionner (0)' : 'Sélectionner'}
                        </button>
                    </div>
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
     * Initialiser le sélecteur de médias avec gestion d'erreurs
     */
    async initMediaSelector(modal, options) {
        try {
            const allowMultiple = options.allowMultiple || false;
            const searchInput = modal.querySelector('#mediaSearch');
            const gameFilter = modal.querySelector('#gameFilter');
            const categoryFilter = modal.querySelector('#categoryFilter');
            const searchBtn = modal.querySelector('#searchBtn');
            const mediaGrid = modal.querySelector('#mediaGrid');
            const selectBtn = modal.querySelector('[data-action="select"]');
            const closeBtn = modal.querySelector('[data-action="close"]');
            const cancelBtn = modal.querySelector('[data-action="cancel"]');
            const loadMoreBtn = modal.querySelector('#loadMoreBtn');
            const mediaCountSpan = modal.querySelector('#mediaCount');

            let selectedMedia = allowMultiple ? [] : null;
            let currentMedias = [];
            let currentPage = 1;
            let totalMedias = 0;
            let isLoading = false;

            // Charger les jeux pour le filtre
            try {
                const games = await this.getAllGames();
                games.forEach(game => {
                    const option = document.createElement('option');
                    option.value = game.id;
                    option.textContent = game.title;
                    gameFilter.appendChild(option);
                });
            } catch (error) {
                console.warn('⚠️ Impossible de charger la liste des jeux:', error);
                // Continuer sans les jeux
            }

            // Fonction de recherche avec gestion d'erreurs
            const performSearch = async () => {
                if (isLoading) return;
                
                try {
                    isLoading = true;
                    const query = searchInput.value;
                    const gameId = gameFilter.value;
                    const category = categoryFilter.value;

                    mediaGrid.innerHTML = '<div class="loading"><div class="loading-spinner"></div><div>Recherche en cours...</div></div>';
                    loadMoreBtn.style.display = 'none';

                    console.log('🔍 Recherche avec filtres:', { query, gameId, category });

                    const medias = await this.searchMedia(query, {
                        game_id: gameId || null,
                        category: category || null,
                        limit: 100,
                        page: currentPage
                    });

                    currentMedias = medias;
                    totalMedias = medias.length;
                    mediaCountSpan.textContent = totalMedias;
                    
                    this.renderMediaGrid(mediaGrid, medias, selectedMedia, allowMultiple, (newSelectedMedia) => {
                        selectedMedia = newSelectedMedia;
                        this.updateSelectButton(selectedMedia, allowMultiple);
                    });

                    if (totalMedias > 100) {
                        loadMoreBtn.style.display = 'block';
                    } else {
                        loadMoreBtn.style.display = 'none';
                    }

                } catch (error) {
                    console.error('❌ Erreur lors de la recherche:', error);
                    mediaGrid.innerHTML = `
                        <div class="error-state">
                            <i class="fas fa-exclamation-triangle"></i>
                            <div>Erreur lors de la recherche</div>
                            <button type="button" class="btn btn-secondary" onclick="performSearch()">
                                Réessayer
                            </button>
                        </div>
                    `;
                    this.errorHandler.handleError(error, 'media_search');
                } finally {
                    isLoading = false;
                }
            };

            // Événements de recherche avec debounce
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

            // Événement de chargement plus
            loadMoreBtn.addEventListener('click', async () => {
                if (isLoading) return;
                currentPage++;
                await performSearch();
            });

            // Événement de sélection
            selectBtn.addEventListener('click', () => {
                if (selectedMedia) {
                    modal.dispatchEvent(new CustomEvent('mediaSelected', {
                        detail: selectedMedia
                    }));
                }
            });

            // Recherche initiale
            await performSearch();

        } catch (error) {
            console.error('❌ Erreur lors de l\'initialisation du sélecteur:', error);
            modal.dispatchEvent(new CustomEvent('mediaSelectorError', {
                detail: error
            }));
        }
    }

    /**
     * Rendre la grille de médias avec gestion d'erreurs
     */
    renderMediaGrid(container, medias, selectedMedia, allowMultiple = false, onSelectionChange = null) {
        if (medias.length === 0) {
            container.innerHTML = `
                <div class="no-results">
                    <i class="fas fa-search"></i>
                    <div>Aucun média trouvé</div>
                    <div class="no-results-hint">Essayez de modifier vos critères de recherche</div>
                </div>
            `;
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
                             class="media-thumbnail"
                             loading="lazy"
                             onerror="this.src='/public/images/default-article.jpg'">
                        ${allowMultiple ? '<div class="selection-indicator">✓</div>' : ''}
                    </div>
                    <div class="media-info">
                        <div class="media-name" title="${media.original_name}">${media.original_name}</div>
                        <div class="media-details">
                            <i class="fas fa-file"></i> ${media.formatted_size} • ${media.mime_type}
                        </div>
                        ${media.game_id ? `<div class="media-game"><i class="fas fa-gamepad"></i> Jeu associé</div>` : ''}
                    </div>
                </div>
            `;
        }).join('');

        // Événements de sélection avec gestion d'erreurs
        container.querySelectorAll('.media-item').forEach(item => {
            item.addEventListener('click', () => {
                try {
                    const mediaId = item.dataset.mediaId;
                    const media = medias.find(m => m.id == mediaId);
                    
                    if (media) {
                        console.log('🎯 Média sélectionné:', media);
                        
                        if (allowMultiple) {
                            const index = selectedMedia.findIndex(m => m.id === media.id);
                            if (index > -1) {
                                selectedMedia.splice(index, 1);
                            } else {
                                selectedMedia.push(media);
                            }
                            this.updateMediaSelection(container, selectedMedia, allowMultiple);
                        } else {
                            selectedMedia = media;
                            this.updateMediaSelection(container, media);
                        }
                        
                        if (onSelectionChange) {
                            onSelectionChange(selectedMedia);
                        }
                    }
                } catch (error) {
                    console.error('❌ Erreur lors de la sélection:', error);
                    this.errorHandler.handleError(error, 'media_selection');
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
     * Ajouter les styles CSS améliorés
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
                font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            }

            .media-selector-overlay {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.8);
                backdrop-filter: blur(10px);
            }

            .media-selector-container {
                position: relative;
                width: 90%;
                max-width: 900px;
                max-height: 85vh;
                background: white;
                border-radius: 20px;
                box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25);
                display: flex;
                flex-direction: column;
                overflow: hidden;
            }

            .media-selector-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 25px 30px;
                border-bottom: 1px solid #eee;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
            }

            .media-selector-header h3 {
                margin: 0;
                font-size: 1.5rem;
                font-weight: 600;
            }

            .close-btn {
                background: rgba(255, 255, 255, 0.2);
                border: none;
                color: white;
                font-size: 20px;
                cursor: pointer;
                padding: 10px;
                border-radius: 50%;
                transition: all 0.3s ease;
            }

            .close-btn:hover {
                background: rgba(255, 255, 255, 0.3);
                transform: scale(1.1);
            }

            .media-selector-body {
                flex: 1;
                padding: 30px;
                overflow-y: auto;
            }

            .search-section {
                margin-bottom: 30px;
            }

            .search-row {
                display: grid;
                grid-template-columns: 1fr auto auto auto;
                gap: 15px;
                align-items: center;
                margin-bottom: 15px;
            }

            .search-input {
                padding: 15px 20px;
                border: 2px solid #e1e5e9;
                border-radius: 12px;
                font-size: 16px;
                transition: all 0.3s ease;
            }

            .search-input:focus {
                outline: none;
                border-color: #667eea;
                box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            }

            .filter-select {
                padding: 15px 20px;
                border: 2px solid #e1e5e9;
                border-radius: 12px;
                font-size: 16px;
                min-width: 180px;
                background: white;
                transition: all 0.3s ease;
            }

            .filter-select:focus {
                outline: none;
                border-color: #667eea;
                box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            }

            .search-btn {
                padding: 15px 25px;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                border: none;
                border-radius: 12px;
                cursor: pointer;
                font-size: 16px;
                font-weight: 600;
                transition: all 0.3s ease;
            }

            .search-btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
            }

            .search-help {
                font-size: 14px;
                color: #666;
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .media-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
                gap: 20px;
            }

            .media-item {
                border: 2px solid #e1e5e9;
                border-radius: 15px;
                padding: 15px;
                cursor: pointer;
                transition: all 0.3s ease;
                background: white;
                position: relative;
                overflow: hidden;
            }

            .media-item:hover {
                border-color: #667eea;
                transform: translateY(-5px);
                box-shadow: 0 15px 30px rgba(102, 126, 234, 0.15);
            }

            .media-item.selected {
                border-color: #28a745;
                background: linear-gradient(135deg, #f8fff9 0%, #e8f5e8 100%);
                transform: translateY(-5px);
                box-shadow: 0 15px 30px rgba(40, 167, 69, 0.15);
            }

            .media-item.selected .selection-indicator {
                display: flex;
            }

            .selection-indicator {
                position: absolute;
                top: 10px;
                right: 10px;
                width: 25px;
                height: 25px;
                background: #28a745;
                color: white;
                border-radius: 50%;
                display: none;
                align-items: center;
                justify-content: center;
                font-size: 14px;
                font-weight: bold;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            }

            .media-preview {
                margin-bottom: 15px;
                position: relative;
                border-radius: 10px;
                overflow: hidden;
            }

            .media-thumbnail {
                width: 100%;
                height: 140px;
                object-fit: cover;
                border-radius: 10px;
                transition: transform 0.3s ease;
            }

            .media-item:hover .media-thumbnail {
                transform: scale(1.05);
            }

            .media-info {
                text-align: center;
            }

            .media-name {
                font-weight: 600;
                margin-bottom: 8px;
                font-size: 14px;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
                color: #333;
            }

            .media-details {
                font-size: 12px;
                color: #666;
                margin-bottom: 8px;
            }

            .media-game {
                font-size: 12px;
                color: #667eea;
                font-weight: 500;
            }

            .media-selector-footer {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 25px 30px;
                border-top: 1px solid #eee;
                background: #f8f9fa;
            }

            .media-count {
                font-size: 14px;
                color: #666;
                font-weight: 500;
            }

            .footer-actions {
                display: flex;
                gap: 15px;
            }

            .btn {
                padding: 12px 24px;
                border: none;
                border-radius: 10px;
                cursor: pointer;
                font-size: 14px;
                font-weight: 600;
                transition: all 0.3s ease;
                text-decoration: none;
                display: inline-flex;
                align-items: center;
                gap: 8px;
            }

            .btn-primary {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
            }

            .btn-primary:hover:not(:disabled) {
                transform: translateY(-2px);
                box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
            }

            .btn-primary:disabled {
                background: #ccc;
                cursor: not-allowed;
                transform: none;
                box-shadow: none;
            }

            .btn-secondary {
                background: #6c757d;
                color: white;
            }

            .btn-secondary:hover {
                background: #5a6268;
                transform: translateY(-2px);
            }

            .loading {
                text-align: center;
                padding: 60px 20px;
                color: #666;
            }

            .loading-spinner {
                width: 40px;
                height: 40px;
                border: 4px solid #f3f3f3;
                border-top: 4px solid #667eea;
                border-radius: 50%;
                animation: spin 1s linear infinite;
                margin: 0 auto 20px;
            }

            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }

            .no-results {
                text-align: center;
                padding: 60px 20px;
                color: #666;
            }

            .no-results i {
                font-size: 3rem;
                margin-bottom: 20px;
                opacity: 0.5;
            }

            .no-results-hint {
                font-size: 14px;
                color: #999;
                margin-top: 10px;
            }

            .error-state {
                text-align: center;
                padding: 60px 20px;
                color: #dc3545;
            }

            .error-state i {
                font-size: 3rem;
                margin-bottom: 20px;
            }

            /* Responsive */
            @media (max-width: 768px) {
                .media-selector-container {
                    width: 95%;
                    max-height: 90vh;
                }

                .search-row {
                    grid-template-columns: 1fr;
                    gap: 10px;
                }

                .media-grid {
                    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
                    gap: 15px;
                }

                .media-selector-header,
                .media-selector-body,
                .media-selector-footer {
                    padding: 20px;
                }
            }
        `;

        document.head.appendChild(style);
    }
}

/**
 * Gestionnaire d'erreurs pour l'API des médias
 */
class MediaErrorHandler {
    constructor() {
        this.errorTypes = {
            'search': 'Recherche de médias',
            'game_search': 'Recherche de jeux',
            'get_media': 'Récupération de média',
            'get_games': 'Récupération de jeux',
            'media_selector': 'Sélecteur de médias',
            'modal_creation': 'Création de modal',
            'media_search': 'Recherche dans le sélecteur',
            'media_selection': 'Sélection de média'
        };
    }

    /**
     * Gérer une erreur avec contexte
     */
    handleError(error, context) {
        const contextName = this.errorTypes[context] || 'Opération';
        const errorMessage = this.formatErrorMessage(error, contextName);
        
        console.error(`❌ ${contextName}:`, error);
        
        // Afficher une notification d'erreur
        if (window.NotificationManager) {
            window.NotificationManager.showError(errorMessage);
        }
        
        // Envoyer l'erreur à un service de monitoring si disponible
        this.logError(error, context);
    }

    /**
     * Formater le message d'erreur pour l'utilisateur
     */
    formatErrorMessage(error, contextName) {
        if (error.message.includes('connexion')) {
            return `Erreur de connexion lors de ${contextName.toLowerCase()}. Vérifiez votre connexion internet.`;
        }
        
        if (error.message.includes('JSON')) {
            return `Erreur de communication avec le serveur lors de ${contextName.toLowerCase()}.`;
        }
        
        if (error.message.includes('HTTP')) {
            return `Erreur serveur lors de ${contextName.toLowerCase()}. Réessayez dans quelques minutes.`;
        }
        
        return `Une erreur est survenue lors de ${contextName.toLowerCase()}: ${error.message}`;
    }

    /**
     * Logger l'erreur pour le debugging
     */
    logError(error, context) {
        const errorLog = {
            timestamp: new Date().toISOString(),
            context: context,
            message: error.message,
            stack: error.stack,
            userAgent: navigator.userAgent,
            url: window.location.href
        };
        
        // Envoyer à un service de monitoring ou stocker localement
        console.group('📊 Erreur MediaLibrary');
        console.log('Contexte:', context);
        console.log('Message:', error.message);
        console.log('Stack:', error.stack);
        console.log('Timestamp:', errorLog.timestamp);
        console.groupEnd();
    }
}

/**
 * Gestionnaire de notifications pour l'API des médias
 */
class NotificationManager {
    constructor() {
        this.notifications = [];
        this.container = this.createContainer();
    }

    /**
     * Créer le conteneur de notifications
     */
    createContainer() {
        const container = document.createElement('div');
        container.id = 'media-notifications';
        container.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 10002;
            display: flex;
            flex-direction: column;
            gap: 10px;
            max-width: 400px;
        `;
        document.body.appendChild(container);
        return container;
    }

    /**
     * Afficher une notification de succès
     */
    showSuccess(message, duration = 4000) {
        this.showNotification(message, 'success', duration);
    }

    /**
     * Afficher une notification d'erreur
     */
    showError(message, duration = 6000) {
        this.showNotification(message, 'error', duration);
    }

    /**
     * Afficher une notification d'avertissement
     */
    showWarning(message, duration = 5000) {
        this.showNotification(message, 'warning', duration);
    }

    /**
     * Afficher une notification d'information
     */
    showInfo(message, duration = 4000) {
        this.showNotification(message, 'info', duration);
    }

    /**
     * Afficher une notification
     */
    showNotification(message, type = 'info', duration = 4000) {
        const notification = document.createElement('div');
        notification.className = `media-notification ${type}`;
        
        const icon = this.getIconForType(type);
        
        notification.innerHTML = `
            <div class="notification-icon">${icon}</div>
            <div class="notification-content">
                <div class="notification-message">${message}</div>
            </div>
            <button class="notification-close">×</button>
        `;

        // Styles de la notification
        notification.style.cssText = `
            background: ${this.getBackgroundForType(type)};
            color: white;
            padding: 15px 20px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            display: flex;
            align-items: center;
            gap: 15px;
            transform: translateX(100%);
            opacity: 0;
            transition: all 0.3s ease;
            max-width: 100%;
        `;

        // Ajouter au conteneur
        this.container.appendChild(notification);

        // Animation d'entrée
        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
            notification.style.opacity = '1';
        }, 100);

        // Gestionnaire de fermeture
        const closeBtn = notification.querySelector('.notification-close');
        closeBtn.addEventListener('click', () => {
            this.removeNotification(notification);
        });

        // Auto-fermeture
        if (duration > 0) {
            setTimeout(() => {
                this.removeNotification(notification);
            }, duration);
        }

        // Stocker la référence
        this.notifications.push(notification);
    }

    /**
     * Supprimer une notification
     */
    removeNotification(notification) {
        notification.style.transform = 'translateX(100%)';
        notification.style.opacity = '0';
        
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
            
            const index = this.notifications.indexOf(notification);
            if (index > -1) {
                this.notifications.splice(index, 1);
            }
        }, 300);
    }

    /**
     * Obtenir l'icône pour le type de notification
     */
    getIconForType(type) {
        const icons = {
            success: '✅',
            error: '❌',
            warning: '⚠️',
            info: 'ℹ️'
        };
        return icons[type] || icons.info;
    }

    /**
     * Obtenir la couleur de fond pour le type de notification
     */
    getBackgroundForType(type) {
        const backgrounds = {
            success: 'linear-gradient(135deg, #28a745 0%, #20c997 100%)',
            error: 'linear-gradient(135deg, #dc3545 0%, #fd7e14 100%)',
            warning: 'linear-gradient(135deg, #ffc107 0%, #fd7e14 100%)',
            info: 'linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%)'
        };
        return backgrounds[type] || backgrounds.info;
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
window.MediaErrorHandler = MediaErrorHandler;
window.NotificationManager = NotificationManager;
