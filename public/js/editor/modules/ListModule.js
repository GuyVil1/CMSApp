/**
 * Module Liste - Gestion des listes à puces et numérotées
 */
class ListModule extends BaseModule {
    constructor(editor) {
        super('list', editor);
        this.listData = {
            type: 'ul', // ul, ol
            items: [
                { text: 'Premier élément', level: 0 },
                { text: 'Deuxième élément', level: 0 },
                { text: 'Troisième élément', level: 0 }
            ],
            style: 'default', // default, modern, elegant, minimal
            spacing: 'normal', // compact, normal, spacious
            bulletStyle: 'disc', // disc, circle, square, decimal, lower-alpha, upper-alpha, arrow, star, check, dash, plus, bullet
            alignment: 'left', // left, center, right
            color: '#333333'
        };
        this.popup = null;
    }

    render() {
        this.element.innerHTML = `
            <div class="module-header">
                <span class="module-type">📝 Liste</span>
                <div class="module-actions">
                    <button type="button" class="module-action" data-action="move-left">⬅️</button>
                    <button type="button" class="module-action" data-action="move-right">➡️</button>
                    <button type="button" class="module-action" data-action="delete">🗑️</button>
                </div>
            </div>
            <div class="module-content">
                <div class="list-display">
                    ${this.renderList()}
                </div>
            </div>
        `;
        
        this.bindEvents();
    }

    renderList() {
        const listType = this.listData.ordered ? 'ol' : 'ul';
        const listClass = `list-${this.listData.style} list-${this.listData.alignment}`;
        const style = `
            color: ${this.listData.textColor};
            font-size: ${this.listData.fontSize}px;
            line-height: ${this.listData.lineHeight};
        `;
        
        let listHTML = `<${listType} class="list-items ${listClass}" style="${style}">`;
        
        this.listData.items.forEach((item, index) => {
            listHTML += `<li class="list-item" contenteditable="true" data-index="${index}">${item}</li>`;
        });
        
        listHTML += `</${listType}>`;
        return listHTML;
    }

