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
            
            console.log('GallerySlider initialisé avec succès');
        } catch (error) {
            console.error('Erreur lors de l\'initialisation de GallerySlider:', error);
        }
    }

    initAllSliders() {
        // Chercher les sliders ET les carousels
        const sliders = document.querySelectorAll('.gallery-slider');
        const carousels = document.querySelectorAll('.gallery-carousel');
        
        console.log('🔍 Sliders trouvés dans les articles:', sliders.length);
        console.log('🔍 Carousels trouvés dans les articles:', carousels.length);
        
        // Initialiser les sliders
        sliders.forEach((slider, index) => {
            console.log(`🔍 Slider ${index + 1}:`, slider);
            if (!slider.dataset.initialized) {
                console.log(`🚀 Initialisation du slider ${index + 1}`);
                this.initSlider(slider);
                slider.dataset.initialized = 'true';
            } else {
                console.log(`⏭️ Slider ${index + 1} déjà initialisé`);
            }
        });
        
        // Initialiser les carousels
        carousels.forEach((carousel, index) => {
            console.log(`🔍 Carousel ${index + 1}:`, carousel);
            if (!carousel.dataset.initialized) {
                console.log(`🚀 Initialisation du carousel ${index + 1}`);
                this.initCarousel(carousel);
                carousel.dataset.initialized = 'true';
            } else {
                console.log(`⏭️ Carousel ${index + 1} déjà initialisé`);
            }
        });
    }

    initSlider(slider) {
        try {
            console.log('🔧 Initialisation du slider dans l\'article:', slider);
            
            const track = slider.querySelector('.slider-track');
            const slides = slider.querySelectorAll('.slider-slide');
            const prevBtn = slider.querySelector('.slider-prev');
            const nextBtn = slider.querySelector('.slider-next');
            const counter = slider.querySelector('.slider-counter');
            const currentSlideSpan = counter?.querySelector('.current-slide');

            console.log('🔍 Éléments slider trouvés:', {
                track: !!track,
                slides: slides.length,
                prevBtn: !!prevBtn,
                nextBtn: !!nextBtn,
                counter: !!counter
            });

            if (!track) {
                console.error('❌ Track non trouvé dans le slider');
                return;
            }
            
            if (slides.length <= 1) {
                console.log('⏭️ Pas assez de slides pour le slider:', slides.length);
                return;
            }

            let currentIndex = 0;
            const totalSlides = slides.length;

            // Fonction pour afficher une slide
            const showSlide = (index) => {
                console.log('🎯 showSlide appelée avec index:', index, 'totalSlides:', totalSlides);
                
                if (index < 0) index = totalSlides - 1;
                if (index >= totalSlides) index = 0;
                
                currentIndex = index;
                
                // Mettre à jour la position du track
                const transform = `translateX(-${index * 100}%)`;
                track.style.transform = transform;
                console.log('🎯 Transform appliqué:', transform);
                
                // Mettre à jour le compteur
                if (currentSlideSpan) {
                    currentSlideSpan.textContent = index + 1;
                    console.log('🎯 Compteur mis à jour:', index + 1);
                }
            };

            // Événements des boutons
            if (prevBtn) {
                console.log('🔧 Ajout de l\'événement click sur prevBtn');
                prevBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    console.log('👆 Clic sur bouton précédent, index actuel:', currentIndex);
                    showSlide(currentIndex - 1);
                });
            } else {
                console.warn('⚠️ Bouton précédent non trouvé');
            }

            if (nextBtn) {
                console.log('🔧 Ajout de l\'événement click sur nextBtn');
                nextBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    console.log('👆 Clic sur bouton suivant, index actuel:', currentIndex);
                    showSlide(currentIndex + 1);
                });
            } else {
                console.warn('⚠️ Bouton suivant non trouvé');
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

            console.log('Slider initialisé dans l\'article:', slider);
        } catch (error) {
            console.error('Erreur lors de l\'initialisation du slider:', error);
        }
    }

    initCarousel(carousel) {
        try {
            console.log('🔧 Initialisation du carousel dans l\'article:', carousel);
            
            const track = carousel.querySelector('.carousel-track');
            const slides = carousel.querySelectorAll('.carousel-slide');
            const prevBtn = carousel.querySelector('.carousel-prev');
            const nextBtn = carousel.querySelector('.carousel-next');
            const counter = carousel.querySelector('.carousel-counter');
            const currentSlideSpan = counter?.querySelector('.current-slide');

            console.log('🔍 Éléments carousel trouvés:', {
                track: !!track,
                slides: slides.length,
                prevBtn: !!prevBtn,
                nextBtn: !!nextBtn,
                counter: !!counter
            });

            if (!track) {
                console.error('❌ Track non trouvé dans le carousel');
                return;
            }
            
            if (slides.length <= 1) {
                console.log('⏭️ Pas assez de slides pour le carousel:', slides.length);
                return;
            }

            let currentIndex = 0;
            const totalSlides = slides.length;

            // Fonction pour afficher une slide
            const showSlide = (index) => {
                console.log('🎯 showSlide carousel appelée avec index:', index, 'totalSlides:', totalSlides);
                
                if (index < 0) index = totalSlides - 1;
                if (index >= totalSlides) index = 0;
                
                currentIndex = index;
                
                // Mettre à jour la position du track
                const transform = `translateX(-${index * 100}%)`;
                track.style.transform = transform;
                console.log('🎯 Transform carousel appliqué:', transform);
                
                // Mettre à jour le compteur
                if (currentSlideSpan) {
                    currentSlideSpan.textContent = index + 1;
                    console.log('🎯 Compteur carousel mis à jour:', index + 1);
                }
            };

            // Événements des boutons
            if (prevBtn) {
                console.log('🔧 Ajout de l\'événement click sur carousel prevBtn');
                prevBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    console.log('👆 Clic sur bouton carousel précédent, index actuel:', currentIndex);
                    showSlide(currentIndex - 1);
                });
            } else {
                console.warn('⚠️ Bouton carousel précédent non trouvé');
            }

            if (nextBtn) {
                console.log('🔧 Ajout de l\'événement click sur carousel nextBtn');
                nextBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    console.log('👆 Clic sur bouton carousel suivant, index actuel:', currentIndex);
                    showSlide(currentIndex + 1);
                });
            } else {
                console.warn('⚠️ Bouton carousel suivant non trouvé');
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

            console.log('✅ Carousel initialisé dans l\'article:', carousel);
        } catch (error) {
            console.error('Erreur lors de l\'initialisation du carousel:', error);
        }
    }

    observeNewSliders() {
        if (typeof MutationObserver !== 'undefined') {
            const observer = new MutationObserver((mutations) => {
                mutations.forEach((mutation) => {
                    if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
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
                        
                        if (hasNewSlider || hasNewCarousel) {
                            console.log('Nouveaux sliders/carousels détectés, réinitialisation...');
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
                console.log('DOM chargé, initialisation des galeries...');
                // Délai pour s'assurer que tous les styles sont chargés
                setTimeout(() => {
                    new GalleryLightbox();
                    new GallerySlider();
                }, 100);
            });
        } else {
            console.log('DOM déjà chargé, initialisation immédiate des galeries...');
            // Délai pour s'assurer que tous les styles sont chargés
            setTimeout(() => {
                new GalleryLightbox();
                new GallerySlider();
            }, 100);
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
                    console.log('Nouveaux éléments de galerie détectés, réinitialisation...');
                    setTimeout(() => new GalleryLightbox(), 100);
                }
                
                if (hasNewSlider || hasNewCarousel) {
                    console.log('Nouveaux sliders/carousels détectés, réinitialisation...');
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
