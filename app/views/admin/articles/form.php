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
        .tag-checkbox {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
            padding: 8px;
            border-radius: 4px;
            background: rgba(255, 255, 255, 0.05);
        }
        .tag-checkbox input[type="checkbox"] {
            margin-right: 10px;
        }
        .tag-checkbox label {
            color: white;
            margin: 0;
            cursor: pointer;
            font-size: 14px;
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
        <?php if (isset($_SESSION['flash'])): ?>
            <?php foreach ($_SESSION['flash'] as $type => $message): ?>
                <div class="flash flash-<?= $type ?>">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endforeach; ?>
            <?php unset($_SESSION['flash']); ?>
        <?php endif; ?>

        <form method="POST" action="<?= $article ? "/admin/articles/update/{$article->getId()}" : '/admin/articles/store' ?>" class="form-container">
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
            <textarea id="content" name="content" style="display: none;" required><?= $article ? htmlspecialchars($article->getContent()) : '' ?></textarea>
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
                    </div>

                                         <!-- Image de couverture -->
                     <div class="form-group">
                         <label for="cover_image">🖼️ Image de couverture *</label>
                         <input type="file" id="cover_image" name="cover_image" accept="image/*" required>
                         <p class="form-hint">Cette image sera utilisée pour les miniatures et la mise en avant</p>
                     </div>

                    <!-- Position en avant -->
                    <div class="featured-positions">
                        <h3>🎯 Position en avant</h3>
                        <p style="color: #ccc; font-size: 12px; margin-bottom: 15px;">
                            Choisissez si cet article doit apparaître en avant sur la page d'accueil
                        </p>
                        
                        <div class="position-option">
                            <input type="radio" id="position_none" name="featured_position" value="" 
                                   <?= !$article || !$article->getFeaturedPosition() ? 'checked' : '' ?>>
                            <label for="position_none">Pas en avant</label>
                        </div>
                        
                        <?php foreach ($featured_positions as $position): ?>
                            <div class="position-option <?= $position['available'] ? 'position-available' : 'position-unavailable' ?>">
                                <input type="radio" id="position_<?= $position['position'] ?>" 
                                       name="featured_position" value="<?= $position['position'] ?>"
                                       <?= $article && $article->getFeaturedPosition() == $position['position'] ? 'checked' : '' ?>
                                       <?= !$position['available'] ? 'disabled' : '' ?>>
                                <label for="position_<?= $position['position'] ?>">
                                    <?= htmlspecialchars($position['label']) ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Tags -->
                    <div class="tags-container">
                        <h3>🏷️ Tags</h3>
                        <p style="color: #ccc; font-size: 12px; margin-bottom: 15px;">
                            Sélectionnez les tags associés à cet article
                        </p>
                        
                        <?php foreach ($tags as $tag): ?>
                            <div class="tag-checkbox">
                                <input type="checkbox" id="tag_<?= $tag['id'] ?>" 
                                       name="tags[]" value="<?= $tag['id'] ?>"
                                       <?= $article && in_array($tag['id'], $articleTagIds ?? []) ? 'checked' : '' ?>>
                                <label for="tag_<?= $tag['id'] ?>">
                                    <?= htmlspecialchars($tag['name']) ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
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
        <script src="/public/js/editor/editor-loader.js"></script>
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
                        
                        // Attendre que l'éditeur modulaire soit prêt
                        if (typeof window.FullscreenEditor === 'undefined') {
                            console.log('Éditeur modulaire en cours de chargement, attente...');
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
                        // Créer l'éditeur modulaire plein écran
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
                        
                        console.log('Éditeur modulaire créé avec succès');
                        showNotification('Éditeur modulaire ouvert !', 'success');
                    } catch (error) {
                        console.error('Erreur lors de la création de l\'éditeur modulaire:', error);
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
            });
        </script>
</body>
</html>
