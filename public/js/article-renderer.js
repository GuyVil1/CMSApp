/**
 * Article Renderer - Rendu des modules de contenu des articles
 * Belgium Vidéo Gaming
 */

class ArticleRenderer {
    constructor() {
        this.articleContent = document.getElementById('articleContent');
        this.modules = new Map();
        
        this.init();
    }
    
    init() {
        if (!this.articleContent || !window.articleData) {
            console.error('❌ ArticleRenderer: Données manquantes');
            return;
        }
        
        console.log('🚀 ArticleRenderer: Initialisation...');
        this.renderArticle();
    }
    
    /**
     * Rendre l'article complet
     */
    renderArticle() {
        try {
            const content = window.articleData.content;
            
            if (!content) {
                this.showError('Aucun contenu à afficher');
                return;
            }
            
            // Parser le contenu JSON
            let parsedContent;
            try {
                parsedContent = typeof content === 'string' ? JSON.parse(content) : content;
            } catch (e) {
                console.error('❌ Erreur parsing JSON:', e);
                this.showError('Erreur lors du chargement du contenu');
                return;
            }
            
            if (!Array.isArray(parsedContent)) {
                this.showError('Format de contenu invalide');
                return;
            }
            
            console.log(`📚 Rendu de ${parsedContent.length} modules`);
            this.renderModules(parsedContent);
            
        } catch (error) {
            console.error('❌ Erreur rendu article:', error);
            this.showError('Erreur lors du rendu de l\'article');
        }
    }
    
    /**
     * Rendre tous les modules
     */
    renderModules(modules) {
        this.articleContent.innerHTML = '';
        
        modules.forEach((moduleData, index) => {
            try {
                const moduleElement = this.renderModule(moduleData, index);
                if (moduleElement) {
                    this.articleContent.appendChild(moduleElement);
                }
            } catch (error) {
                console.error(`❌ Erreur rendu module ${index}:`, error);
                this.renderErrorModule(moduleData, index, error);
            }
        });
        
        console.log('✅ Rendu des modules terminé');
    }
    
    /**
     * Rendre un module individuel
     */
    renderModule(moduleData, index) {
        const { type, data, id } = moduleData;
        
        if (!type || !data) {
            console.warn(`⚠️ Module ${index}: données manquantes`, moduleData);
            return null;
        }
        
        console.log(`🔧 Rendu module ${type} (${index})`);
        
        switch (type) {
            case 'text':
                return this.renderTextModule(data, id);
            case 'image':
                return this.renderImageModule(data, id);
            case 'video':
                return this.renderVideoModule(data, id);
            case 'gallery':
                return this.renderGalleryModule(data, id);
            case 'table':
                return this.renderTableModule(data, id);
            case 'quote':
                return this.renderQuoteModule(data, id);
            case 'separator':
                return this.renderSeparatorModule(data, id);
            default:
                console.warn(`⚠️ Type de module non reconnu: ${type}`);
                return this.renderUnknownModule(moduleData, index);
        }
    }
    
    /**
     * Module texte
     */
    renderTextModule(data, id) {
        const module = document.createElement('div');
        module.className = 'content-module text-module';
        module.id = id || `text_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`;
        
        const { content, alignment = 'left' } = data;
        
        if (content) {
            // Convertir le contenu HTML en éléments DOM sécurisés
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = this.sanitizeHTML(content);
            
            // Appliquer l'alignement
            if (alignment !== 'left') {
                module.style.textAlign = alignment;
            }
            
            module.appendChild(tempDiv);
        }
        
        return module;
    }
    
    /**
     * Module image
     */
    renderImageModule(data, id) {
        const module = document.createElement('div');
        module.className = 'content-module image-module';
        module.id = id || `image_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`;
        
        const { image, caption, alignment = 'center', padding = {} } = data;
        
        if (image && image.filename) {
            const img = document.createElement('img');
            img.src = `/public/uploads/${image.filename}`;
            img.alt = caption || image.original_name || 'Image';
            img.loading = 'lazy';
            
            // Appliquer l'alignement
            if (alignment !== 'center') {
                module.style.textAlign = alignment;
            }
            
            // Appliquer le padding personnalisé
            if (padding && (padding.top || padding.right || padding.bottom || padding.left)) {
                const paddingStyle = this.getPaddingStyle(padding);
                module.style.padding = paddingStyle;
            }
            
            module.appendChild(img);
            
            // Ajouter la légende si elle existe
            if (caption) {
                const captionDiv = document.createElement('div');
                captionDiv.className = 'image-caption';
                captionDiv.textContent = caption;
                module.appendChild(captionDiv);
            }
        }
        
        return module;
    }
    
