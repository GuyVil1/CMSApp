/**
 * Module Image - Gestion des images avec upload et options
 */
class ImageModule extends BaseModule {
    constructor(editor) {
        super('image', editor);
        this.imageData = {
            src: null,
            alt: '',
            caption: '',
            width: null,
            height: null,
            alignment: 'left',
            padding: {
                top: 0,
                right: 0,
                bottom: 0,
                left: 0
            }
        };
    }

    render() {
        this.element.innerHTML = `
            <div class="module-header">
                <span class="module-type">🖼️ Image</span>
                <div class="module-actions">
                    <button type="button" class="module-action" data-action="move-left" title="Déplacer à gauche">⬅️</button>
                    <button type="button" class="module-action" data-action="move-right" title="Déplacer à droite">➡️</button>
                    <button type="button" class="module-action" data-action="move-up" title="Déplacer vers le haut">⬆️</button>
                    <button type="button" class="module-action" data-action="move-down" title="Déplacer vers le bas">⬇️</button>
                    <button type="button" class="module-action" data-action="delete" title="Supprimer">🗑️</button>
                </div>
            </div>
            <div class="module-content">
                <div class="image-upload-area">
                    <div class="image-placeholder">
                        <div class="upload-icon">📁</div>
                        <div class="upload-text">Cliquez pour sélectionner une image</div>
                        <div class="upload-hint">ou glissez-déposez ici</div>
                    </div>
                    <input type="file" class="image-file-input" accept="image/*" style="display: none;">
                </div>
            </div>
        `;
    }

    bindEvents() {
        super.bindEvents();
        this.bindUploadEvents();
    }

