/**
 * Module Titre - Gestion des titres et sous-titres
 */
class HeadingModule extends BaseModule {
    constructor(editor) {
        super('heading', editor);
        this.headingData = {
            text: 'Nouveau titre',
            level: 'h2', // h1, h2, h3, h4, h5, h6
            style: 'default', // default, modern, elegant, minimal
            alignment: 'left', // left, center, right
            color: '#333333',
            size: 'medium' // small, medium, large
        };
    }

    render() {
        this.element.innerHTML = `
            <div class="module-header">
                <span class="module-type">📋 Titre</span>
                <div class="module-actions">
                    <button type="button" class="module-action" data-action="move-left">⬅️</button>
                    <button type="button" class="module-action" data-action="move-right">➡️</button>
                    <button type="button" class="module-action" data-action="delete">🗑️</button>
                </div>
            </div>
            <div class="module-content">
                <div class="heading-display">
                    ${this.renderHeading()}
                </div>
            </div>
        `;
        
        this.bindHeadingEvents();
    }

    renderHeading() {
        const headingClass = `heading-${this.headingData.style} heading-${this.headingData.alignment} heading-${this.headingData.size}`;
        const style = `color: ${this.headingData.color};`;
        
        return `<${this.headingData.level} class="heading-text ${headingClass}" style="${style}" contenteditable="true">${this.headingData.text}</${this.headingData.level}>`;
    }

    bindHeadingEvents() {
        const headingText = this.element.querySelector('.heading-text');
        if (!headingText) return;

        // Gestion de la saisie de texte
        headingText.addEventListener('input', (e) => {
            this.headingData.text = e.target.textContent;
        });

        // Gestion de la perte de focus
        headingText.addEventListener('blur', (e) => {
            if (!e.target.textContent.trim()) {
                e.target.textContent = 'Nouveau titre';
                this.headingData.text = 'Nouveau titre';
            }
        });

        // Clic pour éditer
        headingText.addEventListener('click', (e) => {
            if (e.target === headingText) {
                this.showHeadingEditor();
            }
        });
    }

    showHeadingEditor() {
        const content = this.element.querySelector('.module-content');
        if (!content) return;

        content.innerHTML = `
            <div class="heading-editor">
                <div class="editor-section">
                    <h4>📝 Texte du titre</h4>
                    <textarea class="heading-text-input" placeholder="Entrez votre titre...">${this.headingData.text}</textarea>
                </div>
                
                <div class="editor-section">
                    <h4>⚙️ Options</h4>
                    <div class="option-group">
                        <label>Niveau :</label>
                        <select class="heading-level-select">
                            <option value="h1" ${this.headingData.level === 'h1' ? 'selected' : ''}>H1 - Titre principal</option>
                            <option value="h2" ${this.headingData.level === 'h2' ? 'selected' : ''}>H2 - Titre secondaire</option>
                            <option value="h3" ${this.headingData.level === 'h3' ? 'selected' : ''}>H3 - Sous-titre</option>
                            <option value="h4" ${this.headingData.level === 'h4' ? 'selected' : ''}>H4 - Titre de section</option>
                            <option value="h5" ${this.headingData.level === 'h5' ? 'selected' : ''}>H5 - Titre de sous-section</option>
                            <option value="h6" ${this.headingData.level === 'h6' ? 'selected' : ''}>H6 - Titre mineur</option>
                        </select>
                    </div>
                    
                    <div class="option-group">
                        <label>Style :</label>
                        <div class="style-buttons">
                            <button type="button" class="style-btn ${this.headingData.style === 'default' ? 'active' : ''}" data-style="default">Classique</button>
                            <button type="button" class="style-btn ${this.headingData.style === 'modern' ? 'active' : ''}" data-style="modern">Moderne</button>
                            <button type="button" class="style-btn ${this.headingData.style === 'elegant' ? 'active' : ''}" data-style="elegant">Élégant</button>
                            <button type="button" class="style-btn ${this.headingData.style === 'minimal' ? 'active' : ''}" data-style="minimal">Minimal</button>
                        </div>
                    </div>
                    
                    <div class="option-group">
                        <label>Alignement :</label>
                        <div class="alignment-buttons">
                            <button type="button" class="align-btn ${this.headingData.alignment === 'left' ? 'active' : ''}" data-align="left">⬅️ Gauche</button>
                            <button type="button" class="align-btn ${this.headingData.alignment === 'center' ? 'active' : ''}" data-align="center">⬆️ Centre</button>
                            <button type="button" class="align-btn ${this.headingData.alignment === 'right' ? 'active' : ''}" data-align="right">➡️ Droite</button>
                        </div>
                    </div>
                    
                    <div class="option-group">
                        <label>Taille :</label>
                        <div class="size-buttons">
                            <button type="button" class="size-btn ${this.headingData.size === 'small' ? 'active' : ''}" data-size="small">Petit</button>
                            <button type="button" class="size-btn ${this.headingData.size === 'medium' ? 'active' : ''}" data-size="medium">Moyen</button>
                            <button type="button" class="size-btn ${this.headingData.size === 'large' ? 'active' : ''}" data-size="large">Grand</button>
                        </div>
                    </div>
                    
                    <div class="option-group">
                        <label>Couleur :</label>
                        <input type="color" class="heading-color-input" value="${this.headingData.color}">
                    </div>
                </div>
                
                <div class="editor-actions">
                    <button type="button" class="save-btn" onclick="this.closest('.heading-module').querySelector('.heading-text').dispatchEvent(new Event('save'))">💾 Sauvegarder</button>
                    <button type="button" class="cancel-btn" onclick="this.closest('.heading-module').querySelector('.heading-text').dispatchEvent(new Event('cancel'))">❌ Annuler</button>
                </div>
            </div>
        `;
        
        this.bindEditorEvents();
    }

