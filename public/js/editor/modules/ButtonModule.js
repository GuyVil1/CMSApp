/**
 * Module Bouton - Gestion des boutons d'action
 */
class ButtonModule extends BaseModule {
    constructor(editor) {
        super('button', editor);
        this.buttonData = {
            text: 'Cliquez ici',
            link: '',
            target: '_self', // _self, _blank, _parent, _top
            style: 'primary', // primary, secondary, success, danger, warning, info, light, dark, outline-primary, outline-secondary, etc.
            size: 'medium', // small, medium, large
            alignment: 'left', // left, center, right
            width: 'auto', // auto, full
            icon: '', // emoji ou icône
            iconPosition: 'left', // left, right
            rounded: false,
            shadow: false,
            animation: 'none', // none, pulse, bounce, shake, glow
            customColor: '#007bff',
            customBgColor: '#007bff',
            customBorderColor: '#007bff',
            customTextColor: '#ffffff'
        };
    }

    render() {
        this.element.innerHTML = `
            <div class="module-header">
                <span class="module-type">🔘 Bouton</span>
                <div class="module-actions">
                    <button type="button" class="module-action" data-action="move-left">⬅️</button>
                    <button type="button" class="module-action" data-action="move-right">➡️</button>
                    <button type="button" class="module-action" data-action="delete">🗑️</button>
                </div>
            </div>
            <div class="module-content">
                <div class="button-display">
                    ${this.renderButton()}
                </div>
            </div>
        `;
        
        this.bindButtonEvents();
    }

    renderButton() {
        const buttonClass = this.getButtonClass();
        const alignmentClass = this.getAlignmentClass();
        const widthClass = this.getWidthClass();
        const animationClass = this.getAnimationClass();
        
        const buttonHTML = `
            <div class="button-container ${alignmentClass} ${widthClass}">
                <a href="${this.buttonData.link || '#'}" 
                   target="${this.buttonData.target}" 
                   class="custom-button ${buttonClass} ${animationClass}"
                   style="${this.getCustomStyles()}">
                    ${this.buttonData.icon && this.buttonData.iconPosition === 'left' ? `<span class="button-icon">${this.buttonData.icon}</span>` : ''}
                    <span class="button-text">${this.buttonData.text}</span>
                    ${this.buttonData.icon && this.buttonData.iconPosition === 'right' ? `<span class="button-icon">${this.buttonData.icon}</span>` : ''}
                </a>
            </div>
        `;

        return buttonHTML;
    }

    getButtonClass() {
        const baseClass = 'btn';
        const styleClass = this.buttonData.style.startsWith('outline-') ? 
            `btn-outline-${this.buttonData.style.replace('outline-', '')}` : 
            `btn-${this.buttonData.style}`;
        const sizeClass = `btn-${this.buttonData.size}`;
        const roundedClass = this.buttonData.rounded ? 'btn-rounded' : '';
        const shadowClass = this.buttonData.shadow ? 'btn-shadow' : '';
        
        return `${baseClass} ${styleClass} ${sizeClass} ${roundedClass} ${shadowClass}`.trim();
    }

    getAlignmentClass() {
        return `button-align-${this.buttonData.alignment}`;
    }

    getWidthClass() {
        return this.buttonData.width === 'full' ? 'button-full-width' : '';
    }

    getAnimationClass() {
        return this.buttonData.animation !== 'none' ? `btn-animation-${this.buttonData.animation}` : '';
    }

    getCustomStyles() {
        if (this.buttonData.style === 'custom') {
            return `
                background-color: ${this.buttonData.customBgColor};
                border-color: ${this.buttonData.customBorderColor};
                color: ${this.buttonData.customTextColor};
            `;
        }
        return '';
    }

    bindButtonEvents() {
        const buttonDisplay = this.element.querySelector('.button-display');
        if (buttonDisplay) {
            buttonDisplay.addEventListener('click', (e) => {
                if (!e.target.closest('a')) {
                    this.showButtonEditor();
                }
            });
        }
    }

