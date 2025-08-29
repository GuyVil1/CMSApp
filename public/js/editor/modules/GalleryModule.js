/**
 * Module Galerie - Gestion des galeries d'images multiples
 */
class GalleryModule extends BaseModule {
    constructor(editor) {
        super('gallery', editor);
        this.galleryData = {
            images: [], // Array of {id, file, url, title, description, alt}
            layout: 'grid', // 'grid', 'carousel', 'masonry', 'slider'
            columns: 3, // 2, 3, 4, 5
            spacing: 'medium', // 'small', 'medium', 'large'
            showTitles: true,
            showDescriptions: true,
            autoplay: false, // Pour carousel/slider
            autoplaySpeed: 3000,
            lightbox: true, // Ouvrir en grand au clic
            captions: 'overlay' // 'overlay', 'below', 'none'
        };
        this.isUploading = false; // Flag pour éviter les uploads multiples
    }

    render() {
        this.element.innerHTML = `
            <div class="module-header">
                <span class="module-type">📸 Galerie</span>
                <div class="module-actions">
                    <button type="button" class="module-action" data-action="move-left">⬅️</button>
                    <button type="button" class="module-action" data-action="move-right">➡️</button>
                    <button type="button" class="module-action" data-action="delete">🗑️</button>
                </div>
            </div>
            <div class="module-content">
                <div class="gallery-placeholder">
                    <div class="gallery-icon">📸</div>
                    <div class="gallery-text">Cliquez pour ajouter des images</div>
                    <div class="gallery-hint">Glissez-déposez ou sélectionnez plusieurs images</div>
                </div>
            </div>
        `;
        
        this.bindEvents();
    }

    bindEvents() {
        // Appeler d'abord la méthode parente pour conserver le drag & drop du module
        super.bindEvents();
        
        // Ajouter les événements spécifiques à la galerie
        const content = this.element.querySelector('.module-content');
        if (!content) return;

        // Clic pour éditer
        content.addEventListener('click', (e) => {
            if (e.target.closest('input, textarea, button, .gallery-item')) {
                return;
            }
            this.showGalleryEditor();
        });

        // Drag and drop pour les images
        content.addEventListener('dragover', (e) => {
            e.preventDefault();
            content.classList.add('drag-over');
        });

        content.addEventListener('dragleave', (e) => {
            e.preventDefault();
            content.classList.remove('drag-over');
        });

        content.addEventListener('drop', (e) => {
            e.preventDefault();
            content.classList.remove('drag-over');
            
            const files = Array.from(e.dataTransfer.files);
            const imageFiles = files.filter(file => file.type.startsWith('image/'));
            
            if (imageFiles.length > 0) {
                this.addImages(imageFiles);
            }
        });
    }

    showGalleryEditor() {
        const content = this.element.querySelector('.module-content');
        if (!content) return;

        content.innerHTML = `
            <div class="gallery-editor">
                <div class="gallery-upload-section">
                    <h4>📸 Ajouter des images</h4>
                    <div class="upload-methods">
                        <div class="upload-method">
                            <label for="gallery-file-input" class="upload-btn">
                                <span class="icon">📁</span>
                                Upload direct
                            </label>
                            <input type="file" id="gallery-file-input" multiple accept="image/*" style="display: none;">
                        </div>
                        <div class="upload-method">
                            <button type="button" class="upload-btn library-btn" id="gallery-library-btn">
                                <span class="icon">📚</span>
                                Bibliothèque de médias
                            </button>
                        </div>
                        <div class="upload-method">
                            <div class="drag-drop-zone">
                                <span class="icon">⬇️</span>
                                Glissez-déposez vos images ici
                            </div>
                        </div>
                    </div>
                </div>

                <div class="gallery-images-section">
                    <h4>🖼️ Images de la galerie (${this.galleryData.images.length})</h4>
                    <div class="gallery-images-list">
                        ${this.galleryData.images.length === 0 ? 
                            '<p class="no-images">Aucune image ajoutée</p>' : 
                            this.galleryData.images.map((image, index) => this.renderImageItem(image, index)).join('')
                        }
                    </div>
                </div>

                <div class="gallery-actions">
                    <button type="button" class="gallery-save-btn">💾 Sauvegarder</button>
                    <button type="button" class="gallery-cancel-btn">❌ Annuler</button>
                </div>
            </div>
        `;

        // Événements pour l'upload de fichiers
        const fileInput = content.querySelector('#gallery-file-input');
        fileInput?.addEventListener('change', (e) => {
            const files = Array.from(e.target.files);
            this.addImages(files);
        });

        // Événement pour la bibliothèque de médias
        const libraryBtn = content.querySelector('#gallery-library-btn');
        libraryBtn?.addEventListener('click', () => {
            this.openMediaLibrary();
        });

        // Événements des boutons
        const saveBtn = content.querySelector('.gallery-save-btn');
        const cancelBtn = content.querySelector('.gallery-cancel-btn');

        saveBtn?.addEventListener('click', () => {
            this.saveGallery();
        });

        cancelBtn?.addEventListener('click', () => {
            this.displayGallery();
        });

        // Drag and drop dans l'éditeur
        const dragZone = content.querySelector('.drag-drop-zone');
        if (dragZone) {
            dragZone.addEventListener('dragover', (e) => {
                e.preventDefault();
                dragZone.classList.add('drag-over');
            });

            dragZone.addEventListener('dragleave', (e) => {
                e.preventDefault();
                dragZone.classList.remove('drag-over');
            });

            dragZone.addEventListener('drop', (e) => {
                e.preventDefault();
                dragZone.classList.remove('drag-over');
                
                const files = Array.from(e.dataTransfer.files);
                const imageFiles = files.filter(file => file.type.startsWith('image/'));
                
                if (imageFiles.length > 0) {
                    this.addImages(imageFiles);
                }
            });
        }
    }

