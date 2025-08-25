/**
 * Module Séparateur - Gestion des lignes de séparation
 */
class SeparatorModule extends BaseModule {
    constructor(editor) {
        super('separator', editor);
        this.separatorData = {
            style: 'line', // line, double, dotted, dashed, decorative, gradient
            alignment: 'center', // left, center, right
            width: '100', // pourcentage
            thickness: '2', // pixels
            color: '#cccccc',
            margin: '20', // pixels (marge haut/bas)
            decorativeStyle: 'stars' // stars, arrows, dots, flowers, geometric
        };
    }

    render() {
        this.element.innerHTML = `
            <div class="module-header">
                <span class="module-type">➖ Séparateur</span>
                <div class="module-actions">
                    <button type="button" class="module-action" data-action="move-left">⬅️</button>
                    <button type="button" class="module-action" data-action="move-right">➡️</button>
                    <button type="button" class="module-action" data-action="delete">🗑️</button>
                </div>
            </div>
            <div class="module-content">
                <div class="separator-display">
                    ${this.renderSeparator()}
                </div>
            </div>
        `;
        this.bindSeparatorEvents();
    }

    renderSeparator() {
        const style = this.getSeparatorStyle();
        const alignmentClass = `separator-align-${this.separatorData.alignment}`;
        const width = this.separatorData.width === '100' ? '100%' : `${this.separatorData.width}%`;
        
        return `
            <div class="separator-container ${alignmentClass}" style="margin: ${this.separatorData.margin}px 0;">
                <div class="separator ${this.separatorData.style}" style="width: ${width}; ${style}"></div>
            </div>
        `;
    }

    getSeparatorStyle() {
        const color = this.separatorData.color;
        const thickness = this.separatorData.thickness;
        
        switch (this.separatorData.style) {
            case 'line':
                return `border-top: ${thickness}px solid ${color};`;
            case 'double':
                return `
                    border-top: ${thickness}px solid ${color};
                    border-bottom: ${thickness}px solid ${color};
                    height: ${parseInt(thickness) * 3}px;
                    padding: ${thickness}px 0;
                `;
            case 'dotted':
                return `border-top: ${thickness}px dotted ${color};`;
            case 'dashed':
                return `border-top: ${thickness}px dashed ${color};`;
            case 'gradient':
                return `
                    height: ${thickness}px;
                    background: linear-gradient(to right, transparent, ${color}, transparent);
                `;
            case 'decorative':
                return this.getDecorativeStyle();
            default:
                return `border-top: ${thickness}px solid ${color};`;
        }
    }

    getDecorativeStyle() {
        const color = this.separatorData.color;
        const size = parseInt(this.separatorData.thickness) * 2;
        
        switch (this.separatorData.decorativeStyle) {
            case 'stars':
                return `
                    height: ${size}px;
                    background-image: radial-gradient(circle, ${color} 1px, transparent 1px);
                    background-size: ${size}px ${size}px;
                    background-position: center;
                `;
            case 'arrows':
                return `
                    height: ${size}px;
                    background-image: 
                        linear-gradient(45deg, transparent 40%, ${color} 40%, ${color} 60%, transparent 60%),
                        linear-gradient(-45deg, transparent 40%, ${color} 40%, ${color} 60%, transparent 60%);
                    background-size: ${size}px ${size}px;
                    background-position: center;
                `;
            case 'dots':
                return `
                    height: ${size}px;
                    background-image: radial-gradient(circle, ${color} 2px, transparent 2px);
                    background-size: ${size}px ${size}px;
                    background-position: center;
                `;
            case 'flowers':
                return `
                    height: ${size}px;
                    background-image: 
                        radial-gradient(circle at 30% 50%, ${color} 2px, transparent 2px),
                        radial-gradient(circle at 70% 50%, ${color} 2px, transparent 2px),
                        radial-gradient(circle at 50% 30%, ${color} 2px, transparent 2px),
                        radial-gradient(circle at 50% 70%, ${color} 2px, transparent 2px);
                    background-size: ${size}px ${size}px;
                    background-position: center;
                `;
            case 'geometric':
                return `
                    height: ${size}px;
                    background-image: 
                        linear-gradient(45deg, ${color} 25%, transparent 25%),
                        linear-gradient(-45deg, ${color} 25%, transparent 25%);
                    background-size: ${size}px ${size}px;
                    background-position: center;
                `;
            default:
                return `
                    height: ${size}px;
                    background-image: radial-gradient(circle, ${color} 1px, transparent 1px);
                    background-size: ${size}px ${size}px;
                    background-position: center;
                `;
        }
    }

    bindSeparatorEvents() {
        // Pas d'événements spécifiques pour le séparateur
    }

