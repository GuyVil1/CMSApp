/**
 * Classe de base pour tous les modules de l'éditeur
 */
class BaseModule {
    constructor(type, editor) {
        this.type = type;
        this.editor = editor;
        this.moduleId = this.generateId();
        this.element = null;
        this.isSelected = false;
    }

    generateId() {
        return 'module_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
    }

    create() {
        this.element = document.createElement('div');
        this.element.className = 'content-module';
        this.element.dataset.type = this.type;
        this.element.dataset.moduleId = this.moduleId;
        
        this.render();
        this.bindEvents();
        
        return this.element;
    }

    render() {
        // À implémenter dans les classes enfants
        throw new Error('render() doit être implémenté dans les classes enfants');
    }

    bindEvents() {
        if (!this.element) return;

        // Actions du module
        this.element.addEventListener('click', (e) => {
            const actionBtn = e.target.closest('.module-action');
            if (actionBtn) {
                const action = actionBtn.dataset.action;
                this.handleAction(action);
            }
        });
        
        // Sélection du module (avec délai pour éviter les conflits avec le drag)
        let clickTimeout;
        this.element.addEventListener('mousedown', (e) => {
            // Ne pas sélectionner le module si on clique sur un élément éditable
            if (e.target.closest('[contenteditable="true"]') || e.target.closest('.editable-cell')) {
                return;
            }
            
            if (!e.target.closest('.module-action')) {
                clickTimeout = setTimeout(() => {
                    this.select();
                }, 200); // Délai pour distinguer clic et drag
            }
        });
        
        this.element.addEventListener('mousemove', () => {
            if (clickTimeout) {
                clearTimeout(clickTimeout);
                clickTimeout = null;
            }
        });

        // Drag & Drop
        this.bindDragEvents();
    }

    bindDragEvents() {
        // Rendre le module draggable
        this.element.draggable = true;
        console.log('🔧 Configuration drag & drop pour le module:', this.moduleId);
        
        // Événements de drag
        this.element.addEventListener('dragstart', (e) => {
            console.log('🖱️ Drag start détecté pour le module:', this.moduleId);
            this.handleDragStart(e);
        });
        
        this.element.addEventListener('dragend', (e) => {
            console.log('🖱️ Drag end détecté pour le module:', this.moduleId);
            this.handleDragEnd(e);
        });
        
        // Événements de drop
        this.element.addEventListener('dragover', (e) => {
            this.handleDragOver(e);
        });
        
        this.element.addEventListener('drop', (e) => {
            console.log('🖱️ Drop détecté sur le module:', this.moduleId);
            this.handleDrop(e);
        });
        
        this.element.addEventListener('dragenter', (e) => {
            this.handleDragEnter(e);
        });
        
        this.element.addEventListener('dragleave', (e) => {
            this.handleDragLeave(e);
        });
    }

    handleDragStart(e) {
        console.log('🖱️ Début du drag pour le module:', this.moduleId);
        
        // Éviter le drag si on clique sur un bouton d'action
        if (e.target.closest('.module-action')) {
            console.log('❌ Drag annulé - clic sur bouton d\'action');
            e.preventDefault();
            return;
        }
        
        // Éviter le drag si on clique sur le contenu éditable
        if (e.target.closest('[contenteditable="true"]')) {
            console.log('❌ Drag annulé - clic sur contenu éditable');
            e.preventDefault();
            return;
        }
        
        this.element.classList.add('dragging');
        e.dataTransfer.effectAllowed = 'move';
        e.dataTransfer.setData('text/html', this.element.outerHTML);
        e.dataTransfer.setData('module-id', this.moduleId);
        
        // Créer un effet visuel de drag
        this.element.style.opacity = '0.5';
        this.element.style.transform = 'rotate(2deg)';
        
        console.log('✅ Drag démarré avec succès');
    }

    handleDragEnd(e) {
        this.element.classList.remove('dragging');
        this.element.style.opacity = '';
        this.element.style.transform = '';
        
        // Nettoyer les indicateurs de drop
        document.querySelectorAll('.drop-indicator').forEach(indicator => {
            indicator.remove();
        });
    }

    handleDragOver(e) {
        e.preventDefault();
        e.dataTransfer.dropEffect = 'move';
    }

    handleDrop(e) {
        e.preventDefault();
        console.log('🖱️ Drop détecté sur le module:', this.moduleId);
        
        const draggedModuleId = e.dataTransfer.getData('module-id');
        console.log('📦 Module dragué:', draggedModuleId);
        
        if (draggedModuleId === this.moduleId) {
            console.log('❌ Tentative de déplacement sur soi-même - ignoré');
            return; // Ne pas se déplacer sur soi-même
        }
        
        const draggedModule = this.editor.getModuleById(draggedModuleId);
        if (!draggedModule) {
            console.log('❌ Module dragué non trouvé');
            return;
        }
        
        // Déterminer la position de drop
        const dropPosition = this.getDropPosition(e);
        console.log('📍 Position de drop:', dropPosition);
        
        this.moveModuleToPosition(draggedModule, dropPosition);
    }

    handleDragEnter(e) {
        e.preventDefault();
        if (!this.element.classList.contains('dragging')) {
            this.element.classList.add('drop-target');
        }
    }

