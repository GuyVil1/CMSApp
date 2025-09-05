/**
 * Gestionnaire de sliders pour les galeries dans les articles
 */
class GallerySlider {
    constructor() {
        this.sliders = [];
        this.init();
    }

    init() {
        try {
            // Initialiser tous les sliders existants
            this.initAllSliders();
            
            // Observer les changements du DOM pour les nouveaux sliders
            this.observeNewSliders();
            
            // GallerySlider initialisé
        } catch (error) {
            console.error('Erreur lors de l\'initialisation de GallerySlider:', error);
        }
    }

    initAllSliders() {
        // Chercher les sliders, carousels ET masonry
        const sliders = document.querySelectorAll('.gallery-slider');
        const carousels = document.querySelectorAll('.gallery-carousel');
        const masonry = document.querySelectorAll('.gallery-masonry');
        
        // Logs supprimés pour la production
        
        // Initialiser les sliders
        sliders.forEach((slider, index) => {
            if (!slider.dataset.initialized) {
                this.initSlider(slider);
                slider.dataset.initialized = 'true';
            }
        });
        
        // Initialiser les carousels
        carousels.forEach((carousel, index) => {
            if (!carousel.dataset.initialized) {
                this.initCarousel(carousel);
                carousel.dataset.initialized = 'true';
            }
        });
        
        // Initialiser les masonry
        masonry.forEach((masonryEl, index) => {
            if (!masonryEl.dataset.initialized) {
                this.initMasonry(masonryEl);
                masonryEl.dataset.initialized = 'true';
            }
        });
    }

    initSlider(slider) {
        try {
            const track = slider.querySelector('.slider-track');
            const slides = slider.querySelectorAll('.slider-slide');
            const prevBtn = slider.querySelector('.slider-prev');
            const nextBtn = slider.querySelector('.slider-next');
            const counter = slider.querySelector('.slider-counter');
            const currentSlideSpan = counter?.querySelector('.current-slide');

            if (!track) {
                console.error('❌ Track non trouvé dans le slider');
                return;
            }
            
            if (slides.length <= 1) {
                return;
            }

            let currentIndex = 0;
            const totalSlides = slides.length;

            // Fonction pour afficher une slide
            const showSlide = (index) => {
                if (index < 0) index = totalSlides - 1;
                if (index >= totalSlides) index = 0;
                
                currentIndex = index;
                
                // Mettre à jour la position du track
                const transform = `translateX(-${index * 100}%)`;
                track.style.transform = transform;
                
                // Mettre à jour le compteur
                if (currentSlideSpan) {
                    currentSlideSpan.textContent = index + 1;
                }
            };

            // Événements des boutons
            if (prevBtn) {
                prevBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    showSlide(currentIndex - 1);
                });
            }