    showButtonEditor() {
        const editorHTML = `
            <div class="button-editor">
                <div class="editor-section">
                    <h4>Contenu du bouton</h4>
                    <div class="content-group">
                        <label>Texte:</label>
                        <input type="text" id="button-text" value="${this.buttonData.text}" placeholder="Texte du bouton">
                    </div>
                    <div class="content-group">
                        <label>Lien:</label>
                        <input type="url" id="button-link" value="${this.buttonData.link}" placeholder="https://...">
                    </div>
                    <div class="content-group">
                        <label>Cible:</label>
                        <select id="button-target">
                            <option value="_self" ${this.buttonData.target === '_self' ? 'selected' : ''}>Même fenêtre</option>
                            <option value="_blank" ${this.buttonData.target === '_blank' ? 'selected' : ''}>Nouvelle fenêtre</option>
                            <option value="_parent" ${this.buttonData.target === '_parent' ? 'selected' : ''}>Fenêtre parent</option>
                            <option value="_top" ${this.buttonData.target === '_top' ? 'selected' : ''}>Fenêtre principale</option>
                        </select>
                    </div>
                    <div class="content-group">
                        <label>Icône:</label>
                        <input type="text" id="button-icon" value="${this.buttonData.icon}" placeholder="🎯 ou icône">
                    </div>
                    <div class="content-group">
                        <label>Position icône:</label>
                        <select id="icon-position">
                            <option value="left" ${this.buttonData.iconPosition === 'left' ? 'selected' : ''}>Gauche</option>
                            <option value="right" ${this.buttonData.iconPosition === 'right' ? 'selected' : ''}>Droite</option>
                        </select>
                    </div>
                </div>

                <div class="editor-section">
                    <h4>Style du bouton</h4>
                    <div class="style-buttons">
                        <button type="button" class="style-btn ${this.buttonData.style === 'primary' ? 'active' : ''}" data-style="primary">Primaire</button>
                        <button type="button" class="style-btn ${this.buttonData.style === 'secondary' ? 'active' : ''}" data-style="secondary">Secondaire</button>
                        <button type="button" class="style-btn ${this.buttonData.style === 'success' ? 'active' : ''}" data-style="success">Succès</button>
                        <button type="button" class="style-btn ${this.buttonData.style === 'danger' ? 'active' : ''}" data-style="danger">Danger</button>
                        <button type="button" class="style-btn ${this.buttonData.style === 'warning' ? 'active' : ''}" data-style="warning">Avertissement</button>
                        <button type="button" class="style-btn ${this.buttonData.style === 'info' ? 'active' : ''}" data-style="info">Info</button>
                        <button type="button" class="style-btn ${this.buttonData.style === 'light' ? 'active' : ''}" data-style="light">Clair</button>
                        <button type="button" class="style-btn ${this.buttonData.style === 'dark' ? 'active' : ''}" data-style="dark">Sombre</button>
                        <button type="button" class="style-btn ${this.buttonData.style === 'outline-primary' ? 'active' : ''}" data-style="outline-primary">Contour Primaire</button>
                        <button type="button" class="style-btn ${this.buttonData.style === 'outline-secondary' ? 'active' : ''}" data-style="outline-secondary">Contour Secondaire</button>
                        <button type="button" class="style-btn ${this.buttonData.style === 'custom' ? 'active' : ''}" data-style="custom">Personnalisé</button>
                    </div>
                </div>

                <div class="editor-section">
                    <h4>Taille et alignement</h4>
                    <div class="size-buttons">
                        <button type="button" class="size-btn ${this.buttonData.size === 'small' ? 'active' : ''}" data-size="small">Petit</button>
                        <button type="button" class="size-btn ${this.buttonData.size === 'medium' ? 'active' : ''}" data-size="medium">Moyen</button>
                        <button type="button" class="size-btn ${this.buttonData.size === 'large' ? 'active' : ''}" data-size="large">Grand</button>
                    </div>
                    <div class="alignment-buttons">
                        <button type="button" class="align-btn ${this.buttonData.alignment === 'left' ? 'active' : ''}" data-align="left">Gauche</button>
                        <button type="button" class="align-btn ${this.buttonData.alignment === 'center' ? 'active' : ''}" data-align="center">Centre</button>
                        <button type="button" class="align-btn ${this.buttonData.alignment === 'right' ? 'active' : ''}" data-align="right">Droite</button>
                    </div>
                    <div class="width-buttons">
                        <button type="button" class="width-btn ${this.buttonData.width === 'auto' ? 'active' : ''}" data-width="auto">Largeur auto</button>
                        <button type="button" class="width-btn ${this.buttonData.width === 'full' ? 'active' : ''}" data-width="full">Largeur complète</button>
                    </div>
                </div>

                <div class="editor-section">
                    <h4>Effets</h4>
                    <div class="effects-group">
                        <label>
                            <input type="checkbox" id="button-rounded" ${this.buttonData.rounded ? 'checked' : ''}>
                            Coins arrondis
                        </label>
                        <label>
                            <input type="checkbox" id="button-shadow" ${this.buttonData.shadow ? 'checked' : ''}>
                            Ombre
                        </label>
                    </div>
                    <div class="animation-group">
                        <label>Animation:</label>
                        <select id="button-animation">
                            <option value="none" ${this.buttonData.animation === 'none' ? 'selected' : ''}>Aucune</option>
                            <option value="pulse" ${this.buttonData.animation === 'pulse' ? 'selected' : ''}>Pulse</option>
                            <option value="bounce" ${this.buttonData.animation === 'bounce' ? 'selected' : ''}>Rebond</option>
                            <option value="shake" ${this.buttonData.animation === 'shake' ? 'selected' : ''}>Secousse</option>
                            <option value="glow" ${this.buttonData.animation === 'glow' ? 'selected' : ''}>Lueur</option>
                        </select>
                    </div>
                </div>

                <div class="editor-section custom-colors" id="custom-colors-section" style="display: ${this.buttonData.style === 'custom' ? 'block' : 'none'};">
                    <h4>Couleurs personnalisées</h4>
                    <div class="color-controls">
                        <div class="color-group">
                            <label>Couleur de fond:</label>
                            <input type="color" id="custom-bg-color" value="${this.buttonData.customBgColor}">
                        </div>
                        <div class="color-group">
                            <label>Couleur de bordure:</label>
                            <input type="color" id="custom-border-color" value="${this.buttonData.customBorderColor}">
                        </div>
                        <div class="color-group">
                            <label>Couleur du texte:</label>
                            <input type="color" id="custom-text-color" value="${this.buttonData.customTextColor}">
                        </div>
                    </div>
                </div>

                <div class="editor-actions">
                    <button type="button" class="save-btn" data-action="save">💾 Sauvegarder</button>
                    <button type="button" class="cancel-btn" data-action="cancel">✕ Annuler</button>
                </div>
            </div>
        `;

        this.editor.optionsContent.innerHTML = editorHTML;
        this.bindEditorEvents();
    }

