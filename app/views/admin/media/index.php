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
        <div class="header-nav">
            <h1>
                <span>🖼️</span>
                Gestion des médias
            </h1>
            <div class="actions">
                <a href="/admin/dashboard" class="btn btn-warning">← Retour au dashboard</a>
            </div>
        </div>
        
        <div class="upload-section">
            <div class="upload-area" id="uploadArea">
                <div class="upload-icon">📁</div>
                <div class="upload-text">Glissez-déposez vos fichiers ici</div>
                <div class="upload-hint">ou cliquez pour sélectionner des fichiers</div>
                <input type="file" id="fileInput" class="file-input" multiple accept="image/*,video/*">
            </div>
            <div class="upload-hint">
                Formats acceptés : JPG, PNG, WebP, GIF, MP4, WebM<br>
                Taille maximale : 4MB par fichier
            </div>
        </div>
        
        <div class="stats">
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
        
        <div class="media-grid" id="mediaGrid">
            <?php foreach ($medias as $media): ?>
            <div class="media-card" data-id="<?= $media->getId() ?>">
                <div class="media-preview">
                    <?php if ($media->isImage()): ?>
                        <img src="<?= $media->getUrl() ?>" alt="<?= htmlspecialchars($media->getOriginalName()) ?>">
                    <?php else: ?>
                        <div class="icon">🎥</div>
                    <?php endif; ?>
                </div>
                <div class="media-info">
                    <div class="media-name"><?= htmlspecialchars($media->getOriginalName()) ?></div>
                    <div class="media-details">
                        <?= $media->getFormattedSize() ?> • <?= $media->getMimeType() ?><br>
                        Ajouté le <?= date('d/m/Y H:i', strtotime($media->getCreatedAt())) ?>
                    </div>
                    <div class="media-actions">
                        <button class="btn btn-small" onclick="copyUrl('<?= $media->getUrl() ?>')">Copier URL</button>
                        <button class="btn btn-small btn-danger" onclick="deleteMedia(<?= $media->getId() ?>)">Supprimer</button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php if ($currentPage > 1): ?>
                <a href="?page=<?= $currentPage - 1 ?>">← Précédent</a>
            <?php endif; ?>
            
            <?php for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++): ?>
                <a href="?page=<?= $i ?>" <?= $i === $currentPage ? 'class="active"' : '' ?>><?= $i ?></a>
            <?php endfor; ?>
            
            <?php if ($currentPage < $totalPages): ?>
                <a href="?page=<?= $currentPage + 1 ?>">Suivant →</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
        <div class="loading" id="loading">
            <div class="upload-icon">⏳</div>
            <div class="upload-text">Upload en cours...</div>
        </div>
    </div>
    
    <div class="toast" id="toast"></div>
    
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
            toast.className = `toast ${type}`;
            toast.classList.add('show');
            
            setTimeout(() => {
                toast.classList.remove('show');
            }, 3000);
        }
    </script>
</body>
</html>