    renderImageItem(image, index) {
        return `
            <div class="gallery-image-item" data-image-id="${image.id}">
                <div class="image-preview">
                    <img src="${image.url}" alt="${image.alt || ''}" />
                    <div class="image-actions">
                        <button type="button" class="image-action-btn" data-action="edit" data-index="${index}">✏️</button>
                        <button type="button" class="image-action-btn" data-action="delete" data-index="${index}">🗑️</button>
                        <button type="button" class="image-action-btn" data-action="move-up" data-index="${index}" ${index === 0 ? 'disabled' : ''}>⬆️</button>
                        <button type="button" class="image-action-btn" data-action="move-down" data-index="${index}" ${index === this.galleryData.images.length - 1 ? 'disabled' : ''}>⬇️</button>
                    </div>
                </div>
                <div class="image-info">
                    <input type="text" class="image-title-input" placeholder="Titre de l'image" value="${image.title || ''}" data-index="${index}">
                    <textarea class="image-description-input" placeholder="Description (optionnelle)" rows="2" data-index="${index}">${image.description || ''}</textarea>
                    <input type="text" class="image-alt-input" placeholder="Texte alternatif" value="${image.alt || ''}" data-index="${index}">
                </div>
            </div>
        `;
    }

    addImages(files) {
        if (this.isUploading) {
            console.log('Upload déjà en cours, ignoré');
            return;
        }
        
        this.isUploading = true;
        console.log('Début de l\'upload de', files.length, 'images');
        
        let processedFiles = 0;
        const totalFiles = files.length;
        
        files.forEach(file => {
            const reader = new FileReader();
            reader.onload = (e) => {
                const imageId = 'img_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
                const newImage = {
                    id: imageId,
                    file: file,
                    url: e.target.result,
                    title: file.name.replace(/\.[^/.]+$/, ""), // Nom du fichier sans extension
                    description: '',
                    alt: ''
                };
                
                this.galleryData.images.push(newImage);
                processedFiles++;
                
                // Mettre à jour l'éditeur seulement quand tous les fichiers sont traités
                if (processedFiles === totalFiles) {
                    this.updateGalleryEditor();
                    this.isUploading = false;
                    console.log('Upload terminé pour', totalFiles, 'images');
                }
            };
            reader.readAsDataURL(file);
        });
    }

    updateGalleryEditor() {
        const imagesList = this.element.querySelector('.gallery-images-list');
        if (!imagesList) return;

        if (this.galleryData.images.length === 0) {
            imagesList.innerHTML = '<p class="no-images">Aucune image ajoutée</p>';
        } else {
            imagesList.innerHTML = this.galleryData.images.map((image, index) => this.renderImageItem(image, index)).join('');
        }

        // Mettre à jour le compteur
        const counter = this.element.querySelector('.gallery-images-section h4');
        if (counter) {
            counter.textContent = `🖼️ Images de la galerie (${this.galleryData.images.length})`;
        }

        // Re-binder les événements des boutons d'action
        this.bindImageItemEvents();
    }