    handleDragLeave(e) {
        // Vérifier si on quitte vraiment l'élément
        if (!this.element.contains(e.relatedTarget)) {
            this.element.classList.remove('drop-target');
        }
    }

    getDropPosition(e) {
        const rect = this.element.getBoundingClientRect();
        const y = e.clientY - rect.top;
        const height = rect.height;
        
        if (y < height / 3) {
            return 'before';
        } else if (y > height * 2 / 3) {
            return 'after';
        } else {
            return 'replace';
        }
    }

    moveModuleToPosition(draggedModule, position) {
        console.log('🔄 Déplacement du module vers la position:', position);
        
        const draggedElement = draggedModule.element;
        const targetElement = this.element;
        const targetParent = targetElement.parentNode;
        
        if (!targetParent) {
            console.error('❌ Parent de l\'élément cible non trouvé.');
            return;
        }
        
        // Retirer le module dragué de sa position actuelle
        draggedElement.remove();
        
        if (position === 'before') {
            console.log('🔄 Placement avant le module cible');
            targetParent.insertBefore(draggedElement, targetElement);
        } else if (position === 'after') {
            console.log('🔄 Placement après le module cible');
            targetParent.insertBefore(draggedElement, targetElement.nextSibling);
        } else if (position === 'replace') {
            console.log('🔄 Remplacement du module');
            targetParent.replaceChild(draggedElement, targetElement);
        }
        
        // Nettoyer les placeholders si nécessaire
        this.cleanupEmptyColumns();
        console.log('✅ Déplacement terminé');
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

    select() {
        this.editor.selectModule(this);
        this.isSelected = true;
    }

    deselect() {
        this.isSelected = false;
    }

    handleAction(action) {
        switch (action) {
            case 'move-left':
                this.move('left');
                break;
            case 'move-right':
                this.move('right');
                break;
            case 'move-up':
                this.move('up');
                break;
            case 'move-down':
                this.move('down');
                break;
            case 'delete':
                this.delete();
                break;
        }
    }

    move(direction) {
        if (!this.element) return;
        
        const currentColumn = this.element.closest('.content-column');
        if (!currentColumn) return;
        
        const currentSection = currentColumn.closest('.content-section');
        if (!currentSection) return;
        
        switch (direction) {
            case 'left':
                this.moveInColumn('left', currentColumn);
                break;
            case 'right':
                this.moveInColumn('right', currentColumn);
                break;
            case 'up':
                this.moveBetweenColumns('up', currentColumn, currentSection);
                break;
            case 'down':
                this.moveBetweenColumns('down', currentColumn, currentSection);
                break;
        }
    }

    moveInColumn(direction, currentColumn) {
        const currentIndex = Array.from(currentColumn.children).indexOf(this.element);
        
        if (direction === 'left' && currentIndex > 0) {
            currentColumn.insertBefore(this.element, currentColumn.children[currentIndex - 1]);
        } else if (direction === 'right' && currentIndex < currentColumn.children.length - 1) {
            currentColumn.insertBefore(this.element, currentColumn.children[currentIndex + 2]);
        }
    }

    moveBetweenColumns(direction, currentColumn, currentSection) {
        const columns = currentSection.querySelectorAll('.content-column');
        const currentColumnIndex = Array.from(columns).indexOf(currentColumn);
        
        let targetColumn;
        if (direction === 'up' && currentColumnIndex > 0) {
            targetColumn = columns[currentColumnIndex - 1];
        } else if (direction === 'down' && currentColumnIndex < columns.length - 1) {
            targetColumn = columns[currentColumnIndex + 1];
        }
        
        if (targetColumn) {
            // Supprimer le placeholder si il existe
            const placeholder = targetColumn.querySelector('.column-placeholder');
            if (placeholder) {
                placeholder.remove();
            }
            
            // Déplacer le module vers la nouvelle colonne
            targetColumn.appendChild(this.element);
            
            // Ajouter un placeholder à l'ancienne colonne si elle est vide
            if (currentColumn.children.length === 0) {
                currentColumn.innerHTML = `
                    <div class="column-placeholder">
                        <div class="placeholder-icon">📝</div>
                        <div class="placeholder-text">Cliquez sur un module pour commencer</div>
                    </div>
                `;
            }
        }
    }

    delete() {
        if (!this.element) return;
        
        if (confirm('Supprimer ce module ?')) {
            console.log('🗑️ Suppression du module:', this.moduleId);
            
            // Appeler la méthode destroy si elle existe
            if (typeof this.destroy === 'function') {
                this.destroy();
            }
            
            const column = this.element.closest('.content-column');
            this.element.remove();
            
            if (column && column.children.length === 0) {
                column.innerHTML = `
                    <div class="column-placeholder">
                        <div class="placeholder-icon">📝</div>
                        <div class="placeholder-text">Cliquez sur un module pour commencer</div>
                    </div>
                `;
            }
            
            this.editor.currentModule = null;
            this.editor.hideOptions();
        }
    }

    getContent() {
        // À implémenter dans les classes enfants
        return '';
    }

    getOptionsHTML() {
        // À implémenter dans les classes enfants
        return '';
    }

    bindOptionsEvents() {
        // À implémenter dans les classes enfants
    }
}

// Rendre disponible globalement
window.BaseModule = BaseModule;
