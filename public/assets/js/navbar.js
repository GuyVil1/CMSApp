/**
 * Navbar JavaScript - Belgium Video Gaming
 * Gestion des interactions du menu de navigation
 */

document.addEventListener('DOMContentLoaded', function() {
    const navbarToggle = document.querySelector('.navbar-toggle');
    const navbarMobile = document.querySelector('.navbar-mobile');
    const dropdown = document.querySelector('.dropdown');
    const dropdownMenu = document.querySelector('.dropdown-menu');
    
    // ===== MENU MOBILE =====
    if (navbarToggle && navbarMobile) {
        navbarToggle.addEventListener('click', function() {
            // Toggle classes
            navbarToggle.classList.toggle('active');
            navbarMobile.classList.toggle('active');
            
            // Empêcher le scroll du body quand le menu est ouvert
            document.body.style.overflow = navbarMobile.classList.contains('active') ? 'hidden' : '';
            
            // Annoncer l'état du menu pour l'accessibilité
            const isOpen = navbarMobile.classList.contains('active');
            navbarToggle.setAttribute('aria-expanded', isOpen);
            navbarToggle.setAttribute('aria-label', isOpen ? 'Fermer le menu' : 'Ouvrir le menu');
        });
        
        // Fermer le menu mobile en cliquant sur le fond
        navbarMobile.addEventListener('click', function(e) {
            if (e.target === navbarMobile) {
                closeMobileMenu();
            }
        });
        
        // Fermer le menu mobile avec la touche Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && navbarMobile.classList.contains('active')) {
                closeMobileMenu();
            }
        });
    }
    
    // ===== DROPDOWN HARDWARE =====
    if (dropdown && dropdownMenu) {
        const dropdownButton = dropdown.querySelector('.nav-button');
        
        // Gestion du focus pour l'accessibilité
        dropdownButton.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                toggleDropdown();
            } else if (e.key === 'Escape') {
                closeDropdown();
            }
        });
        
        // Gestion des touches fléchées dans le dropdown
        dropdownMenu.addEventListener('keydown', function(e) {
            const items = dropdownMenu.querySelectorAll('.dropdown-item:not(.disabled)');
            const currentIndex = Array.from(items).indexOf(document.activeElement);
            
            switch(e.key) {
                case 'ArrowDown':
                    e.preventDefault();
                    const nextIndex = (currentIndex + 1) % items.length;
                    items[nextIndex].focus();
                    break;
                case 'ArrowUp':
                    e.preventDefault();
                    const prevIndex = currentIndex <= 0 ? items.length - 1 : currentIndex - 1;
                    items[prevIndex].focus();
                    break;
                case 'Escape':
                    closeDropdown();
                    dropdownButton.focus();
                    break;
            }
        });
    }
    
    // ===== DÉTECTION DE LA PAGE ACTIVE =====
    highlightActivePage();
    
    // ===== FONCTIONS UTILITAIRES =====
    
    function closeMobileMenu() {
        navbarToggle.classList.remove('active');
        navbarMobile.classList.remove('active');
        document.body.style.overflow = '';
        navbarToggle.setAttribute('aria-expanded', 'false');
        navbarToggle.setAttribute('aria-label', 'Ouvrir le menu');
    }
    
    function toggleDropdown() {
        const isOpen = dropdownMenu.style.display === 'block';
        if (isOpen) {
            closeDropdown();
        } else {
            openDropdown();
        }
    }
    
    function openDropdown() {
        dropdownMenu.style.display = 'block';
        dropdownButton.setAttribute('aria-expanded', 'true');
        
        // Focus sur le premier élément
        const firstItem = dropdownMenu.querySelector('.dropdown-item:not(.disabled)');
        if (firstItem) {
            firstItem.focus();
        }
    }
    
    function closeDropdown() {
        dropdownMenu.style.display = 'none';
        dropdownButton.setAttribute('aria-expanded', 'false');
    }
    
    function highlightActivePage() {
        const currentPath = window.location.pathname;
        const navItems = document.querySelectorAll('.nav-item');
        
        navItems.forEach(item => {
            const link = item.querySelector('a, button');
            if (!link) return;
            
            const href = link.getAttribute('href');
            if (!href) return;
            
            // Vérifier si c'est la page active
            let isActive = false;
            
            if (href === '/' && currentPath === '/') {
                isActive = true;
            } else if (href !== '/' && currentPath.startsWith(href)) {
                isActive = true;
            }
            
            if (isActive) {
                item.classList.add('active');
            } else {
                item.classList.remove('active');
            }
        });
    }
    
    // ===== GESTION DES RÉSIZES =====
    let resizeTimeout;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(function() {
            // Fermer le menu mobile si on passe en desktop
            if (window.innerWidth > 768 && navbarMobile.classList.contains('active')) {
                closeMobileMenu();
            }
        }, 250);
    });
    
    // ===== AMÉLIORATION DES PERFORMANCES =====
    
    // Lazy loading des dropdowns
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                // Précharger les données du dropdown si nécessaire
                entry.target.classList.add('loaded');
            }
        });
    });
    
    if (dropdown) {
        observer.observe(dropdown);
    }
    
    // ===== GESTION DES ERREURS =====
    window.addEventListener('error', function(e) {
        console.error('Erreur dans navbar.js:', e.error);
    });
    
    // ===== DEBUG (à supprimer en production) =====
    if (window.location.hostname === 'localhost') {
        console.log('Navbar JavaScript chargé avec succès');
    }
});

// ===== FONCTIONS GLOBALES =====

/**
 * Fermer tous les menus ouverts
 */
function closeAllMenus() {
    // Fermer le menu mobile
    const navbarMobile = document.querySelector('.navbar-mobile');
    const navbarToggle = document.querySelector('.navbar-toggle');
    
    if (navbarMobile && navbarMobile.classList.contains('active')) {
        navbarToggle.classList.remove('active');
        navbarMobile.classList.remove('active');
        document.body.style.overflow = '';
    }
    
    // Fermer les dropdowns
    const dropdowns = document.querySelectorAll('.dropdown-menu');
    dropdowns.forEach(dropdown => {
        dropdown.style.display = 'none';
    });
}

/**
 * Ouvrir le menu mobile programmatiquement
 */
function openMobileMenu() {
    const navbarToggle = document.querySelector('.navbar-toggle');
    if (navbarToggle) {
        navbarToggle.click();
    }
}
