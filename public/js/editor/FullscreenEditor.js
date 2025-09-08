/**
 * Éditeur WYSIWYG plein écran - Version modulaire
 * Architecture modulaire avec barres latérales et système de colonnes
 */
class FullscreenEditor {
    constructor(options = {}) {
        console.log('🏗️ Constructeur FullscreenEditor appelé');
        console.log('Options reçues:', options);
        
        this.options = {
            onSave: null,
            onClose: null,
            initialContent: '',
            ...options
        };
        
        this.currentModule = null;
        this.currentSection = null;
        this.modules = new Map(); // Stockage des instances de modules
        this.sections = new Map(); // Stockage des sections
        
        console.log('🔧 Initialisation des gestionnaires...');
        
        try {
            // Initialisation des gestionnaires
            this.styleManager = new StyleManager();
            console.log('✅ StyleManager créé');
            
            this.moduleFactory = new ModuleFactory(this);
            console.log('✅ ModuleFactory créé');
            
            console.log('🚀 Appel de this.init()...');
            this.init();
        } catch (error) {
            console.error('❌ Erreur dans le constructeur:', error);
            throw error;
        }
    }
    
    init() {
        console.log('🔧 Initialisation de l\'éditeur...');
        try {
            this.createModal();
            console.log('✅ Modal créé');
            
            this.createLayout();
            console.log('✅ Layout créé');
            
            this.bindEvents();
            console.log('✅ Événements liés');
            
            this.styleManager.addStyles();
            console.log('✅ Styles ajoutés');
            
            // Charger le contenu initial s'il existe
            if (this.options.initialContent && this.options.initialContent.trim()) {
                console.log('📂 Chargement du contenu initial:', this.options.initialContent.substring(0, 100) + '...');
                this.loadInitialContent();
            }
            
            this.open(); // Ouvrir l'éditeur automatiquement
            console.log('✅ Méthode open() appelée');
        } catch (error) {
            console.error('❌ Erreur lors de l\'initialisation:', error);
            throw error;
        }
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
                                <button type="button" class="module-btn" data-module="text" draggable="true">
                                    <span class="icon">📝</span> Texte
                                </button>
                                <button type="button" class="module-btn" data-module="image" draggable="true">
                                    <span class="icon">🖼️</span> Image
                                </button>
                                <button type="button" class="module-btn" data-module="video" draggable="true">
                                    <span class="icon">🎥</span> Vidéo
                                </button>
                                <button type="button" class="module-btn" data-module="quote" draggable="true">
                                    <span class="icon">💬</span> Citation
                                </button>
                                <button type="button" class="module-btn" data-module="gallery" draggable="true">
                                    <span class="icon">🖼️</span> Galerie
                                </button>
                                <button type="button" class="module-btn" data-module="heading" draggable="true">
                                    <span class="icon">📋</span> Titre
                                </button>
                                <button type="button" class="module-btn" data-module="list" draggable="true">
                                    <span class="icon">📋</span> Liste
                                </button>
                                <button type="button" class="module-btn" data-module="separator" draggable="true">
                                    <span class="icon">➖</span> Séparateur
                                </button>
                                <button type="button" class="module-btn" data-module="table" draggable="true">
                                    <span class="icon">📊</span> Tableau
                                </button>
                                <button type="button" class="module-btn" data-module="button" draggable="true">
                                    <span class="icon">🔘</span> Bouton
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
        console.log('🔧 Création du layout...');
        console.log('Modal:', this.modal);
        
        this.leftSidebar = this.modal.querySelector('.sidebar-left');
        console.log('LeftSidebar:', this.leftSidebar);
        
        this.rightSidebar = this.modal.querySelector('.sidebar-right');
        console.log('RightSidebar:', this.rightSidebar);
        
        this.editorMain = this.modal.querySelector('.editor-main');
        console.log('EditorMain:', this.editorMain);
        
        this.contentSections = this.modal.querySelector('.content-sections');
        console.log('ContentSections:', this.contentSections);
        
        this.optionsContent = this.modal.querySelector('.options-content');
        console.log('OptionsContent:', this.optionsContent);
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
                this.selectModuleByElement(module);
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

        // Drag & Drop des modules
        this.bindDragAndDropEvents();
    }

