/**
 * Éditeur WYSIWYG maison - Belgium Vidéo Gaming
 * Éditeur complet sans dépendances externes
 */

class WysiwygEditor {
    constructor(container, options = {}) {
        this.container = typeof container === 'string' ? document.querySelector(container) : container;
        this.options = {
            placeholder: 'Commencez à écrire votre article...',
            height: '400px',
            ...options
        };
        
        this.init();
    }
    
    init() {
        this.createToolbar();
        this.createEditor();
        this.bindEvents();
        this.setupCommands();
    }
    
    createToolbar() {
        const toolbar = document.createElement('div');
        toolbar.className = 'wysiwyg-toolbar';
        toolbar.innerHTML = `
            <div class="toolbar-group">
                <button type="button" data-command="bold" title="Gras (Ctrl+B)" class="toolbar-btn">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M6 4h8a4 4 0 0 1 4 4 4 4 0 0 1-4 4H6z"></path>
                        <path d="M6 12h9a4 4 0 0 1 4 4 4 4 0 0 1-4 4H6z"></path>
                    </svg>
                </button>
                <button type="button" data-command="italic" title="Italique (Ctrl+I)" class="toolbar-btn">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="19" y1="4" x2="10" y2="4"></line>
                        <line x1="14" y1="20" x2="5" y2="20"></line>
                        <line x1="15" y1="4" x2="9" y2="20"></line>
                    </svg>
                </button>
                <button type="button" data-command="underline" title="Souligné (Ctrl+U)" class="toolbar-btn">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M6 3v7a6 6 0 0 0 6 6 6 6 0 0 0 6-6V3"></path>
                        <line x1="4" y1="21" x2="20" y2="21"></line>
                    </svg>
                </button>
            </div>
            
            <div class="toolbar-group">
                <button type="button" data-command="insertUnorderedList" title="Liste à puces" class="toolbar-btn">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="8" y1="6" x2="21" y2="6"></line>
                        <line x1="8" y1="12" x2="21" y2="12"></line>
                        <line x1="8" y1="18" x2="21" y2="18"></line>
                        <line x1="3" y1="6" x2="3.01" y2="6"></line>
                        <line x1="3" y1="12" x2="3.01" y2="12"></line>
                        <line x1="3" y1="18" x2="3.01" y2="18"></line>
                    </svg>
                </button>
                <button type="button" data-command="insertOrderedList" title="Liste numérotée" class="toolbar-btn">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="10" y1="6" x2="21" y2="6"></line>
                        <line x1="10" y1="12" x2="21" y2="12"></line>
                        <line x1="10" y1="18" x2="21" y2="18"></line>
                        <path d="M4 6h1v4H4z"></path>
                        <path d="M4 10h2"></path>
                        <path d="M6 4v2"></path>
                        <path d="M4 14h1v4H4z"></path>
                        <path d="M4 18h2"></path>
                        <path d="M6 16v2"></path>
                    </svg>
                </button>
            </div>
            
            <div class="toolbar-group">
                <button type="button" data-command="createLink" title="Insérer un lien" class="toolbar-btn">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path>
                        <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path>
                    </svg>
                </button>
                <button type="button" data-command="insertImage" title="Insérer une image" class="toolbar-btn">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                        <circle cx="8.5" cy="8.5" r="1.5"></circle>
                        <polyline points="21,15 16,10 5,21"></polyline>
                    </svg>
                </button>
            </div>
            
            <div class="toolbar-group">
                <button type="button" data-command="formatBlock" data-value="h2" title="Titre 2" class="toolbar-btn">
                    H2
                </button>
                <button type="button" data-command="formatBlock" data-value="h3" title="Titre 3" class="toolbar-btn">
                    H3
                </button>
                <button type="button" data-command="formatBlock" data-value="p" title="Paragraphe" class="toolbar-btn">
                    P
                </button>
            </div>
            
            <div class="toolbar-group">
                <button type="button" data-command="undo" title="Annuler (Ctrl+Z)" class="toolbar-btn">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 7v6h6"></path>
                        <path d="M21 17a9 9 0 0 0-9-9 9 9 0 0 0-6 2.3L3 13"></path>
                    </svg>
                </button>
                <button type="button" data-command="redo" title="Rétablir (Ctrl+Y)" class="toolbar-btn">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 7v6h-6"></path>
                        <path d="M3 17a9 9 0 0 0 9 9 9 9 0 0 0 6-2.3l3-2.7"></path>
                    </svg>
                </button>
            </div>
        `;
        
        this.container.appendChild(toolbar);
        this.toolbar = toolbar;
    }
    
    createEditor() {
        const editor = document.createElement('div');
        editor.className = 'wysiwyg-editor';
        editor.contentEditable = true;
        editor.style.height = this.options.height;
        editor.style.minHeight = this.options.height;
        editor.innerHTML = `<p><br></p>`;
        
        this.container.appendChild(editor);
        this.editor = editor;
        
        // Ajouter le placeholder
        this.setupPlaceholder();
    }
    
    setupPlaceholder() {
        const editor = this.editor;
        
        editor.addEventListener('focus', () => {
            if (editor.textContent === '') {
                editor.innerHTML = '';
            }
        });
        
        editor.addEventListener('blur', () => {
            if (editor.textContent === '') {
                editor.innerHTML = `<p><br></p>`;
            }
        });
    }
    