    bindEvents() {
        // Appeler d'abord la méthode parente pour conserver le drag & drop du module
        super.bindEvents();
        
        // Ajouter les événements spécifiques à la liste
        const listItems = this.element.querySelectorAll('.list-item');
        
        listItems.forEach((item, index) => {
            // Gestion de la saisie de texte
            item.addEventListener('input', (e) => {
                this.listData.items[index] = e.target.textContent;
            });

            // Gestion de la perte de focus
            item.addEventListener('blur', (e) => {
                if (!e.target.textContent.trim()) {
                    e.target.textContent = `Élément ${index + 1}`;
                    this.listData.items[index] = `Élément ${index + 1}`;
                }
            });

            // Gestion de la touche Entrée pour ajouter un nouvel élément
            item.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    this.addItemAfter(index);
                }
            });
        });

        // Clic pour éditer
        const listDisplay = this.element.querySelector('.list-display');
        if (listDisplay) {
            listDisplay.addEventListener('click', (e) => {
                if (!e.target.closest('[contenteditable="true"]')) {
                    this.showListEditor();
                }
            });
        }
    }

    addItemAfter(index) {
        const newItem = {
            text: 'Nouvel élément',
            level: this.listData.items[index].level
        };
        this.listData.items.splice(index + 1, 0, newItem);
        this.displayList();
    }

    removeItem(index) {
        if (this.listData.items.length > 1) {
            this.listData.items.splice(index, 1);
            this.displayList();
        }
    }

    increaseLevel(index) {
        if (this.listData.items[index].level < 3) {
            this.listData.items[index].level++;
            this.displayList();
        }
    }

    decreaseLevel(index) {
        if (this.listData.items[index].level > 0) {
            this.listData.items[index].level--;
            this.displayList();
        }
    }

    showListPopup() {
        // Créer le popup
        this.popup = document.createElement('div');
        this.popup.className = 'list-popup-overlay';
        this.popup.innerHTML = `
            <div class="list-popup">
                <div class="popup-header">
                    <h3>📋 Éditeur de liste</h3>
                    <button type="button" class="close-popup-btn">✕</button>
                </div>
                <div class="popup-content">
                    <div class="popup-section">
                        <h4>📝 Éléments de la liste</h4>
                        <div class="list-items-container">
                            ${this.listData.items.map((item, index) => `
                                <div class="list-item-row" data-index="${index}">
                                    <div class="item-controls">
                                        <button type="button" class="level-btn decrease" data-index="${index}" ${item.level === 0 ? 'disabled' : ''}>⬅️</button>
                                        <span class="level-indicator">Niv. ${item.level}</span>
                                        <button type="button" class="level-btn increase" data-index="${index}" ${item.level === 3 ? 'disabled' : ''}>➡️</button>
                                        <button type="button" class="remove-item-btn" data-index="${index}" ${this.listData.items.length === 1 ? 'disabled' : ''}>🗑️</button>
                                    </div>
                                    <input type="text" class="item-text-input" value="${item.text}" placeholder="Texte de l'élément...">
                                </div>
                            `).join('')}
                        </div>
                        <button type="button" class="add-item-btn">➕ Ajouter un élément</button>
                    </div>
                    <div class="popup-section">
                        <h4>⚙️ Options de style</h4>
                        <div class="style-options">
                            <div class="option-group">
                                <label>Type de liste :</label>
                                <div class="list-type-buttons">
                                    <button type="button" class="type-btn ${this.listData.type === 'ul' ? 'active' : ''}" data-type="ul">📋 À puces</button>
                                    <button type="button" class="type-btn ${this.listData.type === 'ol' ? 'active' : ''}" data-type="ol">🔢 Numérotée</button>
                                </div>
                            </div>
                            <div class="option-group">
                                <label>Style :</label>
                                <div class="style-buttons">
                                    <button type="button" class="style-btn ${this.listData.style === 'default' ? 'active' : ''}" data-style="default">Classique</button>
                                    <button type="button" class="style-btn ${this.listData.style === 'modern' ? 'active' : ''}" data-style="modern">Moderne</button>
                                    <button type="button" class="style-btn ${this.listData.style === 'elegant' ? 'active' : ''}" data-style="elegant">Élégant</button>
                                    <button type="button" class="style-btn ${this.listData.style === 'minimal' ? 'active' : ''}" data-style="minimal">Minimal</button>
                                </div>
                            </div>
                            <div class="option-group">
                                <label>Espacement :</label>
                                <div class="spacing-buttons">
                                    <button type="button" class="spacing-btn ${this.listData.spacing === 'compact' ? 'active' : ''}" data-spacing="compact">Compact</button>
                                    <button type="button" class="spacing-btn ${this.listData.spacing === 'normal' ? 'active' : ''}" data-spacing="normal">Normal</button>
                                    <button type="button" class="spacing-btn ${this.listData.spacing === 'spacious' ? 'active' : ''}" data-spacing="spacious">Espacé</button>
                                </div>
                            </div>
                            <div class="option-group">
                                <label>Style de puce :</label>
                                <select class="bullet-style-select">
                                    <option value="disc" ${this.listData.bulletStyle === 'disc' ? 'selected' : ''}>● Disque</option>
                                    <option value="circle" ${this.listData.bulletStyle === 'circle' ? 'selected' : ''}>○ Cercle</option>
                                    <option value="square" ${this.listData.bulletStyle === 'square' ? 'selected' : ''}>■ Carré</option>
                                    <option value="arrow" ${this.listData.bulletStyle === 'arrow' ? 'selected' : ''}>→ Flèche</option>
                                    <option value="star" ${this.listData.bulletStyle === 'star' ? 'selected' : ''}>★ Étoile</option>
                                    <option value="check" ${this.listData.bulletStyle === 'check' ? 'selected' : ''}>✓ Cocher</option>
                                    <option value="dash" ${this.listData.bulletStyle === 'dash' ? 'selected' : ''}>— Tiret</option>
                                    <option value="plus" ${this.listData.bulletStyle === 'plus' ? 'selected' : ''}>+ Plus</option>
                                    <option value="bullet" ${this.listData.bulletStyle === 'bullet' ? 'selected' : ''}>• Bullet</option>
                                    <option value="decimal" ${this.listData.bulletStyle === 'decimal' ? 'selected' : ''}>1. Décimal</option>
                                    <option value="lower-alpha" ${this.listData.bulletStyle === 'lower-alpha' ? 'selected' : ''}>a. Alpha minuscule</option>
                                    <option value="upper-alpha" ${this.listData.bulletStyle === 'upper-alpha' ? 'selected' : ''}>A. Alpha majuscule</option>
                                </select>
                            </div>
                            <div class="option-group">
                                <label>Alignement :</label>
                                <div class="alignment-buttons">
                                    <button type="button" class="alignment-btn ${this.listData.alignment === 'left' ? 'active' : ''}" data-alignment="left">⬅️ Gauche</button>
                                    <button type="button" class="alignment-btn ${this.listData.alignment === 'center' ? 'active' : ''}" data-alignment="center">↔️ Centre</button>
                                    <button type="button" class="alignment-btn ${this.listData.alignment === 'right' ? 'active' : ''}" data-alignment="right">➡️ Droite</button>
                                </div>
                            </div>
                            <div class="option-group">
                                <label>Couleur :</label>
                                <input type="color" class="list-color-input" value="${this.listData.color}">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="popup-actions">
                    <button type="button" class="save-popup-btn">💾 Sauvegarder</button>
                    <button type="button" class="cancel-popup-btn">❌ Annuler</button>
                </div>
            </div>
        `;

        // Ajouter le popup au DOM
        document.body.appendChild(this.popup);
        
        // Bind les événements du popup
        this.bindPopupEvents();
        
        // Focus sur le premier input
        setTimeout(() => {
            const firstInput = this.popup.querySelector('.item-text-input');
            if (firstInput) firstInput.focus();
        }, 100);
    }

    bindPopupEvents() {
        if (!this.popup) return;

        // Fermer le popup
        const closeBtn = this.popup.querySelector('.close-popup-btn');
        const cancelBtn = this.popup.querySelector('.cancel-popup-btn');
        const overlay = this.popup;

        [closeBtn, cancelBtn, overlay].forEach(element => {
            if (element) {
                element.addEventListener('click', (e) => {
                    if (e.target === element || e.target === overlay) {
                        this.closePopup();
                    }
                });
            }
        });

        // Événements des éléments de liste
        const textInputs = this.popup.querySelectorAll('.item-text-input');
        textInputs.forEach((input, index) => {
            input.addEventListener('input', (e) => {
                if (this.listData.items[index]) {
                    this.listData.items[index].text = e.target.value;
                }
            });
        });

        // Contrôles de niveau
        const levelBtns = this.popup.querySelectorAll('.level-btn');
        levelBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                const index = parseInt(e.target.dataset.index);
                const action = e.target.classList.contains('increase') ? 'increase' : 'decrease';
                
                if (action === 'increase') {
                    this.increaseLevel(index);
                } else {
                    this.decreaseLevel(index);
                }
                this.updatePopup();
            });
        });

        // Supprimer un élément
        const removeBtns = this.popup.querySelectorAll('.remove-item-btn');
        removeBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                const index = parseInt(e.target.dataset.index);
                this.removeItem(index);
                this.updatePopup();
            });
        });

        // Ajouter un élément
        const addItemBtn = this.popup.querySelector('.add-item-btn');
        if (addItemBtn) {
            addItemBtn.addEventListener('click', () => {
                this.listData.items.push({
                    text: 'Nouvel élément',
                    level: 0
                });
                this.updatePopup();
            });
        }

        // Options de style
        const typeButtons = this.popup.querySelectorAll('.type-btn');
        const styleButtons = this.popup.querySelectorAll('.style-btn');
        const spacingButtons = this.popup.querySelectorAll('.spacing-btn');
        const bulletStyleSelect = this.popup.querySelector('.bullet-style-select');
        const alignmentButtons = this.popup.querySelectorAll('.alignment-btn');
        const colorInput = this.popup.querySelector('.list-color-input');

        typeButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                typeButtons.forEach(b => b.classList.remove('active'));
                e.target.classList.add('active');
                this.listData.type = e.target.dataset.type;
            });
        });

        styleButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                styleButtons.forEach(b => b.classList.remove('active'));
                e.target.classList.add('active');
                this.listData.style = e.target.dataset.style;
            });
        });

        spacingButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                spacingButtons.forEach(b => b.classList.remove('active'));
                e.target.classList.add('active');
                this.listData.spacing = e.target.dataset.spacing;
            });
        });

        bulletStyleSelect.addEventListener('change', (e) => {
            this.listData.bulletStyle = e.target.value;
        });

        alignmentButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                alignmentButtons.forEach(b => b.classList.remove('active'));
                e.target.classList.add('active');
                this.listData.alignment = e.target.dataset.alignment;
            });
        });

        colorInput.addEventListener('change', (e) => {
            this.listData.color = e.target.value;
        });

        // Sauvegarder
        const saveBtn = this.popup.querySelector('.save-popup-btn');
        if (saveBtn) {
            saveBtn.addEventListener('click', () => {
                this.saveFromPopup();
            });
        }

        // Fermer avec Escape
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.popup) {
                this.closePopup();
            }
        });
    }

    updatePopup() {
        if (!this.popup) return;
        
        // Mettre à jour la liste des éléments
        const container = this.popup.querySelector('.list-items-container');
        if (container) {
            container.innerHTML = this.listData.items.map((item, index) => `
                <div class="list-item-row" data-index="${index}">
                    <div class="item-controls">
                        <button type="button" class="level-btn decrease" data-index="${index}" ${item.level === 0 ? 'disabled' : ''}>⬅️</button>
                        <span class="level-indicator">Niv. ${item.level}</span>
                        <button type="button" class="level-btn increase" data-index="${index}" ${item.level === 3 ? 'disabled' : ''}>➡️</button>
                        <button type="button" class="remove-item-btn" data-index="${index}" ${this.listData.items.length === 1 ? 'disabled' : ''}>🗑️</button>
                    </div>
                    <input type="text" class="item-text-input" value="${item.text}" placeholder="Texte de l'élément...">
                </div>
            `).join('');
        }

        // Rebind les événements
        this.bindPopupEvents();
    }

    saveFromPopup() {
        // Mettre à jour les textes depuis les inputs
        const textInputs = this.popup.querySelectorAll('.item-text-input');
        textInputs.forEach((input, index) => {
            if (this.listData.items[index]) {
                this.listData.items[index].text = input.value;
            }
        });
        
        this.closePopup();
        this.displayList();
    }

    closePopup() {
        if (this.popup) {
            document.body.removeChild(this.popup);
            this.popup = null;
        }
    }

    displayList() {
        const content = this.element.querySelector('.module-content');
        if (!content) return;
        content.innerHTML = `<div class="list-display">${this.renderList()}</div>`;
        this.bindListEvents();
    }

    getContent() {
        const listClass = `list-${this.listData.style} list-${this.listData.spacing} list-${this.listData.bulletStyle} list-align-${this.listData.alignment}`;
        const style = `color: ${this.listData.color};`;
        
        let listHTML = `<${this.listData.type} class="list-container ${listClass}" style="${style}">`;
        
        this.listData.items.forEach(item => {
            const indentClass = `list-indent-${item.level}`;
            listHTML += `<li class="list-item ${indentClass}">${item.text}</li>`;
        });
        
        listHTML += `</${this.listData.type}>`;
        return listHTML;
    }

    getOptionsHTML() {
        return `
            <div class="list-options">
                <h4>Options de la liste</h4>
                <div class="option-group">
                    <label>Type :</label>
                    <p class="current-type">${this.listData.type === 'ul' ? 'À puces' : 'Numérotée'}</p>
                </div>
                <div class="option-group">
                    <label>Éléments :</label>
                    <p class="current-items">${this.listData.items.length} élément(s)</p>
                </div>
                <div class="option-group">
                    <label>Style :</label>
                    <p class="current-style">${this.listData.style}</p>
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
        const optionsContent = this.editor.optionsContent;
        if (!optionsContent) return;
        
        const editBtn = optionsContent.querySelector('.action-btn[data-action="edit"]');
        if (editBtn) {
            editBtn.addEventListener('click', () => {
                this.showListPopup();
            });
        }
    }

    loadData(data) {
        console.log('📂 Chargement des données liste:', data);
        
        // Appliquer les données au module
        this.listData = {
            ...this.listData,
            ...data
        };
        
        // Mettre à jour l'affichage si l'élément existe
        if (this.element) {
            this.displayList();
        }
        
        console.log('✅ Données liste chargées avec succès');
    }
}

// Rendre disponible globalement
window.ListModule = ListModule;
