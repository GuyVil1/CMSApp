/**
 * Gestionnaire de styles pour l'éditeur
 */
class StyleManager {
    constructor() {
        this.stylesAdded = false;
    }

    addStyles() {
        if (this.stylesAdded) return;
        
        const styles = `
            <style>
                :root {
                    --belgium-red: #FF0000;
                    --belgium-yellow: #FFD700;
                    --belgium-black: #000000;
                    --primary: #1a1a1a;
                    --secondary: #2d2d2d;
                    --text: #ffffff;
                    --text-muted: #a0a0a0;
                    --border: #404040;
                    --success: #44ff44;
                    --error: #ff4444;
                    --warning: #ffaa00;
                    --bg-light: rgba(255, 255, 255, 0.05);
                    --bg-hover: rgba(255, 255, 255, 0.1);
                }
                
                /* Reset et base */
                .fullscreen-editor-modal * {
                    box-sizing: border-box;
                }
                
                /* Forcer le thème sombre sur tous les éléments */
                .fullscreen-editor-modal,
                .fullscreen-editor-modal *,
                .fullscreen-editor-container,
                .fullscreen-editor-container * {
                    background-color: var(--primary) !important;
                    color: var(--text) !important;
                }
                
                .fullscreen-editor-modal {
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100vw;
                    height: 100vh;
                    background: rgba(0, 0, 0, 0.9);
                    display: none;
                    z-index: 10000;
                    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                }
                
                .fullscreen-editor-container {
                    width: 100%;
                    height: 100%;
                    display: flex;
                    flex-direction: column;
                    background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
                    color: var(--text);
                }
                
                /* Header de l'éditeur */
                .editor-header {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    padding: 1rem 2rem;
                    background: var(--bg-light) !important;
                    border-bottom: 2px solid var(--border);
                    backdrop-filter: blur(10px);
                }
                
                .header-left h2 {
                    color: var(--belgium-yellow) !important;
                    margin: 0;
                    font-size: 1.5rem;
                    font-weight: 700;
                    text-shadow: 0 0 10px rgba(255, 215, 0, 0.5);
                }
                
                .header-center {
                    display: flex;
                    gap: 1rem;
                }
                
                .header-btn {
                    display: flex;
                    align-items: center;
                    gap: 0.5rem;
                    padding: 0.75rem 1.5rem;
                    border: 1px solid var(--border);
                    background: var(--bg-light);
                    color: var(--text);
                    cursor: pointer;
                    border-radius: 8px;
                    transition: all 0.3s ease;
                    font-weight: 600;
                    font-size: 0.9rem;
                }
                
                .header-btn:hover {
                    background: var(--belgium-red);
                    color: white;
                    border-color: var(--belgium-red);
                    transform: translateY(-2px);
                    box-shadow: 0 4px 12px rgba(255, 0, 0, 0.3);
                }
                
                .close-btn {
                    background: var(--error);
                    color: white;
                    border-color: var(--error);
                    padding: 0.75rem;
                    border-radius: 8px;
                }
                
                /* Corps de l'éditeur */
                .editor-body {
                    display: flex;
                    flex: 1;
                    overflow: hidden;
                }
                
                /* Barres latérales */
                .sidebar-left,
                .sidebar-right {
                    background: var(--bg-light) !important;
                    border: 1px solid var(--border);
                    overflow-y: auto;
                }
                
                .sidebar-left {
                    width: 280px;
                    border-right: 2px solid var(--border);
                }
                
                .sidebar-right {
                    width: 320px;
                    border-left: 2px solid var(--border);
                }
                
                .sidebar-section {
                    padding: 1.5rem;
                    border-bottom: 1px solid var(--border);
                }
                
                .sidebar-section h3 {
                    margin: 0 0 1rem 0;
                    font-size: 1.1rem;
                    color: var(--belgium-yellow);
                    font-weight: 600;
                }
                
                /* Boutons de modules */
                .module-buttons {
                    display: flex;
                    flex-direction: column;
                    gap: 0.75rem;
                }
                
                .module-btn {
                    display: flex;
                    align-items: center;
                    gap: 0.75rem;
                    padding: 1rem;
                    border: 1px solid var(--border);
                    background: var(--bg-light) !important;
                    color: var(--text) !important;
                    cursor: pointer;
                    border-radius: 8px;
                    transition: all 0.3s ease;
                    text-align: left;
                    font-weight: 500;
                }
                
                .module-btn:hover {
                    background: var(--belgium-red);
                    color: white;
                    border-color: var(--belgium-red);
                    transform: translateY(-2px);
                    box-shadow: 0 4px 12px rgba(255, 0, 0, 0.3);
                }
                
                /* Boutons de disposition */
                .layout-buttons {
                    display: flex;
                    gap: 0.75rem;
                }
                
                .layout-btn {
                    padding: 0.75rem;
                    border: 1px solid var(--border);
                    background: var(--bg-light);
                    cursor: pointer;
                    border-radius: 8px;
                    transition: all 0.3s ease;
                }
                
                .layout-btn:hover,
                .layout-btn.active {
                    background: var(--belgium-yellow);
                    border-color: var(--belgium-yellow);
                    transform: translateY(-2px);
                    box-shadow: 0 4px 12px rgba(255, 215, 0, 0.3);
                }
                
                .layout-preview {
                    width: 40px;
                    height: 20px;
                    background: var(--border);
                    border-radius: 4px;
                }
                
                .layout-preview.single {
                    background: var(--belgium-yellow);
                }
                
                .layout-preview.double {
                    background: linear-gradient(to right, var(--belgium-yellow) 50%, var(--border) 50%);
                }
                
                .layout-preview.triple {
                    background: linear-gradient(to right, var(--belgium-yellow) 33%, var(--border) 33%, var(--border) 66%, var(--belgium-yellow) 66%);
                }
                
                .section-actions {
                    margin-top: 1rem;
                    padding-top: 1rem;
                    border-top: 1px solid var(--border);
                }
                
                .add-section-btn {
                    display: flex;
                    align-items: center;
                    gap: 0.5rem;
                    width: 100%;
                    padding: 0.75rem 1rem;
                    border: 2px dashed var(--border);
                    background: transparent;
                    color: var(--text-muted);
                    cursor: pointer;
                    border-radius: 8px;
                    transition: all 0.3s ease;
                    font-weight: 500;
                    font-size: 0.9rem;
                }
                
                .add-section-btn:hover {
                    border-color: var(--belgium-yellow);
                    color: var(--belgium-yellow);
                    background: rgba(255, 215, 0, 0.1);
                }
                
                /* Zone principale d'édition */
                .editor-main {
                    flex: 1;
                    background: var(--primary);
                    overflow: hidden;
                }
                
                .editor-content {
                    height: 100%;
                    padding: 2rem;
                    overflow-y: auto;
                    background: var(--primary);
                }
                
                /* Sections de contenu */
                .content-sections {
                    display: flex;
                    flex-direction: column;
                    gap: 2rem;
                }
                
                .content-section {
                    border: 2px solid var(--border);
                    border-radius: 12px;
                    overflow: hidden;
                    background: var(--primary);
                    transition: all 0.3s ease;
                }
                
                .content-section:hover {
                    border-color: var(--belgium-yellow);
                    box-shadow: 0 8px 25px rgba(255, 215, 0, 0.2);
                }
                
                .content-section.selected {
                    border-color: var(--belgium-red);
                    box-shadow: 0 0 0 3px rgba(255, 0, 0, 0.3);
                }
                
                .section-header {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    padding: 1rem 1.5rem;
                    background: var(--bg-hover);
                    border-bottom: 1px solid var(--border);
                }
                
                .section-info {
                    display: flex;
                    flex-direction: column;
                    gap: 0.25rem;
                }
                
                .section-label {
                    font-size: 1rem;
                    font-weight: 600;
                    color: var(--belgium-yellow);
                }
                
                .section-layout {
                    font-size: 0.8rem;
                    color: var(--text-muted);
                }
                
                .section-actions {
                    display: flex;
                    gap: 0.5rem;
                }
                
                .section-action {
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    width: 32px;
                    height: 32px;
                    border: 1px solid var(--border);
                    background: transparent;
                    color: var(--text-muted);
                    cursor: pointer;
                    border-radius: 6px;
                    transition: all 0.3s ease;
                    font-size: 0.9rem;
                }
                
                .section-action:hover {
                    background: var(--belgium-red);
                    color: white;
                    border-color: var(--belgium-red);
                }
                
                .section-action[data-action="add-column"]:hover {
                    background: var(--success);
                    border-color: var(--success);
                }
                
                .section-action[data-action="remove-column"]:hover {
                    background: var(--warning);
                    border-color: var(--warning);
                }
                
                .section-action[data-action="move-up"]:hover,
                .section-action[data-action="move-down"]:hover {
                    background: var(--belgium-yellow);
                    color: var(--primary);
                    border-color: var(--belgium-yellow);
                }
                
                .section-action[data-action="delete-section"]:hover {
                    background: var(--error);
                    border-color: var(--error);
                }
                
                /* Colonnes */
                .content-columns {
                    height: 100%;
                }
                
                .content-columns[data-columns="1"] {
                    display: block;
                }
                
                .content-columns[data-columns="2"] {
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                    gap: 2rem;
                }
                
                .content-columns[data-columns="3"] {
                    display: grid;
                    grid-template-columns: 1fr 1fr 1fr;
                    gap: 2rem;
                }
                
                .content-column {
                    min-height: 300px;
                    border: 2px dashed var(--border);
                    border-radius: 12px;
                    padding: 2rem;
                    transition: all 0.3s ease;
                    background: var(--primary);
                }
                
                .content-column:hover {
                    border-color: var(--belgium-yellow);
                    box-shadow: 0 8px 25px rgba(255, 215, 0, 0.2);
                }
                
                .column-placeholder {
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    justify-content: center;
                    height: 100%;
                    color: var(--text-muted);
                    text-align: center;
                }
                
                .placeholder-icon {
                    font-size: 4rem;
                    margin-bottom: 1rem;
                    opacity: 0.5;
                }
                
                .placeholder-text {
                    font-size: 1.1rem;
                    font-weight: 500;
                }
                
                /* Modules de contenu */
                .content-module {
                    margin-bottom: 1.5rem;
                    border: 2px solid var(--border);
                    border-radius: 10px;
                    overflow: hidden;
                    transition: all 0.3s ease;
                    background: var(--primary);
                }
                
                .content-module:hover {
                    border-color: var(--belgium-yellow);
                    box-shadow: 0 8px 25px rgba(255, 215, 0, 0.2);
                }
                
                .content-module.selected {
                    border-color: var(--belgium-red);
                    box-shadow: 0 0 0 3px rgba(255, 0, 0, 0.3);
                }
                
                .module-header {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    padding: 1rem 1.5rem;
                    background: var(--bg-hover);
                    border-bottom: 1px solid var(--border);
                }
                
                .module-type {
                    font-size: 0.9rem;
                    font-weight: 600;
                    color: var(--belgium-yellow);
                }
                
                .module-actions {
                    display: flex;
                    gap: 0.25rem;
                    flex-wrap: wrap;
                }
                
                .module-action {
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    width: 28px;
                    height: 28px;
                    border: 1px solid var(--border);
                    background: transparent;
                    color: var(--text-muted);
                    cursor: pointer;
                    border-radius: 4px;
                    transition: all 0.3s ease;
                    font-size: 0.8rem;
                }
                
                .module-action:hover {
                    background: var(--belgium-red);
                    color: white;
                    border-color: var(--belgium-red);
                }
                
                /* Styles pour le drag & drop */
                .content-module.dragging {
                    opacity: 0.5;
                    transform: rotate(2deg);
                    z-index: 1000;
                    cursor: grabbing;
                    box-shadow: 0 8px 25px rgba(255, 0, 0, 0.3);
                }
                
                .content-module.drop-target {
                    border-color: var(--belgium-yellow);
                    box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.5);
                    background: rgba(255, 215, 0, 0.15);
                    transform: scale(1.02);
                }
                
                .content-module.drop-target::before {
                    content: '';
                    position: absolute;
                    top: -2px;
                    left: 0;
                    right: 0;
                    height: 4px;
                    background: var(--belgium-yellow);
                    border-radius: 2px;
                    z-index: 1001;
                }
                
                .content-module.drop-target::after {
                    content: '';
                    position: absolute;
                    bottom: -2px;
                    left: 0;
                    right: 0;
                    height: 4px;
                    background: var(--belgium-yellow);
                    border-radius: 2px;
                    z-index: 1001;
                }
                
                .content-module.drop-target::before {
                    content: '';
                    position: absolute;
                    top: -2px;
                    left: 0;
                    right: 0;
                    height: 4px;
                    background: var(--belgium-yellow);
                    border-radius: 2px;
                    z-index: 1001;
                }
                
                .content-module.drop-target::after {
                    content: '';
                    position: absolute;
                    bottom: -2px;
                    left: 0;
                    right: 0;
                    height: 4px;
                    background: var(--belgium-yellow);
                    border-radius: 2px;
                    z-index: 1001;
                }
                
                .content-module {
                    position: relative;
                    cursor: grab;
                }
                
                .content-module:active {
                    cursor: grabbing;
                }
                
                .content-module .module-actions {
                    cursor: default;
                }
                
                .content-module .module-actions * {
                    cursor: pointer;
                }
                
                /* Styles pour les zones de drop des colonnes */
                .content-column.drop-zone {
                    border-color: var(--belgium-yellow);
                    background: rgba(255, 215, 0, 0.1);
                    box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.3);
                }
                
                .content-column.drop-zone .column-placeholder {
                    color: var(--belgium-yellow);
                }
                
                .content-column.drop-zone .placeholder-icon {
                    opacity: 1;
                    transform: scale(1.2);
                }
                
                .module-content {
                    padding: 1.5rem;
                    min-height: 60px;
                    background: var(--primary) !important;
                    color: var(--text) !important;
                    font-size: 16px;
                    line-height: 1.7;
                }
                
                .module-content[contenteditable="true"] {
                    outline: none;
                }
                
                .module-content[contenteditable="true"]:focus {
                    background: var(--bg-hover);
                }
                
                /* Options dans la barre latérale droite */
                .options-content {
                    padding: 0;
                }
                
                .option-group {
                    margin-bottom: 1.5rem;
                }
                
                .option-group label {
                    display: block;
                    margin-bottom: 0.5rem;
                    font-weight: 600;
                    color: var(--text);
                }
                
                .format-buttons,
                .align-buttons {
                    display: flex;
                    gap: 0.5rem;
                    flex-wrap: wrap;
                }
                
                .format-btn,
                .align-btn {
                    padding: 0.5rem 1rem;
                    border: 1px solid var(--border);
                    background: var(--bg-light);
                    color: var(--text);
                    cursor: pointer;
                    border-radius: 6px;
                    font-weight: 600;
                    transition: all 0.3s ease;
                }
                
                .format-btn:hover,
                .align-btn:hover {
                    background: var(--belgium-red);
                    color: white;
                    border-color: var(--belgium-red);
                }
                
                .format-btn.active,
                .align-btn.active {
                    background: var(--belgium-yellow);
                    color: var(--primary);
                    border-color: var(--belgium-yellow);
                    font-weight: 700;
                }
                
                .font-size-select,
                .color-picker {
                    width: 100%;
                    padding: 0.75rem;
                    border: 1px solid var(--border);
                    border-radius: 6px;
                    background: var(--bg-light);
                    color: var(--text);
                    font-size: 0.9rem;
                }
                
                .color-picker {
                    height: 50px;
                    cursor: pointer;
                }
                
                .no-selection {
                    text-align: center;
                    color: var(--text-muted);
                    padding: 2rem;
                    font-style: italic;
                }
                
                /* Styles pour le module image */
                .image-upload-area {
                    width: 100%;
                    min-height: 200px;
                    border: 2px dashed var(--border);
                    border-radius: 8px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    cursor: pointer;
                    transition: all 0.3s ease;
                    background: var(--bg-light);
                }
                
                .image-upload-area:hover,
                .image-upload-area.drag-over {
                    border-color: var(--belgium-yellow);
                    background: rgba(255, 215, 0, 0.1);
                }
                
                .image-placeholder {
                    text-align: center;
                    color: var(--text-muted);
                }
                
                .upload-icon {
                    font-size: 3rem;
                    margin-bottom: 1rem;
                    opacity: 0.7;
                }
                
                .upload-text {
                    font-size: 1.1rem;
                    font-weight: 500;
                    margin-bottom: 0.5rem;
                }
                
                .upload-hint {
                    font-size: 0.9rem;
                    opacity: 0.8;
                }
                
                .image-container {
                    width: 100%;
                    text-align: center;
                }
                
                .uploaded-image {
                    max-width: 100%;
                    height: auto;
                    border-radius: 8px;
                    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
                }
                
                .image-caption {
                    margin-top: 0.5rem;
                    font-size: 0.9rem;
                    color: var(--text-muted);
                    font-style: italic;
                }

                /* Styles pour le module vidéo */
                .video-upload-area {
                    width: 100%;
                    min-height: 200px;
                    border: 2px dashed var(--border);
                    border-radius: 8px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    cursor: pointer;
                    transition: all 0.3s ease;
                    background: var(--bg-light);
                }
                
                .video-upload-area:hover,
                .video-upload-area.drag-over {
                    border-color: var(--belgium-yellow);
                    background: rgba(255, 215, 0, 0.1);
                }
                
                .video-container {
                    width: 100%;
                    text-align: center;
                }
                
                .video-container iframe,
                .video-container video {
                    max-width: 100%;
                    height: auto;
                    border-radius: 8px;
                    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
                }
                
                /* Alignement des vidéos */
                .video-container.align-left {
                    text-align: left;
                }
                
                .video-container.align-center {
                    text-align: center;
                }
                
                .video-container.align-right {
                    text-align: right;
                }
                
                .video-container.align-left iframe,
                .video-container.align-left video {
                    margin-left: 0;
                    margin-right: auto;
                }
                
                .video-container.align-center iframe,
                .video-container.align-center video {
                    margin-left: auto;
                    margin-right: auto;
                }
                
                .video-container.align-right iframe,
                .video-container.align-right video {
                    margin-left: auto;
                    margin-right: 0;
                }
                
                .video-title {
                    margin-top: 0.5rem;
                    font-size: 1rem;
                    font-weight: 500;
                    color: var(--text);
                }
                
                .video-description {
                    margin-top: 0.25rem;
                    font-size: 0.9rem;
                    color: var(--text-muted);
                    font-style: italic;
                }
                
                /* Options de vidéo */
                .video-checkboxes {
                    display: flex;
                    flex-direction: column;
                    gap: 0.5rem;
                }
                
                .checkbox-label {
                    display: flex;
                    align-items: center;
                    gap: 0.5rem;
                    cursor: pointer;
                    font-size: 0.9rem;
                    color: var(--text);
                }
                
                .checkbox-label input[type="checkbox"] {
                    width: 16px;
                    height: 16px;
                    cursor: pointer;
                }
                
                .video-action-buttons {
                    display: flex;
                    flex-direction: column;
                    gap: 0.5rem;
                }
                
                .video-action-btn {
                    display: flex;
                    align-items: center;
                    gap: 0.5rem;
                    padding: 0.75rem 1rem;
                    border: 1px solid var(--border);
                    border-radius: 6px;
                    background: var(--bg-light);
                    color: var(--text);
                    cursor: pointer;
                    transition: all 0.3s ease;
                    font-size: 0.9rem;
                    font-weight: 500;
                }
                
                .video-action-btn:hover {
                    background: var(--belgium-yellow);
                    border-color: var(--belgium-yellow);
                    color: var(--primary);
                }
                
                .video-action-btn.danger:hover {
                    background: var(--belgium-red);
                    border-color: var(--belgium-red);
                    color: white;
                }
                
                /* Informations sur le ratio d'aspect */
                .aspect-ratio-info {
                    font-size: 0.8rem;
                    color: var(--text-muted);
                    margin-top: 0.5rem;
                    font-style: italic;
                }
                
                /* Boutons d'alignement */
                .alignment-buttons {
                    display: flex;
                    gap: 0.5rem;
                    flex-wrap: wrap;
                }
                
                .alignment-buttons .align-btn {
                    flex: 1;
                    min-width: 80px;
                    padding: 0.5rem 0.75rem;
                    border: 1px solid var(--border);
                    background: var(--bg-light);
                    color: var(--text);
                    cursor: pointer;
                    border-radius: 6px;
                    font-weight: 600;
                    transition: all 0.3s ease;
                    font-size: 0.8rem;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    gap: 0.25rem;
                }
                
                .alignment-buttons .align-btn:hover {
                    background: var(--belgium-red);
                    color: white;
                    border-color: var(--belgium-red);
                }
                
                                 .alignment-buttons .align-btn.active {
                     background: var(--belgium-yellow);
                     color: var(--primary);
                     border-color: var(--belgium-yellow);
                     font-weight: 700;
                 }

                /* Éditeur de cellules du tableau */
                .cell-editor {
                    border: 1px solid var(--border);
                    border-radius: 8px;
                    overflow: hidden;
                    background: var(--bg-light);
                }
                
                .cell-editor-header {
                    display: grid;
                    grid-template-columns: 120px 1fr;
                    gap: 1rem;
                    padding: 0.75rem 1rem;
                    background: var(--primary);
                    color: white;
                    font-weight: 600;
                    font-size: 0.9rem;
                }
                
                .cell-editor-content {
                    max-height: 300px;
                    overflow-y: auto;
                }
                
                .cell-editor-row {
                    display: grid;
                    grid-template-columns: 120px 1fr;
                    gap: 1rem;
                    padding: 0.75rem 1rem;
                    border-bottom: 1px solid var(--border);
                    transition: background-color 0.2s ease;
                }
                
                .cell-editor-row:hover {
                    background: var(--bg-hover);
                }
                
                .cell-editor-row.header-row {
                    background: var(--belgium-yellow);
                    color: var(--primary);
                    font-weight: 600;
                }
                
                .cell-editor-row.header-row:hover {
                    background: var(--belgium-yellow);
                    opacity: 0.9;
                }
                
                .cell-label {
                    font-size: 0.85rem;
                    font-weight: 500;
                    color: var(--text);
                    display: flex;
                    align-items: center;
                }
                
                .cell-content {
                    width: 100%;
                    min-height: 60px;
                    padding: 0.5rem;
                    border: 1px solid var(--border);
                    border-radius: 4px;
                    background: white;
                    color: var(--text);
                    font-size: 0.9rem;
                    font-family: inherit;
                    resize: vertical;
                    outline: none;
                    transition: border-color 0.2s ease;
                }
                
                .cell-content:focus {
                    border-color: var(--belgium-yellow);
                    box-shadow: 0 0 0 2px rgba(255, 215, 0, 0.2);
                }
                
                .cell-content::placeholder {
                    color: var(--text-muted);
                    font-style: italic;
                }

                /* Configuration du tableau */
                .config-controls {
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
                    gap: 1rem;
                    margin-bottom: 1rem;
                }
                
                .control-item {
                    display: flex;
                    flex-direction: column;
                    gap: 0.5rem;
                }
                
                .control-item label {
                    font-size: 0.9rem;
                    font-weight: 500;
                    color: var(--text);
                }
                
                .control-item input[type="number"] {
                    padding: 0.5rem;
                    border: 1px solid var(--border);
                    border-radius: 4px;
                    background: white;
                    color: var(--text);
                    font-size: 0.9rem;
                }
                
                .control-item input[type="checkbox"] {
                    width: 16px;
                    height: 16px;
                    margin-right: 0.5rem;
                }
                
                .control-item label {
                    display: flex;
                    align-items: center;
                    cursor: pointer;
                }
                 
                 /* Styles pour le module citation */
                 .quote-placeholder {
                     width: 100%;
                     min-height: 120px;
                     border: 2px dashed var(--border);
                     border-radius: 8px;
                     display: flex;
                     flex-direction: column;
                     align-items: center;
                     justify-content: center;
                     cursor: pointer;
                     transition: all 0.3s ease;
                     background: var(--bg-light);
                 }
                 
                 .quote-placeholder:hover {
                     border-color: var(--belgium-yellow);
                     background: rgba(255, 215, 0, 0.1);
                 }
                 
                 .quote-icon {
                     font-size: 2rem;
                     margin-bottom: 0.5rem;
                     opacity: 0.7;
                 }
                 
                 .quote-text {
                     font-size: 1rem;
                     font-weight: 500;
                     color: var(--text);
                     margin-bottom: 0.25rem;
                 }
                 
                 .quote-hint {
                     font-size: 0.9rem;
                     color: var(--text-muted);
                     font-style: italic;
                 }
                 
                 /* Éditeur de citation */
                 .quote-editor {
                     padding: 1rem;
                     background: var(--bg-light);
                     border-radius: 8px;
                     border: 1px solid var(--border);
                 }
                 
                 .quote-input-group {
                     margin-bottom: 1rem;
                 }
                 
                 .quote-input-group label {
                     display: block;
                     margin-bottom: 0.5rem;
                     font-weight: 500;
                     color: var(--text);
                     font-size: 0.9rem;
                 }
                 
                 .quote-input-group input,
                 .quote-input-group textarea {
                     width: 100%;
                     padding: 0.75rem;
                     border: 1px solid var(--border);
                     border-radius: 6px;
                     background: var(--primary);
                     color: var(--text);
                     font-size: 0.9rem;
                     resize: vertical;
                 }
                 
                 .quote-input-group textarea {
                     min-height: 100px;
                     font-family: inherit;
                 }
                 
                 .quote-input-group input:focus,
                 .quote-input-group textarea:focus {
                     outline: none;
                     border-color: var(--belgium-yellow);
                     box-shadow: 0 0 0 2px rgba(255, 215, 0, 0.2);
                 }
                 
                 .quote-actions {
                     display: flex;
                     gap: 0.5rem;
                     justify-content: flex-end;
                 }
                 
                 .quote-save-btn,
                 .quote-cancel-btn {
                     padding: 0.5rem 1rem;
                     border: 1px solid var(--border);
                     border-radius: 6px;
                     background: var(--bg-light);
                     color: var(--text);
                     cursor: pointer;
                     transition: all 0.3s ease;
                     font-size: 0.9rem;
                     font-weight: 500;
                 }
                 
                 .quote-save-btn:hover {
                     background: var(--belgium-yellow);
                     border-color: var(--belgium-yellow);
                     color: var(--primary);
                 }
                 
                 .quote-cancel-btn:hover {
                     background: var(--belgium-red);
                     border-color: var(--belgium-red);
                     color: white;
                 }
                 
                 /* Affichage des citations */
                 .quote-container {
                     width: 100%;
                     padding: 1.5rem;
                     border-radius: 8px;
                     position: relative;
                     cursor: pointer;
                     transition: all 0.3s ease;
                 }
                 
                 .quote-container:hover {
                     transform: translateY(-2px);
                     box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
                 }
                 
                 /* Styles par défaut */
                 .quote-container.style-default {
                     background: linear-gradient(135deg, var(--bg-light) 0%, var(--primary) 100%);
                     border: 1px solid var(--border);
                 }
                 
                 .quote-container.style-default .quote-text {
                     font-size: 1.1rem;
                     font-style: italic;
                     color: var(--text);
                     margin-bottom: 1rem;
                     line-height: 1.6;
                 }
                 
                 .quote-container.style-default .quote-author {
                     font-weight: 600;
                     color: var(--belgium-yellow);
                     font-size: 0.95rem;
                 }
                 
                 .quote-container.style-default .quote-source {
                     font-size: 0.85rem;
                     color: var(--text-muted);
                     font-style: italic;
                     margin-top: 0.25rem;
                 }
                 
                 /* Style minimal */
                 .quote-container.style-minimal {
                     background: transparent;
                     border-left: 4px solid var(--belgium-yellow);
                     padding-left: 1.5rem;
                 }
                 
                 .quote-container.style-minimal .quote-text {
                     font-size: 1rem;
                     color: var(--text);
                     margin-bottom: 0.75rem;
                     line-height: 1.5;
                 }
                 
                 .quote-container.style-minimal .quote-author {
                     font-weight: 500;
                     color: var(--text-muted);
                     font-size: 0.9rem;
                 }
                 
                 .quote-container.style-minimal .quote-source {
                     font-size: 0.8rem;
                     color: var(--text-muted);
                     margin-top: 0.25rem;
                 }
                 
                 /* Style élégant */
                 .quote-container.style-elegant {
                     background: var(--bg-light);
                     border: 2px solid var(--belgium-yellow);
                     position: relative;
                 }
                 
                 .quote-container.style-elegant::before {
                     content: '"';
                     position: absolute;
                     top: -10px;
                     left: 20px;
                     font-size: 3rem;
                     color: var(--belgium-yellow);
                     background: var(--primary);
                     padding: 0 10px;
                     font-family: serif;
                 }
                 
                 .quote-container.style-elegant .quote-text {
                     font-size: 1.1rem;
                     color: var(--text);
                     margin-bottom: 1rem;
                     line-height: 1.6;
                     padding-top: 0.5rem;
                 }
                 
                 .quote-container.style-elegant .quote-author {
                     font-weight: 600;
                     color: var(--belgium-yellow);
                     font-size: 0.95rem;
                     text-transform: uppercase;
                     letter-spacing: 1px;
                 }
                 
                 .quote-container.style-elegant .quote-source {
                     font-size: 0.85rem;
                     color: var(--text-muted);
                     font-style: italic;
                     margin-top: 0.25rem;
                 }
                 
                 /* Style moderne */
                 .quote-container.style-modern {
                     background: linear-gradient(135deg, var(--belgium-yellow) 0%, var(--belgium-red) 100%);
                     color: white;
                     border: none;
                     position: relative;
                     overflow: hidden;
                 }
                 
                 .quote-container.style-modern::before {
                     content: '';
                     position: absolute;
                     top: 0;
                     left: 0;
                     right: 0;
                     bottom: 0;
                     background: rgba(0, 0, 0, 0.1);
                     z-index: 1;
                 }
                 
                 .quote-container.style-modern .quote-text,
                 .quote-container.style-modern .quote-author,
                 .quote-container.style-modern .quote-source {
                     position: relative;
                     z-index: 2;
                 }
                 
                 .quote-container.style-modern .quote-text {
                     font-size: 1.1rem;
                     color: white;
                     margin-bottom: 1rem;
                     line-height: 1.6;
                     font-weight: 500;
                 }
                 
                 .quote-container.style-modern .quote-author {
                     font-weight: 600;
                     color: rgba(255, 255, 255, 0.9);
                     font-size: 0.95rem;
                 }
                 
                 .quote-container.style-modern .quote-source {
                     font-size: 0.85rem;
                     color: rgba(255, 255, 255, 0.7);
                     font-style: italic;
                     margin-top: 0.25rem;
                 }
                 
                 /* Alignement des citations */
                 .quote-container.align-left {
                     text-align: left;
                 }
                 
                 .quote-container.align-center {
                     text-align: center;
                 }
                 
                 .quote-container.align-right {
                     text-align: right;
                 }
                 
                 /* Tailles des citations */
                 .quote-container.size-small .quote-text {
                     font-size: 0.9rem;
                 }
                 
                 .quote-container.size-medium .quote-text {
                     font-size: 1.1rem;
                 }
                 
                 .quote-container.size-large .quote-text {
                     font-size: 1.3rem;
                 }
                 
                 /* Options de citation */
                 .quote-action-buttons {
                     display: flex;
                     flex-direction: column;
                     gap: 0.5rem;
                 }
                 
                 .quote-action-btn {
                     display: flex;
                     align-items: center;
                     gap: 0.5rem;
                     padding: 0.75rem 1rem;
                     border: 1px solid var(--border);
                     border-radius: 6px;
                     background: var(--bg-light);
                     color: var(--text);
                     cursor: pointer;
                     transition: all 0.3s ease;
                     font-size: 0.9rem;
                     font-weight: 500;
                 }
                 
                 .quote-action-btn:hover {
                     background: var(--belgium-yellow);
                     border-color: var(--belgium-yellow);
                     color: var(--primary);
                 }
                 
                 .quote-action-btn.danger:hover {
                     background: var(--belgium-red);
                     border-color: var(--belgium-red);
                     color: white;
                 }
                 
                 /* Boutons de style */
                 .style-buttons {
                     display: grid;
                     grid-template-columns: 1fr 1fr;
                     gap: 0.5rem;
                 }
                 
                 .style-btn {
                     padding: 0.75rem 0.5rem;
                     border: 1px solid var(--border);
                     background: var(--bg-light);
                     color: var(--text);
                     cursor: pointer;
                     border-radius: 6px;
                     font-weight: 600;
                     transition: all 0.3s ease;
                     font-size: 0.8rem;
                     display: flex;
                     flex-direction: column;
                     align-items: center;
                     gap: 0.25rem;
                 }
                 
                 .style-btn:hover {
                     background: var(--belgium-red);
                     color: white;
                     border-color: var(--belgium-red);
                 }
                 
                 .style-btn.active {
                     background: var(--belgium-yellow);
                     color: var(--primary);
                     border-color: var(--belgium-yellow);
                     font-weight: 700;
                 }
                 
                 /* Boutons de taille */
                 .size-buttons {
                     display: flex;
                     gap: 0.5rem;
                 }
                 
                 .size-btn {
                     flex: 1;
                     padding: 0.5rem 0.75rem;
                     border: 1px solid var(--border);
                     background: var(--bg-light);
                     color: var(--text);
                     cursor: pointer;
                     border-radius: 6px;
                     font-weight: 600;
                     transition: all 0.3s ease;
                     font-size: 0.8rem;
                     display: flex;
                     align-items: center;
                     justify-content: center;
                     gap: 0.25rem;
                 }
                 
                 .size-btn:hover {
                     background: var(--belgium-red);
                     color: white;
                     border-color: var(--belgium-red);
                 }
                 
                 .size-btn.active {
                     background: var(--belgium-yellow);
                     color: var(--primary);
                     border-color: var(--belgium-yellow);
                     font-weight: 700;
                 }
                 
                 /* Aperçu de citation */
                 .quote-preview {
                     margin-top: 1rem;
                     padding: 1rem;
                     background: var(--bg-light);
                     border-radius: 6px;
                     border: 1px solid var(--border);
                 }
                 
                 /* Options d'image */
                 .size-inputs {
                    display: flex;
                    align-items: center;
                    gap: 0.5rem;
                }
                
                .size-inputs input {
                    flex: 1;
                    padding: 0.5rem;
                    border: 1px solid var(--border);
                    border-radius: 4px;
                    background: var(--bg-light);
                    color: var(--text);
                    font-size: 0.9rem;
                }
                
                .size-unit {
                    color: var(--text-muted);
                    font-size: 0.9rem;
                }
                
                .image-action-buttons {
                    display: flex;
                    flex-direction: column;
                    gap: 0.5rem;
                }
                
                .image-action-btn {
                    display: flex;
                    align-items: center;
                    gap: 0.5rem;
                    padding: 0.75rem 1rem;
                    border: 1px solid var(--border);
                    background: var(--bg-light);
                    color: var(--text);
                    cursor: pointer;
                    border-radius: 6px;
                    transition: all 0.3s ease;
                    font-size: 0.9rem;
                    font-weight: 500;
                }
                
                .image-action-btn:hover {
                    background: var(--belgium-red);
                    color: white;
                    border-color: var(--belgium-red);
                    transform: translateY(-1px);
                }
                
                .image-action-btn.danger:hover {
                    background: var(--error);
                    border-color: var(--error);
                }
                
                /* ===== MODULE GALERIE ===== */
                
                /* Placeholder de la galerie */
                .gallery-placeholder {
                    text-align: center;
                    padding: 40px 20px;
                    background: var(--bg-light);
                    border: 2px dashed var(--border);
                    border-radius: 8px;
                    color: var(--text-muted);
                }
                
                .gallery-placeholder.drag-over {
                    border-color: var(--belgium-yellow);
                    background: rgba(255, 215, 0, 0.1);
                }
                
                .gallery-icon {
                    font-size: 48px;
                    margin-bottom: 16px;
                }
                
                .gallery-text {
                    font-size: 18px;
                    font-weight: 600;
                    margin-bottom: 8px;
                    color: var(--text);
                }
                
                .gallery-hint {
                    font-size: 14px;
                    color: var(--text-muted);
                }
                
                /* Éditeur de galerie */
                .gallery-editor {
                    padding: 20px;
                }
                
                .gallery-upload-section {
                    margin-bottom: 24px;
                }
                
                .gallery-upload-section h4 {
                    margin: 0 0 16px 0;
                    color: var(--text);
                    font-size: 16px;
                }
                
                .upload-methods {
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                    gap: 16px;
                }
                
                .upload-method {
                    text-align: center;
                }
                
                .upload-btn {
                    display: inline-block;
                    background: var(--belgium-yellow);
                    color: var(--primary);
                    padding: 12px 24px;
                    border-radius: 6px;
                    cursor: pointer;
                    font-weight: 500;
                    transition: all 0.3s ease;
                }
                
                .upload-btn:hover {
                    background: var(--belgium-red);
                    color: white;
                    transform: translateY(-1px);
                }
                
                .drag-drop-zone {
                    border: 2px dashed var(--border);
                    border-radius: 6px;
                    padding: 24px;
                    background: var(--bg-light);
                    transition: all 0.3s ease;
                }
                
                .drag-drop-zone.drag-over {
                    border-color: var(--belgium-yellow);
                    background: rgba(255, 215, 0, 0.1);
                }
                
                .drag-drop-zone .icon {
                    font-size: 24px;
                    display: block;
                    margin-bottom: 8px;
                }
                
                /* Liste des images */
                .gallery-images-section {
                    margin-bottom: 24px;
                }
                
                .gallery-images-section h4 {
                    margin: 0 0 16px 0;
                    color: var(--text);
                    font-size: 16px;
                }
                
                .gallery-images-list {
                    max-height: 400px;
                    overflow-y: auto;
                    border: 1px solid var(--border);
                    border-radius: 6px;
                    padding: 16px;
                    background: var(--bg-light);
                }
                
                .no-images {
                    text-align: center;
                    color: var(--text-muted);
                    font-style: italic;
                    margin: 20px 0;
                }
                
                .gallery-image-item {
                    display: grid;
                    grid-template-columns: 120px 1fr;
                    gap: 16px;
                    padding: 16px;
                    background: var(--primary);
                    border: 1px solid var(--border);
                    border-radius: 6px;
                    margin-bottom: 12px;
                }
                
                .gallery-image-item:last-child {
                    margin-bottom: 0;
                }
                
                .image-preview {
                    position: relative;
                }
                
                .image-preview img {
                    width: 100%;
                    height: 80px;
                    object-fit: cover;
                    border-radius: 4px;
                }
                
                .image-actions {
                    position: absolute;
                    top: 4px;
                    right: 4px;
                    display: flex;
                    gap: 4px;
                }
                
                .image-action-btn {
                    background: rgba(0, 0, 0, 0.7);
                    color: white;
                    border: none;
                    width: 24px;
                    height: 24px;
                    border-radius: 4px;
                    cursor: pointer;
                    font-size: 12px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }
                
                .image-action-btn:hover {
                    background: rgba(0, 0, 0, 0.9);
                }
                
                .image-action-btn:disabled {
                    opacity: 0.5;
                    cursor: not-allowed;
                }
                
                .image-info {
                    display: flex;
                    flex-direction: column;
                    gap: 8px;
                }
                
                .image-title-input,
                .image-description-input,
                .image-alt-input {
                    width: 100%;
                    padding: 8px;
                    border: 1px solid var(--border);
                    border-radius: 4px;
                    font-size: 14px;
                    background: var(--bg-light);
                    color: var(--text);
                }
                
                .image-title-input:focus,
                .image-description-input:focus,
                .image-alt-input:focus {
                    outline: none;
                    border-color: var(--belgium-yellow);
                    box-shadow: 0 0 0 2px rgba(255, 215, 0, 0.25);
                }
                
                .image-description-input {
                    resize: vertical;
                    min-height: 60px;
                }
                
                /* Actions de la galerie */
                .gallery-actions {
                    display: flex;
                    gap: 12px;
                    justify-content: flex-end;
                }
                
                .gallery-save-btn,
                .gallery-cancel-btn {
                    padding: 10px 20px;
                    border: none;
                    border-radius: 4px;
                    cursor: pointer;
                    font-weight: 500;
                    transition: all 0.3s ease;
                }
                
                .gallery-save-btn {
                    background: var(--success);
                    color: white;
                }
                
                .gallery-save-btn:hover {
                    background: #218838;
                    transform: translateY(-1px);
                }
                
                .gallery-cancel-btn {
                    background: var(--text-muted);
                    color: white;
                }
                
                .gallery-cancel-btn:hover {
                    background: #545b62;
                    transform: translateY(-1px);
                }
                
                /* Affichage de la galerie */
                .gallery-container {
                    display: grid;
                    gap: 16px;
                }
                
                .gallery-container.layout-grid {
                    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                }
                
                .gallery-container.layout-grid.columns-2 {
                    grid-template-columns: repeat(2, 1fr);
                }
                
                .gallery-container.layout-grid.columns-3 {
                    grid-template-columns: repeat(3, 1fr);
                }
                
                .gallery-container.layout-grid.columns-4 {
                    grid-template-columns: repeat(4, 1fr);
                }
                
                .gallery-container.layout-grid.columns-5 {
                    grid-template-columns: repeat(5, 1fr);
                }
                
                .gallery-container.layout-masonry {
                    columns: 3;
                    column-gap: 16px;
                }
                
                .gallery-container.layout-masonry .gallery-item {
                    break-inside: avoid;
                    margin-bottom: 16px;
                }
                
                .gallery-container.layout-carousel,
                .gallery-container.layout-slider {
                    display: flex;
                    overflow-x: auto;
                    scroll-snap-type: x mandatory;
                    gap: 16px;
                    padding: 8px 0;
                }
                
                .gallery-container.layout-carousel .gallery-item,
                .gallery-container.layout-slider .gallery-item {
                    flex: 0 0 300px;
                    scroll-snap-align: start;
                }
                
                /* Espacement */
                .gallery-container.spacing-small {
                    gap: 8px;
                }
                
                .gallery-container.spacing-medium {
                    gap: 16px;
                }
                
                .gallery-container.spacing-large {
                    gap: 24px;
                }
                
                /* Éléments de la galerie */
                .gallery-item {
                    position: relative;
                    border-radius: 8px;
                    overflow: hidden;
                    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
                    transition: all 0.3s ease;
                    background: var(--primary);
                }
                
                .gallery-item:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
                }
                
                .gallery-image {
                    position: relative;
                    width: 100%;
                    height: 200px;
                    overflow: hidden;
                }
                
                .gallery-image img {
                    width: 100%;
                    height: 100%;
                    object-fit: cover;
                    transition: transform 0.3s;
                }
                
                .gallery-item:hover .gallery-image img {
                    transform: scale(1.05);
                }
                
                .lightbox-overlay {
                    position: absolute;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background: rgba(0, 0, 0, 0.5);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    opacity: 0;
                    transition: opacity 0.2s;
                    cursor: pointer;
                    font-size: 24px;
                    color: white;
                }
                
                .gallery-item:hover .lightbox-overlay {
                    opacity: 1;
                }
                
                /* Légendes */
                .gallery-caption {
                    padding: 12px;
                    background: var(--primary);
                }
                
                .gallery-container.captions-overlay .gallery-caption {
                    position: absolute;
                    bottom: 0;
                    left: 0;
                    right: 0;
                    background: rgba(0, 0, 0, 0.8);
                    color: white;
                    padding: 12px;
                }
                
                .gallery-container.captions-none .gallery-caption {
                    display: none;
                }
                
                .image-title {
                    font-weight: 600;
                    margin-bottom: 4px;
                    font-size: 14px;
                }
                
                .image-description {
                    font-size: 12px;
                    color: var(--text-muted);
                    line-height: 1.4;
                }
                
                .gallery-container.captions-overlay .image-description {
                    color: #ccc;
                }
                
                /* Options de la galerie */
                .gallery-options {
                    padding: 16px;
                }
                
                .gallery-action-buttons {
                    display: flex;
                    gap: 8px;
                    margin-top: 8px;
                }
                
                .gallery-action-btn {
                    background: var(--belgium-yellow);
                    color: var(--primary);
                    border: none;
                    padding: 8px 12px;
                    border-radius: 4px;
                    cursor: pointer;
                    font-size: 12px;
                    display: flex;
                    align-items: center;
                    gap: 4px;
                    transition: all 0.3s ease;
                }
                
                .gallery-action-btn:hover {
                    background: var(--belgium-red);
                    color: white;
                    transform: translateY(-1px);
                }
                
                .gallery-action-btn.danger {
                    background: var(--error);
                    color: white;
                }
                
                .gallery-action-btn.danger:hover {
                    background: #c82333;
                }
                
                .layout-buttons,
                .columns-buttons,
                .spacing-buttons,
                .captions-buttons {
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                    gap: 8px;
                    margin-top: 8px;
                }
                
                .layout-btn,
                .gallery-layout-btn,
                .column-btn,
                .spacing-btn,
                .caption-btn {
                    background: var(--bg-light);
                    border: 1px solid var(--border);
                    padding: 8px 12px;
                    border-radius: 4px;
                    cursor: pointer;
                    font-size: 12px;
            display: flex;
            align-items: center;
            gap: 4px;
            transition: all 0.3s ease;
        }
        
        .layout-btn:hover,
        .gallery-layout-btn:hover,
        .column-btn:hover,
        .spacing-btn:hover,
        .caption-btn:hover {
            background: var(--belgium-red);
            color: white;
            border-color: var(--belgium-red);
        }
        
        .layout-btn.active,
        .gallery-layout-btn.active,
        .column-btn.active,
        .spacing-btn.active,
        .caption-btn.active {
            background: var(--belgium-yellow);
            color: var(--primary);
            border-color: var(--belgium-yellow);
            font-weight: 700;
        }
        
        .columns-buttons {
            grid-template-columns: repeat(4, 1fr);
        }
        
        .gallery-checkboxes {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-top: 8px;
        }
        
        .checkbox-label {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            font-size: 14px;
        }
        
        .checkbox-label input[type="checkbox"] {
            width: 16px;
            height: 16px;
        }
        
        .gallery-preview {
            background: var(--bg-light);
            border: 1px solid var(--border);
            border-radius: 4px;
            padding: 12px;
            margin-top: 8px;
        }
        
        .gallery-preview .gallery-container {
            gap: 8px;
        }
        
        .gallery-preview .gallery-item {
            box-shadow: none;
        }
        
        .gallery-preview .gallery-image {
            height: 80px;
        }
        
        .more-images {
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--bg-light);
            border: 1px dashed var(--border);
            border-radius: 4px;
            padding: 20px;
            color: var(--text-muted);
            font-size: 14px;
            font-style: italic;
        }

        /* Styles pour le Carousel */
        .gallery-carousel {
            position: relative;
            width: 100%;
            overflow: hidden;
            border-radius: 8px;
            background: var(--primary);
        }

        .carousel-container {
            position: relative;
            width: 100%;
            overflow: hidden;
        }

        .carousel-track {
            display: flex;
            transition: transform 0.5s ease-in-out;
            width: 100%;
        }

        .carousel-slide {
            flex: 0 0 100%;
            width: 100%;
        }

        .carousel-controls {
            position: absolute;
            bottom: 20px;
            left: 0;
            right: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 20px;
            z-index: 10;
        }

        .carousel-btn {
            background: rgba(0, 0, 0, 0.7);
            border: none;
            color: white;
            padding: 12px;
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
        }

        .carousel-btn:hover {
            background: rgba(0, 0, 0, 0.9);
            transform: scale(1.1);
        }

        .carousel-indicators {
            display: flex;
            gap: 8px;
        }

        .carousel-indicator {
            background: rgba(255, 255, 255, 0.3);
            border: none;
            border-radius: 50%;
            width: 12px;
            height: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .carousel-indicator:hover {
            background: rgba(255, 255, 255, 0.5);
        }

        .carousel-indicator.active {
            background: var(--belgium-yellow);
        }

        .indicator-dot {
            display: block;
            width: 100%;
            height: 100%;
            border-radius: 50%;
        }

        /* Styles pour le Masonry */
        .gallery-masonry {
            width: 100%;
        }

        .masonry-grid {
            position: relative;
            width: 100%;
        }

        .masonry-item {
            position: absolute;
            transition: all 0.3s ease;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .masonry-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
        }

        .masonry-item img {
            width: 100%;
            height: auto;
            display: block;
        }

        /* Styles pour le Slider */
        .gallery-slider {
            position: relative;
            width: 100%;
            overflow: hidden;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
        }

        .slider-container {
            position: relative;
            width: 100%;
            overflow: hidden;
        }

        .slider-track {
            display: flex;
            transition: transform 0.8s cubic-bezier(0.4, 0, 0.2, 1);
            width: 100%;
        }

        .slider-slide {
            flex: 0 0 100%;
            width: 100%;
            position: relative;
        }

        .slider-slide::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(180deg, transparent 0%, rgba(0, 0, 0, 0.3) 100%);
            z-index: 1;
            pointer-events: none;
        }

        .slider-controls {
            position: absolute;
            bottom: 30px;
            left: 0;
            right: 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 30px;
            z-index: 10;
        }

        .slider-btn {
            background: linear-gradient(135deg, var(--belgium-yellow) 0%, #ffd700 100%);
            border: 2px solid rgba(255, 255, 255, 0.3);
            color: var(--primary);
            padding: 14px;
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            justify-content: center;
            width: 48px;
            height: 48px;
            font-weight: bold;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
        }

        .slider-btn:hover {
            background: linear-gradient(135deg, #ffd700 0%, var(--belgium-yellow) 100%);
            transform: scale(1.15) translateY(-2px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
        }

        .slider-btn:active {
            transform: scale(0.95);
        }

        .slider-counter {
            background: linear-gradient(135deg, rgba(0, 0, 0, 0.8) 0%, rgba(0, 0, 0, 0.6) 100%);
            color: white;
            padding: 12px 20px;
            border-radius: 25px;
            font-size: 16px;
            font-weight: 700;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
        }

        .current-slide {
            color: var(--belgium-yellow);
            font-weight: 800;
        }

        /* Styles pour le module Titre */
        .heading-display {
            padding: 20px;
            border: 2px dashed var(--border);
            border-radius: 8px;
            text-align: center;
            transition: all 0.3s ease;
        }

        .heading-display:hover {
            border-color: var(--primary);
            background: rgba(220, 53, 69, 0.05);
        }

        .heading-text {
            margin: 0;
            cursor: pointer;
            transition: all 0.3s ease;
            outline: none;
        }

        .heading-text:focus {
            background: rgba(220, 53, 69, 0.1);
            border-radius: 4px;
            padding: 4px 8px;
        }

        /* Styles des titres */
        .heading-default {
            font-weight: 700;
            line-height: 1.2;
        }

        .heading-modern {
            font-weight: 600;
            letter-spacing: -0.5px;
            text-transform: uppercase;
        }

        .heading-elegant {
            font-weight: 400;
            font-family: 'Georgia', serif;
            font-style: italic;
        }

        .heading-minimal {
            font-weight: 300;
            letter-spacing: 1px;
        }

        /* Tailles des titres */
        .heading-small {
            font-size: 1.2rem;
        }

        .heading-medium {
            font-size: 1.5rem;
        }

        .heading-large {
            font-size: 2rem;
        }

        /* Alignement des titres */
        .heading-left {
            text-align: left;
        }

        .heading-center {
            text-align: center;
        }

        .heading-right {
            text-align: right;
        }

        /* Éditeur de titre */
        .heading-editor {
            padding: 20px;
            background: var(--bg-light);
            border-radius: 8px;
        }

        .editor-section {
            margin-bottom: 20px;
        }

        .editor-section h4 {
            margin: 0 0 10px 0;
            color: var(--text);
            font-size: 14px;
            font-weight: 600;
        }

        .heading-text-input {
            width: 100%;
            min-height: 60px;
            padding: 12px;
            border: 1px solid var(--border);
            border-radius: 4px;
            font-size: 16px;
            font-family: inherit;
            resize: vertical;
        }

        .heading-text-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(220, 53, 69, 0.25);
        }

        .heading-level-select {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid var(--border);
            border-radius: 4px;
            font-size: 14px;
            background: white;
        }

        .style-buttons,
        .alignment-buttons,
        .size-buttons {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .style-btn,
        .align-btn,
        .size-btn {
            padding: 8px 12px;
            border: 1px solid var(--border);
            border-radius: 4px;
            background: white;
            cursor: pointer;
            font-size: 12px;
            transition: all 0.3s ease;
        }

        .style-btn:hover,
        .align-btn:hover,
        .size-btn:hover {
            background: var(--bg-light);
            border-color: var(--primary);
        }

        .style-btn.active,
        .align-btn.active,
        .size-btn.active {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .heading-color-input {
            width: 50px;
            height: 40px;
            border: 1px solid var(--border);
            border-radius: 4px;
            cursor: pointer;
        }

        .editor-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid var(--border);
        }

        .save-btn,
        .cancel-btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .save-btn {
            background: var(--success);
            color: white;
        }

        .save-btn:hover {
            background: #218838;
        }

        .cancel-btn {
            background: var(--secondary);
            color: white;
        }

        .cancel-btn:hover {
            background: #5a6268;
        }

        /* Options du titre */
        .heading-options {
            padding: 15px;
        }

        .heading-options h4 {
            margin: 0 0 15px 0;
            color: var(--text);
            font-size: 16px;
        }

        .current-text,
        .current-level,
        .current-style {
            margin: 5px 0;
            padding: 8px 12px;
            background: var(--bg-light);
            border-radius: 4px;
            font-size: 14px;
            color: var(--text);
        }

        .action-buttons {
            margin-top: 15px;
        }

        .action-btn {
            padding: 8px 16px;
            border: 1px solid var(--border);
            border-radius: 4px;
            background: white;
            color: var(--text);
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .action-btn:hover {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        /* Styles pour le module Liste */
        .list-display {
            padding: 10px;
            border: 1px solid var(--border);
            border-radius: 5px;
            background: var(--bg-light);
            margin: 5px 0;
        }

        .list-container {
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .list-item {
            padding: 5px 0;
            transition: all 0.3s ease;
            border-radius: 3px;
        }

        .list-item:focus {
            outline: none;
            background: white;
            box-shadow: 0 0 5px rgba(0,123,255,0.3);
            padding: 5px 10px;
        }

        /* Indentation des listes */
        .list-indent-0 { margin-left: 0; }
        .list-indent-1 { margin-left: 20px; }
        .list-indent-2 { margin-left: 40px; }
        .list-indent-3 { margin-left: 60px; }

        /* Styles de liste */
        .list-default {
            font-family: 'Arial', sans-serif;
        }

        .list-modern {
            font-family: 'Helvetica', sans-serif;
            font-weight: 300;
        }

        .list-elegant {
            font-family: 'Georgia', serif;
            font-style: italic;
        }

        .list-minimal {
            font-family: 'Courier New', monospace;
            font-size: 14px;
        }

        /* Espacement des listes */
        .list-compact .list-item {
            padding: 2px 0;
            line-height: 1.2;
        }

        .list-normal .list-item {
            padding: 5px 0;
            line-height: 1.4;
        }

        .list-spacious .list-item {
            padding: 8px 0;
            line-height: 1.6;
        }

        /* Styles de puces */
        .list-disc .list-item::before {
            content: "●";
            color: var(--primary);
            font-weight: bold;
            margin-right: 8px;
        }

        .list-circle .list-item::before {
            content: "○";
            color: var(--text-muted);
            margin-right: 8px;
        }

        .list-square .list-item::before {
            content: "■";
            color: var(--text);
            margin-right: 8px;
        }

        .list-decimal .list-item {
            counter-increment: list-counter;
        }

        .list-decimal .list-item::before {
            content: counter(list-counter) ".";
            color: var(--primary);
            font-weight: bold;
            margin-right: 8px;
        }

        .list-lower-alpha .list-item {
            counter-increment: alpha-counter;
        }

        .list-lower-alpha .list-item::before {
            content: counter(alpha-counter, lower-alpha) ".";
            color: var(--primary);
            font-weight: bold;
            margin-right: 8px;
        }

        .list-upper-alpha .list-item {
            counter-increment: alpha-counter;
        }

        .list-upper-alpha .list-item::before {
            content: counter(alpha-counter, upper-alpha) ".";
            color: var(--primary);
            font-weight: bold;
            margin-right: 8px;
        }

        /* Nouveaux styles de puces */
        .list-arrow .list-item::before {
            content: "→";
            color: var(--primary);
            font-weight: bold;
            margin-right: 8px;
        }

        .list-star .list-item::before {
            content: "★";
            color: #FFD700;
            font-weight: bold;
            margin-right: 8px;
        }

        .list-check .list-item::before {
            content: "✓";
            color: #28a745;
            font-weight: bold;
            margin-right: 8px;
        }

        .list-dash .list-item::before {
            content: "—";
            color: var(--text-muted);
            font-weight: bold;
            margin-right: 8px;
        }

        .list-plus .list-item::before {
            content: "+";
            color: var(--primary);
            font-weight: bold;
            margin-right: 8px;
        }

        .list-bullet .list-item::before {
            content: "•";
            color: var(--text);
            font-weight: bold;
            margin-right: 8px;
        }

        /* Alignement du texte */
        .list-align-left .list-item {
            text-align: left;
        }

        .list-align-center .list-item {
            text-align: center;
        }

        .list-align-right .list-item {
            text-align: right;
        }

        /* Éditeur de liste */
        .list-editor {
            padding: 15px;
            background: var(--bg-light);
            border-radius: 8px;
        }

        .list-items-editor {
            max-height: 300px;
            overflow-y: auto;
            margin-bottom: 15px;
        }

        .list-item-editor {
            background: white;
            border: 1px solid var(--border);
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 10px;
        }

        .item-controls {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 8px;
        }

        .level-btn {
            padding: 4px 8px;
            border: 1px solid var(--border);
            border-radius: 3px;
            background: white;
            cursor: pointer;
            font-size: 12px;
        }

        .level-btn:hover:not(:disabled) {
            background: var(--bg-hover);
        }

        .level-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .level-indicator {
            font-size: 12px;
            color: var(--text-muted);
            font-weight: 500;
        }

        .remove-item-btn {
            padding: 4px 8px;
            border: 1px solid var(--danger);
            border-radius: 3px;
            background: white;
            color: var(--danger);
            cursor: pointer;
            font-size: 12px;
        }

        .remove-item-btn:hover:not(:disabled) {
            background: var(--danger);
            color: white;
        }

        .remove-item-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .item-text-input {
            width: 100%;
            min-height: 40px;
            padding: 8px;
            border: 1px solid var(--border);
            border-radius: 4px;
            font-size: 14px;
            resize: vertical;
            font-family: inherit;
        }

        .add-item-btn {
            width: 100%;
            padding: 10px;
            border: 2px dashed var(--primary);
            border-radius: 5px;
            background: white;
            color: var(--primary);
            cursor: pointer;
            font-weight: 500;
            transition: all 0.2s;
        }

        .add-item-btn:hover {
            background: var(--primary);
            color: white;
        }

        .list-type-buttons, .style-buttons, .spacing-buttons {
            display: flex;
            gap: 5px;
            margin-bottom: 10px;
            flex-wrap: wrap;
        }

        .type-btn, .style-btn, .spacing-btn {
            padding: 8px 12px;
            border: 1px solid var(--border);
            border-radius: 4px;
            background: white;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 12px;
        }

        .type-btn:hover, .style-btn:hover, .spacing-btn:hover {
            background: var(--bg-hover);
        }

        .type-btn.active, .style-btn.active, .spacing-btn.active {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .bullet-style-select {
            width: 100%;
            padding: 8px;
            border: 1px solid var(--border);
            border-radius: 4px;
            margin-bottom: 10px;
        }

        .list-color-input {
            width: 50px;
            height: 30px;
            border: 1px solid var(--border);
            border-radius: 4px;
            cursor: pointer;
        }

        /* Boutons d'alignement */
        .alignment-buttons {
            display: flex;
            gap: 5px;
            margin-bottom: 10px;
            flex-wrap: wrap;
        }

        .alignment-btn {
            flex: 1;
            padding: 8px 12px;
            border: 1px solid var(--border);
            border-radius: 4px;
            background: white;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.25rem;
        }

        .alignment-btn:hover {
            background: var(--bg-hover);
        }

        .alignment-btn.active {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        /* Options de la liste */
        .list-options {
            padding: 15px;
            background: var(--bg-light);
            border-radius: 8px;
        }

        .current-type, .current-items, .current-style {
            font-weight: 500;
            color: var(--text-muted);
            margin: 5px 0;
        }

        /* Styles pour le module Séparateur */
        .separator-display {
            padding: 10px;
            min-height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .separator-container {
            width: 100%;
            display: flex;
            align-items: center;
        }

        .separator-align-left {
            justify-content: flex-start;
        }

        .separator-align-center {
            justify-content: center;
        }

        .separator-align-right {
            justify-content: flex-end;
        }

        .separator {
            border: none;
            background: transparent;
        }

        /* Éditeur de séparateur */
        .separator-editor {
            padding: 15px;
            background: var(--bg-light);
            border-radius: 8px;
        }

        .separator-editor .style-buttons {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-bottom: 15px;
        }

        .separator-editor .style-btn {
            background: white;
            border: 1px solid var(--border);
            border-radius: 4px;
            padding: 8px 12px;
            cursor: pointer;
            font-size: 12px;
            transition: all 0.2s;
            flex: 1;
            min-width: 120px;
        }

        .separator-editor .style-btn.active {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .decorative-style-select {
            width: 100%;
            padding: 8px;
            border: 1px solid var(--border);
            border-radius: 4px;
            background: white;
            margin-bottom: 15px;
        }

        .separator-editor .alignment-buttons {
            display: flex;
            gap: 8px;
            margin-bottom: 15px;
        }

        .separator-editor .alignment-btn {
            background: white;
            border: 1px solid var(--border);
            border-radius: 4px;
            padding: 8px 12px;
            cursor: pointer;
            font-size: 12px;
            transition: all 0.2s;
            flex: 1;
        }

        .separator-editor .alignment-btn.active {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .width-control,
        .thickness-control,
        .margin-control {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
        }

        .width-slider,
        .thickness-slider,
        .margin-slider {
            flex: 1;
            height: 8px;
            border-radius: 4px;
            background: var(--border);
            outline: none;
            -webkit-appearance: none;
            border: 1px solid var(--border);
        }

        .width-slider::-webkit-slider-thumb,
        .thickness-slider::-webkit-slider-thumb,
        .margin-slider::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: var(--belgium-yellow);
            border: 2px solid var(--primary);
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .width-slider::-webkit-slider-thumb:hover,
        .thickness-slider::-webkit-slider-thumb:hover,
        .margin-slider::-webkit-slider-thumb:hover {
            background: #ffed4e;
            transform: scale(1.1);
        }

        .width-value,
        .thickness-value,
        .margin-value {
            min-width: 50px;
            text-align: right;
            font-size: 14px;
            color: var(--text);
            font-weight: 600;
            background: var(--bg-light);
            padding: 4px 8px;
            border-radius: 4px;
            border: 1px solid var(--border);
        }

        .separator-color-input {
            width: 60px;
            height: 40px;
            border: 1px solid var(--border);
            border-radius: 4px;
            cursor: pointer;
        }

        .separator-options {
            padding: 15px;
            background: var(--bg-light);
            border-radius: 8px;
        }

        .separator-options h4 {
            margin: 0 0 15px 0;
            color: var(--text);
            font-size: 16px;
        }

        .separator-options .option-group {
            margin-bottom: 15px;
        }

        .separator-options label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: var(--text);
            font-size: 14px;
        }

        .separator-options p {
            margin: 0;
            padding: 8px 12px;
            background: white;
            border-radius: 4px;
            color: var(--text-muted);
            font-size: 14px;
        }

        .separator-options .action-buttons {
            display: flex;
            gap: 8px;
        }

        .separator-options .action-btn {
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 4px;
            padding: 8px 12px;
            cursor: pointer;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .separator-options .action-btn:hover {
            background: var(--primary-dark);
        }

        /* Styles pour le module Tableau */
        .table-display {
            padding: 10px;
            border: 1px solid var(--border);
            border-radius: 5px;
            background: var(--bg-light);
            margin: 5px 0;
        }

        .table-placeholder {
            text-align: center;
            padding: 40px 20px;
            color: var(--text-muted);
        }

        .placeholder-icon {
            font-size: 48px;
            margin-bottom: 10px;
        }

        .placeholder-text {
            font-size: 16px;
            font-weight: 500;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
        }

        .table th,
        .table td {
            padding: 8px;
            text-align: left;
            border: 1px solid #dee2e6;
        }

        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }

        /* Styles de tableau */
        .table-default th,
        .table-default td {
            border: 1px solid #dee2e6;
        }

        .table-striped tbody tr:nth-child(odd) {
            background-color: #f8f9fa;
        }

        .table-bordered th,
        .table-bordered td {
            border: 2px solid #dee2e6;
        }

        .table-compact th,
        .table-compact td {
            padding: 4px 6px;
            font-size: 14px;
        }

        .table-modern {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .table-modern th {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            border: none;
        }

        .table-modern td {
            border: none;
            border-bottom: 1px solid #eee;
        }

        /* Alignement des tableaux */
        .table-align-left {
            text-align: left;
        }

        .table-align-center {
            text-align: center;
        }

        .table-align-right {
            text-align: right;
        }

        /* Éditeur de tableau */
        .table-editor {
            padding: 15px;
            background: var(--bg-light);
            border-radius: 8px;
        }

        .table-config {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 20px;
        }

        .config-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .config-group label {
            font-weight: 600;
            color: var(--text);
            font-size: 14px;
        }

        .config-group input[type="number"] {
            padding: 8px;
            border: 1px solid var(--border);
            border-radius: 4px;
            font-size: 14px;
        }

        .config-group input[type="checkbox"] {
            margin-right: 8px;
        }

        .style-buttons {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-bottom: 15px;
        }

        .style-btn {
            padding: 8px 12px;
            border: 1px solid var(--border);
            border-radius: 4px;
            background: white;
            cursor: pointer;
            font-size: 12px;
            transition: all 0.2s;
            flex: 1;
            min-width: 100px;
        }

        .style-btn.active {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .alignment-buttons {
            display: flex;
            gap: 8px;
            margin-bottom: 15px;
        }

        .align-btn {
            flex: 1;
            padding: 8px 12px;
            border: 1px solid var(--border);
            border-radius: 4px;
            background: white;
            cursor: pointer;
            font-size: 12px;
            transition: all 0.2s;
        }

        .align-btn.active {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .appearance-controls {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-bottom: 20px;
        }

        .control-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .control-group label {
            min-width: 100px;
            font-weight: 600;
            color: var(--text);
            font-size: 14px;
        }

        .control-group input[type="range"] {
            flex: 1;
            height: 6px;
            border-radius: 3px;
            background: var(--border);
            outline: none;
            -webkit-appearance: none;
        }

        .control-group input[type="range"]::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: var(--primary);
            cursor: pointer;
        }

        .control-group input[type="color"] {
            width: 50px;
            height: 30px;
            border: 1px solid var(--border);
            border-radius: 4px;
            cursor: pointer;
        }

        .control-group span {
            min-width: 50px;
            text-align: right;
            font-size: 14px;
            color: var(--text);
            font-weight: 600;
        }

        .table-grid {
            margin-top: 20px;
        }

        .table-grid-container {
            border: 1px solid var(--border);
            border-radius: 4px;
            overflow: hidden;
            background: white;
        }

        .table-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
            gap: 0;
        }

        .editable-cell {
            padding: 8px;
            border: 1px solid var(--border);
            background: white;
            min-height: 40px;
            outline: none;
            transition: all 0.2s;
            cursor: text;
            color: #333;
            font-size: 14px;
            line-height: 1.4;
            position: relative;
            z-index: 10;
        }

        .editable-cell:focus {
            background: #f8f9fa;
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(220, 53, 69, 0.25);
            z-index: 20;
        }

        .editable-cell:hover {
            background: #f0f0f0;
        }

        .editable-cell.header-cell {
            background: #f8f9fa;
            font-weight: bold;
            color: #495057;
        }

        .editable-cell.header-cell:focus {
            background: #e9ecef;
        }

        .editable-cell[contenteditable="true"]:empty::before {
            content: "Cliquez pour éditer";
            color: var(--text-muted);
            font-style: italic;
        }

        /* Options du tableau */
        .table-options {
            padding: 15px;
            background: var(--bg-light);
            border-radius: 8px;
        }

        .table-options h4 {
            margin: 0 0 15px 0;
            color: var(--text);
            font-size: 16px;
        }

        .table-options .option-group {
            margin-bottom: 15px;
        }

        .table-options label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: var(--text);
            font-size: 14px;
        }

        .table-options p {
            margin: 0;
            padding: 8px 12px;
            background: white;
            border-radius: 4px;
            color: var(--text-muted);
            font-size: 14px;
        }

        .table-options .action-buttons {
            display: flex;
            gap: 8px;
        }

        .table-options .action-btn {
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 4px;
            padding: 8px 12px;
            cursor: pointer;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .table-options .action-btn:hover {
            background: var(--primary-dark);
        }

        /* Styles pour le module Bouton */
        .button-display {
            padding: 10px;
            border: 1px solid var(--border);
            border-radius: 5px;
            background: var(--bg-light);
            margin: 5px 0;
        }

        .button-container {
            display: flex;
            align-items: center;
        }

        .button-align-left {
            justify-content: flex-start;
        }

        .button-align-center {
            justify-content: center;
        }

        .button-align-right {
            justify-content: flex-end;
        }

        .button-full-width .custom-button {
            width: 100%;
        }

        .custom-button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border: 2px solid transparent;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 14px;
            line-height: 1.4;
        }

        .custom-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .custom-button:active {
            transform: translateY(0);
        }

        /* Styles de boutons */
        .btn-primary {
            background-color: #007bff;
            color: white;
            border-color: #007bff;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }

        .btn-secondary {
            background-color: #6c757d;
            color: white;
            border-color: #6c757d;
        }

        .btn-secondary:hover {
            background-color: #545b62;
            border-color: #545b62;
        }

        .btn-success {
            background-color: #28a745;
            color: white;
            border-color: #28a745;
        }

        .btn-success:hover {
            background-color: #1e7e34;
            border-color: #1e7e34;
        }

        .btn-danger {
            background-color: #dc3545;
            color: white;
            border-color: #dc3545;
        }

        .btn-danger:hover {
            background-color: #c82333;
            border-color: #c82333;
        }

        .btn-warning {
            background-color: #ffc107;
            color: #212529;
            border-color: #ffc107;
        }

        .btn-warning:hover {
            background-color: #e0a800;
            border-color: #e0a800;
        }

        .btn-info {
            background-color: #17a2b8;
            color: white;
            border-color: #17a2b8;
        }

        .btn-info:hover {
            background-color: #138496;
            border-color: #138496;
        }

        .btn-light {
            background-color: #f8f9fa;
            color: #212529;
            border-color: #f8f9fa;
        }

        .btn-light:hover {
            background-color: #e2e6ea;
            border-color: #e2e6ea;
        }

        .btn-dark {
            background-color: #343a40;
            color: white;
            border-color: #343a40;
        }

        .btn-dark:hover {
            background-color: #23272b;
            border-color: #23272b;
        }

        /* Boutons outline */
        .btn-outline-primary {
            background-color: transparent;
            color: #007bff;
            border-color: #007bff;
        }

        .btn-outline-primary:hover {
            background-color: #007bff;
            color: white;
        }

        .btn-outline-secondary {
            background-color: transparent;
            color: #6c757d;
            border-color: #6c757d;
        }

        .btn-outline-secondary:hover {
            background-color: #6c757d;
            color: white;
        }

        /* Tailles de boutons */
        .btn-small {
            padding: 6px 12px;
            font-size: 12px;
        }

        .btn-medium {
            padding: 10px 20px;
            font-size: 14px;
        }

        .btn-large {
            padding: 14px 28px;
            font-size: 16px;
        }

        /* Effets de boutons */
        .btn-rounded {
            border-radius: 25px;
        }

        .btn-shadow {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .btn-shadow:hover {
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
        }

        /* Animations de boutons */
        .btn-animation-pulse {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .btn-animation-bounce {
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-10px); }
            60% { transform: translateY(-5px); }
        }

        .btn-animation-shake {
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        .btn-animation-glow {
            animation: glow 2s ease-in-out infinite alternate;
        }

        @keyframes glow {
            from { box-shadow: 0 0 5px currentColor; }
            to { box-shadow: 0 0 20px currentColor, 0 0 30px currentColor; }
        }

        /* Éditeur de bouton */
        .button-editor {
            padding: 15px;
            background: var(--bg-light);
            border-radius: 8px;
        }

        .content-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
            margin-bottom: 15px;
        }

        .content-group label {
            font-weight: 600;
            color: var(--text);
            font-size: 14px;
        }

        .content-group input,
        .content-group select {
            padding: 8px;
            border: 1px solid var(--border);
            border-radius: 4px;
            font-size: 14px;
        }

        .content-group input:focus,
        .content-group select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(220, 53, 69, 0.25);
        }

        .size-buttons,
        .width-buttons {
            display: flex;
            gap: 8px;
            margin-bottom: 15px;
        }

        .size-btn,
        .width-btn {
            flex: 1;
            padding: 8px 12px;
            border: 1px solid var(--border);
            border-radius: 4px;
            background: white;
            cursor: pointer;
            font-size: 12px;
            transition: all 0.2s;
        }

        .size-btn.active,
        .width-btn.active {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .effects-group {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-bottom: 15px;
        }

        .effects-group label {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            font-size: 14px;
        }

        .effects-group input[type="checkbox"] {
            width: 16px;
            height: 16px;
        }

        .animation-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .animation-group label {
            font-weight: 600;
            color: var(--text);
            font-size: 14px;
        }

        .animation-group select {
            padding: 8px;
            border: 1px solid var(--border);
            border-radius: 4px;
            font-size: 14px;
        }

        .color-controls {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .color-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .color-group label {
            min-width: 120px;
            font-weight: 600;
            color: var(--text);
            font-size: 14px;
        }

        .color-group input[type="color"] {
            width: 50px;
            height: 30px;
            border: 1px solid var(--border);
            border-radius: 4px;
            cursor: pointer;
        }

        /* Options du bouton */
        .button-options {
            padding: 15px;
            background: var(--bg-light);
            border-radius: 8px;
        }

        .button-options h4 {
            margin: 0 0 15px 0;
            color: var(--text);
            font-size: 16px;
        }

        .button-options .option-group {
            margin-bottom: 15px;
        }

        .button-options label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: var(--text);
            font-size: 14px;
        }

        .button-options p {
            margin: 0;
            padding: 8px 12px;
            background: white;
            border-radius: 4px;
            color: var(--text-muted);
            font-size: 14px;
        }

        .button-options .action-buttons {
            display: flex;
            gap: 8px;
        }

        .button-options .action-btn {
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 4px;
            padding: 8px 12px;
            cursor: pointer;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .button-options .action-btn:hover {
            background: var(--primary-dark);
        }

        /* Styles pour le popup du module Liste */
        .list-popup-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 10000;
            backdrop-filter: blur(5px);
        }

        .list-popup {
            background: var(--primary);
            border: 2px solid var(--belgium-yellow);
            border-radius: 12px;
            width: 90%;
            max-width: 800px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            animation: popupSlideIn 0.3s ease-out;
        }

        @keyframes popupSlideIn {
            from {
                opacity: 0;
                transform: translateY(-50px) scale(0.9);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .popup-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 25px;
            border-bottom: 1px solid var(--border);
            background: linear-gradient(135deg, var(--primary), var(--secondary));
        }

        .popup-header h3 {
            margin: 0;
            color: var(--text);
            font-size: 1.4em;
            font-weight: 600;
        }

        .close-popup-btn {
            background: none;
            border: none;
            color: var(--text-muted);
            font-size: 1.5em;
            cursor: pointer;
            padding: 5px;
            border-radius: 50%;
            transition: all 0.2s;
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .close-popup-btn:hover {
            background-color: var(--bg-light);
            color: var(--text);
        }

        .popup-content {
            padding: 25px;
        }

        .popup-section {
            margin-bottom: 30px;
        }

        .popup-section h4 {
            margin: 0 0 15px 0;
            color: var(--text);
            font-size: 1.1em;
            font-weight: 600;
            border-bottom: 2px solid var(--belgium-yellow);
            padding-bottom: 8px;
        }

        .list-items-container {
            max-height: 300px;
            overflow-y: auto;
            border: 1px solid var(--border);
            border-radius: 8px;
            background: var(--secondary);
            margin-bottom: 15px;
        }

        .list-item-row {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            border-bottom: 1px solid var(--border);
            background: var(--primary);
            transition: background-color 0.2s;
        }

        .list-item-row:last-child {
            border-bottom: none;
        }

        .list-item-row:hover {
            background: var(--bg-light);
        }

        .item-controls {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-right: 15px;
            flex-shrink: 0;
        }

        .level-btn {
            background: var(--secondary);
            border: 1px solid var(--border);
            color: var(--text);
            padding: 6px 8px;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 0.9em;
        }

        .level-btn:hover:not(:disabled) {
            background: var(--belgium-yellow);
            color: var(--primary);
            border-color: var(--belgium-yellow);
        }

        .level-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .level-indicator {
            background: var(--bg-light);
            color: var(--text);
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.8em;
            font-weight: 500;
            min-width: 50px;
            text-align: center;
        }

        .remove-item-btn {
            background: #dc3545;
            border: none;
            color: white;
            padding: 6px 8px;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 0.9em;
        }

        .remove-item-btn:hover:not(:disabled) {
            background: #c82333;
            transform: scale(1.05);
        }

        .remove-item-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .item-text-input {
            flex: 1;
            background: var(--secondary);
            border: 1px solid var(--border);
            color: var(--text);
            padding: 10px 12px;
            border-radius: 6px;
            font-size: 0.95em;
            transition: border-color 0.2s;
        }

        .item-text-input:focus {
            outline: none;
            border-color: var(--belgium-yellow);
            box-shadow: 0 0 0 2px rgba(255, 215, 0, 0.2);
        }

        .item-text-input::placeholder {
            color: var(--text-muted);
        }

        .add-item-btn {
            background: var(--belgium-yellow);
            border: none;
            color: var(--primary);
            padding: 12px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.2s;
            width: 100%;
        }

        .add-item-btn:hover {
            background: #ffd700;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(255, 215, 0, 0.3);
        }

        .style-options {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .option-group {
            margin-bottom: 20px;
        }

        .option-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--text);
            font-weight: 500;
            font-size: 0.9em;
        }

        .list-type-buttons,
        .style-buttons,
        .spacing-buttons,
        .alignment-buttons {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .type-btn,
        .style-btn,
        .spacing-btn,
        .alignment-btn {
            background: var(--secondary);
            border: 1px solid var(--border);
            color: var(--text);
            padding: 8px 12px;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 0.85em;
            flex: 1;
            min-width: 80px;
        }

        .type-btn:hover,
        .style-btn:hover,
        .spacing-btn:hover,
        .alignment-btn:hover {
            background: var(--bg-light);
            border-color: var(--belgium-yellow);
        }

        .type-btn.active,
        .style-btn.active,
        .spacing-btn.active,
        .alignment-btn.active {
            background: var(--belgium-yellow);
            color: var(--primary);
            border-color: var(--belgium-yellow);
            font-weight: 600;
        }

        .bullet-style-select {
            background: var(--secondary);
            border: 1px solid var(--border);
            color: var(--text);
            padding: 10px 12px;
            border-radius: 6px;
            font-size: 0.9em;
            width: 100%;
            cursor: pointer;
        }

        .bullet-style-select:focus {
            outline: none;
            border-color: var(--belgium-yellow);
            box-shadow: 0 0 0 2px rgba(255, 215, 0, 0.2);
        }

        .list-color-input {
            width: 60px;
            height: 40px;
            border: 2px solid var(--border);
            border-radius: 6px;
            cursor: pointer;
            background: none;
        }

        .list-color-input::-webkit-color-swatch-wrapper {
            padding: 0;
        }

        .list-color-input::-webkit-color-swatch {
            border: none;
            border-radius: 4px;
        }

        .popup-actions {
            display: flex;
            gap: 15px;
            padding: 20px 25px;
            border-top: 1px solid var(--border);
            background: var(--secondary);
            justify-content: flex-end;
        }

        .save-popup-btn,
        .cancel-popup-btn {
            padding: 12px 24px;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
            font-size: 0.95em;
        }

        .save-popup-btn {
            background: var(--belgium-yellow);
            color: var(--primary);
        }

        .save-popup-btn:hover {
            background: #ffd700;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(255, 215, 0, 0.3);
        }

        .cancel-popup-btn {
            background: var(--bg-light);
            color: var(--text);
            border: 1px solid var(--border);
        }

        .cancel-popup-btn:hover {
            background: var(--bg-hover);
            border-color: var(--belgium-yellow);
        }
            </style>
        `;
        
        document.head.insertAdjacentHTML('beforeend', styles);
        this.stylesAdded = true;
    }
}

// Rendre disponible globalement
window.StyleManager = StyleManager;
