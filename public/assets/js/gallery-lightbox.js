/**
 * Gestionnaire de lightbox pour les galeries
 */
class GalleryLightbox {
    constructor() {
        this.modal = null;
        this.modalImg = null;
        this.init();
    }

    init() {
        try {
            // Créer le modal lightbox s'il n'existe pas
            this.createLightboxModal();
            
            // Vérifier que le modal a été créé avant de continuer
            if (!this.modal || !this.modalImg) {
                console.warn('Modal lightbox non créé, réessai dans 100ms...');
                setTimeout(() => this.init(), 100);
                return;
            }
            
            // Ajouter les événements sur les images de galerie
            this.bindGalleryEvents();
            
            // Ajouter les événements de fermeture
            this.bindCloseEvents();
            
            console.log('GalleryLightbox initialisé avec succès');
        } catch (error) {
            console.error('Erreur lors de l\'initialisation de GalleryLightbox:', error);
        }
    }

    createLightboxModal() {
        try {
            // Vérifier si le modal existe déjà
            if (document.querySelector('.lightbox-modal')) {
                this.modal = document.querySelector('.lightbox-modal');
                this.modalImg = this.modal.querySelector('img');
                return;
            }

            const modal = document.createElement('div');
            modal.className = 'lightbox-modal';
            modal.innerHTML = `
                <div class="lightbox-close">×</div>
                <img src="" alt="Image agrandie">
            `;
            
            document.body.appendChild(modal);
            this.modal = modal;
            this.modalImg = modal.querySelector('img');
            
            console.log('Modal lightbox créé');
        } catch (error) {
            console.error('Erreur lors de la création du modal:', error);
        }
    }

    bindGalleryEvents() {
        try {
            // Utiliser la délégation d'événements pour les images de galerie
            document.addEventListener('click', (e) => {
                const galleryItem = e.target.closest('.gallery-item');
                if (galleryItem) {
                    e.preventDefault();
                    this.openLightbox(galleryItem);
                }
            });
            
            console.log('Événements de galerie liés');
        } catch (error) {
            console.error('Erreur lors de la liaison des événements de galerie:', error);
        }
    }

    bindCloseEvents() {
        try {
            if (!this.modal) {
                console.error('Modal non disponible pour les événements de fermeture');
                return;
            }
            
            // Fermer en cliquant sur le modal ou le bouton de fermeture
            this.modal.addEventListener('click', (e) => {
                if (e.target === this.modal || e.target.classList.contains('lightbox-close')) {
                    this.closeLightbox();
                }
            });

            // Fermer avec la touche Échap
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    this.closeLightbox();
                }
            });
            
            console.log('Événements de fermeture liés');
        } catch (error) {
            console.error('Erreur lors de la liaison des événements de fermeture:', error);
        }
    }

    openLightbox(galleryItem) {
        try {
            if (!this.modal || !this.modalImg) {
                console.error('Modal non disponible pour ouvrir le lightbox');
                return;
            }
            
            const img = galleryItem.querySelector('img');
            if (!img) {
                console.warn('Aucune image trouvée dans l\'élément de galerie');
                return;
            }

            this.modalImg.src = img.src;
            this.modalImg.alt = img.alt || 'Image agrandie';
            this.modal.classList.add('active');
            
            // Empêcher le défilement de la page
            document.body.style.overflow = 'hidden';
            
            console.log('Lightbox ouvert pour:', img.src);
        } catch (error) {
            console.error('Erreur lors de l\'ouverture du lightbox:', error);
        }
    }

    closeLightbox() {
        try {
            if (!this.modal) {
                console.error('Modal non disponible pour fermer le lightbox');
                return;
            }
            
            this.modal.classList.remove('active');
            
            // Restaurer le défilement de la page
            document.body.style.overflow = '';
            
            console.log('Lightbox fermé');
        } catch (error) {
            console.error('Erreur lors de la fermeture du lightbox:', error);
        }
    }
}

// Fonction d'initialisation sécurisée
function initGalleryLightbox() {
    try {
        // Attendre que le DOM soit complètement chargé
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                console.log('DOM chargé, initialisation de GalleryLightbox...');
                new GalleryLightbox();
            });
        } else {
            console.log('DOM déjà chargé, initialisation immédiate de GalleryLightbox...');
            new GalleryLightbox();
        }
    } catch (error) {
        console.error('Erreur lors de l\'initialisation de GalleryLightbox:', error);
    }
}

// Initialiser le lightbox
initGalleryLightbox();

// Initialiser aussi si le contenu est chargé dynamiquement (MutationObserver)
if (typeof MutationObserver !== 'undefined') {
    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                // Vérifier si de nouveaux éléments de galerie ont été ajoutés
                const hasNewGallery = Array.from(mutation.addedNodes).some(node => {
                    if (node.nodeType === Node.ELEMENT_NODE) {
                        return node.querySelector('.gallery-item') || node.classList.contains('gallery-item');
                    }
                    return false;
                });
                
                if (hasNewGallery) {
                    console.log('Nouveaux éléments de galerie détectés, réinitialisation...');
                    setTimeout(() => new GalleryLightbox(), 100);
                }
            }
        });
    });
    
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
}
