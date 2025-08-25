/**
 * Éditeur WYSIWYG avancé avec blocs - Belgium Vidéo Gaming
 * Éditeur complet avec drag & drop et blocs organisables
 */

class AdvancedWysiwygEditor {
    constructor(container, options = {}) {
        this.container = typeof container === 'string' ? document.querySelector(container) : container;
        this.options = {
            placeholder: 'Commencez à créer votre article...',
            height: '600px',
            ...options
        };
        
        this.blocks = [];
        this.currentBlock = null;
        this.init();
    }
    
    init() {
        this.createToolbar();
        this.createEditor();
        this.bindEvents();
        this.addDefaultStyles();
    }
    
    createToolbar() {
        const toolbar = document.createElement('div');
        toolbar.className = 'advanced-toolbar';
        toolbar.innerHTML = `
            <div class="toolbar-section">
                <button type="button" class="toolbar-btn" data-action="add-text">
                    <span class="icon">✏️</span> Texte
                </button>
                <button type="button" class="toolbar-btn" data-action="add-heading">
                    <span class="icon">📝</span> Titre
                </button>
                <button type="button" class="toolbar-btn" data-action="add-image">
                    <span class="icon">🖼️</span> Image
                </button>
                <button type="button" class="toolbar-btn" data-action="add-video">
                    <span class="icon">🎬</span> Vidéo
                </button>
            </div>
            
            <div class="toolbar-section">
                <button type="button" class="toolbar-btn" data-action="add-quote">
                    <span class="icon">💭</span> Citation
                </button>
                <button type="button" class="toolbar-btn" data-action="add-gallery">
                    <span class="icon">🖼️</span> Galerie
                </button>
                <button type="button" class="toolbar-btn" data-action="add-list">
                    <span class="icon">📋</span> Liste
                </button>
                <button type="button" class="toolbar-btn" data-action="add-divider">
                    <span class="icon">➖</span> Séparateur
                </button>
            </div>
            
            <div class="toolbar-section">
                <button type="button" class="toolbar-btn" data-action="preview">
                    <span class="icon">👁️</span> Aperçu
                </button>
                <button type="button" class="toolbar-btn" data-action="save">
                    <span class="icon">💾</span> Sauvegarder
                </button>
            </div>
        `;
        
        this.container.appendChild(toolbar);
        this.toolbar = toolbar;
    }
    
    createEditor() {
        const editor = document.createElement('div');
        editor.className = 'advanced-editor';
        editor.innerHTML = `
            <div class="editor-content">
                <div class="placeholder-message">
                    <div class="placeholder-icon">🎨</div>
                    <div class="placeholder-text">Commencez à créer votre contenu en utilisant les outils ci-dessus</div>
                </div>
            </div>
        `;
        
        this.container.appendChild(editor);
        this.editor = editor;
        this.editorContent = editor.querySelector('.editor-content');
    }
    
    bindEvents() {
        // Gestion des boutons de la barre d'outils
        this.toolbar.addEventListener('click', (e) => {
            if (e.target.closest('.toolbar-btn')) {
                const btn = e.target.closest('.toolbar-btn');
                const action = btn.dataset.action;
                this.handleToolbarAction(action);
            }
        });
        
        // Gestion des événements de l'éditeur
        this.editor.addEventListener('click', (e) => {
            if (e.target.closest('.content-block')) {
                this.selectBlock(e.target.closest('.content-block'));
            }
        });
        
        // Gestion des événements globaux
        document.addEventListener('click', (e) => {
            if (!this.editor.contains(e.target)) {
                this.deselectAllBlocks();
            }
        });
    }
    
    handleToolbarAction(action) {
        switch (action) {
            case 'add-text':
                this.addTextBlock();
                break;
            case 'add-image':
                this.addImageBlock();
                break;
            case 'add-video':
                this.addVideoBlock();
                break;
            case 'add-quote':
                this.addQuoteBlock();
                break;
            case 'add-gallery':
                this.addGalleryBlock();
                break;
            case 'add-heading':
                this.addHeadingBlock();
                break;
            case 'add-list':
                this.addListBlock();
                break;
            case 'add-divider':
                this.addDividerBlock();
                break;
            case 'preview':
                this.showPreview();
                break;
            case 'save':
                this.saveContent();
                break;
        }
    }
    
    addTextBlock() {
        const block = this.createBlock('text', {
            content: '<p>Commencez à écrire votre texte ici...</p>',
            placeholder: 'Tapez votre texte...'
        });
        this.addBlock(block);
        
        // Ajouter la barre d'outils de formatage
        this.addTextFormattingToolbar(block);
    }
    
    addImageBlock() {
        const block = this.createBlock('image', {
            content: '<div class="image-placeholder"><span class="icon">🖼️</span><div>Cliquez pour ajouter une image</div></div>',
            placeholder: 'URL de l\'image ou glissez-déposez'
        });
        this.addBlock(block);
        
        // Ajouter la fonctionnalité d'upload
        const imageBlock = block.querySelector('.image-placeholder');
        imageBlock.addEventListener('click', () => this.handleImageUpload(block));
    }
    