    bindUploadEvents() {
        if (!this.element) return;

        const uploadArea = this.element.querySelector('.image-upload-area');
        const fileInput = this.element.querySelector('.image-file-input');

        if (!uploadArea || !fileInput) {
            console.error('❌ Éléments upload non trouvés:', { uploadArea, fileInput });
            return;
        }

        console.log('🔧 Configuration des événements upload pour le module image');

        // Clic sur la zone d'upload
        uploadArea.addEventListener('click', (e) => {
            console.log('🖱️ Clic détecté sur la zone d\'upload');
            e.preventDefault();
            e.stopPropagation();
            if (!e.target.closest('.image-action-btn')) {
                console.log('📁 Déclenchement du file input');
                fileInput.click();
            }
        });

        // Drag and drop
        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.classList.add('drag-over');
        });

        uploadArea.addEventListener('dragleave', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('drag-over');
        });

        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('drag-over');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                this.handleFile(files[0]);
            }
        });

        // Sélection de fichier
        fileInput.addEventListener('change', (e) => {
            console.log('📁 Fichier sélectionné:', e.target.files);
            if (e.target.files.length > 0) {
                this.handleFile(e.target.files[0]);
            }
        });
    }

    handleFile(file) {
        console.log('🖼️ Traitement du fichier pour le module:', this.moduleId, 'Fichier:', file.name, file.type);
        
        if (!file.type.startsWith('image/')) {
            console.error('❌ Type de fichier invalide:', file.type);
            alert('Veuillez sélectionner une image valide.');
            this.isUploading = false;
            return;
        }

        const reader = new FileReader();
        reader.onload = (e) => {
            console.log('✅ Image chargée avec succès pour le module:', this.moduleId);
            this.imageData.src = e.target.result;
            this.displayImage();
            
            // S'assurer que ce module est sélectionné après l'upload
            this.select();
            console.log('🎯 Module sélectionné après upload:', this.moduleId);
            
            this.isUploading = false;
        };

        reader.onerror = () => {
            console.error('❌ Erreur lors de la lecture du fichier pour le module:', this.moduleId);
            alert('Erreur lors de la lecture du fichier');
            this.isUploading = false;
        };

        reader.readAsDataURL(file);
    }

    displayImage() {
        const content = this.element.querySelector('.module-content');
        if (!content) return;

        content.innerHTML = `
            <div class="image-container">
                <img src="${this.imageData.src}" alt="${this.imageData.alt}" class="uploaded-image" 
                     style="${this.imageData.width ? `width: ${this.imageData.width}px;` : ''} 
                            ${this.imageData.height ? `height: ${this.imageData.height}px;` : ''}">
                ${this.imageData.caption ? `<div class="image-caption">${this.imageData.caption}</div>` : ''}
            </div>
        `;
    }

    resetToUpload() {
        console.log('🔄 Reset du module image:', this.moduleId);
        
        const content = this.element.querySelector('.module-content');
        if (!content) return;

        content.innerHTML = `
            <div class="image-upload-area">
                <div class="image-placeholder">
                    <div class="upload-icon">📁</div>
                    <div class="upload-text">Cliquez pour sélectionner une image</div>
                    <div class="upload-hint">ou glissez-déposez ici</div>
                </div>
                <input type="file" class="image-file-input" accept="image/*" style="display: none;">
            </div>
        `;
        
        this.imageData = {
            src: null,
            alt: '',
            caption: '',
            width: null,
            height: null,
            alignment: 'left',
            padding: {
                top: 0,
                right: 0,
                bottom: 0,
                left: 0
            }
        };
        
        this.isUploading = false;
        this.bindUploadEvents();
    }

    destroy() {
        console.log('🗑️ Destruction du module image:', this.moduleId);
        this.cleanupOptionsEvents();
        this.isUploading = false;
    }

    getContent() {
        if (!this.imageData.src) return '';
        
        const alignmentClass = this.getAlignmentClass();
        const paddingStyle = this.getPaddingStyle();
        
        return `
            <div class="image-container ${alignmentClass}" style="${paddingStyle}">
                <img src="${this.imageData.src}" alt="${this.imageData.alt}" class="uploaded-image" 
                     style="${this.imageData.width ? `width: ${this.imageData.width}px;` : ''} 
                            ${this.imageData.height ? `height: ${this.imageData.height}px;` : ''}">
                ${this.imageData.caption ? `<div class="image-caption">${this.imageData.caption}</div>` : ''}
            </div>
        `;
    }

    /**
     * Générer le style CSS pour le padding
     */
    getPaddingStyle() {
        // Vérification de sécurité pour éviter l'erreur si padding n'est pas défini
        if (!this.imageData.padding) {
            this.imageData.padding = {
                top: 0,
                right: 0,
                bottom: 0,
                left: 0
            };
        }
        
        const { top, right, bottom, left } = this.imageData.padding;
        
        if (top === 0 && right === 0 && bottom === 0 && left === 0) {
            return ''; // Pas de padding
        }

        // Si tous les paddings sont identiques, utiliser la version raccourcie
        if (top === right && right === bottom && bottom === left) {
            return `padding: ${top}px;`;
        }

        // Sinon, spécifier chaque direction
        return `padding: ${top}px ${right}px ${bottom}px ${left}px;`;
    }

    getAlignmentClass() {
        switch (this.imageData.alignment) {
            case 'left': return 'image-align-left';
            case 'center': return 'image-align-center';
            case 'right': return 'image-align-right';
            default: return 'image-align-left';
        }
    }

    getOptionsHTML() {
        if (!this.imageData.src) {
            return `
                <div class="image-options">
                    <h4>Options de l'image</h4>
                    <p>Aucune image sélectionnée</p>
                    <div class="image-action-buttons">
                        <button type="button" class="image-action-btn" data-action="upload">
                            <span class="icon">📁</span> Upload direct
                        </button>
                        <button type="button" class="image-action-btn" data-action="library">
                            <span class="icon">📚</span> Bibliothèque
                        </button>
                    </div>
                </div>
            `;
        }

        return `
            <div class="image-options">
                <h4>Options de l'image</h4>
                
                <div class="option-group">
                    <label>Actions :</label>
                    <div class="image-action-buttons">
                        <button type="button" class="image-action-btn" data-action="upload">
                            <span class="icon">📁</span> Upload direct
                        </button>
                        <button type="button" class="image-action-btn" data-action="library">
                            <span class="icon">📚</span> Bibliothèque
                        </button>
                        <button type="button" class="image-action-btn danger" data-action="remove">
                            <span class="icon">🗑️</span> Supprimer l'image
                        </button>
                    </div>
                </div>

                <div class="option-group">
                    <label>Texte alternatif :</label>
                    <input type="text" class="image-alt" value="${this.imageData.alt}" placeholder="Description de l'image">
                </div>

                <div class="option-group">
                    <label>Légende :</label>
                    <input type="text" class="image-caption" value="${this.imageData.caption}" placeholder="Légende de l'image">
                </div>

                <div class="option-group">
                    <label>Dimensions :</label>
                    <div class="size-inputs">
                        <input type="number" class="image-width" value="${this.imageData.width || ''}" placeholder="Largeur">
                        <span class="size-unit">px</span>
                        <span class="size-unit">×</span>
                        <input type="number" class="image-height" value="${this.imageData.height || ''}" placeholder="Hauteur">
                        <span class="size-unit">px</span>
                    </div>
                </div>

                <div class="option-group">
                    <label>Padding :</label>
                    <div class="padding-controls">
                        <div class="padding-row">
                            <div class="padding-input-group">
                                <label class="padding-label">Haut</label>
                                <input type="number" class="padding-top" value="${this.imageData.padding?.top || 0}" min="0" max="100" placeholder="0">
                                <span class="padding-unit">px</span>
                            </div>
                            <div class="padding-input-group">
                                <label class="padding-label">Bas</label>
                                <input type="number" class="padding-bottom" value="${this.imageData.padding?.bottom || 0}" min="0" max="100" placeholder="0">
                                <span class="padding-unit">px</span>
                            </div>
                        </div>
                        <div class="padding-row">
                            <div class="padding-input-group">
                                <label class="padding-label">Gauche</label>
                                <input type="number" class="padding-left" value="${this.imageData.padding?.left || 0}" min="0" max="100" placeholder="0">
                                <span class="padding-unit">px</span>
                            </div>
                            <div class="padding-input-group">
                                <label class="padding-label">Droite</label>
                                <input type="number" class="padding-right" value="${this.imageData.padding?.right || 0}" min="0" max="100" placeholder="0">
                                <span class="padding-unit">px</span>
                            </div>
                        </div>
                        <div class="padding-presets">
                            <button type="button" class="padding-preset-btn" data-preset="none">Aucun</button>
                            <button type="button" class="padding-preset-btn" data-preset="small">Petit</button>
                            <button type="button" class="padding-preset-btn" data-preset="medium">Moyen</button>
                            <button type="button" class="padding-preset-btn" data-preset="large">Grand</button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    bindOptionsEvents() {
        const optionsContent = this.editor.optionsContent;
        if (!optionsContent) return;

        // Nettoyer les événements précédents pour éviter les doublons
        this.cleanupOptionsEvents();

        // Actions d'image - avec gestion spécifique au module sélectionné uniquement
        this.optionsClickHandler = (e) => {
            const imageActionBtn = e.target.closest('.image-action-btn');
            if (imageActionBtn) {
                // Vérifier que ce module est bien le module sélectionné
                if (this.editor.currentModule && this.editor.currentModule.moduleId === this.moduleId) {
                    const action = imageActionBtn.dataset.action;
                    console.log('🎛️ Action d\'option détectée pour le module sélectionné:', this.moduleId, 'Action:', action);
                    this.handleOptionAction(action);
                } else {
                    console.log('⚠️ Action ignorée - module non sélectionné:', this.moduleId);
                }
            }
        };

        optionsContent.addEventListener('click', this.optionsClickHandler);

        // Propriétés d'image
        const imageWidth = optionsContent.querySelector('.image-width');
        const imageHeight = optionsContent.querySelector('.image-height');
        const imageAlt = optionsContent.querySelector('.image-alt');
        const imageCaption = optionsContent.querySelector('.image-caption');

        if (imageWidth) {
            this.widthChangeHandler = (e) => {
                this.imageData.width = e.target.value ? parseInt(e.target.value) : null;
                this.displayImage();
            };
            imageWidth.addEventListener('change', this.widthChangeHandler);
        }

        if (imageHeight) {
            this.heightChangeHandler = (e) => {
                this.imageData.height = e.target.value ? parseInt(e.target.value) : null;
                this.displayImage();
            };
            imageHeight.addEventListener('change', this.heightChangeHandler);
        }

        if (imageAlt) {
            this.altInputHandler = (e) => {
                this.imageData.alt = e.target.value;
                this.displayImage();
            };
            imageAlt.addEventListener('input', this.altInputHandler);
        }

        if (imageCaption) {
            this.captionInputHandler = (e) => {
                this.imageData.caption = e.target.value;
                this.displayImage();
            };
            imageCaption.addEventListener('input', this.captionInputHandler);
        }

        // Contrôles de padding
        const paddingTop = optionsContent.querySelector('.padding-top');
        const paddingRight = optionsContent.querySelector('.padding-right');
        const paddingBottom = optionsContent.querySelector('.padding-bottom');
        const paddingLeft = optionsContent.querySelector('.padding-left');

        if (paddingTop) {
            this.paddingTopHandler = (e) => {
                this.imageData.padding.top = parseInt(e.target.value) || 0;
                this.displayImage();
            };
            paddingTop.addEventListener('change', this.paddingTopHandler);
        }

        if (paddingRight) {
            this.paddingRightHandler = (e) => {
                this.imageData.padding.right = parseInt(e.target.value) || 0;
                this.displayImage();
            };
            paddingRight.addEventListener('change', this.paddingRightHandler);
        }

        if (paddingBottom) {
            this.paddingBottomHandler = (e) => {
                this.imageData.padding.bottom = parseInt(e.target.value) || 0;
                this.displayImage();
            };
            paddingBottom.addEventListener('change', this.paddingBottomHandler);
        }

        if (paddingLeft) {
            this.paddingLeftHandler = (e) => {
                this.imageData.padding.left = parseInt(e.target.value) || 0;
                this.displayImage();
            };
            paddingLeft.addEventListener('change', this.paddingLeftHandler);
        }

        // Boutons de presets de padding
        const paddingPresetBtns = optionsContent.querySelectorAll('.padding-preset-btn');
        paddingPresetBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                const preset = e.target.dataset.preset;
                this.applyPaddingPreset(preset);
            });
        });
    }

    cleanupOptionsEvents() {
        const optionsContent = this.editor.optionsContent;
        if (!optionsContent) return;

        // Supprimer les événements précédents
        if (this.optionsClickHandler) {
            optionsContent.removeEventListener('click', this.optionsClickHandler);
        }

        const imageWidth = optionsContent.querySelector('.image-width');
        const imageHeight = optionsContent.querySelector('.image-height');
        const imageAlt = optionsContent.querySelector('.image-alt');
        const imageCaption = optionsContent.querySelector('.image-caption');

        if (imageWidth && this.widthChangeHandler) {
            imageWidth.removeEventListener('change', this.widthChangeHandler);
        }

        if (imageHeight && this.heightChangeHandler) {
            imageHeight.removeEventListener('change', this.heightChangeHandler);
        }

        if (imageAlt && this.altInputHandler) {
            imageAlt.removeEventListener('input', this.altInputHandler);
        }

        if (imageCaption && this.captionInputHandler) {
            imageCaption.removeEventListener('input', this.captionInputHandler);
        }

        // Nettoyer les événements de padding
        const paddingTop = optionsContent.querySelector('.padding-top');
        const paddingRight = optionsContent.querySelector('.padding-right');
        const paddingBottom = optionsContent.querySelector('.padding-bottom');
        const paddingLeft = optionsContent.querySelector('.padding-left');

        if (paddingTop && this.paddingTopHandler) {
            paddingTop.removeEventListener('change', this.paddingTopHandler);
        }

        if (paddingRight && this.paddingRightHandler) {
            paddingRight.removeEventListener('change', this.paddingRightHandler);
        }

        if (paddingBottom && this.paddingBottomHandler) {
            paddingBottom.removeEventListener('change', this.paddingBottomHandler);
        }

        if (paddingLeft && this.paddingLeftHandler) {
            paddingLeft.removeEventListener('change', this.paddingLeftHandler);
        }
    }

    /**
     * Appliquer un preset de padding
     */
    applyPaddingPreset(preset) {
        const presets = {
            none: { top: 0, right: 0, bottom: 0, left: 0 },
            small: { top: 10, right: 10, bottom: 10, left: 10 },
            medium: { top: 20, right: 20, bottom: 20, left: 20 },
            large: { top: 40, right: 40, bottom: 40, left: 40 }
        };

        if (presets[preset]) {
            this.imageData.padding = { ...presets[preset] };
            
            // Mettre à jour les inputs
            const optionsContent = this.editor.optionsContent;
            if (optionsContent) {
                const paddingTop = optionsContent.querySelector('.padding-top');
                const paddingRight = optionsContent.querySelector('.padding-right');
                const paddingBottom = optionsContent.querySelector('.padding-bottom');
                const paddingLeft = optionsContent.querySelector('.padding-left');

                if (paddingTop) paddingTop.value = this.imageData.padding.top;
                if (paddingRight) paddingRight.value = this.imageData.padding.right;
                if (paddingBottom) paddingBottom.value = this.imageData.padding.bottom;
                if (paddingLeft) paddingLeft.value = this.imageData.padding.left;
            }

            // Mettre à jour l'affichage
            this.displayImage();
            
            console.log(`✅ Preset de padding "${preset}" appliqué:`, this.imageData.padding);
        }
    }

    handleOptionAction(action) {
        console.log('🎛️ Action d\'option image pour le module:', this.moduleId, 'Action:', action);
        
        switch (action) {
            case 'upload':
                // Éviter les uploads multiples simultanés
                if (this.isUploading) {
                    console.log('⚠️ Upload déjà en cours pour le module:', this.moduleId);
                    return;
                }
                
                this.isUploading = true;
                console.log('📁 Démarrage de l\'upload pour le module:', this.moduleId);
                
                // Créer un input file temporaire pour l'upload
                const tempFileInput = document.createElement('input');
                tempFileInput.type = 'file';
                tempFileInput.accept = 'image/*';
                tempFileInput.style.display = 'none';
                
                tempFileInput.addEventListener('change', (e) => {
                    if (e.target.files.length > 0) {
                        console.log('📁 Fichier sélectionné pour le module:', this.moduleId, e.target.files[0].name);
                        this.handleFile(e.target.files[0]);
                    }
                    // Nettoyer l'input temporaire
                    if (document.body.contains(tempFileInput)) {
                        document.body.removeChild(tempFileInput);
                    }
                    this.isUploading = false;
                });
                
                // Gestion de l'annulation
                tempFileInput.addEventListener('cancel', () => {
                    console.log('❌ Upload annulé pour le module:', this.moduleId);
                    this.isUploading = false;
                    if (document.body.contains(tempFileInput)) {
                        document.body.removeChild(tempFileInput);
                    }
                });
                
                document.body.appendChild(tempFileInput);
                tempFileInput.click();
                break;
                
            case 'library':
                this.openMediaLibrary();
                break;
                
            case 'remove':
                if (confirm('Supprimer cette image ?')) {
                    console.log('🗑️ Suppression de l\'image pour le module:', this.moduleId);
                    this.resetToUpload();
                    this.editor.hideOptions();
                }
                break;
        }
    }

    /**
     * Ouvrir la bibliothèque de médias
     */
    async openMediaLibrary() {
        try {
            console.log('📚 Ouverture de la bibliothèque de médias...');
            
            // Vérifier que l'API est disponible
            if (typeof MediaLibraryAPI === 'undefined') {
                console.error('❌ MediaLibraryAPI non disponible');
                alert('Erreur: API de médias non disponible');
                return;
            }
            
            const mediaAPI = new MediaLibraryAPI();
            
            // Ouvrir le sélecteur de médias
            const selectedMedia = await mediaAPI.openMediaSelector({
                allowMultiple: false,
                filters: {
                    type: 'image'
                }
            });
            
            if (selectedMedia) {
                console.log('✅ Média sélectionné:', selectedMedia);
                
                // Mettre à jour les données de l'image
                this.imageData = {
                    src: `/public/uploads.php?file=${encodeURIComponent(selectedMedia.filename)}`,
                    alt: selectedMedia.original_name,
                    caption: '',
                    width: null,
                    height: null,
                    alignment: 'left',
                    padding: {
                        top: 0,
                        right: 0,
                        bottom: 0,
                        left: 0
                    },
                    mediaId: selectedMedia.id // Stocker l'ID du média
                };
                
                // Mettre à jour l'affichage
                this.displayImage();
                
                console.log('✅ Image mise à jour depuis la bibliothèque');
            }
            
        } catch (error) {
            if (error.message !== 'Sélection annulée') {
                console.error('❌ Erreur lors de l\'ouverture de la bibliothèque:', error);
                alert('Erreur lors de l\'ouverture de la bibliothèque de médias');
            }
        }
    }

    loadData(data) {
        console.log('📂 Chargement des données image:', data);
        
        // Appliquer les données au module
        this.imageData = {
            ...this.imageData,
            ...data
        };
        
        // S'assurer que le padding est toujours initialisé
        if (!this.imageData.padding) {
            this.imageData.padding = {
                top: 0,
                right: 0,
                bottom: 0,
                left: 0
            };
        }
        
        // Mettre à jour l'affichage si l'élément existe
        if (this.element) {
            this.displayImage();
        }
        
        console.log('✅ Données image chargées avec succès');
    }
}

// Rendre disponible globalement
window.ImageModule = ImageModule;