            if (nextBtn) {
                nextBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    showSlide(currentIndex + 1);
                });
            }

            // Navigation au clavier
            slider.addEventListener('keydown', (e) => {
                if (e.key === 'ArrowLeft') {
                    showSlide(currentIndex - 1);
                } else if (e.key === 'ArrowRight') {
                    showSlide(currentIndex + 1);
                }
            });

            // Rendre le slider focusable
            slider.setAttribute('tabindex', '0');

            // Initialiser à la première slide
            showSlide(0);
        } catch (error) {
            console.error('Erreur lors de l\'initialisation du slider:', error);
        }
    }

    initCarousel(carousel) {
        try {
            const track = carousel.querySelector('.carousel-track');
            const slides = carousel.querySelectorAll('.carousel-slide');
            const prevBtn = carousel.querySelector('.carousel-prev');
            const nextBtn = carousel.querySelector('.carousel-next');
            const counter = carousel.querySelector('.carousel-counter');
            const currentSlideSpan = counter?.querySelector('.current-slide');

            if (!track) {
                console.error('❌ Track non trouvé dans le carousel');
                return;
            }
            
            if (slides.length <= 1) {
                return;
            }

            let currentIndex = 0;
            const totalSlides = slides.length;

            // Fonction pour afficher une slide
            const showSlide = (index) => {
                if (index < 0) index = totalSlides - 1;
                if (index >= totalSlides) index = 0;
                
                currentIndex = index;
                
                // Mettre à jour la position du track
                const transform = `translateX(-${index * 100}%)`;
                track.style.transform = transform;
                
                // Mettre à jour le compteur
                if (currentSlideSpan) {
                    currentSlideSpan.textContent = index + 1;
                }
            };

            // Événements des boutons
            if (prevBtn) {
                prevBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    showSlide(currentIndex - 1);
                });
            }

            if (nextBtn) {
                nextBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    showSlide(currentIndex + 1);
                });
            }

            // Navigation au clavier
            carousel.addEventListener('keydown', (e) => {
                if (e.key === 'ArrowLeft') {
                    showSlide(currentIndex - 1);
                } else if (e.key === 'ArrowRight') {
                    showSlide(currentIndex + 1);
                }
            });

            // Rendre le carousel focusable
            carousel.setAttribute('tabindex', '0');

            // Initialiser à la première slide
            showSlide(0);
        } catch (error) {
            console.error('Erreur lors de l\'initialisation du carousel:', error);
        }
    }

    initMasonry(masonry) {
        try {
            const items = masonry.querySelectorAll('.gallery-item');
            if (items.length === 0) {
                return;
            }

            // Fonction pour réorganiser les éléments en colonne unique
            const reorganizeMasonry = () => {
                const containerWidth = masonry.offsetWidth;
                const gap = 16; // 1rem en pixels
                const columnWidth = containerWidth; // Une seule colonne qui prend toute la largeur
                
                let currentTop = 0;

                // Placer toutes les images dans une seule colonne
                items.forEach((item, index) => {
                    item.style.position = 'absolute';
                    item.style.left = '0px';
                    item.style.top = `${currentTop}px`;
                    item.style.width = `${columnWidth}px`;
                    
                    // Calculer la hauteur réelle de l'élément
                    const img = item.querySelector('img');
                    let itemHeight;
                    if (img && img.complete && img.naturalHeight > 0) {
                        const aspectRatio = img.naturalHeight / img.naturalWidth;
                        itemHeight = columnWidth * aspectRatio;
                    } else {
                        itemHeight = columnWidth * 0.75; // 4:3 par défaut
                    }
                    
                    currentTop += itemHeight + gap;
                });

                // Ajuster la hauteur du conteneur
                masonry.style.height = `${currentTop - gap}px`; // Retirer le dernier gap
            };

            // Réorganiser au chargement et au redimensionnement
            setTimeout(reorganizeMasonry, 100); // Petit délai pour s'assurer que les images sont chargées
            
            // Réorganiser quand les images sont chargées
            const images = masonry.querySelectorAll('img');
            let loadedImages = 0;
            
            images.forEach(img => {
                if (img.complete) {
                    loadedImages++;
                    if (loadedImages === images.length) {
                        reorganizeMasonry();
                    }
                } else {
                    img.addEventListener('load', () => {
                        loadedImages++;
                        if (loadedImages === images.length) {
                            reorganizeMasonry();
                        }
                    });
                }
            });

            // Réorganiser au redimensionnement
            window.addEventListener('resize', reorganizeMasonry);
        } catch (error) {
            console.error('❌ Erreur lors de l\'initialisation du masonry:', error);
        }
    }

    observeNewSliders() {
        if (typeof MutationObserver !== 'undefined') {
            const observer = new MutationObserver((mutations) => {
                mutations.forEach((mutation) => {
                    if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                        // Vérifier si de nouveaux sliders, carousels ou masonry ont été ajoutés
                        const hasNewSlider = Array.from(mutation.addedNodes).some(node => {
                            if (node.nodeType === Node.ELEMENT_NODE) {
                                return node.querySelector('.gallery-slider') || node.classList.contains('gallery-slider');
                            }
                            return false;
                        });
                        
                        const hasNewCarousel = Array.from(mutation.addedNodes).some(node => {
                            if (node.nodeType === Node.ELEMENT_NODE) {
                                return node.querySelector('.gallery-carousel') || node.classList.contains('gallery-carousel');
                            }
                            return false;
                        });
                        
                        const hasNewMasonry = Array.from(mutation.addedNodes).some(node => {
                            if (node.nodeType === Node.ELEMENT_NODE) {
                                return node.querySelector('.gallery-masonry') || node.classList.contains('gallery-masonry');
                            }
                            return false;
                        });
                        
                        if (hasNewSlider || hasNewCarousel || hasNewMasonry) {
                            setTimeout(() => this.initAllSliders(), 100);
                        }
                    }
                });
            });
            
            observer.observe(document.body, {
                childList: true,
                subtree: true
            });
        }
    }
}

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
            
            // GalleryLightbox initialisé
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
            
            // Modal lightbox créé
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
            
            // Événements de galerie liés
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
            
            // Événements de fermeture liés
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
            
            // Lightbox ouvert
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
            
            // Lightbox fermé
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
                // Délai pour s'assurer que tous les styles sont chargés
                setTimeout(() => {
                    new GalleryLightbox();
                    new GallerySlider();
                }, 100);
            });
        } else {
            // Délai pour s'assurer que tous les styles sont chargés
            setTimeout(() => {
                new GalleryLightbox();
                new GallerySlider();
            }, 100);
            
            // Délai supplémentaire pour s'assurer que le contenu de l'article est chargé
            setTimeout(() => {
                const masonry = document.querySelectorAll('.gallery-masonry');
                if (masonry.length > 0) {
                    new GallerySlider();
                }
            }, 500);
        }
    } catch (error) {
        console.error('Erreur lors de l\'initialisation des galeries:', error);
    }
}

// Initialiser les galeries
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
                
                // Vérifier si de nouveaux sliders ou carousels ont été ajoutés
                const hasNewSlider = Array.from(mutation.addedNodes).some(node => {
                    if (node.nodeType === Node.ELEMENT_NODE) {
                        return node.querySelector('.gallery-slider') || node.classList.contains('gallery-slider');
                    }
                    return false;
                });
                
                const hasNewCarousel = Array.from(mutation.addedNodes).some(node => {
                    if (node.nodeType === Node.ELEMENT_NODE) {
                        return node.querySelector('.gallery-carousel') || node.classList.contains('gallery-carousel');
                    }
                    return false;
                });
                
                if (hasNewGallery) {
                    setTimeout(() => new GalleryLightbox(), 100);
                }
                
                if (hasNewSlider || hasNewCarousel) {
                    setTimeout(() => new GallerySlider(), 100);
                }
            }
        });
    });
    
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
}
