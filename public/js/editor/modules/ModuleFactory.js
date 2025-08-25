/**
 * Factory pour créer les différents types de modules
 */
class ModuleFactory {
    constructor(editor) {
        this.editor = editor;
        this.moduleTypes = {
            'text': TextModule,
            'image': ImageModule,
            'video': VideoModule,
            'quote': QuoteModule,
            'gallery': GalleryModule,
            'heading': HeadingModule,
            'list': ListModule,
            'separator': SeparatorModule,
            'table': TableModule,
            'button': ButtonModule
        };
    }

    createModule(type, initialData = null) {
        const ModuleClass = this.moduleTypes[type];
        if (!ModuleClass) {
            console.warn(`Type de module non supporté: ${type}`);
            return this.createDefaultModule(type);
        }

        const module = new ModuleClass(this.editor);
        
        // Si des données initiales sont fournies, les appliquer au module
        if (initialData && typeof module.loadData === 'function') {
            module.loadData(initialData);
        }

        return module;
    }

    createDefaultModule(type) {
        return new DefaultModule(type, this.editor);
    }

    getSupportedTypes() {
        return Object.keys(this.moduleTypes);
    }

    isSupported(type) {
        return this.moduleTypes.hasOwnProperty(type);
    }
}

/**
 * Module par défaut pour les types non supportés
 */
class DefaultModule extends BaseModule {
    constructor(type, editor) {
        super(type, editor);
    }

    render() {
        this.element.innerHTML = `
            <div class="module-header">
                <span class="module-type">${this.getModuleIcon()} ${this.getModuleLabel()}</span>
                <div class="module-actions">
                    <button type="button" class="module-action" data-action="move-left" title="Déplacer à gauche">⬅️</button>
                    <button type="button" class="module-action" data-action="move-right" title="Déplacer à droite">➡️</button>
                    <button type="button" class="module-action" data-action="move-up" title="Déplacer vers le haut">⬆️</button>
                    <button type="button" class="module-action" data-action="move-down" title="Déplacer vers le bas">⬇️</button>
                    <button type="button" class="module-action" data-action="delete" title="Supprimer">🗑️</button>
                </div>
            </div>
            <div class="module-content">
                <p>Module ${this.getModuleLabel()}</p>
            </div>
        `;
    }

    getModuleIcon() {
        const icons = {
            'video': '🎥',
            'quote': '💬',
            'gallery': '🖼️',
            'heading': '📋',
            'list': '📋',
            'divider': '➖'
        };
        return icons[this.type] || '📄';
    }

    getModuleLabel() {
        const labels = {
            'video': 'Vidéo',
            'quote': 'Citation',
            'gallery': 'Galerie',
            'heading': 'Titre',
            'list': 'Liste',
            'divider': 'Séparateur'
        };
        return labels[this.type] || this.type;
    }

    getContent() {
        return `<p>Module ${this.getModuleLabel()}</p>`;
    }

    getOptionsHTML() {
        return `
            <div class="default-options">
                <h4>Options du module</h4>
                <p>Module ${this.getModuleLabel()}</p>
            </div>
        `;
    }
}

// Rendre disponible globalement
window.ModuleFactory = ModuleFactory;
window.DefaultModule = DefaultModule;