    showSeparatorEditor() {
        const content = this.element.querySelector('.module-content');
        if (!content) return;
        
        content.innerHTML = `
            <div class="separator-editor">
                <div class="editor-section">
                    <h4>⚙️ Options du séparateur</h4>
                    <div class="option-group">
                        <label>Style :</label>
                        <div class="style-buttons">
                            <button type="button" class="style-btn ${this.separatorData.style === 'line' ? 'active' : ''}" data-style="line">━ Ligne simple</button>
                            <button type="button" class="style-btn ${this.separatorData.style === 'double' ? 'active' : ''}" data-style="double">═ Double ligne</button>
                            <button type="button" class="style-btn ${this.separatorData.style === 'dotted' ? 'active' : ''}" data-style="dotted">┄ Pointillée</button>
                            <button type="button" class="style-btn ${this.separatorData.style === 'dashed' ? 'active' : ''}" data-style="dashed">┅ Tiretée</button>
                            <button type="button" class="style-btn ${this.separatorData.style === 'gradient' ? 'active' : ''}" data-style="gradient">⚡ Dégradé</button>
                            <button type="button" class="style-btn ${this.separatorData.style === 'decorative' ? 'active' : ''}" data-style="decorative">✨ Décoratif</button>
                        </div>
                    </div>
                    <div class="option-group decorative-options" style="display: ${this.separatorData.style === 'decorative' ? 'block' : 'none'};">
                        <label>Style décoratif :</label>
                        <select class="decorative-style-select">
                            <option value="stars" ${this.separatorData.decorativeStyle === 'stars' ? 'selected' : ''}>⭐ Étoiles</option>
                            <option value="arrows" ${this.separatorData.decorativeStyle === 'arrows' ? 'selected' : ''}>➡️ Flèches</option>
                            <option value="dots" ${this.separatorData.decorativeStyle === 'dots' ? 'selected' : ''}>● Points</option>
                            <option value="flowers" ${this.separatorData.decorativeStyle === 'flowers' ? 'selected' : ''}>🌸 Fleurs</option>
                            <option value="geometric" ${this.separatorData.decorativeStyle === 'geometric' ? 'selected' : ''}>🔷 Géométrique</option>
                        </select>
                    </div>
                    <div class="option-group">
                        <label>Alignement :</label>
                        <div class="alignment-buttons">
                            <button type="button" class="alignment-btn ${this.separatorData.alignment === 'left' ? 'active' : ''}" data-alignment="left">⬅️ Gauche</button>
                            <button type="button" class="alignment-btn ${this.separatorData.alignment === 'center' ? 'active' : ''}" data-alignment="center">↔️ Centre</button>
                            <button type="button" class="alignment-btn ${this.separatorData.alignment === 'right' ? 'active' : ''}" data-alignment="right">➡️ Droite</button>
                        </div>
                    </div>
                    <div class="option-group">
                        <label>Largeur :</label>
                        <div class="width-control">
                            <input type="range" class="width-slider" min="10" max="100" value="${this.separatorData.width}">
                            <span class="width-value">${this.separatorData.width}%</span>
                        </div>
                    </div>
                    <div class="option-group">
                        <label>Épaisseur :</label>
                        <div class="thickness-control">
                            <input type="range" class="thickness-slider" min="1" max="10" value="${this.separatorData.thickness}">
                            <span class="thickness-value">${this.separatorData.thickness}px</span>
                        </div>
                    </div>
                    <div class="option-group">
                        <label>Marge :</label>
                        <div class="margin-control">
                            <input type="range" class="margin-slider" min="5" max="50" value="${this.separatorData.margin}">
                            <span class="margin-value">${this.separatorData.margin}px</span>
                        </div>
                    </div>
                    <div class="option-group">
                        <label>Couleur :</label>
                        <input type="color" class="separator-color-input" value="${this.separatorData.color}">
                    </div>
                </div>
                <div class="editor-actions">
                    <button type="button" class="save-btn">💾 Sauvegarder</button>
                    <button type="button" class="cancel-btn">❌ Annuler</button>
                </div>
            </div>
        `;
        this.bindEditorEvents();
    }

