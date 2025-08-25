/**
 * Module Vidéo - Gestion des vidéos (YouTube, Vimeo, fichiers locaux)
 */
class VideoModule extends BaseModule {
    constructor(editor) {
        super('video', editor);
        this.videoData = {
            type: null, // 'youtube', 'vimeo', 'file'
            url: null,
            file: null,
            title: '',
            description: '',
            width: null,
            height: null,
            aspectRatio: null, // Pour conserver le ratio
            alignment: 'center', // 'left', 'center', 'right'
            autoplay: false,
            controls: true,
            loop: false,
            muted: false
        };
    }

    render() {
        this.element.innerHTML = `
            <div class="module-header">
                <span class="module-type">🎬 Vidéo</span>
                <div class="module-actions">
                    <button type="button" class="module-action" data-action="move-left" title="Déplacer à gauche">⬅️</button>
                    <button type="button" class="module-action" data-action="move-right" title="Déplacer à droite">➡️</button>
                    <button type="button" class="module-action" data-action="move-up" title="Déplacer vers le haut">⬆️</button>
                    <button type="button" class="module-action" data-action="move-down" title="Déplacer vers le bas">⬇️</button>
                    <button type="button" class="module-action" data-action="delete" title="Supprimer">🗑️</button>
                </div>
            </div>
            <div class="module-content">
                <div class="video-upload-area">
                    <div class="video-placeholder">
                        <div class="upload-icon">🎬</div>
                        <div class="upload-text">Ajouter une vidéo</div>
                        <div class="upload-hint">YouTube, Vimeo ou fichier vidéo</div>
                    </div>
                    <input type="file" class="video-file-input" accept="video/*" style="display: none;">
                </div>
            </div>
        `;
    }

    bindEvents() {
        super.bindEvents();
        this.bindVideoEvents();
    }

    bindVideoEvents() {
        if (!this.element) return;

        const uploadArea = this.element.querySelector('.video-upload-area');
        const fileInput = this.element.querySelector('.video-file-input');

        if (!uploadArea || !fileInput) {
            console.error('❌ Éléments vidéo non trouvés:', { uploadArea, fileInput });
            return;
        }

        console.log('🔧 Configuration des événements vidéo pour le module');

        // Clic sur la zone d'upload
        uploadArea.addEventListener('click', (e) => {
            console.log('🖱️ Clic détecté sur la zone vidéo');
            e.preventDefault();
            e.stopPropagation();
            if (!e.target.closest('.video-action-btn')) {
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
                this.handleVideoFile(files[0]);
            }
        });

        // Sélection de fichier
        fileInput.addEventListener('change', (e) => {
            console.log('📁 Fichier vidéo sélectionné:', e.target.files);
            if (e.target.files.length > 0) {
                this.handleVideoFile(e.target.files[0]);
            }
        });
    }

    handleVideoFile(file) {
        console.log('🎬 Traitement du fichier vidéo pour le module:', this.moduleId, 'Fichier:', file.name, file.type);
        
        if (!file.type.startsWith('video/')) {
            console.error('❌ Type de fichier invalide:', file.type);
            alert('Veuillez sélectionner un fichier vidéo valide.');
            return;
        }

        this.videoData.type = 'file';
        this.videoData.file = file;
        this.videoData.title = file.name;
        
        // Créer un URL temporaire pour la prévisualisation
        const videoUrl = URL.createObjectURL(file);
        this.videoData.url = videoUrl;
        
        // Détecter le ratio d'aspect de la vidéo
        this.detectVideoAspectRatio(videoUrl);
        
        this.displayVideo();
        this.select();
        console.log('🎯 Module vidéo sélectionné après upload:', this.moduleId);
    }