    /**
     * Module vidéo
     */
    renderVideoModule(data, id) {
        const module = document.createElement('div');
        module.className = 'content-module video-module';
        module.id = id || `video_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`;
        
        const { video, title, description, alignment = 'center' } = data;
        
        if (video && video.url) {
            const container = document.createElement('div');
            container.className = 'video-container';
            
            if (alignment !== 'center') {
                container.style.textAlign = alignment;
            }
            
            if (video.type === 'youtube' || video.type === 'vimeo') {
                // Vidéo externe (YouTube, Vimeo)
                const iframe = document.createElement('iframe');
                iframe.src = this.getEmbedUrl(video.url, video.type);
                iframe.width = '100%';
                iframe.height = '400';
                iframe.frameBorder = '0';
                iframe.allowFullscreen = true;
                iframe.title = title || 'Vidéo';
                
                container.appendChild(iframe);
            } else if (video.type === 'file') {
                // Vidéo locale
                const videoElement = document.createElement('video');
                videoElement.controls = true;
                videoElement.width = '100%';
                videoElement.height = '400';
                videoElement.src = `/public/uploads/${video.filename}`;
                videoElement.title = title || 'Vidéo';
                
                container.appendChild(videoElement);
            }
            
            // Ajouter le titre si il existe
            if (title) {
                const titleDiv = document.createElement('div');
                titleDiv.className = 'video-title';
                titleDiv.textContent = title;
                container.appendChild(titleDiv);
            }
            
            // Ajouter la description si elle existe
            if (description) {
                const descDiv = document.createElement('div');
                descDiv.className = 'video-description';
                descDiv.textContent = description;
                container.appendChild(descDiv);
            }
            
            module.appendChild(container);
        }
        
        return module;
    }
    
    /**
     * Module galerie
     */
    renderGalleryModule(data, id) {
        const module = document.createElement('div');
        module.className = 'content-module gallery-module';
        module.id = id || `gallery_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`;
        
        const { images, columns = 3, spacing = '15px' } = data;
        
        if (images && Array.isArray(images) && images.length > 0) {
            const grid = document.createElement('div');
            grid.className = 'gallery-grid';
            grid.style.gridTemplateColumns = `repeat(${columns}, 1fr)`;
            grid.style.gap = spacing;
            
            images.forEach(image => {
                if (image && image.filename) {
                    const item = document.createElement('div');
                    item.className = 'gallery-item';
                    
                    const img = document.createElement('img');
                    img.src = `/public/uploads/${image.filename}`;
                    img.alt = image.original_name || 'Image galerie';
                    img.loading = 'lazy';
                    
                    item.appendChild(img);
                    grid.appendChild(item);
                }
            });
            
            module.appendChild(grid);
        }
        
        return module;
    }
    
    /**
     * Module tableau
     */
    renderTableModule(data, id) {
        const module = document.createElement('div');
        module.className = 'content-module table-module';
        module.id = id || `table_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`;
        
        const { headers, rows, striped = true, bordered = true } = data;
        
        if (headers && Array.isArray(headers) && rows && Array.isArray(rows)) {
            const table = document.createElement('table');
            table.style.width = '100%';
            table.style.borderCollapse = 'collapse';
            
            if (striped) table.classList.add('striped');
            if (bordered) table.classList.add('bordered');
            
            // En-têtes
            const thead = document.createElement('thead');
            const headerRow = document.createElement('tr');
            
            headers.forEach(header => {
                const th = document.createElement('th');
                th.textContent = header;
                th.style.padding = '12px';
                th.style.borderBottom = '2px solid #ddd';
                th.style.backgroundColor = '#f8f9fa';
                headerRow.appendChild(th);
            });
            
            thead.appendChild(headerRow);
            table.appendChild(thead);
            
            // Corps
            const tbody = document.createElement('tbody');
            
            rows.forEach((row, rowIndex) => {
                const tr = document.createElement('tr');
                if (striped && rowIndex % 2 === 1) {
                    tr.style.backgroundColor = '#f8f9fa';
                }
                
                row.forEach(cell => {
                    const td = document.createElement('td');
                    td.textContent = cell;
                    td.style.padding = '12px';
                    td.style.borderBottom = '1px solid #ddd';
                    tr.appendChild(td);
                });
                
                tbody.appendChild(tr);
            });
            
            table.appendChild(tbody);
            module.appendChild(table);
        }
        
        return module;
    }
    
    /**
     * Module citation
     */
    renderQuoteModule(data, id) {
        const module = document.createElement('div');
        module.className = 'content-module quote-module';
        module.id = id || `quote_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`;
        
        const { text, author, source } = data;
        
        if (text) {
            const blockquote = document.createElement('blockquote');
            blockquote.textContent = text;
            
            module.appendChild(blockquote);
            
            if (author || source) {
                const footer = document.createElement('footer');
                footer.style.marginTop = '15px';
                footer.style.fontSize = '0.9rem';
                footer.style.color = '#666';
                
                if (author) {
                    footer.innerHTML += `<strong>— ${author}</strong>`;
                }
                
                if (source) {
                    if (author) footer.innerHTML += ' ';
                    footer.innerHTML += `<cite>${source}</cite>`;
                }
                
                module.appendChild(footer);
            }
        }
        
        return module;
    }
    
