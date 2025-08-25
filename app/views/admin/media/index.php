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
    <title>Gestion des médias - Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
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
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            min-height: 100vh;
            color: var(--text);
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--border);
        }
        
        .header h1 {
            font-size: 2rem;
            color: var(--belgium-yellow);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            background: var(--belgium-red);
            color: white;
            text-decoration: none;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn:hover {
            background: #cc0000;
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background: transparent;
            border: 2px solid var(--belgium-yellow);
            color: var(--belgium-yellow);
        }
        
        .btn-secondary:hover {
            background: var(--belgium-yellow);
            color: var(--belgium-black);
        }
        
        .upload-section {
            background: rgba(255, 255, 255, 0.05);
            border: 2px dashed var(--border);
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            text-align: center;
        }
        
        .upload-area {
            border: 2px dashed var(--belgium-yellow);
            border-radius: 10px;
            padding: 3rem;
            margin-bottom: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .upload-area:hover {
            border-color: var(--belgium-red);
            background: rgba(255, 0, 0, 0.1);
        }
        
        .upload-area.dragover {
            border-color: var(--success);
            background: rgba(68, 255, 68, 0.1);
        }
        
        .upload-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        
        .upload-text {
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
        }
        
        .upload-hint {
            color: var(--text-muted);
            font-size: 0.9rem;
        }
        
        .file-input {
            display: none;
        }
        
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 1.5rem;
            text-align: center;
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--belgium-yellow);
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            color: var(--text-muted);
            font-size: 0.9rem;
        }
        
        .media-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1.5rem;
        }
        
        .media-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border);
            border-radius: 10px;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .media-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
        }
        
        .media-preview {
            position: relative;
            height: 200px;
            background: var(--secondary);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        
        .media-preview img {
            max-width: 100%;
            max-height: 100%;
            object-fit: cover;
        }
        
        .media-preview .icon {
            font-size: 3rem;
            color: var(--text-muted);
        }
        
        .media-info {
            padding: 1rem;
        }
        
        .media-name {
            font-weight: 600;
            margin-bottom: 0.5rem;
            word-break: break-word;
        }
        
        .media-details {
            font-size: 0.8rem;
            color: var(--text-muted);
            margin-bottom: 1rem;
        }
        
        .media-actions {
            display: flex;
            gap: 0.5rem;
        }
        
        .btn-small {
            padding: 0.5rem 1rem;
            font-size: 0.8rem;
        }
        
        .btn-danger {
            background: var(--error);
        }
        
        .btn-danger:hover {
            background: #cc0000;
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 1rem;
            margin-top: 2rem;
        }
        
        .pagination a {
            padding: 0.5rem 1rem;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid var(--border);
            border-radius: 5px;
            color: var(--text);
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .pagination a:hover,
        .pagination a.active {
            background: var(--belgium-red);
            border-color: var(--belgium-red);
        }
        
        .loading {
            display: none;
            text-align: center;
            padding: 2rem;
        }
        
        .loading.show {
            display: block;
        }
        
        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 1rem 1.5rem;
            border-radius: 8px;
            color: white;
            font-weight: 600;
            z-index: 1000;
            transform: translateX(100%);
            transition: transform 0.3s ease;
        }
        
        .toast.show {
            transform: translateX(0);
        }
        
        .toast.success {
            background: var(--success);
        }
        
        .toast.error {
            background: var(--error);
        }
        
        .toast.warning {
            background: var(--warning);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>
                <span>🖼️</span>
                Gestion des médias
            </h1>
            <div>
                <a href="/admin" class="btn btn-secondary">← Retour au dashboard</a>
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