    handleVideoUrl(url) {
        console.log('🔗 Traitement de l\'URL vidéo:', url);
        
        // Détecter le type de vidéo
        if (this.isYouTubeUrl(url)) {
            this.videoData.type = 'youtube';
            this.videoData.url = this.extractYouTubeEmbedUrl(url);
            // Ratio par défaut pour YouTube (16:9)
            this.videoData.aspectRatio = 16/9;
        } else if (this.isVimeoUrl(url)) {
            this.videoData.type = 'vimeo';
            this.videoData.url = this.extractVimeoEmbedUrl(url);
            // Ratio par défaut pour Vimeo (16:9)
            this.videoData.aspectRatio = 16/9;
        } else {
            console.error('❌ URL vidéo non supportée:', url);
            alert('URL non supportée. Utilisez YouTube ou Vimeo.');
            return;
        }
        
        this.displayVideo();
        this.select();
        console.log('🎯 Module vidéo sélectionné après URL:', this.moduleId);
    }

    isYouTubeUrl(url) {
        return url.includes('youtube.com') || url.includes('youtu.be');
    }

    isVimeoUrl(url) {
        return url.includes('vimeo.com');
    }

    extractYouTubeEmbedUrl(url) {
        let videoId = '';
        
        if (url.includes('youtube.com/watch')) {
            const urlParams = new URLSearchParams(url.split('?')[1]);
            videoId = urlParams.get('v');
        } else if (url.includes('youtu.be/')) {
            videoId = url.split('youtu.be/')[1].split('?')[0];
        }
        
        return `https://www.youtube.com/embed/${videoId}`;
    }

    extractVimeoEmbedUrl(url) {
        const videoId = url.split('vimeo.com/')[1].split('?')[0];
        return `https://player.vimeo.com/video/${videoId}`;
    }

    detectVideoAspectRatio(videoUrl) {
        const video = document.createElement('video');
        video.src = videoUrl;
        video.muted = true;
        video.preload = 'metadata';
        
        video.addEventListener('loadedmetadata', () => {
            this.videoData.aspectRatio = video.videoWidth / video.videoHeight;
            console.log('📐 Ratio d\'aspect détecté:', this.videoData.aspectRatio);
            this.displayVideo(); // Re-afficher avec le bon ratio
        });
        
        video.addEventListener('error', () => {
            console.warn('⚠️ Impossible de détecter le ratio, utilisation du ratio par défaut (16:9)');
            this.videoData.aspectRatio = 16/9;
        });
    }

    calculateDimensions() {
        let width = this.videoData.width;
        let height = this.videoData.height;
        
        // Si on a un ratio d'aspect et qu'une dimension est définie, calculer l'autre
        if (this.videoData.aspectRatio) {
            if (width && !height) {
                // Largeur définie, calculer la hauteur
                height = Math.round(width / this.videoData.aspectRatio);
            } else if (height && !width) {
                // Hauteur définie, calculer la largeur
                width = Math.round(height * this.videoData.aspectRatio);
            }
        }
        
        // Pour les vidéos YouTube/Vimeo, si aucune dimension n'est définie, utiliser des valeurs par défaut
        if ((this.videoData.type === 'youtube' || this.videoData.type === 'vimeo') && !width && !height) {
            width = 560;
            height = 315;
        }
        
        return { width, height };
    }

    getAlignmentClass() {
        switch (this.videoData.alignment) {
            case 'left': return 'align-left';
            case 'right': return 'align-right';
            case 'center':
            default: return 'align-center';
        }
    }

