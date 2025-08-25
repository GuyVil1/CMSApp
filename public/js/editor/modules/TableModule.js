/**
 * Module Tableau - Gestion des tableaux de données
 */
class TableModule extends BaseModule {
    constructor(editor) {
        super('table', editor);
        this.tableData = {
            rows: 3,
            columns: 3,
            headers: true,
            style: 'default', // default, striped, bordered, compact, modern
            alignment: 'left', // left, center, right
            width: '100', // pourcentage
            cellPadding: '8', // pixels
            borderWidth: '1', // pixels
            borderColor: '#dee2e6',
            headerColor: '#f8f9fa',
            stripedColor: '#f8f9fa'
        };
        this.cells = []; // Données des cellules
        this.mergedCells = new Map(); // Cellules fusionnées
    }

    render() {
        if (this.cells.length === 0) {
            this.initializeCells();
        }

        this.element.innerHTML = `
            <div class="module-header">
                <span class="module-type">📊 Tableau</span>
                <div class="module-actions">
                    <button type="button" class="module-action" data-action="move-left">⬅️</button>
                    <button type="button" class="module-action" data-action="move-right">➡️</button>
                    <button type="button" class="module-action" data-action="delete">🗑️</button>
                </div>
            </div>
            <div class="module-content">
                <div class="table-display">
                    ${this.renderTable()}
                </div>
            </div>
        `;

        this.bindEvents();
    }

    bindEvents() {
        const tableDisplay = this.element.querySelector('.table-display');
        if (tableDisplay) {
            tableDisplay.addEventListener('click', () => {
                // Les options sont maintenant directement dans la barre d'options
                // Pas besoin d'ouvrir un éditeur séparé
            });
        }
    }

    renderTable() {
        if (this.cells.length === 0) {
            this.initializeCells();
        }

        let tableHTML = `<table class="table table-${this.tableData.style}" style="width: ${this.tableData.width}%; text-align: ${this.tableData.alignment}; border-collapse: collapse;">`;
        
        for (let row = 0; row < this.tableData.rows; row++) {
            tableHTML += '<tr>';
            for (let col = 0; col < this.tableData.columns; col++) {
                const cellContent = this.getCell(row, col) || '';
                const isHeader = this.tableData.headers && row === 0;
                const cellType = isHeader ? 'th' : 'td';
                
                const cellStyle = `
                    padding: ${this.tableData.cellPadding}px;
                    border: ${this.tableData.borderWidth}px solid ${this.tableData.borderColor};
                    ${isHeader ? `background-color: ${this.tableData.headerColor};` : ''}
                    ${this.tableData.style === 'striped' && row % 2 === 1 ? `background-color: ${this.tableData.stripedColor};` : ''}
                `;
                
                tableHTML += `<${cellType} style="${cellStyle}">${cellContent}</${cellType}>`;
            }
            tableHTML += '</tr>';
        }
        
        tableHTML += '</table>';
        return tableHTML;
    }

    getCell(row, col) {
        const index = row * this.tableData.columns + col;
        return this.cells[index] || '';
    }

    setCell(row, col, content) {
        const index = row * this.tableData.columns + col;
        this.cells[index] = content;
    }

    initializeCells() {
        this.cells = [];
        for (let row = 0; row < this.tableData.rows; row++) {
            for (let col = 0; col < this.tableData.columns; col++) {
                if (this.tableData.headers && row === 0) {
                    this.cells.push(`En-tête ${col + 1}`);
                } else {
                    this.cells.push(`Cellule ${row + 1}-${col + 1}`);
                }
            }
        }
    }

    displayTable() {
        const tableDisplay = this.element.querySelector('.table-display');
        if (tableDisplay) {
            tableDisplay.innerHTML = this.renderTable();
        }
    }

    getContent() {
        return this.renderTable();
    }