    bindEditorEvents() {
        // Contenu du bouton
        const textInput = document.getElementById('button-text');
        const linkInput = document.getElementById('button-link');
        const targetSelect = document.getElementById('button-target');
        const iconInput = document.getElementById('button-icon');
        const iconPositionSelect = document.getElementById('icon-position');

        if (textInput) textInput.addEventListener('input', (e) => {
            this.buttonData.text = e.target.value;
            this.updatePreview();
        });

        if (linkInput) linkInput.addEventListener('input', (e) => {
            this.buttonData.link = e.target.value;
        });

        if (targetSelect) targetSelect.addEventListener('change', (e) => {
            this.buttonData.target = e.target.value;
        });

        if (iconInput) iconInput.addEventListener('input', (e) => {
            this.buttonData.icon = e.target.value;
            this.updatePreview();
        });

        if (iconPositionSelect) iconPositionSelect.addEventListener('change', (e) => {
            this.buttonData.iconPosition = e.target.value;
            this.updatePreview();
        });

        // Styles
        const styleButtons = document.querySelectorAll('.style-btn');
        styleButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                styleButtons.forEach(b => b.classList.remove('active'));
                e.target.classList.add('active');
                this.buttonData.style = e.target.dataset.style;
                this.toggleCustomColors();
                this.updatePreview();
            });
        });

        // Tailles
        const sizeButtons = document.querySelectorAll('.size-btn');
        sizeButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                sizeButtons.forEach(b => b.classList.remove('active'));
                e.target.classList.add('active');
                this.buttonData.size = e.target.dataset.size;
                this.updatePreview();
            });
        });

        // Alignement
        const alignButtons = document.querySelectorAll('.align-btn');
        alignButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                alignButtons.forEach(b => b.classList.remove('active'));
                e.target.classList.add('active');
                this.buttonData.alignment = e.target.dataset.align;
                this.updatePreview();
            });
        });

        // Largeur
        const widthButtons = document.querySelectorAll('.width-btn');
        widthButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                widthButtons.forEach(b => b.classList.remove('active'));
                e.target.classList.add('active');
                this.buttonData.width = e.target.dataset.width;
                this.updatePreview();
            });
        });

        // Effets
        const roundedCheckbox = document.getElementById('button-rounded');
        const shadowCheckbox = document.getElementById('button-shadow');
        const animationSelect = document.getElementById('button-animation');

        if (roundedCheckbox) roundedCheckbox.addEventListener('change', (e) => {
            this.buttonData.rounded = e.target.checked;
            this.updatePreview();
        });

        if (shadowCheckbox) shadowCheckbox.addEventListener('change', (e) => {
            this.buttonData.shadow = e.target.checked;
            this.updatePreview();
        });

        if (animationSelect) animationSelect.addEventListener('change', (e) => {
            this.buttonData.animation = e.target.value;
            this.updatePreview();
        });

        // Couleurs personnalisées
        const bgColorInput = document.getElementById('custom-bg-color');
        const borderColorInput = document.getElementById('custom-border-color');
        const textColorInput = document.getElementById('custom-text-color');

        if (bgColorInput) bgColorInput.addEventListener('change', (e) => {
            this.buttonData.customBgColor = e.target.value;
            this.updatePreview();
        });

        if (borderColorInput) borderColorInput.addEventListener('change', (e) => {
            this.buttonData.customBorderColor = e.target.value;
            this.updatePreview();
        });

        if (textColorInput) textColorInput.addEventListener('change', (e) => {
            this.buttonData.customTextColor = e.target.value;
            this.updatePreview();
        });

        // Actions
        const saveBtn = document.querySelector('.save-btn');
        const cancelBtn = document.querySelector('.cancel-btn');

        if (saveBtn) {
            saveBtn.addEventListener('click', (e) => {
                e.stopPropagation(); // Empêcher la propagation vers l'éditeur global
                this.saveButton();
            });
        }

        if (cancelBtn) {
            cancelBtn.addEventListener('click', (e) => {
                e.stopPropagation(); // Empêcher la propagation vers l'éditeur global
                this.cancelEdit();
            });
        }
    }

    toggleCustomColors() {
        const customSection = document.getElementById('custom-colors-section');
        if (customSection) {
            customSection.style.display = this.buttonData.style === 'custom' ? 'block' : 'none';
        }
    }

    updatePreview() {
        const buttonDisplay = this.element.querySelector('.button-display');
        if (buttonDisplay) {
            buttonDisplay.innerHTML = this.renderButton();
        }
    }

    saveButton() {
        this.displayButton();
        this.editor.hideOptions();
    }

    cancelEdit() {
        this.editor.hideOptions();
    }

    displayButton() {
        const buttonDisplay = this.element.querySelector('.button-display');
        if (buttonDisplay) {
            buttonDisplay.innerHTML = this.renderButton();
        }
    }

    getContent() {
        return this.renderButton();
    }

    getOptionsHTML() {
        return `
            <div class="button-options">
                <h4>🔘 Options du bouton</h4>
                <div class="option-group">
                    <label>Configuration actuelle:</label>
                    <p>Texte: "${this.buttonData.text}"</p>
                    <p>Style: ${this.getStyleName(this.buttonData.style)}</p>
                    <p>Taille: ${this.getSizeName(this.buttonData.size)}</p>
                    <p>Alignement: ${this.getAlignmentName(this.buttonData.alignment)}</p>
                </div>
                <div class="option-group">
                    <label>Actions:</label>
                    <div class="action-buttons">
                        <button type="button" class="action-btn" data-action="edit">
                            <span class="icon">✏️</span> Modifier
                        </button>
                    </div>
                </div>
            </div>
        `;
    }

    getStyleName(style) {
        const names = {
            'primary': 'Primaire',
            'secondary': 'Secondaire',
            'success': 'Succès',
            'danger': 'Danger',
            'warning': 'Avertissement',
            'info': 'Info',
            'light': 'Clair',
            'dark': 'Sombre',
            'outline-primary': 'Contour Primaire',
            'outline-secondary': 'Contour Secondaire',
            'custom': 'Personnalisé'
        };
        return names[style] || style;
    }

    getSizeName(size) {
        const names = {
            'small': 'Petit',
            'medium': 'Moyen',
            'large': 'Grand'
        };
        return names[size] || size;
    }

    getAlignmentName(alignment) {
        const names = {
            'left': 'Gauche',
            'center': 'Centre',
            'right': 'Droite'
        };
        return names[alignment] || alignment;
    }

    bindOptionsEvents() {
        const optionsContent = this.editor.optionsContent;
        if (!optionsContent) return;
        
        const editBtn = optionsContent.querySelector('.action-btn[data-action="edit"]');
        if (editBtn) {
            editBtn.addEventListener('click', () => {
                this.showButtonEditor();
            });
        }
    }

    loadData(data) {
        console.log('📂 Chargement des données bouton:', data);
        
        // Appliquer les données au module
        this.buttonData = {
            ...this.buttonData,
            ...data
        };
        
        // Mettre à jour l'affichage si l'élément existe
        if (this.element) {
            this.displayButton();
        }
        
        console.log('✅ Données bouton chargées avec succès');
    }
}

// Rendre disponible globalement
window.ButtonModule = ButtonModule;