    bindEditorEvents() {
        const textInput = this.element.querySelector('.heading-text-input');
        const levelSelect = this.element.querySelector('.heading-level-select');
        const styleButtons = this.element.querySelectorAll('.style-btn');
        const alignButtons = this.element.querySelectorAll('.align-btn');
        const sizeButtons = this.element.querySelectorAll('.size-btn');
        const colorInput = this.element.querySelector('.heading-color-input');
        const saveBtn = this.element.querySelector('.save-btn');
        const cancelBtn = this.element.querySelector('.cancel-btn');

        // Sauvegarde du texte
        textInput.addEventListener('input', (e) => {
            this.headingData.text = e.target.value;
        });

        // Changement de niveau
        levelSelect.addEventListener('change', (e) => {
            this.headingData.level = e.target.value;
        });

        // Changement de style
        styleButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                styleButtons.forEach(b => b.classList.remove('active'));
                e.target.classList.add('active');
                this.headingData.style = e.target.dataset.style;
            });
        });

        // Changement d'alignement
        alignButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                alignButtons.forEach(b => b.classList.remove('active'));
                e.target.classList.add('active');
                this.headingData.alignment = e.target.dataset.align;
            });
        });

        // Changement de taille
        sizeButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                sizeButtons.forEach(b => b.classList.remove('active'));
                e.target.classList.add('active');
                this.headingData.size = e.target.dataset.size;
            });
        });

        // Changement de couleur
        colorInput.addEventListener('change', (e) => {
            this.headingData.color = e.target.value;
        });

        // Sauvegarder
        saveBtn.addEventListener('click', () => {
            this.saveHeading();
        });

        // Annuler
        cancelBtn.addEventListener('click', () => {
            this.displayHeading();
        });
    }

    saveHeading() {
        const textInput = this.element.querySelector('.heading-text-input');
        if (textInput) {
            this.headingData.text = textInput.value;
        }
        this.displayHeading();
    }

    displayHeading() {
        const content = this.element.querySelector('.module-content');
        if (!content) return;

        content.innerHTML = `
            <div class="heading-display">
                ${this.renderHeading()}
            </div>
        `;
        
        this.bindHeadingEvents();
    }

    getContent() {
        const headingClass = `heading-${this.headingData.style} heading-${this.headingData.alignment} heading-${this.headingData.size}`;
        const style = `color: ${this.headingData.color};`;
        
        return `<${this.headingData.level} class="heading-text ${headingClass}" style="${style}">${this.headingData.text}</${this.headingData.level}>`;
    }

    getOptionsHTML() {
        return `
            <div class="heading-options">
                <h4>Options du titre</h4>
                <div class="option-group">
                    <label>Texte actuel :</label>
                    <p class="current-text">${this.headingData.text}</p>
                </div>
                <div class="option-group">
                    <label>Niveau :</label>
                    <p class="current-level">${this.headingData.level.toUpperCase()}</p>
                </div>
                <div class="option-group">
                    <label>Style :</label>
                    <p class="current-style">${this.headingData.style}</p>
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

    bindOptionsEvents() {
        const editBtn = this.element.querySelector('.action-btn[data-action="edit"]');
        if (editBtn) {
            editBtn.addEventListener('click', () => {
                this.showHeadingEditor();
            });
        }
    }

    loadData(data) {
        console.log('📂 Chargement des données titre:', data);
        
        // Appliquer les données au module
        this.headingData = {
            ...this.headingData,
            ...data
        };
        
        // Mettre à jour l'affichage si l'élément existe
        if (this.element) {
            this.displayHeading();
        }
        
        console.log('✅ Données titre chargées avec succès');
    }
}

// Rendre disponible globalement
window.HeadingModule = HeadingModule;
