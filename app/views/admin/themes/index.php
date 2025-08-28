<?php
/**
 * Interface de gestion des thèmes
 */
?>

<div class="admin-container">
    <div class="admin-header">
        <div class="header-left">
            <div class="header-icon">🎨</div>
            <div class="header-text">
                <h1>Gestion des thèmes</h1>
                <p>Gérez l'apparence de votre site avec des thèmes personnalisés</p>
            </div>
        </div>
        <a href="/admin/dashboard" class="back-btn">← Retour au dashboard</a>
    </div>

    <div class="themes-grid">
        <?php foreach ($themes as $theme): ?>
            <div class="theme-card <?php echo $theme['name'] === $currentTheme['name'] ? 'active' : ''; ?>">
                <div class="theme-preview">
                    <div class="theme-preview-header">
                        <div class="preview-title">Aperçu du thème</div>
                    </div>
                    <div class="theme-preview-content">
                        <div class="preview-banner-left"></div>
                        <div class="preview-content-center">
                            <div class="preview-content-header">
                                <div class="preview-logo">🎮</div>
                                <div class="preview-text">
                                    <h4><?php echo htmlspecialchars($theme['display_name']); ?></h4>
                                    <p>Aperçu du contenu</p>
                                </div>
                            </div>
                        </div>
                        <div class="preview-banner-right"></div>
                    </div>
                </div>
                
                <div class="theme-info">
                    <h3><?php echo htmlspecialchars($theme['display_name']); ?></h3>
                    <?php if ($theme['name'] === $currentTheme['name']): ?>
                        <span class="theme-status active">✅ Actif</span>
                    <?php else: ?>
                        <span class="theme-status inactive">⏸️ Inactif</span>
                    <?php endif; ?>
                </div>
                
                <div class="theme-actions">
                    <?php if ($theme['name'] !== $currentTheme['name']): ?>
                        <button class="btn btn-primary" onclick="openThemeModal('<?php echo htmlspecialchars($theme['name']); ?>')">
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
        <div class="no-themes">
            <div class="no-themes-icon">🎨</div>
            <h3>Aucun thème trouvé</h3>
            <p>Créez des dossiers dans <code>themes/</code> avec des fichiers <code>left.png</code> et <code>right.png</code></p>
        </div>
    <?php endif; ?>
</div>

<!-- Modal d'application de thème -->
<div id="themeModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>🎨 Appliquer un thème</h2>
            <span class="close" onclick="closeThemeModal()">&times;</span>
        </div>
        
        <form id="themeForm" method="POST" action="/admin/themes/apply">
            <input type="hidden" id="themeName" name="theme_name" value="">
            
            <div class="form-group">
                <label>
                    <input type="checkbox" id="isPermanent" name="is_permanent" value="1">
                    Thème permanent (devient le thème par défaut)
                </label>
                <small>⚠️ Attention : Cette action remplacera définitivement le thème par défaut</small>
            </div>
            
            <div class="form-group" id="expiresGroup">
                <label for="expiresAt">Date de fin (optionnel)</label>
                <input type="datetime-local" id="expiresAt" name="expires_at" 
                       min="<?php echo date('Y-m-d\TH:i'); ?>">
                <small>Si non renseigné, le thème sera permanent</small>
            </div>
            
            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" onclick="closeThemeModal()">
                    Annuler
                </button>
                <button type="submit" class="btn btn-primary">
                    Appliquer le thème
                </button>
            </div>
        </form>
    </div>
</div>

