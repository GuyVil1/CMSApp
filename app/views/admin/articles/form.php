<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $article ? 'Modifier' : 'Créer' ?> un article - Belgium Vidéo Gaming</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            color: white;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #ffd700;
            margin: 0;
            font-size: 2.5em;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #e74c3c;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s;
            border: none;
            cursor: pointer;
            font-size: 14px;
            margin-right: 10px;
        }
        .btn:hover {
            background: #c0392b;
        }
        .btn-success {
            background: #27ae60;
        }
        .btn-success:hover {
            background: #229954;
        }
        .btn-warning {
            background: #f39c12;
        }
        .btn-warning:hover {
            background: #e67e22;
        }
        .btn-secondary {
            background: #95a5a6;
        }
        .btn-secondary:hover {
            background: #7f8c8d;
        }
        .btn-primary {
            background: #3498db;
        }
        .btn-primary:hover {
            background: #2980b9;
        }
        .btn-small {
            padding: 6px 12px;
            font-size: 12px;
        }
        .btn-sm {
            padding: 6px 12px;
            font-size: 12px;
        }
        .btn-outline-secondary {
            background: transparent;
            border: 1px solid #95a5a6;
            color: #95a5a6;
        }
        .btn-outline-secondary:hover {
            background: #95a5a6;
            color: white;
        }
        
        /* Cover image styles */
        .existing-cover-preview {
            margin: 15px 0;
            padding: 15px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.05);
        }
        
        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 5px;
            background: rgba(0, 0, 0, 0.3);
            color: white;
            font-family: inherit;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #ffd700;
            box-shadow: 0 0 0 2px rgba(255, 215, 0, 0.2);
        }
        
        /* Formulaire */
        .form-container {
            background: rgba(255, 255, 255, 0.1);
            padding: 30px;
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .form-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
        }
        .form-main {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .form-sidebar {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .form-group {
            display: flex;
            flex-direction: column;
        }
        .form-group label {
            margin-bottom: 8px;
            color: #ffd700;
            font-weight: bold;
            font-size: 14px;
        }
        .form-group input,
        .form-group select,
        .form-group textarea {
            padding: 12px;
        }
        
        .content-editor-wrapper {
            margin: 20px 0;
        }
        
        .content-preview {
            margin-top: 15px;
            padding: 15px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 6px;
            background: rgba(255, 255, 255, 0.05);
            min-height: 100px;
            max-height: 300px;
            overflow-y: auto;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
        
        .preview-content {
            font-size: 14px;
            line-height: 1.5;
            word-wrap: break-word;
            overflow-wrap: break-word;
            max-width: 100%;
            overflow-x: hidden;
        }
        
        /* Styles pour éviter le débordement du contenu HTML */
        .preview-content * {
            max-width: 100% !important;
            word-wrap: break-word !important;
            overflow-wrap: break-word !important;
            box-sizing: border-box !important;
        }
        
        .preview-content img,
        .preview-content video {
            max-width: 100% !important;
            height: auto !important;
        }
        
        .preview-content table {
            max-width: 100% !important;
            table-layout: fixed !important;
        }
        
        .preview-content pre,
        .preview-content code {
            white-space: pre-wrap !important;
            word-wrap: break-word !important;
            overflow-wrap: break-word !important;
        }
        
        .preview-placeholder {
            color: #ccc;
            text-align: center;
            padding: 20px;
        }
        
        .preview-placeholder p {
            margin: 0;
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 5px;
            background: rgba(0, 0, 0, 0.3);
            color: white;
            font-size: 14px;
            font-family: inherit;
        }
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #ffd700;
            box-shadow: 0 0 0 2px rgba(255, 215, 0, 0.2);
        }
        .form-group input::placeholder,
        .form-group textarea::placeholder {
            color: #999;
        }
        
        /* Éditeur WYSIWYG */
        .wysiwyg-container {
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 5px;
            overflow: hidden;
            background: white;
        }
        
        /* Position en avant */
        .featured-positions {
            background: rgba(0, 0, 0, 0.2);
            padding: 20px;
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .featured-positions h3 {
            color: #ffd700;
            margin: 0 0 15px 0;
            font-size: 16px;
        }
        .position-option {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 5px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .position-option input[type="radio"] {
            margin-right: 10px;
        }
        .position-option label {
            color: white;
            margin: 0;
            cursor: pointer;
            flex: 1;
        }
        .position-available {
            border-color: #27ae60;
            background: rgba(39, 174, 96, 0.1);
        }
        .position-unavailable {
            border-color: #e74c3c;
            background: rgba(231, 76, 60, 0.1);
            opacity: 0.6;
        }
        .position-unavailable input,
        .position-unavailable label {
            cursor: not-allowed;
        }
        
        /* Nouveau design en quadrillage pour les positions en avant */
        .featured-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-top: 20px;
        }
        
        .grid-item {
            position: relative;
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.05);
            transition: all 0.3s ease;
            cursor: pointer;
            overflow: hidden;
        }
        
        .grid-item:hover {
            border-color: #ffd700;
            background: rgba(255, 215, 0, 0.1);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 215, 0, 0.2);
        }
        
        .grid-item input[type="radio"] {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }
        
        .grid-item input[type="radio"]:checked + .grid-label {
            border-color: #27ae60;
            background: rgba(39, 174, 96, 0.2);
            box-shadow: 0 0 20px rgba(39, 174, 96, 0.3);
        }
        
        .grid-label {
            display: block;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .grid-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
        }
        
        .grid-icon {
            font-size: 24px;
            font-weight: bold;
            color: #ffd700;
        }
        
        .grid-title {
            font-size: 16px;
            font-weight: 600;
            color: white;
            margin: 0;
        }
        
        .grid-desc {
            font-size: 12px;
            color: #ccc;
            text-align: center;
            line-height: 1.3;
        }
        
        .grid-current {
            margin-top: 8px;
            padding: 4px 8px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 4px;
            font-size: 10px;
            color: #ffd700;
        }
        
        .grid-item-none {
            grid-column: 1 / -1;
            background: rgba(128, 128, 128, 0.1);
        }
        
        .grid-item-none:hover {
            border-color: #95a5a6;
            background: rgba(149, 165, 166, 0.2);
        }
        
        .grid-item-none .grid-icon {
            color: #95a5a6;
        }
        
        /* Responsive pour le quadrillage */
        @media (max-width: 768px) {
            .featured-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .grid-item-none {
                grid-column: 1 / -1;
            }
        }
        
        @media (max-width: 480px) {
            .featured-grid {
                grid-template-columns: 1fr;
            }
        }
        
        /* Tags */
        .tags-container {
            background: rgba(0, 0, 0, 0.2);
            padding: 20px;
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .tags-container h3 {
            color: #ffd700;
            margin: 0 0 15px 0;
            font-size: 16px;
        }
        
        /* Styles pour l'autocomplete des tags */
        .tag-search-container {
            position: relative;
            margin-bottom: 15px;
        }

        .tag-search {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 5px;
            background: rgba(0, 0, 0, 0.3);
            color: white;
            font-size: 14px;
            font-family: inherit;
            box-sizing: border-box;
        }

        .tag-search:focus {
            outline: none;
            border-color: #ffd700;
            box-shadow: 0 0 0 2px rgba(255, 215, 0, 0.2);
        }

        .tags-dropdown {
            position: absolute;
            top: 100%;
            left: 0;
            width: 100%;
            max-height: 200px;
            overflow-y: auto;
            background: rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 5px;
            z-index: 1000;
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.3);
            display: none;
        }

        .tags-dropdown.active {
            display: block;
        }

        .tags-dropdown ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .tags-dropdown li {
            padding: 10px 15px;
            cursor: pointer;
            color: white;
            font-size: 14px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .tags-dropdown li:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .selected-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 10px;
            padding: 8px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 5px;
        }

        .selected-tag {
            display: flex;
            align-items: center;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 4px;
            padding: 4px 8px;
            font-size: 12px;
            color: #ffd700;
        }

        .selected-tag .remove-tag {
            background: none;
            border: none;
            color: #ffd700;
            cursor: pointer;
            font-size: 16px;
            margin-left: 5px;
        }

        /* ========================================
           STYLES POUR LE GESTIONNAIRE DE CHAPITRES
           ======================================== */
        
        /* Section dossiers */
        #dossier-section {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 215, 0, 0.3);
            border-radius: 12px;
            padding: 25px;
            margin-top: 20px;
        }
        
        .dossier-header h3 {
            color: #ffd700;
            margin: 0 0 10px 0;
            font-size: 1.4em;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .dossier-actions {
            margin: 20px 0;
            text-align: center;
        }
        
        .dossier-workflow-info {
            background: rgba(52, 152, 219, 0.1);
            border: 1px solid rgba(52, 152, 219, 0.3);
            border-radius: 8px;
            padding: 15px;
            margin: 15px 0;
        }
        
        .dossier-workflow-info p {
            margin: 0;
            color: #3498db;
            font-size: 14px;
            line-height: 1.4;
        }
        
        .chapters-container {
            margin-top: 20px;
        }
        
        .chapters-placeholder {
            text-align: center;
            padding: 30px;
            color: #ccc;
            background: rgba(255, 255, 255, 0.03);
            border: 2px dashed rgba(255, 255, 255, 0.2);
            border-radius: 8px;
        }
        
        .chapters-loading {
            text-align: center;
            padding: 30px;
            color: #ffd700;
            background: rgba(255, 215, 0, 0.05);
            border: 2px dashed rgba(255, 215, 0, 0.3);
            border-radius: 8px;
        }
        
        .chapters-loading p {
            margin: 0;
            font-size: 16px;
            font-weight: 600;
        }
        
        .chapters-error {
            text-align: center;
            padding: 30px;
            color: #e74c3c;
            background: rgba(231, 76, 60, 0.05);
            border: 2px dashed rgba(231, 76, 60, 0.3);
            border-radius: 8px;
        }
        
        .chapters-error p {
            margin: 0;
            font-size: 16px;
            font-weight: 600;
        }
        
        /* Modal du gestionnaire de chapitres */
        .chapter-manager-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            z-index: 10000;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            box-sizing: border-box;
        }
        
        .chapter-manager-container {
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            border: 1px solid rgba(255, 215, 0, 0.3);
            border-radius: 15px;
            width: 100%;
            max-width: 1200px;
            max-height: 90vh;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5);
        }
        
        .chapter-manager-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 25px 30px;
            background: rgba(255, 215, 0, 0.1);
            border-bottom: 1px solid rgba(255, 215, 0, 0.2);
        }
        
        .chapter-manager-header h2 {
            color: #ffd700;
            margin: 0;
            font-size: 1.8em;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .dossier-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .dossier-id {
            background: rgba(255, 215, 0, 0.2);
            color: #ffd700;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            border: 1px solid rgba(255, 215, 0, 0.3);
        }
        
        .chapter-manager-header .close-btn {
            background: rgba(231, 76, 60, 0.8);
            border: none;
            color: white;
            padding: 10px 15px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        
        .chapter-manager-header .close-btn:hover {
            background: rgba(231, 76, 60, 1);
            transform: scale(1.05);
        }
        
        .chapter-manager-body {
            padding: 30px;
            max-height: calc(90vh - 100px);
            overflow-y: auto;
        }
        
        .chapters-toolbar {
            display: flex;
            gap: 15px;
            margin-bottom: 30px;
            padding: 20px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .chapters-list-container {
            background: rgba(255, 255, 255, 0.03);
            border-radius: 10px;
            padding: 20px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .chapters-list {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        
        .chapter-item {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 12px;
            padding: 20px;
            transition: all 0.3s ease;
        }
        
        .chapter-item:hover {
            border-color: rgba(255, 215, 0, 0.4);
            background: rgba(255, 255, 255, 0.1);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        }
        
        .chapter-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
            gap: 15px;
        }
        
        .chapter-info {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        
        .chapter-title,
        .chapter-slug {
            padding: 10px 12px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 6px;
            background: rgba(0, 0, 0, 0.3);
            color: white;
            font-size: 14px;
            font-family: inherit;
        }
        
        .chapter-title {
            font-weight: 600;
            font-size: 16px;
        }
        
        .chapter-slug {
            font-size: 13px;
            color: #ccc;
        }
        
        .chapter-title:focus,
        .chapter-slug:focus {
            outline: none;
            border-color: #ffd700;
            box-shadow: 0 0 0 2px rgba(255, 215, 0, 0.2);
        }
        
        .chapter-actions {
            display: flex;
            gap: 8px;
            flex-shrink: 0;
        }
        
        .chapter-actions .btn-sm {
            padding: 8px 12px;
            font-size: 12px;
            min-width: 40px;
        }
        
        .chapter-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .chapter-preview {
            flex: 1;
        }
        
        .chapter-excerpt {
            color: #ccc;
            margin: 0;
            font-style: italic;
        }
        
        .chapter-metadata {
            display: flex;
            gap: 15px;
            align-items: center;
        }
        
        .chapter-status {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .chapter-status.draft {
            background: rgba(243, 156, 18, 0.2);
            color: #f39c12;
            border: 1px solid rgba(243, 156, 18, 0.3);
        }
        
        .chapter-status.published {
            background: rgba(39, 174, 96, 0.2);
            color: #27ae60;
            border: 1px solid rgba(39, 174, 96, 0.3);
        }
        
        .chapter-reading-time {
            color: #95a5a6;
            font-size: 12px;
            font-weight: 500;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .chapter-header {
                flex-direction: column;
                align-items: stretch;
            }
            
            .chapter-actions {
                justify-content: center;
                margin-top: 10px;
            }
            
            .chapter-content {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            
            .chapter-manager-container {
                margin: 10px;
                max-height: 95vh;
            }
        }
        
        /* ========================================
           STYLES POUR LA LISTE DES CHAPITRES SAUVEGARDÉS
           ======================================== */
        
        /* Styles pour la liste des chapitres sauvegardés */
        .chapter-list-item {
            background: rgba(248, 249, 250, 0.95);
            border: 2px solid rgba(0, 123, 255, 0.6);
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 4px 15px rgba(0, 123, 255, 0.15);
            transition: all 0.3s ease;
        }
        
        .chapter-list-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 123, 255, 0.25);
            border-color: rgba(0, 123, 255, 0.8);
        }
        
        .chapter-list-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid rgba(233, 236, 239, 0.6);
        }
        
        .chapter-list-info {
            flex: 1;
        }
        
        .chapter-list-title {
            font-size: 1.4rem;
            font-weight: 700;
            color: #2c3e50;
            margin: 0 0 8px 0;
            line-height: 1.3;
        }
        
        .chapter-list-slug {
            font-size: 0.9rem;
            color: #6c757d;
            font-family: 'Courier New', monospace;
            background: rgba(233, 236, 239, 0.8);
            padding: 6px 10px;
            border-radius: 6px;
            display: inline-block;
            border: 1px solid rgba(108, 117, 125, 0.2);
        }
        
        .chapter-list-actions {
            display: flex;
            gap: 10px;
            flex-shrink: 0;
        }
        
        .chapter-list-content {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 8px;
            padding: 20px;
            border: 1px solid rgba(222, 226, 230, 0.6);
        }
        
        .chapter-list-preview {
            margin-bottom: 15px;
        }
        
        .chapter-content-preview {
            max-height: 200px;
            overflow-y: auto;
            border: 1px solid rgba(233, 236, 239, 0.8);
            border-radius: 6px;
            padding: 15px;
            background: rgba(248, 249, 250, 0.8);
        }
        
        .no-content {
            color: #6c757d;
            font-style: italic;
            text-align: center;
            margin: 20px 0;
        }
        
        .chapter-list-metadata {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 15px;
            border-top: 1px solid rgba(233, 236, 239, 0.6);
        }
        
        /* Statuts des chapitres dans la liste */
        .chapter-status.content-created {
            background: rgba(23, 162, 184, 0.2);
            color: #17a2b8;
            border: 1px solid rgba(23, 162, 184, 0.3);
        }
        
        /* Bouton d'éditeur dans le chapitre */
        .chapter-editor-actions {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .chapter-editor-actions .btn {
            width: 100%;
            padding: 12px 20px;
            font-size: 1rem;
            font-weight: 600;
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            border: none;
            border-radius: 8px;
            color: white;
            transition: all 0.3s ease;
        }
        
        .chapter-editor-actions .btn:hover {
            background: linear-gradient(135deg, #0056b3 0%, #004085 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
        }
        
        /* Zone d'aperçu du contenu du chapitre */
        .chapter-content-preview {
            margin-top: 20px;
            padding: 20px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
        }
        
        .chapter-content-preview h4 {
            color: #ffd700;
            margin: 0 0 15px 0;
            font-size: 1.1rem;
            font-weight: 600;
        }
        
        .chapter-content-display {
            max-height: 300px;
            overflow-y: auto;
            padding: 15px;
            background: rgba(0, 0, 0, 0.2);
            border-radius: 6px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        /* ========================================
           STYLES POUR LES NOTIFICATIONS
           ======================================== */
        
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 10000;
            max-width: 400px;
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
            transform: translateX(100%);
            transition: all 0.3s ease;
            overflow: hidden;
        }
        
        .notification.show {
            transform: translateX(0);
        }
        
        .notification-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px 20px;
            color: white;
        }
        
        .notification-message {
            flex: 1;
            font-size: 14px;
            font-weight: 500;
            line-height: 1.4;
        }
        
        .notification-close {
            background: none;
            border: none;
            color: rgba(255, 255, 255, 0.7);
            font-size: 20px;
            cursor: pointer;
            padding: 0;
            margin-left: 15px;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.2s ease;
        }
        
        .notification-close:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }
        
        /* Types de notifications */
        .notification-success {
            border-left: 4px solid #27ae60;
        }
        
        .notification-warning {
            border-left: 4px solid #f39c12;
        }
        
        .notification-error {
            border-left: 4px solid #e74c3c;
        }
        
        .notification-info {
            border-left: 4px solid #3498db;
        }
        
        /* ========================================
           STYLES POUR L'IMAGE DE COUVERTURE
           ======================================== */
        
        .chapter-cover-display {
            margin-top: 20px;
            padding: 20px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
        }
        
        .chapter-cover-info h5 {
            color: #ffd700;
            margin: 0 0 15px 0;
            font-size: 1rem;
            font-weight: 600;
        }
        
        .chapter-cover-preview {
            display: flex;
            align-items: center;
            gap: 15px;
            background: rgba(0, 0, 0, 0.2);
            border-radius: 6px;
            padding: 15px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .cover-thumbnail {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 6px;
            border: 2px solid rgba(255, 255, 255, 0.2);
        }
        
        .cover-details {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        
        .cover-title {
            color: white;
            font-weight: 600;
            font-size: 14px;
        }
        
        .cover-filename {
            color: #ccc;
            font-size: 12px;
            font-family: 'Courier New', monospace;
        }
        
        /* ========================================
           STYLES POUR L'ÉDITEUR DE CHAPITRE
           ======================================== */
        
        /* Modal de l'éditeur de chapitre */
        .chapter-editor-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            z-index: 10001;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            box-sizing: border-box;
        }
        
        .chapter-editor-container {
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            border: 1px solid rgba(255, 215, 0, 0.3);
            border-radius: 15px;
            width: 100%;
            max-width: 1000px;
            max-height: 90vh;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5);
        }
        
        .chapter-editor-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 25px 30px;
            background: rgba(52, 152, 219, 0.1);
            border-bottom: 1px solid rgba(52, 152, 219, 0.2);
        }
        
        .chapter-editor-header h2 {
            color: #3498db;
            margin: 0;
            font-size: 1.6em;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .chapter-editor-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .chapter-title-display {
            background: rgba(52, 152, 219, 0.2);
            color: #3498db;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            border: 1px solid rgba(52, 152, 219, 0.3);
        }
        
        .chapter-editor-header .close-btn {
            background: rgba(231, 76, 60, 0.8);
            border: none;
            color: white;
            padding: 10px 15px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        
        .chapter-editor-header .close-btn:hover {
            background: rgba(231, 76, 60, 1);
            transform: scale(1.05);
        }
        
        .chapter-editor-body {
            padding: 30px;
            max-height: calc(90vh - 100px);
            overflow-y: auto;
        }
        
        .chapter-editor-toolbar {
            display: flex;
            gap: 15px;
            margin-bottom: 30px;
            padding: 20px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .chapter-content-editor {
            background: rgba(255, 255, 255, 0.03);
            border-radius: 10px;
            padding: 20px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .chapter-content-preview {
            min-height: 200px;
            max-height: 400px;
            overflow-y: auto;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
            padding: 15px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .chapter-content-preview .preview-content {
            font-size: 14px;
            line-height: 1.6;
            color: #e0e0e0;
        }
        
        .chapter-content-preview .preview-placeholder {
            text-align: center;
            color: #999;
            font-style: italic;
        }
        
        .chapter-content-preview .preview-placeholder p {
            margin: 0;
        }
        
        /* Responsive pour l'éditeur de chapitre */
        @media (max-width: 768px) {
            .chapter-editor-container {
                margin: 10px;
                max-height: 95vh;
            }
            
            .chapter-editor-header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            
            .chapter-editor-toolbar {
                flex-direction: column;
                align-items: stretch;
            }
        }
            padding: 0;
            line-height: 1;
        }

        .selected-tag .remove-tag:hover {
            color: #e74c3c;
        }
        
        /* Actions */
        .form-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
            .header {
                flex-direction: column;
                gap: 20px;
                text-align: center;
            }
            .form-actions {
                flex-direction: column;
                gap: 15px;
            }
        }
        
        /* Messages flash */
        .flash {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid;
        }
        .flash-success {
            background: rgba(39, 174, 96, 0.2);
            border-color: #27ae60;
            color: #27ae60;
        }
        .flash-error {
            background: rgba(231, 76, 60, 0.2);
            border-color: #e74c3c;
            color: #e74c3c;
        }

        /* Nouveaux styles pour la gestion de l'image de couverture */
        .game-cover-preview {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 10px;
        }

        .game-cover-preview img {
            border: 1px solid #ccc;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .form-hint {
            font-size: 12px;
            color: #ccc;
            margin-top: 5px;
        }

    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><?= $article ? '✏️ Modifier' : '📝 Créer' ?> un article</h1>
            <div>
                <a href="/admin/articles" class="btn btn-secondary">🔙 Retour à la liste</a>
                <?php if ($article): ?>
                    <a href="/admin/articles" class="btn btn-warning">📋 Voir tous les articles</a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Messages flash -->
        <?php 
        // Charger le helper flash avec un chemin absolu depuis la racine du serveur
        // require_once $_SERVER['DOCUMENT_ROOT'] . '/app/helpers/flash_helper.php';
        // Afficher les messages flash
        // displayFlashMessages();
        
        // Affichage temporaire des messages flash
        if (isset($_SESSION['flash']) && !empty($_SESSION['flash'])) {
            foreach ($_SESSION['flash'] as $type => $message) {
                echo '<div class="flash flash-' . htmlspecialchars($type) . '">';
                echo htmlspecialchars($message);
                echo '</div>';
            }
            unset($_SESSION['flash']);
        }
        ?>

        <form method="POST" action="<?= $article ? "/admin/articles/update/{$article->getId()}" : '/admin/articles/store' ?>" class="form-container" enctype="multipart/form-data">
            <div class="form-grid">
                <!-- Colonne principale -->
                <div class="form-main">
                    <!-- Titre -->
                    <div class="form-group">
                        <label for="title">Titre de l'article *</label>
                        <input type="text" id="title" name="title" required 
                               value="<?= $article ? htmlspecialchars($article->getTitle()) : '' ?>"
                               placeholder="Entrez le titre de l'article">
                    </div>

                    <!-- Extrait -->
                    <div class="form-group">
                        <label for="excerpt">Extrait (résumé)</label>
                        <textarea id="excerpt" name="excerpt" rows="3" 
                                  placeholder="Résumé court de l'article (optionnel)"><?= $article ? htmlspecialchars($article->getExcerpt() ?? '') : '' ?></textarea>
                    </div>

                            <!-- Contenu -->
        <div class="form-group">
            <label for="content">Contenu de l'article *</label>
            <div class="content-editor-wrapper">
                <button type="button" id="open-fullscreen-editor" class="btn btn-primary">
                    <span class="icon">📝</span> Ouvrir l'éditeur plein écran
                </button>
                <div class="content-preview" id="content-preview">
                    <?php if ($article && $article->getContent()): ?>
                        <div class="preview-content">
                            <?= htmlspecialchars($article->getContent()) ?>
                        </div>
                    <?php else: ?>
                        <div class="preview-placeholder">
                            <p>Aucun contenu saisi. Cliquez sur "Ouvrir l'éditeur plein écran" pour commencer.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <textarea id="content" name="content" style="display: none;"><?= $article ? htmlspecialchars($article->getContent()) : '' ?></textarea>
        </div>

        <!-- Section Dossiers - Visible seulement lors de l'édition d'un article existant de catégorie "Dossiers" -->
        <?php if ($article): ?>
        <div id="dossier-section" class="form-group" style="display: none;">
            <div class="dossier-header">
                <h3>📚 Gestion des chapitres du dossier</h3>
                <p class="form-hint">Cette section vous permet de gérer les chapitres de votre dossier</p>
                <div class="dossier-workflow-info">
                    <p><strong>Workflow :</strong> Créez d'abord votre article, puis revenez l'éditer pour ajouter des chapitres.</p>
                </div>
            </div>
            
            <!-- Bouton pour ajouter un chapitre -->
            <div class="dossier-actions">
                <button type="button" id="add-chapter-btn" class="btn btn-success">
                    <span class="icon">➕</span> Ajouter un chapitre
                </button>
            </div>
            
            <!-- Liste des chapitres existants -->
            <div id="chapters-list" class="chapters-container">
                <div class="chapters-placeholder">
                    <p>Aucun chapitre créé pour le moment. Commencez par ajouter votre premier chapitre.</p>
                </div>
            </div>
        </div>
        <?php endif; ?>
                </div>

                <!-- Colonne latérale -->
                <div class="form-sidebar">
                    <!-- Catégorie -->
                    <div class="form-group">
                        <label for="category_id">Catégorie</label>
                        <select id="category_id" name="category_id">
                            <option value="">Aucune catégorie</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= $category['id'] ?>" 
                                    <?= $article && $article->getCategoryId() == $category['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($category['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Jeu associé -->
                    <div class="form-group">
                        <label for="game_id">Jeu associé</label>
                        <select id="game_id" name="game_id">
                            <option value="">Aucun jeu</option>
                            <?php foreach ($games as $game): ?>
                                <option value="<?= $game['id'] ?>" 
                                    <?= $article && $article->getGameId() == $game['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($game['title']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <p class="form-hint">Si sélectionné, la cover du jeu sera automatiquement utilisée</p>
                    </div>

                    <!-- Statut de l'article -->
                    <div class="form-group">
                        <label for="status">📊 Statut de l'article</label>
                        <select id="status" name="status" required>
                            <option value="draft" <?= (!$article || $article->getStatus() === 'draft') ? 'selected' : '' ?>>📝 Brouillon</option>
                            <?php if (isset($user) && $user['role'] === 'admin'): ?>
                                <option value="published" <?= ($article && $article->getStatus() === 'published') ? 'selected' : '' ?>>✅ Publié</option>
                                <option value="archived" <?= ($article && $article->getStatus() === 'archived') ? 'selected' : '' ?>>📦 Archivé</option>
                            <?php endif; ?>
                        </select>
                        <p class="form-hint">
                            <?php if (isset($user) && $user['role'] === 'admin'): ?>
                                Choisissez le statut de publication de l'article
                            <?php else: ?>
                                En tant que rédacteur, vous ne pouvez que sauvegarder en brouillon. Un administrateur devra publier l'article.
                            <?php endif; ?>
                        </p>
                    </div>

                    <!-- Date de publication (visible seulement si statut = publié) -->
                    <div class="form-group" id="published-date-group" style="display: none;">
                        <label for="published_at">📅 Date de publication</label>
                        <input type="datetime-local" id="published_at" name="published_at" 
                               value="<?= $article && $article->getPublishedAt() ? date('Y-m-d\TH:i', strtotime($article->getPublishedAt())) : date('Y-m-d\TH:i') ?>">
                        <p class="form-hint">Date et heure de publication (optionnel)</p>
                    </div>

                    <!-- Image de couverture -->
                    <div class="form-group" id="cover-image-group">
                        <label for="cover_image_id">🖼️ Image de couverture</label>

                        <!-- Affichage de l'image de couverture existante (en mode édition) -->
                        <?php if (isset($article) && $article && $article->getCoverImageId()): ?>
                            <?php 
                            $existingCoverImage = \Media::find($article->getCoverImageId());
                            if ($existingCoverImage): 
                            ?>
                            <div id="existing-cover-info" class="existing-cover-preview">
                                <img src="<?= htmlspecialchars($existingCoverImage->getUrl()) ?>" alt="Image de couverture actuelle" style="max-width: 200px; border-radius: 8px; margin: 10px 0;">
                                <p class="form-hint">Image de couverture actuelle : <?= htmlspecialchars($existingCoverImage->getFilename()) ?></p>
                                <input type="hidden" name="current_cover_image_id" value="<?= $existingCoverImage->getId() ?>">
                                <button type="button" class="btn btn-secondary btn-sm" onclick="window.changeCoverImage()">Changer l'image</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="window.keepCoverImage()" style="display: none;" id="keep-cover-btn">Garder l'image actuelle</button>
                            </div>
                            <?php endif; ?>
                        <?php endif; ?>

                        <!-- Si un jeu est sélectionné -->
                        <div id="game-cover-info" style="display: none;">
                            <div class="game-cover-preview">
                                <img id="game-cover-preview" src="" alt="Cover du jeu" style="max-width: 200px; border-radius: 8px; margin: 10px 0;">
                                <p class="form-hint">ℹ️ Cover du jeu sélectionné (informatif pour le design futur)</p>
                                <input type="hidden" id="game_cover_image_id" name="game_cover_image_id" value="">
                            </div>
                        </div>

                        <!-- Sélection manuelle de l'image (upload ou médiathèque) -->
                        <div id="manual-cover-selection" style="<?= (isset($article) && $article && $article->getCoverImageId()) ? 'display: none;' : 'display: block;' ?>">
                            <input type="file" id="cover_image_file" name="cover_image_file" accept="image/*" class="form-control">
                            <div id="upload-preview" style="margin-top: 10px; display: none;">
                                <img id="preview-image" src="" alt="Prévisualisation" style="max-width: 200px; border-radius: 8px;">
                            </div>
                            <p class="form-hint">Ou sélectionnez une image existante dans la médiathèque :</p>
                            <div style="display: flex; gap: 10px; align-items: center;">
                                <input type="number" id="cover_image_id" name="cover_image_id" class="form-control" placeholder="ID de l'image de la médiathèque" value="<?= htmlspecialchars($article && $article->getCoverImageId() ? $article->getCoverImageId() : '') ?>" style="flex: 1;">
                                <button type="button" class="btn btn-info" onclick="openMediaLibrary()" style="white-space: nowrap;">
                                    📚 Parcourir la médiathèque
                                </button>
                            </div>
                            <div id="selected-media-preview" style="margin-top: 10px; display: none;">
                                <img id="selected-media-image" src="" alt="Image sélectionnée" style="max-width: 200px; border-radius: 8px;">
                                <p class="form-hint" id="selected-media-info"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Position en avant -->
                    <div class="featured-positions">
                        <h3>🎯 Position en avant</h3>
                        <p style="color: #ccc; font-size: 12px; margin-bottom: 15px;">
                            Choisissez si cet article doit apparaître en avant sur la page d'accueil
                        </p>
                        
                        <!-- Nouveau design en quadrillage -->
                        <div class="featured-grid">
                            <!-- Position "Pas en avant" -->
                            <div class="grid-item grid-item-none">
                                <input type="radio" id="position_none" name="featured_position" value="" 
                                       <?= !$article || !$article->getFeaturedPosition() ? 'checked' : '' ?>>
                                <label for="position_none" class="grid-label">
                                    <div class="grid-content">
                                        <div class="grid-icon">❌</div>
                                        <div class="grid-title">Pas en avant</div>
                                        <div class="grid-desc">Article non mis en avant</div>
                                    </div>
                                </label>
                            </div>
                            
                            <!-- Positions 1-6 en quadrillage -->
                            <?php foreach ($featured_positions as $position): ?>
                                <?php if ($position['position']): ?>
                                    <div class="grid-item grid-item-position" data-position="<?= $position['position'] ?>">
                                        <input type="radio" id="position_<?= $position['position'] ?>" 
                                               name="featured_position" value="<?= $position['position'] ?>"
                                               <?= $article && $article->getFeaturedPosition() == $position['position'] ? 'checked' : '' ?>>
                                        <label for="position_<?= $position['position'] ?>" class="grid-label">
                                            <div class="grid-content">
                                                <div class="grid-icon"><?= $position['position'] ?></div>
                                                <div class="grid-title">Position <?= $position['position'] ?></div>
                                                <div class="grid-desc"><?= htmlspecialchars($position['description']) ?></div>
                                                <?php if ($position['current_article']): ?>
                                                    <div class="grid-current">
                                                        <small>Actuellement : <?= htmlspecialchars($position['current_article']['title']) ?></small>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </label>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Tags -->
                    <div class="tags-container">
                        <h3>🏷️ Tags</h3>
                        <p style="color: #ccc; font-size: 12px; margin-bottom: 15px;">
                            Recherchez et sélectionnez les tags associés à cet article
                        </p>
                        
                        <!-- Recherche de tags -->
                        <div class="tag-search-container">
                            <input type="text" id="tagSearch" class="tag-search" 
                                   placeholder="Tapez pour rechercher un tag..." 
                                   autocomplete="off">
                            <div id="tagsDropdown" class="tags-dropdown"></div>
                        </div>
                        
                        <!-- Tags sélectionnés -->
                        <div id="selectedTags" class="selected-tags">
                            <?php if ($article && !empty($articleTagIds)): ?>
                                <?php foreach ($tags as $tag): ?>
                                    <?php if (in_array($tag['id'], $articleTagIds)): ?>
                                        <div class="selected-tag" data-tag-id="<?= $tag['id'] ?>">
                                            <span><?= htmlspecialchars($tag['name']) ?></span>
                                            <button type="button" class="remove-tag" onclick="removeTag(<?= $tag['id'] ?>)">×</button>
                                            <input type="hidden" name="tags[]" value="<?= $tag['id'] ?>">
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="form-actions">
                <div>
                    <a href="/admin/articles" class="btn btn-secondary">❌ Annuler</a>
                    <?php if ($article): ?>
                        <a href="/admin/articles" class="btn btn-warning">📋 Retour à la liste</a>
                    <?php endif; ?>
                </div>
                
                <div>
                    <?php if ($article): ?>
                        <button type="submit" class="btn btn-success">💾 Mettre à jour l'article</button>
                    <?php else: ?>
                        <button type="submit" class="btn btn-success">💾 Créer l'article</button>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>

            <!-- Éditeur modulaire plein écran -->
        <script src="/public/js/editor/editor-loader.js?v=<?= time() ?>"></script>
        <script>
                                                               // Attendre que le DOM soit chargé
                 document.addEventListener('DOMContentLoaded', function() {
                     console.log('DOM chargé, initialisation de l\'éditeur modulaire...');
                     
                     let fullscreenEditor = null;
                     
                     // Initialiser l'éditeur modulaire
                     const editorButton = document.getElementById('open-fullscreen-editor');
                     const contentPreview = document.getElementById('content-preview');
                     const contentTextarea = document.getElementById('content');
                     
                     // Fonction pour formater la prévisualisation initiale
                     function formatInitialPreview() {
                         if (contentPreview) {
                             const previewContent = contentPreview.querySelector('.preview-content');
                             if (previewContent) {
                                 // Appliquer des styles pour éviter le débordement
                                 previewContent.style.maxWidth = '100%';
                                 previewContent.style.overflowWrap = 'break-word';
                                 previewContent.style.wordWrap = 'break-word';
                                 
                                 // Nettoyer les éléments qui pourraient causer des problèmes
                                 const allElements = previewContent.querySelectorAll('*');
                                 allElements.forEach(element => {
                                     element.style.maxWidth = '100%';
                                     element.style.boxSizing = 'border-box';
                                     
                                     // Gérer les tableaux
                                     if (element.tagName === 'TABLE') {
                                         element.style.tableLayout = 'fixed';
                                         element.style.width = '100%';
                                     }
                                     
                                     // Gérer les images et vidéos
                                     if (element.tagName === 'IMG' || element.tagName === 'VIDEO') {
                                         element.style.maxWidth = '100%';
                                         element.style.height = 'auto';
                                     }
                                     
                                     // Gérer le code et pre
                                     if (element.tagName === 'PRE' || element.tagName === 'CODE') {
                                         element.style.whiteSpace = 'pre-wrap';
                                         element.style.wordWrap = 'break-word';
                                     }
                                 });
                             }
                         }
                     }
                     
                     // Nettoyer et formater le contenu de prévisualisation initial
                     formatInitialPreview();
                
                if (editorButton && contentPreview && contentTextarea) {
                    editorButton.addEventListener('click', function() {
                        console.log('Clic sur le bouton éditeur détecté');
                        
                        // Vérifier si l'éditeur est prêt
                        if (typeof window.FullscreenEditor === 'undefined') {
                            console.log('Éditeur modulaire en cours de chargement, attente...');
                            showNotification('Chargement de l\'éditeur en cours...', 'info');
                            
                            // Attendre l'événement editorReady
                            window.addEventListener('editorReady', function() {
                                console.log('Éditeur modulaire prêt, initialisation...');
                                initModularEditor();
                            });
                        } else {
                            console.log('Éditeur modulaire déjà prêt, initialisation immédiate...');
                            initModularEditor();
                        }
                    });
                    
                    console.log('Événement click ajouté au bouton éditeur');
                } else {
                    console.error('Éléments manquants:', {
                        editorButton: !!editorButton,
                        contentPreview: !!contentPreview,
                        contentTextarea: !!contentTextarea
                    });
                }
                
                function initModularEditor() {
                    try {
                        console.log('🚀 Début de l\'initialisation de l\'éditeur modulaire...');
                        console.log('Vérification des dépendances:');
                        console.log('- FullscreenEditor:', typeof window.FullscreenEditor);
                        console.log('- StyleManager:', typeof window.StyleManager);
                        console.log('- ModuleFactory:', typeof window.ModuleFactory);
                        
                        // Créer l'éditeur modulaire plein écran
                        console.log('🔧 Création de l\'instance FullscreenEditor...');
                        console.log('FullscreenEditor:', window.FullscreenEditor);
                        console.log('StyleManager:', window.StyleManager);
                        console.log('ModuleFactory:', window.ModuleFactory);
                        
                        try {
                            console.log('🔧 Tentative de création de l\'instance FullscreenEditor...');
                            console.log('Options passées:', {
                                initialContent: contentTextarea.value,
                                onSave: typeof function() {},
                                onClose: typeof function() {}
                            });
                            
                            fullscreenEditor = new window.FullscreenEditor({
                                initialContent: contentTextarea.value,
                                                                 onSave: function(content) {
                                     console.log('Sauvegarde du contenu:', content.substring(0, 50) + '...');
                                     // Mettre à jour le textarea et la prévisualisation
                                     contentTextarea.value = content;
                                     
                                     // Créer une div temporaire pour nettoyer le HTML
                                     const tempDiv = document.createElement('div');
                                     tempDiv.innerHTML = content;
                                     
                                     // Appliquer des styles de base pour éviter le débordement
                                     const previewContent = document.createElement('div');
                                     previewContent.className = 'preview-content';
                                     previewContent.innerHTML = content;
                                     
                                     // Mettre à jour la prévisualisation
                                     contentPreview.innerHTML = '';
                                     contentPreview.appendChild(previewContent);
                                     
                                     // Fermer l'éditeur
                                     if (fullscreenEditor) {
                                         fullscreenEditor.close();
                                         fullscreenEditor = null;
                                     }
                                     
                                     // Afficher un message de succès
                                     showNotification('Contenu sauvegardé avec succès !', 'success');
                                 },
                                onClose: function() {
                                    console.log('Fermeture de l\'éditeur');
                                    // Fermer l'éditeur sans sauvegarder
                                    if (fullscreenEditor) {
                                        fullscreenEditor = null;
                                    }
                                }
                            });
                            
                            console.log('✅ Éditeur modulaire créé avec succès');
                            console.log('Instance créée:', fullscreenEditor);
                        } catch (constructorError) {
                            console.error('❌ Erreur dans le constructeur FullscreenEditor:', constructorError);
                            console.error('Stack trace du constructeur:', constructorError.stack);
                            console.error('Type d\'erreur:', constructorError.name);
                            console.error('Message d\'erreur:', constructorError.message);
                            throw constructorError;
                        }
                        showNotification('Éditeur modulaire ouvert !', 'success');
                    } catch (error) {
                        console.error('❌ Erreur lors de la création de l\'éditeur modulaire:', error);
                        console.error('Stack trace:', error.stack);
                        showNotification('Erreur lors de l\'ouverture de l\'éditeur: ' + error.message, 'error');
                    }
                }
                
                
                
                // Fonction pour afficher les notifications
                function showNotification(message, type = 'info') {
                    const notification = document.createElement('div');
                    notification.className = `notification notification-${type}`;
                    notification.textContent = message;
                    notification.style.cssText = `
                        position: fixed;
                        top: 20px;
                        right: 20px;
                        padding: 15px 20px;
                        border-radius: 5px;
                        color: white;
                        font-weight: bold;
                        z-index: 10000;
                        animation: slideIn 0.3s ease-out;
                    `;
                    
                    // Couleurs selon le type
                    switch(type) {
                        case 'success':
                            notification.style.background = '#27ae60';
                            break;
                        case 'error':
                            notification.style.background = '#e74c3c';
                            break;
                        case 'warning':
                            notification.style.background = '#f39c12';
                            break;
                        default:
                            notification.style.background = '#3498db';
                    }
                    
                    document.body.appendChild(notification);
                    
                    // Supprimer après 3 secondes
                    setTimeout(() => {
                        notification.style.animation = 'slideOut 0.3s ease-in';
                        setTimeout(() => {
                            if (notification.parentNode) {
                                notification.parentNode.removeChild(notification);
                            }
                        }, 300);
                    }, 3000);
                }
                
                // Styles pour les animations
                const style = document.createElement('style');
                style.textContent = `
                    @keyframes slideIn {
                        from { transform: translateX(100%); opacity: 0; }
                        to { transform: translateX(0); opacity: 1; }
                    }
                    @keyframes slideOut {
                        from { transform: translateX(0); opacity: 1; }
                        to { transform: translateX(100%); opacity: 0; }
                    }
                `;
                document.head.appendChild(style);
                
                // Gestion du statut et de la date de publication
                const statusSelect = document.getElementById('status');
                const publishedDateGroup = document.getElementById('published-date-group');
                
                if (statusSelect) {
                    statusSelect.addEventListener('change', function() {
                        if (this.value === 'published') {
                            publishedDateGroup.style.display = 'block';
                        } else {
                            publishedDateGroup.style.display = 'none';
                        }
                    });
                    
                    // Initialiser l'affichage
                    if (statusSelect.value === 'published') {
                        publishedDateGroup.style.display = 'block';
                    }
                }
                
                // Gestion intelligente de l'image de couverture
                const gameSelect = document.getElementById('game_id');
                const gameCoverInfo = document.getElementById('game-cover-info');
                const manualCoverSelection = document.getElementById('manual-cover-selection');
                const gameCoverPreview = document.getElementById('game-cover-preview');
                const gameCoverImageIdInput = document.getElementById('game_cover_image_id');
                const coverImageFileInput = document.getElementById('cover_image_file');
                const uploadPreview = document.getElementById('upload-preview');
                const previewImage = document.getElementById('preview-image');
                const existingCoverInfo = document.getElementById('existing-cover-info');
                const keepCoverBtn = document.getElementById('keep-cover-btn');

                // Initialiser l'affichage si un jeu est déjà sélectionné et pas d'image existante
                if (gameSelect && gameSelect.value && (!existingCoverInfo || existingCoverInfo.style.display === 'none')) {
                    fetchGameCover(gameSelect.value);
                }

                if (gameSelect) {
                    gameSelect.addEventListener('change', function() {
                        const selectedGameId = this.value;

                        // Si une image existante est affichée, la cacher
                        if (existingCoverInfo && existingCoverInfo.style.display === 'block') {
                            existingCoverInfo.style.display = 'none';
                            keepCoverBtn.style.display = 'none';
                        }

                        if (selectedGameId) {
                            // Récupérer les informations du jeu sélectionné
                            fetchGameCover(selectedGameId);
                        } else {
                            // Aucun jeu sélectionné, afficher la sélection manuelle
                            showManualCoverSelection();
                        }
                    });
                }

                // Gestion de l'upload d'image d'illustration
                if (coverImageFileInput) {
                    coverImageFileInput.addEventListener('change', function(event) {
                        const file = event.target.files[0];
                        if (file) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                previewImage.src = e.target.result;
                                uploadPreview.style.display = 'block';
                                // Mettre à jour l'ID de l'image de couverture
                                document.getElementById('cover_image_id').value = '';
                                // Cacher l'image existante
                                if (existingCoverInfo) {
                                    existingCoverInfo.style.display = 'none';
                                    keepCoverBtn.style.display = 'none';
                                }
                                // Cacher la cover du jeu
                                gameCoverInfo.style.display = 'none';
                            };
                            reader.readAsDataURL(file);
                        } else {
                            previewImage.src = '';
                            uploadPreview.style.display = 'none';
                        }
                    });
                }
                
                // Fonction pour récupérer la cover du jeu
                function fetchGameCover(gameId) {
                    fetch(`/admin/games/get/${gameId}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success && data.game.cover_image) {
                                showGameCover(data.game.cover_image);
                            } else {
                                showManualCoverSelection();
                            }
                        })
                        .catch(error => {
                            console.error('Erreur lors de la récupération du jeu:', error);
                            showManualCoverSelection();
                        });
                }
                
                // Afficher la cover du jeu
                function showGameCover(coverImage) {
                    gameCoverInfo.style.display = 'block';
                    manualCoverSelection.style.display = 'block'; // Garder la sélection manuelle visible
                    gameCoverPreview.src = coverImage.url;
                    gameCoverImageIdInput.value = coverImage.id;
                    // NE PAS remplacer l'ID de l'image de couverture de l'article
                    // L'image du jeu est juste informative pour le design futur
                    // Garder l'image existante si elle existe
                    if (existingCoverInfo) {
                        existingCoverInfo.style.display = 'block';
                        keepCoverBtn.style.display = 'block';
                    }
                    // Ne pas cacher l'upload preview - l'utilisateur peut toujours uploader
                    // uploadPreview.style.display = 'none'; // Commenté
                    // previewImage.src = ''; // Commenté
                }

                // Afficher la sélection manuelle
                function showManualCoverSelection() {
                    gameCoverInfo.style.display = 'none';
                    manualCoverSelection.style.display = 'block';
                    gameCoverImageIdInput.value = '';
                    // Si une image existante est présente, la réafficher
                    if (existingCoverInfo && existingCoverInfo.dataset.hasImage === 'true') {
                        existingCoverInfo.style.display = 'block';
                        keepCoverBtn.style.display = 'none'; // Hide keep button if no game selected
                    } else {
                        uploadPreview.style.display = 'none';
                        previewImage.src = '';
                    }
                }
                
                // Fonction pour ouvrir la médiathèque
                window.openMediaLibrary = function() {
                    window.open('/admin/media', '_blank', 'width=1000,height=700');
                };

                // Validation du formulaire avant soumission
                const form = document.querySelector('.form-container');
                if (form) {
                    form.addEventListener('submit', function(e) {
                        const title = document.getElementById('title').value.trim();
                        const content = document.getElementById('content').value.trim();
                        
                        // Debug: Afficher les valeurs dans la console
                        console.log('🔍 Validation du formulaire:');
                        console.log('Titre:', title);
                        console.log('Contenu:', content);
                        console.log('Longueur du contenu:', content.length);
                        
                        if (!title) {
                            e.preventDefault();
                            alert('Le titre est obligatoire');
                            return false;
                        }
                        
                        if (!content) {
                            console.log('⚠️ Contenu vide détecté, mais on continue pour debug');
                            // e.preventDefault();
                            // alert('Le contenu est obligatoire');
                            // return false;
                        }
                    });
                }

                // Gestion de la recherche dynamique des tags
                const tagSearch = document.getElementById('tagSearch');
                const tagsDropdown = document.getElementById('tagsDropdown');
                const selectedTags = document.getElementById('selectedTags');
                let searchTimeout;
                let selectedTagsList = new Set();

                // Initialiser les tags déjà sélectionnés
                document.querySelectorAll('.selected-tag').forEach(tag => {
                    const tagId = tag.dataset.tagId;
                    selectedTagsList.add(parseInt(tagId));
                });

                if (tagSearch) {
                    tagSearch.addEventListener('input', function() {
                        clearTimeout(searchTimeout);
                        const query = this.value.trim();
                        
                        if (query.length < 2) {
                            tagsDropdown.classList.remove('active');
                            return;
                        }
                        
                        searchTimeout = setTimeout(() => {
                            searchTags(query);
                        }, 300);
                    });

                    // Fermer le dropdown quand on clique ailleurs
                    document.addEventListener('click', function(e) {
                        if (!tagSearch.contains(e.target) && !tagsDropdown.contains(e.target)) {
                            tagsDropdown.classList.remove('active');
                        }
                    });
                }

                // Recherche de tags
                async function searchTags(query) {
                    try {
                        const response = await fetch(`/tags.php?action=search-tags&q=${encodeURIComponent(query)}&limit=10`);
                        const data = await response.json();
                        
                        if (data.success) {
                            displayTagsDropdown(data.tags);
                        }
                    } catch (error) {
                        console.error('Erreur recherche tags:', error);
                    }
                }

                // Afficher le dropdown des tags
                function displayTagsDropdown(tags) {
                    tagsDropdown.innerHTML = '';
                    
                    if (tags.length === 0) {
                        tagsDropdown.innerHTML = '<ul><li>Aucun tag trouvé</li></ul>';
                    } else {
                        const ul = document.createElement('ul');
                        tags.forEach(tag => {
                            const li = document.createElement('li');
                            li.textContent = tag.name;
                            li.addEventListener('click', () => selectTag(tag));
                            ul.appendChild(li);
                        });
                        tagsDropdown.appendChild(ul);
                    }
                    
                    tagsDropdown.classList.add('active');
                }

                // Sélectionner un tag
                function selectTag(tag) {
                    if (selectedTagsList.has(tag.id)) {
                        // Tag déjà sélectionné
                        return;
                    }

                    selectedTagsList.add(tag.id);
                    
                    const tagElement = document.createElement('div');
                    tagElement.className = 'selected-tag';
                    tagElement.dataset.tagId = tag.id;
                    tagElement.innerHTML = `
                        <span>${tag.name}</span>
                        <button type="button" class="remove-tag" onclick="removeTag(${tag.id})">×</button>
                        <input type="hidden" name="tags[]" value="${tag.id}">
                    `;
                    
                    selectedTags.appendChild(tagElement);
                    tagSearch.value = '';
                    tagsDropdown.classList.remove('active');
                    
                    // Notification
                    showNotification(`Tag ajouté : ${tag.name}`, 'success');
                }

                // Supprimer un tag
                window.removeTag = function(tagId) {
                    selectedTagsList.delete(tagId);
                    const tagElement = document.querySelector(`[data-tag-id="${tagId}"]`);
                    if (tagElement) {
                        tagElement.remove();
                    }
                };

                // Fonctions pour la gestion de l'image de couverture
                window.changeCoverImage = function() {
                    document.getElementById('existing-cover-info').style.display = 'none';
                    document.getElementById('manual-cover-selection').style.display = 'block';
                    document.getElementById('keep-cover-btn').style.display = 'block';
                    document.getElementById('cover_image_id').value = ''; // Clear manual ID
                    document.getElementById('cover_image_file').value = ''; // Clear file input
                    document.getElementById('upload-preview').style.display = 'none'; // Hide upload preview
                    document.getElementById('game-cover-info').style.display = 'none'; // Hide game cover info
                };

                window.keepCoverImage = function() {
                    document.getElementById('existing-cover-info').style.display = 'block';
                    document.getElementById('manual-cover-selection').style.display = 'none';
                    document.getElementById('keep-cover-btn').style.display = 'none';
                    // Restore the hidden input value for current_cover_image_id
                    const currentCoverImageIdInput = document.querySelector('input[name="current_cover_image_id"]');
                    if (currentCoverImageIdInput) {
                        document.getElementById('cover_image_id').value = currentCoverImageIdInput.value;
                    }
                    document.getElementById('cover_image_file').value = ''; // Clear file input
                    document.getElementById('upload-preview').style.display = 'none'; // Hide upload preview
                    // Re-evaluate game cover if a game is selected
                    if (gameSelect && gameSelect.value) {
                        fetchGameCover(gameSelect.value);
                    }
                };

                // ========================================
                // GESTION DES DOSSIERS ET CHAPITRES
                // ========================================
                
                // Détecter la catégorie "Dossiers" (ID 10)
                const categorySelect = document.getElementById('category_id');
                const dossierSection = document.getElementById('dossier-section');
                const addChapterBtn = document.getElementById('add-chapter-btn');
                
                // Vérifier si on est en mode édition (article existant)
                const isEditMode = <?= $article ? 'true' : 'false' ?>;
                const currentArticleId = <?= $article ? $article->getId() : 'null' ?>;
                
                // Fonction pour afficher/masquer la section dossiers
                function toggleDossierSection() {
                    // GARDE-FOU : Section dossiers visible SEULEMENT en mode édition
                    if (!isEditMode) {
                        console.log('🚫 Section dossiers masquée - Mode création (pas d\'ID d\'article)');
                        return;
                    }
                    
                    const selectedCategoryId = categorySelect.value;
                    const isDossierCategory = selectedCategoryId === '10'; // ID de la catégorie "Dossiers"
                    
                    if (isDossierCategory) {
                        dossierSection.style.display = 'block';
                        console.log('📚 Section dossiers affichée - Article ID:', currentArticleId, 'Catégorie Dossiers');
                    } else {
                        dossierSection.style.display = 'none';
                        console.log('📝 Section dossiers masquée - Autre catégorie sélectionnée');
                    }
                }
                
                // Écouter les changements de catégorie
                if (categorySelect) {
                    categorySelect.addEventListener('change', toggleDossierSection);
                    
                    // Vérifier l'état initial
                    toggleDossierSection();
                }
                
                // Gestionnaire pour le bouton "Ajouter un chapitre"
                if (addChapterBtn) {
                    addChapterBtn.addEventListener('click', function() {
                        // GARDE-FOU : Vérifier qu'on a un ID d'article
                        if (!currentArticleId) {
                            showNotification('❌ Erreur : Impossible de créer des chapitres sans ID d\'article', 'error');
                            return;
                        }
                        
                        console.log('➕ Bouton "Ajouter un chapitre" cliqué pour l\'article ID:', currentArticleId);
                        openChapterManager();
                    });
                }
                
                // Fonction pour ouvrir le gestionnaire de chapitres
                function openChapterManager() {
                    // GARDE-FOU : Vérifier qu'on a un ID d'article
                    if (!currentArticleId) {
                        showNotification('❌ Erreur : Impossible d\'ouvrir le gestionnaire sans ID d\'article', 'error');
                        return;
                    }
                    
                    console.log('🚀 Ouverture du gestionnaire de chapitres pour l\'article ID:', currentArticleId);
                    
                    // Créer et afficher le modal du gestionnaire de chapitres
                    const chapterManager = createChapterManagerModal();
                    document.body.appendChild(chapterManager);
                    
                    // Afficher le modal
                    chapterManager.style.display = 'block';
                    
                    // Charger les chapitres existants depuis la base de données
                    loadExistingChapters();
                }
                
                // Fonction pour charger les chapitres existants depuis la base de données
                function loadExistingChapters() {
                    console.log('📚 Chargement des chapitres existants pour l\'article ID:', currentArticleId);
                    
                    // Afficher un indicateur de chargement
                    const chaptersList = document.getElementById('modal-chapters-list');
                    if (chaptersList) {
                        chaptersList.innerHTML = '<div class="chapters-loading"><p>🔄 Chargement des chapitres...</p></div>';
                    }
                    
                    // Appeler l'API pour récupérer les chapitres
                    fetch(`/admin/articles/load-chapters/${currentArticleId}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success && data.chapters) {
                                console.log('✅ Chapitres chargés:', data.chapters);
                                
                                // Vider la liste
                                chaptersList.innerHTML = '';
                                
                                if (data.chapters.length === 0) {
                                    // Aucun chapitre, afficher le placeholder
                                    chaptersList.innerHTML = '<div class="chapters-placeholder"><p>Aucun chapitre créé. Commencez par créer votre premier chapitre.</p></div>';
                                } else {
                                    // Créer les éléments pour chaque chapitre existant
                                    data.chapters.forEach(chapter => {
                                        const chapterElement = createExistingChapterElement(chapter);
                                        chaptersList.appendChild(chapterElement);
                                    });
                                }
                            } else {
                                throw new Error(data.error || 'Erreur lors du chargement des chapitres');
                            }
                        })
                        .catch(error => {
                            console.error('❌ Erreur lors du chargement des chapitres:', error);
                            chaptersList.innerHTML = '<div class="chapters-error"><p>❌ Erreur lors du chargement: ' + error.message + '</p></div>';
                        });
                }
                
                // Fonction pour créer un élément de chapitre existant
                function createExistingChapterElement(chapter) {
                    const chapterDiv = document.createElement('div');
                    chapterDiv.className = 'chapter-item';
                    chapterDiv.dataset.chapterId = chapter.id;
                    chapterDiv.dataset.chapterContent = chapter.content || '';
                    chapterDiv.dataset.coverImageId = chapter.cover_image_id || '';
                    
                    chapterDiv.innerHTML = `
                        <div class="chapter-header">
                            <div class="chapter-info">
                                <input type="text" class="chapter-title" placeholder="Titre du chapitre" maxlength="200" value="${chapter.title || ''}">
                                <input type="text" class="chapter-slug" placeholder="Slug du chapitre" maxlength="220" value="${chapter.slug || ''}">
                                
                                <!-- Bouton pour modifier le contenu existant -->
                                <div class="chapter-editor-actions">
                                    <button type="button" class="btn btn-primary" onclick="editChapterContent(this)" title="Modifier le contenu">
                                        <span class="icon">✏️</span> Modifier le contenu
                                    </button>
                                </div>
                            </div>
                            
                            <div class="chapter-actions">
                                <button type="button" class="btn btn-info" onclick="uploadChapterCover(this)" title="Uploader image de couverture">
                                    <span class="icon">🖼️</span> Image de couverture
                                </button>
                                <button type="button" class="btn btn-${(chapter.status || 'draft') === 'published' ? 'warning' : 'success'}" 
                                        onclick="toggleChapterStatus(this, '${chapter.id}')" title="${(chapter.status || 'draft') === 'published' ? 'Mettre en brouillon' : 'Publier'}">
                                    <span class="icon">${(chapter.status || 'draft') === 'published' ? '📝' : '📤'}</span> ${(chapter.status || 'draft') === 'published' ? 'Dépublier' : 'Publier'}
                                </button>
                                <button type="button" class="btn btn-danger" onclick="deleteChapter(this, '${chapter.id}')" title="Supprimer le chapitre">
                                    <span class="icon">🗑️</span> Supprimer
                                </button>
                            </div>
                        </div>
                        
                        <div class="chapter-content">
                            <div class="chapter-preview">
                                <span class="chapter-status ${chapter.status || 'draft'}">${(chapter.status || 'draft') === 'published' ? 'Publié' : 'Brouillon'}</span>
                                <span class="chapter-excerpt">${chapter.content ? (chapter.content.substring(0, 100) + (chapter.content.length > 100 ? '...' : '')) : 'Aucun contenu rédigé'}</span>
                            </div>
                            
                            ${chapter.content ? `
                                <div class="chapter-content-preview" style="display: block;">
                                    <h4>Aperçu du contenu :</h4>
                                    <div class="chapter-content-display">${chapter.content}</div>
                                </div>
                            ` : ''}
                        </div>
                    `;
                    
                    return chapterDiv;
                }
                
                // Fonction pour créer le modal du gestionnaire de chapitres
                function createChapterManagerModal() {
                    const modal = document.createElement('div');
                    modal.className = 'chapter-manager-modal';
                    modal.innerHTML = `
                        <div class="chapter-manager-container">
                            <div class="chapter-manager-header">
                                <h2>📚 Gestionnaire de chapitres</h2>
                                <div class="dossier-info">
                                    <span class="dossier-id">Dossier ID: ${currentArticleId}</span>
                                </div>
                                <button type="button" class="close-btn" onclick="this.closest('.chapter-manager-modal').remove()">
                                    <span class="icon">✕</span>
                                </button>
                            </div>
                            
                            <div class="chapter-manager-body">
                                <div class="chapters-toolbar">
                                    <button type="button" class="btn btn-success" onclick="createNewChapter()">
                                        <span class="icon">➕</span> Nouveau chapitre
                                    </button>
                                    <button type="button" id="save-all-chapters-btn" class="btn btn-primary" onclick="saveAllChapters()">
                                        <span class="icon">💾</span> Sauvegarder tout
                                    </button>
                                </div>
                                
                                <div class="chapters-list-container">
                                    <div id="modal-chapters-list" class="chapters-list">
                                        <div class="chapters-placeholder">
                                            <p>Aucun chapitre créé. Commencez par créer votre premier chapitre.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    
                    return modal;
                }
                
                // Fonction pour créer un nouveau chapitre
                window.createNewChapter = function() {
                    console.log('📝 Création d\'un nouveau chapitre...');
                    
                    const chaptersList = document.getElementById('modal-chapters-list');
                    const placeholder = chaptersList.querySelector('.chapters-placeholder');
                    
                    if (placeholder) {
                        placeholder.remove();
                    }
                    
                    const chapterElement = createChapterElement();
                    chaptersList.appendChild(chapterElement);
                    
                    // Focus sur le titre du nouveau chapitre
                    const titleInput = chapterElement.querySelector('.chapter-title');
                    if (titleInput) {
                        titleInput.focus();
                    }
                };
                
                // Fonction pour créer un élément de chapitre
                function createChapterElement() {
                    const chapterDiv = document.createElement('div');
                    chapterDiv.className = 'chapter-item';
                    chapterDiv.dataset.chapterId = 'new-' + Date.now();
                    
                    chapterDiv.innerHTML = `
                        <div class="chapter-header">
                            <div class="chapter-info">
                                <input type="text" class="chapter-title" placeholder="Titre du chapitre" maxlength="200">
                                <input type="text" class="chapter-slug" placeholder="Slug du chapitre" maxlength="220">
                                
                                <!-- Bouton pour ouvrir l'éditeur modulaire -->
                                <div class="chapter-editor-actions">
                                    <button type="button" class="btn btn-primary" onclick="openChapterModularEditor(this)" title="Ouvrir l'éditeur modulaire">
                                        <span class="icon">📝</span> Ouvrir l'éditeur
                                    </button>
                                </div>
                            </div>
                            
                            <div class="chapter-actions">
                                <button type="button" class="btn btn-sm btn-primary" onclick="editChapterContent(this)" title="Modifier le contenu">
                                    <span class="icon">✏️</span>
                                </button>
                                <button type="button" class="btn btn-sm btn-warning" onclick="uploadChapterCover(this)" title="Image de couverture">
                                    <span class="icon">🖼️</span>
                                </button>
                            </div>
                        </div>
                        
                        <div class="chapter-content">
                            <div class="chapter-preview">
                                <p class="chapter-excerpt">Aucun contenu rédigé pour le moment.</p>
                            </div>
                            <div class="chapter-metadata">
                                <span class="chapter-status draft">Brouillon</span>
                                <span class="chapter-reading-time">0 min</span>
                            </div>
                        </div>
                        
                        <!-- Zone d'aperçu du contenu (visible après rédaction) -->
                        <div class="chapter-content-preview" style="display: none;">
                            <h4>Aperçu du contenu :</h4>
                            <div class="chapter-content-display"></div>
                        </div>
                    `;
                    
                    return chapterDiv;
                }
                
                // ========================================
                // FONCTIONS D'ACTION POUR LES CHAPITRES SAUVEGARDÉS
                // ========================================
                
                // Fonction pour éditer un chapitre depuis la liste
                window.editChapterFromList = function(button) {
                    const chapterItem = button.closest('.chapter-list-item');
                    const chapterId = chapterItem.dataset.chapterId;
                    console.log('✏️ Édition du chapitre depuis la liste:', chapterId);
                    
                    // Récupérer le contenu actuel depuis plusieurs sources possibles
                    let currentContent = '';
                    
                    // 1. Essayer de récupérer depuis la zone d'aperçu
                    const contentPreview = chapterItem.querySelector('.chapter-content-preview');
                    if (contentPreview) {
                        currentContent = contentPreview.innerHTML;
                    }
                    
                    // 2. Si pas de contenu, essayer depuis la zone de prévisualisation
                    if (!currentContent) {
                        const contentDisplay = chapterItem.querySelector('.chapter-content-display');
                        if (contentDisplay) {
                            currentContent = contentDisplay.innerHTML;
                        }
                    }
                    
                    // 3. Si toujours pas de contenu, essayer depuis le dataset
                    if (!currentContent && chapterItem.dataset.chapterContent) {
                        currentContent = chapterItem.dataset.chapterContent;
                    }
                    
                    console.log('📝 Contenu trouvé pour modification:', currentContent ? 'Oui' : 'Non', currentContent.substring(0, 50) + '...');
                    
                    if (!currentContent) {
                        showNotification('❌ Aucun contenu à modifier pour ce chapitre. Veuillez d\'abord créer du contenu.', 'warning');
                        return;
                    }
                    
                    // Ouvrir l'éditeur modulaire pour modifier le contenu
                    openChapterFullscreenEditorDirect(chapterItem, currentContent);
                };
                
                // Fonction pour basculer le statut d'un chapitre (Publier/Dépublier)
                window.toggleChapterStatus = function(button, chapterId) {
                    console.log('🔄 toggleChapterStatus appelé avec chapterId:', chapterId);
                    const chapterItem = button.closest('.chapter-item, .chapter-list-item');
                    const statusBadge = chapterItem.querySelector('.chapter-status');
                    const currentStatus = statusBadge.textContent;
                    const newStatus = currentStatus === 'Brouillon' ? 'published' : 'draft';
                    
                    console.log('🔄 Changement de statut du chapitre:', chapterId, 'de', currentStatus, 'vers', newStatus);
                    
                    // Mettre à jour l'affichage immédiatement
                    if (newStatus === 'published') {
                        statusBadge.textContent = 'Publié';
                        statusBadge.className = 'chapter-status published';
                        button.innerHTML = '<span class="icon">📝</span> Dépublier';
                        button.className = 'btn btn-warning';
                        button.title = 'Mettre en brouillon';
                    } else {
                        statusBadge.textContent = 'Brouillon';
                        statusBadge.className = 'chapter-status draft';
                        button.innerHTML = '<span class="icon">📤</span> Publier';
                        button.className = 'btn btn-success';
                        button.title = 'Publier';
                    }
                    
                    // Mettre à jour la base de données
                    updateChapterStatus(chapterId, newStatus);
                };
                
                // Fonction pour mettre à jour le statut d'un chapitre dans la base de données
                function updateChapterStatus(chapterId, newStatus) {
                    console.log('💾 updateChapterStatus appelé avec chapterId:', chapterId, 'newStatus:', newStatus);
                    const formData = new FormData();
                    formData.append('chapter_id', chapterId);
                    formData.append('status', newStatus);
                    
                    fetch('/admin/articles/update-chapter-status', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showNotification(`✅ Statut du chapitre mis à jour : ${newStatus === 'published' ? 'Publié' : 'Brouillon'}`, 'success');
                        } else {
                            showNotification(`❌ Erreur lors de la mise à jour : ${data.message}`, 'error');
                            // Remettre l'ancien statut en cas d'erreur
                            location.reload();
                        }
                    })
                    .catch(error => {
                        console.error('❌ Erreur lors de la mise à jour du statut:', error);
                        showNotification('❌ Erreur lors de la mise à jour du statut', 'error');
                        location.reload();
                    });
                }
                
                // Fonction pour basculer le statut d'un chapitre depuis la liste principale
                window.toggleChapterStatusFromList = function(button) {
                    const chapterItem = button.closest('.chapter-list-item');
                    const chapterId = chapterItem.dataset.chapterId;
                    
                    if (!chapterId) {
                        console.error('❌ ChapterId non trouvé dans la liste');
                        showNotification('❌ Erreur : ID du chapitre non trouvé', 'error');
                        return;
                    }
                    
                    console.log('🔄 toggleChapterStatusFromList appelé avec chapterId:', chapterId);
                    
                    // Appeler la fonction principale avec le chapterId
                    window.toggleChapterStatus(button, chapterId);
                };
                
                // Fonction pour supprimer un chapitre depuis la liste
                window.deleteChapterFromList = function(button) {
                    const chapterItem = button.closest('.chapter-list-item');
                    const chapterTitle = chapterItem.querySelector('.chapter-list-title').textContent;
                    
                    if (confirm(`Êtes-vous sûr de vouloir supprimer le chapitre "${chapterTitle}" ?`)) {
                        chapterItem.remove();
                        showNotification('🗑️ Chapitre supprimé avec succès', 'success');
                        
                        // Vérifier s'il reste des chapitres
                        const chaptersList = document.getElementById('chapters-list');
                        if (chaptersList.children.length === 0) {
                            chaptersList.innerHTML = '<div class="chapters-placeholder"><p>Aucun chapitre créé. Commencez par créer votre premier chapitre.</p></div>';
                        }
                    }
                };
                
                // ========================================
                // FONCTION D'UPLOAD D'IMAGE DE COUVERTURE
                // ========================================
                
                // Fonction pour uploader l'image de couverture d'un chapitre
                window.uploadChapterCover = function(button) {
                    const chapterItem = button.closest('.chapter-item, .chapter-list-item');
                    const chapterId = chapterItem.dataset.chapterId;
                    console.log('🖼️ Upload d\'image de couverture pour le chapitre:', chapterId);
                    
                    // Vérifier si la médiathèque est disponible
                    if (typeof window.MediaLibraryAPI === 'undefined') {
                        showNotification('❌ Médiathèque non disponible. Veuillez recharger la page.', 'error');
                        return;
                    }
                    
                    try {
                        // Créer une instance de MediaLibraryAPI et ouvrir le sélecteur
                        const mediaLibrary = new window.MediaLibraryAPI();
                        mediaLibrary.openMediaSelector({
                            allowMultiple: false
                        }).then(function(mediaItem) {
                            console.log('🖼️ Image sélectionnée:', mediaItem);
                            
                            // Mettre à jour l'image de couverture du chapitre
                            updateChapterCoverImage(chapterItem, mediaItem);
                            
                            showNotification('✅ Image de couverture mise à jour !', 'success');
                        }).catch(function(error) {
                            if (error.message !== 'Sélection annulée') {
                                console.error('❌ Erreur lors de la sélection:', error);
                                showNotification('Erreur lors de la sélection: ' + error.message, 'error');
                            } else {
                                console.log('❌ Sélection d\'image annulée');
                            }
                        });
                        
                    } catch (error) {
                        console.error('❌ Erreur lors de l\'ouverture de la médiathèque:', error);
                        showNotification('Erreur lors de l\'ouverture de la médiathèque: ' + error.message, 'error');
                    }
                };
                
                // Fonction pour mettre à jour l'image de couverture d'un chapitre
                function updateChapterCoverImage(chapterItem, mediaItem) {
                    // Créer ou mettre à jour l'affichage de l'image de couverture
                    let coverDisplay = chapterItem.querySelector('.chapter-cover-display');
                    
                    if (!coverDisplay) {
                        coverDisplay = document.createElement('div');
                        coverDisplay.className = 'chapter-cover-display';
                        coverDisplay.innerHTML = `
                            <div class="chapter-cover-info">
                                <h5>Image de couverture :</h5>
                                <div class="chapter-cover-preview">
                                    <img src="${mediaItem.url}" alt="${mediaItem.title || 'Image de couverture'}" class="cover-thumbnail">
                                    <div class="cover-details">
                                        <span class="cover-title">${mediaItem.title || 'Sans titre'}</span>
                                        <span class="cover-filename">${mediaItem.filename || 'Fichier'}</span>
                                    </div>
                                </div>
                            </div>
                        `;
                        
                        // Insérer après les actions du chapitre
                        const chapterActions = chapterItem.querySelector('.chapter-actions, .chapter-list-actions');
                        if (chapterActions) {
                            chapterActions.parentNode.insertBefore(coverDisplay, chapterActions.nextSibling);
                        }
                    } else {
                        // Mettre à jour l'image existante
                        const coverImg = coverDisplay.querySelector('.cover-thumbnail');
                        const coverTitle = coverDisplay.querySelector('.cover-title');
                        const coverFilename = coverDisplay.querySelector('.cover-filename');
                        
                        if (coverImg) coverImg.src = mediaItem.url;
                        if (coverTitle) coverTitle.textContent = mediaItem.title || 'Sans titre';
                        if (coverFilename) coverFilename.textContent = mediaItem.filename || 'Fichier';
                    }
                    
                    // Stocker l'ID de l'image dans le dataset du chapitre
                    chapterItem.dataset.coverImageId = mediaItem.id;
                }
                
                // ========================================
                // FONCTION DE NOTIFICATION
                // ========================================
                
                // Fonction pour afficher des notifications
                function showNotification(message, type = 'info') {
                    // Créer l'élément de notification
                    const notification = document.createElement('div');
                    notification.className = `notification notification-${type}`;
                    notification.innerHTML = `
                        <div class="notification-content">
                            <span class="notification-message">${message}</span>
                            <button class="notification-close" onclick="this.parentElement.parentElement.remove()">×</button>
                        </div>
                    `;
                    
                    // Ajouter au body
                    document.body.appendChild(notification);
                    
                    // Animation d'entrée
                    setTimeout(() => {
                        notification.classList.add('show');
                    }, 100);
                    
                    // Auto-suppression après 5 secondes
                    setTimeout(() => {
                        if (notification.parentElement) {
                            notification.classList.remove('show');
                            setTimeout(() => {
                                if (notification.parentElement) {
                                    notification.remove();
                                }
                            }, 300);
                        }
                    }, 5000);
                }
                
                // Fonction pour ouvrir l'éditeur modulaire directement depuis le chapitre
                window.openChapterModularEditor = function(button) {
                    const chapterItem = button.closest('.chapter-item');
                    const chapterId = chapterItem.dataset.chapterId;
                    console.log('🚀 Ouverture directe de l\'éditeur modulaire pour le chapitre:', chapterId);
                    
                    // Récupérer le contenu actuel du chapitre s'il existe
                    const contentDisplay = chapterItem.querySelector('.chapter-content-display');
                    const currentContent = contentDisplay ? contentDisplay.innerHTML : '';
                    
                    // Ouvrir directement l'éditeur modulaire plein écran
                    openChapterFullscreenEditorDirect(chapterItem, currentContent);
                };
                
                // Fonction pour ouvrir l'éditeur de chapitre (ancienne fonction, maintenant pour modifier)
                window.editChapterContent = function(button) {
                    const chapterItem = button.closest('.chapter-item');
                    const chapterId = chapterItem.dataset.chapterId;
                    console.log('📝 Modification du contenu pour le chapitre:', chapterId);
                    
                    // Récupérer le contenu actuel du chapitre
                    const contentDisplay = chapterItem.querySelector('.chapter-content-display');
                    const currentContent = contentDisplay ? contentDisplay.innerHTML : '';
                    
                    if (!currentContent) {
                        showNotification('❌ Aucun contenu à modifier. Ouvrez d\'abord l\'éditeur pour créer du contenu.', 'warning');
                        return;
                    }
                    
                    // Ouvrir l'éditeur modulaire pour modifier le contenu existant
                    openChapterFullscreenEditorDirect(chapterItem, currentContent);
                };
                
                // Fonction pour ouvrir l'éditeur modulaire d'un chapitre
                function openChapterModularEditor(chapterItem, initialContent = '') {
                    console.log('🚀 Ouverture de l\'éditeur modulaire pour le chapitre...');
                    
                    // Créer le modal de l'éditeur de chapitre
                    const editorModal = createChapterEditorModal(chapterItem, initialContent);
                    document.body.appendChild(editorModal);
                    
                    // Afficher le modal
                    editorModal.style.display = 'block';
                    
                    // Initialiser l'éditeur modulaire
                    initChapterModularEditor(editorModal, chapterItem);
                }
                
                // Fonction pour créer le modal de l'éditeur de chapitre
                function createChapterEditorModal(chapterItem, initialContent) {
                    const modal = document.createElement('div');
                    modal.className = 'chapter-editor-modal';
                    
                    // Attacher le chapterItem au modal pour y accéder plus tard
                    modal.chapterItem = chapterItem;
                    
                    modal.innerHTML = `
                        <div class="chapter-editor-container">
                            <div class="chapter-editor-header">
                                <h2>📝 Éditeur de chapitre</h2>
                                <div class="chapter-editor-info">
                                    <span class="chapter-title-display">${chapterItem.querySelector('.chapter-title').value || 'Nouveau chapitre'}</span>
                                </div>
                                <button type="button" class="close-btn" onclick="this.closest('.chapter-editor-modal').remove()">
                                    <span class="icon">✕</span>
                                </button>
                            </div>
                            
                            <div class="chapter-editor-body">
                                <div class="chapter-editor-toolbar">
                                    <button type="button" class="btn btn-primary" onclick="openChapterFullscreenEditor(this)">
                                        <span class="icon">📝</span> Ouvrir l'éditeur plein écran
                                    </button>
                                    <button type="button" class="btn btn-success" onclick="saveChapterContent(this)">
                                        <span class="icon">💾</span> Sauvegarder le chapitre
                                    </button>
                                </div>
                                
                                <div class="chapter-content-editor">
                                    <div class="chapter-content-preview" id="chapter-content-preview">
                                        ${initialContent ? `<div class="preview-content">${initialContent}</div>` : '<div class="preview-placeholder"><p>Aucun contenu rédigé. Cliquez sur "Ouvrir l\'éditeur plein écran" pour commencer.</p></div>'}
                                    </div>
                                    <textarea id="chapter-content-textarea" style="display: none;">${initialContent}</textarea>
                                </div>
                            </div>
                        </div>
                    `;
                    
                    return modal;
                }
                
                // Fonction pour initialiser l'éditeur modulaire du chapitre
                function initChapterModularEditor(editorModal, chapterItem) {
                    console.log('🔧 Initialisation de l\'éditeur modulaire pour le chapitre...');
                    
                    // Attendre que l'éditeur soit prêt
                    if (typeof window.FullscreenEditor === 'undefined') {
                        console.log('Éditeur modulaire en cours de chargement, attente...');
                        showNotification('Chargement de l\'éditeur en cours...', 'info');
                        
                        window.addEventListener('editorReady', function() {
                            console.log('Éditeur modulaire prêt pour le chapitre');
                        });
                    } else {
                        console.log('Éditeur modulaire déjà prêt pour le chapitre');
                    }
                }
                
                // Fonction pour ouvrir l'éditeur plein écran directement depuis le chapitre
                function openChapterFullscreenEditorDirect(chapterItem, initialContent = '') {
                    console.log('🚀 Ouverture directe de l\'éditeur modulaire pour le chapitre...');
                    
                    try {
                        // Créer l'éditeur modulaire plein écran pour ce chapitre
                        let chapterFullscreenEditor = new window.FullscreenEditor({
                            initialContent: initialContent,
                            onSave: function(content) {
                                console.log('💾 Sauvegarde du contenu du chapitre:', content.substring(0, 50) + '...');
                                
                                // Sauvegarder le contenu dans le chapitre
                                saveChapterContentToItem(chapterItem, content);
                                
                                // Mettre à jour l'aperçu dans la liste des chapitres
                                updateChapterPreviewDirect(chapterItem, content);
                                
                                // Fermer l'éditeur
                                if (chapterFullscreenEditor) {
                                    chapterFullscreenEditor.close();
                                }
                                
                                // Afficher un message de succès
                                showNotification('Contenu du chapitre sauvegardé avec succès !', 'success');
                            },
                            onClose: function() {
                                console.log('Fermeture de l\'éditeur de chapitre');
                                if (chapterFullscreenEditor) {
                                    chapterFullscreenEditor = null;
                                }
                            }
                        });
                        
                        console.log('✅ Éditeur modulaire de chapitre créé avec succès');
                        showNotification('Éditeur modulaire ouvert pour le chapitre !', 'success');
                        
                    } catch (error) {
                        console.error('❌ Erreur lors de la création de l\'éditeur modulaire du chapitre:', error);
                        showNotification('Erreur lors de l\'ouverture de l\'éditeur: ' + error.message, 'error');
                    }
                }
                
                // Fonction pour sauvegarder le contenu du chapitre dans l'élément
                function saveChapterContentToItem(chapterItem, content) {
                    // Créer ou mettre à jour la zone d'aperçu du contenu
                    let contentPreviewZone = chapterItem.querySelector('.chapter-content-preview');
                    let contentDisplay = chapterItem.querySelector('.chapter-content-display');
                    
                    if (!contentPreviewZone) {
                        contentPreviewZone = document.createElement('div');
                        contentPreviewZone.className = 'chapter-content-preview';
                        contentPreviewZone.style.display = 'none';
                        contentPreviewZone.innerHTML = `
                            <h4>Aperçu du contenu :</h4>
                            <div class="chapter-content-display"></div>
                        `;
                        
                        // Insérer après le contenu du chapitre
                        const chapterContent = chapterItem.querySelector('.chapter-content');
                        if (chapterContent) {
                            chapterContent.parentNode.insertBefore(contentPreviewZone, chapterContent.nextSibling);
                        }
                        
                        contentDisplay = contentPreviewZone.querySelector('.chapter-content-display');
                    }
                    
                    if (contentDisplay) {
                        contentDisplay.innerHTML = content;
                        contentPreviewZone.style.display = 'block';
                    }
                    
                    // Mettre à jour le statut
                    const statusBadge = chapterItem.querySelector('.chapter-status');
                    if (statusBadge) {
                        statusBadge.textContent = 'Contenu créé';
                        statusBadge.className = 'chapter-status content-created';
                    }
                    
                    // Stocker le contenu dans le dataset pour la sauvegarde
                    chapterItem.dataset.chapterContent = content;
                    
                    console.log('✅ Contenu sauvegardé dans le chapitre:', chapterItem.dataset.chapterId);
                }
                
                // Fonction pour ouvrir l'éditeur plein écran du chapitre (ancienne fonction, maintenant pour le modal)
                window.openChapterFullscreenEditor = function(button) {
                    const editorModal = button.closest('.chapter-editor-modal');
                    const chapterItem = editorModal.chapterItem; // On va l'attacher plus tard
                    const contentTextarea = editorModal.querySelector('#chapter-content-textarea');
                    const contentPreview = editorModal.querySelector('#chapter-content-preview');
                    
                    console.log('🚀 Ouverture de l\'éditeur plein écran pour le chapitre...');
                    
                    try {
                        // Créer l'éditeur modulaire plein écran pour ce chapitre
                        let chapterFullscreenEditor = new window.FullscreenEditor({
                            initialContent: contentTextarea.value,
                            onSave: function(content) {
                                console.log('💾 Sauvegarde du contenu du chapitre:', content.substring(0, 50) + '...');
                                
                                // Mettre à jour le textarea et la prévisualisation
                                contentTextarea.value = content;
                                
                                // Créer une div temporaire pour nettoyer le HTML
                                const tempDiv = document.createElement('div');
                                tempDiv.innerHTML = content;
                                
                                // Appliquer des styles de base pour éviter le débordement
                                const previewContent = document.createElement('div');
                                previewContent.className = 'preview-content';
                                previewContent.innerHTML = content;
                                
                                // Mettre à jour la prévisualisation
                                contentPreview.innerHTML = '';
                                contentPreview.appendChild(previewContent);
                                
                                // Fermer l'éditeur
                                if (chapterFullscreenEditor) {
                                    chapterFullscreenEditor.close();
                                }
                                
                                // Afficher un message de succès
                                showNotification('Contenu du chapitre sauvegardé avec succès !', 'success');
                                
                                // Mettre à jour l'aperçu dans la liste des chapitres
                                updateChapterPreview(chapterItem, content);
                            },
                            onClose: function() {
                                console.log('Fermeture de l\'éditeur de chapitre');
                                if (chapterFullscreenEditor) {
                                    chapterFullscreenEditor = null;
                                }
                            }
                        });
                        
                        console.log('✅ Éditeur modulaire de chapitre créé avec succès');
                        showNotification('Éditeur modulaire ouvert pour le chapitre !', 'success');
                        
                    } catch (error) {
                        console.error('❌ Erreur lors de la création de l\'éditeur modulaire du chapitre:', error);
                        showNotification('Erreur lors de l\'ouverture de l\'éditeur: ' + error.message, 'error');
                    }
                };
                
                // Fonction pour mettre à jour l'aperçu du chapitre (pour le modal)
                function updateChapterPreview(chapterItem, content) {
                    const chapterPreview = chapterItem.querySelector('.chapter-preview .chapter-excerpt');
                    if (chapterPreview) {
                        // Extraire le texte du HTML pour l'aperçu
                        const tempDiv = document.createElement('div');
                        tempDiv.innerHTML = content;
                        const textContent = tempDiv.textContent || tempDiv.innerText || '';
                        
                        // Limiter à 100 caractères pour l'aperçu
                        const excerpt = textContent.length > 100 ? textContent.substring(0, 100) + '...' : textContent;
                        chapterPreview.textContent = excerpt || 'Aucun contenu rédigé pour le moment.';
                    }
                }
                
                // Fonction pour mettre à jour l'aperçu du chapitre directement (pour la liste)
                function updateChapterPreviewDirect(chapterItem, content) {
                    console.log('🔄 Mise à jour de l\'aperçu du chapitre:', chapterItem.dataset.chapterId);
                    
                    // Mettre à jour l'aperçu dans la liste
                    const chapterPreview = chapterItem.querySelector('.chapter-preview .chapter-excerpt');
                    if (chapterPreview) {
                        // Extraire le texte du HTML pour l'aperçu
                        const tempDiv = document.createElement('div');
                        tempDiv.innerHTML = content;
                        const textContent = tempDiv.textContent || tempDiv.innerText || '';
                        
                        // Limiter à 100 caractères pour l'aperçu
                        const excerpt = textContent.length > 100 ? textContent.substring(0, 100) + '...' : textContent;
                        chapterPreview.textContent = excerpt || 'Aucun contenu rédigé pour le moment.';
                    }
                    
                    // Créer ou mettre à jour la zone d'aperçu du contenu
                    let contentPreviewZone = chapterItem.querySelector('.chapter-content-preview');
                    let contentDisplay = chapterItem.querySelector('.chapter-content-display');
                    
                    if (!contentPreviewZone) {
                        contentPreviewZone = document.createElement('div');
                        contentPreviewZone.className = 'chapter-content-preview';
                        contentPreviewZone.innerHTML = `
                            <h4>Aperçu du contenu :</h4>
                            <div class="chapter-content-display"></div>
                        `;
                        
                        // Insérer après le contenu du chapitre
                        const chapterContent = chapterItem.querySelector('.chapter-content');
                        if (chapterContent) {
                            chapterContent.parentNode.insertBefore(contentPreviewZone, chapterContent.nextSibling);
                        }
                        
                        contentDisplay = contentPreviewZone.querySelector('.chapter-content-display');
                    }
                    
                    if (contentDisplay) {
                        contentDisplay.innerHTML = content;
                        contentPreviewZone.style.display = 'block';
                    }
                    
                    // Mettre à jour le statut
                    const statusBadge = chapterItem.querySelector('.chapter-status');
                    if (statusBadge) {
                        statusBadge.textContent = 'Contenu créé';
                        statusBadge.className = 'chapter-status content-created';
                    }
                    
                    // Stocker le contenu dans le dataset pour la sauvegarde
                    chapterItem.dataset.chapterContent = content;
                    
                    console.log('✅ Aperçu mis à jour avec succès');
                }
                
                // Fonction pour sauvegarder le contenu du chapitre
                window.saveChapterContent = function(button) {
                    const editorModal = button.closest('.chapter-editor-modal');
                    const contentTextarea = editorModal.querySelector('#chapter-content-textarea');
                    const content = contentTextarea.value;
                    
                    console.log('💾 Sauvegarde du contenu du chapitre...');
                    
                    // Ici on gérera la sauvegarde en base de données
                    // Pour l'instant, on affiche un message
                    showNotification('Sauvegarde du chapitre en cours de développement...', 'info');
                    
                    // Fermer le modal d'édition
                    editorModal.remove();
                };
                
                // Fonction pour uploader l'image de couverture d'un chapitre
                // (Déjà définie plus haut dans le fichier)
                
                // Fonction pour supprimer un chapitre
                window.deleteChapter = function(button, chapterId) {
                    const chapterItem = button.closest('.chapter-item');
                    const chapterTitle = chapterItem.querySelector('.chapter-title').value || 'Sans titre';
                    
                    if (confirm(`Êtes-vous sûr de vouloir supprimer le chapitre "${chapterTitle}" ?`)) {
                        console.log('🗑️ Suppression du chapitre:', chapterId);
                        
                        // Supprimer de la base de données
                        deleteChapterFromDatabase(chapterId, chapterItem);
                    }
                };
                
                // Fonction pour supprimer un chapitre de la base de données
                function deleteChapterFromDatabase(chapterId, chapterItem) {
                    const formData = new FormData();
                    formData.append('chapter_id', chapterId);
                    
                    fetch('/admin/articles/delete-chapter', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            chapterItem.remove();
                            showNotification('🗑️ Chapitre supprimé avec succès', 'success');
                            
                            // Vérifier s'il reste des chapitres
                            const chaptersList = document.getElementById('modal-chapters-list');
                            if (chaptersList.children.length === 0) {
                                chaptersList.innerHTML = '<div class="chapters-placeholder"><p>Aucun chapitre créé. Commencez par créer votre premier chapitre.</p></div>';
                            }
                        } else {
                            showNotification(`❌ Erreur lors de la suppression : ${data.message}`, 'error');
                        }
                    })
                    .catch(error => {
                        console.error('❌ Erreur lors de la suppression du chapitre:', error);
                        showNotification('❌ Erreur lors de la suppression du chapitre', 'error');
                    });
                }
                
                // Fonction pour sauvegarder tous les chapitres
                window.saveAllChapters = function() {
                    console.log('💾 Sauvegarde de tous les chapitres...');
                    
                    // Récupérer tous les chapitres créés
                    const chaptersList = document.getElementById('modal-chapters-list');
                    const chapterItems = chaptersList.querySelectorAll('.chapter-item');
                    
                    if (chapterItems.length === 0) {
                        showNotification('❌ Aucun chapitre à sauvegarder', 'warning');
                        return;
                    }
                    
                    // Vérifier que chaque chapitre a au moins un titre
                    let hasValidChapters = false;
                    let validChapters = [];
                    
                    chapterItems.forEach(chapterItem => {
                        const title = chapterItem.querySelector('.chapter-title').value.trim();
                        if (title) {
                            hasValidChapters = true;
                            validChapters.push(chapterItem);
                        }
                    });
                    
                    if (!hasValidChapters) {
                        showNotification('❌ Veuillez au moins donner un titre à un chapitre', 'warning');
                        return;
                    }
                    
                    // Désactiver le bouton pour éviter les clics multiples
                    const saveButton = document.querySelector('#save-all-chapters-btn');
                    if (saveButton) {
                        saveButton.disabled = true;
                        saveButton.textContent = '💾 Sauvegarde...';
                    }
                    
                    // Préparer les données pour l'envoi au backend
                    const chaptersData = validChapters.map(chapterItem => {
                        const title = chapterItem.querySelector('.chapter-title').value.trim();
                        const slug = chapterItem.querySelector('.chapter-slug').value.trim();
                        const content = chapterItem.dataset.chapterContent || '';
                        const coverImageId = chapterItem.dataset.coverImageId || null;
                        // Préserver le statut actuel du chapitre
                        const statusElement = chapterItem.querySelector('.chapter-status');
                        const currentStatus = statusElement ? statusElement.textContent.trim() : 'Brouillon';
                        const status = currentStatus === 'Publié' ? 'published' : 'draft';
                        
                        return {
                            title: title,
                            slug: slug,
                            content: content,
                            cover_image_id: coverImageId,
                            status: status
                        };
                    });
                    
                    // Envoyer les données au backend
                    const formData = new FormData();
                    formData.append('article_id', currentArticleId);
                    formData.append('chapters', JSON.stringify(chaptersData));
                    
                    fetch('/admin/articles/save-chapters', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => {
                        console.log('🔍 Réponse du serveur:', response);
                        console.log('🔍 Status:', response.status);
                        console.log('🔍 Headers:', response.headers);
                        
                        // Vérifier le type de contenu
                        const contentType = response.headers.get('content-type');
                        console.log('🔍 Content-Type:', contentType);
                        
                        if (!response.ok) {
                            throw new Error(`Erreur HTTP: ${response.status}`);
                        }
                        
                        // Si ce n'est pas du JSON, afficher le contenu brut
                        if (!contentType || !contentType.includes('application/json')) {
                            return response.text().then(text => {
                                console.log('⚠️ Réponse non-JSON reçue:', text.substring(0, 500));
                                throw new Error('Le serveur a retourné du HTML au lieu de JSON. Vérifiez les logs du serveur.');
                            });
                        }
                        
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            showNotification('✅ ' + data.message, 'success');
                            
                            // Fermer le modal de création de chapitre
                            closeChapterCreationModal();
                            
                            // Mettre à jour la liste des chapitres dans le gestionnaire principal
                            updateChaptersListInManager(validChapters);
                            
                            // Mettre à jour le progrès du dossier si disponible
                            if (data.progress !== undefined) {
                                console.log('📊 Progrès du dossier mis à jour:', data.progress + '%');
                            }
                        } else {
                            throw new Error(data.error || 'Erreur lors de la sauvegarde');
                        }
                    })
                    .catch(error => {
                        console.error('❌ Erreur lors de la sauvegarde des chapitres:', error);
                        showNotification('❌ Erreur: ' + error.message, 'error');
                    })
                    .finally(() => {
                        // Réactiver le bouton
                        if (saveButton) {
                            saveButton.disabled = false;
                            saveButton.textContent = '💾 Sauvegarder tout';
                        }
                    });
                };
                
                // Fonction pour fermer le modal de création de chapitre
                function closeChapterCreationModal() {
                    // Chercher tous les modals possibles
                    const modals = [
                        document.querySelector('.chapter-editor-modal'),
                        document.querySelector('.chapter-manager-modal'),
                        document.querySelector('.modal-chapter-creation')
                    ];
                    
                    modals.forEach(modal => {
                        if (modal) {
                            console.log('🔒 Fermeture du modal:', modal.className);
                            modal.remove();
                        }
                    });
                    
                    // Vérifier qu'aucun modal n'est resté ouvert
                    const remainingModals = document.querySelectorAll('.chapter-editor-modal, .chapter-manager-modal, .modal-chapter-creation');
                    if (remainingModals.length > 0) {
                        console.log('⚠️ Modal restant ouvert, suppression forcée');
                        remainingModals.forEach(modal => modal.remove());
                    }
                }
                
                // Fonction pour mettre à jour la liste des chapitres dans le gestionnaire principal
                function updateChaptersListInManager(chapterItems) {
                    const chaptersList = document.getElementById('chapters-list');
                    const placeholder = chaptersList.querySelector('.chapters-placeholder');
                    
                    if (placeholder) {
                        placeholder.remove();
                    }
                    
                    // Vider complètement la liste existante pour éviter les doublons
                    const existingChapters = chaptersList.querySelectorAll('.chapter-list-item');
                    existingChapters.forEach(chapter => chapter.remove());
                    
                    // Créer des éléments de chapitre pour la liste principale
                    chapterItems.forEach(chapterItem => {
                        const title = chapterItem.querySelector('.chapter-title').value.trim();
                        const slug = chapterItem.querySelector('.chapter-slug').value.trim();
                        
                        // Récupérer le contenu depuis le dataset ou la zone d'aperçu
                        let content = '';
                        if (chapterItem.dataset.chapterContent) {
                            content = chapterItem.dataset.chapterContent;
                        } else {
                            const contentDisplay = chapterItem.querySelector('.chapter-content-display');
                            content = contentDisplay ? contentDisplay.innerHTML : '';
                        }
                        
                        if (title) {
                            const chapterElement = createChapterListItem(title, slug, content);
                            chaptersList.appendChild(chapterElement);
                        }
                    });
                }
                
                // Fonction pour créer un élément de chapitre dans la liste principale
                function createChapterListItem(title, slug, content) {
                    console.log('📝 Création d\'un élément de chapitre dans la liste:', { title, slug, hasContent: !!content });
                    
                    const chapterDiv = document.createElement('div');
                    chapterDiv.className = 'chapter-list-item';
                    chapterDiv.dataset.chapterId = 'saved-' + Date.now();
                    
                    // Stocker le contenu dans le dataset pour la modification
                    if (content) {
                        chapterDiv.dataset.chapterContent = content;
                    }
                    
                    chapterDiv.innerHTML = `
                        <div class="chapter-list-header">
                            <div class="chapter-list-info">
                                <h4 class="chapter-list-title">${title}</h4>
                                <span class="chapter-list-slug">${slug || 'Aucun slug'}</span>
                            </div>
                            <div class="chapter-list-actions">
                                <button type="button" class="btn btn-sm btn-primary" onclick="editChapterFromList(this)" title="Modifier le contenu">
                                    <span class="icon">✏️</span>
                                </button>
                                <button type="button" class="btn btn-sm btn-warning" onclick="toggleChapterStatusFromList(this)" title="Publier/Dépublier">
                                    <span class="icon">📢</span>
                                </button>
                                <button type="button" class="btn btn-sm btn-danger" onclick="deleteChapterFromList(this)" title="Supprimer">
                                    <span class="icon">🗑️</span>
                                </button>
                            </div>
                        </div>
                        <div class="chapter-list-content">
                            <div class="chapter-list-preview">
                                ${content ? `<div class="chapter-content-preview">${content}</div>` : '<p class="no-content">Aucun contenu rédigé</p>'}
                            </div>
                            <div class="chapter-list-metadata">
                                <span class="chapter-status draft">Brouillon</span>
                                <span class="chapter-reading-time">0 min</span>
                            </div>
                        </div>
                    `;
                    
                    console.log('✅ Élément de chapitre créé avec succès');
                    return chapterDiv;
                }

                // Fonction pour ouvrir la médiathèque
                window.openMediaLibrary = function() {
                    // Ouvrir la médiathèque dans une nouvelle fenêtre
                    const mediaWindow = window.open('/admin/media?select_mode=1', 'mediaLibrary', 'width=1200,height=800,scrollbars=yes,resizable=yes');
                    
                    // Écouter les messages de la fenêtre de médiathèque
                    window.addEventListener('message', function(event) {
                        if (event.origin !== window.location.origin) return;
                        
                        if (event.data.type === 'mediaSelected') {
                            const media = event.data.media;
                            
                            // Mettre à jour l'ID de l'image
                            document.getElementById('cover_image_id').value = media.id;
                            
                            // Afficher la prévisualisation
                            const preview = document.getElementById('selected-media-preview');
                            const previewImg = document.getElementById('selected-media-image');
                            const previewInfo = document.getElementById('selected-media-info');
                            
                            previewImg.src = media.url;
                            previewInfo.textContent = `Image sélectionnée: ${media.filename}`;
                            preview.style.display = 'block';
                            
                            // Cacher l'upload preview
                            document.getElementById('upload-preview').style.display = 'none';
                            document.getElementById('cover_image_file').value = '';
                            
                            // Fermer la fenêtre de médiathèque
                            mediaWindow.close();
                        }
                    });
                };

            });
        </script>
</body>
</html>