    displayVideo() {
        const content = this.element.querySelector('.module-content');
        if (!content) return;

        let videoHtml = '';
        const dimensions = this.calculateDimensions();
        const alignmentClass = this.getAlignmentClass();
        
        if (this.videoData.type === 'youtube' || this.videoData.type === 'vimeo') {
            // Calculer les styles CSS pour l'iframe uniquement
            let iframeStyle = '';
            
            if (dimensions.width) {
                iframeStyle += `width: ${dimensions.width}px; `;
            }
            
            if (dimensions.height) {
                iframeStyle += `height: ${dimensions.height}px; `;
            }
            
            videoHtml = `
                <div class="video-container ${alignmentClass}">
                    <iframe 
                        src="${this.videoData.url}${this.getEmbedParams()}"
                        style="${iframeStyle}"
                        frameborder="0" 
                        allowfullscreen
                        title="${this.videoData.title || 'Vidéo'}">
                    </iframe>
                    ${this.videoData.title ? `<div class="video-title">${this.videoData.title}</div>` : ''}
                    ${this.videoData.description ? `<div class="video-description">${this.videoData.description}</div>` : ''}
                </div>
            `;
        } else if (this.videoData.type === 'file') {
            videoHtml = `
                <div class="video-container ${alignmentClass}">
                    <video 
                        controls="${this.videoData.controls}"
                        autoplay="${this.videoData.autoplay}"
                        loop="${this.videoData.loop}"
                        muted="${this.videoData.muted}"
                        style="width: ${dimensions.width || '100%'}; height: ${dimensions.height || 'auto'};">
                        <source src="${this.videoData.url}" type="${this.videoData.file?.type || 'video/mp4'}">
                        Votre navigateur ne supporte pas la lecture de vidéos.
                    </video>
                    ${this.videoData.title ? `<div class="video-title">${this.videoData.title}</div>` : ''}
                    ${this.videoData.description ? `<div class="video-description">${this.videoData.description}</div>` : ''}
                </div>
            `;
        }

        content.innerHTML = videoHtml;
    }

    getEmbedParams() {
        const params = new URLSearchParams();
        
        if (this.videoData.autoplay) params.append('autoplay', '1');
        if (this.videoData.controls === false) params.append('controls', '0');
        if (this.videoData.loop) params.append('loop', '1');
        if (this.videoData.muted) params.append('muted', '1');
        
        return params.toString() ? '?' + params.toString() : '';
    }

    resetToUpload() {
        console.log('🔄 Reset du module vidéo:', this.moduleId);
        
        const content = this.element.querySelector('.module-content');
        if (!content) return;

        content.innerHTML = `
            <div class="video-upload-area">
                <div class="video-placeholder">
                    <div class="upload-icon">🎬</div>
                    <div class="upload-text">Ajouter une vidéo</div>
                    <div class="upload-hint">YouTube, Vimeo ou fichier vidéo</div>
                </div>
                <input type="file" class="video-file-input" accept="video/*" style="display: none;">
            </div>
        `;
        
        this.videoData = {
            type: null,
            url: null,
            file: null,
            title: '',
            description: '',
            width: null,
            height: null,
            aspectRatio: null,
            alignment: 'center',
            autoplay: false,
            controls: true,
            loop: false,
            muted: false
        };
        
        this.bindVideoEvents();
    }

    destroy() {
        console.log('🗑️ Destruction du module vidéo:', this.moduleId);
        this.cleanupOptionsEvents();
        
        // Libérer l'URL temporaire si elle existe
        if (this.videoData.url && this.videoData.type === 'file') {
            URL.revokeObjectURL(this.videoData.url);
        }
    }

    getContent() {
        if (!this.videoData.url) return '';
        
        let videoHtml = '';
        const dimensions = this.calculateDimensions();
        const alignmentClass = this.getAlignmentClass();
        
        if (this.videoData.type === 'youtube' || this.videoData.type === 'vimeo') {
            // Calculer les styles CSS pour l'iframe uniquement
            let iframeStyle = '';
            
            if (dimensions.width) {
                iframeStyle += `width: ${dimensions.width}px; `;
            }
            
            if (dimensions.height) {
                iframeStyle += `height: ${dimensions.height}px; `;
            }
            
            videoHtml = `
                <div class="video-container ${alignmentClass}">
                    <iframe 
                        src="${this.videoData.url}${this.getEmbedParams()}"
                        style="${iframeStyle}"
                        frameborder="0" 
                        allowfullscreen
                        title="${this.videoData.title || 'Vidéo'}">
                    </iframe>
                    ${this.videoData.title ? `<div class="video-title">${this.videoData.title}</div>` : ''}
                    ${this.videoData.description ? `<div class="video-description">${this.videoData.description}</div>` : ''}
                </div>
            `;
        } else if (this.videoData.type === 'file') {
            videoHtml = `
                <div class="video-container ${alignmentClass}">
                    <video 
                        controls="${this.videoData.controls}"
                        autoplay="${this.videoData.autoplay}"
                        loop="${this.videoData.loop}"
                        muted="${this.videoData.muted}"
                        style="width: ${dimensions.width || '100%'}; height: ${dimensions.height || 'auto'};">
                        <source src="${this.videoData.url}" type="${this.videoData.file?.type || 'video/mp4'}">
                        Votre navigateur ne supporte pas la lecture de vidéos.
                    </video>
                    ${this.videoData.title ? `<div class="video-title">${this.videoData.title}</div>` : ''}
                    ${this.videoData.description ? `<div class="video-description">${this.videoData.description}</div>` : ''}
                </div>
            `;
        }
        
        return videoHtml;
    }