    getOptionsHTML() {
        return `
            <div class="table-options">
                <h4>📊 Options du tableau</h4>
                
                <!-- Configuration du tableau -->
                <div class="option-group">
                    <label>Configuration:</label>
                    <div class="config-controls">
                        <div class="control-item">
                            <label for="table-rows">Lignes:</label>
                            <input type="number" id="table-rows" min="1" max="20" value="${this.tableData.rows}">
                        </div>
                        <div class="control-item">
                            <label for="table-columns">Colonnes:</label>
                            <input type="number" id="table-columns" min="1" max="10" value="${this.tableData.columns}">
                        </div>
                        <div class="control-item">
                            <label>
                                <input type="checkbox" id="table-headers" ${this.tableData.headers ? 'checked' : ''}>
                                En-têtes
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Éditeur de cellules -->
                <div class="option-group">
                    <label>Contenu des cellules:</label>
                    <div class="cell-editor">
                        <div class="cell-editor-header">
                            <span>Ligne</span>
                            <span>Colonne</span>
                            <span>Contenu</span>
                        </div>
                        <div class="cell-editor-content">
                            ${this.renderCellEditor()}
                        </div>
                    </div>
                </div>

                <!-- Styles du tableau -->
                <div class="option-group">
                    <label>Style:</label>
                    <div class="style-buttons">
                        <button type="button" class="style-btn ${this.tableData.style === 'default' ? 'active' : ''}" data-style="default">Par défaut</button>
                        <button type="button" class="style-btn ${this.tableData.style === 'striped' ? 'active' : ''}" data-style="striped">Rayé</button>
                        <button type="button" class="style-btn ${this.tableData.style === 'bordered' ? 'active' : ''}" data-style="bordered">Bordé</button>
                        <button type="button" class="style-btn ${this.tableData.style === 'compact' ? 'active' : ''}" data-style="compact">Compact</button>
                        <button type="button" class="style-btn ${this.tableData.style === 'modern' ? 'active' : ''}" data-style="modern">Moderne</button>
                    </div>
                </div>

                <!-- Alignement -->
                <div class="option-group">
                    <label>Alignement:</label>
                    <div class="align-buttons">
                        <button type="button" class="align-btn ${this.tableData.alignment === 'left' ? 'active' : ''}" data-align="left">⬅️ Gauche</button>
                        <button type="button" class="align-btn ${this.tableData.alignment === 'center' ? 'active' : ''}" data-align="center">⬆️ Centre</button>
                        <button type="button" class="align-btn ${this.tableData.alignment === 'right' ? 'active' : ''}" data-align="right">➡️ Droite</button>
                    </div>
                </div>

                <!-- Apparence -->
                <div class="option-group">
                    <label>Largeur du tableau:</label>
                    <div class="slider-control">
                        <input type="range" id="table-width" min="50" max="100" value="${this.tableData.width}">
                        <span id="width-value">${this.tableData.width}%</span>
                    </div>
                </div>

                <div class="option-group">
                    <label>Espacement des cellules:</label>
                    <div class="slider-control">
                        <input type="range" id="cell-padding" min="4" max="20" value="${this.tableData.cellPadding}">
                        <span id="padding-value">${this.tableData.cellPadding}px</span>
                    </div>
                </div>

                <div class="option-group">
                    <label>Épaisseur des bordures:</label>
                    <div class="slider-control">
                        <input type="range" id="border-width" min="0" max="5" value="${this.tableData.borderWidth}">
                        <span id="border-value">${this.tableData.borderWidth}px</span>
                    </div>
                </div>

                <div class="option-group">
                    <label>Couleur des bordures:</label>
                    <input type="color" id="border-color" value="${this.tableData.borderColor}">
                </div>

                <div class="option-group">
                    <label>Couleur des en-têtes:</label>
                    <input type="color" id="header-color" value="${this.tableData.headerColor}">
                </div>

                <div class="option-group">
                    <label>Couleur des lignes alternées:</label>
                    <input type="color" id="striped-color" value="${this.tableData.stripedColor}">
                </div>

                <!-- Actions -->
                <div class="option-group">
                    <div class="action-buttons">
                        <button type="button" class="action-btn primary" data-action="save">
                            <span class="icon">💾</span> Sauvegarder
                        </button>
                        <button type="button" class="action-btn" data-action="preview">
                            <span class="icon">👁️</span> Aperçu
                        </button>
                    </div>
                </div>
            </div>
        `;
    }

