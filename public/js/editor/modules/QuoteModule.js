/**
 * Module Citation - Gestion des citations stylisées
 */
class QuoteModule extends BaseModule {
    constructor(editor) {
        super('quote', editor);
        this.quoteData = {
            text: '',
            author: '',
            source: '',
            style: 'default', // 'default', 'minimal', 'elegant', 'modern'
            alignment: 'left', // 'left', 'center', 'right'
            size: 'medium' // 'small', 'medium', 'large'
        };
    }

    render() {
        this.element.innerHTML = `
            <div class="module-header">
                <span class="module-type">💬 Citation</span>
                <div class="module-actions">
                    <button type="button" class="module-action" data-action="move-left">⬅️</button>
                    <button type="button" class="module-action" data-action="move-right">➡️</button>
                    <button type="button" class="module-action" data-action="delete">🗑️</button>
                </div>
            </div>
            <div class="module-content">
                <div class="quote-display">
                    ${this.renderQuote()}
                </div>
            </div>
        `;
        
        this.bindEvents();
    }

    renderQuote() {
        const quoteClass = `quote-${this.quoteData.style} quote-${this.quoteData.alignment}`;
        const style = `
            color: ${this.quoteData.textColor};
            background-color: ${this.quoteData.backgroundColor};
            border-left: ${this.quoteData.borderWidth}px solid ${this.quoteData.borderColor};
            padding: ${this.quoteData.padding}px;
            font-style: ${this.quoteData.italic ? 'italic' : 'normal'};
            font-weight: ${this.quoteData.bold ? 'bold' : 'normal'};
        `;
        
        return `
            <blockquote class="quote-text ${quoteClass}" style="${style}">
                <div class="quote-content" contenteditable="true">${this.quoteData.text}</div>
                ${this.quoteData.author ? `<cite class="quote-author" contenteditable="true">— ${this.quoteData.author}</cite>` : ''}
            </blockquote>
        `;
    }

    bindEvents() {
        // Appeler d'abord la méthode parente pour conserver le drag & drop du module
        super.bindEvents();
        
        // Ajouter les événements spécifiques à la citation
        const quoteContent = this.element.querySelector('.quote-content');
        const quoteAuthor = this.element.querySelector('.quote-author');
        
        if (quoteContent) {
            // Gestion de la saisie de texte
            quoteContent.addEventListener('input', (e) => {
                this.quoteData.text = e.target.textContent;
            });

            // Gestion de la perte de focus
            quoteContent.addEventListener('blur', (e) => {
                if (!e.target.textContent.trim()) {
                    e.target.textContent = 'Votre citation ici...';
                    this.quoteData.text = 'Votre citation ici...';
                }
            });
        }
        
        if (quoteAuthor) {
            // Gestion de la saisie de l'auteur
            quoteAuthor.addEventListener('input', (e) => {
                this.quoteData.author = e.target.textContent.replace('— ', '');
            });

            // Gestion de la perte de focus
            quoteAuthor.addEventListener('blur', (e) => {
                if (!e.target.textContent.trim() || e.target.textContent === '— ') {
                    e.target.textContent = '— Auteur';
                    this.quoteData.author = 'Auteur';
                }
            });
        }

        // Clic pour éditer
        const quoteDisplay = this.element.querySelector('.quote-display');
        if (quoteDisplay) {
            quoteDisplay.addEventListener('click', (e) => {
                if (!e.target.closest('[contenteditable="true"]')) {
                    this.showQuoteEditor();
                }
            });
        }
    }