    handleImageUpload(block) {
        // Créer un input file caché
        const fileInput = document.createElement('input');
        fileInput.type = 'file';
        fileInput.accept = 'image/*';
        fileInput.style.display = 'none';
        
        fileInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                this.uploadImage(file, block);
            }
        });
        
        document.body.appendChild(fileInput);
        fileInput.click();
        document.body.removeChild(fileInput);
    }
    
    uploadImage(file, block) {
        // Créer un FormData pour l'upload
        const formData = new FormData();
        formData.append('image', file);
        formData.append('type', 'article');
        
        // Afficher un indicateur de chargement
        const placeholder = block.querySelector('.image-placeholder');
        placeholder.innerHTML = '📤 Upload en cours...';
        
        // Simuler l'upload (remplacez par votre endpoint réel)
        fetch('/admin/upload-image', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Afficher l'image
                const imageContent = block.querySelector('.block-content');
                imageContent.innerHTML = `
                    <img src="${data.url}" alt="${file.name}" style="max-width: 100%; height: auto;">
                    <div class="image-caption" contenteditable="true">Légende de l'image (cliquez pour éditer)</div>
                `;
                placeholder.style.display = 'none';
                
                // Ajouter les contrôles d'image
                this.addImageControls(block, data.url);
            } else {
                placeholder.innerHTML = '❌ Erreur upload: ' + data.message;
            }
        })
        .catch(error => {
            console.error('Erreur upload:', error);
            placeholder.innerHTML = '❌ Erreur upload. Réessayez.';
        });
    }
    
    addImageControls(block, imageUrl) {
        const controls = document.createElement('div');
        controls.className = 'image-controls';
        controls.innerHTML = `
            <button type="button" class="image-btn" data-action="resize" title="Redimensionner">📏</button>
            <button type="button" class="image-btn" data-action="align-left" title="Aligner à gauche">⬅️</button>
            <button type="button" class="image-btn" data-action="align-center" title="Centrer">↔️</button>
            <button type="button" class="image-btn" data-action="align-right" title="Aligner à droite">➡️</button>
            <button type="button" class="image-btn" data-action="remove" title="Supprimer l'image">🗑️</button>
        `;
        
        block.appendChild(controls);
        
        // Gérer les actions des contrôles
        controls.addEventListener('click', (e) => {
            const btn = e.target.closest('.image-btn');
            if (btn) {
                const action = btn.dataset.action;
                this.handleImageAction(block, action, imageUrl);
            }
        });
    }
    
    handleImageAction(block, action, imageUrl) {
        const image = block.querySelector('img');
        
        switch (action) {
            case 'resize':
                const width = prompt('Largeur en pixels (laissez vide pour auto):');
                if (width && !isNaN(width)) {
                    image.style.width = width + 'px';
                    image.style.height = 'auto';
                }
                break;
            case 'align-left':
                image.style.display = 'block';
                image.style.margin = '0 auto 0 0';
                break;
            case 'align-center':
                image.style.display = 'block';
                image.style.margin = '0 auto';
                break;
            case 'align-right':
                image.style.display = 'block';
                image.style.margin = '0 0 0 auto';
                break;
            case 'remove':
                if (confirm('Supprimer cette image ?')) {
                    const imageContent = block.querySelector('.block-content');
                    imageContent.innerHTML = '<div class="image-placeholder"><span class="icon">🖼️</span><div>Cliquez pour ajouter une image</div></div>';
                    block.querySelector('.image-controls')?.remove();
                    block.querySelector('.image-placeholder').style.display = 'block';
                }
                break;
        }
    }
    
    addVideoBlock() {
        const block = this.createBlock('video', {
            content: '<div class="video-placeholder"><span class="icon">🎬</span><div>Cliquez pour ajouter une vidéo</div></div>',
            placeholder: 'URL de la vidéo (YouTube, Vimeo...)'
        });
        this.addBlock(block);
        
        // Ajouter la fonctionnalité d'ajout de vidéo
        const videoBlock = block.querySelector('.video-placeholder');
        videoBlock.addEventListener('click', () => this.handleVideoAdd(block));
    }
    
    handleVideoAdd(block) {
        const url = prompt('Entrez l\'URL de la vidéo YouTube ou Vimeo:');
        if (url) {
            this.embedVideo(url, block);
        }
    }
    
    embedVideo(url, block) {
        let embedCode = '';
        let videoId = '';
        
        // Détecter le type de vidéo et extraire l'ID
        if (url.includes('youtube.com/watch') || url.includes('youtu.be/')) {
            // YouTube
            if (url.includes('youtube.com/watch')) {
                videoId = url.match(/[?&]v=([^&]+)/)?.[1];
            } else if (url.includes('youtu.be/')) {
                videoId = url.match(/youtu\.be\/([^?]+)/)?.[1];
            }
            
            if (videoId) {
                embedCode = `
                    <div class="video-container">
                        <iframe 
                            width="100%" 
                            height="315" 
                            src="https://www.youtube.com/embed/${videoId}" 
                            frameborder="0" 
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                            allowfullscreen>
                        </iframe>
                        <div class="video-caption" contenteditable="true">Légende de la vidéo (cliquez pour éditer)</div>
                    </div>
                `;
            }
        } else if (url.includes('vimeo.com/')) {
            // Vimeo
            videoId = url.match(/vimeo\.com\/(\d+)/)?.[1];
            
            if (videoId) {
                embedCode = `
                    <div class="video-container">
                        <iframe 
                            width="100%" 
                            height="315" 
                            src="https://player.vimeo.com/video/${videoId}" 
                            frameborder="0" 
                            allow="autoplay; fullscreen; picture-in-picture" 
                            allowfullscreen>
                        </iframe>
                        <div class="video-caption" contenteditable="true">Légende de la vidéo (cliquez pour éditer)</div>
                    </div>
                `;
            }
        }
        
        if (embedCode) {
            // Afficher la vidéo
            const videoContent = block.querySelector('.block-content');
            videoContent.innerHTML = embedCode;
            block.querySelector('.video-placeholder').style.display = 'none';
            
            // Ajouter les contrôles vidéo
            this.addVideoControls(block, url);
        } else {
            alert('URL de vidéo non reconnue. Utilisez YouTube ou Vimeo.');
        }
    }
    
    addVideoControls(block, videoUrl) {
        const controls = document.createElement('div');
        controls.className = 'video-controls';
        controls.innerHTML = `
            <button type="button" class="video-btn" data-action="resize" title="Redimensionner">📏</button>
            <button type="button" class="video-btn" data-action="autoplay" title="Lecture automatique">▶️</button>
            <button type="button" class="video-btn" data-action="remove" title="Supprimer la vidéo">🗑️</button>
        `;
        
        block.appendChild(controls);
        
        // Gérer les actions des contrôles
        controls.addEventListener('click', (e) => {
            const btn = e.target.closest('.video-btn');
            if (btn) {
                const action = btn.dataset.action;
                this.handleVideoAction(block, action, videoUrl);
            }
        });
    }
    
    handleVideoAction(block, action, videoUrl) {
        const iframe = block.querySelector('iframe');
        
        switch (action) {
            case 'resize':
                const width = prompt('Largeur en pixels (laissez vide pour 100%):');
                if (width && !isNaN(width)) {
                    iframe.style.width = width + 'px';
                } else if (width === '') {
                    iframe.style.width = '100%';
                }
                break;
            case 'autoplay':
                const currentSrc = iframe.src;
                if (currentSrc.includes('autoplay=1')) {
                    iframe.src = currentSrc.replace('autoplay=1', 'autoplay=0');
                    e.target.textContent = '▶️';
                    e.target.title = 'Lecture automatique';
                } else {
                    iframe.src = currentSrc + (currentSrc.includes('?') ? '&' : '?') + 'autoplay=1';
                    e.target.textContent = '⏸️';
                    e.target.title = 'Lecture automatique activée';
                }
                break;
            case 'remove':
                if (confirm('Supprimer cette vidéo ?')) {
                    const videoContent = block.querySelector('.block-content');
                    videoContent.innerHTML = '<div class="video-placeholder"><span class="icon">🎬</span><div>Cliquez pour ajouter une vidéo</div></div>';
                    block.querySelector('.video-controls')?.remove();
                    block.querySelector('.video-placeholder').style.display = 'block';
                }
                break;
        }
    }
    
    addQuoteBlock() {
        const block = this.createBlock('quote', {
            content: '<blockquote>Votre citation ici...</blockquote>',
            placeholder: 'Tapez votre citation...'
        });
        this.addBlock(block);
    }
    
    addHeadingBlock() {
        const block = this.createBlock('heading', {
            content: '<h2>Titre de section</h2>',
            placeholder: 'Tapez votre titre...'
        });
        this.addBlock(block);
    }
    
    addListBlock() {
        const block = this.createBlock('list', {
            content: '<ul><li>Premier élément</li><li>Deuxième élément</li></ul>',
            placeholder: 'Ajoutez vos éléments de liste...'
        });
        this.addBlock(block);
    }
    
    addDividerBlock() {
        const block = this.createBlock('divider', {
            content: '<hr>',
            placeholder: ''
        });
        this.addBlock(block);
    }
    
    addGalleryBlock() {
        const block = this.createBlock('gallery', {
            content: '<div class="gallery-placeholder"><span class="icon">🖼️</span><div>Cliquez pour créer une galerie d\'images</div></div>',
            placeholder: 'Ajoutez des images à votre galerie'
        });
        this.addBlock(block);
        
        // Ajouter la fonctionnalité de galerie
        const galleryBlock = block.querySelector('.gallery-placeholder');
        galleryBlock.addEventListener('click', () => this.handleGalleryCreate(block));
    }
    
    handleGalleryCreate(block) {
        const imageCount = prompt('Combien d\'images voulez-vous dans cette galerie ? (1-10)');
        const count = parseInt(imageCount);
        
        if (count && count > 0 && count <= 10) {
            this.createGallery(block, count);
        } else if (imageCount !== null) {
            alert('Veuillez entrer un nombre entre 1 et 10.');
        }
    }
    
    createGallery(block, imageCount) {
        const galleryContent = block.querySelector('.block-content');
        let galleryHTML = '<div class="gallery-grid">';
        
        for (let i = 0; i < imageCount; i++) {
            galleryHTML += `
                <div class="gallery-item" data-index="${i}">
                    <div class="gallery-image-placeholder">🖼️ Image ${i + 1}</div>
                    <div class="gallery-caption" contenteditable="true">Légende ${i + 1}</div>
                </div>
            `;
        }
        
        galleryHTML += '</div>';
        galleryContent.innerHTML = galleryHTML;
        
        // Rendre les placeholders cliquables
        const placeholders = galleryContent.querySelectorAll('.gallery-image-placeholder');
        placeholders.forEach((placeholder, index) => {
            placeholder.addEventListener('click', () => this.handleGalleryImageUpload(block, index));
        });
        
        // Ajouter les contrôles de galerie
        this.addGalleryControls(block);
        
        // Masquer le placeholder principal
        block.querySelector('.gallery-placeholder').style.display = 'none';
    }
    
    handleGalleryImageUpload(block, index) {
        const fileInput = document.createElement('input');
        fileInput.type = 'file';
        fileInput.accept = 'image/*';
        fileInput.style.display = 'none';
        
        fileInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                this.uploadGalleryImage(file, block, index);
            }
        });
        
        document.body.appendChild(fileInput);
        fileInput.click();
        document.body.removeChild(fileInput);
    }
    
    uploadGalleryImage(file, block, index) {
        const formData = new FormData();
        formData.append('image', file);
        formData.append('type', 'gallery');
        
        const placeholder = block.querySelector(`[data-index="${index}"] .gallery-image-placeholder`);
        placeholder.innerHTML = '📤 Upload...';
        
        // Simuler l'upload (remplacez par votre endpoint réel)
        fetch('/admin/upload-image', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                placeholder.innerHTML = `<img src="${data.url}" alt="${file.name}" style="width: 100%; height: 100%; object-fit: cover;">`;
            } else {
                placeholder.innerHTML = '❌ Erreur: ' + data.message;
            }
        })
        .catch(error => {
            console.error('Erreur upload galerie:', error);
            placeholder.innerHTML = '❌ Erreur upload';
        });
    }
    
    addGalleryControls(block) {
        const controls = document.createElement('div');
        controls.className = 'gallery-controls';
        controls.innerHTML = `
            <button type="button" class="gallery-btn" data-action="layout" title="Changer la disposition">🔀</button>
            <button type="button" class="gallery-btn" data-action="add-image" title="Ajouter une image">➕</button>
            <button type="button" class="gallery-btn" data-action="remove" title="Supprimer la galerie">🗑️</button>
        `;
        
        block.appendChild(controls);
        
        controls.addEventListener('click', (e) => {
            const btn = e.target.closest('.gallery-btn');
            if (btn) {
                const action = btn.dataset.action;
                this.handleGalleryAction(block, action);
            }
        });
    }
    
    handleGalleryAction(block, action) {
        switch (action) {
            case 'layout':
                const layouts = ['grid', 'masonry', 'carousel'];
                const currentLayout = block.querySelector('.gallery-grid').className;
                const currentIndex = layouts.findIndex(l => currentLayout.includes(l));
                const nextLayout = layouts[(currentIndex + 1) % layouts.length];
                
                const galleryGrid = block.querySelector('.gallery-grid');
                galleryGrid.className = `gallery-grid gallery-${nextLayout}`;
                break;
                
            case 'add-image':
                const currentCount = block.querySelectorAll('.gallery-item').length;
                if (currentCount < 10) {
                    this.addGalleryImage(block, currentCount);
                } else {
                    alert('Maximum 10 images dans une galerie.');
                }
                break;
                
            case 'remove':
                if (confirm('Supprimer cette galerie ?')) {
                    const galleryContent = block.querySelector('.block-content');
                    galleryContent.innerHTML = '<div class="gallery-placeholder"><span class="icon">🖼️</span><div>Cliquez pour créer une galerie d\'images</div></div>';
                    block.querySelector('.gallery-controls')?.remove();
                    block.querySelector('.gallery-placeholder').style.display = 'block';
                }
                break;
        }
    }
    
    addGalleryImage(block, index) {
        const galleryGrid = block.querySelector('.gallery-grid');
        const newItem = document.createElement('div');
        newItem.className = 'gallery-item';
        newItem.dataset.index = index;
        newItem.innerHTML = `
            <div class="gallery-image-placeholder">🖼️ Image ${index + 1}</div>
            <div class="gallery-caption" contenteditable="true">Légende ${index + 1}</div>
        `;
        
        galleryGrid.appendChild(newItem);
        
        // Rendre le nouveau placeholder cliquable
        const placeholder = newItem.querySelector('.gallery-image-placeholder');
        placeholder.addEventListener('click', () => this.handleGalleryImageUpload(block, index));
    }
    
    createBlock(type, data) {
        const block = document.createElement('div');
        block.className = `content-block content-block-${type}`;
        block.dataset.type = type;
        block.dataset.blockId = this.generateBlockId();
        
        block.innerHTML = `
            <div class="block-header">
                <div class="block-title">
                    <span class="icon">${this.getBlockIcon(type)}</span>
                    ${this.getBlockTypeLabel(type)}
                </div>
                <div class="block-controls">
                    <button type="button" class="block-btn" data-action="move-up" title="Déplacer vers le haut">⬆️</button>
                    <button type="button" class="block-btn" data-action="move-down" title="Déplacer vers le bas">⬇️</button>
                    <button type="button" class="block-btn delete" data-action="delete" title="Supprimer">🗑️</button>
                </div>
            </div>
            <div class="block-content" contenteditable="true">${data.content}</div>
        `;
        
        // Gestion des événements du bloc
        this.bindBlockEvents(block);
        
        return block;
    }
    
    bindBlockEvents(block) {
        // Actions du bloc
        block.addEventListener('click', (e) => {
            if (e.target.closest('.block-btn')) {
                const btn = e.target.closest('.block-btn');
                const action = btn.dataset.action;
                this.handleBlockAction(block, action);
            }
        });
        
        // Édition du contenu
        const content = block.querySelector('.block-content');
        content.addEventListener('focus', () => {
            this.selectBlock(block);
            content.classList.add('editing');
        });
        
        content.addEventListener('blur', () => {
            content.classList.remove('editing');
        });
        
        content.addEventListener('input', () => {
            this.updateBlockContent(block);
        });
    }
    
    handleBlockAction(block, action) {
        switch (action) {
            case 'move-up':
                this.moveBlock(block, 'up');
                break;
            case 'move-down':
                this.moveBlock(block, 'down');
                break;
            case 'delete':
                this.deleteBlock(block);
                break;
        }
    }
    
    addBlock(block) {
        this.editorContent.appendChild(block);
        this.blocks.push(block);
        this.hidePlaceholder();
        this.selectBlock(block);
        
        // Focus sur le contenu du bloc
        const content = block.querySelector('.block-content');
        content.focus();
    }
    
    selectBlock(block) {
        this.deselectAllBlocks();
        block.classList.add('selected');
        this.currentBlock = block;
    }
    
    deselectAllBlocks() {
        this.blocks.forEach(b => b.classList.remove('selected'));
        this.currentBlock = null;
    }
    
    moveBlock(block, direction) {
        const currentIndex = Array.from(this.editorContent.children).indexOf(block);
        let newIndex;
        
        if (direction === 'up' && currentIndex > 0) {
            newIndex = currentIndex - 1;
        } else if (direction === 'down' && currentIndex < this.editorContent.children.length - 1) {
            newIndex = currentIndex + 1;
        } else {
            return;
        }
        
        const targetBlock = this.editorContent.children[newIndex];
        if (targetBlock.classList.contains('content-block')) {
            this.editorContent.insertBefore(block, targetBlock);
            
            // Mettre à jour le tableau this.blocks pour maintenir la cohérence
            this.updateBlocksArray();
        }
    }
    
    deleteBlock(block) {
        if (confirm('Êtes-vous sûr de vouloir supprimer ce bloc ?')) {
            block.remove();
            this.blocks = this.blocks.filter(b => b !== block);
            
            if (this.blocks.length === 0) {
                this.showPlaceholder();
            }
        }
    }
    
    updateBlockContent(block) {
        // Sauvegarder le contenu dans le bloc
        const content = block.querySelector('.block-content');
        block.dataset.content = content.innerHTML;
    }
    
    /**
     * Mettre à jour le tableau this.blocks pour refléter l'ordre réel dans le DOM
     */
    updateBlocksArray() {
        this.blocks = Array.from(this.editorContent.querySelectorAll('.content-block'));
    }
    
    /**
     * Ajouter une barre d'outils de formatage pour les blocs texte
     */
    addTextFormattingToolbar(block) {
        const toolbar = document.createElement('div');
        toolbar.className = 'text-formatting-toolbar';
        toolbar.innerHTML = `
            <div class="formatting-section">
                <button type="button" class="format-btn" data-command="bold" title="Gras (Ctrl+B)">
                    <span class="icon">B</span>
                </button>
                <button type="button" class="format-btn" data-command="italic" title="Italique (Ctrl+I)">
                    <span class="icon">I</span>
                </button>
                <button type="button" class="format-btn" data-command="underline" title="Souligné (Ctrl+U)">
                    <span class="icon">U</span>
                </button>
                <button type="button" class="format-btn" data-command="strikethrough" title="Barré">
                    <span class="icon">S</span>
                </button>
            </div>
            
            <div class="formatting-section">
                <button type="button" class="format-btn" data-command="h1" title="Titre 1">H1</button>
                <button type="button" class="format-btn" data-command="h2" title="Titre 2">H2</button>
                <button type="button" class="format-btn" data-command="h3" title="Titre 3">H3</button>
                <button type="button" class="format-btn" data-command="p" title="Paragraphe">P</button>
            </div>
            
            <div class="formatting-section">
                <button type="button" class="format-btn" data-command="justifyLeft" title="Aligner à gauche">
                    <span class="icon">⫷</span>
                </button>
                <button type="button" class="format-btn" data-command="justifyCenter" title="Centrer">
                    <span class="icon">⫸</span>
                </button>
                <button type="button" class="format-btn" data-command="justifyRight" title="Aligner à droite">
                    <span class="icon">⫹</span>
                </button>
                <button type="button" class="format-btn" data-command="justifyFull" title="Justifier">
                    <span class="icon">⫺</span>
                </button>
            </div>
            
            <div class="formatting-section">
                <button type="button" class="format-btn" data-command="insertUnorderedList" title="Liste à puces">
                    <span class="icon">•</span>
                </button>
                <button type="button" class="format-btn" data-command="insertOrderedList" title="Liste numérotée">
                    <span class="icon">1.</span>
                </button>
                <button type="button" class="format-btn" data-command="createLink" title="Insérer un lien">
                    <span class="icon">🔗</span>
                </button>
                <button type="button" class="format-btn" data-command="removeFormat" title="Supprimer le formatage">
                    <span class="icon">🧹</span>
                </button>
            </div>
            
            <div class="formatting-section">
                <select class="font-size-select" title="Taille de police">
                    <option value="12px">12px</option>
                    <option value="14px">14px</option>
                    <option value="16px" selected>16px</option>
                    <option value="18px">18px</option>
                    <option value="20px">20px</option>
                    <option value="24px">24px</option>
                </select>
                <input type="color" class="color-picker" value="#000000" title="Couleur du texte">
            </div>
        `;
        
        // Insérer la barre d'outils après l'en-tête du bloc
        const blockHeader = block.querySelector('.block-header');
        blockHeader.after(toolbar);
        
        // Gérer les événements de la barre d'outils
        this.bindTextFormattingEvents(block, toolbar);
    }
    
    /**
     * Gérer les événements de la barre d'outils de formatage
     */
    bindTextFormattingEvents(block, toolbar) {
        const content = block.querySelector('.block-content');
        
        // Gestion des boutons de formatage
        toolbar.addEventListener('click', (e) => {
            const btn = e.target.closest('.format-btn');
            if (!btn) return;
            
            e.preventDefault();
            const command = btn.dataset.command;
            
            // Focus sur le contenu pour appliquer la commande
            content.focus();
            
            switch (command) {
                case 'createLink':
                    this.createLink(content);
                    break;
                case 'removeFormat':
                    document.execCommand('removeFormat', false, null);
                    break;
                default:
                    // Commandes standard execCommand
                    document.execCommand(command, false, null);
                    break;
            }
            
            // Mettre à jour l'état des boutons
            this.updateFormattingButtons(block, toolbar);
        });
        
        // Gestion de la taille de police
        const fontSizeSelect = toolbar.querySelector('.font-size-select');
        fontSizeSelect.addEventListener('change', (e) => {
            content.focus();
            
            const size = e.target.value;
            const selection = window.getSelection();
            
            // Vérifier si on a une sélection valide
            if (selection.rangeCount > 0) {
                const range = selection.getRangeAt(0);
                
                // Si on a une sélection de texte
                if (!range.collapsed) {
                    // Utiliser une approche plus robuste pour la sélection
                    this.applyFontSizeToSelection(size);
                } else {
                    // Pas de sélection, appliquer au contenu entier
                    this.applyFontSizeToContent(content, size);
                }
            } else {
                // Pas de sélection, appliquer au contenu entier
                this.applyFontSizeToContent(content, size);
            }
            
            // Mettre à jour l'état des boutons
            this.updateFormattingButtons(block, toolbar);
        });
        
        // Gestion de la couleur
        const colorPicker = toolbar.querySelector('.color-picker');
        colorPicker.addEventListener('change', (e) => {
            content.focus();
            
            const color = e.target.value;
            const selection = window.getSelection();
            
            if (selection.rangeCount > 0) {
                const range = selection.getRangeAt(0);
                if (!range.collapsed) {
                    // Appliquer à la sélection avec une approche plus robuste
                    this.applyColorToSelection(color);
                } else {
                    // Appliquer au contenu entier si aucune sélection
                    this.applyColorToContent(content, color);
                }
            } else {
                // Appliquer au contenu entier si aucune sélection
                this.applyColorToContent(content, color);
            }
            
            // Mettre à jour l'état des boutons
            this.updateFormattingButtons(block, toolbar);
        });
        
        // Mise à jour des boutons lors de la sélection
        content.addEventListener('keyup', () => this.updateFormattingButtons(block, toolbar));
        content.addEventListener('mouseup', () => this.updateFormattingButtons(block, toolbar));
    }
    
    /**
     * Créer un lien
     */
    createLink(content) {
        const url = prompt('Entrez l\'URL du lien:');
        if (url) {
            document.execCommand('createLink', false, url);
        }
    }
    
    /**
     * Appliquer la taille de police à une sélection
     */
    applyFontSizeToSelection(size) {
        const selection = window.getSelection();
        if (selection.rangeCount > 0) {
            const range = selection.getRangeAt(0);
            
            // Créer un span avec la nouvelle taille
            const span = document.createElement('span');
            span.style.fontSize = size;
            
            // Entourer la sélection avec le span
            range.surroundContents(span);
        }
    }
    
    /**
     * Appliquer la taille de police au contenu entier
     */
    applyFontSizeToContent(content, size) {
        // Supprimer toutes les tailles de police existantes
        const elementsWithFontSize = content.querySelectorAll('[style*="font-size"]');
        elementsWithFontSize.forEach(el => {
            el.style.fontSize = '';
        });
        
        // Appliquer la nouvelle taille au contenu principal
        content.style.fontSize = size;
        
        // Appliquer aussi aux paragraphes et autres éléments de texte
        const textElements = content.querySelectorAll('p, span, div, h1, h2, h3, h4, h5, h6');
        textElements.forEach(el => {
            if (el === content) return; // Éviter de modifier le conteneur principal
            el.style.fontSize = size;
        });
    }
    
    /**
     * Appliquer la couleur à une sélection
     */
    applyColorToSelection(color) {
        const selection = window.getSelection();
        if (selection.rangeCount > 0) {
            const range = selection.getRangeAt(0);
            
            // Créer un span avec la nouvelle couleur
            const span = document.createElement('span');
            span.style.color = color;
            
            // Entourer la sélection avec le span
            range.surroundContents(span);
        }
    }
    
    /**
     * Appliquer la couleur au contenu entier
     */
    applyColorToContent(content, color) {
        // Supprimer toutes les couleurs existantes
        const elementsWithColor = content.querySelectorAll('[style*="color"]');
        elementsWithColor.forEach(el => {
            el.style.color = '';
        });
        
        // Appliquer la nouvelle couleur au contenu principal
        content.style.color = color;
        
        // Appliquer aussi aux paragraphes et autres éléments de texte
        const textElements = content.querySelectorAll('p, span, div, h1, h2, h3, h4, h5, h6');
        textElements.forEach(el => {
            if (el === content) return; // Éviter de modifier le conteneur principal
            el.style.color = color;
        });
    }
    
    /**
     * Mettre à jour l'état des boutons de formatage
     */
    updateFormattingButtons(block, toolbar) {
        const content = block.querySelector('.block-content');
        
        // Vérifier l'état de la sélection
        const isBold = document.queryCommandState('bold');
        const isItalic = document.queryCommandState('italic');
        const isUnderline = document.queryCommandState('underline');
        const isStrikethrough = document.queryCommandState('strikethrough');
        
        // Mettre à jour l'apparence des boutons
        toolbar.querySelector('[data-command="bold"]').classList.toggle('active', isBold);
        toolbar.querySelector('[data-command="italic"]').classList.toggle('active', isItalic);
        toolbar.querySelector('[data-command="underline"]').classList.toggle('active', isUnderline);
        toolbar.querySelector('[data-command="strikethrough"]').classList.toggle('active', isStrikethrough);
        
        // Mettre à jour le select de taille de police avec la taille actuelle
        const fontSizeSelect = toolbar.querySelector('.font-size-select');
        if (fontSizeSelect) {
            const currentSize = content.style.fontSize || '16px';
            fontSizeSelect.value = currentSize;
        }
    }
    
    hidePlaceholder() {
        const placeholder = this.editor.querySelector('.placeholder-message');
        if (placeholder) {
            placeholder.style.display = 'none';
        }
    }
    
    showPlaceholder() {
        const placeholder = this.editor.querySelector('.placeholder-message');
        if (placeholder) {
            placeholder.style.display = 'block';
        }
    }
    
    getBlockTypeLabel(type) {
        const labels = {
            'text': 'Texte',
            'image': 'Image',
            'video': 'Vidéo',
            'quote': 'Citation',
            'heading': 'Titre',
            'list': 'Liste',
            'divider': 'Séparateur'
        };
        return labels[type] || type;
    }
    
    getBlockIcon(type) {
        const icons = {
            'text': '✏️',
            'image': '🖼️',
            'video': '🎬',
            'quote': '💭',
            'heading': '📝',
            'list': '📋',
            'divider': '➖'
        };
        return icons[type] || '📄';
    }
    
    generateBlockId() {
        return 'block_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
    }
    
    showPreview() {
        const preview = this.getFormattedContent();
        const previewWindow = window.open('', '_blank');
        previewWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>Aperçu de l'article</title>
                <style>
                    body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
                    .content-block { margin: 20px 0; }
                    .content-block-image img { max-width: 100%; height: auto; }
                    .content-block-quote blockquote { border-left: 4px solid #007bff; padding-left: 20px; font-style: italic; }
                    .content-block-heading h2 { color: #333; border-bottom: 2px solid #007bff; padding-bottom: 10px; }
                </style>
            </head>
            <body>
                <h1>Aperçu de l'article</h1>
                ${preview}
            </body>
            </html>
        `);
    }
    
    saveContent() {
        const content = this.getFormattedContent();
        console.log('Contenu sauvegardé:', content);
        
        // Ici vous pouvez ajouter la logique pour sauvegarder dans la base de données
        alert('Contenu sauvegardé !');
    }
    
    getFormattedContent() {
        let content = '';
        
        // Récupérer l'ordre réel des blocs dans le DOM
        const actualBlocks = Array.from(this.editorContent.querySelectorAll('.content-block'));
        
        actualBlocks.forEach(block => {
            const blockContent = block.querySelector('.block-content').innerHTML;
            const blockType = block.dataset.type;
            
            content += `<div class="content-block content-block-${blockType}">${blockContent}</div>`;
        });
        return content;
    }
    
    getContent() {
        return this.getFormattedContent();
    }
    
    setContent(content) {
        // Parser le contenu HTML et créer les blocs correspondants
        this.clearContent();
        
        if (content) {
            // Logique pour parser le contenu et recréer les blocs
            // Pour l'instant, on ajoute juste un bloc texte
            this.addTextBlock();
            const block = this.blocks[this.blocks.length - 1];
            const contentDiv = block.querySelector('.block-content');
            contentDiv.innerHTML = content;
        }
    }
    
    clearContent() {
        this.blocks.forEach(block => block.remove());
        this.blocks = [];
        this.showPlaceholder();
    }
    
    addDefaultStyles() {
        const styles = `
            <style>
                /* Variables CSS pour le thème belge */
                :root {
                    --belgium-red: #FF0000;
                    --belgium-yellow: #FFD700;
                    --belgium-black: #000000;
                    --primary: #1a1a1a;
                    --secondary: #2d2d2d;
                    --text: #ffffff;
                    --text-muted: #a0a0a0;
                    --border: #404040;
                    --success: #44ff44;
                    --error: #ff4444;
                    --warning: #ffaa00;
                    --bg-light: rgba(255, 255, 255, 0.05);
                    --bg-hover: rgba(255, 255, 255, 0.1);
                }
                
                /* Reset et base */
                .advanced-wysiwyg-editor * {
                    box-sizing: border-box;
                }
                
                .advanced-wysiwyg-editor {
                    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                    background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
                    color: var(--text);
                    min-height: 100vh;
                    padding: 0;
                    margin: 0;
                }
                
                /* Style pour le H2 du header */
                .advanced-wysiwyg-editor h2 {
                    color: var(--belgium-yellow) !important;
                    margin: 0;
                    font-size: 1.5rem;
                    font-weight: 700;
                }
                
                /* Header de l'éditeur */
                .advanced-toolbar {
                    background: var(--bg-light);
                    border-bottom: 2px solid var(--border);
                    padding: 1rem 2rem;
                    position: sticky;
                    top: 0;
                    z-index: 100;
                    backdrop-filter: blur(10px);
                }
                
                .toolbar-section {
                    display: flex;
                    gap: 0.5rem;
                    margin-bottom: 1rem;
                    flex-wrap: wrap;
                }
                
                .toolbar-section:last-child {
                    margin-bottom: 0;
                }
                
                .toolbar-btn {
                    display: inline-flex;
                    align-items: center;
                    gap: 0.5rem;
                    padding: 0.75rem 1rem;
                    background: var(--belgium-red);
                    color: white;
                    border: none;
                    border-radius: 8px;
                    font-weight: 600;
                    cursor: pointer;
                    transition: all 0.3s ease;
                    font-size: 0.9rem;
                }
                
                .toolbar-btn:hover {
                    background: #cc0000;
                    transform: translateY(-2px);
                    box-shadow: 0 4px 12px rgba(255, 0, 0, 0.3);
                }
                
                .toolbar-btn .icon {
                    font-size: 1.1rem;
                }
                
                /* Zone d'édition principale */
                .advanced-editor {
                    padding: 2rem;
                    max-width: 1400px;
                    margin: 0 auto;
                }
                
                .editor-content {
                    background: var(--bg-light);
                    border: 2px solid var(--border);
                    border-radius: 15px;
                    min-height: 600px;
                    padding: 2rem;
                    position: relative;
                }
                
                /* Message placeholder */
                .placeholder-message {
                    text-align: center;
                    padding: 4rem 2rem;
                    color: var(--text-muted);
                }
                
                .placeholder-icon {
                    font-size: 4rem;
                    margin-bottom: 1rem;
                    opacity: 0.5;
                }
                
                .placeholder-text {
                    font-size: 1.2rem;
                    font-weight: 500;
                }
                
                /* Blocs de contenu */
                .content-block {
                    background: var(--bg-light);
                    border: 2px solid var(--border);
                    border-radius: 10px;
                    margin-bottom: 1.5rem;
                    overflow: hidden;
                    transition: all 0.3s ease;
                }
                
                .content-block:hover {
                    border-color: var(--belgium-yellow);
                    box-shadow: 0 8px 25px rgba(255, 215, 0, 0.2);
                }
                
                .content-block.selected {
                    border-color: var(--belgium-red);
                    box-shadow: 0 0 0 3px rgba(255, 0, 0, 0.3);
                }
                
                /* En-tête des blocs */
                .block-header {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    padding: 1rem 1.5rem;
                    background: var(--bg-hover);
                    border-bottom: 1px solid var(--border);
                }
                
                .block-title {
                    display: flex;
                    align-items: center;
                    gap: 0.5rem;
                    font-weight: 600;
                    color: var(--belgium-yellow);
                }
                
                .block-title .icon {
                    font-size: 1.2rem;
                }
                
                .block-controls {
                    display: flex;
                    gap: 0.5rem;
                }
                
                .block-btn {
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    width: 32px;
                    height: 32px;
                    background: transparent;
                    border: 1px solid var(--border);
                    color: var(--text-muted);
                    border-radius: 6px;
                    cursor: pointer;
                    transition: all 0.3s ease;
                }
                
                .block-btn:hover {
                    background: var(--belgium-red);
                    color: white;
                    border-color: var(--belgium-red);
                }
                
                .block-btn.delete:hover {
                    background: var(--error);
                    border-color: var(--error);
                }
                
                /* Contenu des blocs */
                .block-content {
                    padding: 1.5rem;
                    background: var(--primary);
                }
                
                /* Bloc texte */
                .content-block-text .block-content {
                    font-size: 16px;
                    line-height: 1.7;
                    color: var(--text) !important;
                    font-family: 'Inter', sans-serif;
                }
                
                .content-block-text .block-content p {
                    margin: 0 0 1rem 0;
                }
                
                .content-block-text .block-content h1,
                .content-block-text .block-content h2,
                .content-block-text .block-content h3 {
                    margin: 0 0 1rem 0;
                    color: var(--belgium-yellow);
                    font-weight: 700;
                }
                
                .content-block-text .block-content h1 {
                    font-size: 2rem;
                    line-height: 1.2;
                }
                
                .content-block-text .block-content h2 {
                    font-size: 1.75rem;
                    line-height: 1.3;
                }
                
                .content-block-text .block-content h3 {
                    font-size: 1.5rem;
                    line-height: 1.4;
                }
                
                .content-block-text .block-content ul,
                .content-block-text .block-content ol {
                    margin: 0 0 1rem 0;
                    padding-left: 2rem;
                }
                
                .content-block-text .block-content li {
                    margin-bottom: 0.5rem;
                }
                
                .content-block-text .block-content a {
                    color: var(--belgium-red);
                    text-decoration: none;
                    border-bottom: 1px solid transparent;
                    transition: border-color 0.3s ease;
                }
                
                .content-block-text .block-content a:hover {
                    border-bottom-color: var(--belgium-red);
                }
                
                /* Bloc image */
                .content-block-image .image-placeholder {
                    height: 300px;
                    background: var(--bg-hover);
                    border: 2px dashed var(--border);
                    border-radius: 10px;
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    justify-content: center;
                    cursor: pointer;
                    transition: all 0.3s ease;
                    color: var(--text-muted);
                }
                
                .content-block-image .image-placeholder:hover {
                    border-color: var(--belgium-yellow);
                    background: rgba(255, 215, 0, 0.1);
                    color: var(--belgium-yellow);
                }
                
                .content-block-image .image-placeholder .icon {
                    font-size: 3rem;
                    margin-bottom: 1rem;
                }
                
                /* Bloc vidéo */
                .content-block-video .video-placeholder {
                    height: 300px;
                    background: var(--bg-hover);
                    border: 2px dashed var(--border);
                    border-radius: 10px;
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    justify-content: center;
                    cursor: pointer;
                    transition: all 0.3s ease;
                    color: var(--text-muted);
                }
                
                .content-block-video .video-placeholder:hover {
                    border-color: var(--belgium-yellow);
                    background: rgba(255, 215, 0, 0.1);
                    color: var(--belgium-yellow);
                }
                
                .content-block-video .video-placeholder .icon {
                    font-size: 3rem;
                    margin-bottom: 1rem;
                }
                
                /* Bloc citation */
                .content-block-quote .block-content {
                    font-style: italic;
                    font-size: 1.2rem;
                    line-height: 1.6;
                    color: var(--text);
                    border-left: 4px solid var(--belgium-yellow);
                    padding-left: 2rem;
                    margin: 0;
                }
                
                /* Bloc séparateur */
                .content-block-divider .block-content hr {
                    margin: 0;
                    border: none;
                    height: 3px;
                    background: linear-gradient(90deg, transparent, var(--belgium-yellow), transparent);
                    border-radius: 2px;
                }
                
                /* Barre d'outils de formatage */
                .text-formatting-toolbar {
                    display: flex;
                    flex-wrap: wrap;
                    gap: 0.5rem;
                    padding: 1rem;
                    background: var(--bg-hover);
                    border-bottom: 1px solid var(--border);
                }
                
                .formatting-section {
                    display: flex;
                    gap: 0.25rem;
                    padding: 0 0.5rem;
                    border-right: 1px solid var(--border);
                    align-items: center;
                }
                
                .formatting-section:last-child {
                    border-right: none;
                }
                
                .format-btn {
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    width: 36px;
                    height: 36px;
                    border: 1px solid var(--border);
                    background: var(--bg-light);
                    color: var(--text);
                    cursor: pointer;
                    border-radius: 6px;
                    transition: all 0.3s ease;
                    font-weight: 600;
                }
                
                .format-btn:hover {
                    background: var(--belgium-red);
                    color: white;
                    border-color: var(--belgium-red);
                    transform: translateY(-1px);
                }
                
                .format-btn.active {
                    background: var(--belgium-yellow);
                    color: var(--belgium-black);
                    border-color: var(--belgium-yellow);
                }
                
                .format-btn .icon {
                    font-size: 1rem;
                }
                
                .font-size-select {
                    padding: 0.5rem;
                    border: 1px solid var(--border);
                    border-radius: 6px;
                    background: var(--bg-light);
                    color: var(--text);
                    font-size: 0.9rem;
                    min-width: 80px;
                }
                
                .font-size-select:focus {
                    outline: none;
                    border-color: var(--belgium-yellow);
                }
                
                .color-picker {
                    width: 36px;
                    height: 36px;
                    border: 1px solid var(--border);
                    border-radius: 6px;
                    cursor: pointer;
                    background: var(--bg-light);
                }
                
                .color-picker:hover {
                    border-color: var(--belgium-yellow);
                }
                
                /* Contrôles d'image et vidéo */
                .image-controls,
                .video-controls {
                    display: flex;
                    gap: 0.5rem;
                    padding: 1rem;
                    background: var(--bg-hover);
                    border-top: 1px solid var(--border);
                }
                
                .image-btn,
                .video-btn {
                    padding: 0.5rem 1rem;
                    border: 1px solid var(--border);
                    background: var(--bg-light);
                    color: var(--text);
                    cursor: pointer;
                    border-radius: 6px;
                    font-size: 0.9rem;
                    transition: all 0.3s ease;
                }
                
                .image-btn:hover,
                .video-btn:hover {
                    background: var(--belgium-red);
                    color: white;
                    border-color: var(--belgium-red);
                }
                
                /* Légendes */
                .image-caption,
                .video-caption {
                    margin-top: 1rem;
                    padding: 1rem;
                    background: var(--bg-hover);
                    border-radius: 8px;
                    font-size: 0.9rem;
                    color: var(--text-muted);
                    text-align: center;
                    border: 1px solid var(--border);
                }
                
                /* Galerie */
                .gallery-grid {
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                    gap: 1rem;
                    margin: 1rem 0;
                }
                
                .gallery-item {
                    border: 1px solid var(--border);
                    border-radius: 8px;
                    overflow: hidden;
                    background: var(--bg-light);
                }
                
                .gallery-image-placeholder {
                    height: 150px;
                    background: var(--bg-hover);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    cursor: pointer;
                    border-bottom: 1px solid var(--border);
                    transition: all 0.3s ease;
                }
                
                .gallery-image-placeholder:hover {
                    background: rgba(255, 215, 0, 0.1);
                    color: var(--belgium-yellow);
                }
                
                .gallery-caption {
                    padding: 1rem;
                    background: var(--bg-light);
                    font-size: 0.9rem;
                    color: var(--text-muted);
                    text-align: center;
                }
                
                .gallery-controls {
                    display: flex;
                    gap: 0.5rem;
                    padding: 1rem;
                    background: var(--bg-hover);
                    border-top: 1px solid var(--border);
                }
                
                .gallery-btn {
                    padding: 0.5rem 1rem;
                    border: 1px solid var(--border);
                    background: var(--bg-light);
                    color: var(--text);
                    cursor: pointer;
                    border-radius: 6px;
                    font-size: 0.9rem;
                    transition: all 0.3s ease;
                }
                
                .gallery-btn:hover {
                    background: var(--belgium-red);
                    color: white;
                    border-color: var(--belgium-red);
                }
                
                /* Responsive */
                @media (max-width: 768px) {
                    .advanced-toolbar {
                        padding: 1rem;
                    }
                    
                    .toolbar-section {
                        justify-content: center;
                    }
                    
                    .advanced-editor {
                        padding: 1rem;
                    }
                    
                    .editor-content {
                        padding: 1rem;
                    }
                    
                    .text-formatting-toolbar {
                        flex-direction: column;
                        gap: 1rem;
                    }
                    
                    .formatting-section {
                        border-right: none;
                        border-bottom: 1px solid var(--border);
                        padding-bottom: 1rem;
                    }
                    
                    .formatting-section:last-child {
                        border-bottom: none;
                        padding-bottom: 0;
                    }
                }
            </style>
        `;
        document.head.insertAdjacentHTML('beforeend', styles);
    }
}

// Rendre la classe disponible globalement
window.AdvancedWysiwygEditor = AdvancedWysiwygEditor;
