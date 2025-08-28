<?php
/**
 * Vue de gestion des médias - Admin
 */
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des médias - GameNews Belgium</title>
    <link rel="stylesheet" href="/admin.css">

</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🖼️ Gestion des médias</h1>
            <p>Administration des fichiers multimédia de Belgium Vidéo Gaming</p>
        </div>
        
        <div class="actions" style="margin-bottom: var(--admin-spacing-lg);">
            <a href="/admin/dashboard" class="btn">← Retour au dashboard</a>
        </div>
        
        <div class="form-container">
            <h3 style="color: var(--admin-primary); margin-bottom: var(--admin-spacing-md);">📁 Zone d'upload</h3>
            <div class="upload-area" id="uploadArea" style="border: 2px dashed var(--admin-border); border-radius: 10px; padding: var(--admin-spacing-xl); text-align: center; cursor: pointer; transition: all 0.3s ease; background: var(--admin-input-bg); margin-bottom: var(--admin-spacing-md);">
                <div class="upload-icon" style="font-size: 48px; margin-bottom: var(--admin-spacing-md); color: var(--admin-primary);">📁</div>
                <div class="upload-text" style="font-size: 18px; font-weight: 500; margin-bottom: var(--admin-spacing-sm); color: var(--admin-text);">Glissez-déposez vos fichiers ici</div>
                <div class="upload-hint" style="color: var(--admin-text-muted); font-size: 14px;">ou cliquez pour sélectionner des fichiers</div>
                <input type="file" id="fileInput" class="file-input" multiple accept="image/*,video/*">
            </div>
            <div class="upload-hint" style="color: var(--admin-text-muted); font-size: 13px; text-align: center;">
                Formats acceptés : JPG, PNG, WebP, GIF, MP4, WebM<br>
                Taille maximale : 4MB par fichier
            </div>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?= $totalMedias ?></div>
                <div class="stat-label">Total des médias</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $currentPage ?></div>
                <div class="stat-label">Page actuelle</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $totalPages ?></div>
                <div class="stat-label">Pages totales</div>
            </div>
        </div>
        
        <div class="table-container">
            <h3 style="color: var(--admin-primary); margin-bottom: var(--admin-spacing-md);">📋 Liste des médias</h3>
            <div class="media-grid" id="mediaGrid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: var(--admin-spacing-md);">
                <?php foreach ($medias as $media): ?>
                <div class="media-card" data-id="<?= $media->getId() ?>" style="background: var(--admin-card-bg); border: 1px solid var(--admin-border); border-radius: 10px; overflow: hidden; transition: transform 0.2s ease;">
                    <div class="media-preview" style="height: 200px; background: var(--admin-input-bg); display: flex; align-items: center; justify-content: center; overflow: hidden;">
                        <?php if ($media->isImage()): ?>
                            <img src="<?= $media->getUrl() ?>" alt="<?= htmlspecialchars($media->getOriginalName()) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                        <?php else: ?>
                            <div class="icon" style="font-size: 48px; color: var(--admin-text-muted);">🎥</div>
                        <?php endif; ?>
                    </div>
                    <div class="media-info" style="padding: var(--admin-spacing-md);">
                        <div class="media-name" style="font-weight: 500; margin-bottom: var(--admin-spacing-sm); color: var(--admin-text); word-break: break-word;"><?= htmlspecialchars($media->getOriginalName()) ?></div>
                        <div class="media-details" style="font-size: 12px; color: var(--admin-text-muted); margin-bottom: var(--admin-spacing-md); line-height: 1.4;">
                            <?= $media->getFormattedSize() ?> • <?= $media->getMimeType() ?><br>
                            Ajouté le <?= date('d/m/Y H:i', strtotime($media->getCreatedAt())) ?>
                        </div>
                        <div class="media-actions" style="display: flex; gap: var(--admin-spacing-sm);">
                            <button class="btn btn-sm" onclick="copyUrl('<?= $media->getUrl() ?>')">Copier URL</button>
                            <button class="btn btn-sm" onclick="deleteMedia(<?= $media->getId() ?>)">Supprimer</button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <?php if ($totalPages > 1): ?>
        <div class="pagination" style="display: flex; justify-content: center; gap: var(--admin-spacing-sm); margin: var(--admin-spacing-lg) 0;">
            <?php if ($currentPage > 1): ?>
                <a href="?page=<?= $currentPage - 1 ?>" style="padding: 8px 12px; background: var(--admin-card-bg); border: 1px solid var(--admin-border); border-radius: 5px; color: var(--admin-text); text-decoration: none; transition: all 0.2s ease;">← Précédent</a>
            <?php endif; ?>
            
            <?php for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++): ?>
                <a href="?page=<?= $i ?>" <?= $i === $currentPage ? 'style="background: var(--admin-primary); color: var(--admin-dark); border-color: var(--admin-primary);"' : 'style="padding: 8px 12px; background: var(--admin-card-bg); border: 1px solid var(--admin-border); border-radius: 5px; color: var(--admin-text); text-decoration: none; transition: all 0.2s ease;"' ?>><?= $i ?></a>
            <?php endfor; ?>
            
            <?php if ($currentPage < $totalPages): ?>
                <a href="?page=<?= $currentPage + 1 ?>" style="padding: 8px 12px; background: var(--admin-card-bg); border: 1px solid var(--admin-border); border-radius: 5px; color: var(--admin-text); text-decoration: none; transition: all 0.2s ease;">Suivant →</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
        <div class="loading" id="loading" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0, 0, 0, 0.8); display: flex; flex-direction: column; align-items: center; justify-content: center; z-index: 1000; opacity: 0; visibility: hidden; transition: all 0.3s ease;">
            <div class="upload-icon" style="font-size: 48px; color: var(--admin-primary); margin-bottom: var(--admin-spacing-md);">⏳</div>
            <div class="upload-text" style="color: var(--admin-text); font-size: 18px;">Upload en cours...</div>
        </div>
    </div>
    
    <div class="toast" id="toast" style="position: fixed; top: 20px; right: 20px; padding: var(--admin-spacing-md); border-radius: 5px; color: var(--admin-text); z-index: 1001; opacity: 0; visibility: hidden; transition: all 0.3s ease; max-width: 300px;"></div>
    
    <script>
        // Variables globales
        const csrfToken = '<?= Auth::generateCsrfToken() ?>';
        
        // Éléments DOM
        const uploadArea = document.getElementById('uploadArea');
        const fileInput = document.getElementById('fileInput');
        const mediaGrid = document.getElementById('mediaGrid');
        const loading = document.getElementById('loading');
        const toast = document.getElementById('toast');
        
        // Gestion du drag & drop
        uploadArea.addEventListener('click', () => fileInput.click());
        
        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.classList.add('dragover');
        });
        
        uploadArea.addEventListener('dragleave', () => {
            uploadArea.classList.remove('dragover');
        });
        
        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
            const files = e.dataTransfer.files;
            handleFiles(files);
        });
        
        fileInput.addEventListener('change', (e) => {
            handleFiles(e.target.files);
        });
        
        // Gestion des fichiers
        function handleFiles(files) {
            Array.from(files).forEach(file => {
                uploadFile(file);
            });
        }
        
        // Upload d'un fichier
        async function uploadFile(file) {
            const formData = new FormData();
            formData.append('file', file);
            formData.append('csrf_token', csrfToken);
            
            loading.classList.add('show');
            
            try {
                const response = await fetch('/admin/media/upload', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showToast('Fichier uploadé avec succès !', 'success');
                    // Recharger la page pour afficher le nouveau média
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showToast(result.error || 'Erreur lors de l\'upload', 'error');
                }
            } catch (error) {
                showToast('Erreur de connexion', 'error');
            } finally {
                loading.classList.remove('show');
            }
        }
        
        // Supprimer un média
        async function deleteMedia(id) {
            if (!confirm('Êtes-vous sûr de vouloir supprimer ce média ?')) {
                return;
            }
            
            const formData = new FormData();
            formData.append('csrf_token', csrfToken);
            
            try {
                const response = await fetch(`/admin/media/delete/${id}`, {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showToast('Média supprimé avec succès !', 'success');
                    document.querySelector(`[data-id="${id}"]`).remove();
                } else {
                    showToast(result.error || 'Erreur lors de la suppression', 'error');
                }
            } catch (error) {
                showToast('Erreur de connexion', 'error');
            }
        }
        
        // Copier l'URL d'un média
        function copyUrl(url) {
            navigator.clipboard.writeText(url).then(() => {
                showToast('URL copiée dans le presse-papiers !', 'success');
            }).catch(() => {
                showToast('Erreur lors de la copie', 'error');
            });
        }
        
        // Afficher un toast
        function showToast(message, type = 'success') {
            toast.textContent = message;
            
            // Styles selon le type
            if (type === 'success') {
                toast.style.background = 'rgba(39, 174, 96, 0.9)';
                toast.style.border = '1px solid var(--admin-success)';
            } else if (type === 'error') {
                toast.style.background = 'rgba(231, 76, 60, 0.9)';
                toast.style.border = '1px solid var(--admin-secondary)';
            } else {
                toast.style.background = 'rgba(52, 152, 219, 0.9)';
                toast.style.border = '1px solid var(--admin-info)';
            }
            
            toast.classList.add('show');
            toast.style.opacity = '1';
            toast.style.visibility = 'visible';
            
            setTimeout(() => {
                toast.classList.remove('show');
                toast.style.opacity = '0';
                toast.style.visibility = 'hidden';
            }, 3000);
        }
    </script>
</body>
</html>
