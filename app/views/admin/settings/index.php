<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paramètres - GameNews Belgium</title>
    <link rel="stylesheet" href="/admin.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>⚙️ Paramètres du site</h1>
            <p>Configurez les options de votre site</p>
        </div>

        <?php if (isset($_SESSION['flash_message'])): ?>
            <div class="flash-message flash-<?= $_SESSION['flash_type'] ?>">
                <?= htmlspecialchars($_SESSION['flash_message']) ?>
            </div>
            <?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); ?>
        <?php endif; ?>
        
        <div class="actions" style="margin-bottom: var(--admin-spacing-lg);">
            <a href="/admin/dashboard" class="btn">← Retour au dashboard</a>
        </div>

        <form method="POST" action="/admin/settings/save" class="settings-form">
            <div class="settings-section">
                <h3>👥 Gestion des utilisateurs</h3>
                <div class="setting-item">
                    <label class="toggle-switch">
                        <input type="checkbox" name="allow_registration" value="1" 
                               <?= ($settings['allow_registration'] ?? '0') === '1' ? 'checked' : '' ?>>
                        <span class="slider"></span>
                    </label>
                    <div class="setting-info">
                        <h4>Autoriser les inscriptions</h4>
                        <p>Permet aux visiteurs de créer un compte sur le site</p>
                    </div>
                </div>
            </div>

            <div class="settings-section">
                <h3>🎨 Interface</h3>
                <div class="setting-item">
                    <label class="toggle-switch">
                        <input type="checkbox" name="dark_mode" value="1" 
                               <?= ($settings['dark_mode'] ?? '0') === '1' ? 'checked' : '' ?>>
                        <span class="slider"></span>
                    </label>
                    <div class="setting-info">
                        <h4>Mode sombre</h4>
                        <p>Active le mode sombre par défaut pour tous les visiteurs</p>
                    </div>
                </div>
                
                <div class="setting-item">
                    <label class="select-label">
                        <h4>Thème par défaut</h4>
                        <select name="default_theme" class="theme-select">
                            <?php foreach ($themes as $theme): ?>
                                <option value="<?= htmlspecialchars($theme['name']) ?>" 
                                        <?= ($settings['default_theme'] ?? 'defaut') === $theme['name'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($theme['display_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <p>Thème appliqué par défaut aux nouveaux visiteurs</p>
                    </label>
                </div>
            </div>

            <div class="settings-section">
                <h3>🔧 Maintenance</h3>
                <div class="setting-item">
                    <label class="toggle-switch">
                        <input type="checkbox" name="maintenance_mode" value="1" 
                               <?= ($settings['maintenance_mode'] ?? '0') === '1' ? 'checked' : '' ?>>
                        <span class="slider"></span>
                    </label>
                    <div class="setting-info">
                        <h4>Mode maintenance</h4>
                        <p>Affiche une page de maintenance au lieu du site (utile pour les mises à jour)</p>
                    </div>
                </div>
            </div>

            <!-- Section Footer -->
            <div class="settings-section">
                <h3>📄 Configuration du Footer</h3>
                
                <div class="setting-item">
                    <label class="setting-label">
                        <input type="text" name="footer_tagline" 
                               value="<?= htmlspecialchars($settings['footer_tagline'] ?? '') ?>"
                               placeholder="Votre phrase d'accroche..."
                               class="setting-input">
                    </label>
                    <div class="setting-info">
                        <h4>Phrase d'accroche</h4>
                        <p>Texte affiché dans la section "À propos de GameNews" du footer</p>
                    </div>
                </div>
            </div>

            <!-- Section Réseaux Sociaux -->
            <div class="settings-section">
                <h3>🌐 Réseaux Sociaux</h3>
                
                <div class="setting-item">
                    <label class="setting-label">
                        <input type="url" name="social_twitter" 
                               value="<?= htmlspecialchars($settings['social_twitter'] ?? '') ?>"
                               placeholder="https://twitter.com/votrecompte"
                               class="setting-input">
                    </label>
                    <div class="setting-info">
                        <h4>Twitter</h4>
                        <p>URL de votre compte Twitter (laisser vide pour désactiver le bouton)</p>
                    </div>
                </div>

                <div class="setting-item">
                    <label class="setting-label">
                        <input type="url" name="social_facebook" 
                               value="<?= htmlspecialchars($settings['social_facebook'] ?? '') ?>"
                               placeholder="https://facebook.com/votrecompte"
                               class="setting-input">
                    </label>
                    <div class="setting-info">
                        <h4>Facebook</h4>
                        <p>URL de votre compte Facebook (laisser vide pour désactiver le bouton)</p>
                    </div>
                </div>

                <div class="setting-item">
                    <label class="setting-label">
                        <input type="url" name="social_youtube" 
                               value="<?= htmlspecialchars($settings['social_youtube'] ?? '') ?>"
                               placeholder="https://youtube.com/@votrecompte"
                               class="setting-input">
                    </label>
                    <div class="setting-info">
                        <h4>YouTube</h4>
                        <p>URL de votre chaîne YouTube (laisser vide pour désactiver le bouton)</p>
                    </div>
                </div>
            </div>

            <!-- Section Logos et Branding -->
            <div class="settings-section">
                <h3>🎨 Logos et Branding</h3>
                
                <div class="setting-item">
                    <label class="setting-label">
                        <select name="header_logo" class="setting-select">
                            <?php foreach ($logos as $logo): ?>
                                <option value="<?= htmlspecialchars($logo['filename']) ?>" 
                                        <?= ($settings['header_logo'] ?? 'Logo.svg') === $logo['filename'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($logo['display_name']) ?> (<?= strtoupper($logo['extension']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <div class="setting-info">
                        <h4>Logo du header</h4>
                        <p>Logo affiché dans l'en-tête du site</p>
                        <div class="logo-preview">
                            <img src="/assets/images/logos/<?= htmlspecialchars($settings['header_logo'] ?? 'Logo.svg') ?>" 
                                 alt="Prévisualisation" style="max-width: 100px; max-height: 50px; margin-top: 0.5rem;">
                        </div>
                    </div>
                </div>

                <div class="setting-item">
                    <label class="setting-label">
                        <select name="footer_logo" class="setting-select">
                            <?php foreach ($logos as $logo): ?>
                                <option value="<?= htmlspecialchars($logo['filename']) ?>" 
                                        <?= ($settings['footer_logo'] ?? 'Logo_neutre_500px.png') === $logo['filename'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($logo['display_name']) ?> (<?= strtoupper($logo['extension']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <div class="setting-info">
                        <h4>Logo du footer</h4>
                        <p>Logo affiché dans le pied de page</p>
                        <div class="logo-preview">
                            <img src="/assets/images/logos/<?= htmlspecialchars($settings['footer_logo'] ?? 'Logo_neutre_500px.png') ?>" 
                                 alt="Prévisualisation" style="max-width: 100px; max-height: 50px; margin-top: 0.5rem;">
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="save-btn">
                    💾 Sauvegarder les paramètres
                </button>
                <a href="/admin/dashboard" class="cancel-btn">
                    ❌ Annuler
                </a>
            </div>
        </form>
    </div>

    <style>
        .settings-form {
            max-width: 800px;
            margin: 0 auto;
        }

        .settings-section {
            background: var(--admin-card-bg);
            border: 1px solid var(--admin-border);
            border-radius: 12px;
            padding: var(--admin-spacing-lg);
            margin-bottom: var(--admin-spacing-lg);
        }

        .settings-section h3 {
            color: var(--admin-primary);
            margin-bottom: var(--admin-spacing-lg);
            font-size: 1.3em;
            border-bottom: 2px solid var(--admin-border);
            padding-bottom: var(--admin-spacing-sm);
        }

        .setting-item {
            display: flex;
            align-items: flex-start;
            gap: var(--admin-spacing-lg);
            padding: var(--admin-spacing-md) 0;
            border-bottom: 1px solid var(--admin-border);
        }

        .setting-item:last-child {
            border-bottom: none;
        }

        .setting-info {
            flex: 1;
        }

        .setting-info h4 {
            color: var(--admin-text);
            margin-bottom: var(--admin-spacing-xs);
            font-size: 1.1em;
        }

        .setting-info p {
            color: var(--admin-text-muted);
            font-size: 0.9em;
            line-height: 1.4;
        }

        /* Toggle Switch */
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
            flex-shrink: 0;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked + .slider {
            background-color: var(--admin-primary);
        }

        input:checked + .slider:before {
            transform: translateX(26px);
        }

        /* Select */
        .select-label {
            display: flex;
            flex-direction: column;
            gap: var(--admin-spacing-sm);
            flex: 1;
        }

        .theme-select {
            padding: var(--admin-spacing-sm) var(--admin-spacing-md);
            border: 1px solid var(--admin-border);
            border-radius: 8px;
            background: var(--admin-card-bg);
            color: var(--admin-text);
            font-size: 1em;
            max-width: 200px;
        }

        .theme-select:focus {
            outline: none;
            border-color: var(--admin-primary);
            box-shadow: 0 0 0 2px rgba(255, 215, 0, 0.2);
        }

        /* Form Actions */
        .form-actions {
            display: flex;
            gap: var(--admin-spacing-md);
            justify-content: center;
            margin-top: var(--admin-spacing-xl);
        }

        .save-btn {
            background: var(--admin-success);
            color: white;
            border: none;
            padding: var(--admin-spacing-md) var(--admin-spacing-xl);
            border-radius: 25px;
            font-size: 1.1em;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: var(--admin-spacing-sm);
        }

        .save-btn:hover {
            background: #27ae60;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(39, 174, 96, 0.3);
        }

        .cancel-btn {
            background: var(--admin-card-bg);
            color: var(--admin-text);
            border: 1px solid var(--admin-border);
            padding: var(--admin-spacing-md) var(--admin-spacing-xl);
            border-radius: 25px;
            font-size: 1.1em;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: var(--admin-spacing-sm);
        }

        .cancel-btn:hover {
            background: var(--admin-secondary);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(231, 76, 60, 0.3);
        }

        /* Flash Messages */
        .flash-message {
            padding: var(--admin-spacing-md);
            border-radius: 8px;
            margin-bottom: var(--admin-spacing-lg);
            font-weight: 600;
        }

        .flash-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .flash-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .setting-item {
                flex-direction: column;
                gap: var(--admin-spacing-md);
            }

            .form-actions {
                flex-direction: column;
                align-items: center;
            }
        }
    </style>
</body>
</html>
