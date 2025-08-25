/**
 * Éditeur WYSIWYG plein écran - Belgium Vidéo Gaming
 * Architecture modulaire avec barres latérales et système de colonnes
 */

class FullscreenEditor {
    constructor(options = {}) {
        this.options = {
            onSave: null,
            onClose: null,
            initialContent: '',
            ...options
        };
        
        this.currentModule = null;
        this.columns = 1;
        this.init();
    }
    
    init() {
        this.createModal();
        this.createLayout();
        this.bindEvents();
        this.addStyles();
        this.open(); // Ouvrir l'éditeur automatiquement
    }
    
    createModal() {
        this.modal = document.createElement('div');
        this.modal.className = 'fullscreen-editor-modal';
        this.modal.innerHTML = `
            <div class="fullscreen-editor-container">
                <div class="editor-header">
                    <div class="header-left">
                        <h2>Éditeur d'article</h2>
                    </div>
                    <div class="header-center">
                        <button type="button" class="header-btn" data-action="preview">
                            <span class="icon">👁️</span> Aperçu
                        </button>
                        <button type="button" class="header-btn" data-action="save">
                            <span class="icon">💾</span> Sauvegarder
                        </button>
                    </div>
                    <div class="header-right">
                        <button type="button" class="header-btn close-btn" data-action="close">
                            <span class="icon">✕</span>
                        </button>
                    </div>
                </div>
                
                <div class="editor-body">
                    <div class="sidebar-left">
                        <div class="sidebar-section">
                            <h3>Modules</h3>
                            <div class="module-buttons">
                                <button type="button" class="module-btn" data-module="text">
                                    <span class="icon">📝</span> Texte
                                </button>
                                <button type="button" class="module-btn" data-module="image">
                                    <span class="icon">🖼️</span> Image
                                </button>
                                <button type="button" class="module-btn" data-module="video">
                                    <span class="icon">🎥</span> Vidéo
                                </button>
                                <button type="button" class="module-btn" data-module="quote">
                                    <span class="icon">💬</span> Citation
                                </button>
                                <button type="button" class="module-btn" data-module="gallery">
                                    <span class="icon">🖼️</span> Galerie
                                </button>
                                <button type="button" class="module-btn" data-module="heading">
                                    <span class="icon">📋</span> Titre
                                </button>
                                <button type="button" class="module-btn" data-module="list">
                                    <span class="icon">📋</span> Liste
                                </button>
                                <button type="button" class="module-btn" data-module="divider">
                                    <span class="icon">➖</span> Séparateur
                                </button>
                            </div>
                        </div>
                        
                        <div class="sidebar-section">
                            <h3>Disposition</h3>
                            <div class="layout-buttons">
                                <button type="button" class="layout-btn" data-columns="1" title="1 colonne">
                                    <div class="layout-preview single"></div>
                                </button>
                                <button type="button" class="layout-btn" data-columns="2" title="2 colonnes">
                                    <div class="layout-preview double"></div>
                                </button>
                                <button type="button" class="layout-btn" data-columns="3" title="3 colonnes">
                                    <div class="layout-preview triple"></div>
                                </button>
                            </div>
                            <div class="section-actions">
                                <button type="button" class="add-section-btn" data-action="add-section">
                                    <span class="icon">➕</span> Ajouter une section
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="editor-main">
                        <div class="editor-content">
                            <div class="content-sections">
                                <!-- Les sections seront ajoutées ici dynamiquement -->
                            </div>
                        </div>
                    </div>
                    
                    <div class="sidebar-right">
                        <div class="sidebar-section">
                            <h3>Options</h3>
                            <div class="options-content">
                                <div class="no-selection">
                                    <p>Sélectionnez un module pour voir les options</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        document.body.appendChild(this.modal);
    }
    
    createLayout() {
        this.leftSidebar = this.modal.querySelector('.sidebar-left');
        this.rightSidebar = this.modal.querySelector('.sidebar-right');
        this.editorMain = this.modal.querySelector('.editor-main');
        this.contentSections = this.modal.querySelector('.content-sections');
        this.optionsContent = this.modal.querySelector('.options-content');
    }
    
    bindEvents() {
        // Gestion des boutons d'en-tête
        this.modal.addEventListener('click', (e) => {
            const btn = e.target.closest('[data-action]');
            if (btn) {
                const action = btn.dataset.action;
                this.handleHeaderAction(action);
            }
        });
        
        // Gestion des modules
        this.modal.addEventListener('click', (e) => {
            const moduleBtn = e.target.closest('.module-btn');
            if (moduleBtn) {
                const module = moduleBtn.dataset.module;
                this.addModule(module);
            }
        });
        
        // Gestion de la disposition et des sections
        this.modal.addEventListener('click', (e) => {
            const layoutBtn = e.target.closest('.layout-btn');
            if (layoutBtn) {
                const columns = parseInt(layoutBtn.dataset.columns);
                this.addSection(columns);
            }
            
            const addSectionBtn = e.target.closest('.add-section-btn');
            if (addSectionBtn) {
                this.addSection(1); // Section par défaut avec 1 colonne
            }
            
            const sectionActionBtn = e.target.closest('.section-action');
            if (sectionActionBtn) {
                const action = sectionActionBtn.dataset.action;
                const section = sectionActionBtn.closest('.content-section');
                this.handleSectionAction(section, action);
            }
        });
        
        // Gestion de la sélection de modules et sections
        this.modal.addEventListener('click', (e) => {
            const module = e.target.closest('.content-module');
            if (module) {
                this.selectModule(module);
            }
            
            // Sélection de section (si on clique sur la section mais pas sur un module ou une action)
            const section = e.target.closest('.content-section');
            if (section && !e.target.closest('.content-module') && !e.target.closest('.section-action')) {
                this.selectSection(section);
            }
        });
        
        // Fermeture avec Escape
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.modal.style.display === 'flex') {
                this.close();
            }
        });
    }
    
    handleHeaderAction(action) {
        switch (action) {
            case 'preview':
                this.showPreview();
                break;
            case 'save':
                this.save();
                break;
            case 'close':
                this.close();
                break;
        }
    }
    
    addModule(type) {
        const activeColumn = this.getActiveColumn();
        if (!activeColumn) return;
        
        const module = this.createModule(type);
        activeColumn.appendChild(module);
        this.selectModule(module);
        
        // Supprimer le placeholder si c'est le premier module
        const placeholder = activeColumn.querySelector('.column-placeholder');
        if (placeholder) {
            placeholder.remove();
        }
    }
    
    createModule(type) {
        const module = document.createElement('div');
        module.className = 'content-module';
        module.dataset.type = type;
        module.dataset.moduleId = this.generateModuleId();
        
        switch (type) {
            case 'text':
                module.innerHTML = `
                    <div class="module-header">
                        <span class="module-type">📝 Texte</span>
                        <div class="module-actions">
                            <button type="button" class="module-action" data-action="move-left">⬅️</button>
                            <button type="button" class="module-action" data-action="move-right">➡️</button>
                            <button type="button" class="module-action" data-action="delete">🗑️</button>
                        </div>
                    </div>
                    <div class="module-content" contenteditable="true" style="color: var(--text) !important;">
                        <p style="color: var(--text) !important; margin: 0;">Commencez à écrire votre texte ici...</p>
                    </div>
                `;
                break;
                
            default:
                module.innerHTML = `
                    <div class="module-header">
                        <span class="module-type">${this.getModuleIcon(type)} ${this.getModuleLabel(type)}</span>
                        <div class="module-actions">
                            <button type="button" class="module-action" data-action="move-left">⬅️</button>
                            <button type="button" class="module-action" data-action="move-right">➡️</button>
                            <button type="button" class="module-action" data-action="delete">🗑️</button>
                        </div>
                    </div>
                    <div class="module-content">
                        <p>Module ${this.getModuleLabel(type)}</p>
                    </div>
                `;
        }
        
        this.bindModuleEvents(module);
        
        // Gestionnaire spécial pour le module texte
        if (type === 'text') {
            const content = module.querySelector('.module-content');
            content.addEventListener('focus', (e) => {
                if (content.textContent === 'Commencez à écrire votre texte ici...') {
                    content.innerHTML = '<p style="color: var(--text) !important; margin: 0;"></p>';
                }
            });
            
            content.addEventListener('blur', (e) => {
                if (content.textContent.trim() === '') {
                    content.innerHTML = '<p style="color: var(--text) !important; margin: 0;">Commencez à écrire votre texte ici...</p>';
                }
            });
            
            // Gestionnaire pour nettoyer le contenu collé
            content.addEventListener('paste', (e) => {
                e.preventDefault();
                const text = e.clipboardData.getData('text/plain');
                document.execCommand('insertText', false, text);
            });
        }
        
        // Gestionnaire spécial pour le module image
        if (type === 'image') {
            console.log('Module image créé, liaison des événements d\'upload');
            this.bindImageUploadEvents(module);
        }
        
        return module;
    }
    
    bindModuleEvents(module) {
        // Actions du module
        module.addEventListener('click', (e) => {
            const actionBtn = e.target.closest('.module-action');
            if (actionBtn) {
                const action = actionBtn.dataset.action;
                this.handleModuleAction(module, action);
            }
        });
        
        // Sélection du module
        module.addEventListener('click', (e) => {
            if (!e.target.closest('.module-action')) {
                this.selectModule(module);
            }
        });
    }
    
    handleModuleAction(module, action) {
        if (!module || !document.contains(module)) {
            console.warn('Module non trouvé dans le DOM');
            return;
        }
        
        switch (action) {
            case 'move-left':
                this.moveModule(module, 'left');
                break;
            case 'move-right':
                this.moveModule(module, 'right');
                break;
            case 'delete':
                this.deleteModule(module);
                break;
        }
    }
    
    moveModule(module, direction) {
        const currentColumn = module.closest('.content-column');
        if (!currentColumn) {
            console.warn('Colonne non trouvée pour le module');
            return;
        }
        
        const currentIndex = Array.from(currentColumn.children).indexOf(module);
        
        if (direction === 'left' && currentIndex > 0) {
            currentColumn.insertBefore(module, currentColumn.children[currentIndex - 1]);
        } else if (direction === 'right' && currentIndex < currentColumn.children.length - 1) {
            currentColumn.insertBefore(module, currentColumn.children[currentIndex + 2]);
        }
    }
    
    deleteModule(module) {
        if (confirm('Supprimer ce module ?')) {
            const column = module.closest('.content-column');
            module.remove();
            
            if (column && column.children.length === 0) {
                column.innerHTML = `
                    <div class="column-placeholder">
                        <div class="placeholder-icon">📝</div>
                        <div class="placeholder-text">Cliquez sur un module pour commencer</div>
                    </div>
                `;
            }
            
            this.currentModule = null;
            this.hideOptions();
        }
    }
    
    selectModule(module) {
        console.log('selectModule appelé pour:', module);
        
        this.modal.querySelectorAll('.content-module').forEach(m => {
            m.classList.remove('selected');
        });
        
        module.classList.add('selected');
        this.currentModule = module;
        this.showModuleOptions(module);
        
        console.log('Module sélectionné:', this.currentModule);
    }
    
    hideOptions() {
        this.optionsContent.innerHTML = `
            <div class="no-selection">
                <div class="placeholder-icon">📝</div>
                <div class="placeholder-text">Sélectionnez un module pour voir les options</div>
            </div>
        `;
    }
    
    showModuleOptions(module) {
        const type = module.dataset.type;
        let optionsHTML = '';
        
        switch (type) {
            case 'text':
                optionsHTML = `
                    <div class="text-options">
                        <h4>Formatage du texte</h4>
                        <div class="option-group">
                            <label>Style :</label>
                            <div class="format-buttons">
                                <button type="button" class="format-btn" data-command="bold">B</button>
                                <button type="button" class="format-btn" data-command="italic">I</button>
                                <button type="button" class="format-btn" data-command="underline">U</button>
                            </div>
                        </div>
                        <div class="option-group">
                            <label>Taille :</label>
                            <select class="font-size-select">
                                <option value="12px">12px</option>
                                <option value="14px">14px</option>
                                <option value="16px" selected>16px</option>
                                <option value="18px">18px</option>
                                <option value="20px">20px</option>
                                <option value="24px">24px</option>
                            </select>
                        </div>
                        <div class="option-group">
                            <label>Couleur :</label>
                            <input type="color" class="color-picker" value="#000000">
                        </div>
                        <div class="option-group">
                            <label>Alignement :</label>
                            <div class="align-buttons">
                                <button type="button" class="align-btn" data-align="left">⬅️</button>
                                <button type="button" class="align-btn" data-align="center">↔️</button>
                                <button type="button" class="align-btn" data-align="right">➡️</button>
                            </div>
                        </div>
                    </div>
                `;
                break;
                
            default:
                optionsHTML = `
                    <div class="default-options">
                        <h4>Options du module</h4>
                        <p>Module ${this.getModuleLabel(type)}</p>
                    </div>
                `;
        }
        
        this.optionsContent.innerHTML = optionsHTML;
        this.bindOptionsEvents();
    }
    
    bindOptionsEvents() {
        // Formatage du texte
        this.optionsContent.addEventListener('click', (e) => {
            const formatBtn = e.target.closest('.format-btn');
            if (formatBtn && this.currentModule) {
                const command = formatBtn.dataset.command;
                const content = this.currentModule.querySelector('.module-content');
                content.focus();
                formatBtn.classList.toggle('active');
                document.execCommand(command, false, null);
            }
            
            const alignBtn = e.target.closest('.align-btn');
            if (alignBtn && this.currentModule) {
                const align = alignBtn.dataset.align;
                const content = this.currentModule.querySelector('.module-content');
                
                this.optionsContent.querySelectorAll('.align-btn').forEach(btn => {
                    btn.classList.remove('active');
                });
                
                alignBtn.classList.add('active');
                content.style.textAlign = align;
            }
        });
        
        // Taille de police
        const fontSizeSelect = this.optionsContent.querySelector('.font-size-select');
        if (fontSizeSelect) {
            fontSizeSelect.addEventListener('change', (e) => {
                if (this.currentModule) {
                    const content = this.currentModule.querySelector('.module-content');
                    content.focus();
                    const size = e.target.value;
                    const selection = window.getSelection();
                    
                    if (selection.rangeCount > 0) {
                        const range = selection.getRangeAt(0);
                        if (!range.collapsed) {
                            this.applyFontSizeToSelection(size);
                        } else {
                            this.applyFontSizeToContent(content, size);
                        }
                    } else {
                        this.applyFontSizeToContent(content, size);
                    }
                }
            });
        }
        
        // Couleur
        const colorPicker = this.optionsContent.querySelector('.color-picker');
        if (colorPicker) {
            colorPicker.addEventListener('change', (e) => {
                if (this.currentModule) {
                    const content = this.currentModule.querySelector('.module-content');
                    content.focus();
                    const color = e.target.value;
                    const selection = window.getSelection();
                    
                    if (selection.rangeCount > 0) {
                        const range = selection.getRangeAt(0);
                        if (!range.collapsed) {
                            this.applyColorToSelection(color);
                        } else {
                            this.applyColorToContent(content, color);
                        }
                    } else {
                        this.applyColorToContent(content, color);
                    }
                }
            });
        }
    }
    
    // Fonctions pour les sections
    createSection(columns = 1) {
        const section = document.createElement('div');
        section.className = 'content-section';
        section.dataset.sectionId = this.generateSectionId();
        section.dataset.columns = columns;
        
        const columnsContainer = document.createElement('div');
        columnsContainer.className = 'content-columns';
        columnsContainer.dataset.columns = columns;
        
        for (let i = 1; i <= columns; i++) {
            const column = document.createElement('div');
            column.className = 'content-column';
            column.dataset.column = i;
            column.dataset.section = section.dataset.sectionId;
            
            column.innerHTML = `
                <div class="column-placeholder">
                    <div class="placeholder-icon">📝</div>
                    <div class="placeholder-text">Colonne ${i}</div>
                </div>
            `;
            
            columnsContainer.appendChild(column);
        }
        
        const sectionHeader = document.createElement('div');
        sectionHeader.className = 'section-header';
        sectionHeader.innerHTML = `
            <div class="section-info">
                <span class="section-label">Section ${this.getSectionCount() + 1}</span>
                <span class="section-layout">${columns} colonne${columns > 1 ? 's' : ''}</span>
            </div>
            <div class="section-actions">
                <button type="button" class="section-action" data-action="add-column" title="Ajouter une colonne">
                    <span class="icon">➕</span>
                </button>
                <button type="button" class="section-action" data-action="remove-column" title="Supprimer une colonne">
                    <span class="icon">➖</span>
                </button>
                <button type="button" class="section-action" data-action="move-up" title="Déplacer vers le haut">
                    <span class="icon">⬆️</span>
                </button>
                <button type="button" class="section-action" data-action="move-down" title="Déplacer vers le bas">
                    <span class="icon">⬇️</span>
                </button>
                <button type="button" class="section-action" data-action="delete-section" title="Supprimer la section">
                    <span class="icon">🗑️</span>
                </button>
            </div>
        `;
        
        section.appendChild(sectionHeader);
        section.appendChild(columnsContainer);
        
        return section;
    }
    
    addSection(columns = 1) {
        const section = this.createSection(columns);
        this.editorMain.appendChild(section);
        this.updateLayoutButtons();
        return section;
    }
    
    handleSectionAction(section, action) {
        switch (action) {
            case 'add-column':
                this.addColumnToSection(section);
                break;
            case 'remove-column':
                this.removeColumnFromSection(section);
                break;
            case 'move-up':
                this.moveSection(section, 'up');
                break;
            case 'move-down':
                this.moveSection(section, 'down');
                break;
            case 'delete-section':
                this.deleteSection(section);
                break;
        }
    }
    
    addColumnToSection(section) {
        const columnsContainer = section.querySelector('.content-columns');
        const currentColumns = parseInt(columnsContainer.dataset.columns);
        const newColumnCount = currentColumns + 1;
        
        if (newColumnCount <= 3) {
            columnsContainer.dataset.columns = newColumnCount;
            section.dataset.columns = newColumnCount;
            
            const newColumn = document.createElement('div');
            newColumn.className = 'content-column';
            newColumn.dataset.column = newColumnCount;
            newColumn.dataset.section = section.dataset.sectionId;
            
            newColumn.innerHTML = `
                <div class="column-placeholder">
                    <div class="placeholder-icon">📝</div>
                    <div class="placeholder-text">Colonne ${newColumnCount}</div>
                </div>
            `;
            
            columnsContainer.appendChild(newColumn);
            
            const sectionLayout = section.querySelector('.section-layout');
            sectionLayout.textContent = `${newColumnCount} colonne${newColumnCount > 1 ? 's' : ''}`;
        }
    }
    
    removeColumnFromSection(section) {
        const columnsContainer = section.querySelector('.content-columns');
        const currentColumns = parseInt(columnsContainer.dataset.columns);
        
        if (currentColumns > 1) {
            const columns = columnsContainer.querySelectorAll('.content-column');
            const lastColumn = columns[columns.length - 1];
            
            if (columns.length > 1) {
                const previousColumn = columns[columns.length - 2];
                const modules = lastColumn.querySelectorAll('.content-module');
                modules.forEach(module => {
                    previousColumn.appendChild(module);
                });
            }
            
            lastColumn.remove();
            
            const newColumnCount = currentColumns - 1;
            columnsContainer.dataset.columns = newColumnCount;
            section.dataset.columns = newColumnCount;
            
            const sectionLayout = section.querySelector('.section-layout');
            sectionLayout.textContent = `${newColumnCount} colonne${newColumnCount > 1 ? 's' : ''}`;
        }
    }
    
    moveSection(section, direction) {
        const sections = Array.from(this.editorMain.querySelectorAll('.content-section'));
        const currentIndex = sections.indexOf(section);
        
        if (direction === 'up' && currentIndex > 0) {
            this.editorMain.insertBefore(section, sections[currentIndex - 1]);
        } else if (direction === 'down' && currentIndex < sections.length - 1) {
            this.editorMain.insertBefore(section, sections[currentIndex + 1].nextSibling);
        }
    }
    
    deleteSection(section) {
        if (confirm('Supprimer cette section ? Tous les modules qu\'elle contient seront perdus.')) {
            section.remove();
            this.updateLayoutButtons();
        }
    }
    
    getActiveColumn() {
        const sections = this.editorMain.querySelectorAll('.content-section');
        for (let section of sections) {
            const columns = section.querySelectorAll('.content-column');
            for (let column of columns) {
                if (column.querySelector('.column-placeholder')) {
                    return column;
                }
            }
            if (columns.length > 0) {
                return columns[0];
            }
        }
        return null;
    }
    
    generateModuleId() {
        return 'module_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
    }
    
    generateSectionId() {
        return 'section_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
    }
    
    getSectionCount() {
        return this.editorMain.querySelectorAll('.content-section').length;
    }
    
    updateLayoutButtons() {
        const sectionCount = this.getSectionCount();
        const addSectionBtn = this.modal.querySelector('.add-section-btn');
        
        if (addSectionBtn) {
            addSectionBtn.style.display = sectionCount === 0 ? 'none' : 'block';
        }
    }
    
    getModuleIcon(type) {
        const icons = {
            'text': '📝',
            'image': '🖼️',
            'video': '🎥',
            'quote': '💬',
            'gallery': '🖼️',
            'heading': '📋',
            'list': '📋',
            'divider': '➖'
        };
        return icons[type] || '📄';
    }
    
    getModuleLabel(type) {
        const labels = {
            'text': 'Texte',
            'image': 'Image',
            'video': 'Vidéo',
            'quote': 'Citation',
            'gallery': 'Galerie',
            'heading': 'Titre',
            'list': 'Liste',
            'divider': 'Séparateur'
        };
        return labels[type] || type;
    }
    
    // Fonctions de formatage
    applyFontSizeToSelection(size) {
        const selection = window.getSelection();
        if (selection.rangeCount > 0) {
            const range = selection.getRangeAt(0);
            
            try {
                const span = document.createElement('span');
                span.style.setProperty('font-size', size, 'important');
                span.style.setProperty('color', 'inherit', 'important');
                range.surroundContents(span);
                span.offsetHeight;
            } catch (error) {
                const fragment = range.extractContents();
                const span = document.createElement('span');
                span.style.setProperty('font-size', size, 'important');
                span.style.setProperty('color', 'inherit', 'important');
                span.appendChild(fragment);
                range.insertNode(span);
                span.offsetHeight;
            }
        }
    }
    
    applyFontSizeToContent(content, size) {
        const elementsWithFontSize = content.querySelectorAll('[style*="font-size"]');
        elementsWithFontSize.forEach(el => {
            el.style.fontSize = '';
            el.style.removeProperty('font-size');
        });
        
        content.style.setProperty('font-size', size, 'important');
        
        const textElements = content.querySelectorAll('p, span, div, h1, h2, h3, h4, h5, h6');
        textElements.forEach(el => {
            if (el === content) return;
            el.style.setProperty('font-size', size, 'important');
        });
        
        content.offsetHeight;
    }
    
    applyColorToSelection(color) {
        const selection = window.getSelection();
        if (selection.rangeCount > 0) {
            const range = selection.getRangeAt(0);
            
            const moduleContent = this.currentModule?.querySelector('.module-content');
            if (!moduleContent || !moduleContent.contains(range.commonAncestorContainer)) {
                return;
            }
            
            if (range.collapsed) {
                return;
            }
            
            const selectedText = range.toString().trim();
            if (!selectedText) {
                return;
            }
            
            try {
                const span = document.createElement('span');
                span.style.setProperty('color', color, 'important');
                span.style.setProperty('background', 'transparent', 'important');
                range.surroundContents(span);
                span.offsetHeight;
            } catch (error) {
                const fragment = range.extractContents();
                const span = document.createElement('span');
                span.style.setProperty('color', color, 'important');
                span.style.setProperty('background', 'transparent', 'important');
                span.appendChild(fragment);
                range.insertNode(span);
                span.offsetHeight;
            }
        }
    }
    
    applyColorToContent(content, color) {
        const elementsWithColor = content.querySelectorAll('[style*="color"]');
        elementsWithColor.forEach(el => {
            el.style.color = '';
            el.style.removeProperty('color');
        });
        
        content.style.setProperty('color', color, 'important');
        
        const textElements = content.querySelectorAll('p, span, div, h1, h2, h3, h4, h5, h6');
        textElements.forEach(el => {
            if (el === content) return;
            el.style.setProperty('color', color, 'important');
        });
        
        content.offsetHeight;
    }
    
    showPreview() {
        const content = this.getContent();
        const previewWindow = window.open('', '_blank');
        previewWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>Aperçu de l'article</title>
                <style>
                    body { 
                        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; 
                        max-width: 800px; 
                        margin: 0 auto; 
                        padding: 20px; 
                        background: #ffffff;
                        color: #333333;
                        line-height: 1.6;
                    }
                    
                    h1 {
                        color: #333333;
                        border-bottom: 2px solid #FF0000;
                        padding-bottom: 10px;
                        margin-bottom: 30px;
                    }
                    
                    .content-module { 
                        margin: 20px 0; 
                        padding: 15px;
                        border-radius: 8px;
                        background: #ffffff;
                    }
                    
                    .content-module img { 
                        max-width: 100%; 
                        height: auto; 
                        border-radius: 8px;
                        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
                    }
                    
                    .content-module p {
                        margin: 0 0 15px 0;
                        color: #333333;
                    }
                    
                    .content-module p:last-child {
                        margin-bottom: 0;
                    }
                    
                    .content-module span[style*="color"] {
                        color: inherit !important;
                    }
                    
                    .content-module span[style*="font-size"] {
                        font-size: inherit !important;
                    }
                    
                    .content-module span[style*="font-size: 12px"] { font-size: 12px !important; }
                    .content-module span[style*="font-size: 14px"] { font-size: 14px !important; }
                    .content-module span[style*="font-size: 16px"] { font-size: 16px !important; }
                    .content-module span[style*="font-size: 18px"] { font-size: 18px !important; }
                    .content-module span[style*="font-size: 20px"] { font-size: 20px !important; }
                    .content-module span[style*="font-size: 24px"] { font-size: 24px !important; }
                    
                    .content-module[style*="background"] {
                        background: #ffffff !important;
                    }
                    
                                    .content-module[style*="color"] {
                    color: #333333 !important;
                }
                
                /* Styles pour les colonnes dans l'aperçu */
                .content-columns {
                    display: grid;
                    gap: 2rem;
                    margin: 20px 0;
                }
                
                .content-columns[data-columns="1"] {
                    grid-template-columns: 1fr;
                }
                
                .content-columns[data-columns="2"] {
                    grid-template-columns: 1fr 1fr;
                }
                
                .content-columns[data-columns="3"] {
                    grid-template-columns: 1fr 1fr 1fr;
                }
                
                .content-column {
                    min-height: 100px;
                }
                
                /* Responsive pour les colonnes */
                @media (max-width: 768px) {
                    .content-columns[data-columns="2"],
                    .content-columns[data-columns="3"] {
                        grid-template-columns: 1fr;
                        gap: 1rem;
                    }
                }
                </style>
            </head>
            <body>
                <h1>Aperçu de l'article</h1>
                ${content}
            </body>
            </html>
        `);
    }
    
    getContent() {
        let content = '';
        const sections = this.editorMain.querySelectorAll('.content-section');
        
        sections.forEach(section => {
            const columnCount = section.dataset.columns;
            const columns = section.querySelectorAll('.content-column');
            
            if (columns.length > 0) {
                let sectionContent = '<div class="content-columns" data-columns="' + columnCount + '">';
                
                columns.forEach(column => {
                    sectionContent += '<div class="content-column">';
                    const modules = column.querySelectorAll('.content-module');
                    modules.forEach(module => {
                        const moduleContent = module.querySelector('.module-content').innerHTML;
                        const moduleType = module.dataset.type;
                        sectionContent += `<div class="content-module content-module-${moduleType}">${moduleContent}</div>`;
                    });
                    sectionContent += '</div>';
                });
                
                sectionContent += '</div>';
                content += `<div class="content-section" data-columns="${columnCount}">${sectionContent}</div>`;
            }
        });
        
        return content;
    }
    
    // Fonctions pour les sections
    createSection(columns = 1) {
        const section = document.createElement('div');
        section.className = 'content-section';
        section.dataset.sectionId = this.generateSectionId();
        section.dataset.columns = columns;
        
        // Créer les colonnes pour cette section
        const columnsContainer = document.createElement('div');
        columnsContainer.className = 'content-columns';
        columnsContainer.dataset.columns = columns;
        
        for (let i = 1; i <= columns; i++) {
            const column = document.createElement('div');
            column.className = 'content-column';
            column.dataset.column = i;
            column.dataset.section = section.dataset.sectionId;
            
            column.innerHTML = `
                <div class="column-placeholder">
                    <div class="placeholder-icon">📝</div>
                    <div class="placeholder-text">Colonne ${i}</div>
                </div>
            `;
            
            columnsContainer.appendChild(column);
        }
        
        // Ajouter les actions de section
        const sectionHeader = document.createElement('div');
        sectionHeader.className = 'section-header';
        sectionHeader.innerHTML = `
            <div class="section-info">
                <span class="section-label">Section ${this.getSectionCount() + 1}</span>
                <span class="section-layout">${columns} colonne${columns > 1 ? 's' : ''}</span>
            </div>
            <div class="section-actions">
                <button type="button" class="section-action" data-action="add-column" title="Ajouter une colonne">
                    <span class="icon">➕</span>
                </button>
                <button type="button" class="section-action" data-action="remove-column" title="Supprimer une colonne">
                    <span class="icon">➖</span>
                </button>
                <button type="button" class="section-action" data-action="move-up" title="Déplacer vers le haut">
                    <span class="icon">⬆️</span>
                </button>
                <button type="button" class="section-action" data-action="move-down" title="Déplacer vers le bas">
                    <span class="icon">⬇️</span>
                </button>
                <button type="button" class="section-action" data-action="delete-section" title="Supprimer la section">
                    <span class="icon">🗑️</span>
                </button>
            </div>
        `;
        
        section.appendChild(sectionHeader);
        section.appendChild(columnsContainer);
        
        return section;
    }
    
    addSection(columns = 1) {
        console.log('Ajout d\'une section avec', columns, 'colonne(s)');
        const section = this.createSection(columns);
        
        // S'assurer que la section est ajoutée dans le bon conteneur
        const contentSections = this.modal.querySelector('.content-sections');
        if (contentSections) {
            contentSections.appendChild(section);
            console.log('Section ajoutée dans content-sections:', section);
        } else {
            console.error('Conteneur content-sections non trouvé');
        }
        
        return section;
    }
    
    getSectionCount() {
        return this.editorMain.querySelectorAll('.content-section').length;
    }
    
    generateSectionId() {
        return 'section_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
    }
    
    handleSectionAction(section, action) {
        switch (action) {
            case 'add-column':
                this.addColumnToSection(section);
                break;
            case 'remove-column':
                this.removeColumnFromSection(section);
                break;
            case 'move-up':
                this.moveSection(section, 'up');
                break;
            case 'move-down':
                this.moveSection(section, 'down');
                break;
            case 'delete-section':
                this.deleteSection(section);
                break;
        }
    }
    
    addColumnToSection(section) {
        const columnsContainer = section.querySelector('.content-columns');
        const currentColumns = parseInt(columnsContainer.dataset.columns);
        const newColumnCount = currentColumns + 1;
        
        if (newColumnCount <= 3) {
            columnsContainer.dataset.columns = newColumnCount;
            section.dataset.columns = newColumnCount;
            
            const newColumn = document.createElement('div');
            newColumn.className = 'content-column';
            newColumn.dataset.column = newColumnCount;
            newColumn.dataset.section = section.dataset.sectionId;
            
            newColumn.innerHTML = `
                <div class="column-placeholder">
                    <div class="placeholder-icon">📝</div>
                    <div class="placeholder-text">Colonne ${newColumnCount}</div>
                </div>
            `;
            
            columnsContainer.appendChild(newColumn);
            
            // Mettre à jour l'affichage de la disposition
            const sectionLayout = section.querySelector('.section-layout');
            sectionLayout.textContent = `${newColumnCount} colonne${newColumnCount > 1 ? 's' : ''}`;
        }
    }
    
    removeColumnFromSection(section) {
        const columnsContainer = section.querySelector('.content-columns');
        const currentColumns = parseInt(columnsContainer.dataset.columns);
        
        if (currentColumns > 1) {
            const columns = columnsContainer.querySelectorAll('.content-column');
            const lastColumn = columns[columns.length - 1];
            
            // Déplacer les modules de la dernière colonne vers la colonne précédente
            if (columns.length > 1) {
                const previousColumn = columns[columns.length - 2];
                const modules = lastColumn.querySelectorAll('.content-module');
                modules.forEach(module => {
                    previousColumn.appendChild(module);
                });
            }
            
            lastColumn.remove();
            
            const newColumnCount = currentColumns - 1;
            columnsContainer.dataset.columns = newColumnCount;
            section.dataset.columns = newColumnCount;
            
            // Mettre à jour l'affichage de la disposition
            const sectionLayout = section.querySelector('.section-layout');
            sectionLayout.textContent = `${newColumnCount} colonne${newColumnCount > 1 ? 's' : ''}`;
        }
    }
    
    moveSection(section, direction) {
        const sections = Array.from(this.editorMain.querySelectorAll('.content-section'));
        const currentIndex = sections.indexOf(section);
        
        if (direction === 'up' && currentIndex > 0) {
            this.editorMain.insertBefore(section, sections[currentIndex - 1]);
        } else if (direction === 'down' && currentIndex < sections.length - 1) {
            this.editorMain.insertBefore(section, sections[currentIndex + 1].nextSibling);
        }
    }
    
    deleteSection(section) {
        if (confirm('Supprimer cette section ? Tous les modules qu\'elle contient seront perdus.')) {
            section.remove();
        }
    }
    
    // Fonctions pour les modules
    addModule(type) {
        // Si une section est sélectionnée, utiliser celle-ci
        let targetColumn = this.getTargetColumn();
        
        if (!targetColumn) {
            console.error('Aucune colonne disponible pour ajouter le module');
            return;
        }
        
        const module = this.createModule(type);
        targetColumn.appendChild(module);
        this.selectModule(module);
        
        // Supprimer le placeholder si c'est le premier module
        const placeholder = targetColumn.querySelector('.column-placeholder');
        if (placeholder) {
            placeholder.remove();
        }
        
        console.log(`Module ${type} ajouté dans la colonne`, targetColumn);
    }
    
    getTargetColumn() {
        // 1. Vérifier s'il y a une section sélectionnée
        const selectedSection = this.modal.querySelector('.content-section.selected');
        if (selectedSection) {
            const columns = selectedSection.querySelectorAll('.content-column');
            // Chercher une colonne vide dans la section sélectionnée
            for (let column of columns) {
                if (column.querySelector('.column-placeholder')) {
                    return column;
                }
            }
            // Si aucune colonne vide, utiliser la première colonne de la section sélectionnée
            if (columns.length > 0) {
                return columns[0];
            }
        }
        
        // 2. Sinon, utiliser la logique précédente (première colonne vide)
        return this.getActiveColumn() || this.getFirstColumn();
    }
    
    getActiveColumn() {
        // Retourner la première colonne non vide dans la première section
        const sections = this.editorMain.querySelectorAll('.content-section');
        for (let section of sections) {
            const columns = section.querySelectorAll('.content-column');
            for (let column of columns) {
                if (column.querySelector('.column-placeholder')) {
                    return column;
                }
            }
            // Si aucune colonne vide, retourner la première colonne de la première section
            if (columns.length > 0) {
                return columns[0];
            }
        }
        return null;
    }
    
    getFirstColumn() {
        // Retourner la première colonne disponible, même si elle a déjà du contenu
        const sections = this.editorMain.querySelectorAll('.content-section');
        if (sections.length > 0) {
            const firstSection = sections[0];
            const columns = firstSection.querySelectorAll('.content-column');
            if (columns.length > 0) {
                return columns[0];
            }
        }
        return null;
    }
    
    createModule(type) {
        const module = document.createElement('div');
        module.className = 'content-module';
        module.dataset.type = type;
        module.dataset.moduleId = this.generateModuleId();
        
        switch (type) {
            case 'text':
                module.innerHTML = `
                    <div class="module-header">
                        <span class="module-type">📝 Texte</span>
                        <div class="module-actions">
                            <button type="button" class="module-action" data-action="move-left">⬅️</button>
                            <button type="button" class="module-action" data-action="move-right">➡️</button>
                            <button type="button" class="module-action" data-action="delete">🗑️</button>
                        </div>
                    </div>
                    <div class="module-content" contenteditable="true" style="color: var(--text) !important;">
                        <p style="color: var(--text) !important; margin: 0;">Commencez à écrire votre texte ici...</p>
                    </div>
                `;
                break;
                
                         case 'image':
                 module.innerHTML = `
                     <div class="module-header">
                         <span class="module-type">🖼️ Image</span>
                         <div class="module-actions">
                             <button type="button" class="module-action" data-action="move-left">⬅️</button>
                             <button type="button" class="module-action" data-action="move-right">➡️</button>
                             <button type="button" class="module-action" data-action="delete">🗑️</button>
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
                 break;
                
            default:
                module.innerHTML = `
                    <div class="module-header">
                        <span class="module-type">${this.getModuleIcon(type)} ${this.getModuleLabel(type)}</span>
                        <div class="module-actions">
                            <button type="button" class="module-action" data-action="move-left">⬅️</button>
                            <button type="button" class="module-action" data-action="move-right">➡️</button>
                            <button type="button" class="module-action" data-action="delete">🗑️</button>
                        </div>
                    </div>
                    <div class="module-content">
                        <p>Module ${this.getModuleLabel(type)}</p>
                    </div>
                `;
        }
        
        this.bindModuleEvents(module);
        
        // Gestionnaire spécial pour le module image
        if (type === 'image') {
            console.log('Module image créé, liaison des événements d\'upload');
            this.bindImageUploadEvents(module);
        }
        
        return module;
    }
    
    bindModuleEvents(module) {
        // Actions du module
        module.addEventListener('click', (e) => {
            const actionBtn = e.target.closest('.module-action');
            if (actionBtn) {
                const action = actionBtn.dataset.action;
                this.handleModuleAction(module, action);
            }
        });
        
        // Sélection du module
        module.addEventListener('click', (e) => {
            if (!e.target.closest('.module-action')) {
                this.selectModule(module);
            }
        });
    }
    
    handleModuleAction(module, action) {
        switch (action) {
            case 'move-left':
                this.moveModule(module, 'left');
                break;
            case 'move-right':
                this.moveModule(module, 'right');
                break;
            case 'delete':
                this.deleteModule(module);
                break;
        }
    }
    
    moveModule(module, direction) {
        const currentColumn = module.closest('.content-column');
        if (!currentColumn) return;
        
        const currentIndex = Array.from(currentColumn.children).indexOf(module);
        
        if (direction === 'left' && currentIndex > 0) {
            currentColumn.insertBefore(module, currentColumn.children[currentIndex - 1]);
        } else if (direction === 'right' && currentIndex < currentColumn.children.length - 1) {
            currentColumn.insertBefore(module, currentColumn.children[currentIndex + 2]);
        }
    }
    
    deleteModule(module) {
        if (confirm('Supprimer ce module ?')) {
            const column = module.closest('.content-column');
            module.remove();
            
            // Remettre le placeholder si la colonne est vide
            if (column && column.children.length === 0) {
                column.innerHTML = `
                    <div class="column-placeholder">
                        <div class="placeholder-icon">📝</div>
                        <div class="placeholder-text">Cliquez sur un module pour commencer</div>
                    </div>
                `;
            }
            
            this.currentModule = null;
            this.hideOptions();
        }
    }
    
    selectModule(module) {
        // Désélectionner tous les modules et sections
        this.modal.querySelectorAll('.content-module').forEach(m => {
            m.classList.remove('selected');
        });
        this.modal.querySelectorAll('.content-section').forEach(s => {
            s.classList.remove('selected');
        });
        
        // Sélectionner le module
        module.classList.add('selected');
        this.currentModule = module;
        
        // Afficher les options correspondantes
        this.showModuleOptions(module);
    }
    
    selectSection(section) {
        // Désélectionner tous les modules et sections
        this.modal.querySelectorAll('.content-module').forEach(m => {
            m.classList.remove('selected');
        });
        this.modal.querySelectorAll('.content-section').forEach(s => {
            s.classList.remove('selected');
        });
        
        // Sélectionner la section
        section.classList.add('selected');
        this.currentModule = null;
        
        // Afficher les options de section
        this.showSectionOptions(section);
    }
    
    hideOptions() {
        this.optionsContent.innerHTML = `
            <div class="no-selection">
                <p>Sélectionnez un module pour voir les options</p>
            </div>
        `;
    }
    
    showModuleOptions(module) {
        const type = module.dataset.type;
        let optionsHTML = '';
        
        switch (type) {
            case 'text':
                optionsHTML = `
                    <div class="text-options">
                        <h4>Formatage du texte</h4>
                        <div class="option-group">
                            <label>Style :</label>
                            <div class="format-buttons">
                                <button type="button" class="format-btn" data-command="bold">B</button>
                                <button type="button" class="format-btn" data-command="italic">I</button>
                                <button type="button" class="format-btn" data-command="underline">U</button>
                            </div>
                        </div>
                        <div class="option-group">
                            <label>Taille :</label>
                            <select class="font-size-select">
                                <option value="12px">12px</option>
                                <option value="14px">14px</option>
                                <option value="16px" selected>16px</option>
                                <option value="18px">18px</option>
                                <option value="20px">20px</option>
                                <option value="24px">24px</option>
                            </select>
                        </div>
                        <div class="option-group">
                            <label>Couleur :</label>
                            <input type="color" class="color-picker" value="#000000">
                        </div>
                        <div class="option-group">
                            <label>Alignement :</label>
                            <div class="align-buttons">
                                <button type="button" class="align-btn" data-align="left">⬅️</button>
                                <button type="button" class="align-btn" data-align="center">↔️</button>
                                <button type="button" class="align-btn" data-align="right">➡️</button>
                            </div>
                        </div>
                    </div>
                `;
                break;
                
            default:
                optionsHTML = `
                    <div class="default-options">
                        <h4>Options du module</h4>
                        <p>Module ${this.getModuleLabel(type)}</p>
                    </div>
                `;
        }
        
        this.optionsContent.innerHTML = optionsHTML;
        this.bindOptionsEvents();
    }
    
    bindOptionsEvents() {
        // Formatage du texte
        this.optionsContent.addEventListener('click', (e) => {
            const formatBtn = e.target.closest('.format-btn');
            if (formatBtn && this.currentModule) {
                const command = formatBtn.dataset.command;
                const content = this.currentModule.querySelector('.module-content');
                content.focus();
                formatBtn.classList.toggle('active');
                document.execCommand(command, false, null);
            }
            
            const alignBtn = e.target.closest('.align-btn');
            if (alignBtn && this.currentModule) {
                const align = alignBtn.dataset.align;
                const content = this.currentModule.querySelector('.module-content');
                
                this.optionsContent.querySelectorAll('.align-btn').forEach(btn => {
                    btn.classList.remove('active');
                });
                
                alignBtn.classList.add('active');
                content.style.textAlign = align;
            }
        });
        
        // Taille de police
        const fontSizeSelect = this.optionsContent.querySelector('.font-size-select');
        if (fontSizeSelect) {
            fontSizeSelect.addEventListener('change', (e) => {
                if (this.currentModule) {
                    const content = this.currentModule.querySelector('.module-content');
                    content.focus();
                    const size = e.target.value;
                    document.execCommand('fontSize', false, size);
                }
            });
        }
        
        // Couleur
        const colorPicker = this.optionsContent.querySelector('.color-picker');
        if (colorPicker) {
            colorPicker.addEventListener('change', (e) => {
                if (this.currentModule) {
                    const content = this.currentModule.querySelector('.module-content');
                    content.focus();
                    const color = e.target.value;
                    const selection = window.getSelection();
                    
                    if (selection.rangeCount > 0) {
                        const range = selection.getRangeAt(0);
                        if (!range.collapsed) {
                            this.applyColorToSelection(color);
                        } else {
                            this.applyColorToContent(content, color);
                        }
                    } else {
                        this.applyColorToContent(content, color);
                    }
                }
            });
        }
        
        // Options d'image
        this.bindImageOptionsEvents();
        
        // Actions d'image
        this.optionsContent.addEventListener('click', (e) => {
            const imageActionBtn = e.target.closest('.image-action-btn');
            if (imageActionBtn && this.currentModule) {
                const action = imageActionBtn.dataset.action;
                this.handleImageAction(action);
            }
        });
        
        // Propriétés d'image
        const imageWidth = this.optionsContent.querySelector('.image-width');
        const imageHeight = this.optionsContent.querySelector('.image-height');
        const imageAlt = this.optionsContent.querySelector('.image-alt');
        const imageCaption = this.optionsContent.querySelector('.image-caption');
        
        if (imageWidth) {
            imageWidth.addEventListener('change', (e) => {
                this.updateImageProperty('width', e.target.value);
            });
        }
        
        if (imageHeight) {
            imageHeight.addEventListener('change', (e) => {
                this.updateImageProperty('height', e.target.value);
            });
        }
        
        if (imageAlt) {
            imageAlt.addEventListener('input', (e) => {
                this.updateImageProperty('alt', e.target.value);
            });
        }
        
        if (imageCaption) {
            imageCaption.addEventListener('input', (e) => {
                this.updateImageProperty('caption', e.target.value);
            });
        }
    }
    
    getModuleIcon(type) {
        const icons = {
            'text': '📝',
            'image': '🖼️',
            'video': '🎥',
            'quote': '💬',
            'gallery': '🖼️',
            'heading': '📋',
            'list': '📋',
            'divider': '➖'
        };
        return icons[type] || '📄';
    }
    
    getModuleLabel(type) {
        const labels = {
            'text': 'Texte',
            'image': 'Image',
            'video': 'Vidéo',
            'quote': 'Citation',
            'gallery': 'Galerie',
            'heading': 'Titre',
            'list': 'Liste',
            'divider': 'Séparateur'
        };
        return labels[type] || type;
    }
    
    generateModuleId() {
        return 'module_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
    }
    
    save() {
        const content = this.getContent();
        if (this.options.onSave) {
            this.options.onSave(content);
        }
        this.close();
    }
    
    close() {
        if (this.options.onClose) {
            this.options.onClose();
        }
        this.modal.remove();
    }
    
    open() {
        this.modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        
        // Créer une première section par défaut si aucune n'existe
        if (this.getSectionCount() === 0) {
            console.log('Création de la première section par défaut');
            this.addSection(1);
        }
        
        // Afficher le message par défaut dans les options
        this.hideOptions();
        
        console.log('Éditeur ouvert avec', this.getSectionCount(), 'section(s)');
    }
    
    addStyles() {
        const styles = `
            <style>
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
                .fullscreen-editor-modal * {
                    box-sizing: border-box;
                }
                
                /* Forcer le thème sombre sur tous les éléments */
                .fullscreen-editor-modal,
                .fullscreen-editor-modal *,
                .fullscreen-editor-container,
                .fullscreen-editor-container * {
                    background-color: var(--primary) !important;
                    color: var(--text) !important;
                }
                
                .fullscreen-editor-modal {
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100vw;
                    height: 100vh;
                    background: rgba(0, 0, 0, 0.9);
                    display: none;
                    z-index: 10000;
                    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                }
                
                .fullscreen-editor-container {
                    width: 100%;
                    height: 100%;
                    display: flex;
                    flex-direction: column;
                    background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
                    color: var(--text);
                }
                
                /* Header de l'éditeur */
                .editor-header {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    padding: 1rem 2rem;
                    background: var(--bg-light) !important;
                    border-bottom: 2px solid var(--border);
                    backdrop-filter: blur(10px);
                }
                
                .header-left h2 {
                    color: var(--belgium-yellow) !important;
                    margin: 0;
                    font-size: 1.5rem;
                    font-weight: 700;
                    text-shadow: 0 0 10px rgba(255, 215, 0, 0.5);
                }
                
                .header-center {
                    display: flex;
                    gap: 1rem;
                }
                
                .header-btn {
                    display: flex;
                    align-items: center;
                    gap: 0.5rem;
                    padding: 0.75rem 1.5rem;
                    border: 1px solid var(--border);
                    background: var(--bg-light);
                    color: var(--text);
                    cursor: pointer;
                    border-radius: 8px;
                    transition: all 0.3s ease;
                    font-weight: 600;
                    font-size: 0.9rem;
                }
                
                .header-btn:hover {
                    background: var(--belgium-red);
                    color: white;
                    border-color: var(--belgium-red);
                    transform: translateY(-2px);
                    box-shadow: 0 4px 12px rgba(255, 0, 0, 0.3);
                }
                
                .close-btn {
                    background: var(--error);
                    color: white;
                    border-color: var(--error);
                    padding: 0.75rem;
                    border-radius: 8px;
                }
                
                /* Corps de l'éditeur */
                .editor-body {
                    display: flex;
                    flex: 1;
                    overflow: hidden;
                }
                
                /* Barres latérales */
                .sidebar-left,
                .sidebar-right {
                    background: var(--bg-light) !important;
                    border: 1px solid var(--border);
                    overflow-y: auto;
                }
                
                .sidebar-left {
                    width: 280px;
                    border-right: 2px solid var(--border);
                }
                
                .sidebar-right {
                    width: 320px;
                    border-left: 2px solid var(--border);
                }
                
                .sidebar-section {
                    padding: 1.5rem;
                    border-bottom: 1px solid var(--border);
                }
                
                .sidebar-section h3 {
                    margin: 0 0 1rem 0;
                    font-size: 1.1rem;
                    color: var(--belgium-yellow);
                    font-weight: 600;
                }
                
                /* Boutons de modules */
                .module-buttons {
                    display: flex;
                    flex-direction: column;
                    gap: 0.75rem;
                }
                
                .module-btn {
                    display: flex;
                    align-items: center;
                    gap: 0.75rem;
                    padding: 1rem;
                    border: 1px solid var(--border);
                    background: var(--bg-light) !important;
                    color: var(--text) !important;
                    cursor: pointer;
                    border-radius: 8px;
                    transition: all 0.3s ease;
                    text-align: left;
                    font-weight: 500;
                }
                
                .module-btn:hover {
                    background: var(--belgium-red);
                    color: white;
                    border-color: var(--belgium-red);
                    transform: translateY(-2px);
                    box-shadow: 0 4px 12px rgba(255, 0, 0, 0.3);
                }
                
                /* Boutons de disposition */
                .layout-buttons {
                    display: flex;
                    gap: 0.75rem;
                }
                
                .layout-btn {
                    padding: 0.75rem;
                    border: 1px solid var(--border);
                    background: var(--bg-light);
                    cursor: pointer;
                    border-radius: 8px;
                    transition: all 0.3s ease;
                }
                
                .layout-btn:hover,
                .layout-btn.active {
                    background: var(--belgium-yellow);
                    border-color: var(--belgium-yellow);
                    transform: translateY(-2px);
                    box-shadow: 0 4px 12px rgba(255, 215, 0, 0.3);
                }
                
                .layout-preview {
                    width: 40px;
                    height: 20px;
                    background: var(--border);
                    border-radius: 4px;
                }
                
                .layout-preview.single {
                    background: var(--belgium-yellow);
                }
                
                .layout-preview.double {
                    background: linear-gradient(to right, var(--belgium-yellow) 50%, var(--border) 50%);
                }
                
                .layout-preview.triple {
                    background: linear-gradient(to right, var(--belgium-yellow) 33%, var(--border) 33%, var(--border) 66%, var(--belgium-yellow) 66%);
                }
                
                .section-actions {
                    margin-top: 1rem;
                    padding-top: 1rem;
                    border-top: 1px solid var(--border);
                }
                
                .add-section-btn {
                    display: flex;
                    align-items: center;
                    gap: 0.5rem;
                    width: 100%;
                    padding: 0.75rem 1rem;
                    border: 2px dashed var(--border);
                    background: transparent;
                    color: var(--text-muted);
                    cursor: pointer;
                    border-radius: 8px;
                    transition: all 0.3s ease;
                    font-weight: 500;
                    font-size: 0.9rem;
                }
                
                .add-section-btn:hover {
                    border-color: var(--belgium-yellow);
                    color: var(--belgium-yellow);
                    background: rgba(255, 215, 0, 0.1);
                }
                
                /* Zone principale d'édition */
                .editor-main {
                    flex: 1;
                    background: var(--primary);
                    overflow: hidden;
                }
                
                .editor-content {
                    height: 100%;
                    padding: 2rem;
                    overflow-y: auto;
                    background: var(--primary);
                }
                
                /* Sections de contenu */
                .content-sections {
                    display: flex;
                    flex-direction: column;
                    gap: 2rem;
                }
                
                .content-section {
                    border: 2px solid var(--border);
                    border-radius: 12px;
                    overflow: hidden;
                    background: var(--primary);
                    transition: all 0.3s ease;
                }
                
                .content-section:hover {
                    border-color: var(--belgium-yellow);
                    box-shadow: 0 8px 25px rgba(255, 215, 0, 0.2);
                }
                
                .content-section.selected {
                    border-color: var(--belgium-red);
                    box-shadow: 0 0 0 3px rgba(255, 0, 0, 0.3);
                }
                
                .section-header {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    padding: 1rem 1.5rem;
                    background: var(--bg-hover);
                    border-bottom: 1px solid var(--border);
                }
                
                .section-info {
                    display: flex;
                    flex-direction: column;
                    gap: 0.25rem;
                }
                
                .section-label {
                    font-size: 1rem;
                    font-weight: 600;
                    color: var(--belgium-yellow);
                }
                
                .section-layout {
                    font-size: 0.8rem;
                    color: var(--text-muted);
                }
                
                .section-actions {
                    display: flex;
                    gap: 0.5rem;
                }
                
                .section-action {
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    width: 32px;
                    height: 32px;
                    border: 1px solid var(--border);
                    background: transparent;
                    color: var(--text-muted);
                    cursor: pointer;
                    border-radius: 6px;
                    transition: all 0.3s ease;
                    font-size: 0.9rem;
                }
                
                .section-action:hover {
                    background: var(--belgium-red);
                    color: white;
                    border-color: var(--belgium-red);
                }
                
                .section-action[data-action="add-column"]:hover {
                    background: var(--success);
                    border-color: var(--success);
                }
                
                .section-action[data-action="remove-column"]:hover {
                    background: var(--warning);
                    border-color: var(--warning);
                }
                
                .section-action[data-action="move-up"]:hover,
                .section-action[data-action="move-down"]:hover {
                    background: var(--belgium-yellow);
                    color: var(--primary);
                    border-color: var(--belgium-yellow);
                }
                
                .section-action[data-action="delete-section"]:hover {
                    background: var(--error);
                    border-color: var(--error);
                }
                
                /* Colonnes */
                .content-columns {
                    height: 100%;
                }
                
                .content-columns[data-columns="1"] {
                    display: block;
                }
                
                .content-columns[data-columns="2"] {
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                    gap: 2rem;
                }
                
                .content-columns[data-columns="3"] {
                    display: grid;
                    grid-template-columns: 1fr 1fr 1fr;
                    gap: 2rem;
                }
                
                .content-column {
                    min-height: 300px;
                    border: 2px dashed var(--border);
                    border-radius: 12px;
                    padding: 2rem;
                    transition: all 0.3s ease;
                    background: var(--primary);
                }
                
                .content-column:hover {
                    border-color: var(--belgium-yellow);
                    box-shadow: 0 8px 25px rgba(255, 215, 0, 0.2);
                }
                
                .column-placeholder {
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    justify-content: center;
                    height: 100%;
                    color: var(--text-muted);
                    text-align: center;
                }
                
                .placeholder-icon {
                    font-size: 4rem;
                    margin-bottom: 1rem;
                    opacity: 0.5;
                }
                
                .placeholder-text {
                    font-size: 1.1rem;
                    font-weight: 500;
                }
                
                /* Modules de contenu */
                .content-module {
                    margin-bottom: 1.5rem;
                    border: 2px solid var(--border);
                    border-radius: 10px;
                    overflow: hidden;
                    transition: all 0.3s ease;
                    background: var(--primary);
                }
                
                .content-module:hover {
                    border-color: var(--belgium-yellow);
                    box-shadow: 0 8px 25px rgba(255, 215, 0, 0.2);
                }
                
                .content-module.selected {
                    border-color: var(--belgium-red);
                    box-shadow: 0 0 0 3px rgba(255, 0, 0, 0.3);
                }
                
                .module-header {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    padding: 1rem 1.5rem;
                    background: var(--bg-hover);
                    border-bottom: 1px solid var(--border);
                }
                
                .module-type {
                    font-size: 0.9rem;
                    font-weight: 600;
                    color: var(--belgium-yellow);
                }
                
                .module-actions {
                    display: flex;
                    gap: 0.5rem;
                }
                
                .module-action {
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    width: 32px;
                    height: 32px;
                    border: 1px solid var(--border);
                    background: transparent;
                    color: var(--text-muted);
                    cursor: pointer;
                    border-radius: 6px;
                    transition: all 0.3s ease;
                    font-size: 0.9rem;
                }
                
                .module-action:hover {
                    background: var(--belgium-red);
                    color: white;
                    border-color: var(--belgium-red);
                }
                
                .module-content {
                    padding: 1.5rem;
                    min-height: 60px;
                    background: var(--primary) !important;
                    color: var(--text) !important;
                    font-size: 16px;
                    line-height: 1.7;
                }
                
                .module-content[contenteditable="true"] {
                    outline: none;
                }
                
                .module-content[contenteditable="true"]:focus {
                    background: var(--bg-hover);
                }
                
                /* Options dans la barre latérale droite */
                .options-content {
                    padding: 0;
                }
                
                .option-group {
                    margin-bottom: 1.5rem;
                }
                
                .option-group label {
                    display: block;
                    margin-bottom: 0.5rem;
                    font-weight: 600;
                    color: var(--text);
                }
                
                .format-buttons,
                .align-buttons {
                    display: flex;
                    gap: 0.5rem;
                    flex-wrap: wrap;
                }
                
                .format-btn,
                .align-btn {
                    padding: 0.5rem 1rem;
                    border: 1px solid var(--border);
                    background: var(--bg-light);
                    color: var(--text);
                    cursor: pointer;
                    border-radius: 6px;
                    font-weight: 600;
                    transition: all 0.3s ease;
                }
                
                .format-btn:hover,
                .align-btn:hover {
                    background: var(--belgium-red);
                    color: white;
                    border-color: var(--belgium-red);
                }
                
                .format-btn.active,
                .align-btn.active {
                    background: var(--belgium-yellow);
                    color: var(--primary);
                    border-color: var(--belgium-yellow);
                    font-weight: 700;
                }
                
                .font-size-select,
                .color-picker {
                    width: 100%;
                    padding: 0.75rem;
                    border: 1px solid var(--border);
                    border-radius: 6px;
                    background: var(--bg-light);
                    color: var(--text);
                    font-size: 0.9rem;
                }
                
                .color-picker {
                    height: 50px;
                    cursor: pointer;
                }
                
                .no-selection {
                    text-align: center;
                    color: var(--text-muted);
                    padding: 2rem;
                    font-style: italic;
                }
                
                /* Options de section */
                .section-options {
                    padding: 1rem;
                }
                
                .section-info {
                    font-weight: 600;
                    color: var(--belgium-yellow);
                    margin: 0.5rem 0;
                }
                
                .section-action-buttons {
                    display: flex;
                    flex-direction: column;
                    gap: 0.5rem;
                }
                
                .section-action-btn {
                    display: flex;
                    align-items: center;
                    gap: 0.5rem;
                    padding: 0.75rem 1rem;
                    border: 1px solid var(--border);
                    background: var(--bg-light);
                    color: var(--text);
                    cursor: pointer;
                    border-radius: 6px;
                    transition: all 0.3s ease;
                    font-size: 0.9rem;
                    font-weight: 500;
                }
                
                .section-action-btn:hover {
                    background: var(--belgium-red);
                    color: white;
                    border-color: var(--belgium-red);
                    transform: translateY(-1px);
                }
                
                .section-action-btn.danger:hover {
                    background: var(--error);
                    border-color: var(--error);
                }
                
                                 .instruction-text {
                     font-size: 0.9rem;
                     color: var(--text-muted);
                     font-style: italic;
                     margin: 0;
                 }
                 
                 /* Styles pour le module image */
                 .image-upload-area {
                     width: 100%;
                     min-height: 200px;
                     border: 2px dashed var(--border);
                     border-radius: 8px;
                     display: flex;
                     align-items: center;
                     justify-content: center;
                     cursor: pointer;
                     transition: all 0.3s ease;
                     background: var(--bg-light);
                 }
                 
                 .image-upload-area:hover,
                 .image-upload-area.drag-over {
                     border-color: var(--belgium-yellow);
                     background: rgba(255, 215, 0, 0.1);
                 }
                 
                 .image-placeholder {
                     text-align: center;
                     color: var(--text-muted);
                 }
                 
                 .upload-icon {
                     font-size: 3rem;
                     margin-bottom: 1rem;
                     opacity: 0.7;
                 }
                 
                 .upload-text {
                     font-size: 1.1rem;
                     font-weight: 500;
                     margin-bottom: 0.5rem;
                 }
                 
                 .upload-hint {
                     font-size: 0.9rem;
                     opacity: 0.8;
                 }
                 
                 .image-container {
                     width: 100%;
                     text-align: center;
                 }
                 
                 .uploaded-image {
                     max-width: 100%;
                     height: auto;
                     border-radius: 8px;
                     box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
                 }
                 
                 .image-caption {
                     margin-top: 0.5rem;
                     font-size: 0.9rem;
                     color: var(--text-muted);
                     font-style: italic;
                 }
                 
                 /* Options d'image */
                 .size-inputs {
                     display: flex;
                     align-items: center;
                     gap: 0.5rem;
                 }
                 
                 .size-inputs input {
                     flex: 1;
                     padding: 0.5rem;
                     border: 1px solid var(--border);
                     border-radius: 4px;
                     background: var(--bg-light);
                     color: var(--text);
                     font-size: 0.9rem;
                 }
                 
                 .size-unit {
                     color: var(--text-muted);
                     font-size: 0.9rem;
                 }
                 
                 .image-action-buttons {
                     display: flex;
                     flex-direction: column;
                     gap: 0.5rem;
                 }
                 
                 .image-action-btn {
                     display: flex;
                     align-items: center;
                     gap: 0.5rem;
                     padding: 0.75rem 1rem;
                     border: 1px solid var(--border);
                     background: var(--bg-light);
                     color: var(--text);
                     cursor: pointer;
                     border-radius: 6px;
                     transition: all 0.3s ease;
                     font-size: 0.9rem;
                     font-weight: 500;
                 }
                 
                 .image-action-btn:hover {
                     background: var(--belgium-red);
                     color: white;
                     border-color: var(--belgium-red);
                     transform: translateY(-1px);
                 }
                 
                 .image-action-btn.danger:hover {
                     background: var(--error);
                     border-color: var(--error);
                 }
            </style>
        `;
        document.head.insertAdjacentHTML('beforeend', styles);
    }
    
    showSectionOptions(section) {
        const columnCount = section.dataset.columns;
        const sectionNumber = Array.from(this.modal.querySelectorAll('.content-section')).indexOf(section) + 1;
        
        const optionsHTML = `
            <div class="section-options">
                <h4>Options de la section</h4>
                <div class="option-group">
                    <label>Section sélectionnée :</label>
                    <p class="section-info">Section ${sectionNumber} (${columnCount} colonne${columnCount > 1 ? 's' : ''})</p>
                </div>
                <div class="option-group">
                    <label>Actions :</label>
                    <div class="section-action-buttons">
                        <button type="button" class="section-action-btn" data-action="add-column">
                            <span class="icon">➕</span> Ajouter une colonne
                        </button>
                        <button type="button" class="section-action-btn" data-action="remove-column">
                            <span class="icon">➖</span> Supprimer une colonne
                        </button>
                        <button type="button" class="section-action-btn" data-action="move-up">
                            <span class="icon">⬆️</span> Déplacer vers le haut
                        </button>
                        <button type="button" class="section-action-btn" data-action="move-down">
                            <span class="icon">⬇️</span> Déplacer vers le bas
                        </button>
                        <button type="button" class="section-action-btn danger" data-action="delete-section">
                            <span class="icon">🗑️</span> Supprimer la section
                        </button>
                    </div>
                </div>
                <div class="option-group">
                    <label>Instructions :</label>
                    <p class="instruction-text">Cette section est maintenant sélectionnée. Les modules que vous ajoutez seront placés dans cette section.</p>
                </div>
            </div>
        `;
        
        this.optionsContent.innerHTML = optionsHTML;
        this.bindSectionOptionsEvents(section);
    }
    
    bindImageOptionsEvents() {
        // Actions d'image
        this.optionsContent.addEventListener('click', (e) => {
            const imageActionBtn = e.target.closest('.image-action-btn');
            if (imageActionBtn && this.currentModule) {
                const action = imageActionBtn.dataset.action;
                this.handleImageAction(action);
            }
        });
        
        // Propriétés d'image
        const imageWidth = this.optionsContent.querySelector('.image-width');
        const imageHeight = this.optionsContent.querySelector('.image-height');
        const imageAlt = this.optionsContent.querySelector('.image-alt');
        const imageCaption = this.optionsContent.querySelector('.image-caption');
        
        if (imageWidth) {
            imageWidth.addEventListener('change', (e) => {
                this.updateImageProperty('width', e.target.value);
            });
        }
        
        if (imageHeight) {
            imageHeight.addEventListener('change', (e) => {
                this.updateImageProperty('height', e.target.value);
            });
        }
        
        if (imageAlt) {
            imageAlt.addEventListener('input', (e) => {
                this.updateImageProperty('alt', e.target.value);
            });
        }
        
        if (imageCaption) {
            imageCaption.addEventListener('input', (e) => {
                this.updateImageProperty('caption', e.target.value);
            });
        }
    }
    
    handleImageAction(action) {
        switch (action) {
            case 'upload':
                this.triggerImageUpload();
                break;
            case 'remove':
                this.removeImage();
                break;
        }
    }
    
    triggerImageUpload() {
        console.log('triggerImageUpload appelé');
        const fileInput = this.currentModule.querySelector('.image-file-input');
        if (fileInput) {
            console.log('FileInput trouvé, déclenchement du clic');
            fileInput.click();
        } else {
            console.error('FileInput non trouvé dans le module courant');
        }
    }
    
    removeImage() {
        if (confirm('Supprimer cette image ?')) {
            const content = this.currentModule.querySelector('.module-content');
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
            this.bindImageUploadEvents();
        }
    }
    
    bindImageUploadEvents(module = null) {
        const targetModule = module || this.currentModule;
        if (!targetModule) {
            console.warn('bindImageUploadEvents: Aucun module cible');
            return;
        }
        
        console.log('bindImageUploadEvents: Liaison des événements pour le module image');
        
        // Attendre un peu que le DOM soit prêt
        setTimeout(() => {
            const uploadArea = targetModule.querySelector('.image-upload-area');
            const fileInput = targetModule.querySelector('.image-file-input');
            
            if (!uploadArea) {
                console.warn('bindImageUploadEvents: Zone d\'upload non trouvée');
                return;
            }
            
            if (!fileInput) {
                console.warn('bindImageUploadEvents: Input file non trouvé');
                return;
            }
            
            console.log('Éléments trouvés, liaison des événements...');
            
            // Supprimer les anciens événements pour éviter les doublons
            const newUploadArea = uploadArea.cloneNode(true);
            uploadArea.parentNode.replaceChild(newUploadArea, uploadArea);
            
            const newFileInput = fileInput.cloneNode(true);
            fileInput.parentNode.replaceChild(newFileInput, fileInput);
            
            // Clic sur la zone d'upload
            newUploadArea.addEventListener('click', (e) => {
                console.log('Clic sur la zone d\'upload détecté');
                e.preventDefault();
                e.stopPropagation();
                if (!e.target.closest('.image-action-btn')) {
                    console.log('Déclenchement du clic sur fileInput');
                    newFileInput.click();
                }
            });
            
            // Drag and drop
            newUploadArea.addEventListener('dragover', (e) => {
                e.preventDefault();
                newUploadArea.classList.add('drag-over');
            });
            
            newUploadArea.addEventListener('dragleave', (e) => {
                e.preventDefault();
                newUploadArea.classList.remove('drag-over');
            });
            
            newUploadArea.addEventListener('drop', (e) => {
                e.preventDefault();
                newUploadArea.classList.remove('drag-over');
                
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    console.log('Fichier déposé:', files[0].name);
                    this.handleImageFile(files[0], targetModule);
                }
            });
            
            // Sélection de fichier
            newFileInput.addEventListener('change', (e) => {
                console.log('Fichier sélectionné:', e.target.files[0]?.name);
                if (e.target.files.length > 0) {
                    this.handleImageFile(e.target.files[0], targetModule);
                }
            });
            
            console.log('Événements d\'upload liés avec succès');
        }, 100);
    }
    
    handleImageFile(file, module) {
        console.log('handleImageFile appelé avec:', file.name, file.type);
        
        if (!file.type.startsWith('image/')) {
            alert('Veuillez sélectionner une image valide.');
            return;
        }
        
        const reader = new FileReader();
        reader.onload = (e) => {
            console.log('Fichier lu avec succès, mise à jour du module');
            const content = module.querySelector('.module-content');
            if (!content) {
                console.error('Module content non trouvé');
                return;
            }
            
            content.innerHTML = `
                <div class="image-container">
                    <img src="${e.target.result}" alt="" class="uploaded-image">
                    <div class="image-caption" style="display: none;"></div>
                </div>
            `;
            
            // Sélectionner automatiquement le module
            this.selectModule(module);
            console.log('Image uploadée avec succès');
        };
        
        reader.onerror = (e) => {
            console.error('Erreur lors de la lecture du fichier:', e);
            alert('Erreur lors de la lecture du fichier');
        };
        
        reader.readAsDataURL(file);
    }
    
    updateImageProperty(property, value) {
        const img = this.currentModule.querySelector('img');
        if (!img) return;
        
        switch (property) {
            case 'width':
                img.style.width = value + 'px';
                break;
            case 'height':
                img.style.height = value + 'px';
                break;
            case 'alt':
                img.alt = value;
                break;
            case 'caption':
                const caption = this.currentModule.querySelector('.image-caption');
                if (caption) {
                    caption.textContent = value;
                    caption.style.display = value ? 'block' : 'none';
                } else if (value) {
                    const captionDiv = document.createElement('div');
                    captionDiv.className = 'image-caption';
                    captionDiv.textContent = value;
                    img.parentNode.appendChild(captionDiv);
                }
                break;
        }
    }
    
    bindSectionOptionsEvents(section) {
        this.optionsContent.addEventListener('click', (e) => {
            const actionBtn = e.target.closest('.section-action-btn');
            if (actionBtn) {
                const action = actionBtn.dataset.action;
                this.handleSectionAction(section, action);
            }
        });
    }
}

// Rendre la classe disponible globalement
window.FullscreenEditor = FullscreenEditor;