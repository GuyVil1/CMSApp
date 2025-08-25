/**
 * Chargeur de l'éditeur modulaire
 * Charge tous les modules dans le bon ordre de dépendances
 */

// Fonction pour charger un script de manière asynchrone
function loadScript(src) {
    return new Promise((resolve, reject) => {
        const script = document.createElement('script');
        script.src = src;
        script.onload = resolve;
        script.onerror = reject;
        document.head.appendChild(script);
    });
}

// Fonction pour charger tous les modules de l'éditeur
async function loadEditorModules() {
    try {
        console.log('🔄 Chargement des modules de l\'éditeur...');
        
                            // Ordre de chargement des modules (dépendances)
        const modules = [
            '/public/js/editor/core/BaseModule.js',
            '/public/js/editor/modules/TextModule.js',
            '/public/js/editor/modules/ImageModule.js',
            '/public/js/editor/modules/VideoModule.js',
            '/public/js/editor/modules/QuoteModule.js',
            '/public/js/editor/modules/GalleryModule.js',
                '/public/js/editor/modules/HeadingModule.js',
                '/public/js/editor/modules/ListModule.js',
            '/public/js/editor/modules/SeparatorModule.js',
            '/public/js/editor/modules/TableModule.js',
            '/public/js/editor/modules/ButtonModule.js',
            '/public/js/editor/modules/ModuleFactory.js',
            '/public/js/editor/core/StyleManager.js',
            '/public/js/editor/FullscreenEditor.js'
        ];
        
        // Charger chaque module dans l'ordre
        for (const module of modules) {
            console.log(`📦 Chargement de ${module}...`);
            await loadScript(module);
        }
        
        console.log('✅ Tous les modules de l\'éditeur ont été chargés avec succès !');
        
        // Événement pour indiquer que l'éditeur est prêt
        window.dispatchEvent(new CustomEvent('editorReady'));
        
    } catch (error) {
        console.error('❌ Erreur lors du chargement des modules de l\'éditeur:', error);
    }
}

// Fonction pour initialiser l'éditeur une fois les modules chargés
function initEditor(options = {}) {
    if (typeof FullscreenEditor === 'undefined') {
        console.error('❌ L\'éditeur n\'est pas encore chargé. Attendez l\'événement "editorReady".');
        return null;
    }
    
    console.log('🚀 Initialisation de l\'éditeur...');
    const editor = new FullscreenEditor(options);
    return editor;
}

// Charger automatiquement les modules au chargement de la page
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', loadEditorModules);
} else {
    loadEditorModules();
}

// Exposer les fonctions globalement
window.loadEditorModules = loadEditorModules;
window.initEditor = initEditor;