    getOptionsHTML() {
        if (!this.videoData.url) {
            return `
                <div class="video-options">
                    <h4>Options de la vidéo</h4>
                    <p>Aucune vidéo sélectionnée</p>
                    <div class="video-action-buttons">
                        <button type="button" class="video-action-btn" data-action="upload">
                            <span class="icon">📁</span> Fichier vidéo
                        </button>
                        <button type="button" class="video-action-btn" data-action="url">
                            <span class="icon">🔗</span> URL YouTube/Vimeo
                        </button>
                    </div>
                </div>
            `;
        }

        return `
            <div class="video-options">
                <h4>Options de la vidéo</h4>
                
                <div class="option-group">
                    <label>Actions :</label>
                    <div class="video-action-buttons">
                        <button type="button" class="video-action-btn" data-action="upload">
                            <span class="icon">📁</span> Changer le fichier
                        </button>
                        <button type="button" class="video-action-btn" data-action="url">
                            <span class="icon">🔗</span> Changer l'URL
                        </button>
                        <button type="button" class="video-action-btn danger" data-action="remove">
                            <span class="icon">🗑️</span> Supprimer la vidéo
                        </button>
                    </div>
                </div>

                <div class="option-group">
                    <label>Titre :</label>
                    <input type="text" class="video-title" value="${this.videoData.title}" placeholder="Titre de la vidéo">
                </div>

                <div class="option-group">
                    <label>Description :</label>
                    <textarea class="video-description" placeholder="Description de la vidéo">${this.videoData.description}</textarea>
                </div>

                <div class="option-group">
                    <label>Dimensions :</label>
                    <div class="size-inputs">
                        <input type="number" class="video-width" value="${this.videoData.width || ''}" placeholder="Largeur">
                        <span class="size-unit">px</span>
                        <span class="size-unit">×</span>
                        <input type="number" class="video-height" value="${this.videoData.height || ''}" placeholder="Hauteur">
                        <span class="size-unit">px</span>
                    </div>
                    ${this.videoData.aspectRatio ? `<div class="aspect-ratio-info">Ratio: ${this.videoData.aspectRatio.toFixed(2)}:1</div>` : ''}
                </div>

                <div class="option-group">
                    <label>Alignement :</label>
                    <div class="alignment-buttons">
                        <button type="button" class="align-btn ${this.videoData.alignment === 'left' ? 'active' : ''}" data-alignment="left">
                            <span class="icon">⬅️</span> Gauche
                        </button>
                        <button type="button" class="align-btn ${this.videoData.alignment === 'center' ? 'active' : ''}" data-alignment="center">
                            <span class="icon">↔️</span> Centre
                        </button>
                        <button type="button" class="align-btn ${this.videoData.alignment === 'right' ? 'active' : ''}" data-alignment="right">
                            <span class="icon">➡️</span> Droite
                        </button>
                    </div>
                </div>

                <div class="option-group">
                    <label>Options de lecture :</label>
                    <div class="video-checkboxes">
                        <label class="checkbox-label">
                            <input type="checkbox" class="video-autoplay" ${this.videoData.autoplay ? 'checked' : ''}>
                            Lecture automatique
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" class="video-controls" ${this.videoData.controls ? 'checked' : ''}>
                            Afficher les contrôles
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" class="video-loop" ${this.videoData.loop ? 'checked' : ''}>
                            Lecture en boucle
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" class="video-muted" ${this.videoData.muted ? 'checked' : ''}>
                            Muet
                        </label>
                    </div>
                </div>
            </div>
        `;
    }

