/**
 * Lazy Loading intelligent pour les images
 * Améliore les performances en chargeant les images seulement quand nécessaire
 */

class LazyLoader {
    constructor(options = {}) {
        this.options = {
            root: null,
            rootMargin: '50px',
            threshold: 0.1,
            placeholder: 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMSIgaGVpZ2h0PSIxIiB2aWV3Qm94PSIwIDAgMSAxIiBmaWxsPSJub25lIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPjxyZWN0IHdpZHRoPSIxIiBoZWlnaHQ9IjEiIGZpbGw9IiNmMGYwZjAiLz48L3N2Zz4=',
            fadeIn: true,
            ...options
        };
        
        this.observer = null;
        this.images = [];
        this.loadedCount = 0;
        this.totalCount = 0;
        
        this.init();
    }
    
    init() {
        // Vérifier le support de l'Intersection Observer
        if (!('IntersectionObserver' in window)) {
            // Fallback pour les navigateurs non supportés
            this.loadAllImages();
            return;
        }
        
        this.observer = new IntersectionObserver(
            this.handleIntersection.bind(this),
            {
                root: this.options.root,
                rootMargin: this.options.rootMargin,
                threshold: this.options.threshold
            }
        );
        
        this.scanImages();
        this.bindEvents();
    }
    
    scanImages() {
        // Scanner toutes les images avec data-lazy
        const lazyImages = document.querySelectorAll('img[data-lazy]');
        this.totalCount = lazyImages.length;
        
        lazyImages.forEach(img => {
            this.images.push(img);
            this.observer.observe(img);
            
            // Ajouter le placeholder
            if (this.options.placeholder) {
                img.src = this.options.placeholder;
                img.classList.add('lazy-loading');
            }
        });
        
        console.log(`LazyLoader: ${this.totalCount} images détectées`);
    }
    
    handleIntersection(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                this.loadImage(entry.target);
                this.observer.unobserve(entry.target);
            }
        });
    }
    
    loadImage(img) {
        const src = img.dataset.lazy;
        if (!src) return;
        
        // Créer une nouvelle image pour précharger
        const imageLoader = new Image();
        
        imageLoader.onload = () => {
            // Image chargée avec succès
            img.src = src;
            img.classList.remove('lazy-loading');
            img.classList.add('lazy-loaded');
            
            if (this.options.fadeIn) {
                img.style.opacity = '0';
                img.style.transition = 'opacity 0.3s ease-in-out';
                
                // Forcer le reflow
                img.offsetHeight;
                
                img.style.opacity = '1';
            }
            
            this.loadedCount++;
            this.onImageLoaded(img);
        };
        
        imageLoader.onerror = () => {
            // Erreur de chargement
            img.classList.remove('lazy-loading');
            img.classList.add('lazy-error');
            img.src = '/assets/images/default-article.jpg'; // Image de fallback
            
            this.loadedCount++;
            this.onImageError(img);
        };
        
        // Démarrer le chargement
        imageLoader.src = src;
    }
    
    onImageLoaded(img) {
        // Callback personnalisé pour image chargée
        const event = new CustomEvent('lazyImageLoaded', {
            detail: { img, loadedCount: this.loadedCount, totalCount: this.totalCount }
        });
        document.dispatchEvent(event);
        
        // Vérifier si toutes les images sont chargées
        if (this.loadedCount >= this.totalCount) {
            this.onAllImagesLoaded();
        }
    }
    
    onImageError(img) {
        // Callback personnalisé pour erreur de chargement
        const event = new CustomEvent('lazyImageError', {
            detail: { img, loadedCount: this.loadedCount, totalCount: this.totalCount }
        });
        document.dispatchEvent(event);
    }
    
    onAllImagesLoaded() {
        // Callback quand toutes les images sont chargées
        const event = new CustomEvent('lazyAllImagesLoaded', {
            detail: { loadedCount: this.loadedCount, totalCount: this.totalCount }
        });
        document.dispatchEvent(event);
        
        console.log(`LazyLoader: Toutes les images chargées (${this.loadedCount}/${this.totalCount})`);
    }
    
    loadAllImages() {
        // Fallback pour les navigateurs non supportés
        const lazyImages = document.querySelectorAll('img[data-lazy]');
        lazyImages.forEach(img => {
            img.src = img.dataset.lazy;
            img.classList.add('lazy-loaded');
        });
    }
    
    bindEvents() {
        // Re-scanner les images après un changement de contenu
        document.addEventListener('contentChanged', () => {
            this.scanImages();
        });
        
        // Re-scanner après un scroll (pour les images ajoutées dynamiquement)
        let scrollTimeout;
        window.addEventListener('scroll', () => {
            clearTimeout(scrollTimeout);
            scrollTimeout = setTimeout(() => {
                this.scanImages();
            }, 100);
        });
    }
    
    // Méthodes publiques
    refresh() {
        this.images = [];
        this.loadedCount = 0;
        this.totalCount = 0;
        
        if (this.observer) {
            this.observer.disconnect();
        }
        
        this.scanImages();
    }
    
    destroy() {
        if (this.observer) {
            this.observer.disconnect();
        }
        this.images = [];
    }
    
    getStats() {
        return {
            total: this.totalCount,
            loaded: this.loadedCount,
            remaining: this.totalCount - this.loadedCount,
            percentage: this.totalCount > 0 ? Math.round((this.loadedCount / this.totalCount) * 100) : 0
        };
    }
}

// Auto-initialisation
document.addEventListener('DOMContentLoaded', () => {
    window.lazyLoader = new LazyLoader({
        rootMargin: '100px', // Commencer à charger 100px avant que l'image soit visible
        threshold: 0.1,
        fadeIn: true
    });
    
    // Écouter les événements pour les statistiques
    document.addEventListener('lazyImageLoaded', (e) => {
        const { loadedCount, totalCount } = e.detail;
        if (loadedCount % 10 === 0 || loadedCount === totalCount) {
            console.log(`LazyLoader: ${loadedCount}/${totalCount} images chargées`);
        }
    });
    
    document.addEventListener('lazyAllImagesLoaded', (e) => {
        const { loadedCount, totalCount } = e.detail;
        console.log(`🎉 LazyLoader: Toutes les images chargées! (${loadedCount}/${totalCount})`);
    });
});

// Export pour utilisation dans d'autres modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = LazyLoader;
}
