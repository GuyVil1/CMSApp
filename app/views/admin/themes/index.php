<?php
/**
 * Interface de gestion des thèmes
 */
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des thèmes - GameNews Belgium</title>
    <link rel="stylesheet" href="/admin.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎨 Gestion des thèmes</h1>
            <p>Gérez l'apparence de votre site avec des thèmes personnalisés</p>
        </div>
        
        <div class="actions" style="margin-bottom: var(--admin-spacing-lg);">
            <a href="/admin/dashboard" class="btn">← Retour au dashboard</a>
        </div>

        <div class="stats-grid">
            <?php foreach ($themes as $theme): ?>
                <div class="stat-card <?php echo $theme['name'] === $currentTheme['name'] ? 'active' : ''; ?>" style="position: relative; overflow: hidden;">
                    <?php if ($theme['name'] === $currentTheme['name']): ?>
                        <div style="position: absolute; top: 0; left: 0; right: 0; height: 4px; background: var(--admin-primary);"></div>
                    <?php endif; ?>
                    
                    <div class="theme-preview" style="margin: -15px -15px 15px -15px; background: var(--admin-input-bg); border-radius: 10px 10px 0 0; padding: 15px;">
                        <div class="preview-title" style="color: var(--admin-text-muted); font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 10px;">Aperçu du thème</div>
                        <div class="preview-content" style="display: flex; height: 80px; border-radius: 8px; overflow: hidden; border: 1px solid var(--admin-border);">
                            <div class="preview-banner-left" style="width: 20%; background: var(--admin-bg); border-right: 1px solid var(--admin-border);"></div>
                            <div class="preview-content-center" style="flex: 1; background: var(--admin-card-bg); display: flex; align-items: center; justify-content: center; padding: 10px;">
                                <div class="preview-content-header" style="display: flex; align-items: center; gap: 10px;">
                                    <div class="preview-logo" style="width: 30px; height: 30px; background: var(--admin-primary); border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 16px; color: var(--admin-dark);">🎮</div>
                                    <div class="preview-text">
                                        <h4 style="color: var(--admin-text); font-size: 14px; font-weight: 600; margin: 0 0 2px 0;"><?php echo htmlspecialchars($theme['display_name']); ?></h4>
                                        <p style="color: var(--admin-text-muted); font-size: 12px; margin: 0;">Aperçu du contenu</p>
                                    </div>
                                </div>
                            </div>
                            <div class="preview-banner-right" style="width: 20%; background: var(--admin-bg); border-left: 1px solid var(--admin-border);"></div>
                        </div>
                    </div>
                    
                    <div class="theme-info" style="text-align: center; margin-bottom: 15px;">
                        <h3 style="margin: 0 0 10px 0; color: var(--admin-text); font-size: 18px; font-weight: 600;"><?php echo htmlspecialchars($theme['display_name']); ?></h3>
                        <?php if ($theme['name'] === $currentTheme['name']): ?>
                            <span class="theme-status active" style="display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; background: var(--admin-success); color: white; border: 1px solid var(--admin-success);">✅ Actif</span>
                        <?php else: ?>
                            <span class="theme-status inactive" style="display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; background: var(--admin-input-bg); color: var(--admin-text-muted); border: 1px solid var(--admin-border);">⏸️ Inactif</span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="theme-actions" style="text-align: center;">
                        <?php if ($theme['name'] !== $currentTheme['name']): ?>
                            <button class="btn" onclick="openThemeModal('<?php echo htmlspecialchars($theme['name']); ?>')">
                                Appliquer
                            </button>
                        <?php else: ?>
                            <button class="btn btn-secondary" disabled>
                                Thème actuel
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if (empty($themes)): ?>
            <div class="form-container" style="text-align: center;">
                <div style="font-size: 4rem; margin-bottom: var(--admin-spacing-md); color: var(--admin-primary);">🎨</div>
                <h3 style="color: var(--admin-text); margin-bottom: var(--admin-spacing-md);">Aucun thème trouvé</h3>
                <p style="color: var(--admin-text-muted);">Créez des dossiers dans <code style="background: var(--admin-input-bg); padding: 0.25rem 0.5rem; border-radius: 4px; font-family: 'Courier New', monospace; color: var(--admin-primary);">themes/</code> avec des fichiers <code style="background: var(--admin-input-bg); padding: 0.25rem 0.5rem; border-radius: 4px; font-family: 'Courier New', monospace; color: var(--admin-primary);">left.png</code> et <code style="background: var(--admin-input-bg); padding: 0.25rem 0.5rem; border-radius: 4px; font-family: 'Courier New', monospace; color: var(--admin-primary);">right.png</code></p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Modal d'application de thème -->
    <div id="themeModal" class="modal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.7);">
        <div class="modal-content" style="background-color: var(--admin-light); margin: 5% auto; padding: 0; border-radius: 10px; width: 90%; max-width: 500px; box-shadow: 0 8px 32px rgba(0,0,0,0.5); border: 1px solid var(--admin-border);">
            <div class="modal-header" style="display: flex; justify-content: space-between; align-items: center; padding: var(--admin-spacing-lg); border-bottom: 1px solid var(--admin-border);">
                <h2 style="margin: 0; color: var(--admin-primary);">🎨 Appliquer un thème</h2>
                <span class="close" onclick="closeThemeModal()" style="color: var(--admin-text-muted); font-size: 28px; font-weight: bold; cursor: pointer; line-height: 1; transition: color 0.3s ease;">&times;</span>
            </div>
            
            <form id="themeForm" method="POST" action="/admin/themes/apply" style="padding: var(--admin-spacing-lg);">
                <input type="hidden" id="themeName" name="theme_name" value="">
                
                <div class="form-group" style="margin-bottom: var(--admin-spacing-lg);">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--admin-text);">
                        <input type="checkbox" id="isPermanent" name="is_permanent" value="1" style="margin-right: 0.5rem; accent-color: var(--admin-primary);">
                        Thème permanent (devient le thème par défaut)
                    </label>
                    <small style="display: block; margin-top: 0.5rem; color: var(--admin-text-muted); font-size: 0.875rem;">⚠️ Attention : Cette action remplacera définitivement le thème par défaut</small>
                </div>
                
                <div class="form-group" id="expiresGroup" style="margin-bottom: var(--admin-spacing-lg);">
                    <label for="expiresAt" style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--admin-text);">Date de fin (optionnel)</label>
                    <input type="datetime-local" id="expiresAt" name="expires_at" 
                           min="<?php echo date('Y-m-d\TH:i'); ?>" style="width: 100%; padding: 0.75rem; border: 1px solid var(--admin-border); border-radius: 6px; font-size: 1rem; background: var(--admin-input-bg); color: var(--admin-text);">
                    <small style="display: block; margin-top: 0.5rem; color: var(--admin-text-muted); font-size: 0.875rem;">Si non renseigné, le thème sera permanent</small>
                </div>
                
                <div class="modal-actions" style="display: flex; gap: var(--admin-spacing-md); justify-content: flex-end; margin-top: var(--admin-spacing-lg); padding-top: var(--admin-spacing-lg); border-top: 1px solid var(--admin-border);">
                    <button type="button" class="btn btn-secondary" onclick="closeThemeModal()">
                        Annuler
                    </button>
                    <button type="submit" class="btn">
                        Appliquer le thème
                    </button>
                </div>
            </form>
        </div>
    </div>



<script>
function openThemeModal(themeName) {
    document.getElementById('themeName').value = themeName;
    document.getElementById('themeModal').style.display = 'block';
}

function closeThemeModal() {
    document.getElementById('themeModal').style.display = 'none';
    document.getElementById('themeForm').reset();
}

// Gérer l'affichage de la date d'expiration
document.getElementById('isPermanent').addEventListener('change', function() {
    const expiresGroup = document.getElementById('expiresGroup');
    if (this.checked) {
        expiresGroup.style.display = 'none';
    } else {
        expiresGroup.style.display = 'block';
    }
});

// Fermer la modal en cliquant à l'extérieur
window.onclick = function(event) {
    const modal = document.getElementById('themeModal');
    if (event.target === modal) {
        closeThemeModal();
    }
}
</script>
</body>
</html>