    /**
     * Gestion des événements de drag & drop pour les modules
     */
    bindDragAndDropEvents() {
        // Événements de drag sur les boutons de modules
        this.modal.addEventListener('dragstart', (e) => {
            const moduleBtn = e.target.closest('.module-btn');
            if (moduleBtn) {
                const moduleType = moduleBtn.dataset.module;
                e.dataTransfer.setData('text/plain', moduleType);
                e.dataTransfer.effectAllowed = 'copy';
                
                // Ajouter une classe pour le feedback visuel
                moduleBtn.classList.add('dragging');
                
                console.log('🚀 Début du drag pour le module:', moduleType);
            }
        });

        // Gestion du dragend pour les boutons de modules
        this.modal.addEventListener('dragend', (e) => {
            const moduleBtn = e.target.closest('.module-btn');
            if (moduleBtn) {
                moduleBtn.classList.remove('dragging');
                console.log('🏁 Fin du drag pour module:', moduleBtn.dataset.module);
            }
            
            // Nettoyer les classes de feedback
            this.modal.querySelectorAll('.drop-zone-active').forEach(el => {
                el.classList.remove('drop-zone-active');
            });
        });

        // Événements de drop sur les colonnes
        this.modal.addEventListener('dragover', (e) => {
            // Toujours prévenir le comportement par défaut pour permettre le drop
            e.preventDefault();
            
            const column = e.target.closest('.content-column');
            if (column) {
                // Ajouter le feedback visuel
                column.classList.add('drop-zone-active');
            }
        });

        // Gestion spécifique des drops sur les colonnes vides
        this.modal.addEventListener('drop', (e) => {
            e.preventDefault();
            
            // Vérifier si le drop a déjà été traité par un gestionnaire plus spécifique
            if (e.defaultPrevented || e._dropHandled) {
                return;
            }
            
            const column = e.target.closest('.content-column');
            if (column) {
                column.classList.remove('drop-zone-active');
                
                // Vérifier d'abord si c'est un déplacement de module existant
                const draggedModuleId = e.dataTransfer.getData('module-id');
                if (draggedModuleId) {
                    console.log('🔄 Drop de module existant dans colonne vide');
                    this.handleModuleDropInColumn(draggedModuleId, column);
                    e._dropHandled = true;
                    return;
                }
                
                // Sinon, c'est la création d'un nouveau module
                const moduleType = e.dataTransfer.getData('text/plain');
                if (moduleType) {
                    console.log('🎯 Drop du module', moduleType, 'dans la colonne');
                    this.addModule(moduleType, column);
                    e._dropHandled = true;
                }
            } else {
                // Drop sur le modal mais pas sur une colonne spécifique
                // Essayer de trouver la colonne la plus proche ou la première colonne disponible
                const moduleType = e.dataTransfer.getData('text/plain');
                if (moduleType) {
                    console.log('🎯 Drop du module', moduleType, 'sur le modal, recherche de colonne...');
                    const targetColumn = this.getTargetColumn();
                    if (targetColumn) {
                        this.addModule(moduleType, targetColumn);
                        e._dropHandled = true;
                    }
                }
            }
        });

        this.modal.addEventListener('dragleave', (e) => {
            const column = e.target.closest('.content-column');
            if (column) {
                // Vérifier si on quitte vraiment la colonne
                const rect = column.getBoundingClientRect();
                const x = e.clientX;
                const y = e.clientY;
                
                if (x < rect.left || x > rect.right || y < rect.top || y > rect.bottom) {
                    column.classList.remove('drop-zone-active');
                }
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
    
    /**
     * Gérer le drop d'un module existant dans une colonne vide
     */
    handleModuleDropInColumn(moduleId, targetColumn) {
        console.log('🔄 Déplacement du module', moduleId, 'vers la colonne', targetColumn.dataset.column);
        
        const moduleInstance = this.modules.get(moduleId);
        if (!moduleInstance) {
            console.error('❌ Module non trouvé:', moduleId);
            return;
        }
        
        // Retirer le module de sa colonne actuelle
        const currentColumn = moduleInstance.element.closest('.content-column');
        if (currentColumn) {
            moduleInstance.element.remove();
        }
        
        // Ajouter le module à la nouvelle colonne
        targetColumn.appendChild(moduleInstance.element);
        
        // Nettoyer les colonnes vides
        this.cleanupEmptyColumns();
        
        console.log('✅ Module déplacé avec succès');
    }

    addModule(type, targetColumn = null) {
        const column = targetColumn || this.getTargetColumn();
        if (!column) return;
        
        const moduleInstance = this.moduleFactory.createModule(type);
        const moduleElement = moduleInstance.create();
        
        // Stocker l'instance du module
        this.modules.set(moduleElement.dataset.moduleId, moduleInstance);
        
        column.appendChild(moduleElement);
        this.selectModule(moduleInstance);
        
        // Supprimer le placeholder si c'est le premier module
        const placeholder = column.querySelector('.column-placeholder');
        if (placeholder) {
            placeholder.remove();
        }
    }
    
    selectModule(moduleInstance) {
        // Désélectionner tous les modules et sections
        this.modal.querySelectorAll('.content-module').forEach(m => {
            m.classList.remove('selected');
        });
        this.modal.querySelectorAll('.content-section').forEach(s => {
            s.classList.remove('selected');
        });
        
        // Sélectionner le module
        if (moduleInstance.element) {
            moduleInstance.element.classList.add('selected');
        }
        
        this.currentModule = moduleInstance;
        this.currentSection = null;
        
        // Afficher les options du module
        this.showModuleOptions(moduleInstance);
    }
    
    selectModuleByElement(moduleElement) {
        const moduleId = moduleElement.dataset.moduleId;
        const moduleInstance = this.modules.get(moduleId);
        
        if (moduleInstance) {
            this.selectModule(moduleInstance);
        }
    }

    getModuleById(moduleId) {
        return this.modules.get(moduleId);
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
        this.currentSection = section;
        
        // Afficher les options de section
        this.showSectionOptions(section);
    }
    
    showModuleOptions(moduleInstance) {
        if (!moduleInstance) return;
        
        const optionsHTML = moduleInstance.getOptionsHTML();
        this.optionsContent.innerHTML = optionsHTML;
        moduleInstance.bindOptionsEvents();
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
    
    hideOptions() {
        this.optionsContent.innerHTML = `
            <div class="no-selection">
                <div class="placeholder-icon">📝</div>
                <div class="placeholder-text">Sélectionnez un module pour voir les options</div>
            </div>
        `;
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
            
            // Support du drag & drop pour les colonnes
            this.bindColumnDragEvents(column);
            
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
        this.contentSections.appendChild(section);
        this.sections.set(section.dataset.sectionId, section);
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
        const sections = Array.from(this.contentSections.querySelectorAll('.content-section'));
        const currentIndex = sections.indexOf(section);
        
        if (direction === 'up' && currentIndex > 0) {
            this.contentSections.insertBefore(section, sections[currentIndex - 1]);
        } else if (direction === 'down' && currentIndex < sections.length - 1) {
            this.contentSections.insertBefore(section, sections[currentIndex + 1].nextSibling);
        }
    }
    
    deleteSection(section) {
        if (confirm('Supprimer cette section ? Tous les modules qu\'elle contient seront perdus.')) {
            // Supprimer les modules de la section
            const modules = section.querySelectorAll('.content-module');
            modules.forEach(module => {
                const moduleId = module.dataset.moduleId;
                this.modules.delete(moduleId);
            });
            
            section.remove();
            this.sections.delete(section.dataset.sectionId);
            
            if (this.currentSection === section) {
                this.currentSection = null;
                this.hideOptions();
            }
        }
    }
    
    getTargetColumn() {
        // 1. Vérifier s'il y a une section sélectionnée
        if (this.currentSection) {
            const columns = this.currentSection.querySelectorAll('.content-column');
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
        
        // 2. Sinon, utiliser la première colonne vide disponible
        const sections = this.contentSections.querySelectorAll('.content-section');
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
    
    generateSectionId() {
        return 'section_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
    }
    
    getSectionCount() {
        if (!this.contentSections) {
            console.error('❌ contentSections non défini');
            return 0;
        }
        const count = this.contentSections.querySelectorAll('.content-section').length;
        console.log(`📊 Nombre de sections: ${count}`);
        return count;
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
                    
                    /* Styles pour les sliders */
                    .gallery-slider {
                        position: relative;
                        width: 100%;
                        overflow: hidden;
                        border-radius: 8px;
                        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
                    }
                    
                    .slider-track {
                        display: flex;
                        transition: transform 0.3s ease;
                        width: 100%;
                    }
                    
                    .slider-slide {
                        min-width: 100%;
                        flex-shrink: 0;
                    }
                    
                    .slider-slide img {
                        width: 100%;
                        height: auto;
                        display: block;
                    }
                    
                    .slider-nav {
                        position: absolute;
                        top: 50%;
                        transform: translateY(-50%);
                        background: rgba(0, 0, 0, 0.7);
                        color: white;
                        border: none;
                        width: 50px;
                        height: 50px;
                        border-radius: 50%;
                        cursor: pointer;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        font-size: 18px;
                        transition: background 0.2s;
                        z-index: 10;
                    }
                    
                    .slider-nav:hover {
                        background: rgba(0, 0, 0, 0.9);
                    }
                    
                    .slider-prev {
                        left: 15px;
                    }
                    
                    .slider-next {
                        right: 15px;
                    }
                    
                    .slider-counter {
                        position: absolute;
                        bottom: 15px;
                        left: 50%;
                        transform: translateX(-50%);
                        background: rgba(0, 0, 0, 0.7);
                        color: white;
                        padding: 8px 16px;
                        border-radius: 20px;
                        font-size: 14px;
                        font-weight: 500;
                    }
                    
                    /* Lightbox styles */
                    .lightbox-modal {
                        position: fixed;
                        top: 0;
                        left: 0;
                        width: 100%;
                        height: 100%;
                        background: rgba(0, 0, 0, 0.9);
                        display: none;
                        z-index: 9999;
                        align-items: center;
                        justify-content: center;
                    }
                    
                    .lightbox-content {
                        position: relative;
                        max-width: 90%;
                        max-height: 90%;
                        text-align: center;
                    }
                    
                    .lightbox-image {
                        max-width: 100%;
                        max-height: 100%;
                        object-fit: contain;
                        border-radius: 8px;
                        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
                    }
                    
                    .lightbox-close {
                        position: absolute;
                        top: -40px;
                        right: 0;
                        background: rgba(255, 255, 255, 0.2);
                        border: none;
                        color: white;
                        font-size: 24px;
                        width: 40px;
                        height: 40px;
                        border-radius: 50%;
                        cursor: pointer;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        transition: background 0.2s;
                    }
                    
                    .lightbox-close:hover {
                        background: rgba(255, 255, 255, 0.3);
                    }
                    
                    .lightbox-caption {
                        position: absolute;
                        bottom: -40px;
                        left: 0;
                        right: 0;
                        color: white;
                        text-align: center;
                        padding: 10px;
                    }
                    
                    .lightbox-caption h3 {
                        margin: 0 0 5px 0;
                        font-size: 18px;
                    }
                    
                    .lightbox-caption p {
                        margin: 0;
                        font-size: 14px;
                        opacity: 0.8;
                    }
                    
                    h1 {
                        color: #333333;
                        border-bottom: 2px solid #FF0000;
                        padding-bottom: 10px;
                        margin-bottom: 30px;
                    }
                    
                    .content-module { 
                        margin: 0; 
                        padding: 0;
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
                
                /* Styles pour les vidéos dans l'aperçu */
                .video-container {
                    width: 100%;
                    text-align: center;
                }
                
                .video-container iframe,
                .video-container video {
                    max-width: 100%;
                    height: auto;
                    border-radius: 8px;
                    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
                }
                
                /* Alignement des vidéos dans l'aperçu */
                .video-container.align-left {
                    text-align: left;
                }
                
                .video-container.align-center {
                    text-align: center;
                }
                
                .video-container.align-right {
                    text-align: right;
                }
                
                .video-container.align-left iframe,
                .video-container.align-left video {
                    margin-left: 0;
                    margin-right: auto;
                }
                
                .video-container.align-center iframe,
                .video-container.align-center video {
                    margin-left: auto;
                    margin-right: auto;
                }
                
                .video-container.align-right iframe,
                .video-container.align-right video {
                    margin-left: auto;
                    margin-right: 0;
                }
                
                .video-title {
                    margin-top: 0.5rem;
                    font-size: 1rem;
                    font-weight: 500;
                    color: #333333;
                }
                
                                 .video-description {
                     margin-top: 0.25rem;
                     font-size: 0.9rem;
                     color: #666666;
                     font-style: italic;
                 }
                 
                 /* Styles pour les citations dans l'aperçu */
                 .quote-container {
                     width: 100%;
                     padding: 1.5rem;
                     border-radius: 8px;
                     position: relative;
                     margin: 20px 0;
                 }
                 
                 /* Style par défaut */
                 .quote-container.style-default {
                     background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
                     border: 1px solid #dee2e6;
                 }
                 
                 .quote-container.style-default .quote-text {
                     font-size: 1.1rem;
                     font-style: italic;
                     color: #333333;
                     margin-bottom: 1rem;
                     line-height: 1.6;
                 }
                 
                 .quote-container.style-default .quote-author {
                     font-weight: 600;
                     color: #FFD700;
                     font-size: 0.95rem;
                 }
                 
                 .quote-container.style-default .quote-source {
                     font-size: 0.85rem;
                     color: #666666;
                     font-style: italic;
                     margin-top: 0.25rem;
                 }
                 
                 /* Style minimal */
                 .quote-container.style-minimal {
                     background: transparent;
                     border-left: 4px solid #FFD700;
                     padding-left: 1.5rem;
                 }
                 
                 .quote-container.style-minimal .quote-text {
                     font-size: 1rem;
                     color: #333333;
                     margin-bottom: 0.75rem;
                     line-height: 1.5;
                 }
                 
                 .quote-container.style-minimal .quote-author {
                     font-weight: 500;
                     color: #666666;
                     font-size: 0.9rem;
                 }
                 
                 .quote-container.style-minimal .quote-source {
                     font-size: 0.8rem;
                     color: #666666;
                     margin-top: 0.25rem;
                 }
                 
                 /* Style élégant */
                 .quote-container.style-elegant {
                     background: #f8f9fa;
                     border: 2px solid #FFD700;
                     position: relative;
                 }
                 
                 .quote-container.style-elegant::before {
                     content: '"';
                     position: absolute;
                     top: -10px;
                     left: 20px;
                     font-size: 3rem;
                     color: #FFD700;
                     background: #ffffff;
                     padding: 0 10px;
                     font-family: serif;
                 }
                 
                 .quote-container.style-elegant .quote-text {
                     font-size: 1.1rem;
                     color: #333333;
                     margin-bottom: 1rem;
                     line-height: 1.6;
                     padding-top: 0.5rem;
                 }
                 
                 .quote-container.style-elegant .quote-author {
                     font-weight: 600;
                     color: #FFD700;
                     font-size: 0.95rem;
                     text-transform: uppercase;
                     letter-spacing: 1px;
                 }
                 
                 .quote-container.style-elegant .quote-source {
                     font-size: 0.85rem;
                     color: #666666;
                     font-style: italic;
                     margin-top: 0.25rem;
                 }
                 
                 /* Style moderne */
                 .quote-container.style-modern {
                     background: linear-gradient(135deg, #FFD700 0%, #FF0000 100%);
                     color: white;
                     border: none;
                     position: relative;
                     overflow: hidden;
                 }
                 
                 .quote-container.style-modern::before {
                     content: '';
                     position: absolute;
                     top: 0;
                     left: 0;
                     right: 0;
                     bottom: 0;
                     background: rgba(0, 0, 0, 0.1);
                     z-index: 1;
                 }
                 
                 .quote-container.style-modern .quote-text,
                 .quote-container.style-modern .quote-author,
                 .quote-container.style-modern .quote-source {
                     position: relative;
                     z-index: 2;
                 }
                 
                 .quote-container.style-modern .quote-text {
                     font-size: 1.1rem;
                     color: white;
                     margin-bottom: 1rem;
                     line-height: 1.6;
                     font-weight: 500;
                 }
                 
                 .quote-container.style-modern .quote-author {
                     font-weight: 600;
                     color: rgba(255, 255, 255, 0.9);
                     font-size: 0.95rem;
                 }
                 
                 .quote-container.style-modern .quote-source {
                     font-size: 0.85rem;
                     color: rgba(255, 255, 255, 0.7);
                     font-style: italic;
                     margin-top: 0.25rem;
                 }
                 
                 /* Alignement des citations */
                 .quote-container.align-left {
                     text-align: left;
                 }
                 
                 .quote-container.align-center {
                     text-align: center;
                 }
                 
                 .quote-container.align-right {
                     text-align: right;
                 }
                 
                 /* Tailles des citations */
                 .quote-container.size-small .quote-text {
                     font-size: 0.9rem;
                 }
                 
                 .quote-container.size-medium .quote-text {
                     font-size: 1.1rem;
                 }
                 
                 .quote-container.size-large .quote-text {
                     font-size: 1.3rem;
                 }
                     
                     .content-module p:last-child {
                        margin-bottom: 0;
                    }
                    
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
                    
                    @media (max-width: 768px) {
                        .content-columns[data-columns="2"],
                        .content-columns[data-columns="3"] {
                            grid-template-columns: 1fr;
                            gap: 1rem;
                        }
                    }

                    /* Styles pour le module Galerie */
                    .gallery-container {
                        display: grid;
                        gap: 16px;
                        margin: 20px 0;
                    }
                    
                    .gallery-container.layout-grid {
                        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                    }
                    
                    .gallery-container.layout-grid.columns-2 {
                        grid-template-columns: repeat(2, 1fr);
                    }
                    
                    .gallery-container.layout-grid.columns-3 {
                        grid-template-columns: repeat(3, 1fr);
                    }
                    
                    .gallery-container.layout-grid.columns-4 {
                        grid-template-columns: repeat(4, 1fr);
                    }
                    
                    .gallery-container.layout-grid.columns-5 {
                        grid-template-columns: repeat(5, 1fr);
                    }
                    
                    .gallery-container.layout-masonry {
                        columns: 3;
                        column-gap: 16px;
                    }
                    
                    .gallery-container.layout-masonry .gallery-item {
                        break-inside: avoid;
                        margin-bottom: 16px;
                    }
                    
                    .gallery-container.layout-carousel,
                    .gallery-container.layout-slider {
                        display: flex;
                        overflow-x: auto;
                        scroll-snap-type: x mandatory;
                        gap: 16px;
                        padding: 8px 0;
                    }
                    
                    .gallery-container.layout-carousel .gallery-item,
                    .gallery-container.layout-slider .gallery-item {
                        flex: 0 0 300px;
                        scroll-snap-align: start;
                    }
                    
                    .gallery-container.spacing-small {
                        gap: 8px;
                    }
                    
                    .gallery-container.spacing-medium {
                        gap: 16px;
                    }
                    
                    .gallery-container.spacing-large {
                        gap: 24px;
                    }
                    
                    .gallery-item {
                        position: relative;
                        border-radius: 8px;
                        overflow: hidden;
                        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
                        transition: transform 0.3s ease;
                    }
                    
                    .gallery-item:hover {
                        transform: translateY(-2px);
                        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
                    }
                    
                    .gallery-image {
                        position: relative;
                        width: 100%;
                        height: 200px;
                        overflow: hidden;
                    }
                    
                    .gallery-image img {
                        width: 100%;
                        height: 100%;
                        object-fit: cover;
                        transition: transform 0.3s;
                    }
                    
                    .gallery-item:hover .gallery-image img {
                        transform: scale(1.05);
                    }
                    
                    .lightbox-overlay {
                        position: absolute;
                        top: 0;
                        left: 0;
                        right: 0;
                        bottom: 0;
                        background: rgba(0, 0, 0, 0.5);
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        opacity: 0;
                        transition: opacity 0.2s;
                        cursor: pointer;
                        font-size: 24px;
                        color: white;
                    }
                    
                    .gallery-item:hover .lightbox-overlay {
                        opacity: 1;
                    }
                    
                    .gallery-caption {
                        padding: 12px;
                        background: white;
                    }
                    
                    .gallery-container.captions-overlay .gallery-caption {
                        position: absolute;
                        bottom: 0;
                        left: 0;
                        right: 0;
                        background: rgba(0, 0, 0, 0.8);
                        color: white;
                        padding: 12px;
                    }
                    
                    .gallery-container.captions-none .gallery-caption {
                        display: none;
                    }
                    
                    .image-title {
                        font-weight: 600;
                        margin-bottom: 4px;
                        font-size: 14px;
                    }
                    
                    .image-description {
                        font-size: 12px;
                        color: #6c757d;
                        line-height: 1.4;
                    }
                    
                    .gallery-container.captions-overlay .image-description {
                        color: #ccc;
                    }

                    /* Styles pour le Carousel */
                    .gallery-carousel {
                        position: relative;
                        width: 100%;
                        overflow: hidden;
                        border-radius: 8px;
                        margin: 20px 0;
                    }

                    .carousel-container {
                        position: relative;
                        width: 100%;
                        overflow: hidden;
                    }

                    .carousel-track {
                        display: flex;
                        transition: transform 0.5s ease-in-out;
                        width: 100%;
                    }

                    .carousel-slide {
                        flex: 0 0 100%;
                        width: 100%;
                    }

                    .carousel-controls {
                        position: absolute;
                        bottom: 20px;
                        left: 0;
                        right: 0;
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        gap: 20px;
                        z-index: 10;
                    }

                    .carousel-btn {
                        background: rgba(0, 0, 0, 0.7);
                        border: none;
                        color: white;
                        padding: 12px;
                        border-radius: 50%;
                        cursor: pointer;
                        transition: all 0.3s ease;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        width: 44px;
                        height: 44px;
                    }

                    .carousel-btn:hover {
                        background: rgba(0, 0, 0, 0.9);
                        transform: scale(1.1);
                    }

                    .carousel-indicators {
                        display: flex;
                        gap: 8px;
                    }

                    .carousel-indicator {
                        background: rgba(255, 255, 255, 0.3);
                        border: none;
                        border-radius: 50%;
                        width: 12px;
                        height: 12px;
                        cursor: pointer;
                        transition: all 0.3s ease;
                    }

                    .carousel-indicator:hover {
                        background: rgba(255, 255, 255, 0.5);
                    }

                    .carousel-indicator.active {
                        background: #ffd700;
                    }

                    .indicator-dot {
                        display: block;
                        width: 100%;
                        height: 100%;
                        border-radius: 50%;
                    }

                    /* Styles pour le Masonry */
                    .gallery-masonry {
                        width: 100%;
                        margin: 20px 0;
                    }

                    .masonry-grid {
                        position: relative;
                        width: 100%;
                    }

                    .masonry-item {
                        position: absolute;
                        transition: all 0.3s ease;
                        border-radius: 8px;
                        overflow: hidden;
                        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
                    }

                    .masonry-item:hover {
                        transform: translateY(-2px);
                        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
                    }

                    .masonry-item img {
                        width: 100%;
                        height: auto;
                        display: block;
                    }

                    /* Styles pour le Slider */
                    .gallery-slider {
                        position: relative;
                        width: 100%;
                        overflow: hidden;
                        border-radius: 12px;
                        margin: 20px 0;
                        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
                        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
                    }

                    .slider-container {
                        position: relative;
                        width: 100%;
                        overflow: hidden;
                    }

                    .slider-track {
                        display: flex;
                        transition: transform 0.8s cubic-bezier(0.4, 0, 0.2, 1);
                        width: 100%;
                    }

                    .slider-slide {
                        flex: 0 0 100%;
                        width: 100%;
                        position: relative;
                    }

                    .slider-slide::before {
                        content: '';
                        position: absolute;
                        top: 0;
                        left: 0;
                        right: 0;
                        bottom: 0;
                        background: linear-gradient(180deg, transparent 0%, rgba(0, 0, 0, 0.3) 100%);
                        z-index: 1;
                        pointer-events: none;
                    }

                    .slider-controls {
                        position: absolute;
                        bottom: 30px;
                        left: 0;
                        right: 0;
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                        padding: 0 30px;
                        z-index: 10;
                    }

                    .slider-btn {
                        background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%);
                        border: 2px solid rgba(255, 255, 255, 0.3);
                        color: #dc3545;
                        padding: 14px;
                        border-radius: 50%;
                        cursor: pointer;
                        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        width: 48px;
                        height: 48px;
                        font-weight: bold;
                        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
                    }

                    .slider-btn:hover {
                        background: linear-gradient(135deg, #ffed4e 0%, #ffd700 100%);
                        transform: scale(1.15) translateY(-2px);
                        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
                    }

                    .slider-btn:active {
                        transform: scale(0.95);
                    }

                    .slider-counter {
                        background: linear-gradient(135deg, rgba(0, 0, 0, 0.8) 0%, rgba(0, 0, 0, 0.6) 100%);
                        color: white;
                        padding: 12px 20px;
                        border-radius: 25px;
                        font-size: 16px;
                        font-weight: 700;
                        backdrop-filter: blur(10px);
                        border: 1px solid rgba(255, 255, 255, 0.1);
                        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
                    }

                    .current-slide {
                        color: #ffd700;
                        font-weight: 800;
                    }

                    /* Styles pour les boutons de layout de la galerie */
                    .gallery-layout-btn {
                        background: #f8f9fa;
                        border: 1px solid #dee2e6;
                        padding: 8px 12px;
                        border-radius: 4px;
                        cursor: pointer;
                        font-size: 12px;
                        display: flex;
                        align-items: center;
                        gap: 4px;
                        transition: all 0.3s ease;
                    }

                    .gallery-layout-btn:hover {
                        background: #dc3545;
                        color: white;
                        border-color: #dc3545;
                    }

                                         .gallery-layout-btn.active {
                         background: #ffc107;
                         color: #212529;
                         border-color: #ffc107;
                         font-weight: 700;
                     }

                     /* Styles pour les titres dans l'aperçu */
                     .heading-text {
                         margin: 20px 0;
                         line-height: 1.2;
                     }

                     .heading-default {
                         font-weight: 700;
                     }

                     .heading-modern {
                         font-weight: 600;
                         letter-spacing: -0.5px;
                         text-transform: uppercase;
                     }

                     .heading-elegant {
                         font-weight: 400;
                         font-family: 'Georgia', serif;
                         font-style: italic;
                     }

                     .heading-minimal {
                         font-weight: 300;
                         letter-spacing: 1px;
                     }

                     .heading-small {
                         font-size: 1.2rem;
                     }

                     .heading-medium {
                         font-size: 1.5rem;
                     }

                     .heading-large {
                         font-size: 2rem;
                     }

                     .heading-left {
                         text-align: left;
                     }

                     .heading-center {
                         text-align: center;
                     }

                     .heading-right {
                         text-align: right;
                     }

                     /* Styles pour le module Liste */
                     .list-container {
                         margin: 0;
                         padding: 0;
                         list-style: none;
                     }

                     .list-item {
                         padding: 5px 0;
                         transition: all 0.3s ease;
                         border-radius: 3px;
                     }

                     /* Indentation des listes */
                     .list-indent-0 { margin-left: 0; }
                     .list-indent-1 { margin-left: 20px; }
                     .list-indent-2 { margin-left: 40px; }
                     .list-indent-3 { margin-left: 60px; }

                     /* Styles de liste */
                     .list-default {
                         font-family: 'Arial', sans-serif;
                     }

                     .list-modern {
                         font-family: 'Helvetica', sans-serif;
                         font-weight: 300;
                     }

                     .list-elegant {
                         font-family: 'Georgia', serif;
                         font-style: italic;
                     }

                     .list-minimal {
                         font-family: 'Courier New', monospace;
                         font-size: 14px;
                     }

                     /* Espacement des listes */
                     .list-compact .list-item {
                         padding: 2px 0;
                         line-height: 1.2;
                     }

                     .list-normal .list-item {
                         padding: 5px 0;
                         line-height: 1.4;
                     }

                     .list-spacious .list-item {
                         padding: 8px 0;
                         line-height: 1.6;
                     }

                     /* Styles de puces */
                     .list-disc .list-item::before {
                         content: "●";
                         color: #007bff;
                         font-weight: bold;
                         margin-right: 8px;
                     }

                     .list-circle .list-item::before {
                         content: "○";
                         color: #6c757d;
                         margin-right: 8px;
                     }

                     .list-square .list-item::before {
                         content: "■";
                         color: #495057;
                         margin-right: 8px;
                     }

                     .list-decimal .list-item {
                         counter-increment: list-counter;
                     }

                     .list-decimal .list-item::before {
                         content: counter(list-counter) ".";
                         color: #007bff;
                         font-weight: bold;
                         margin-right: 8px;
                     }

                     .list-lower-alpha .list-item {
                         counter-increment: alpha-counter;
                     }

                     .list-lower-alpha .list-item::before {
                         content: counter(alpha-counter, lower-alpha) ".";
                         color: #007bff;
                         font-weight: bold;
                         margin-right: 8px;
                     }

                     .list-upper-alpha .list-item {
                         counter-increment: alpha-counter;
                     }

                     .list-upper-alpha .list-item::before {
                         content: counter(alpha-counter, upper-alpha) ".";
                         color: #007bff;
                         font-weight: bold;
                         margin-right: 8px;
                     }

                     /* Nouveaux styles de puces */
                     .list-arrow .list-item::before {
                         content: "→";
                         color: #007bff;
                         font-weight: bold;
                         margin-right: 8px;
                     }

                     .list-star .list-item::before {
                         content: "★";
                         color: #FFD700;
                         font-weight: bold;
                         margin-right: 8px;
                     }

                     .list-check .list-item::before {
                         content: "✓";
                         color: #28a745;
                         font-weight: bold;
                         margin-right: 8px;
                     }

                     .list-dash .list-item::before {
                         content: "—";
                         color: #6c757d;
                         font-weight: bold;
                         margin-right: 8px;
                     }

                     .list-plus .list-item::before {
                         content: "+";
                         color: #007bff;
                         font-weight: bold;
                         margin-right: 8px;
                     }

                     .list-bullet .list-item::before {
                         content: "•";
                         color: #495057;
                         font-weight: bold;
                         margin-right: 8px;
                     }

                     /* Alignement du texte */
                     .list-align-left .list-item {
                         text-align: left;
                     }

                     .list-align-center .list-item {
                         text-align: center;
                     }

                     .list-align-right .list-item {
                         text-align: right;
                     }

                     /* Styles pour le module Séparateur */
                     .separator-container {
                         width: 100%;
                         display: flex;
                         align-items: center;
                     }

                     .separator-align-left {
                         justify-content: flex-start;
                     }

                     .separator-align-center {
                         justify-content: center;
                     }

                     .separator-align-right {
                         justify-content: flex-end;
                     }

                     .separator {
                         border: none;
                         background: transparent;
                         height: 2px;
                         margin: 0;
                         padding: 0;
                     }

                     /* Styles spécifiques pour chaque type de séparateur */
                     .separator.line {
                         background: #cccccc;
                         height: 2px;
                     }

                     .separator.double {
                         background: linear-gradient(to bottom, #cccccc 0%, #cccccc 40%, transparent 40%, transparent 60%, #cccccc 60%, #cccccc 100%);
                         height: 4px;
                     }

                     .separator.dotted {
                         background: repeating-linear-gradient(to right, #cccccc 0px, #cccccc 4px, transparent 4px, transparent 8px);
                         height: 2px;
                     }

                     .separator.dashed {
                         background: repeating-linear-gradient(to right, #cccccc 0px, #cccccc 8px, transparent 8px, transparent 16px);
                         height: 2px;
                     }

                     .separator.gradient {
                         background: linear-gradient(to right, transparent, #cccccc, transparent);
                         height: 2px;
                     }

                     .separator.decorative {
                         background: none;
                         height: auto;
                         text-align: center;
                         font-size: 16px;
                         line-height: 1;
                         color: #cccccc;
                     }

                     /* Styles décoratifs spécifiques */
                     .separator.decorative.stars::before {
                         content: "⭐ ⭐ ⭐";
                         letter-spacing: 10px;
                     }

                     .separator.decorative.arrows::before {
                         content: "➡️ ➡️ ➡️";
                         letter-spacing: 8px;
                     }

                     .separator.decorative.dots::before {
                         content: "● ● ●";
                         letter-spacing: 12px;
                     }

                     .separator.decorative.flowers::before {
                         content: "🌸 🌸 🌸";
                         letter-spacing: 8px;
                     }

                     .separator.decorative.geometric::before {
                         content: "🔷 🔷 🔷";
                         letter-spacing: 8px;
                     }

                     /* Styles pour le module Tableau */
                     .table {
                         width: 100%;
                         border-collapse: collapse;
                         margin: 0;
                     }

                     .table th,
                     .table td {
                         padding: 8px;
                         text-align: left;
                         border: 1px solid #dee2e6;
                     }

                     .table th {
                         background-color: #f8f9fa;
                         font-weight: 600;
                     }

                     /* Styles de tableau */
                     .table-default th,
                     .table-default td {
                         border: 1px solid #dee2e6;
                     }

                     .table-striped tbody tr:nth-child(odd) {
                         background-color: #f8f9fa;
                     }

                     .table-bordered th,
                     .table-bordered td {
                         border: 2px solid #dee2e6;
                     }

                     .table-compact th,
                     .table-compact td {
                         padding: 4px 6px;
                         font-size: 14px;
                     }

                     .table-modern {
                         border-radius: 8px;
                         overflow: hidden;
                         box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
                     }

                     .table-modern th {
                         background: linear-gradient(135deg, #dc3545 0%, #6c757d 100%);
                         color: white;
                         border: none;
                     }

                     .table-modern td {
                         border: none;
                         border-bottom: 1px solid #eee;
                     }

                     /* Alignement des tableaux */
                     .table-align-left {
                         text-align: left;
                     }

                     .table-align-center {
                         text-align: center;
                     }

                     .table-align-right {
                         text-align: right;
                     }

                     /* Styles pour le module Bouton */
                     .button-container {
                         display: flex;
                         align-items: center;
                     }

                     .button-align-left {
                         justify-content: flex-start;
                     }

                     .button-align-center {
                         justify-content: center;
                     }

                     .button-align-right {
                         justify-content: flex-end;
                     }

                     .button-full-width .content-module-button {
                         width: 100%;
                     }

                     .content-module-button {
                         display: inline-flex;
                         align-items: center;
                         gap: 8px;
                         padding: 10px 20px;
                         border: 2px solid transparent;
                         border-radius: 6px;
                         text-decoration: none;
                         font-weight: 500;
                         text-align: center;
                         cursor: pointer;
                         transition: all 0.3s ease;
                         font-size: 14px;
                         line-height: 1.4;
                     }

                     .content-module-button:hover {
                         transform: translateY(-2px);
                         box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                     }

                     /* Styles de boutons */
                     .btn-primary {
                         background-color: #007bff;
                         color: white;
                         border-color: #007bff;
                     }

                     .btn-primary:hover {
                         background-color: #0056b3;
                         border-color: #0056b3;
                     }

                     .btn-secondary {
                         background-color: #6c757d;
                         color: white;
                         border-color: #6c757d;
                     }

                     .btn-secondary:hover {
                         background-color: #545b62;
                         border-color: #545b62;
                     }

                     .btn-success {
                         background-color: #28a745;
                         color: white;
                         border-color: #28a745;
                     }

                     .btn-success:hover {
                         background-color: #1e7e34;
                         border-color: #1e7e34;
                     }

                     .btn-danger {
                         background-color: #dc3545;
                         color: white;
                         border-color: #dc3545;
                     }

                     .btn-danger:hover {
                         background-color: #c82333;
                         border-color: #c82333;
                     }

                     .btn-warning {
                         background-color: #ffc107;
                         color: #212529;
                         border-color: #ffc107;
                     }

                     .btn-warning:hover {
                         background-color: #e0a800;
                         border-color: #e0a800;
                     }

                     .btn-info {
                         background-color: #17a2b8;
                         color: white;
                         border-color: #17a2b8;
                     }

                     .btn-info:hover {
                         background-color: #138496;
                         border-color: #138496;
                     }

                     .btn-light {
                         background-color: #f8f9fa;
                         color: #212529;
                         border-color: #f8f9fa;
                     }

                     .btn-light:hover {
                         background-color: #e2e6ea;
                         border-color: #e2e6ea;
                     }

                     .btn-dark {
                         background-color: #343a40;
                         color: white;
                         border-color: #343a40;
                     }

                     .btn-dark:hover {
                         background-color: #23272b;
                         border-color: #23272b;
                     }

                     /* Boutons outline */
                     .btn-outline-primary {
                         background-color: transparent;
                         color: #007bff;
                         border-color: #007bff;
                     }

                     .btn-outline-primary:hover {
                         background-color: #007bff;
                         color: white;
                     }

                     .btn-outline-secondary {
                         background-color: transparent;
                         color: #6c757d;
                         border-color: #6c757d;
                     }

                     .btn-outline-secondary:hover {
                         background-color: #6c757d;
                         color: white;
                     }

                     /* Tailles de boutons */
                     .btn-small {
                         padding: 6px 12px;
                         font-size: 12px;
                     }

                     .btn-medium {
                         padding: 10px 20px;
                         font-size: 14px;
                     }

                     .btn-large {
                         padding: 14px 28px;
                         font-size: 16px;
                     }

                     /* Effets de boutons */
                     .btn-rounded {
                         border-radius: 25px;
                     }

                     .btn-shadow {
                         box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                     }

                     .btn-shadow:hover {
                         box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
                     }

                     /* Animations de boutons */
                     .btn-animation-pulse {
                         animation: pulse 2s infinite;
                     }

                     @keyframes pulse {
                         0% { transform: scale(1); }
                         50% { transform: scale(1.05); }
                         100% { transform: scale(1); }
                     }

                     .btn-animation-bounce {
                         animation: bounce 2s infinite;
                     }

                     @keyframes bounce {
                         0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
                         40% { transform: translateY(-10px); }
                         60% { transform: translateY(-5px); }
                     }

                     .btn-animation-shake {
                         animation: shake 0.5s ease-in-out;
                     }

                     @keyframes shake {
                         0%, 100% { transform: translateX(0); }
                         25% { transform: translateX(-5px); }
                         75% { transform: translateX(5px); }
                     }

                     .btn-animation-glow {
                         animation: glow 2s ease-in-out infinite alternate;
                     }

                     @keyframes glow {
                         from { box-shadow: 0 0 5px currentColor; }
                         to { box-shadow: 0 0 20px currentColor, 0 0 30px currentColor; }
                     }
                 </style>
            </head>
            <body>
                <h1>Aperçu de l'article</h1>
                ${content}
                
                <!-- Lightbox Modal -->
                <div id="lightbox-modal" class="lightbox-modal">
                    <div class="lightbox-content">
                        <button class="lightbox-close" onclick="closeLightbox()">✕</button>
                        <img id="lightbox-image" class="lightbox-image" src="" alt="">
                        <div id="lightbox-caption" class="lightbox-caption">
                            <h3 id="lightbox-title"></h3>
                            <p id="lightbox-description"></p>
                        </div>
                    </div>
                </div>
                
                <script>
                    // Lightbox functionality
                    function openLightbox(imageSrc, title, description) {
                        const modal = document.getElementById('lightbox-modal');
                        const image = document.getElementById('lightbox-image');
                        const captionTitle = document.getElementById('lightbox-title');
                        const captionDescription = document.getElementById('lightbox-description');
                        const caption = document.getElementById('lightbox-caption');
                        
                        image.src = imageSrc;
                        image.alt = title || '';
                        
                        if (title || description) {
                            captionTitle.textContent = title || '';
                            captionDescription.textContent = description || '';
                            caption.style.display = 'block';
                        } else {
                            caption.style.display = 'none';
                        }
                        
                        modal.style.display = 'flex';
                        document.body.style.overflow = 'hidden';
                    }
                    
                    function closeLightbox() {
                        const modal = document.getElementById('lightbox-modal');
                        modal.style.display = 'none';
                        document.body.style.overflow = 'auto';
                    }
                    
                    // Close lightbox on background click
                    document.getElementById('lightbox-modal').addEventListener('click', function(e) {
                        if (e.target === this) {
                            closeLightbox();
                        }
                    });
                    
                    // Close lightbox on Escape key
                    document.addEventListener('keydown', function(e) {
                        if (e.key === 'Escape') {
                            closeLightbox();
                        }
                    });
                    
                                         // Add click events to gallery images with lightbox overlay
                     function initLightbox() {
                         const galleryItems = document.querySelectorAll('.gallery-item');
                         galleryItems.forEach(function(item) {
                             const lightboxOverlay = item.querySelector('.lightbox-overlay');
                             if (lightboxOverlay) {
                                 lightboxOverlay.addEventListener('click', function(e) {
                                     e.preventDefault();
                                     e.stopPropagation();
                                     
                                     const image = item.querySelector('img');
                                     const title = item.querySelector('.image-title');
                                     const description = item.querySelector('.image-description');
                                     
                                     if (image) {
                                         openLightbox(
                                             image.src,
                                             title ? title.textContent : '',
                                             description ? description.textContent : ''
                                         );
                                     }
                                 });
                             }
                         });
                     }
                     
                     // Initialize lightbox with a small delay to ensure DOM is ready
                     setTimeout(initLightbox, 100);
                     document.addEventListener('DOMContentLoaded', initLightbox);
                     
                     // Initialize sliders
                     function initSliders() {
                         console.log('🔧 Initialisation des sliders dans l\'aperçu...');
                         const sliders = document.querySelectorAll('.gallery-slider');
                         console.log('🔍 Sliders trouvés dans l\'aperçu:', sliders.length);
                         
                         sliders.forEach((slider, index) => {
                             console.log(\`🔧 Initialisation du slider \${index + 1} dans l'aperçu\`);
                             
                             const track = slider.querySelector('.slider-track');
                             const slides = slider.querySelectorAll('.slider-slide');
                             const prevBtn = slider.querySelector('.slider-prev');
                             const nextBtn = slider.querySelector('.slider-next');
                             const counter = slider.querySelector('.slider-counter');
                             const currentSlideSpan = counter?.querySelector('.current-slide');
                             
                             console.log('🔍 Éléments slider trouvés:', {
                                 track: !!track,
                                 slides: slides.length,
                                 prevBtn: !!prevBtn,
                                 nextBtn: !!nextBtn,
                                 counter: !!counter
                             });
                             
                             if (!track || slides.length <= 1) {
                                 console.log('⏭️ Pas assez de slides pour le slider:', slides.length);
                                 return;
                             }
                             
                             let currentIndex = 0;
                             const totalSlides = slides.length;
                             
                             // Fonction pour afficher une slide
                             const showSlide = (index) => {
                                 console.log('🎯 showSlide appelée avec index:', index, 'totalSlides:', totalSlides);
                                 
                                 if (index < 0) index = totalSlides - 1;
                                 if (index >= totalSlides) index = 0;
                                 
                                 currentIndex = index;
                                 
                                 // Mettre à jour la position du track
                                 const transform = \`translateX(-\${index * 100}%)\`;
                                 track.style.transform = transform;
                                 console.log('🎯 Transform appliqué:', transform);
                                 
                                 // Mettre à jour le compteur
                                 if (currentSlideSpan) {
                                     currentSlideSpan.textContent = index + 1;
                                     console.log('🎯 Compteur mis à jour:', index + 1);
                                 }
                             };
                             
                             // Événements des boutons
                             if (prevBtn) {
                                 console.log('🔧 Ajout de l\'événement click sur prevBtn');
                                 prevBtn.addEventListener('click', (e) => {
                                     e.preventDefault();
                                     console.log('👆 Clic sur bouton précédent, index actuel:', currentIndex);
                                     showSlide(currentIndex - 1);
                                 });
                             }
                             
                             if (nextBtn) {
                                 console.log('🔧 Ajout de l\'événement click sur nextBtn');
                                 nextBtn.addEventListener('click', (e) => {
                                     e.preventDefault();
                                     console.log('👆 Clic sur bouton suivant, index actuel:', currentIndex);
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
                             
                             // Initialiser à la première slide
                             showSlide(0);
                             
                             console.log('✅ Slider initialisé dans l\'aperçu:', slider);
                         });
                     }
                     
                     // Initialize sliders with a small delay to ensure DOM is ready
                     setTimeout(initSliders, 100);
                     document.addEventListener('DOMContentLoaded', initSliders);
                </script>
            </body>
            </html>
        `);
    }
    
    getContent() {
        let content = '';
        const sections = this.contentSections.querySelectorAll('.content-section');
        
        sections.forEach(section => {
            const columnCount = section.dataset.columns;
            const columns = section.querySelectorAll('.content-column');
            
            if (columns.length > 0) {
                let sectionContent = '<div class="content-columns" data-columns="' + columnCount + '">';
                
                columns.forEach(column => {
                    sectionContent += '<div class="content-column">';
                    const modules = column.querySelectorAll('.content-module');
                    modules.forEach(module => {
                        const moduleId = module.dataset.moduleId;
                        const moduleInstance = this.modules.get(moduleId);
                        
                        if (moduleInstance && typeof moduleInstance.getContent === 'function') {
                            // Utiliser directement le contenu du module qui inclut déjà les données
                            const moduleContent = moduleInstance.getContent();
                            sectionContent += `<div class="content-module content-module-${moduleInstance.type}" data-module-id="${moduleId}" data-module-type="${moduleInstance.type}">${moduleContent}</div>`;
                        } else {
                            // Fallback pour les modules sans instance
                            const moduleContent = module.querySelector('.module-content').innerHTML;
                            const moduleType = module.dataset.type;
                            const moduleData = module.getAttribute('data-module-data');
                            
                            // Préserver les données du module si elles existent
                            let moduleAttributes = `class="content-module content-module-${moduleType}" data-module-id="${moduleId}" data-module-type="${moduleType}"`;
                            if (moduleData) {
                                moduleAttributes += ` data-module-data='${moduleData}'`;
                            }
                            
                            sectionContent += `<div ${moduleAttributes}>${moduleContent}</div>`;
                        }
                    });
                    sectionContent += '</div>';
                });
                
                sectionContent += '</div>';
                content += `<div class="content-section" data-columns="${columnCount}">${sectionContent}</div>`;
            }
        });
        
        return content;
    }
    
    save() {
        // S'assurer que tous les modules sauvegardent leurs données avant la sauvegarde globale
        this.modules.forEach(module => {
            if (typeof module.saveData === 'function') {
                module.saveData();
            }
        });
        
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
        // Restaurer le scroll du body
        document.body.style.overflow = '';
        this.modal.remove();
    }
    
    open() {
        console.log('🔓 Ouverture de l\'éditeur...');
        console.log('Modal:', this.modal);
        console.log('ContentSections:', this.contentSections);
        
        if (!this.modal) {
            console.error('❌ Modal non trouvé');
            return;
        }
        
        this.modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        
        console.log('✅ Modal affiché');
        
        // Créer une première section par défaut si aucune n'existe
        if (this.getSectionCount() === 0) {
            console.log('📝 Création de la première section...');
            this.addSection(1);
        }
        
        // Afficher le message par défaut dans les options
        this.hideOptions();
        
        console.log('🎉 Éditeur ouvert avec succès');
    }

    bindColumnDragEvents(column) {
        column.addEventListener('dragover', (e) => {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'copy';
            
            // Ajouter un indicateur visuel
            if (!column.classList.contains('drop-zone')) {
                column.classList.add('drop-zone');
            }
        });
        
        column.addEventListener('dragleave', (e) => {
            // Vérifier si on quitte vraiment la colonne
            if (!column.contains(e.relatedTarget)) {
                column.classList.remove('drop-zone');
            }
        });
        
        column.addEventListener('drop', (e) => {
            e.preventDefault();
            column.classList.remove('drop-zone');
            
            console.log('🎯 Drop détecté sur la colonne');
            console.log('DataTransfer types:', e.dataTransfer.types);
            console.log('module-id:', e.dataTransfer.getData('module-id'));
            console.log('text/plain:', e.dataTransfer.getData('text/plain'));
            
            // Vérifier d'abord si c'est un déplacement de module existant
            const draggedModuleId = e.dataTransfer.getData('module-id');
            if (draggedModuleId) {
                console.log('🔄 Déplacement de module existant:', draggedModuleId);
                const draggedModule = this.getModuleById(draggedModuleId);
                if (draggedModule) {
                    // Déplacer le module vers cette colonne
                    column.appendChild(draggedModule.element);
                    
                    // Supprimer le placeholder si il existe
                    const placeholder = column.querySelector('.column-placeholder');
                    if (placeholder) {
                        placeholder.remove();
                    }
                    
                    // Nettoyer les colonnes vides
                    this.cleanupEmptyColumns();
                    e._dropHandled = true; // Marquer comme traité pour éviter la duplication
                    return;
                }
            }
            
            // Sinon, c'est la création d'un nouveau module depuis la barre latérale
            const moduleType = e.dataTransfer.getData('text/plain');
            if (moduleType) {
                console.log('🎯 Création d\'un nouveau module', moduleType, 'dans la colonne');
                this.addModule(moduleType, column);
                e._dropHandled = true; // Marquer comme traité pour éviter la duplication
            } else {
                console.log('❌ Aucun type de module trouvé dans le drop');
            }
        });
    }

    cleanupEmptyColumns() {
        const columns = document.querySelectorAll('.content-column');
        columns.forEach(column => {
            const modules = column.querySelectorAll('.content-module');
            if (modules.length === 0) {
                column.innerHTML = `
                    <div class="column-placeholder">
                        <div class="placeholder-icon">📝</div>
                        <div class="placeholder-text">Cliquez sur un module pour commencer</div>
                    </div>
                `;
            }
        });
    }

    loadInitialContent() {
        if (!this.options.initialContent) {
            console.log('📂 Aucun contenu initial à charger');
            return;
        }

        console.log('📂 Début du chargement du contenu initial...');
        console.log('📄 Contenu HTML à parser:', this.options.initialContent);
        
        // Créer un élément temporaire pour parser le HTML
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = this.options.initialContent;
        
        // Chercher toutes les sections
        const sections = tempDiv.querySelectorAll('.content-section');
        console.log(`📋 ${sections.length} sections trouvées dans le contenu initial`);
        
        // Vider les sections existantes
        this.contentSections.innerHTML = '';
        
        sections.forEach((sectionElement, sectionIndex) => {
            console.log(`\n🔍 === PARSING SECTION ${sectionIndex + 1} ===`);
            console.log('📄 HTML de la section:', sectionElement.outerHTML);
            
            // Obtenir le nombre de colonnes
            const columnsCount = parseInt(sectionElement.getAttribute('data-columns')) || 1;
            console.log('📊 Nombre de colonnes trouvées:', columnsCount);
            
            // Créer la section dans l'éditeur
            const newSection = this.createSection(columnsCount);
            console.log('✅ Section créée dans l\'éditeur:', newSection);
            
            // Ajouter la section au DOM de l'éditeur
            this.contentSections.appendChild(newSection);
            console.log('✅ Section ajoutée au DOM de l\'éditeur');
            
            // Ajouter la section à la Map this.sections
            this.sections.set(newSection.dataset.sectionId, newSection);
            console.log('✅ Section ajoutée à la Map this.sections');
            
            // Parser les modules dans cette section
            this.parseModulesInSection(sectionElement, newSection);
        });
        
        console.log('✅ Contenu initial chargé avec succès');
    }

    parseModulesInSection(sectionElement, section) {
        console.log('🔍 Parsing des modules dans la section...');
        console.log('📄 Section source:', sectionElement);
        console.log('📄 Section cible:', section);
        
        // Pour les sections multi-colonnes, chercher dans chaque colonne
        const columns = sectionElement.querySelectorAll('.content-column');
        const columnsCount = columns.length;
        console.log(`📊 Nombre de colonnes trouvées dans la section source: ${columnsCount}`);
        
        if (columnsCount > 1) {
            console.log(`📋 Section multi-colonnes détectée avec ${columnsCount} colonnes`);
            columns.forEach((col, index) => {
                console.log(`\n🔍 --- PARSING COLONNE ${index + 1} ---`);
                console.log('📄 HTML de la colonne:', col.outerHTML);
                const targetColumn = section.querySelectorAll('.content-column')[index];
                console.log('📄 Colonne cible:', targetColumn);
                this.parseModulesInColumn(col, targetColumn);
            });
        } else {
            // Section à une seule colonne, chercher directement
            console.log('📋 Section à une seule colonne détectée');
            const targetColumn = section.querySelector('.content-column');
            console.log('📄 Colonne cible:', targetColumn);
            this.parseModulesInColumn(sectionElement, targetColumn);
        }
    }

    parseModulesInColumn(sourceColumn, targetColumn) {
        console.log('🔍 Parsing des modules dans la colonne...');
        console.log('📄 Élément colonne source:', sourceColumn);
        console.log('📄 Élément colonne cible:', targetColumn);
        
        // Chercher d'abord les modules avec les classes content-module-*
        const contentModules = sourceColumn.querySelectorAll('[class*="content-module-"]');
        console.log(`📦 ${contentModules.length} modules content-module-* trouvés dans la colonne`);
        
        contentModules.forEach((moduleElement, moduleIndex) => {
            const className = moduleElement.className;
            console.log(`\n🔍 --- MODULE ${moduleIndex + 1} ---`);
            console.log('📄 Classe du module:', className);
            console.log('📄 HTML du module:', moduleElement.outerHTML);
            
            if (className.includes('content-module-text')) {
                console.log('📝 Module texte trouvé, recréation...');
                this.recreateTextModuleFromContent(moduleElement, targetColumn);
            } else if (className.includes('content-module-image')) {
                console.log('🖼️ Module image trouvé, recréation...');
                this.recreateImageModuleFromContent(moduleElement, targetColumn);
            } else if (className.includes('content-module-video')) {
                console.log('🎬 Module vidéo trouvé, recréation...');
                this.recreateVideoModuleFromContent(moduleElement, targetColumn);
            } else if (className.includes('content-module-separator')) {
                console.log('➖ Module séparateur trouvé, recréation...');
                this.recreateSeparatorModuleFromContent(moduleElement, targetColumn);
            } else if (className.includes('content-module-heading')) {
                console.log('📋 Module titre trouvé, recréation...');
                this.recreateHeadingModuleFromContent(moduleElement, targetColumn);
            } else if (className.includes('content-module-quote')) {
                console.log('💬 Module citation trouvé, recréation...');
                this.recreateQuoteModuleFromContent(moduleElement, targetColumn);
            } else if (className.includes('content-module-button')) {
                console.log('🔘 Module bouton trouvé, recréation...');
                this.recreateButtonModuleFromContent(moduleElement, targetColumn);
            } else if (className.includes('content-module-table')) {
                console.log('📊 Module tableau trouvé, recréation...');
                this.recreateTableModuleFromContent(moduleElement, targetColumn);
            } else if (className.includes('content-module-gallery')) {
                console.log('🖼️ Module galerie trouvé, recréation...');
                this.recreateGalleryModuleFromContent(moduleElement, targetColumn);
            } else if (className.includes('content-module-list')) {
                console.log('📋 Module liste trouvé, recréation...');
                this.recreateListModuleFromContent(moduleElement, targetColumn);
            } else {
                console.log('❓ Type de module non reconnu:', className);
            }
        });
    }

    recreateTextModuleFromContent(moduleElement, columnElement) {
        try {
            console.log('📝 Recréation du module texte depuis content-module-text');
            
            // Extraire le contenu du module
            const textContent = moduleElement.innerHTML || '';
            console.log('📄 Contenu texte extrait:', textContent.substring(0, 100) + '...');
            
            // Extraire l'alignement depuis les classes
            const alignment = this.getAlignmentFromClass(moduleElement.className);
            
            const textData = {
                content: textContent,
                formatting: {
                    textAlign: 'left',
                    color: '#000000',
                    fontSize: '16px'
                },
                alignment: alignment
            };
            
            console.log('📝 Données texte extraites:', textData);
            
            const module = this.moduleFactory.createModule('text', textData);
            if (module) {
                columnElement.appendChild(module.element);
                this.modules.set(module.moduleId, module);
                console.log('✅ Module texte recréé avec succès');
            }
        } catch (error) {
            console.error('❌ Erreur lors de la recréation du module texte:', error);
        }
    }

    recreateImageModuleFromContent(moduleElement, columnElement) {
        try {
            console.log('🖼️ Recréation du module image depuis content-module-image');
            
            const img = moduleElement.querySelector('img');
            if (img) {
                const imageData = {
                    src: img.src,
                    alt: img.alt || '',
                    title: '',
                    description: '',
                    caption: '',
                    alignment: this.getAlignmentFromClass(moduleElement.className),
                    width: img.style.width ? parseInt(img.style.width) : null,
                    height: img.style.height ? parseInt(img.style.height) : null
                };
                
                console.log('🖼️ Données image extraites:', imageData);
                
                const module = this.moduleFactory.createModule('image', imageData);
                if (module) {
                    columnElement.appendChild(module.element);
                    this.modules.set(module.moduleId, module);
                    console.log('✅ Module image recréé avec succès');
                }
            }
        } catch (error) {
            console.error('❌ Erreur lors de la recréation du module image:', error);
        }
    }

    recreateVideoModuleFromContent(moduleElement, columnElement) {
        try {
            console.log('🎬 Recréation du module vidéo depuis content-module-video');
            
            const iframe = moduleElement.querySelector('iframe');
            const video = moduleElement.querySelector('video');
            
            if (iframe) {
                const src = iframe.src;
                
                // Essayer de charger les données depuis data-module-data d'abord
                let videoData = null;
                let moduleData = moduleElement.getAttribute('data-module-data');
                
                // Si pas trouvé sur l'élément principal, chercher sur l'élément parent
                if (!moduleData) {
                    const parentElement = moduleElement.parentElement;
                    if (parentElement) {
                        moduleData = parentElement.getAttribute('data-module-data');
                    }
                }
                
                if (moduleData) {
                    try {
                        // Décoder l'attribut HTML encodé
                        const decodedData = moduleData.replace(/&quot;/g, '"').replace(/&amp;/g, '&').replace(/&lt;/g, '<').replace(/&gt;/g, '>');
                        videoData = JSON.parse(decodedData);
                        console.log('🎬 Données vidéo chargées depuis data-module-data:', videoData);
                    } catch (e) {
                        console.warn('⚠️ Erreur parsing data-module-data:', e);
                    }
                }
                
                // Si pas de données sauvegardées, extraire depuis le HTML
                if (!videoData) {
                    videoData = {
                        type: src.includes('youtube.com') ? 'youtube' : 'vimeo',
                        url: src,
                        title: '',
                        description: '',
                        alignment: this.getAlignmentFromClass(moduleElement.className)
                    };
                    console.log('🎬 Données vidéo extraites depuis HTML:', videoData);
                }
                
                const module = this.moduleFactory.createModule('video', videoData);
                if (module) {
                    columnElement.appendChild(module.element);
                    this.modules.set(module.moduleId, module);
                    console.log('✅ Module vidéo recréé avec succès');
                }
            } else if (video) {
                const src = video.querySelector('source')?.src || '';
                
                // Essayer de charger les données depuis data-module-data d'abord
                let videoData = null;
                let moduleData = moduleElement.getAttribute('data-module-data');
                
                // Si pas trouvé sur l'élément principal, chercher sur l'élément parent
                if (!moduleData) {
                    const parentElement = moduleElement.parentElement;
                    if (parentElement) {
                        moduleData = parentElement.getAttribute('data-module-data');
                    }
                }
                
                if (moduleData) {
                    try {
                        // Décoder l'attribut HTML encodé
                        const decodedData = moduleData.replace(/&quot;/g, '"').replace(/&amp;/g, '&').replace(/&lt;/g, '<').replace(/&gt;/g, '>');
                        videoData = JSON.parse(decodedData);
                        console.log('🎬 Données vidéo chargées depuis data-module-data:', videoData);
                    } catch (e) {
                        console.warn('⚠️ Erreur parsing data-module-data:', e);
                    }
                }
                
                // Si pas de données sauvegardées, extraire depuis le HTML
                if (!videoData) {
                    videoData = {
                        type: 'file',
                        url: src,
                        title: '',
                        description: '',
                        controls: video.hasAttribute('controls'),
                        autoplay: video.hasAttribute('autoplay'),
                        loop: video.hasAttribute('loop'),
                        muted: video.hasAttribute('muted'),
                        alignment: this.getAlignmentFromClass(moduleElement.className)
                    };
                    console.log('🎬 Données vidéo extraites depuis HTML:', videoData);
                }
                
                const module = this.moduleFactory.createModule('video', videoData);
                if (module) {
                    columnElement.appendChild(module.element);
                    this.modules.set(module.moduleId, module);
                    console.log('✅ Module vidéo recréé avec succès');
                }
            }
        } catch (error) {
            console.error('❌ Erreur lors de la recréation du module vidéo:', error);
        }
    }

    recreateSeparatorModuleFromContent(moduleElement, columnElement) {
        try {
            console.log('➖ Recréation du module séparateur depuis content-module-separator');
            
            const separatorData = {
                style: 'line',
                alignment: this.getAlignmentFromClass(moduleElement.className)
            };
            
            console.log('➖ Données séparateur extraites:', separatorData);
            
            const module = this.moduleFactory.createModule('separator', separatorData);
            if (module) {
                columnElement.appendChild(module.element);
                this.modules.set(module.moduleId, module);
                console.log('✅ Module séparateur recréé avec succès');
            }
        } catch (error) {
            console.error('❌ Erreur lors de la recréation du module séparateur:', error);
        }
    }

    recreateHeadingModuleFromContent(moduleElement, columnElement) {
        try {
            console.log('📋 Recréation du module titre depuis content-module-heading');

            const moduleHtml = moduleElement.innerHTML;
            console.log('📄 Contenu HTML du module titre (brut):', moduleHtml);

            let headingText = '';
            let headingLevel = 'h2'; // Default
            let headingAlignment = 'left'; // Default

            // Attempt to parse as direct HTML first
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = moduleHtml;
            let headingElement = tempDiv.querySelector('h1, h2, h3, h4, h5, h6');

            if (headingElement) {
                // Found direct HTML heading
                headingText = headingElement.textContent || '';
                headingLevel = headingElement.tagName.toLowerCase();
                headingAlignment = this.getAlignmentFromClass(headingElement.className) || 'left';
                console.log('✅ Titre trouvé comme HTML direct.');
            } else {
                // If not found as direct HTML, it might be HTML entities
                const decodedHtml = this.decodeHtmlEntities(moduleHtml);
                console.log('📄 Contenu HTML du module titre (décodé):', decodedHtml);

                // Try parsing the decoded string as HTML
                const tempDivDecoded = document.createElement('div');
                tempDivDecoded.innerHTML = decodedHtml;
                headingElement = tempDivDecoded.querySelector('h1, h2, h3, h4, h5, h6');

                if (headingElement) {
                    // Found heading after decoding
                    headingText = headingElement.textContent || '';
                    headingLevel = headingElement.tagName.toLowerCase();
                    headingAlignment = this.getAlignmentFromClass(headingElement.className) || 'left';
                    console.log('✅ Titre trouvé après décodage HTML.');
                } else {
                    // Fallback: use the raw text content of the module element, assuming it's just text
                    headingText = moduleElement.textContent.trim();
                    console.log('⚠️ Titre non trouvé, utilisant le texte brut du module.');
                }
            }

            if (headingText.trim()) {
                const headingData = {
                    text: headingText,
                    level: headingLevel,
                    alignment: headingAlignment
                };

                console.log('📋 Données titre extraites:', headingData);

                const module = this.moduleFactory.createModule('heading', headingData);
                if (module) {
                    columnElement.appendChild(module.element);
                    this.modules.set(module.moduleId, module);
                    console.log('✅ Module titre recréé avec succès');
                }
            } else {
                console.warn('⚠️ Module titre ignoré car le texte est vide.');
            }
        } catch (error) {
            console.error('❌ Erreur lors de la recréation du module titre:', error);
        }
    }

    // New helper function
    decodeHtmlEntities(text) {
        const textArea = document.createElement('textarea');
        textArea.innerHTML = text;
        return textArea.value;
    }

    recreateQuoteModuleFromContent(moduleElement, columnElement) {
        try {
            console.log('💬 Recréation du module citation depuis content-module-quote');
            
            const quote = moduleElement.querySelector('blockquote');
            const author = moduleElement.querySelector('.quote-author');
            
            if (quote) {
                const quoteData = {
                    text: quote.textContent || '',
                    author: author ? author.textContent : '',
                    alignment: this.getAlignmentFromClass(moduleElement.className)
                };
                
                console.log('💬 Données citation extraites:', quoteData);
                
                const module = this.moduleFactory.createModule('quote', quoteData);
                if (module) {
                    columnElement.appendChild(module.element);
                    this.modules.set(module.moduleId, module);
                    console.log('✅ Module citation recréé avec succès');
                }
            }
        } catch (error) {
            console.error('❌ Erreur lors de la recréation du module citation:', error);
        }
    }

    recreateButtonModuleFromContent(moduleElement, columnElement) {
        try {
            console.log('🔘 Recréation du module bouton depuis content-module-button');
            
            // Chercher le bouton (peut être <a> ou <button>)
            const button = moduleElement.querySelector('a.content-module-button, button.content-module-button');
            if (button) {
                console.log('🔘 Bouton trouvé:', button);
                console.log('🔘 Type de bouton:', button.tagName);
                console.log('🔘 Classes du bouton:', button.className);
                console.log('🔘 HTML du bouton:', button.outerHTML);
                
                // Chercher le container pour l'alignement
                const buttonContainer = moduleElement.querySelector('.button-container');
                const containerClasses = buttonContainer ? buttonContainer.className : '';
                console.log('🔘 Classes du container:', containerClasses);
                
                const buttonText = button.querySelector('.button-text');
                const buttonData = {
                    text: buttonText ? buttonText.textContent : button.textContent || '',
                    link: button.href || '',
                    target: button.target || '_self',
                    onclick: button.getAttribute('onclick') || '',
                    style: this.extractButtonStyle(button.className),
                    size: this.extractButtonSize(button.className),
                    alignment: this.getAlignmentFromClass(containerClasses),
                    icon: this.extractButtonIcon(button),
                    iconPosition: this.extractButtonIconPosition(button),
                    rounded: button.className.includes('btn-rounded'),
                    shadow: button.className.includes('btn-shadow'),
                    animation: this.extractButtonAnimation(button.className),
                    width: this.extractButtonWidth(containerClasses)
                };
                
                console.log('🔘 Données bouton extraites:', buttonData);
                
                const module = this.moduleFactory.createModule('button', buttonData);
                if (module) {
                    columnElement.appendChild(module.element);
                    this.modules.set(module.moduleId, module);
                    console.log('✅ Module bouton recréé avec succès');
                }
            } else {
                console.log('❌ Aucun bouton trouvé dans le module');
            }
        } catch (error) {
            console.error('❌ Erreur lors de la recréation du module bouton:', error);
        }
    }

    extractButtonStyle(className) {
        if (className.includes('btn-primary')) return 'primary';
        if (className.includes('btn-secondary')) return 'secondary';
        if (className.includes('btn-success')) return 'success';
        if (className.includes('btn-danger')) return 'danger';
        if (className.includes('btn-warning')) return 'warning';
        if (className.includes('btn-info')) return 'info';
        if (className.includes('btn-light')) return 'light';
        if (className.includes('btn-dark')) return 'dark';
        return 'primary';
    }

    extractButtonSize(className) {
        if (className.includes('btn-small')) return 'small';
        if (className.includes('btn-large')) return 'large';
        return 'medium';
    }
    
    extractButtonIcon(button) {
        const iconElement = button.querySelector('.button-icon');
        return iconElement ? iconElement.textContent : '';
    }
    
    extractButtonIconPosition(button) {
        const icons = button.querySelectorAll('.button-icon');
        if (icons.length === 0) return 'left';
        
        const firstIcon = icons[0];
        const buttonText = button.querySelector('.button-text');
        
        if (buttonText && firstIcon.compareDocumentPosition(buttonText) & Node.DOCUMENT_POSITION_FOLLOWING) {
            return 'left';
        } else {
            return 'right';
        }
    }
    
    extractButtonAnimation(className) {
        if (className.includes('btn-animation-pulse')) return 'pulse';
        if (className.includes('btn-animation-bounce')) return 'bounce';
        if (className.includes('btn-animation-shake')) return 'shake';
        if (className.includes('btn-animation-glow')) return 'glow';
        return 'none';
    }
    
    extractButtonWidth(className) {
        if (className.includes('button-full-width')) return 'full';
        return 'auto';
    }

    recreateTableModuleFromContent(moduleElement, columnElement) {
        try {
            console.log('📊 Recréation du module tableau depuis content-module-table');
            
            const table = moduleElement.querySelector('table');
            if (table) {
                const tableData = {
                    content: table.innerHTML,
                    alignment: this.getAlignmentFromClass(moduleElement.className)
                };
                
                console.log('📊 Données tableau extraites:', tableData);
                
                const module = this.moduleFactory.createModule('table', tableData);
                if (module) {
                    columnElement.appendChild(module.element);
                    this.modules.set(module.moduleId, module);
                    console.log('✅ Module tableau recréé avec succès');
                }
            }
        } catch (error) {
            console.error('❌ Erreur lors de la recréation du module tableau:', error);
        }
    }

    recreateGalleryModuleFromContent(moduleElement, columnElement) {
        try {
            console.log('🖼️ Recréation du module galerie depuis content-module-gallery');
            
            // Extraire la configuration depuis les classes CSS
            // Chercher d'abord dans l'élément principal, puis dans les conteneurs spécifiques
            let containerClasses = moduleElement.className;
            let galleryContainer = moduleElement.querySelector('.gallery-container, .gallery-masonry, .gallery-slider, .gallery-carousel');
            
            if (galleryContainer) {
                containerClasses = galleryContainer.className;
            }
            
            console.log('🎨 Classes CSS de la galerie:', containerClasses);
            
            // Analyser les classes pour extraire la configuration
            const layout = this.extractGalleryLayout(containerClasses);
            const columns = this.extractGalleryColumns(containerClasses);
            const spacing = this.extractGallerySpacing(containerClasses);
            const captions = this.extractGalleryCaptions(containerClasses);
            
            // Déduire les options depuis le HTML généré
            const showTitles = moduleElement.querySelector('.image-title') !== null;
            const showDescriptions = moduleElement.querySelector('.image-description') !== null;
            const lightbox = moduleElement.querySelector('.lightbox-overlay') !== null;
            const autoplay = moduleElement.querySelector('.carousel-controls, .slider-controls') !== null;

            const galleryData = {
                images: [],
                layout: layout,
                columns: columns,
                spacing: spacing,
                captions: captions,
                showTitles: showTitles,
                showDescriptions: showDescriptions,
                lightbox: lightbox,
                autoplay: autoplay,
                alignment: this.getAlignmentFromClass(moduleElement.className)
            };
            
            console.log('⚙️ Configuration galerie extraite:', {
                layout, columns, spacing, captions
            });
            
            console.log('🔧 Options galerie déduites:', {
                showTitles, showDescriptions, lightbox, autoplay
            });
            
            // Extraire les images de la galerie
            const images = moduleElement.querySelectorAll('img');
            console.log(`🖼️ ${images.length} images trouvées dans la galerie`);
            
            images.forEach((img, index) => {
                console.log(`🖼️ Image ${index + 1}:`, img.src, img.alt);
                
                // Extraire l'ID de l'image depuis data-image-id si disponible
                const galleryItem = img.closest('.gallery-item');
                const imageId = galleryItem ? galleryItem.dataset.imageId : null;
                
                // S'assurer que l'URL est correcte
                const imageUrl = img.src || img.getAttribute('src');
                console.log(`🖼️ URL de l'image ${index + 1}:`, imageUrl);
                
                if (imageUrl) {
                    galleryData.images.push({
                        id: imageId || Date.now() + Math.random(),
                        url: imageUrl,
                        src: imageUrl, // Garder la compatibilité
                        alt: img.alt || '',
                        title: img.alt || '',
                        description: '',
                        caption: ''
                    });
                } else {
                    console.warn(`⚠️ Image ${index + 1} ignorée car pas d'URL trouvée`);
                }
            });
            
            console.log('🖼️ Données galerie extraites:', galleryData);
            
            if (galleryData.images.length > 0) {
                const module = this.moduleFactory.createModule('gallery', galleryData);
                if (module) {
                    columnElement.appendChild(module.element);
                    this.modules.set(module.moduleId, module);
                    console.log('✅ Module galerie recréé avec succès');
                }
            } else {
                console.warn('⚠️ Module galerie ignoré car aucune image valide trouvée');
            }
        } catch (error) {
            console.error('❌ Erreur lors de la recréation du module galerie:', error);
        }
    }

    recreateListModuleFromContent(moduleElement, columnElement) {
        try {
            console.log('📋 Recréation du module liste depuis content-module-list');
            
            const listData = {
                items: [],
                type: 'ul',
                alignment: this.getAlignmentFromClass(moduleElement.className)
            };
            
            // Extraire les éléments de la liste
            const listItems = moduleElement.querySelectorAll('li');
            listItems.forEach(item => {
                listData.items.push(item.textContent.trim());
            });
            
            console.log('📋 Données liste extraites:', listData);
            
            const module = this.moduleFactory.createModule('list', listData);
            if (module) {
                columnElement.appendChild(module.element);
                this.modules.set(module.moduleId, module);
                console.log('✅ Module liste recréé avec succès');
            }
        } catch (error) {
            console.error('❌ Erreur lors de la recréation du module liste:', error);
        }
    }

    // Méthodes d'extraction des propriétés de galerie
    extractGalleryLayout(containerClasses) {
        // Détecter le layout depuis les classes CSS réelles
        if (containerClasses.includes('gallery-masonry')) return 'masonry';
        if (containerClasses.includes('gallery-carousel')) return 'carousel';
        if (containerClasses.includes('gallery-slider')) return 'slider';
        if (containerClasses.includes('gallery-container')) return 'grid';
        if (containerClasses.includes('layout-masonry')) return 'masonry';
        if (containerClasses.includes('layout-carousel')) return 'carousel';
        if (containerClasses.includes('layout-slider')) return 'slider';
        if (containerClasses.includes('layout-grid')) return 'grid';
        return 'grid'; // Par défaut
    }

    extractGalleryColumns(containerClasses) {
        if (containerClasses.includes('columns-2')) return 2;
        if (containerClasses.includes('columns-3')) return 3;
        if (containerClasses.includes('columns-4')) return 4;
        if (containerClasses.includes('columns-5')) return 5;
        return 3; // Par défaut
    }

    extractGallerySpacing(containerClasses) {
        if (containerClasses.includes('spacing-small')) return 'small';
        if (containerClasses.includes('spacing-large')) return 'large';
        if (containerClasses.includes('spacing-medium')) return 'medium';
        return 'medium'; // Par défaut
    }

    extractGalleryCaptions(containerClasses) {
        if (containerClasses.includes('captions-overlay')) return 'overlay';
        if (containerClasses.includes('captions-below')) return 'below';
        if (containerClasses.includes('captions-none')) return 'none';
        return 'overlay'; // Par défaut
    }

    getAlignmentFromClass(className) {
        // Nettoyer les espaces
        const cleanClassName = className.trim();
        
        // Gérer tous les types d'alignement
        if (cleanClassName.includes('text-align-left') || cleanClassName.includes('image-align-left') || 
            cleanClassName.includes('video-align-left') || cleanClassName.includes('separator-align-left') ||
            cleanClassName.includes('button-align-left') || cleanClassName.includes('align-left')) {
            return 'left';
        }
        if (cleanClassName.includes('text-align-right') || cleanClassName.includes('image-align-right') || 
            cleanClassName.includes('video-align-right') || cleanClassName.includes('separator-align-right') ||
            cleanClassName.includes('button-align-right') || cleanClassName.includes('align-right')) {
            return 'right';
        }
        if (cleanClassName.includes('text-align-center') || cleanClassName.includes('image-align-center') || 
            cleanClassName.includes('video-align-center') || cleanClassName.includes('separator-align-center') ||
            cleanClassName.includes('button-align-center') || cleanClassName.includes('align-center')) {
            return 'center';
        }
        return 'left'; // Par défaut à gauche
    }
}

// Rendre disponible globalement
window.FullscreenEditor = FullscreenEditor;
