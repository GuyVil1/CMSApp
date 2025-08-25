/**
 * Module Texte - Gestion du texte avec formatage
 */
class TextModule extends BaseModule {
    constructor(editor) {
        super('text', editor);
        this.content = 'Commencez à écrire votre texte ici...';
        this.formatting = {
            fontSize: '16px',
            color: '#000000',
            textAlign: 'left'
        };
    }

    render() {
        this.element.innerHTML = `
            <div class="module-header">
                <span class="module-type">📝 Texte</span>
                <div class="module-actions">
                    <button type="button" class="module-action" data-action="move-left" title="Déplacer à gauche">⬅️</button>
                    <button type="button" class="module-action" data-action="move-right" title="Déplacer à droite">➡️</button>
                    <button type="button" class="module-action" data-action="move-up" title="Déplacer vers le haut">⬆️</button>
                    <button type="button" class="module-action" data-action="move-down" title="Déplacer vers le bas">⬇️</button>
                    <button type="button" class="module-action" data-action="delete" title="Supprimer">🗑️</button>
                </div>
            </div>
            <div class="module-content" contenteditable="true" style="color: var(--text) !important;">
                <p style="color: var(--text) !important; margin: 0;">${this.content}</p>
            </div>
        `;
    }

    bindEvents() {
        super.bindEvents();
        this.bindTextEvents();
    }

    bindTextEvents() {
        const content = this.element.querySelector('.module-content');
        if (!content) return;

        content.addEventListener('focus', (e) => {
            if (content.textContent === 'Commencez à écrire votre texte ici...') {
                content.innerHTML = '<p style="color: var(--text) !important; margin: 0;"></p>';
            }
        });

        content.addEventListener('blur', (e) => {
            if (content.textContent.trim() === '') {
                content.innerHTML = '<p style="color: var(--text) !important; margin: 0;">Commencez à écrire votre texte ici...</p>';
            }
        });

        // Gestionnaire pour nettoyer le contenu collé
        content.addEventListener('paste', (e) => {
            e.preventDefault();
            const text = e.clipboardData.getData('text/plain');
            document.execCommand('insertText', false, text);
        });

        // Sauvegarder le contenu lors des modifications
        content.addEventListener('input', (e) => {
            this.content = content.innerHTML;
        });

        // S'assurer que le contenu est éditable
        content.setAttribute('contenteditable', 'true');
        content.style.outline = 'none';
    }

    getContent() {
        return this.content;
    }

    getOptionsHTML() {
        return `
            <div class="text-options">
                <h4>Formatage du texte</h4>
                <div class="option-group">
                    <label>Style :</label>
                    <div class="format-buttons">
                        <button type="button" class="format-btn" data-command="bold">B</button>
                        <button type="button" class="format-btn" data-command="italic">I</button>
                        <button type="button" class="format-btn" data-command="underline">U</button>
                    </div>
                </div>
                <div class="option-group">
                    <label>Taille :</label>
                    <select class="font-size-select">
                        <option value="12px">12px</option>
                        <option value="14px">14px</option>
                        <option value="16px" selected>16px</option>
                        <option value="18px">18px</option>
                        <option value="20px">20px</option>
                        <option value="24px">24px</option>
                    </select>
                </div>
                <div class="option-group">
                    <label>Couleur :</label>
                    <input type="color" class="color-picker" value="#000000">
                </div>
                <div class="option-group">
                    <label>Alignement :</label>
                    <div class="align-buttons">
                        <button type="button" class="align-btn" data-align="left">⬅️</button>
                        <button type="button" class="align-btn" data-align="center">↔️</button>
                        <button type="button" class="align-btn" data-align="right">➡️</button>
                    </div>
                </div>
            </div>
        `;
    }