    bindImageItemEvents() {
        const content = this.element.querySelector('.module-content');
        if (!content) return;

        // Nettoyer les anciens événements en supprimant les listeners existants
        this.cleanupImageItemEvents();

        // Événements des boutons d'action
        this.imageItemClickHandler = (e) => {
            const actionBtn = e.target.closest('.image-action-btn');
            if (!actionBtn) return;

            const action = actionBtn.dataset.action;
            const index = parseInt(actionBtn.dataset.index);

            switch (action) {
                case 'edit':
                    this.editImage(index);
                    break;
                case 'delete':
                    this.deleteImage(index);
                    break;
                case 'move-up':
                    this.moveImage(index, -1);
                    break;
                case 'move-down':
                    this.moveImage(index, 1);
                    break;
            }
        };

        content.addEventListener('click', this.imageItemClickHandler);

        // Événements des champs de saisie
        const inputs = content.querySelectorAll('.image-title-input, .image-description-input, .image-alt-input');
        this.imageInputHandlers = [];
        
        inputs.forEach(input => {
            const inputHandler = (e) => {
                const index = parseInt(e.target.dataset.index);
                const field = e.target.classList.contains('image-title-input') ? 'title' :
                             e.target.classList.contains('image-description-input') ? 'description' : 'alt';
                
                if (this.galleryData.images[index]) {
                    this.galleryData.images[index][field] = e.target.value;
                }
            };
            
            input.addEventListener('input', inputHandler);
            this.imageInputHandlers.push({ element: input, handler: inputHandler });
        });
    }

    cleanupImageItemEvents() {
        const content = this.element.querySelector('.module-content');
        if (!content) return;

        // Supprimer le handler de clic principal
        if (this.imageItemClickHandler) {
            content.removeEventListener('click', this.imageItemClickHandler);
            this.imageItemClickHandler = null;
        }

        // Supprimer les handlers des champs de saisie
        if (this.imageInputHandlers) {
            this.imageInputHandlers.forEach(({ element, handler }) => {
                element.removeEventListener('input', handler);
            });
            this.imageInputHandlers = [];
        }
    }

    editImage(index) {
        // Pour l'instant, on peut modifier directement dans la liste
        // On pourrait ajouter un modal d'édition plus avancé ici
        console.log('Édition de l\'image', index);
    }

    deleteImage(index) {
        if (confirm('Supprimer cette image de la galerie ?')) {
            this.galleryData.images.splice(index, 1);
            this.updateGalleryEditor();
        }
    }

    moveImage(index, direction) {
        const newIndex = index + direction;
        if (newIndex >= 0 && newIndex < this.galleryData.images.length) {
            const temp = this.galleryData.images[index];
            this.galleryData.images[index] = this.galleryData.images[newIndex];
            this.galleryData.images[newIndex] = temp;
            this.updateGalleryEditor();
        }
    }

    saveGallery() {
        // Sauvegarder les données des images depuis les champs de saisie
        const content = this.element.querySelector('.module-content');
        if (content) {
            const titleInputs = content.querySelectorAll('.image-title-input');
            const descInputs = content.querySelectorAll('.image-description-input');
            const altInputs = content.querySelectorAll('.image-alt-input');

            titleInputs.forEach((input, index) => {
                if (this.galleryData.images[index]) {
                    this.galleryData.images[index].title = input.value.trim();
                }
            });

            descInputs.forEach((input, index) => {
                if (this.galleryData.images[index]) {
                    this.galleryData.images[index].description = input.value.trim();
                }
            });

            altInputs.forEach((input, index) => {
                if (this.galleryData.images[index]) {
                    this.galleryData.images[index].alt = input.value.trim();
                }
            });
        }

        this.displayGallery();
    }