    showQuoteEditor() {
        const content = this.element.querySelector('.module-content');
        if (!content) return;

        content.innerHTML = `
            <div class="quote-editor">
                <div class="quote-input-group">
                    <label>Citation :</label>
                    <textarea class="quote-text-input" placeholder="Entrez votre citation ici..." rows="4">${this.quoteData.text}</textarea>
                </div>
                <div class="quote-input-group">
                    <label>Auteur :</label>
                    <input type="text" class="quote-author-input" placeholder="Nom de l'auteur" value="${this.quoteData.author}">
                </div>
                <div class="quote-input-group">
                    <label>Source :</label>
                    <input type="text" class="quote-source-input" placeholder="Livre, article, etc." value="${this.quoteData.source}">
                </div>
                <div class="quote-actions">
                    <button type="button" class="quote-save-btn">💾 Sauvegarder</button>
                    <button type="button" class="quote-cancel-btn">❌ Annuler</button>
                </div>
            </div>
        `;

        // Focus sur le texte seulement si aucun texte n'est déjà saisi
        const textInput = content.querySelector('.quote-text-input');
        if (textInput && !this.quoteData.text) {
            textInput.focus();
        }

        // Événements des boutons
        const saveBtn = content.querySelector('.quote-save-btn');
        const cancelBtn = content.querySelector('.quote-cancel-btn');

        saveBtn?.addEventListener('click', () => {
            this.saveQuote();
        });

        cancelBtn?.addEventListener('click', () => {
            this.displayQuote();
        });

        // Sauvegarde avec Entrée
        const inputs = content.querySelectorAll('input, textarea');
        inputs.forEach(input => {
            input.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' && e.ctrlKey) {
                    this.saveQuote();
                }
            });
        });
    }

    saveQuote() {
        const content = this.element.querySelector('.module-content');
        if (!content) return;

        const textInput = content.querySelector('.quote-text-input');
        const authorInput = content.querySelector('.quote-author-input');
        const sourceInput = content.querySelector('.quote-source-input');

        if (textInput) {
            this.quoteData.text = textInput.value.trim();
        }
        if (authorInput) {
            this.quoteData.author = authorInput.value.trim();
        }
        if (sourceInput) {
            this.quoteData.source = sourceInput.value.trim();
        }

        this.displayQuote();
    }

    displayQuote() {
        const content = this.element.querySelector('.module-content');
        if (!content) return;

        if (!this.quoteData.text) {
            // Retour au placeholder si pas de texte
            content.innerHTML = `
                <div class="quote-placeholder">
                    <div class="quote-icon">💬</div>
                    <div class="quote-text">Cliquez pour ajouter une citation</div>
                    <div class="quote-hint">Texte, auteur et source</div>
                </div>
            `;
            this.bindQuoteEvents();
            return;
        }

        // Afficher la citation
        const alignmentClass = this.getAlignmentClass();
        const styleClass = this.getStyleClass();
        const sizeClass = this.getSizeClass();

        content.innerHTML = `
            <div class="quote-container ${alignmentClass} ${styleClass} ${sizeClass}">
                <blockquote class="quote-text">
                    "${this.quoteData.text}"
                </blockquote>
                ${this.quoteData.author ? `<div class="quote-author">— ${this.quoteData.author}</div>` : ''}
                ${this.quoteData.source ? `<div class="quote-source">${this.quoteData.source}</div>` : ''}
            </div>
        `;

        // Re-binder les événements
        this.bindQuoteEvents();
    }

    getAlignmentClass() {
        switch (this.quoteData.alignment) {
            case 'left': return 'align-left';
            case 'right': return 'align-right';
            case 'center':
            default: return 'align-center';
        }
    }

    getStyleClass() {
        switch (this.quoteData.style) {
            case 'minimal': return 'style-minimal';
            case 'elegant': return 'style-elegant';
            case 'modern': return 'style-modern';
            case 'default':
            default: return 'style-default';
        }
    }

    getSizeClass() {
        switch (this.quoteData.size) {
            case 'small': return 'size-small';
            case 'large': return 'size-large';
            case 'medium':
            default: return 'size-medium';
        }
    }

    destroy() {
        console.log('🗑️ Destruction du module citation:', this.moduleId);
        this.cleanupOptionsEvents();
    }

    getContent() {
        if (!this.quoteData.text) return '';
        
        const alignmentClass = this.getAlignmentClass();
        const styleClass = this.getStyleClass();
        const sizeClass = this.getSizeClass();

        return `
            <div class="quote-container ${alignmentClass} ${styleClass} ${sizeClass}">
                <blockquote class="quote-text">
                    "${this.quoteData.text}"
                </blockquote>
                ${this.quoteData.author ? `<div class="quote-author">— ${this.quoteData.author}</div>` : ''}
                ${this.quoteData.source ? `<div class="quote-source">${this.quoteData.source}</div>` : ''}
            </div>
        `;
    }

    getOptionsHTML() {
        if (!this.quoteData.text) {
            return `
                <div class="quote-options">
                    <h4>Options de la citation</h4>
                    <p>Aucune citation définie</p>
                    <div class="option-group">
                        <button type="button" class="quote-action-btn" data-action="edit">
                            <span class="icon">✏️</span> Ajouter une citation
                        </button>
                    </div>
                </div>
            `;
        }

        return `
            <div class="quote-options">
                <h4>Options de la citation</h4>
                
                <div class="option-group">
                    <label>Actions :</label>
                    <div class="quote-action-buttons">
                        <button type="button" class="quote-action-btn" data-action="edit">
                            <span class="icon">✏️</span> Modifier
                        </button>
                        <button type="button" class="quote-action-btn danger" data-action="clear">
                            <span class="icon">🗑️</span> Effacer
                        </button>
                    </div>
                </div>

                <div class="option-group">
                    <label>Style :</label>
                    <div class="style-buttons">
                        <button type="button" class="style-btn ${this.quoteData.style === 'default' ? 'active' : ''}" data-style="default">
                            <span class="icon">📝</span> Classique
                        </button>
                        <button type="button" class="style-btn ${this.quoteData.style === 'minimal' ? 'active' : ''}" data-style="minimal">
                            <span class="icon">⚪</span> Minimal
                        </button>
                        <button type="button" class="style-btn ${this.quoteData.style === 'elegant' ? 'active' : ''}" data-style="elegant">
                            <span class="icon">✨</span> Élégant
                        </button>
                        <button type="button" class="style-btn ${this.quoteData.style === 'modern' ? 'active' : ''}" data-style="modern">
                            <span class="icon">🎨</span> Moderne
                        </button>
                    </div>
                </div>

                <div class="option-group">
                    <label>Alignement :</label>
                    <div class="alignment-buttons">
                        <button type="button" class="align-btn ${this.quoteData.alignment === 'left' ? 'active' : ''}" data-alignment="left">
                            <span class="icon">⬅️</span> Gauche
                        </button>
                        <button type="button" class="align-btn ${this.quoteData.alignment === 'center' ? 'active' : ''}" data-alignment="center">
                            <span class="icon">↔️</span> Centre
                        </button>
                        <button type="button" class="align-btn ${this.quoteData.alignment === 'right' ? 'active' : ''}" data-alignment="right">
                            <span class="icon">➡️</span> Droite
                        </button>
                    </div>
                </div>

                <div class="option-group">
                    <label>Taille :</label>
                    <div class="size-buttons">
                        <button type="button" class="size-btn ${this.quoteData.size === 'small' ? 'active' : ''}" data-size="small">
                            <span class="icon">📏</span> Petite
                        </button>
                        <button type="button" class="size-btn ${this.quoteData.size === 'medium' ? 'active' : ''}" data-size="medium">
                            <span class="icon">📏</span> Moyenne
                        </button>
                        <button type="button" class="size-btn ${this.quoteData.size === 'large' ? 'active' : ''}" data-size="large">
                            <span class="icon">📏</span> Grande
                        </button>
                    </div>
                </div>

                <div class="option-group">
                    <label>Aperçu :</label>
                    <div class="quote-preview">
                        <div class="quote-container ${this.getAlignmentClass()} ${this.getStyleClass()} ${this.getSizeClass()}">
                            <blockquote class="quote-text">
                                "${this.quoteData.text.substring(0, 50)}${this.quoteData.text.length > 50 ? '...' : ''}"
                            </blockquote>
                            ${this.quoteData.author ? `<div class="quote-author">— ${this.quoteData.author}</div>` : ''}
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    bindOptionsEvents() {
        this.cleanupOptionsEvents();
        
        const optionsContent = this.editor.optionsContent;
        if (!optionsContent) return;

        // Actions principales
        optionsContent.addEventListener('click', (e) => {
            const actionBtn = e.target.closest('.quote-action-btn');
            if (actionBtn && this.editor.currentModule && this.editor.currentModule.moduleId === this.moduleId) {
                const action = actionBtn.dataset.action;
                this.handleOptionAction(action);
            }
        });

        // Styles
        optionsContent.addEventListener('click', (e) => {
            const styleBtn = e.target.closest('.style-btn');
            if (styleBtn && this.editor.currentModule && this.editor.currentModule.moduleId === this.moduleId) {
                const style = styleBtn.dataset.style;
                this.quoteData.style = style;
                this.displayQuote();
                this.updateOptionsDisplay();
            }
        });

        // Alignement
        optionsContent.addEventListener('click', (e) => {
            const alignBtn = e.target.closest('.align-btn');
            if (alignBtn && this.editor.currentModule && this.editor.currentModule.moduleId === this.moduleId) {
                const alignment = alignBtn.dataset.alignment;
                this.quoteData.alignment = alignment;
                this.displayQuote();
                this.updateOptionsDisplay();
            }
        });

        // Taille
        optionsContent.addEventListener('click', (e) => {
            const sizeBtn = e.target.closest('.size-btn');
            if (sizeBtn && this.editor.currentModule && this.editor.currentModule.moduleId === this.moduleId) {
                const size = sizeBtn.dataset.size;
                this.quoteData.size = size;
                this.displayQuote();
                this.updateOptionsDisplay();
            }
        });
    }

    cleanupOptionsEvents() {
        // Les événements sont gérés globalement, pas besoin de cleanup spécifique
    }

    handleOptionAction(action) {
        switch (action) {
            case 'edit':
                this.showQuoteEditor();
                break;
            case 'clear':
                if (confirm('Effacer cette citation ?')) {
                    this.quoteData = {
                        text: '',
                        author: '',
                        source: '',
                        style: 'default',
                        alignment: 'left',
                        size: 'medium'
                    };
                    this.displayQuote();
                    this.updateOptionsDisplay();
                }
                break;
        }
    }

    updateOptionsDisplay() {
        // Mettre à jour l'affichage des options
        const optionsHTML = this.getOptionsHTML();
        if (this.editor.optionsContent) {
            this.editor.optionsContent.innerHTML = optionsHTML;
        }
    }

    loadData(data) {
        console.log('📂 Chargement des données citation:', data);
        
        // Appliquer les données au module
        this.quoteData = {
            ...this.quoteData,
            ...data
        };
        
        // Mettre à jour l'affichage si l'élément existe
        if (this.element) {
            this.displayQuote();
        }
        
        console.log('✅ Données citation chargées avec succès');
    }

    /**
     * Lier les événements de la citation
     */
    bindQuoteEvents() {
        this.bindEvents();
    }
}

// Rendre disponible globalement
window.QuoteModule = QuoteModule;