    bindOptionsEvents() {
        const optionsContent = this.editor.optionsContent;
        if (!optionsContent) return;

        // Formatage du texte
        optionsContent.addEventListener('click', (e) => {
            const formatBtn = e.target.closest('.format-btn');
            if (formatBtn) {
                const command = formatBtn.dataset.command;
                const content = this.element.querySelector('.module-content');
                
                // S'assurer que le contenu est focalisé
                content.focus();
                
                // Attendre un peu pour que le focus soit établi
                setTimeout(() => {
                    // Vérifier si du texte est sélectionné
                    const selection = window.getSelection();
                    if (selection.rangeCount > 0) {
                        const range = selection.getRangeAt(0);
                        if (!range.collapsed) {
                            // Appliquer le style à la sélection
                            document.execCommand(command, false, null);
                            formatBtn.classList.toggle('active');
                        } else {
                            // Aucune sélection, appliquer au contenu entier
                            document.execCommand(command, false, null);
                            formatBtn.classList.toggle('active');
                        }
                    } else {
                        // Aucune sélection, appliquer au contenu entier
                        document.execCommand(command, false, null);
                        formatBtn.classList.toggle('active');
                    }
                }, 10);
            }

            const alignBtn = e.target.closest('.align-btn');
            if (alignBtn) {
                const align = alignBtn.dataset.align;
                const content = this.element.querySelector('.module-content');

                optionsContent.querySelectorAll('.align-btn').forEach(btn => {
                    btn.classList.remove('active');
                });

                alignBtn.classList.add('active');
                content.style.textAlign = align;
                this.formatting.textAlign = align;
            }
        });

        // Taille de police
        const fontSizeSelect = optionsContent.querySelector('.font-size-select');
        if (fontSizeSelect) {
            fontSizeSelect.addEventListener('change', (e) => {
                const content = this.element.querySelector('.module-content');
                content.focus();
                const size = e.target.value;
                const selection = window.getSelection();

                if (selection.rangeCount > 0) {
                    const range = selection.getRangeAt(0);
                    if (!range.collapsed) {
                        this.applyFontSizeToSelection(size);
                    } else {
                        this.applyFontSizeToContent(content, size);
                    }
                } else {
                    this.applyFontSizeToContent(content, size);
                }
                this.formatting.fontSize = size;
            });
        }

        // Couleur
        const colorPicker = optionsContent.querySelector('.color-picker');
        if (colorPicker) {
            colorPicker.addEventListener('change', (e) => {
                const content = this.element.querySelector('.module-content');
                content.focus();
                const color = e.target.value;
                const selection = window.getSelection();

                if (selection.rangeCount > 0) {
                    const range = selection.getRangeAt(0);
                    if (!range.collapsed) {
                        this.applyColorToSelection(color);
                    } else {
                        // Aucune sélection, appliquer à tout le contenu
                        this.applyColorToContent(content, color);
                    }
                } else {
                    // Aucune sélection, appliquer à tout le contenu
                    this.applyColorToContent(content, color);
                }
                this.formatting.color = color;
            });
        }
    }

    applyFontSizeToSelection(size) {
        const selection = window.getSelection();
        if (selection.rangeCount > 0) {
            const range = selection.getRangeAt(0);

            try {
                const span = document.createElement('span');
                span.style.setProperty('font-size', size, 'important');
                span.style.setProperty('color', 'inherit', 'important');
                range.surroundContents(span);
                span.offsetHeight;
            } catch (error) {
                const fragment = range.extractContents();
                const span = document.createElement('span');
                span.style.setProperty('font-size', size, 'important');
                span.style.setProperty('color', 'inherit', 'important');
                span.appendChild(fragment);
                range.insertNode(span);
                span.offsetHeight;
            }
        }
    }

    applyFontSizeToContent(content, size) {
        const elementsWithFontSize = content.querySelectorAll('[style*="font-size"]');
        elementsWithFontSize.forEach(el => {
            el.style.fontSize = '';
            el.style.removeProperty('font-size');
        });

        content.style.setProperty('font-size', size, 'important');

        const textElements = content.querySelectorAll('p, span, div, h1, h2, h3, h4, h5, h6');
        textElements.forEach(el => {
            if (el === content) return;
            el.style.setProperty('font-size', size, 'important');
        });

        content.offsetHeight;
    }

    applyColorToSelection(color) {
        const selection = window.getSelection();
        if (selection.rangeCount > 0) {
            const range = selection.getRangeAt(0);

            const moduleContent = this.element?.querySelector('.module-content');
            if (!moduleContent || !moduleContent.contains(range.commonAncestorContainer)) {
                return;
            }

            if (range.collapsed) {
                return;
            }

            const selectedText = range.toString().trim();
            if (!selectedText) {
                return;
            }

            try {
                const span = document.createElement('span');
                span.style.setProperty('color', color, 'important');
                span.style.setProperty('background', 'transparent', 'important');
                range.surroundContents(span);
                span.offsetHeight;
            } catch (error) {
                // Si surroundContents échoue, utiliser extractContents
                const fragment = range.extractContents();
                const span = document.createElement('span');
                span.style.setProperty('color', color, 'important');
                span.style.setProperty('background', 'transparent', 'important');
                span.appendChild(fragment);
                range.insertNode(span);
                span.offsetHeight;
            }
            
            // Mettre à jour le contenu sauvegardé
            this.content = moduleContent.innerHTML;
        }
    }

    applyColorToContent(content, color) {
        // Supprimer les couleurs existantes sur les éléments enfants
        const elementsWithColor = content.querySelectorAll('[style*="color"]');
        elementsWithColor.forEach(el => {
            el.style.color = '';
            el.style.removeProperty('color');
        });

        // Appliquer la couleur au contenu principal
        content.style.setProperty('color', color, 'important');

        // Appliquer la couleur aux éléments de texte enfants
        const textElements = content.querySelectorAll('p, span, div, h1, h2, h3, h4, h5, h6');
        textElements.forEach(el => {
            if (el === content) return;
            el.style.setProperty('color', color, 'important');
        });

        content.offsetHeight;
        
        // Mettre à jour le contenu sauvegardé
        this.content = content.innerHTML;
    }
}

// Rendre disponible globalement
window.TextModule = TextModule;