    displayGallery() {
        const content = this.element.querySelector('.module-content');
        if (!content) return;

        if (this.galleryData.images.length === 0) {
            // Retour au placeholder si pas d'images
            content.innerHTML = `
                <div class="gallery-placeholder">
                    <div class="gallery-icon">📸</div>
                    <div class="gallery-text">Cliquez pour ajouter des images</div>
                    <div class="gallery-hint">Glissez-déposez ou sélectionnez plusieurs images</div>
                </div>
            `;
            this.bindEvents(); // Re-bind events to restore drag & drop
            return;
        }

        // Afficher la galerie selon le layout
        const layoutClass = this.getLayoutClass();
        const columnsClass = this.getColumnsClass();
        const spacingClass = this.getSpacingClass();
        const captionsClass = this.getCaptionsClass();

        let galleryHTML = '';
        
        switch (this.galleryData.layout) {
            case 'carousel':
                galleryHTML = this.renderCarousel();
                break;
            case 'masonry':
                galleryHTML = this.renderMasonry();
                break;
            case 'slider':
                galleryHTML = this.renderSlider();
                break;
            case 'grid':
            default:
                galleryHTML = `
                    <div class="gallery-container ${layoutClass} ${columnsClass} ${spacingClass} ${captionsClass}">
                        ${this.galleryData.images.map(image => this.renderGalleryImage(image)).join('')}
                    </div>
                `;
                break;
        }

        content.innerHTML = galleryHTML;

        // Re-binder les événements
        this.bindEvents(); // Re-bind events to restore drag & drop

        // Initialiser les fonctionnalités spécifiques
        if (this.galleryData.layout === 'carousel') {
            this.initCarousel();
        } else if (this.galleryData.layout === 'masonry') {
            this.initMasonry();
        } else if (this.galleryData.layout === 'slider') {
            this.initSlider();
        }
    }

    renderGalleryImage(image) {
        const showTitle = this.galleryData.showTitles && image.title;
        const showDescription = this.galleryData.showDescriptions && image.description;
        const hasCaption = showTitle || showDescription;

        return `
            <div class="gallery-item" data-image-id="${image.id}">
                <div class="gallery-image">
                    <img src="${image.url}" alt="${image.alt || image.title || ''}" />
                    ${this.galleryData.lightbox ? '<div class="lightbox-overlay">🔍</div>' : ''}
                </div>
                ${hasCaption ? `
                    <div class="gallery-caption">
                        ${showTitle ? `<div class="image-title">${image.title}</div>` : ''}
                        ${showDescription ? `<div class="image-description">${image.description}</div>` : ''}
                    </div>
                ` : ''}
            </div>
        `;
    }

    getLayoutClass() {
        switch (this.galleryData.layout) {
            case 'carousel': return 'layout-carousel';
            case 'masonry': return 'layout-masonry';
            case 'slider': return 'layout-slider';
            case 'grid':
            default: return 'layout-grid';
        }
    }

    getColumnsClass() {
        return `columns-${this.galleryData.columns}`;
    }

    getSpacingClass() {
        switch (this.galleryData.spacing) {
            case 'small': return 'spacing-small';
            case 'large': return 'spacing-large';
            case 'medium':
            default: return 'spacing-medium';
        }
    }

    getCaptionsClass() {
        switch (this.galleryData.captions) {
            case 'overlay': return 'captions-overlay';
            case 'below': return 'captions-below';
            case 'none': return 'captions-none';
            default: return 'captions-overlay';
        }
    }

    renderCarousel() {
        const captionsClass = this.getCaptionsClass();
        const spacingClass = this.getSpacingClass();
        
        return `
            <div class="gallery-carousel ${spacingClass} ${captionsClass}">
                <div class="carousel-container">
                    <div class="carousel-track">
                        ${this.galleryData.images.map((image, index) => `
                            <div class="carousel-slide" data-index="${index}">
                                ${this.renderGalleryImage(image)}
                            </div>
                        `).join('')}
                    </div>
                </div>
                
                ${this.galleryData.images.length > 1 ? `
                    <div class="carousel-controls">
                        <button type="button" class="carousel-btn carousel-prev" aria-label="Image précédente">
                            <span class="icon">⬅️</span>
                        </button>
                        <div class="carousel-indicators">
                            ${this.galleryData.images.map((_, index) => `
                                <button type="button" class="carousel-indicator ${index === 0 ? 'active' : ''}" data-index="${index}" aria-label="Image ${index + 1}">
                                    <span class="indicator-dot"></span>
                                </button>
                            `).join('')}
                        </div>
                        <button type="button" class="carousel-btn carousel-next" aria-label="Image suivante">
                            <span class="icon">➡️</span>
                        </button>
                    </div>
                ` : ''}
            </div>
        `;
    }

    renderMasonry() {
        const spacingClass = this.getSpacingClass();
        const captionsClass = this.getCaptionsClass();
        
        return `
            <div class="gallery-masonry ${spacingClass} ${captionsClass}">
                <div class="masonry-grid">
                    ${this.galleryData.images.map(image => `
                        <div class="masonry-item">
                            ${this.renderGalleryImage(image)}
                        </div>
                    `).join('')}
                </div>
            </div>
        `;
    }