    bindOptionsEvents() {
        const optionsContent = this.editor.optionsContent;
        if (!optionsContent) return;

        // Nettoyer les événements précédents
        this.cleanupOptionsEvents();

        // Actions vidéo - avec gestion spécifique au module sélectionné uniquement
        this.optionsClickHandler = (e) => {
            const videoActionBtn = e.target.closest('.video-action-btn');
            if (videoActionBtn) {
                // Vérifier que ce module est bien le module sélectionné
                if (this.editor.currentModule && this.editor.currentModule.moduleId === this.moduleId) {
                    const action = videoActionBtn.dataset.action;
                    console.log('🎛️ Action d\'option vidéo détectée pour le module sélectionné:', this.moduleId, 'Action:', action);
                    this.handleOptionAction(action);
                } else {
                    console.log('⚠️ Action ignorée - module non sélectionné:', this.moduleId);
                }
            }
        };

        optionsContent.addEventListener('click', this.optionsClickHandler);

        // Propriétés vidéo
        const videoWidth = optionsContent.querySelector('.video-width');
        const videoHeight = optionsContent.querySelector('.video-height');
        const videoTitle = optionsContent.querySelector('.video-title');
        const videoDescription = optionsContent.querySelector('.video-description');
        const videoAutoplay = optionsContent.querySelector('.video-autoplay');
        const videoControls = optionsContent.querySelector('.video-controls');
        const videoLoop = optionsContent.querySelector('.video-loop');
        const videoMuted = optionsContent.querySelector('.video-muted');

        if (videoWidth) {
            this.widthChangeHandler = (e) => {
                this.videoData.width = e.target.value ? parseInt(e.target.value) : null;
                // Si on a un ratio d'aspect et qu'on change la largeur, calculer automatiquement la hauteur
                if (this.videoData.aspectRatio && this.videoData.width && !this.videoData.height) {
                    const calculatedHeight = Math.round(this.videoData.width / this.videoData.aspectRatio);
                    const heightInput = optionsContent.querySelector('.video-height');
                    if (heightInput) {
                        heightInput.value = calculatedHeight;
                    }
                }
                this.displayVideo();
            };
            videoWidth.addEventListener('change', this.widthChangeHandler);
        }

        if (videoHeight) {
            this.heightChangeHandler = (e) => {
                this.videoData.height = e.target.value ? parseInt(e.target.value) : null;
                // Si on a un ratio d'aspect et qu'on change la hauteur, calculer automatiquement la largeur
                if (this.videoData.aspectRatio && this.videoData.height && !this.videoData.width) {
                    const calculatedWidth = Math.round(this.videoData.height * this.videoData.aspectRatio);
                    const widthInput = optionsContent.querySelector('.video-width');
                    if (widthInput) {
                        widthInput.value = calculatedWidth;
                    }
                }
                this.displayVideo();
            };
            videoHeight.addEventListener('change', this.heightChangeHandler);
        }

        if (videoTitle) {
            this.titleInputHandler = (e) => {
                this.videoData.title = e.target.value;
                this.displayVideo();
            };
            videoTitle.addEventListener('input', this.titleInputHandler);
        }

        if (videoDescription) {
            this.descriptionInputHandler = (e) => {
                this.videoData.description = e.target.value;
                this.displayVideo();
            };
            videoDescription.addEventListener('input', this.descriptionInputHandler);
        }

        if (videoAutoplay) {
            this.autoplayChangeHandler = (e) => {
                this.videoData.autoplay = e.target.checked;
                this.displayVideo();
            };
            videoAutoplay.addEventListener('change', this.autoplayChangeHandler);
        }

        if (videoControls) {
            this.controlsChangeHandler = (e) => {
                this.videoData.controls = e.target.checked;
                this.displayVideo();
            };
            videoControls.addEventListener('change', this.controlsChangeHandler);
        }

        if (videoLoop) {
            this.loopChangeHandler = (e) => {
                this.videoData.loop = e.target.checked;
                this.displayVideo();
            };
            videoLoop.addEventListener('change', this.loopChangeHandler);
        }

        if (videoMuted) {
            this.mutedChangeHandler = (e) => {
                this.videoData.muted = e.target.checked;
                this.displayVideo();
            };
            videoMuted.addEventListener('change', this.mutedChangeHandler);
        }

        // Gestion des boutons d'alignement
        const alignmentButtons = optionsContent.querySelectorAll('.align-btn');
        alignmentButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                const alignment = e.target.closest('.align-btn').dataset.alignment;
                this.videoData.alignment = alignment;
                
                // Mettre à jour les classes actives
                alignmentButtons.forEach(b => b.classList.remove('active'));
                e.target.closest('.align-btn').classList.add('active');
                
                this.displayVideo();
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

        const videoWidth = optionsContent.querySelector('.video-width');
        const videoHeight = optionsContent.querySelector('.video-height');
        const videoTitle = optionsContent.querySelector('.video-title');
        const videoDescription = optionsContent.querySelector('.video-description');
        const videoAutoplay = optionsContent.querySelector('.video-autoplay');
        const videoControls = optionsContent.querySelector('.video-controls');
        const videoLoop = optionsContent.querySelector('.video-loop');
        const videoMuted = optionsContent.querySelector('.video-muted');

        if (videoWidth && this.widthChangeHandler) {
            videoWidth.removeEventListener('change', this.widthChangeHandler);
        }

        if (videoHeight && this.heightChangeHandler) {
            videoHeight.removeEventListener('change', this.heightChangeHandler);
        }

        if (videoTitle && this.titleInputHandler) {
            videoTitle.removeEventListener('input', this.titleInputHandler);
        }

        if (videoDescription && this.descriptionInputHandler) {
            videoDescription.removeEventListener('input', this.descriptionInputHandler);
        }

        if (videoAutoplay && this.autoplayChangeHandler) {
            videoAutoplay.removeEventListener('change', this.autoplayChangeHandler);
        }

        if (videoControls && this.controlsChangeHandler) {
            videoControls.removeEventListener('change', this.controlsChangeHandler);
        }

        if (videoLoop && this.loopChangeHandler) {
            videoLoop.removeEventListener('change', this.loopChangeHandler);
        }

        if (videoMuted && this.mutedChangeHandler) {
            videoMuted.removeEventListener('change', this.mutedChangeHandler);
        }
    }

