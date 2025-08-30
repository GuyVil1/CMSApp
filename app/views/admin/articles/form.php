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
        }
        
        .preview-content {
            font-size: 14px;
            line-height: 1.5;
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
                            <?= $article->getContent() ?>
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
                            <option value="published" <?= ($article && $article->getStatus() === 'published') ? 'selected' : '' ?>>✅ Publié</option>
                            <option value="archived" <?= ($article && $article->getStatus() === 'archived') ? 'selected' : '' ?>>📦 Archivé</option>
                        </select>
                        <p class="form-hint">Choisissez le statut de publication de l'article</p>
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
                        
                        <!-- Si un jeu est sélectionné -->
                        <div id="game-cover-info" style="display: none;">
                            <div class="game-cover-preview">
                                <img id="game-cover-preview" src="" alt="Cover du jeu" style="max-width: 200px; border-radius: 8px; margin: 10px 0;">
                                <p class="form-hint">✅ Cover du jeu sélectionné (automatique)</p>
                                <input type="hidden" id="cover_image_id" name="cover_image_id" value="">
                            </div>
                        </div>
                        
                        <!-- Si aucun jeu sélectionné -->
                        <div id="manual-cover-selection">
                            <input type="file" id="cover_image_file" name="cover_image_file" accept="image/*" style="margin-bottom: 10px;">
                            <p class="form-hint">📁 Upload direct d'une image d'illustration (JPG, PNG, WebP - max 4MB)</p>
                            <div id="upload-preview" style="display: none; margin-top: 10px;">
                                <img id="preview-image" src="" alt="Aperçu" style="max-width: 200px; border-radius: 8px; border: 1px solid #ccc;">
                                <p class="form-hint">✅ Image sélectionnée</p>
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
                                    contentPreview.innerHTML = '<div class="preview-content">' + content + '</div>';
                                    
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
                const coverImageIdInput = document.getElementById('cover_image_id');
                const coverImageFileInput = document.getElementById('cover_image_file');
                const uploadPreview = document.getElementById('upload-preview');
                const previewImage = document.getElementById('preview-image');
                
                if (gameSelect) {
                    gameSelect.addEventListener('change', function() {
                        const selectedGameId = this.value;
                        
                        if (selectedGameId) {
                            // Récupérer les informations du jeu sélectionné
                            fetchGameCover(selectedGameId);
                        } else {
                            // Aucun jeu sélectionné, afficher la sélection manuelle
                            showManualCoverSelection();
                        }
                    });
                    
                    // Initialiser l'affichage
                    if (gameSelect.value) {
                        fetchGameCover(gameSelect.value);
                    }
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
                                coverImageIdInput.value = ''; // Clear previous cover_image_id
                            };
                            reader.readAsDataURL(file);
                        } else {
                            previewImage.src = '';
                            uploadPreview.style.display = 'none';
                            coverImageIdInput.value = '';
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
                    manualCoverSelection.style.display = 'none';
                    gameCoverPreview.src = coverImage.url;
                    coverImageIdInput.value = coverImage.id;
                }
                
                // Afficher la sélection manuelle
                function showManualCoverSelection() {
                    gameCoverInfo.style.display = 'none';
                    manualCoverSelection.style.display = 'block';
                    coverImageIdInput.value = '';
                    uploadPreview.style.display = 'none';
                    previewImage.src = '';
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
                        
                        if (!title) {
                            e.preventDefault();
                            alert('Le titre est obligatoire');
                            return false;
                        }
                        
                        if (!content) {
                            e.preventDefault();
                            alert('Le contenu est obligatoire');
                            return false;
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


            });
        </script>
</body>
</html>