    bindEvents() {
        // Événements de la barre d'outils
        this.toolbar.addEventListener('click', (e) => {
            if (e.target.classList.contains('toolbar-btn')) {
                e.preventDefault();
                const command = e.target.dataset.command;
                const value = e.target.dataset.value;
                
                if (command === 'createLink') {
                    this.createLink();
                } else if (command === 'insertImage') {
                    this.insertImage();
                } else {
                    this.execCommand(command, value);
                }
                
                this.editor.focus();
            }
        });
        
        // Raccourcis clavier
        this.editor.addEventListener('keydown', (e) => {
            if (e.ctrlKey || e.metaKey) {
                switch (e.key) {
                    case 'b':
                        e.preventDefault();
                        this.execCommand('bold');
                        break;
                    case 'i':
                        e.preventDefault();
                        this.execCommand('italic');
                        break;
                    case 'u':
                        e.preventDefault();
                        this.execCommand('underline');
                        break;
                    case 'z':
                        e.preventDefault();
                        if (e.shiftKey) {
                            this.execCommand('redo');
                        } else {
                            this.execCommand('undo');
                        }
                        break;
                }
            }
        });
        
        // Mise à jour de la barre d'outils
        this.editor.addEventListener('keyup', () => this.updateToolbar());
        this.editor.addEventListener('mouseup', () => this.updateToolbar());
    }
    
    setupCommands() {
        // Commande personnalisée pour les liens
        this.createLink = () => {
            const url = prompt('Entrez l\'URL du lien :', 'https://');
            if (url) {
                this.execCommand('createLink', url);
            }
        };
        
        // Commande personnalisée pour les images
        this.insertImage = () => {
            const url = prompt('Entrez l\'URL de l\'image :', 'https://');
            if (url) {
                this.execCommand('insertImage', url);
            }
        };
    }
    
    execCommand(command, value = null) {
        document.execCommand(command, false, value);
        this.updateToolbar();
    }
    
    updateToolbar() {
        // Mettre à jour l'état des boutons
        const buttons = this.toolbar.querySelectorAll('.toolbar-btn');
        buttons.forEach(btn => {
            const command = btn.dataset.command;
            const value = btn.dataset.value;
            
            if (command === 'formatBlock') {
                btn.classList.toggle('active', document.queryCommandValue('formatBlock') === value);
            } else if (command === 'createLink' || command === 'insertImage') {
                // Pas de mise à jour pour ces boutons
            } else {
                btn.classList.toggle('active', document.queryCommandState(command));
            }
        });
    }
    
    getContent() {
        return this.editor.innerHTML;
    }
    
    setContent(content) {
        this.editor.innerHTML = content;
    }
    
    getTextContent() {
        return this.editor.textContent;
    }
    
    focus() {
        this.editor.focus();
    }
    
    destroy() {
        if (this.container) {
            this.container.innerHTML = '';
        }
    }
}

// Styles CSS pour l'éditeur
const styles = `
<style>
.wysiwyg-editor-container {
    border: 1px solid #ddd;
    border-radius: 8px;
    overflow: hidden;
    background: white;
}

.wysiwyg-toolbar {
    display: flex;
    flex-wrap: wrap;
    gap: 2px;
    padding: 8px;
    background: #f8f9fa;
    border-bottom: 1px solid #ddd;
}

.toolbar-group {
    display: flex;
    gap: 1px;
    padding: 0 4px;
    border-right: 1px solid #ddd;
}

.toolbar-group:last-child {
    border-right: none;
}

.toolbar-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border: none;
    background: white;
    color: #333;
    cursor: pointer;
    border-radius: 4px;
    transition: all 0.2s;
    font-size: 12px;
    font-weight: bold;
}

.toolbar-btn:hover {
    background: #e9ecef;
    color: #000;
}

.toolbar-btn.active {
    background: #007bff;
    color: white;
}

.toolbar-btn svg {
    width: 16px;
    height: 16px;
}

.wysiwyg-editor {
    padding: 16px;
    outline: none;
    line-height: 1.6;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    font-size: 14px;
}

.wysiwyg-editor:empty::before {
    content: attr(data-placeholder);
    color: #999;
    pointer-events: none;
}

.wysiwyg-editor h2 {
    font-size: 1.5em;
    font-weight: bold;
    margin: 1em 0 0.5em 0;
    color: #333;
}

.wysiwyg-editor h3 {
    font-size: 1.25em;
    font-weight: bold;
    margin: 1em 0 0.5em 0;
    color: #444;
}

.wysiwyg-editor p {
    margin: 0 0 1em 0;
}

.wysiwyg-editor ul, .wysiwyg-editor ol {
    margin: 0 0 1em 1.5em;
}

.wysiwyg-editor a {
    color: #007bff;
    text-decoration: underline;
}

.wysiwyg-editor img {
    max-width: 100%;
    height: auto;
    border-radius: 4px;
    margin: 1em 0;
}

.wysiwyg-editor blockquote {
    border-left: 4px solid #007bff;
    margin: 1em 0;
    padding-left: 1em;
    color: #666;
    font-style: italic;
}
</style>
`;

// Injecter les styles
document.head.insertAdjacentHTML('beforeend', styles);