    loadData(data) {
        console.log('📂 Chargement des données vidéo:', data);
        
        // Appliquer les données au module
        this.videoData = {
            ...this.videoData,
            ...data
        };
        
        // Afficher la vidéo avec les données chargées
        this.displayVideo();
        
        console.log('✅ Données vidéo chargées avec succès');
    }

    handleOptionAction(action) {
        console.log('🎛️ Action d\'option vidéo pour le module:', this.moduleId, 'Action:', action);
        
        switch (action) {
            case 'upload':
                // Créer un input file temporaire pour l'upload
                const tempFileInput = document.createElement('input');
                tempFileInput.type = 'file';
                tempFileInput.accept = 'video/*';
                tempFileInput.style.display = 'none';
                
                tempFileInput.addEventListener('change', (e) => {
                    if (e.target.files.length > 0) {
                        console.log('📁 Fichier vidéo sélectionné pour le module:', this.moduleId, e.target.files[0].name);
                        this.handleVideoFile(e.target.files[0]);
                    }
                    // Nettoyer l'input temporaire
                    if (document.body.contains(tempFileInput)) {
                        document.body.removeChild(tempFileInput);
                    }
                });
                
                document.body.appendChild(tempFileInput);
                tempFileInput.click();
                break;
                
            case 'url':
                const url = prompt('Entrez l\'URL de la vidéo (YouTube ou Vimeo):');
                if (url && url.trim()) {
                    this.handleVideoUrl(url.trim());
                }
                break;
                
            case 'remove':
                if (confirm('Supprimer cette vidéo ?')) {
                    console.log('🗑️ Suppression de la vidéo pour le module:', this.moduleId);
                    this.resetToUpload();
                    this.editor.hideOptions();
                }
                break;
        }
    }
}

// Rendre disponible globalement
window.VideoModule = VideoModule;
