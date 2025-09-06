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

            <!-- Section Pages Légales -->
            <div class="settings-section">
                <h3>📋 Pages Légales</h3>
                <p class="section-description">Éditez le contenu des pages légales du site</p>
                
                <div class="legal-editor">
                    <div class="legal-tabs">
                        <button type="button" class="legal-tab active" data-tab="mentions">Mentions Légales</button>
                        <button type="button" class="legal-tab" data-tab="privacy">Politique de Confidentialité</button>
                        <button type="button" class="legal-tab" data-tab="cgu">CGU</button>
                        <button type="button" class="legal-tab" data-tab="cookies">Cookies</button>
                    </div>
                    
                    <div class="legal-content">
                        <div class="legal-panel active" id="mentions">
                            <h4>Mentions Légales</h4>
                            <p class="legal-help">💡 Vous pouvez utiliser du HTML : <code>&lt;h2&gt;</code>, <code>&lt;p&gt;</code>, <code>&lt;ul&gt;</code>, <code>&lt;li&gt;</code>, <code>&lt;strong&gt;</code>, <code>&lt;em&gt;</code>, etc.</p>
                            <textarea name="legal_mentions_content" class="legal-textarea" placeholder="<h2>Éditeur du site</h2>
<p><strong>Belgium Video Gaming</strong></p>
<p>Site web dédié à l'actualité jeux vidéo en Belgique</p>
<p>📧 Contact : <a href='mailto:contact@belgium-video-gaming.be'>contact@belgium-video-gaming.be</a></p>"><?= htmlspecialchars($settings['legal_mentions_content'] ?? '') ?></textarea>
                        </div>
                        
                        <div class="legal-panel" id="privacy">
                            <h4>Politique de Confidentialité</h4>
                            <p class="legal-help">💡 Vous pouvez utiliser du HTML : <code>&lt;h2&gt;</code>, <code>&lt;p&gt;</code>, <code>&lt;ul&gt;</code>, <code>&lt;li&gt;</code>, <code>&lt;strong&gt;</code>, <code>&lt;em&gt;</code>, etc.</p>
                            <textarea name="legal_privacy_content" class="legal-textarea" placeholder="<h2>Collecte des données</h2>
<p>Belgium Video Gaming collecte uniquement les données personnelles nécessaires au fonctionnement du site :</p>
<ul>
    <li><strong>Inscription :</strong> nom d'utilisateur, adresse e-mail, mot de passe (chiffré)</li>
    <li><strong>Navigation :</strong> cookies techniques pour le bon fonctionnement du site</li>
</ul>"><?= htmlspecialchars($settings['legal_privacy_content'] ?? '') ?></textarea>
                        </div>
                        
                        <div class="legal-panel" id="cgu">
                            <h4>Conditions Générales d'Utilisation</h4>
                            <p class="legal-help">💡 Vous pouvez utiliser du HTML : <code>&lt;h2&gt;</code>, <code>&lt;p&gt;</code>, <code>&lt;ul&gt;</code>, <code>&lt;li&gt;</code>, <code>&lt;strong&gt;</code>, <code>&lt;em&gt;</code>, etc.</p>
                            <textarea name="legal_cgu_content" class="legal-textarea" placeholder="<h2>Acceptation des conditions</h2>
<p>L'utilisation du site Belgium Video Gaming implique l'acceptation pleine et entière des présentes conditions générales d'utilisation.</p>

<h2>Description du service</h2>
<p>Belgium Video Gaming est un site d'information dédié à l'actualité des jeux vidéo en Belgique.</p>"><?= htmlspecialchars($settings['legal_cgu_content'] ?? '') ?></textarea>
                        </div>
                        
                        <div class="legal-panel" id="cookies">
                            <h4>Politique des Cookies</h4>
                            <p class="legal-help">💡 Vous pouvez utiliser du HTML : <code>&lt;h2&gt;</code>, <code>&lt;p&gt;</code>, <code>&lt;ul&gt;</code>, <code>&lt;li&gt;</code>, <code>&lt;strong&gt;</code>, <code>&lt;em&gt;</code>, etc.</p>
                            <textarea name="legal_cookies_content" class="legal-textarea" placeholder="<h2>Qu'est-ce qu'un cookie ?</h2>
<p>Un cookie est un petit fichier texte stocké sur votre ordinateur ou appareil mobile lorsque vous visitez un site web.</p>

<h2>Cookies utilisés sur ce site</h2>
<p>Belgium Video Gaming utilise uniquement des cookies techniques nécessaires au bon fonctionnement du site :</p>"><?= htmlspecialchars($settings['legal_cookies_content'] ?? '') ?></textarea>
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

        /* ========================================
           ÉDITEUR DE PAGES LÉGALES
           ======================================== */
        .legal-editor {
            background: var(--admin-card-bg);
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid var(--admin-border);
        }

        .legal-tabs {
            display: flex;
            background: var(--admin-bg-secondary);
            border-bottom: 1px solid var(--admin-border);
        }

        .legal-tab {
            flex: 1;
            padding: 1rem;
            background: transparent;
            border: none;
            color: var(--admin-text-secondary);
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .legal-tab:hover {
            background: var(--admin-bg-hover);
            color: var(--admin-text);
        }

        .legal-tab.active {
            background: var(--admin-primary);
            color: white;
        }

        .legal-content {
            padding: 0;
        }

        .legal-panel {
            display: none;
            padding: 1.5rem;
        }

        .legal-panel.active {
            display: block;
        }

        .legal-panel h4 {
            margin: 0 0 0.5rem 0;
            color: var(--admin-text);
            font-size: 1.1rem;
        }

        .legal-help {
            margin: 0 0 1rem 0;
            padding: 0.75rem;
            background: rgba(255, 193, 7, 0.1);
            border: 1px solid rgba(255, 193, 7, 0.3);
            border-radius: 6px;
            color: var(--admin-text);
            font-size: 0.85rem;
            line-height: 1.4;
        }

        .legal-help code {
            background: rgba(255, 193, 7, 0.2);
            padding: 0.2rem 0.4rem;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
            font-size: 0.8rem;
            color: var(--admin-primary);
        }

        .legal-textarea {
            width: 100%;
            min-height: 300px;
            padding: 1rem;
            border: 1px solid var(--admin-border);
            border-radius: 6px;
            background: var(--admin-input-bg);
            color: var(--admin-text);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 0.9rem;
            line-height: 1.5;
            resize: vertical;
            transition: border-color 0.3s ease;
        }

        .legal-textarea:focus {
            outline: none;
            border-color: var(--admin-primary);
            box-shadow: 0 0 0 2px rgba(255, 193, 7, 0.2);
        }

        .legal-textarea::placeholder {
            color: var(--admin-text-secondary);
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

            .legal-tabs {
                flex-direction: column;
            }

            .legal-tab {
                text-align: center;
            }
        }
    </style>

    <script>
        // Gestion des onglets pour l'éditeur de pages légales
        document.addEventListener('DOMContentLoaded', function() {
            const tabs = document.querySelectorAll('.legal-tab');
            const panels = document.querySelectorAll('.legal-panel');

            tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    const targetTab = this.getAttribute('data-tab');
                    
                    // Retirer la classe active de tous les onglets et panneaux
                    tabs.forEach(t => t.classList.remove('active'));
                    panels.forEach(p => p.classList.remove('active'));
                    
                    // Ajouter la classe active à l'onglet cliqué et au panneau correspondant
                    this.classList.add('active');
                    document.getElementById(targetTab).classList.add('active');
                });
            });
        });
    </script>
</body>
</html>
