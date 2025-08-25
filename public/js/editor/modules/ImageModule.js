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
            height: null
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
            height: null
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
        
        return `
            <div class="image-container">
                <img src="${this.imageData.src}" alt="${this.imageData.alt}" class="uploaded-image" 
                     style="${this.imageData.width ? `width: ${this.imageData.width}px;` : ''} 
                            ${this.imageData.height ? `height: ${this.imageData.height}px;` : ''}">
                ${this.imageData.caption ? `<div class="image-caption">${this.imageData.caption}</div>` : ''}
            </div>
        `;
    }

    getOptionsHTML() {
        if (!this.imageData.src) {
            return `
                <div class="image-options">
                    <h4>Options de l'image</h4>
                    <p>Aucune image sélectionnée</p>
                    <div class="image-action-buttons">
                        <button type="button" class="image-action-btn" data-action="upload">
                            <span class="icon">📁</span> Sélectionner une image
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
                            <span class="icon">📁</span> Changer l'image
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
                
            case 'remove':
                if (confirm('Supprimer cette image ?')) {
                    console.log('🗑️ Suppression de l\'image pour le module:', this.moduleId);
                    this.resetToUpload();
                    this.editor.hideOptions();
                }
                break;
        }
    }
}

// Rendre disponible globalement
window.ImageModule = ImageModule;