    /**
     * Module séparateur
     */
    renderSeparatorModule(data, id) {
        const module = document.createElement('div');
        module.className = 'content-module separator-module';
        module.id = id || `separator_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`;
        
        const { style = 'line', color = '#ddd', thickness = '1px', margin = '30px' } = data;
        
        const separator = document.createElement('hr');
        separator.style.border = 'none';
        separator.style.height = thickness;
        separator.style.backgroundColor = color;
        separator.style.margin = margin;
        
        if (style === 'dashed') {
            separator.style.borderTop = `${thickness} dashed ${color}`;
        } else if (style === 'dotted') {
            separator.style.borderTop = `${thickness} dotted ${color}`;
        }
        
        module.appendChild(separator);
        return module;
    }
    
    /**
     * Module inconnu
     */
    renderUnknownModule(moduleData, index) {
        const module = document.createElement('div');
        module.className = 'content-module unknown-module';
        module.style.border = '2px dashed #ff6b6b';
        module.style.backgroundColor = '#fff5f5';
        module.style.color = '#d63031';
        module.style.textAlign = 'center';
        module.style.padding = '20px';
        
        module.innerHTML = `
            <div style="font-size: 1.2rem; margin-bottom: 10px;">⚠️</div>
            <div><strong>Type de module non reconnu:</strong> ${moduleData.type || 'inconnu'}</div>
            <div style="font-size: 0.9rem; margin-top: 10px;">Index: ${index}</div>
        `;
        
        return module;
    }
    
    /**
     * Module d'erreur
     */
    renderErrorModule(moduleData, index, error) {
        const module = document.createElement('div');
        module.className = 'content-module error-module';
        module.style.border = '2px solid #e74c3c';
        module.style.backgroundColor = '#fdf2f2';
        module.style.color = '#c53030';
        module.style.textAlign = 'center';
        module.style.padding = '20px';
        
        module.innerHTML = `
            <div style="font-size: 1.2rem; margin-bottom: 10px;">❌</div>
            <div><strong>Erreur lors du rendu du module</strong></div>
            <div style="font-size: 0.9rem; margin-top: 10px;">
                Type: ${moduleData.type || 'inconnu'}<br>
                Index: ${index}<br>
                Erreur: ${error.message}
            </div>
        `;
        
        return module;
    }
    
    /**
     * Afficher une erreur générale
     */
    showError(message) {
        this.articleContent.innerHTML = `
            <div class="error-message" style="text-align: center; padding: 60px 20px; color: #e74c3c;">
                <div style="font-size: 2rem; margin-bottom: 20px;">❌</div>
                <div style="font-size: 1.2rem;">${message}</div>
            </div>
        `;
    }
    
    /**
     * Obtenir l'URL d'intégration pour les vidéos
     */
    getEmbedUrl(url, type) {
        if (type === 'youtube') {
            const videoId = this.extractYouTubeId(url);
            return videoId ? `https://www.youtube.com/embed/${videoId}` : url;
        } else if (type === 'vimeo') {
            const videoId = this.extractVimeoId(url);
            return videoId ? `https://player.vimeo.com/video/${videoId}` : url;
        }
        return url;
    }
    
    /**
     * Extraire l'ID YouTube d'une URL
     */
    extractYouTubeId(url) {
        const regex = /(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/;
        const match = url.match(regex);
        return match ? match[1] : null;
    }
    
    /**
     * Extraire l'ID Vimeo d'une URL
     */
    extractVimeoId(url) {
        const regex = /vimeo\.com\/([0-9]+)/;
        const match = url.match(regex);
        return match ? match[1] : null;
    }
    
    /**
     * Obtenir le style de padding
     */
    getPaddingStyle(padding) {
        const { top = 0, right = 0, bottom = 0, left = 0 } = padding;
        return `${top}px ${right}px ${bottom}px ${left}px`;
    }
    
    /**
     * Nettoyer le HTML pour la sécurité
     */
    sanitizeHTML(html) {
        // Supprimer les scripts et styles dangereux
        return html
            .replace(/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi, '')
            .replace(/<style\b[^<]*(?:(?!<\/style>)<[^<]*)*<\/style>/gi, '')
            .replace(/on\w+\s*=/gi, 'data-removed=')
            .replace(/javascript:/gi, 'data-removed:');
    }
}

// Initialiser le renderer quand le DOM est prêt
document.addEventListener('DOMContentLoaded', () => {
    new ArticleRenderer();
});