<style>
:root {
    --belgium-red: #CC0000;
    --belgium-yellow: #E6B800;
    --belgium-black: #000000;
    --admin-bg: #0f0f0f;
    --admin-card: #1a1a1a;
    --admin-border: #333333;
    --admin-text: #ffffff;
    --admin-text-muted: #cccccc;
    --admin-accent: #E6B800;
    --gradient-bg: linear-gradient(135deg, #0f0f0f 0%, #1a1a1a 50%, #2d2d2d 100%);
    --card-gradient: linear-gradient(145deg, #1a1a1a 0%, #2d2d2d 100%);
    --yellow-gradient: linear-gradient(135deg, #E6B800 0%, #FFD700 100%);
    --red-gradient: linear-gradient(135deg, #CC0000 0%, #FF0000 100%);
}

.admin-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
    background: var(--admin-bg);
    min-height: 100vh;
}

.admin-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 3rem;
    padding: 2rem;
    background: var(--admin-card);
    border-radius: 12px;
    border: 1px solid var(--admin-border);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.header-left {
    display: flex;
    align-items: center;
    gap: 15px;
}

.header-icon {
    width: 48px;
    height: 48px;
    background: var(--belgium-yellow);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: var(--belgium-black);
}

.header-text h1 {
    color: var(--admin-text);
    font-size: 28px;
    font-weight: bold;
    margin-bottom: 5px;
}

.header-text p {
    color: var(--admin-text-muted);
    font-size: 14px;
}

.back-btn {
    background: var(--belgium-yellow);
    color: var(--belgium-black);
    border: none;
    padding: 12px 20px;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.back-btn:hover {
    background: #d4a700;
    transform: translateY(-2px);
}

.themes-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 2rem;
    margin-bottom: 2rem;
}

.theme-card {
    background: var(--admin-card);
    border: 2px solid var(--admin-border);
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.theme-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.15);
}

.theme-card.active {
    border-color: var(--belgium-yellow);
    box-shadow: 0 0 0 3px rgba(230, 184, 0, 0.2);
}

.theme-preview {
    background: var(--admin-card);
    border-bottom: 1px solid var(--admin-border);
}

.theme-preview-header {
    padding: 1rem;
    background: var(--admin-bg);
    border-bottom: 1px solid var(--admin-border);
}

.preview-title {
    color: var(--admin-text-muted);
    font-size: 14px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.theme-preview-content {
    display: flex;
    height: 120px;
    position: relative;
}

.preview-banner-left,
.preview-banner-right {
    width: 15%;
    background: var(--admin-bg);
    border: 1px solid var(--admin-border);
    position: relative;
}

.preview-content-center {
    flex: 1;
    background: var(--admin-card);
    display: flex;
    align-items: center;
    justify-content: center;
    border-left: 1px solid var(--admin-border);
    border-right: 1px solid var(--admin-border);
}

.preview-content-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
}

.preview-logo {
    width: 40px;
    height: 40px;
    background: var(--belgium-yellow);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    color: var(--belgium-black);
}

.preview-text h4 {
    color: var(--admin-text);
    font-size: 16px;
    font-weight: 600;
    margin: 0 0 0.25rem 0;
}

.preview-text p {
    color: var(--admin-text-muted);
    font-size: 14px;
    margin: 0;
}

.theme-info {
    padding: 1.5rem;
    text-align: center;
    background: var(--admin-card);
}

.theme-info h3 {
    margin: 0 0 0.5rem 0;
    color: var(--admin-text);
    font-size: 20px;
    font-weight: 600;
}

.theme-status {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 14px;
    font-weight: 600;
    background: var(--admin-bg);
    border: 1px solid var(--admin-border);
}

.theme-status.active {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.theme-status.inactive {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.theme-actions {
    padding: 0 1.5rem 1.5rem 1.5rem;
    text-align: center;
    background: var(--admin-card);
}

.no-themes {
    text-align: center;
    padding: 4rem 2rem;
    background: var(--admin-card);
    border: 2px dashed var(--admin-border);
    border-radius: 12px;
}

.no-themes-icon {
    font-size: 4rem;
    margin-bottom: 1rem;
}

.no-themes h3 {
    color: var(--admin-text);
    margin-bottom: 1rem;
}

.no-themes p {
    color: var(--admin-text-muted);
}

.no-themes code {
    background: var(--admin-bg);
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-family: 'Courier New', monospace;
    color: var(--belgium-yellow);
}

/* Modal */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.7);
}

.modal-content {
    background-color: var(--admin-card);
    margin: 5% auto;
    padding: 0;
    border-radius: 12px;
    width: 90%;
    max-width: 500px;
    box-shadow: 0 8px 32px rgba(0,0,0,0.5);
    border: 1px solid var(--admin-border);
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem;
    border-bottom: 1px solid var(--admin-border);
}

.modal-header h2 {
    margin: 0;
    color: var(--admin-text);
}

.close {
    color: var(--admin-text-muted);
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
    line-height: 1;
    transition: color 0.3s ease;
}

.close:hover {
    color: var(--admin-text);
}

#themeForm {
    padding: 1.5rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: var(--admin-text);
}

.form-group input[type="checkbox"] {
    margin-right: 0.5rem;
    accent-color: var(--belgium-yellow);
}

.form-group input[type="datetime-local"] {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid var(--admin-border);
    border-radius: 6px;
    font-size: 1rem;
    background: var(--admin-bg);
    color: var(--admin-text);
}

.form-group small {
    display: block;
    margin-top: 0.5rem;
    color: var(--admin-text-muted);
    font-size: 0.875rem;
}

.modal-actions {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
    margin-top: 2rem;
}

.btn {
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 6px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-block;
    text-align: center;
}

.btn-primary {
    background: var(--belgium-red);
    color: white;
}

.btn-primary:hover {
    background: #990000;
    transform: translateY(-2px);
}

.btn-secondary {
    background: var(--admin-border);
    color: var(--admin-text);
}

.btn-secondary:hover {
    background: #505050;
    transform: translateY(-2px);
}

.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none !important;
}

@media (max-width: 768px) {
    .admin-header {
        flex-direction: column;
        gap: 20px;
        text-align: center;
    }
    
    .themes-grid {
        grid-template-columns: 1fr;
    }
    
    .modal-actions {
        flex-direction: column;
    }
}
</style>

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