    renderSlider() {
        const captionsClass = this.getCaptionsClass();
        const spacingClass = this.getSpacingClass();
        
        return `
            <div class="gallery-slider ${spacingClass} ${captionsClass}">
                <div class="slider-container">
                    <div class="slider-track">
                        ${this.galleryData.images.map((image, index) => `
                            <div class="slider-slide" data-index="${index}">
                                ${this.renderGalleryImage(image)}
                            </div>
                        `).join('')}
                    </div>
                </div>
                
                ${this.galleryData.images.length > 1 ? `
                    <div class="slider-controls">
                        <button type="button" class="slider-btn slider-prev" aria-label="Image précédente">
                            <span class="icon">⬅️</span>
                        </button>
                        <div class="slider-counter">
                            <span class="current-slide">1</span> / <span class="total-slides">${this.galleryData.images.length}</span>
                        </div>
                        <button type="button" class="slider-btn slider-next" aria-label="Image suivante">
                            <span class="icon">➡️</span>
                        </button>
                    </div>
                ` : ''}
            </div>
        `;
    }

    initCarousel() {
        const carousel = this.element.querySelector('.gallery-carousel');
        if (!carousel) return;

        const track = carousel.querySelector('.carousel-track');
        const slides = carousel.querySelectorAll('.carousel-slide');
        const prevBtn = carousel.querySelector('.carousel-prev');
        const nextBtn = carousel.querySelector('.carousel-next');
        const indicators = carousel.querySelectorAll('.carousel-indicator');

        let currentIndex = 0;
        const totalSlides = slides.length;

        if (totalSlides <= 1) return;

        // Fonction pour afficher une slide
        const showSlide = (index) => {
            if (index < 0) index = totalSlides - 1;
            if (index >= totalSlides) index = 0;
            
            currentIndex = index;
            
            // Mettre à jour la position du track
            track.style.transform = `translateX(-${index * 100}%)`;
            
            // Mettre à jour les indicateurs
            indicators.forEach((indicator, i) => {
                indicator.classList.toggle('active', i === index);
            });
        };

        // Événements des boutons
        if (prevBtn) {
            prevBtn.addEventListener('click', () => {
                showSlide(currentIndex - 1);
            });
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', () => {
                showSlide(currentIndex + 1);
            });
        }

        // Événements des indicateurs
        indicators.forEach((indicator, index) => {
            indicator.addEventListener('click', () => {
                showSlide(index);
            });
        });

        // Navigation au clavier
        carousel.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowLeft') {
                showSlide(currentIndex - 1);
            } else if (e.key === 'ArrowRight') {
                showSlide(currentIndex + 1);
            }
        });

        // Rendre le carousel focusable
        carousel.setAttribute('tabindex', '0');
    }

    initMasonry() {
        const masonry = this.element.querySelector('.gallery-masonry');
        if (!masonry) return;

        const grid = masonry.querySelector('.masonry-grid');
        const items = masonry.querySelectorAll('.masonry-item');

        // Fonction pour réorganiser les éléments
        const reorganizeItems = () => {
            const containerWidth = grid.offsetWidth;
            const columns = this.galleryData.columns || 3;
            const columnWidth = containerWidth / columns;
            
            // Créer des colonnes
            const columnHeights = new Array(columns).fill(0);
            const columnElements = new Array(columns).fill().map(() => []);

            // Distribuer les éléments dans les colonnes
            items.forEach((item, index) => {
                const shortestColumn = columnHeights.indexOf(Math.min(...columnHeights));
                columnElements[shortestColumn].push(item);
                
                // Calculer la hauteur approximative (basée sur l'image)
                const img = item.querySelector('img');
                if (img) {
                    const aspectRatio = img.naturalHeight / img.naturalWidth;
                    const itemHeight = columnWidth * aspectRatio;
                    columnHeights[shortestColumn] += itemHeight;
                }
            });

            // Appliquer les positions
            columnElements.forEach((columnItems, columnIndex) => {
                let currentTop = 0;
                columnItems.forEach(item => {
                    item.style.position = 'absolute';
                    item.style.left = `${columnIndex * columnWidth}px`;
                    item.style.top = `${currentTop}px`;
                    item.style.width = `${columnWidth}px`;
                    
                    const img = item.querySelector('img');
                    if (img) {
                        const aspectRatio = img.naturalHeight / img.naturalWidth;
                        const itemHeight = columnWidth * aspectRatio;
                        currentTop += itemHeight;
                    }
                });
            });

            // Ajuster la hauteur du conteneur
            const maxHeight = Math.max(...columnHeights);
            grid.style.height = `${maxHeight}px`;
        };

        // Réorganiser au chargement et au redimensionnement
        reorganizeItems();
        window.addEventListener('resize', reorganizeItems);

        // Réorganiser quand les images sont chargées
        const images = masonry.querySelectorAll('img');
        images.forEach(img => {
            if (img.complete) {
                reorganizeItems();
            } else {
                img.addEventListener('load', reorganizeItems);
            }
        });
    }

    initSlider() {
        const slider = this.element.querySelector('.gallery-slider');
        if (!slider) return;

        const track = slider.querySelector('.slider-track');
        const slides = slider.querySelectorAll('.slider-slide');
        const prevBtn = slider.querySelector('.slider-prev');
        const nextBtn = slider.querySelector('.slider-next');
        const counter = slider.querySelector('.slider-counter');
        const currentSlideSpan = counter?.querySelector('.current-slide');

        let currentIndex = 0;
        const totalSlides = slides.length;

        if (totalSlides <= 1) return;

        // Fonction pour afficher une slide
        const showSlide = (index) => {
            if (index < 0) index = totalSlides - 1;
            if (index >= totalSlides) index = 0;
            
            currentIndex = index;
            
            // Mettre à jour la position du track
            track.style.transform = `translateX(-${index * 100}%)`;
            
            // Mettre à jour le compteur
            if (currentSlideSpan) {
                currentSlideSpan.textContent = index + 1;
            }
        };

        // Événements des boutons
        if (prevBtn) {
            prevBtn.addEventListener('click', () => {
                showSlide(currentIndex - 1);
            });
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', () => {
                showSlide(currentIndex + 1);
            });
        }

        // Navigation au clavier
        slider.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowLeft') {
                showSlide(currentIndex - 1);
            } else if (e.key === 'ArrowRight') {
                showSlide(currentIndex + 1);
            }
        });

        // Rendre le slider focusable
        slider.setAttribute('tabindex', '0');
    }

    destroy() {
        console.log('🗑️ Destruction du module galerie:', this.moduleId);
        this.cleanupOptionsEvents();
        this.cleanupImageItemEvents();
    }

    getContent() {
        if (this.galleryData.images.length === 0) return '';
        
        const layoutClass = this.getLayoutClass();
        const columnsClass = this.getColumnsClass();
        const spacingClass = this.getSpacingClass();
        const captionsClass = this.getCaptionsClass();

        return `
            <div class="gallery-container ${layoutClass} ${columnsClass} ${spacingClass} ${captionsClass}">
                ${this.galleryData.images.map(image => this.renderGalleryImage(image)).join('')}
            </div>
        `;
    }

    getOptionsHTML() {
        if (this.galleryData.images.length === 0) {
            return `
                <div class="gallery-options">
                    <h4>Options de la galerie</h4>
                    <p>Aucune image dans la galerie</p>
                    <div class="option-group">
                        <button type="button" class="gallery-action-btn" data-action="edit">
                            <span class="icon">✏️</span> Ajouter des images
                        </button>
                    </div>
                </div>
            `;
        }

        return `
            <div class="gallery-options">
                <h4>Options de la galerie</h4>
                
                <div class="option-group">
                    <label>Actions :</label>
                    <div class="gallery-action-buttons">
                        <button type="button" class="gallery-action-btn" data-action="edit">
                            <span class="icon">✏️</span> Modifier
                        </button>
                        <button type="button" class="gallery-action-btn danger" data-action="clear">
                            <span class="icon">🗑️</span> Effacer tout
                        </button>
                    </div>
                </div>

                <div class="option-group">
                    <label>Disposition :</label>
                    <div class="layout-buttons">
                        <button type="button" class="gallery-layout-btn ${this.galleryData.layout === 'grid' ? 'active' : ''}" data-layout="grid">
                            <span class="icon">⊞</span> Grille
                        </button>
                        <button type="button" class="gallery-layout-btn ${this.galleryData.layout === 'carousel' ? 'active' : ''}" data-layout="carousel">
                            <span class="icon">🔄</span> Carousel
                        </button>
                        <button type="button" class="gallery-layout-btn ${this.galleryData.layout === 'masonry' ? 'active' : ''}" data-layout="masonry">
                            <span class="icon">🧱</span> Masonry
                        </button>
                        <button type="button" class="gallery-layout-btn ${this.galleryData.layout === 'slider' ? 'active' : ''}" data-layout="slider">
                            <span class="icon">📽️</span> Slider
                        </button>
                    </div>
                </div>

                <div class="option-group">
                    <label>Colonnes (grille) :</label>
                    <div class="columns-buttons">
                        <button type="button" class="column-btn ${this.galleryData.columns === 2 ? 'active' : ''}" data-columns="2">2</button>
                        <button type="button" class="column-btn ${this.galleryData.columns === 3 ? 'active' : ''}" data-columns="3">3</button>
                        <button type="button" class="column-btn ${this.galleryData.columns === 4 ? 'active' : ''}" data-columns="4">4</button>
                        <button type="button" class="column-btn ${this.galleryData.columns === 5 ? 'active' : ''}" data-columns="5">5</button>
                    </div>
                </div>

                <div class="option-group">
                    <label>Espacement :</label>
                    <div class="spacing-buttons">
                        <button type="button" class="spacing-btn ${this.galleryData.spacing === 'small' ? 'active' : ''}" data-spacing="small">
                            <span class="icon">📏</span> Petit
                        </button>
                        <button type="button" class="spacing-btn ${this.galleryData.spacing === 'medium' ? 'active' : ''}" data-spacing="medium">
                            <span class="icon">📏</span> Moyen
                        </button>
                        <button type="button" class="spacing-btn ${this.galleryData.spacing === 'large' ? 'active' : ''}" data-spacing="large">
                            <span class="icon">📏</span> Grand
                        </button>
                    </div>
                </div>

                <div class="option-group">
                    <label>Légendes :</label>
                    <div class="captions-buttons">
                        <button type="button" class="caption-btn ${this.galleryData.captions === 'overlay' ? 'active' : ''}" data-captions="overlay">
                            <span class="icon">🖼️</span> Superposées
                        </button>
                        <button type="button" class="caption-btn ${this.galleryData.captions === 'below' ? 'active' : ''}" data-captions="below">
                            <span class="icon">📝</span> En dessous
                        </button>
                        <button type="button" class="caption-btn ${this.galleryData.captions === 'none' ? 'active' : ''}" data-captions="none">
                            <span class="icon">❌</span> Aucune
                        </button>
                    </div>
                </div>

                <div class="option-group">
                    <label>Options :</label>
                    <div class="gallery-checkboxes">
                        <label class="checkbox-label">
                            <input type="checkbox" ${this.galleryData.showTitles ? 'checked' : ''} data-option="showTitles">
                            <span class="checkmark"></span>
                            Afficher les titres
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" ${this.galleryData.showDescriptions ? 'checked' : ''} data-option="showDescriptions">
                            <span class="checkmark"></span>
                            Afficher les descriptions
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" ${this.galleryData.lightbox ? 'checked' : ''} data-option="lightbox">
                            <span class="checkmark"></span>
                            Lightbox au clic
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" ${this.galleryData.autoplay ? 'checked' : ''} data-option="autoplay">
                            <span class="checkmark"></span>
                            Lecture automatique
                        </label>
                    </div>
                </div>

                <div class="option-group">
                    <label>Aperçu :</label>
                    <div class="gallery-preview">
                        <div class="gallery-container ${this.getLayoutClass()} ${this.getColumnsClass()} ${this.getSpacingClass()} ${this.getCaptionsClass()} preview-mode">
                            ${this.galleryData.images.slice(0, 3).map(image => this.renderGalleryImage(image)).join('')}
                            ${this.galleryData.images.length > 3 ? `<div class="more-images">+${this.galleryData.images.length - 3} autres</div>` : ''}
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    bindOptionsEvents() {
        this.cleanupOptionsEvents();
        
        const optionsContent = this.editor.optionsContent;
        if (!optionsContent) return;

        // Actions principales
        optionsContent.addEventListener('click', (e) => {
            const actionBtn = e.target.closest('.gallery-action-btn');
            if (actionBtn && this.editor.currentModule && this.editor.currentModule.moduleId === this.moduleId) {
                const action = actionBtn.dataset.action;
                this.handleOptionAction(action);
            }
        });

        // Layout
        optionsContent.addEventListener('click', (e) => {
            const layoutBtn = e.target.closest('.gallery-layout-btn');
            if (layoutBtn && this.editor.currentModule && this.editor.currentModule.moduleId === this.moduleId) {
                const layout = layoutBtn.dataset.layout;
                this.galleryData.layout = layout;
                this.displayGallery();
                this.updateOptionsDisplay();
            }
        });

        // Colonnes
        optionsContent.addEventListener('click', (e) => {
            const columnBtn = e.target.closest('.column-btn');
            if (columnBtn && this.editor.currentModule && this.editor.currentModule.moduleId === this.moduleId) {
                const columns = parseInt(columnBtn.dataset.columns);
                this.galleryData.columns = columns;
                this.displayGallery();
                this.updateOptionsDisplay();
            }
        });

        // Espacement
        optionsContent.addEventListener('click', (e) => {
            const spacingBtn = e.target.closest('.spacing-btn');
            if (spacingBtn && this.editor.currentModule && this.editor.currentModule.moduleId === this.moduleId) {
                const spacing = spacingBtn.dataset.spacing;
                this.galleryData.spacing = spacing;
                this.displayGallery();
                this.updateOptionsDisplay();
            }
        });

        // Légendes
        optionsContent.addEventListener('click', (e) => {
            const captionBtn = e.target.closest('.caption-btn');
            if (captionBtn && this.editor.currentModule && this.editor.currentModule.moduleId === this.moduleId) {
                const captions = captionBtn.dataset.captions;
                this.galleryData.captions = captions;
                this.displayGallery();
                this.updateOptionsDisplay();
            }
        });

        // Checkboxes
        optionsContent.addEventListener('change', (e) => {
            const checkbox = e.target.closest('input[type="checkbox"]');
            if (checkbox && this.editor.currentModule && this.editor.currentModule.moduleId === this.moduleId) {
                const option = checkbox.dataset.option;
                this.galleryData[option] = checkbox.checked;
                this.displayGallery();
                this.updateOptionsDisplay();
            }
        });
    }

    cleanupOptionsEvents() {
        // Les événements sont gérés globalement, pas besoin de cleanup spécifique
    }

    handleOptionAction(action) {
        switch (action) {
            case 'edit':
                this.showGalleryEditor();
                break;
            case 'clear':
                if (confirm('Effacer toutes les images de la galerie ?')) {
                    this.galleryData.images = [];
                    this.displayGallery();
                    this.updateOptionsDisplay();
                }
                break;
        }
    }

    updateOptionsDisplay() {
        const optionsHTML = this.getOptionsHTML();
        if (this.editor.optionsContent) {
            this.editor.optionsContent.innerHTML = optionsHTML;
        }
    }

    /**
     * Ouvrir la bibliothèque de médias pour sélection multiple
     */
    async openMediaLibrary() {
        try {
            console.log('📚 Ouverture de la bibliothèque de médias pour galerie...');
            
            // Vérifier que l'API est disponible
            if (typeof MediaLibraryAPI === 'undefined') {
                console.error('❌ MediaLibraryAPI non disponible');
                alert('Erreur: API de médias non disponible');
                return;
            }
            
            const mediaAPI = new MediaLibraryAPI();
            
            // Ouvrir le sélecteur de médias avec sélection multiple
            const selectedMedias = await mediaAPI.openMediaSelector({
                allowMultiple: true,
                filters: {
                    type: 'image'
                }
            });
            
            if (selectedMedias && selectedMedias.length > 0) {
                console.log('✅ Médias sélectionnés pour galerie:', selectedMedias);
                
                // Convertir les médias en format galerie
                const newImages = selectedMedias.map(media => ({
                    id: media.id,
                    url: `/public/uploads.php?file=${encodeURIComponent(media.filename)}`,
                    title: media.original_name,
                    description: '',
                    alt: media.original_name,
                    mediaId: media.id
                }));
                
                // Ajouter les nouvelles images à la galerie
                this.galleryData.images.push(...newImages);
                
                // Mettre à jour l'affichage
                this.displayGallery();
                
                console.log('✅ Images ajoutées à la galerie depuis la bibliothèque');
            }
            
        } catch (error) {
            if (error.message !== 'Sélection annulée') {
                console.error('❌ Erreur lors de l\'ouverture de la bibliothèque:', error);
                alert('Erreur lors de l\'ouverture de la bibliothèque de médias');
            }
        }
    }

    loadData(data) {
        console.log('📂 Chargement des données galerie:', data);
        
        // Appliquer les données au module
        this.galleryData = {
            ...this.galleryData,
            ...data
        };
        
        // Mettre à jour l'affichage si l'élément existe
        if (this.element) {
            this.displayGallery();
        }
        
        console.log('✅ Données galerie chargées avec succès');
    }
}

// Rendre disponible globalement
window.GalleryModule = GalleryModule;