    renderCellEditor() {
        let html = '';
        for (let row = 0; row < this.tableData.rows; row++) {
            for (let col = 0; col < this.tableData.columns; col++) {
                const cellContent = this.getCell(row, col) || '';
                const isHeader = this.tableData.headers && row === 0;
                const cellType = isHeader ? 'En-tête' : 'Cellule';
                
                html += `
                    <div class="cell-editor-row ${isHeader ? 'header-row' : ''}" data-row="${row}" data-col="${col}">
                        <span class="cell-label">${cellType} ${row + 1}-${col + 1}</span>
                        <textarea class="cell-content" 
                                  data-row="${row}" 
                                  data-col="${col}" 
                                  placeholder="Contenu de la ${cellType.toLowerCase()} ${row + 1}-${col + 1}">${cellContent}</textarea>
                    </div>
                `;
            }
        }
        return html;
    }

    getStyleName(style) {
        const names = {
            'default': 'Par défaut',
            'striped': 'Rayé',
            'bordered': 'Bordé',
            'compact': 'Compact',
            'modern': 'Moderne'
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

        // Configuration du tableau
        const rowsInput = optionsContent.querySelector('#table-rows');
        const columnsInput = optionsContent.querySelector('#table-columns');
        const headersCheckbox = optionsContent.querySelector('#table-headers');

        if (rowsInput) {
            rowsInput.addEventListener('change', (e) => {
                this.tableData.rows = parseInt(e.target.value);
                this.updateCellEditor();
            });
        }

        if (columnsInput) {
            columnsInput.addEventListener('change', (e) => {
                this.tableData.columns = parseInt(e.target.value);
                this.updateCellEditor();
            });
        }

        if (headersCheckbox) {
            headersCheckbox.addEventListener('change', (e) => {
                this.tableData.headers = e.target.checked;
                this.updateCellEditor();
            });
        }

        // Édition des cellules
        this.bindCellEditorEvents();

        // Styles du tableau
        const styleButtons = optionsContent.querySelectorAll('.style-btn');
        styleButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                styleButtons.forEach(b => b.classList.remove('active'));
                e.target.classList.add('active');
                this.tableData.style = e.target.dataset.style;
            });
        });

        // Alignement
        const alignButtons = optionsContent.querySelectorAll('.align-btn');
        alignButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                alignButtons.forEach(b => b.classList.remove('active'));
                e.target.classList.add('active');
                this.tableData.alignment = e.target.dataset.align;
            });
        });

        // Contrôles d'apparence
        const widthSlider = optionsContent.querySelector('#table-width');
        const paddingSlider = optionsContent.querySelector('#cell-padding');
        const borderSlider = optionsContent.querySelector('#border-width');
        const borderColor = optionsContent.querySelector('#border-color');
        const headerColor = optionsContent.querySelector('#header-color');
        const stripedColor = optionsContent.querySelector('#striped-color');

        if (widthSlider) {
            widthSlider.addEventListener('input', (e) => {
                this.tableData.width = e.target.value;
                optionsContent.querySelector('#width-value').textContent = e.target.value + '%';
            });
        }

        if (paddingSlider) {
            paddingSlider.addEventListener('input', (e) => {
                this.tableData.cellPadding = e.target.value;
                optionsContent.querySelector('#padding-value').textContent = e.target.value + 'px';
            });
        }

        if (borderSlider) {
            borderSlider.addEventListener('input', (e) => {
                this.tableData.borderWidth = e.target.value;
                optionsContent.querySelector('#border-value').textContent = e.target.value + 'px';
            });
        }

        if (borderColor) {
            borderColor.addEventListener('change', (e) => {
                this.tableData.borderColor = e.target.value;
            });
        }

        if (headerColor) {
            headerColor.addEventListener('change', (e) => {
                this.tableData.headerColor = e.target.value;
            });
        }

        if (stripedColor) {
            stripedColor.addEventListener('change', (e) => {
                this.tableData.stripedColor = e.target.value;
            });
        }

        // Actions
        const saveBtn = optionsContent.querySelector('.action-btn[data-action="save"]');
        const previewBtn = optionsContent.querySelector('.action-btn[data-action="preview"]');

        if (saveBtn) {
            saveBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                this.saveTableFromOptions();
            });
        }

        if (previewBtn) {
            previewBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                this.showTablePreview();
            });
        }
    }

    bindCellEditorEvents() {
        const optionsContent = this.editor.optionsContent;
        if (!optionsContent) return;

        const cellTextareas = optionsContent.querySelectorAll('.cell-content');
        cellTextareas.forEach(textarea => {
            textarea.addEventListener('input', (e) => {
                const row = parseInt(e.target.dataset.row);
                const col = parseInt(e.target.dataset.col);
                this.setCell(row, col, e.target.value);
            });
        });
    }

    updateCellEditor() {
        const optionsContent = this.editor.optionsContent;
        if (!optionsContent) return;

        const cellEditorContent = optionsContent.querySelector('.cell-editor-content');
        if (cellEditorContent) {
            cellEditorContent.innerHTML = this.renderCellEditor();
            this.bindCellEditorEvents();
        }
    }

    saveTableFromOptions() {
        // Sauvegarder le contenu des cellules depuis les textareas
        const optionsContent = this.editor.optionsContent;
        if (optionsContent) {
            const cellTextareas = optionsContent.querySelectorAll('.cell-content');
            cellTextareas.forEach(textarea => {
                const row = parseInt(textarea.dataset.row);
                const col = parseInt(textarea.dataset.col);
                this.setCell(row, col, textarea.value);
            });
        }

        // Mettre à jour l'affichage du tableau
        this.displayTable();
        
        // Fermer les options
        this.editor.hideOptions();
    }

    showTablePreview() {
        // Sauvegarder le contenu des cellules depuis les textareas
        const optionsContent = this.editor.optionsContent;
        if (optionsContent) {
            const cellTextareas = optionsContent.querySelectorAll('.cell-content');
            cellTextareas.forEach(textarea => {
                const row = parseInt(textarea.dataset.row);
                const col = parseInt(textarea.dataset.col);
                this.setCell(row, col, textarea.value);
            });
        }

        // Afficher un aperçu dans une popup
        const previewHTML = `
            <div style="padding: 20px; background: white; border-radius: 8px; max-width: 800px; max-height: 600px; overflow: auto;">
                <h3>Aperçu du tableau</h3>
                ${this.renderTable()}
            </div>
        `;

        const popup = window.open('', '_blank', 'width=900,height=700');
        popup.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>Aperçu du tableau</title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background: #f5f5f5; }
                    table { border-collapse: collapse; width: 100%; }
                    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                    th { background-color: #f2f2f2; font-weight: bold; }
                    .table-striped tr:nth-child(even) { background-color: #f9f9f9; }
                    .table-bordered th, .table-bordered td { border: 2px solid #ddd; }
                    .table-compact th, .table-compact td { padding: 4px; }
                    .table-modern { border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
                    .table-modern th { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
                </style>
            </head>
            <body>
                ${previewHTML}
            </body>
            </html>
        `);
        popup.document.close();
    }

    loadData(data) {
        console.log('📂 Chargement des données tableau:', data);
        
        // Appliquer les données au module
        this.tableData = {
            ...this.tableData,
            ...data
        };
        
        // Mettre à jour l'affichage si l'élément existe
        if (this.element) {
            this.displayTable();
        }
        
        console.log('✅ Données tableau chargées avec succès');
    }
}

// Rendre disponible globalement
window.TableModule = TableModule;