    bindEditorEvents() {
        // Style buttons
        const styleButtons = this.element.querySelectorAll('.style-btn');
        const decorativeOptions = this.element.querySelector('.decorative-options');
        const decorativeSelect = this.element.querySelector('.decorative-style-select');

        styleButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                styleButtons.forEach(b => b.classList.remove('active'));
                e.target.classList.add('active');
                this.separatorData.style = e.target.dataset.style;
                
                // Afficher/masquer les options décoratives
                if (decorativeOptions) {
                    decorativeOptions.style.display = this.separatorData.style === 'decorative' ? 'block' : 'none';
                }
                
                this.updateSeparator();
            });
        });

        // Decorative style select
        if (decorativeSelect) {
            decorativeSelect.addEventListener('change', (e) => {
                this.separatorData.decorativeStyle = e.target.value;
                this.updateSeparator();
            });
        }

        // Alignment buttons
        const alignmentButtons = this.element.querySelectorAll('.alignment-btn');
        alignmentButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                alignmentButtons.forEach(b => b.classList.remove('active'));
                e.target.classList.add('active');
                this.separatorData.alignment = e.target.dataset.alignment;
                this.updateSeparator();
            });
        });

        // Width slider
        const widthSlider = this.element.querySelector('.width-slider');
        const widthValue = this.element.querySelector('.width-value');
        if (widthSlider && widthValue) {
            widthSlider.addEventListener('input', (e) => {
                this.separatorData.width = e.target.value;
                widthValue.textContent = `${e.target.value}%`;
                this.updateSeparator();
            });
        }

        // Thickness slider
        const thicknessSlider = this.element.querySelector('.thickness-slider');
        const thicknessValue = this.element.querySelector('.thickness-value');
        if (thicknessSlider && thicknessValue) {
            thicknessSlider.addEventListener('input', (e) => {
                this.separatorData.thickness = e.target.value;
                thicknessValue.textContent = `${e.target.value}px`;
                this.updateSeparator();
            });
        }

        // Margin slider
        const marginSlider = this.element.querySelector('.margin-slider');
        const marginValue = this.element.querySelector('.margin-value');
        if (marginSlider && marginValue) {
            marginSlider.addEventListener('input', (e) => {
                this.separatorData.margin = e.target.value;
                marginValue.textContent = `${e.target.value}px`;
                this.updateSeparator();
            });
        }

        // Color input
        const colorInput = this.element.querySelector('.separator-color-input');
        if (colorInput) {
            colorInput.addEventListener('change', (e) => {
                this.separatorData.color = e.target.value;
                this.updateSeparator();
            });
        }

        // Action buttons
        const saveBtn = this.element.querySelector('.save-btn');
        const cancelBtn = this.element.querySelector('.cancel-btn');

        saveBtn.addEventListener('click', () => {
            this.saveSeparator();
        });

        cancelBtn.addEventListener('click', () => {
            this.displaySeparator();
        });
    }

    updateSeparator() {
        const display = this.element.querySelector('.separator-display');
        if (display) {
            display.innerHTML = this.renderSeparator();
        }
    }

    saveSeparator() {
        this.displaySeparator();
    }

    displaySeparator() {
        const content = this.element.querySelector('.module-content');
        if (!content) return;
        content.innerHTML = `<div class="separator-display">${this.renderSeparator()}</div>`;
        this.bindSeparatorEvents();
    }

    getContent() {
        const style = this.getSeparatorStyle();
        const alignmentClass = `separator-align-${this.separatorData.alignment}`;
        const width = this.separatorData.width === '100' ? '100%' : `${this.separatorData.width}%`;
        
        return `
            <div class="separator-container ${alignmentClass}" style="margin: ${this.separatorData.margin}px 0;">
                <div class="separator ${this.separatorData.style}" style="width: ${width}; ${style}"></div>
            </div>
        `;
    }

    getOptionsHTML() {
        return `
            <div class="separator-options">
                <h4>Options du séparateur</h4>
                <div class="option-group">
                    <label>Style :</label>
                    <p class="current-style">${this.getStyleName(this.separatorData.style)}</p>
                </div>
                <div class="option-group">
                    <label>Alignement :</label>
                    <p class="current-alignment">${this.getAlignmentName(this.separatorData.alignment)}</p>
                </div>
                <div class="option-group">
                    <label>Largeur :</label>
                    <p class="current-width">${this.separatorData.width}%</p>
                </div>
                <div class="option-group">
                    <label>Actions :</label>
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
            'line': 'Ligne simple',
            'double': 'Double ligne',
            'dotted': 'Pointillée',
            'dashed': 'Tiretée',
            'gradient': 'Dégradé',
            'decorative': 'Décoratif'
        };
        return names[style] || style;
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
                this.showSeparatorEditor();
            });
        }
    }

    loadData(data) {
        console.log('📂 Chargement des données séparateur:', data);
        
        // Appliquer les données au module
        this.separatorData = {
            ...this.separatorData,
            ...data
        };
        
        // Re-rendre le séparateur avec les données chargées
        this.render();
        
        console.log('✅ Données séparateur chargées avec succès');
    }
}

// Rendre disponible globalement
window.SeparatorModule = SeparatorModule;
