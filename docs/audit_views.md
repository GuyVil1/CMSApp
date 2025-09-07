# 🎨 AUDIT - VUES (VIEWS)

## 📋 **Templates et vues de l'application**

---

### **37. app/views/layout/main.php**
**📍 Emplacement :** `/app/views/layout/main.php`  
**🎯 Fonction :** Layout principal pour les pages admin avec navigation et thème  
**🔗 Interactions :**
- Utilise `core/Auth.php` pour l'authentification
- Utilise `public/assets/css/layout/main-layout.css`
- Inclut des CSS additionnels dynamiques
- Inclut des JS additionnels dynamiques

**⚙️ Fonctionnalités :**
- **Header** avec logo, navigation et authentification
- **Navigation principale** : Accueil, Jeux, Articles, Catégories, Thèmes
- **Section authentification** : Connexion/Déconnexion, accès admin
- **Thème visuel** conditionnel (uniquement sur pages non-articles)
- **Footer** avec liens et informations
- **Scripts dynamiques** pour CSS et JS additionnels

**🎨 CSS intégré :** Aucun CSS intégré  
**📄 Feuilles de style :**
- `/public/assets/css/layout/main-layout.css` (principal)
- CSS additionnels via `$additionalCSS`

**🔧 Variables utilisées :**
- `$pageTitle` - Titre de la page
- `$additionalCSS` - Tableau de CSS additionnels
- `$additionalJS` - Tableau de JS additionnels
- `$currentTheme` - Thème actuel avec images
- `$content` - Contenu principal de la page

**🎨 Thème visuel :**
- Bannières thématiques conditionnelles
- Exclusion sur les pages d'articles
- Images left/right du thème actuel

---

### **38. app/views/layout/public.php**
**📍 Emplacement :** `/app/views/layout/public.php`  
**🎯 Fonction :** Layout principal pour les pages publiques avec thème belge complet  
**🔗 Interactions :**
- Utilise `core/Auth.php` pour l'authentification
- Utilise `app/models/Setting.php` pour les paramètres
- Utilise `app/models/Hardware.php` pour la navbar
- Utilise `app/helpers/navigation_helper.php`
- Utilise `app/views/components/navbar.php`

**⚙️ Fonctionnalités :**
- **SEO complet** : Meta tags, Open Graph, Twitter Cards
- **Header belge** avec logo configurable et titre
- **Navbar dynamique** avec hardware et navigation
- **Bannières thématiques** dynamiques
- **Mode sombre** conditionnel avec CSS intégré
- **Footer belge** avec logos, réseaux sociaux, navigation
- **Scripts** : navbar.js, gallery-lightbox.js

**🎨 CSS intégré :**
```css
/* Bannières thématiques dynamiques */
.theme-banner-left, .theme-banner-right { background: url('/theme-image.php?...') }

/* Mode sombre complet */
body, .main-layout, .main-content, .section, .footer, .header {
    background-color: #1a1a1a !important;
    color: #ffffff !important;
}
```

**📄 Feuilles de style :**
- `/style.css` (principal)
- `/public/assets/css/components/content-modules.css`
- `/public/assets/css/components/navbar.css`
- `/public/assets/css/components/article-responsive.css`
- `/public/assets/css/pages/legal.css`
- CSS additionnels via `$additionalCSS`

**🔧 Variables utilisées :**
- `$seoMetaTags` - Meta tags SEO personnalisés
- `$pageTitle`, `$pageDescription` - SEO de base
- `$darkMode` - Activation mode sombre
- `$isLoggedIn`, `$allowRegistration` - Authentification
- `$currentTheme` - Thème avec images
- `$article`, `$featuredArticles` - Contenu conditionnel
- `$content` - Contenu générique

**🎨 Thème belge :**
- **Couleurs** : Rouge (#E30613), Jaune (#FFD700), Noir, Blanc
- **Fonts** : Luckiest Guy (titres), Segoe UI (corps)
- **Bannières** : Images left/right dynamiques
- **Logos** : Header et footer configurables
- **Réseaux sociaux** : URLs configurables

**📱 Responsive :**
- Design adaptatif
- Navigation mobile
- Images responsives

---

### **39. app/views/layout/maintenance.php**
**📍 Emplacement :** `/app/views/layout/maintenance.php`  
**🎯 Fonction :** Page de maintenance avec design belge et animations  
**🔗 Interactions :**
- Utilise `/public/assets/images/logo.png`
- Accès admin conditionnel

**⚙️ Fonctionnalités :**
- **Design belge** avec couleurs officielles
- **Animations** : Logo flottant, icône rotative, barre de progression
- **Éléments flottants** décoratifs
- **Accès admin** conditionnel (coin supérieur droit)
- **Vérification automatique** de disponibilité (toutes les 30s)
- **Liens sociaux** avec SVG
- **Responsive** complet

**🎨 CSS intégré :**
```css
:root {
    --belgium-red: #E30613;
    --belgium-yellow: #FFD700;
    --belgium-black: #000000;
    --belgium-white: #FFFFFF;
}

/* Animations */
@keyframes logoFloat, iconSpin, backgroundPulse, progressAnimation, float

/* Design moderne */
background: linear-gradient(135deg, var(--belgium-red) 0%, #B8050E 100%);
backdrop-filter: blur(10px);
```

**📄 Feuilles de style :** Aucune référence externe

**🔧 Variables utilisées :**
- `$isAdmin` - Accès admin conditionnel

**🎨 Design :**
- **Gradient** rouge belge
- **Glassmorphism** avec backdrop-filter
- **Animations** fluides et modernes
- **Typographie** Inter (Google Fonts)
- **Icônes** SVG intégrées

**⚙️ JavaScript intégré :**
- Animation barre de progression
- Vérification périodique disponibilité
- Redirection automatique si site disponible

**📱 Responsive :**
- Breakpoints : 768px, 480px
- Adaptation mobile complète
- Éléments redimensionnés

---

## 📋 **VUES ADMIN**

### **40. app/views/admin/dashboard/index.php**
**📍 Emplacement :** `/app/views/admin/dashboard/index.php`  
**🎯 Fonction :** Tableau de bord principal de l'administration  
**🔗 Interactions :**
- Utilise `admin.css` pour le style
- Variables : `$options`, `$user`, `$stats`

**⚙️ Fonctionnalités :**
- **Alertes maintenance** conditionnelles
- **Informations utilisateur** : login, email, rôle, dernière connexion
- **Statistiques** : articles, utilisateurs, jeux, catégories
- **Options du site** : inscriptions, mode sombre, maintenance, thème
- **Actions rapides** : gestion contenu, administration, navigation
- **Bouton déconnexion** centré

**🎨 CSS intégré :**
```css
/* Effets de survol pour les boutons d'action */
.action-btn:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3); }

/* Section Options */
.options-section { background: var(--admin-card-bg); border: 1px solid var(--admin-border); }

/* Responsive design */
@media (max-width: 1200px) { .actions-grid { grid-template-columns: repeat(2, 1fr) !important; } }
```

**📄 Feuilles de style :**
- `/admin.css` (principal)

**🔧 Variables utilisées :**
- `$options` - Paramètres du site (maintenance, dark mode, etc.)
- `$user` - Informations utilisateur connecté
- `$stats` - Statistiques (articles, users, games, categories)

**🎨 Design :**
- **Grilles responsives** : 4 colonnes → 2 → 1
- **Boutons d'action** avec effets hover
- **Cartes d'options** avec statuts colorés
- **Navigation** vers toutes les sections admin

---

### **41. app/views/admin/settings/index.php**
**📍 Emplacement :** `/app/views/admin/settings/index.php`  
**🎯 Fonction :** Interface de configuration complète du site  
**🔗 Interactions :**
- Utilise `admin.css` pour le style
- Variables : `$settings`, `$themes`, `$logos`
- Formulaire POST vers `/admin/settings/save`

**⚙️ Fonctionnalités :**
- **Gestion utilisateurs** : toggle inscriptions
- **Interface** : mode sombre, thème par défaut
- **Maintenance** : toggle mode maintenance
- **Footer** : phrase d'accroche personnalisable
- **Réseaux sociaux** : URLs Twitter, Facebook, YouTube
- **Logos** : sélection header/footer avec prévisualisation
- **Pages légales** : éditeur HTML avec onglets

**🎨 CSS intégré :**
```css
/* Toggle Switch */
.toggle-switch { position: relative; display: inline-block; width: 60px; height: 34px; }
.slider { position: absolute; cursor: pointer; background-color: #ccc; transition: .4s; }

/* Éditeur de pages légales */
.legal-editor { background: var(--admin-card-bg); border-radius: 8px; overflow: hidden; }
.legal-tabs { display: flex; background: var(--admin-bg-secondary); }
.legal-tab.active { background: var(--admin-primary); color: white; }
.legal-textarea { width: 100%; min-height: 300px; font-family: 'Segoe UI'; }
```

**📄 Feuilles de style :**
- `/admin.css` (principal)

**🔧 Variables utilisées :**
- `$settings` - Paramètres actuels du site
- `$themes` - Thèmes disponibles
- `$logos` - Logos disponibles
- `$_SESSION['flash_message']` - Messages flash

**🎨 Éditeur pages légales :**
- **4 onglets** : Mentions, Confidentialité, CGU, Cookies
- **Textareas HTML** avec placeholders
- **Aide contextuelle** pour les balises HTML
- **JavaScript** pour gestion des onglets

**📱 Responsive :**
- Adaptation mobile des formulaires
- Onglets en colonne sur mobile
- Boutons empilés sur petits écrans

---

### **42. app/views/admin/articles/index.php**
**📍 Emplacement :** `/app/views/admin/articles/index.php`  
**🎯 Fonction :** Liste et gestion des articles avec filtres et pagination  
**🔗 Interactions :**
- Utilise `admin.css` pour le style
- Variables : `$articles`, `$filters`, `$categories`, `$pagination`, `$user`

**⚙️ Fonctionnalités :**
- **Navigation** : accueil, dashboard, nouvel article
- **Filtres** : recherche, statut, catégorie
- **Tableau articles** : titre, statut, position, catégorie, auteur, date
- **Actions** : édition, publication, archivage, suppression
- **Pagination** complète avec paramètres
- **Statistiques** : nombre total d'articles

**🎨 CSS intégré :** Aucun CSS intégré  
**📄 Feuilles de style :**
- `/admin.css` (principal)

**🔧 Variables utilisées :**
- `$articles` - Liste des articles
- `$filters` - Filtres appliqués (search, status, category)
- `$categories` - Liste des catégories
- `$pagination` - Informations de pagination
- `$user` - Utilisateur connecté (pour permissions)

**🔐 Permissions :**
- **Édition** : admin ou auteur de l'article
- **Publication** : admin uniquement
- **Suppression** : admin ou auteur de l'article

**📊 Fonctionnalités avancées :**
- **Recherche** dans titre, extrait, contenu
- **Filtres multiples** : statut + catégorie
- **Actions conditionnelles** selon les permissions
- **Confirmation** pour suppression

---

### **43. app/views/admin/articles/form.php**
**📍 Emplacement :** `/app/views/admin/articles/form.php`  
**🎯 Fonction :** Formulaire de création/édition d'articles avec éditeur WYSIWYG  
**🔗 Interactions :**
- Utilise `admin.css` pour le style
- Variables : `$article`, `$categories`, `$games`, `$hardware`, `$tags`
- Éditeur WYSIWYG intégré

**⚙️ Fonctionnalités :**
- **Formulaire complet** : titre, slug, extrait, contenu
- **Sélection** : catégorie, jeu, hardware
- **Tags** : système de tags avec autocomplétion
- **Image de couverture** : upload et prévisualisation
- **Statut** : brouillon, publié, archivé
- **Position en avant** : 1-5 pour mise en avant
- **Éditeur WYSIWYG** : barre d'outils complète

**🎨 CSS intégré :**
```css
/* Styles personnalisés pour l'éditeur */
body { background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%); color: white; }
.btn { padding: 12px 24px; background: #e74c3c; border-radius: 5px; transition: background 0.3s; }
.form-control { width: 100%; padding: 10px; border: 1px solid rgba(255, 255, 255, 0.3); }
```

**📄 Feuilles de style :**
- `/admin.css` (principal)
- Styles intégrés pour l'éditeur

**🔧 Variables utilisées :**
- `$article` - Article à éditer (null pour création)
- `$categories` - Liste des catégories
- `$games` - Liste des jeux
- `$hardware` - Liste du hardware
- `$tags` - Tags existants

**🎨 Éditeur WYSIWYG :**
- **Barre d'outils** complète
- **Upload d'images** intégré
- **Prévisualisation** en temps réel
- **Sauvegarde automatique** (optionnel)

**📱 Responsive :**
- Formulaire adaptatif
- Éditeur responsive
- Boutons empilés sur mobile

---

### **44. app/views/admin/categories/index.php**
**📍 Emplacement :** `/app/views/admin/categories/index.php`  
**🎯 Fonction :** Liste et gestion des catégories avec statistiques et filtres  
**🔗 Interactions :**
- Utilise `admin.css` pour le style
- Variables : `$categories`, `$totalCategories`, `$search`, `$currentPage`, `$totalPages`
- Actions AJAX pour suppression

**⚙️ Fonctionnalités :**
- **Navigation** : retour dashboard, nouvelle catégorie
- **Messages** : erreurs et succès via GET parameters
- **Statistiques** : total catégories, articles catégorisés, catégories utilisées
- **Filtres** : recherche par nom
- **Tableau** : ID, nom, slug, description, nombre d'articles, actions
- **Pagination** avec paramètres de recherche
- **Suppression AJAX** avec vérification articles associés

**🎨 CSS intégré :** Aucun CSS intégré  
**📄 Feuilles de style :**
- `/admin.css` (principal)

**🔧 Variables utilisées :**
- `$categories` - Liste des catégories avec compteurs
- `$totalCategories` - Nombre total de catégories
- `$search` - Terme de recherche
- `$currentPage`, `$totalPages` - Pagination

**🔐 Sécurité :**
- **CSRF tokens** pour suppression
- **Vérification articles** avant suppression
- **Validation côté client** et serveur

**📊 Fonctionnalités avancées :**
- **Compteurs d'articles** par catégorie
- **Badges colorés** selon utilisation
- **Suppression conditionnelle** (interdite si articles)
- **Messages contextuels** selon le statut

**⚙️ JavaScript intégré :**
- **Suppression AJAX** avec fetch API
- **Confirmation** avec détails des articles
- **Gestion d'erreurs** complète
- **Rechargement** automatique après succès

---

### **45. app/views/admin/categories/create.php**
**📍 Emplacement :** `/app/views/admin/categories/create.php`  
**🎯 Fonction :** Formulaire de création de catégorie avec validation  
**🔗 Interactions :**
- Utilise `admin.css` pour le style
- Variables : `$error`, `$success`, `$csrf_token`
- Formulaire POST vers `/categories.php?action=store`

**⚙️ Fonctionnalités :**
- **Navigation** : retour à la liste
- **Messages** : erreurs et succès
- **Formulaire** : nom, slug, description
- **Validation** : côté client et serveur
- **Génération automatique** du slug
- **Compteur de caractères** pour description

**🎨 CSS intégré :** Aucun CSS intégré  
**📄 Feuilles de style :**
- `/admin.css` (principal)

**🔧 Variables utilisées :**
- `$error` - Message d'erreur
- `$success` - Message de succès
- `$csrf_token` - Token CSRF
- `$_POST` - Valeurs du formulaire

**📝 Champs du formulaire :**
- **Nom** : obligatoire, max 80 caractères
- **Slug** : obligatoire, max 120 caractères, pattern alphanumérique
- **Description** : optionnel, max 500 caractères

**⚙️ JavaScript intégré :**
- **Génération automatique** du slug depuis le nom
- **Validation côté client** complète
- **Compteur de caractères** dynamique avec couleurs
- **Prévention soumission** si erreurs

**🎨 UX/UI :**
- **Feedback visuel** pour compteur
- **Couleurs contextuelles** (rouge/jaune/gris)
- **Validation en temps réel**
- **Messages d'aide** pour chaque champ

---

### **46. app/views/admin/categories/edit.php**
**📍 Emplacement :** `/app/views/admin/categories/edit.php`  
**🎯 Fonction :** Formulaire d'édition de catégorie avec informations système  
**🔗 Interactions :**
- Utilise `admin.css` pour le style
- Variables : `$category`, `$error`, `$success`, `$csrf_token`
- Formulaire POST vers `/categories.php?action=update&id={id}`

**⚙️ Fonctionnalités :**
- **Navigation** : retour à la liste
- **Messages** : erreurs et succès
- **Informations système** : ID, slug, date création, articles associés
- **Formulaire** : nom, slug, description (pré-remplis)
- **Validation** : côté client et serveur
- **Compteur de caractères** pour description

**🎨 CSS intégré :** Aucun CSS intégré  
**📄 Feuilles de style :**
- `/admin.css` (principal)

**🔧 Variables utilisées :**
- `$category` - Objet catégorie à éditer
- `$error` - Message d'erreur
- `$success` - Message de succès
- `$csrf_token` - Token CSRF
- `$_POST` - Valeurs du formulaire (priorité sur objet)

**📊 Informations système :**
- **ID** de la catégorie
- **Slug actuel**
- **Date de création**
- **Nombre d'articles** associés

**⚙️ JavaScript intégré :**
- **Génération conditionnelle** du slug (si vide)
- **Validation côté client** complète
- **Compteur de caractères** avec initialisation
- **Prévention soumission** si erreurs

**🎨 UX/UI :**
- **Pré-remplissage** des champs
- **Préservation** des valeurs POST en cas d'erreur
- **Feedback visuel** pour compteur
- **Validation en temps réel**

---

### **47. app/views/admin/games/index.php**
**📍 Emplacement :** `/app/views/admin/games/index.php`  
**🎯 Fonction :** Liste et gestion des jeux avec filtres avancés et statistiques  
**🔗 Interactions :**
- Utilise `admin.css` pour le style
- Variables : `$games`, `$totalGames`, `$platforms`, `$genres`, `$search`, `$platform`, `$genre`
- Actions AJAX pour suppression

**⚙️ Fonctionnalités :**
- **Navigation** : retour dashboard, nouveau jeu
- **Messages** : succès et erreurs via GET parameters
- **Statistiques** : total jeux, plateformes, genres, articles liés
- **Filtres avancés** : recherche, plateforme, genre
- **Tableau détaillé** : ID, cover, titre, hardware, genre, date sortie, articles, actions
- **Pagination** avec tous les paramètres de filtre
- **Suppression AJAX** avec vérification articles

**🎨 CSS intégré :** Aucun CSS intégré  
**📄 Feuilles de style :**
- `/admin.css` (principal)

**🔧 Variables utilisées :**
- `$games` - Liste des jeux avec relations
- `$totalGames` - Nombre total de jeux
- `$platforms` - Liste des plateformes
- `$genres` - Liste des genres
- `$search`, `$platform`, `$genre` - Filtres appliqués

**🎮 Fonctionnalités jeux :**
- **Images de couverture** avec fallback
- **Badges de plateforme** et genre
- **Statut de sortie** (sorti/à venir)
- **Compteurs d'articles** liés
- **Informations détaillées** (titre, slug, date)

**🔐 Sécurité :**
- **CSRF tokens** pour suppression
- **Vérification articles** avant suppression
- **Validation côté client** et serveur

**⚙️ JavaScript intégré :**
- **Suppression AJAX** avec fetch API
- **Confirmation** avec détails des articles
- **Gestion d'erreurs** complète
- **Rechargement** automatique après succès

**📱 Responsive :**
- **Tableau adaptatif**
- **Filtres empilés** sur mobile
- **Actions compactes**

---

### **48. app/views/admin/games/create.php**
**📍 Emplacement :** `/app/views/admin/games/create.php`  
**🎯 Fonction :** Formulaire de création de jeu avec gestion d'images et liens d'achat  
**🔗 Interactions :**
- Utilise `admin.css` pour le style
- Variables : `$error`, `$csrf_token`, `$hardware`, `$genres`
- Formulaire POST vers `/games.php?action=store`
- Intégration médiathèque via popup

**⚙️ Fonctionnalités :**
- **Navigation** : retour à la liste
- **Messages** : erreurs
- **Formulaire complet** : informations de base, classification, équipe, test, PEGI, liens d'achat, image
- **Gestion d'images** : upload nouveau ou sélection existante
- **Validation** : côté client et serveur
- **Génération automatique** du slug

**🎨 CSS intégré :** Styles pour médiathèque et sélection d'images  
**📄 Feuilles de style :**
- `/admin.css` (principal)

**🔧 Variables utilisées :**
- `$error` - Message d'erreur
- `$csrf_token` - Token CSRF
- `$hardware` - Liste des hardwares
- `$genres` - Liste des genres
- `$_POST` - Valeurs du formulaire

**📝 Sections du formulaire :**
- **Informations de base** : titre, slug, description
- **Classification** : hardware, genre, date de sortie
- **Équipe** : développeur, éditeur
- **Test** : checkbox testé + note conditionnelle
- **PEGI** : classification d'âge
- **Liens d'achat** : Steam, eShop, Xbox, PSN, Epic, GOG
- **Image de couverture** : upload ou médiathèque

**⚙️ JavaScript intégré :**
- **Génération automatique** du slug
- **Upload d'images** avec drag & drop
- **Prévisualisation** d'images
- **Affichage conditionnel** du champ score
- **Intégration médiathèque** via popup
- **Validation** côté client

**🎨 UX/UI :**
- **Sections organisées** avec icônes
- **Upload drag & drop** avec feedback visuel
- **Champs conditionnels** (score si testé)
- **Messages d'aide** pour chaque champ
- **Validation en temps réel**

---

### **49. app/views/admin/games/edit.php**
**📍 Emplacement :** `/app/views/admin/games/edit.php`  
**🎯 Fonction :** Formulaire d'édition de jeu avec informations système et médiathèque  
**🔗 Interactions :**
- Utilise `admin.css` pour le style
- Variables : `$game`, `$error`, `$csrf_token`, `$hardware`, `$genres`
- Formulaire POST vers `/games.php?action=update&id={id}`
- Intégration médiathèque via popup

**⚙️ Fonctionnalités :**
- **Navigation** : retour à la liste
- **Messages** : erreurs
- **Informations système** : ID, slug, date création, articles liés
- **Formulaire complet** : toutes les sections (pré-remplies)
- **Gestion d'images** : sélection via médiathèque uniquement
- **Validation** : côté client et serveur
- **Affichage cover actuelle**

**🎨 CSS intégré :** Styles pour médiathèque et sélection d'images  
**📄 Feuilles de style :**
- `/admin.css` (principal)

**🔧 Variables utilisées :**
- `$game` - Objet jeu à éditer
- `$error` - Message d'erreur
- `$csrf_token` - Token CSRF
- `$hardware` - Liste des hardwares
- `$genres` - Liste des genres
- `$_POST` - Valeurs du formulaire (priorité sur objet)

**📊 Informations système :**
- **ID** du jeu
- **Slug actuel**
- **Date de création**
- **Nombre d'articles** liés

**🖼️ Gestion d'images :**
- **Médiathèque uniquement** (pas d'upload)
- **Affichage cover actuelle** si existante
- **Sélection via popup** avec feedback

**⚙️ JavaScript intégré :**
- **Génération conditionnelle** du slug (si vide)
- **Affichage conditionnel** du champ score
- **Intégration médiathèque** via popup
- **Validation** côté client

**🎨 UX/UI :**
- **Pré-remplissage** des champs
- **Préservation** des valeurs POST en cas d'erreur
- **Affichage cover actuelle**
- **Feedback visuel** pour sélection d'image

---

### **50. app/views/admin/users/index.php**
**📍 Emplacement :** `/app/views/admin/users/index.php`  
**🎯 Fonction :** Liste et gestion des utilisateurs avec filtres par rôle  
**🔗 Interactions :**
- Utilise `admin.css` pour le style
- Variables : `$users`, `$totalUsers`, `$adminUsers`, `$search`, `$role`, `$currentPage`, `$totalPages`
- Actions AJAX pour suppression

**⚙️ Fonctionnalités :**
- **Navigation** : retour dashboard, nouvel utilisateur
- **Statistiques** : total utilisateurs, administrateurs
- **Filtres** : recherche par login/email, filtre par rôle
- **Tableau** : ID, login, email, rôle, date création, dernière connexion, actions
- **Pagination** avec paramètres de filtre
- **Suppression AJAX** avec confirmation

**🎨 CSS intégré :** Aucun CSS intégré  
**📄 Feuilles de style :**
- `/admin.css` (principal)

**🔧 Variables utilisées :**
- `$users` - Liste des utilisateurs
- `$totalUsers` - Nombre total d'utilisateurs
- `$adminUsers` - Nombre d'administrateurs
- `$search` - Terme de recherche
- `$role` - Rôle filtré
- `$currentPage`, `$totalPages` - Pagination

**👥 Gestion des utilisateurs :**
- **Badges de rôle** colorés
- **Dates formatées** (création, dernière connexion)
- **Filtrage par rôle** (admin, editor, author, member)
- **Recherche** par login ou email

**🔐 Sécurité :**
- **CSRF tokens** pour suppression
- **Confirmation** avant suppression
- **Validation côté client** et serveur

**⚙️ JavaScript intégré :**
- **Suppression AJAX** avec fetch API
- **Confirmation** avant suppression
- **Gestion d'erreurs** complète
- **Rechargement** automatique après succès

**📱 Responsive :**
- **Tableau adaptatif**
- **Filtres empilés** sur mobile
- **Actions compactes**

---

### **51. app/views/admin/users/create.php**
**📍 Emplacement :** `/app/views/admin/users/create.php`  
**🎯 Fonction :** Formulaire de création d'utilisateur avec validation complète  
**🔗 Interactions :**
- Utilise `admin.css` pour le style
- Variables : `$error`, `$success`, `$csrf_token`, `$roles`
- Formulaire POST vers `/users.php?action=create`

**⚙️ Fonctionnalités :**
- **Navigation** : retour à la liste
- **Messages** : erreurs et succès
- **Formulaire** : informations de base, rôle et permissions
- **Validation** : côté client et serveur
- **Sélection de rôle** avec liste déroulante

**🎨 CSS intégré :** Aucun CSS intégré  
**📄 Feuilles de style :**
- `/admin.css` (principal)

**🔧 Variables utilisées :**
- `$error` - Message d'erreur
- `$success` - Message de succès
- `$csrf_token` - Token CSRF
- `$roles` - Liste des rôles disponibles
- `$_POST` - Valeurs du formulaire

**📝 Champs du formulaire :**
- **Nom d'utilisateur** : obligatoire, 3-20 caractères
- **Email** : obligatoire, validation email
- **Mot de passe** : obligatoire, minimum 8 caractères
- **Rôle** : sélection obligatoire parmi les rôles disponibles

**⚙️ JavaScript intégré :**
- **Validation côté client** complète
- **Vérification** des contraintes de longueur
- **Validation email** avec regex
- **Prévention soumission** si erreurs

**🎨 UX/UI :**
- **Messages d'aide** pour chaque champ
- **Validation en temps réel**
- **Feedback visuel** pour erreurs
- **Sections organisées** (base + permissions)

---

### **52. app/views/admin/users/edit.php**
**📍 Emplacement :** `/app/views/admin/users/edit.php`  
**🎯 Fonction :** Formulaire d'édition d'utilisateur avec informations système  
**🔗 Interactions :**
- Utilise `admin.css` pour le style
- Variables : `$user`, `$error`, `$success`, `$csrf_token`, `$roles`
- Formulaire POST vers `/users.php?action=edit&id={id}`

**⚙️ Fonctionnalités :**
- **Navigation** : retour à la liste
- **Messages** : erreurs et succès
- **Formulaire** : informations de base, rôle et permissions
- **Informations système** : ID, rôle actuel, dates, dernière connexion
- **Validation** : côté client et serveur
- **Mot de passe optionnel** (conservation si vide)

**🎨 CSS intégré :** Aucun CSS intégré  
**📄 Feuilles de style :**
- `/admin.css` (principal)

**🔧 Variables utilisées :**
- `$user` - Données utilisateur à éditer
- `$error` - Message d'erreur
- `$success` - Message de succès
- `$csrf_token` - Token CSRF
- `$roles` - Liste des rôles disponibles
- `$_POST` - Valeurs du formulaire (priorité sur objet)

**📝 Champs du formulaire :**
- **Nom d'utilisateur** : obligatoire, 3-20 caractères
- **Email** : obligatoire, validation email
- **Nouveau mot de passe** : optionnel, minimum 8 caractères
- **Rôle** : sélection obligatoire parmi les rôles disponibles

**📊 Informations système :**
- **ID** utilisateur
- **Rôle actuel** avec badge coloré
- **Date de création**
- **Dernière connexion**
- **Dernière modification** (si applicable)

**⚙️ JavaScript intégré :**
- **Validation côté client** complète
- **Vérification** des contraintes de longueur
- **Validation email** avec regex
- **Validation mot de passe** conditionnelle
- **Prévention soumission** si erreurs

**🎨 UX/UI :**
- **Pré-remplissage** des champs
- **Préservation** des valeurs POST en cas d'erreur
- **Messages d'aide** pour chaque champ
- **Sections organisées** (base + permissions + système)

---

### **53. app/views/admin/media/index.php**
**📍 Emplacement :** `/app/views/admin/media/index.php`  
**🎯 Fonction :** Bibliothèque de médias moderne avec upload, filtres et sélection  
**🔗 Interactions :**
- Utilise `admin.css` pour le style
- Utilise Font Awesome 6.4.0 pour les icônes
- Variables : `$medias`, `$totalMedias`, `$games`, `$currentPage`, `$totalPages`, `$selectMode`
- Actions AJAX pour upload et suppression

**⚙️ Fonctionnalités :**
- **Navigation** : retour dashboard
- **Upload moderne** : drag & drop, prévisualisation, catégories
- **Filtres avancés** : recherche, jeu, catégorie
- **Statistiques** : total médias, jeux disponibles, page actuelle
- **Grille de médias** : cartes avec prévisualisation
- **Mode sélection** : pour choisir images comme couvertures
- **Pagination** moderne
- **Actions** : copier URL, supprimer, sélectionner

**🎨 CSS intégré :** Styles complets avec variables CSS et glassmorphism  
**📄 Feuilles de style :**
- `/admin.css` (principal)
- `https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css`

**🔧 Variables utilisées :**
- `$medias` - Liste des médias
- `$totalMedias` - Nombre total de médias
- `$games` - Liste des jeux disponibles
- `$currentPage`, `$totalPages` - Pagination
- `$selectMode` - Mode sélection d'images

**🎨 Design moderne :**
- **Variables CSS** : couleurs Belgique (rouge, jaune, noir)
- **Glassmorphism** : effets de transparence et flou
- **Animations** : transitions fluides, hover effects
- **Responsive** : grille adaptative
- **Thème sombre** : arrière-plan dégradé

**📤 Upload avancé :**
- **Drag & drop** avec feedback visuel
- **Sélection de jeu** avec recherche en temps réel
- **Catégories** : screenshots, news, événements, autre
- **Prévisualisation** d'images
- **Validation** de format et taille

**🔍 Filtres intelligents :**
- **Recherche** par nom de fichier
- **Filtre par jeu** avec autocomplétion
- **Filtre par catégorie** avec sélecteur
- **Application en temps réel** des filtres

**⚙️ JavaScript intégré :**
- **Recherche de jeux** avec debouncing
- **Upload AJAX** avec progress
- **Filtres dynamiques** côté client
- **Sélection d'images** pour mode popup
- **Toast notifications** modernes
- **Gestion d'erreurs** complète

**🎯 Mode sélection :**
- **Popup** pour sélection d'images
- **Communication** avec fenêtre parent
- **Feedback visuel** pour sélection
- **Fermeture automatique** après sélection

**📱 Responsive :**
- **Grille adaptative** selon la taille d'écran
- **Filtres empilés** sur mobile
- **Cartes optimisées** pour mobile

---

### **54. app/views/admin/hardware/index.php**
**📍 Emplacement :** `/app/views/admin/hardware/index.php`  
**🎯 Fonction :** Liste et gestion des hardwares avec filtres par type  
**🔗 Interactions :**
- Utilise `admin.css` pour le style
- Variables : `$hardware`, `$totalHardware`, `$types`, `$search`, `$type`, `$currentPage`, `$totalPages`
- Actions AJAX pour suppression

**⚙️ Fonctionnalités :**
- **Navigation** : nouveau hardware, retour dashboard
- **Messages** : succès et erreurs via GET parameters
- **Statistiques** : total hardware, hardware actifs, avec jeux
- **Filtres** : recherche par nom/fabricant, filtre par type
- **Tableau** : ID, nom, type, fabricant, année, statut, jeux, actions
- **Pagination** avec paramètres de filtre
- **Suppression AJAX** avec vérification jeux associés

**🎨 CSS intégré :** Aucun CSS intégré  
**📄 Feuilles de style :**
- `/admin.css` (principal)

**🔧 Variables utilisées :**
- `$hardware` - Liste des hardwares
- `$totalHardware` - Nombre total de hardwares
- `$types` - Types de hardware disponibles
- `$search` - Terme de recherche
- `$type` - Type filtré
- `$currentPage`, `$totalPages` - Pagination

**🖥️ Gestion des hardwares :**
- **Badges de type** colorés
- **Statut actif/inactif** avec badges
- **Compteurs de jeux** associés
- **Informations détaillées** (nom, slug, fabricant, année)

**🔐 Sécurité :**
- **CSRF tokens** pour suppression
- **Vérification jeux** avant suppression
- **Confirmation** avant suppression

**⚙️ JavaScript intégré :**
- **Suppression AJAX** avec fetch API
- **Confirmation** avec détails des jeux
- **Gestion d'erreurs** complète
- **Rechargement** automatique après succès

**📱 Responsive :**
- **Tableau adaptatif**
- **Filtres empilés** sur mobile
- **Actions compactes**

---

### **55. app/views/admin/hardware/create.php**
**📍 Emplacement :** `/app/views/admin/hardware/create.php`  
**🎯 Fonction :** Formulaire de création de hardware avec validation  
**🔗 Interactions :**
- Utilise `admin.css` pour le style
- Variables : `$error`, `$csrf_token`, `$types`
- Formulaire POST vers `/hardware.php?action=store`

**⚙️ Fonctionnalités :**
- **Navigation** : retour à la liste
- **Messages** : erreurs
- **Formulaire complet** : informations de base, fabricant, description
- **Validation** : côté client et serveur
- **Génération automatique** du slug
- **Paramètres** : ordre de tri, statut actif

**🎨 CSS intégré :** Aucun CSS intégré  
**📄 Feuilles de style :**
- `/admin.css` (principal)

**🔧 Variables utilisées :**
- `$error` - Message d'erreur
- `$csrf_token` - Token CSRF
- `$types` - Types de hardware disponibles
- `$_POST` - Valeurs du formulaire

**📝 Sections du formulaire :**
- **Informations de base** : nom, slug, type
- **Fabricant** : fabricant, année de sortie
- **Description** : description, ordre de tri, statut actif

**⚙️ JavaScript intégré :**
- **Génération automatique** du slug
- **Validation côté client** complète
- **Vérification** des champs obligatoires
- **Prévention soumission** si erreurs

**🎨 UX/UI :**
- **Sections organisées** avec icônes
- **Messages d'aide** pour chaque champ
- **Validation en temps réel**
- **Champs conditionnels** (ordre de tri, statut)

---

### **64. app/views/hardware/show.php**
**📍 Emplacement :** `/app/views/hardware/show.php`  
**🎯 Fonction :** Affichage détaillé d'un hardware avec articles et jeux associés  
**🔗 Interactions :**
- Utilise `public.php` comme layout
- Variables : `$hardware`, `$articles`, `$games`
- Intégration avec `image.php` pour les images
- JavaScript pour recherche dynamique

**⚙️ Fonctionnalités :**
- **Informations hardware** : nom complet, fabricant, statistiques
- **Recherche dynamique** : barre de recherche avec résultats en temps réel
- **Articles associés** : format large (6 premiers) + compact (reste)
- **Jeux associés** : grille des jeux compatibles
- **État vide** : message si aucun contenu
- **Design responsive** : adaptation mobile

**🎨 CSS intégré :** Styles complets pour la page hardware individuelle  
**📄 Feuilles de style :**
- `/public/assets/css/main.css` (principal)
- Styles intégrés pour cartes et recherche

**🔧 Variables utilisées :**
- `$hardware` - Objet Hardware complet
- `$articles` - Tableau des articles associés
- `$games` - Tableau des jeux compatibles

**📱 JavaScript intégré :**
- Recherche dynamique avec debounce (300ms)
- Filtrage en temps réel des articles
- Mise en surbrillance des résultats
- Fermeture des résultats au clic extérieur

**🎨 CSS intégré détaillé :**
- `.hardware-info` - Bloc d'informations hardware (fond noir)
- `.search-section` - Barre de recherche avec résultats
- `.article-card-large` - Cartes d'articles principales
- `.article-card-small` - Cartes d'articles secondaires
- `.games-grid` - Grille des jeux associés
- `.empty-state` - État vide
- Responsive design complet

---

### **65. app/views/auth/login.php**
**📍 Emplacement :** `/app/views/auth/login.php`  
**🎯 Fonction :** Page de connexion avec design belge et validation  
**🔗 Interactions :**
- Page standalone (pas de layout)
- Variables : `$site_name`, `$error`, `$csrf_token`
- Formulaire POST vers `/login`
- Intégration avec modèle Setting pour inscriptions

**⚙️ Fonctionnalités :**
- **Design belge** : couleurs du drapeau, logo gaming
- **Formulaire connexion** : login/email + mot de passe
- **Messages d'erreur** : affichage des erreurs de connexion
- **Liens conditionnels** : inscription si autorisée
- **CSRF protection** : token de sécurité
- **Responsive design** : adaptation mobile

**🎨 CSS intégré :** Styles complets pour la page de connexion  
**📄 Feuilles de style :**
- Google Fonts (Inter)
- Styles intégrés complets

**🔧 Variables utilisées :**
- `$site_name` - Nom du site
- `$error` - Message d'erreur
- `$csrf_token` - Token CSRF
- `$_POST['login']` - Valeur du champ login

**📱 JavaScript intégré :** Aucun JavaScript intégré

**🎨 CSS intégré détaillé :**
- Variables CSS pour couleurs belges
- `.login-container` - Container principal avec backdrop-filter
- `.logo` - Logo avec icône gaming et drapeau belge
- `.form-group` - Champs de formulaire stylisés
- `.btn` - Bouton avec effets hover
- `.belgium-flag` - Drapeau belge en position absolue
- Responsive design complet

---

### **66. app/views/auth/register.php**
**📍 Emplacement :** `/app/views/auth/register.php`  
**🎯 Fonction :** Page d'inscription avec validation complète  
**🔗 Interactions :**
- Page standalone (pas de layout)
- Variables : `$site_name`, `$error`, `$success`, `$csrf_token`
- Formulaire POST vers `/auth/register`
- Liens vers conditions d'utilisation

**⚙️ Fonctionnalités :**
- **Formulaire complet** : login, email, mot de passe, confirmation
- **Validation côté client** : messages d'aide pour chaque champ
- **Checkbox obligatoire** : acceptation des conditions
- **Messages d'état** : erreurs et succès
- **Design belge** : cohérent avec la page de connexion
- **Responsive design** : adaptation mobile

**🎨 CSS intégré :** Styles complets pour la page d'inscription  
**📄 Feuilles de style :**
- Google Fonts (Inter)
- Styles intégrés complets

**🔧 Variables utilisées :**
- `$site_name` - Nom du site
- `$error` - Message d'erreur
- `$success` - Message de succès
- `$csrf_token` - Token CSRF
- `$_POST['login']`, `$_POST['email']` - Valeurs des champs

**📱 JavaScript intégré :** Aucun JavaScript intégré

**🎨 CSS intégré détaillé :**
- Variables CSS pour couleurs belges
- `.register-container` - Container principal (plus large que login)
- `.form-group` - Champs avec messages d'aide
- `.checkbox-group` - Checkbox avec liens vers conditions
- `.btn` - Bouton avec icône et effets
- `.belgium-flag` - Drapeau belge
- Responsive design complet

---

### **67. app/views/legal/cgu.php**
**📍 Emplacement :** `/app/views/legal/cgu.php`  
**🎯 Fonction :** Contenu des Conditions Générales d'Utilisation  
**🔗 Interactions :**
- Utilise `public.php` comme layout
- Variables : `$content` (contenu dynamique)
- Intégration avec système de settings

**⚙️ Fonctionnalités :**
- **Sections structurées** : acceptation, service, inscription, conduite
- **Règles détaillées** : modération, propriété intellectuelle
- **Limitation responsabilité** : clauses légales
- **Date de mise à jour** : dynamique
- **Contenu modifiable** : via admin settings

**🎨 CSS intégré :** Aucun CSS intégré  
**📄 Feuilles de style :**
- `/public/assets/css/main.css` (principal)
- Classes `.legal-section`, `.legal-footer`

**🔧 Variables utilisées :**
- `$content` - Contenu HTML des CGU (depuis settings)

**📱 JavaScript intégré :** Aucun JavaScript intégré

**🎨 CSS intégré détaillé :**
- Utilise les styles du layout public
- Sections avec titres h2
- Listes à puces pour les règles
- Footer avec date de mise à jour

---

### **68. app/views/legal/mentions-legales.php**
**📍 Emplacement :** `/app/views/legal/mentions-legales.php`  
**🎯 Fonction :** Contenu des mentions légales du site  
**🔗 Interactions :**
- Utilise `public.php` comme layout
- Variables : `$content` (contenu dynamique)
- Intégration avec système de settings

**⚙️ Fonctionnalités :**
- **Éditeur du site** : informations de contact et localisation
- **Hébergement** : informations sur l'hébergeur
- **Propriété intellectuelle** : droits d'auteur et protection
- **Responsabilité** : limitations de responsabilité
- **Liens externes** : politique sur les liens sortants
- **Droit applicable** : juridiction belge
- **Date de mise à jour** : dynamique

**🎨 CSS intégré :** Aucun CSS intégré  
**📄 Feuilles de style :**
- `/public/assets/css/main.css` (principal)
- Classes `.legal-section`, `.legal-footer`

**🔧 Variables utilisées :**
- `$content` - Contenu HTML des mentions légales (depuis settings)

**📱 JavaScript intégré :** Aucun JavaScript intégré

**🎨 CSS intégré détaillé :**
- Utilise les styles du layout public
- Sections avec titres h2
- Liens de contact stylisés
- Footer avec date de mise à jour

---

### **69. app/views/legal/politique-confidentialite.php**
**📍 Emplacement :** `/app/views/legal/politique-confidentialite.php`  
**🎯 Fonction :** Contenu de la politique de confidentialité RGPD  
**🔗 Interactions :**
- Utilise `public.php` comme layout
- Variables : `$content` (contenu dynamique)
- Intégration avec système de settings

**⚙️ Fonctionnalités :**
- **Collecte des données** : types de données collectées
- **Utilisation des données** : finalités du traitement
- **Conservation** : durée de conservation
- **Droits RGPD** : accès, rectification, effacement, portabilité, opposition
- **Cookies** : politique des cookies
- **Sécurité** : mesures de protection
- **Contact** : exercice des droits

**🎨 CSS intégré :** Aucun CSS intégré  
**📄 Feuilles de style :**
- `/public/assets/css/main.css` (principal)
- Classes `.legal-section`, `.legal-footer`

**🔧 Variables utilisées :**
- `$content` - Contenu HTML de la politique (depuis settings)

**📱 JavaScript intégré :** Aucun JavaScript intégré

**🎨 CSS intégré détaillé :**
- Utilise les styles du layout public
- Sections avec titres h2
- Listes à puces pour les droits
- Liens de contact stylisés
- Footer avec date de mise à jour

---

### **70. app/views/legal/cookies.php**
**📍 Emplacement :** `/app/views/legal/cookies.php`  
**🎯 Fonction :** Contenu de la politique des cookies  
**🔗 Interactions :**
- Utilise `public.php` comme layout
- Variables : `$content` (contenu dynamique)
- Intégration avec système de settings

**⚙️ Fonctionnalités :**
- **Définition cookies** : explication des cookies
- **Cookies utilisés** : session, préférences, techniques
- **Cookies non utilisés** : pas de tracking, publicité, réseaux sociaux
- **Durée de conservation** : règles de suppression
- **Gestion navigateurs** : instructions pour Chrome, Firefox, Safari, Edge
- **Conséquences désactivation** : impact sur le fonctionnement
- **Cookies tiers** : absence de cookies externes

**🎨 CSS intégré :** Aucun CSS intégré  
**📄 Feuilles de style :**
- `/public/assets/css/main.css` (principal)
- Classes `.legal-section`, `.legal-footer`

**🔧 Variables utilisées :**
- `$content` - Contenu HTML de la politique cookies (depuis settings)

**📱 JavaScript intégré :** Aucun JavaScript intégré

**🎨 CSS intégré détaillé :**
- Utilise les styles du layout public
- Sections avec titres h2 et h3
- Listes à puces détaillées
- Instructions par navigateur
- Footer avec date de mise à jour

---

### **71. app/views/chapters/show.php**
**📍 Emplacement :** `/app/views/chapters/show.php`  
**🎯 Fonction :** Affichage d'un chapitre de dossier avec navigation séquentielle  
**🔗 Interactions :**
- Utilise `public.php` comme layout
- Variables : `$chapter`, `$dossier`, `$allChapters`, `$currentChapterIndex`, `$previousChapter`, `$nextChapter`
- Intégration avec `ImageHelper`
- Navigation entre chapitres

**⚙️ Fonctionnalités :**
- **Métadonnées chapitre** : dossier, numéro, date, statut
- **Hero unifié** : image du dossier + titre du chapitre
- **Navigation séquentielle** : chapitre précédent/suivant
- **Contenu du chapitre** : HTML nettoyé et affiché
- **Navigation complète** : liste de tous les chapitres
- **Actions admin** : liens d'édition si connecté
- **Design responsive** : adaptation mobile

**🎨 CSS intégré :** Aucun CSS intégré  
**📄 Feuilles de style :**
- `/public/assets/css/main.css` (principal)
- `/public/assets/css/layout/public.css` (layout)

**🔧 Variables utilisées :**
- `$chapter` - Données du chapitre actuel
- `$dossier` - Objet Article (dossier parent)
- `$allChapters` - Tous les chapitres du dossier
- `$currentChapterIndex` - Index du chapitre actuel
- `$previousChapter` - Chapitre précédent (ou null)
- `$nextChapter` - Chapitre suivant (ou null)
- `$cleanedContent` - Contenu HTML nettoyé

**📱 JavaScript intégré :** Aucun JavaScript intégré

**🎨 CSS intégré détaillé :**
- Utilise les styles du layout public
- `.article-meta-section` - Métadonnées du chapitre
- `.article-hero-unified` - Hero avec image du dossier
- `.chapter-sequential-navigation` - Navigation séquentielle
- `.dossier-chapters-navigation-bottom` - Navigation complète
- `.current-chapter` - Indicateur du chapitre actuel
- Responsive design complet

---

### **72. app/views/components/navbar.php**
**📍 Emplacement :** `/app/views/components/navbar.php`  
**🎯 Fonction :** Composant de navigation principal avec menu déroulant hardware  
**🔗 Interactions :**
- Utilise le modèle `Hardware` pour le menu déroulant
- Variables : `$hardwares`, `$categories`
- Intégration avec JavaScript pour menu mobile
- Liens vers catégories et hardwares

**⚙️ Fonctionnalités :**
- **Menu Hardware** : dropdown avec tous les hardwares
- **Catégories principales** : actualités, tests, dossiers, trailers
- **Menu mobile** : hamburger avec navigation complète
- **Accessibilité** : attributs ARIA et rôles
- **Responsive design** : adaptation mobile
- **Navigation dynamique** : liens générés automatiquement

**🎨 CSS intégré :** Aucun CSS intégré  
**📄 Feuilles de style :**
- `/public/assets/css/main.css` (principal)
- Classes pour navbar, dropdown, mobile

**🔧 Variables utilisées :**
- `$hardwares` - Liste des hardwares pour le menu
- `$categories` - Catégories principales du site

**📱 JavaScript intégré :** Aucun JavaScript intégré (géré par navbar.js)

**🎨 CSS intégré détaillé :**
- Utilise les styles du layout public
- `.main-navbar` - Navigation principale
- `.dropdown-menu` - Menu déroulant hardware
- `.navbar-mobile` - Menu mobile
- `.hamburger-line` - Icône hamburger
- Responsive design complet

---

### **73. app/views/layout/403.php**
**📍 Emplacement :** `/app/views/layout/403.php`  
**🎯 Fonction :** Page d'erreur 403 (Accès interdit)  
**🔗 Interactions :**
- Page standalone (pas de layout)
- Variables : `$site_name`, `$message`
- Liens vers accueil et connexion

**⚙️ Fonctionnalités :**
- **Design belge** : couleurs du drapeau, icône cadenas
- **Message d'erreur** : personnalisable via variable
- **Actions utilisateur** : retour accueil, connexion
- **Responsive design** : adaptation mobile
- **Accessibilité** : structure sémantique

**🎨 CSS intégré :** Styles complets pour la page d'erreur  
**📄 Feuilles de style :**
- Google Fonts (Inter)
- Styles intégrés complets

**🔧 Variables utilisées :**
- `$site_name` - Nom du site
- `$message` - Message d'erreur personnalisé

**📱 JavaScript intégré :** Aucun JavaScript intégré

**🎨 CSS intégré détaillé :**
- Variables CSS pour couleurs belges
- `.error-container` - Container principal
- `.error-code` - Code d'erreur (403) avec effet glow
- `.error-title` - Titre de l'erreur
- `.error-message` - Message explicatif
- `.btn` - Boutons d'action avec effets hover
- `.lock-icon` - Icône cadenas
- Responsive design complet

---

### **74. app/views/layout/404.php**
**📍 Emplacement :** `/app/views/layout/404.php`  
**🎯 Fonction :** Page d'erreur 404 (Page non trouvée)  
**🔗 Interactions :**
- Page standalone (pas de layout)
- Variables : `$site_name`, `$message`
- Liens vers accueil et articles

**⚙️ Fonctionnalités :**
- **Design belge** : couleurs du drapeau, icône gaming
- **Message d'erreur** : personnalisable via variable
- **Actions utilisateur** : retour accueil, voir articles
- **Responsive design** : adaptation mobile
- **Accessibilité** : structure sémantique

**🎨 CSS intégré :** Styles complets pour la page d'erreur  
**📄 Feuilles de style :**
- Google Fonts (Inter)
- Styles intégrés complets

**🔧 Variables utilisées :**
- `$site_name` - Nom du site
- `$message` - Message d'erreur personnalisé

**📱 JavaScript intégré :** Aucun JavaScript intégré

**🎨 CSS intégré détaillé :**
- Variables CSS pour couleurs belges
- `.error-container` - Container principal
- `.error-code` - Code d'erreur (404) avec effet glow
- `.error-title` - Titre de l'erreur
- `.error-message` - Message explicatif
- `.btn` - Boutons d'action avec effets hover
- `.game-icon` - Icône gaming (🎮)
- Responsive design complet

---

### **75. app/views/layout/500.php**
**📍 Emplacement :** `/app/views/layout/500.php`  
**🎯 Fonction :** Page d'erreur 500 (Erreur serveur)  
**🔗 Interactions :**
- Page standalone (pas de layout)
- Variables : `$site_name`, `$message`
- Liens vers accueil et actualisation

**⚙️ Fonctionnalités :**
- **Design belge** : couleurs du drapeau, icône warning
- **Message d'erreur** : personnalisable via variable
- **Actions utilisateur** : retour accueil, actualiser
- **Responsive design** : adaptation mobile
- **Accessibilité** : structure sémantique

**🎨 CSS intégré :** Styles complets pour la page d'erreur  
**📄 Feuilles de style :**
- Google Fonts (Inter)
- Styles intégrés complets

**🔧 Variables utilisées :**
- `$site_name` - Nom du site
- `$message` - Message d'erreur personnalisé

**📱 JavaScript intégré :** Aucun JavaScript intégré

**🎨 CSS intégré détaillé :**
- Variables CSS pour couleurs belges
- `.error-container` - Container principal
- `.error-code` - Code d'erreur (500) avec effet glow
- `.error-title` - Titre de l'erreur
- `.error-message` - Message explicatif
- `.btn` - Boutons d'action avec effets hover
- `.error-icon` - Icône warning (⚠️)
- Responsive design complet

---

## **ASSETS CSS/JS**

### **76. public/assets/css/main.css**
**📍 Emplacement :** `/public/assets/css/main.css`  
**🎯 Fonction :** Feuille de style principale avec imports modulaires  
**🔗 Interactions :**
- Importe tous les fichiers CSS modulaires
- Utilisé par tous les layouts publics
- Variables CSS centralisées

**⚙️ Fonctionnalités :**
- **Imports base** : variables, reset, typography
- **Imports composants** : buttons, image-module, article-display, typography-override
- **Imports layout** : grid, header, footer
- **Imports pages** : auth, admin, home
- **Styles globaux** : container, responsive
- **Architecture modulaire** : séparation des préoccupations

**🎨 CSS intégré :** Styles globaux et container principal  
**📄 Feuilles de style :**
- Imports de tous les modules CSS
- Variables CSS centralisées
- Container principal responsive

**🔧 Variables utilisées :**
- `--container-max-width` - Largeur max du container
- `--spacing-md` - Espacement moyen
- `--container-max-width-lg` - Largeur max large

**📱 JavaScript intégré :** Aucun JavaScript intégré

**🎨 CSS intégré détaillé :**
- `.container` - Container principal responsive
- `.container-lg` - Container large
- Imports modulaires pour organisation
- Architecture CSS scalable

---

### **77. public/assets/js/navbar.js**
**📍 Emplacement :** `/public/assets/js/navbar.js`  
**🎯 Fonction :** Gestionnaire JavaScript pour la navigation  
**🔗 Interactions :**
- Utilise les classes CSS de la navbar
- Gère les événements utilisateur
- Intégration avec accessibilité

**⚙️ Fonctionnalités :**
- **Menu mobile** : toggle hamburger, fermeture automatique
- **Dropdown hardware** : ouverture/fermeture, navigation clavier
- **Accessibilité** : ARIA, navigation clavier, focus management
- **Détection page active** : surlignage automatique
- **Gestion resize** : fermeture menu mobile sur desktop
- **Performance** : lazy loading, debounce
- **Gestion erreurs** : try/catch, logs debug

**🎨 CSS intégré :** Aucun CSS intégré  
**📄 Feuilles de style :**
- Utilise les classes CSS existantes
- Gestion des états (active, loaded)

**🔧 Variables utilisées :**
- Classes CSS : `.navbar-toggle`, `.navbar-mobile`, `.dropdown`
- Événements : click, keydown, resize
- États : active, loaded

**📱 JavaScript intégré :**
- `closeMobileMenu()` - Fermer menu mobile
- `toggleDropdown()` - Basculer dropdown
- `openDropdown()` - Ouvrir dropdown
- `closeDropdown()` - Fermer dropdown
- `highlightActivePage()` - Surligner page active
- `closeAllMenus()` - Fermer tous les menus
- `openMobileMenu()` - Ouvrir menu mobile

**🎨 CSS intégré détaillé :**
- Gestion des classes CSS dynamiques
- États d'accessibilité (aria-expanded)
- Responsive design avec JavaScript
- Performance optimisée

---

### **78. public/assets/js/gallery-lightbox.js**
**📍 Emplacement :** `/public/assets/js/gallery-lightbox.js`  
**🎯 Fonction :** Gestionnaire de galeries avec sliders et lightbox  
**🔗 Interactions :**
- Utilise les classes CSS de galerie
- Gère les événements d'images
- Intégration avec DOM dynamique

**⚙️ Fonctionnalités :**
- **GallerySlider** : sliders, carousels, masonry
- **GalleryLightbox** : modal d'agrandissement d'images
- **Navigation clavier** : flèches, échap
- **Responsive** : adaptation mobile
- **Performance** : lazy loading, debounce
- **DOM dynamique** : MutationObserver pour contenu ajouté
- **Gestion erreurs** : try/catch complet

**🎨 CSS intégré :** Aucun CSS intégré  
**📄 Feuilles de style :**
- Utilise les classes CSS existantes
- Gestion des états (active, loaded)

**🔧 Variables utilisées :**
- Classes CSS : `.gallery-slider`, `.gallery-carousel`, `.gallery-masonry`
- Événements : click, keydown, load, resize
- États : active, loaded, initialized

**📱 JavaScript intégré :**
- `GallerySlider` - Classe pour sliders/carousels/masonry
- `GalleryLightbox` - Classe pour lightbox
- `initGalleryLightbox()` - Fonction d'initialisation
- Navigation clavier complète
- Gestion des images lazy loading

**🎨 CSS intégré détaillé :**
- Gestion des classes CSS dynamiques
- États de galerie (active, loaded)
- Responsive design avec JavaScript
- Performance optimisée avec observers

---

### **79. public/js/wysiwyg-editor.js**
**📍 Emplacement :** `/public/js/wysiwyg-editor.js`  
**🎯 Fonction :** Éditeur WYSIWYG maison sans dépendances  
**🔗 Interactions :**
- Utilise les classes CSS de l'éditeur
- Gère les commandes de formatage
- Intégration avec formulaires

**⚙️ Fonctionnalités :**
- **Toolbar complète** : gras, italique, souligné, titres, listes
- **Commandes clavier** : Ctrl+B, Ctrl+I, Ctrl+U
- **Formatage avancé** : liens, images, citations
- **Gestion contenu** : HTML propre, validation
- **Accessibilité** : navigation clavier, ARIA
- **Responsive** : adaptation mobile
- **Performance** : événements optimisés

**🎨 CSS intégré :** Aucun CSS intégré  
**📄 Feuilles de style :**
- Utilise les classes CSS existantes
- Gestion des états (active, disabled)

**🔧 Variables utilisées :**
- Classes CSS : `.wysiwyg-toolbar`, `.wysiwyg-editor`
- Événements : click, keydown, input
- États : active, disabled, focused

**📱 JavaScript intégré :**
- `WysiwygEditor` - Classe principale
- `createToolbar()` - Création de la barre d'outils
- `createEditor()` - Création de l'éditeur
- `bindEvents()` - Liaison des événements
- `setupCommands()` - Configuration des commandes
- Gestion complète des raccourcis clavier

**🎨 CSS intégré détaillé :**
- Gestion des classes CSS dynamiques
- États de l'éditeur (active, disabled)
- Responsive design avec JavaScript
- Interface utilisateur complète

---

## **FICHIERS DE ROUTAGE**

### **80. index.php (racine)**
**📍 Emplacement :** `/index.php`  
**🎯 Fonction :** Point d'entrée principal avec redirection vers public  
**🔗 Interactions :**
- Redirige vers `/public/index.php`
- Gère les fichiers statiques
- Définit les types MIME

**⚙️ Fonctionnalités :**
- **Redirection racine** : vers page d'accueil
- **Gestion fichiers statiques** : CSS, JS, images
- **Types MIME** : définition des types de fichiers
- **Routeur principal** : délègue au public/index.php
- **Sécurité** : validation des extensions

**🎨 CSS intégré :** Aucun CSS intégré  
**📄 Feuilles de style :**
- Aucune feuille de style

**🔧 Variables utilisées :**
- `$_SERVER['REQUEST_URI']` - URI de la requête
- `$request_uri` - URI nettoyée
- `$public_path` - Chemin vers le dossier public
- `$mime_types` - Types MIME supportés

**📱 JavaScript intégré :** Aucun JavaScript intégré

**🎨 CSS intégré détaillé :**
- Aucun CSS intégré
- Fichier de routage pur

---

### **81. articles.php**
**📍 Emplacement :** `/articles.php`  
**🎯 Fonction :** Routeur temporaire pour la gestion des articles  
**🔗 Interactions :**
- Simule l'URI `/admin/articles`
- Délègue au routeur principal
- Fichier temporaire (à supprimer)

**⚙️ Fonctionnalités :**
- **Simulation URI** : `/admin/articles`
- **Délégation** : vers public/index.php
- **Temporaire** : en attente de configuration .htaccess
- **Simplicité** : redirection directe

**🎨 CSS intégré :** Aucun CSS intégré  
**📄 Feuilles de style :**
- Aucune feuille de style

**🔧 Variables utilisées :**
- `$_SERVER['REQUEST_URI']` - URI simulée

**📱 JavaScript intégré :** Aucun JavaScript intégré

**🎨 CSS intégré détaillé :**
- Aucun CSS intégré
- Fichier de routage temporaire

---

### **82. games.php**
**📍 Emplacement :** `/games.php`  
**🎯 Fonction :** Routeur pour la gestion des jeux avec actions CRUD  
**🔗 Interactions :**
- Utilise les modèles Game et Auth
- Gère les actions AJAX
- Délègue au routeur principal

**⚙️ Fonctionnalités :**
- **Actions CRUD** : index, create, store, edit, update, delete
- **Suppression AJAX** : traitement direct avec CSRF
- **Gestion erreurs** : logs détaillés et codes HTTP
- **Sécurité** : vérification CSRF obligatoire
- **Simulation URI** : construction d'URLs pour le routeur
- **Session** : démarrage automatique si nécessaire

**🎨 CSS intégré :** Aucun CSS intégré  
**📄 Feuilles de style :**
- Aucune feuille de style

**🔧 Variables utilisées :**
- `$action` - Action à effectuer
- `$id` - ID de l'élément
- `$_POST['csrf_token']` - Token CSRF
- `$_SESSION['csrf_token']` - Token en session

**📱 JavaScript intégré :** Aucun JavaScript intégré

**🎨 CSS intégré détaillé :**
- Aucun CSS intégré
- Fichier de routage avec logique métier

---

### **83. hardware.php**
**📍 Emplacement :** `/hardware.php`  
**🎯 Fonction :** Routeur pour la gestion des hardware avec actions CRUD  
**🔗 Interactions :**
- Utilise les modèles Hardware et Auth
- Gère les actions AJAX
- Délègue au routeur principal

**⚙️ Fonctionnalités :**
- **Actions CRUD** : index, create, store, edit, update, delete
- **Suppression AJAX** : traitement direct avec CSRF
- **Gestion erreurs** : codes HTTP appropriés
- **Sécurité** : vérification CSRF obligatoire
- **Simulation URI** : construction d'URLs pour le routeur
- **Session** : démarrage automatique si nécessaire

**🎨 CSS intégré :** Aucun CSS intégré  
**📄 Feuilles de style :**
- Aucune feuille de style

**🔧 Variables utilisées :**
- `$action` - Action à effectuer
- `$id` - ID de l'élément
- `$_POST['csrf_token']` - Token CSRF
- `$_SESSION['csrf_token']` - Token en session

**📱 JavaScript intégré :** Aucun JavaScript intégré

**🎨 CSS intégré détaillé :**
- Aucun CSS intégré
- Fichier de routage avec logique métier

---

### **84. categories.php**
**📍 Emplacement :** `/categories.php`  
**🎯 Fonction :** Routeur pour la gestion des catégories avec actions CRUD  
**🔗 Interactions :**
- Utilise les modèles Category et Auth
- Gère les actions AJAX
- Délègue au routeur principal

**⚙️ Fonctionnalités :**
- **Actions CRUD** : index, create, store, edit, update, delete
- **Suppression AJAX** : traitement direct avec CSRF
- **Gestion erreurs** : logs détaillés et codes HTTP
- **Sécurité** : vérification CSRF obligatoire
- **Simulation URI** : construction d'URLs pour le routeur
- **Session** : démarrage automatique si nécessaire

**🎨 CSS intégré :** Aucun CSS intégré  
**📄 Feuilles de style :**
- Aucune feuille de style

**🔧 Variables utilisées :**
- `$action` - Action à effectuer
- `$id` - ID de l'élément
- `$_POST['csrf_token']` - Token CSRF
- `$_SESSION['csrf_token']` - Token en session

**📱 JavaScript intégré :** Aucun JavaScript intégré

**🎨 CSS intégré détaillé :**
- Aucun CSS intégré
- Fichier de routage avec logique métier

---

### **85. users.php**
**📍 Emplacement :** `/users.php`  
**🎯 Fonction :** Routeur pour la gestion des utilisateurs  
**🔗 Interactions :**
- Délègue au routeur principal
- Simule les URLs admin/users

**⚙️ Fonctionnalités :**
- **Actions CRUD** : index, create, store, edit, update, delete
- **Simulation URI** : construction d'URLs pour le routeur
- **Simplicité** : redirection directe sans logique métier
- **Paramètres** : gestion des actions et IDs

**🎨 CSS intégré :** Aucun CSS intégré  
**📄 Feuilles de style :**
- Aucune feuille de style

**🔧 Variables utilisées :**
- `$action` - Action à effectuer
- `$id` - ID de l'élément
- `$simulatedUrl` - URL simulée

**📱 JavaScript intégré :** Aucun JavaScript intégré

**🎨 CSS intégré détaillé :**
- Aucun CSS intégré
- Fichier de routage simple

---

### **86. media.php**
**📍 Emplacement :** `/media.php`  
**🎯 Fonction :** Routeur pour la gestion des médias avec contrôleur direct  
**🔗 Interactions :**
- Utilise MediaController directement
- Gère les headers de sécurité
- Vérifie l'authentification

**⚙️ Fonctionnalités :**
- **Actions média** : index, upload, delete, search, search-games
- **Actions avancées** : by-type, get, get-games
- **Sécurité** : headers de sécurité, authentification
- **Gestion erreurs** : codes HTTP appropriés
- **Contrôleur direct** : instanciation directe du contrôleur
- **Session** : démarrage automatique

**🎨 CSS intégré :** Aucun CSS intégré  
**📄 Feuilles de style :**
- Aucune feuille de style

**🔧 Variables utilisées :**
- `$action` - Action à effectuer
- `$id` - ID de l'élément
- `$controller` - Instance du contrôleur

**📱 JavaScript intégré :** Aucun JavaScript intégré

**🎨 CSS intégré détaillé :**
- Aucun CSS intégré
- Fichier de routage avec contrôleur direct

---

### **87. tags.php**
**📍 Emplacement :** `/tags.php`  
**🎯 Fonction :** Routeur pour la gestion des tags avec actions CRUD et recherche  
**🔗 Interactions :**
- Utilise les modèles Tag et Auth
- Gère les actions AJAX
- Délègue au routeur principal

**⚙️ Fonctionnalités :**
- **Actions CRUD** : index, create, store, edit, update, delete
- **Suppression AJAX** : traitement direct avec CSRF
- **Recherche AJAX** : recherche de tags en temps réel
- **Gestion erreurs** : logs détaillés et codes HTTP
- **Sécurité** : vérification CSRF obligatoire
- **Simulation URI** : construction d'URLs pour le routeur
- **Session** : démarrage automatique si nécessaire

**🎨 CSS intégré :** Aucun CSS intégré  
**📄 Feuilles de style :**
- Aucune feuille de style

**🔧 Variables utilisées :**
- `$action` - Action à effectuer
- `$id` - ID de l'élément
- `$query` - Terme de recherche
- `$limit` - Limite de résultats
- `$_POST['csrf_token']` - Token CSRF

**📱 JavaScript intégré :** Aucun JavaScript intégré

**🎨 CSS intégré détaillé :**
- Aucun CSS intégré
- Fichier de routage avec logique métier avancée

---

## **FICHIERS UTILITAIRES**

### **88. public/index.php**
**📍 Emplacement :** `/public/index.php`  
**🎯 Fonction :** Point d'entrée principal avec routeur complet  
**🔗 Interactions :**
- Utilise tous les modèles et contrôleurs
- Gère les headers de sécurité
- Vérifie le mode maintenance
- Routeur complet de l'application

**⚙️ Fonctionnalités :**
- **Headers de sécurité** : CSP, XSS, CSRF protection
- **Mode maintenance** : vérification et exclusion routes admin
- **Autoloader** : chargement automatique des classes
- **Gestion erreurs** : affichage conditionnel selon environnement
- **Routeur complet** : gestion de toutes les routes
- **Sécurité** : vérification des fichiers, validation des paramètres
- **Performance** : cache des fichiers statiques

**🎨 CSS intégré :** Aucun CSS intégré  
**📄 Feuilles de style :**
- Aucune feuille de style

**🔧 Variables utilisées :**
- `$requestUri` - URI de la requête
- `$route` - Route résolue
- `$controller` - Contrôleur instancié
- `$action` - Action à exécuter
- `$params` - Paramètres de l'action

**📱 JavaScript intégré :** Aucun JavaScript intégré

**🎨 CSS intégré détaillé :**
- Aucun CSS intégré
- Fichier de routage principal

---

### **89. public/security-headers.php**
**📍 Emplacement :** `/public/security-headers.php`  
**🎯 Fonction :** Configuration des headers de sécurité  
**🔗 Interactions :**
- Inclus dans public/index.php
- Définit les politiques de sécurité
- Gère le cache des fichiers

**⚙️ Fonctionnalités :**
- **Headers de sécurité** : X-Content-Type-Options, X-Frame-Options, X-XSS-Protection
- **Content Security Policy** : CSP avec support YouTube
- **Politique de permissions** : restrictions géolocalisation, microphone, caméra
- **Gestion cache** : cache 1 an pour fichiers statiques
- **Sécurité** : protection contre les attaques XSS, clickjacking
- **Performance** : optimisation du cache

**🎨 CSS intégré :** Aucun CSS intégré  
**📄 Feuilles de style :**
- Aucune feuille de style

**🔧 Variables utilisées :**
- `$csp` - Content Security Policy
- `$requestUri` - URI de la requête
- `$extension` - Extension du fichier

**📱 JavaScript intégré :** Aucun JavaScript intégré

**🎨 CSS intégré détaillé :**
- Aucun CSS intégré
- Fichier de configuration de sécurité

---

### **90. public/uploads.php**
**📍 Emplacement :** `/public/uploads.php`  
**🎯 Fonction :** Serveur sécurisé pour les fichiers uploadés  
**🔗 Interactions :**
- Utilise les paramètres GET
- Gère les types MIME
- Vérifie la sécurité des chemins

**⚙️ Fonctionnalités :**
- **Sécurité** : protection contre les attaques de traversée de répertoire
- **Validation** : vérification de l'existence et du type de fichier
- **Types MIME** : support images, PDF, texte
- **Cache** : cache 1 an pour les fichiers
- **Performance** : lecture directe des fichiers
- **Sécurité** : vérification des chemins réels

**🎨 CSS intégré :** Aucun CSS intégré  
**📄 Feuilles de style :**
- Aucune feuille de style

**🔧 Variables utilisées :**
- `$requestedFile` - Fichier demandé
- `$filePath` - Chemin vers le fichier
- `$realPath` - Chemin réel du fichier
- `$mimeType` - Type MIME du fichier

**📱 JavaScript intégré :** Aucun JavaScript intégré

**🎨 CSS intégré détaillé :**
- Aucun CSS intégré
- Fichier de service sécurisé

---

### **91. image.php**
**📍 Emplacement :** `/image.php`  
**🎯 Fonction :** Serveur d'images avec sécurité  
**🔗 Interactions :**
- Utilise les paramètres GET
- Gère les types MIME d'images
- Vérifie la sécurité des chemins

**⚙️ Fonctionnalités :**
- **Sécurité** : protection contre les attaques de traversée de répertoire
- **Validation** : vérification de l'existence et du type de fichier
- **Types MIME** : support images uniquement (JPG, PNG, GIF, WebP)
- **Cache** : cache 1 an pour les images
- **Performance** : lecture directe des fichiers
- **Sécurité** : vérification des chemins réels

**🎨 CSS intégré :** Aucun CSS intégré  
**📄 Feuilles de style :**
- Aucune feuille de style

**🔧 Variables utilisées :**
- `$filename` - Nom du fichier
- `$filePath` - Chemin vers le fichier
- `$realPath` - Chemin réel du fichier
- `$mimeType` - Type MIME du fichier

**📱 JavaScript intégré :** Aucun JavaScript intégré

**🎨 CSS intégré détaillé :**
- Aucun CSS intégré
- Fichier de service d'images sécurisé

---

## **FICHIERS DE CONFIGURATION**

### **92. config/config.php**
**📍 Emplacement :** `/config/config.php`  
**🎯 Fonction :** Configuration principale de l'application  
**🔗 Interactions :**
- Lit le fichier .env
- Utilisé par tous les autres fichiers
- Définit les valeurs par défaut

**⚙️ Fonctionnalités :**
- **Chargement .env** : lecture des variables d'environnement
- **Valeurs par défaut** : configuration de base
- **Méthodes utilitaires** : get, isLocal, isProduction
- **Sécurité** : secrets pour sessions et CSRF
- **Base de données** : configuration DB
- **Uploads** : taille max et extensions autorisées
- **Site** : nom et tagline

**🎨 CSS intégré :** Aucun CSS intégré  
**📄 Feuilles de style :**
- Aucune feuille de style

**🔧 Variables utilisées :**
- `$config` - Tableau de configuration
- `$envFile` - Chemin vers le fichier .env
- `$lines` - Lignes du fichier .env

**📱 JavaScript intégré :** Aucun JavaScript intégré

**🎨 CSS intégré détaillé :**
- Aucun CSS intégré
- Fichier de configuration PHP

---

### **93. robots.txt**
**📍 Emplacement :** `/robots.txt`  
**🎯 Fonction :** Instructions pour les robots d'indexation  
**🔗 Interactions :**
- Utilisé par les moteurs de recherche
- Référence le sitemap.xml
- Définit les restrictions d'accès

**⚙️ Fonctionnalités :**
- **User-agent** : règles pour tous les robots
- **Disallow** : restrictions d'accès aux dossiers sensibles
- **Sitemap** : référence vers le sitemap XML
- **Sécurité** : protection des dossiers admin et config
- **SEO** : optimisation pour l'indexation

**🎨 CSS intégré :** Aucun CSS intégré  
**📄 Feuilles de style :**
- Aucune feuille de style

**🔧 Variables utilisées :**
- Aucune variable utilisée

**📱 JavaScript intégré :** Aucun JavaScript intégré

**🎨 CSS intégré détaillé :**
- Aucun CSS intégré
- Fichier de configuration pour robots

---

### **94. sitemap.xml**
**📍 Emplacement :** `/sitemap.xml`  
**🎯 Fonction :** Plan du site pour les moteurs de recherche  
**🔗 Interactions :**
- Utilisé par les moteurs de recherche
- Référencé dans robots.txt
- Généré dynamiquement

**⚙️ Fonctionnalités :**
- **URLs principales** : page d'accueil avec priorité 1.0
- **Articles** : URLs des articles avec priorité 0.8
- **Jeux** : URLs des jeux avec priorité 0.7
- **Catégories** : URLs des catégories avec priorité 0.6
- **Métadonnées** : changefreq, lastmod, priority
- **SEO** : optimisation pour l'indexation

**🎨 CSS intégré :** Aucun CSS intégré  
**📄 Feuilles de style :**
- Aucune feuille de style

**🔧 Variables utilisées :**
- Aucune variable utilisée

**📱 JavaScript intégré :** Aucun JavaScript intégré

**🎨 CSS intégré détaillé :**
- Aucun CSS intégré
- Fichier XML de sitemap

---

### **95. update-sitemap.php**
**📍 Emplacement :** `/update-sitemap.php`  
**🎯 Fonction :** Script de mise à jour du sitemap et robots.txt  
**🔗 Interactions :**
- Utilise SeoHelper pour générer le sitemap
- Met à jour robots.txt
- Peut être exécuté via cron

**⚙️ Fonctionnalités :**
- **Génération sitemap** : création dynamique du sitemap
- **Mise à jour robots.txt** : génération du fichier robots
- **Statistiques** : affichage du nombre d'URLs
- **Gestion erreurs** : try/catch avec messages
- **Cron** : peut être exécuté automatiquement
- **Logs** : affichage des résultats

**🎨 CSS intégré :** Aucun CSS intégré  
**📄 Feuilles de style :**
- Aucune feuille de style

**🔧 Variables utilisées :**
- `$sitemap` - Contenu du sitemap généré
- `$robots` - Contenu du robots.txt généré
- `$xml` - Objet XML du sitemap

**📱 JavaScript intégré :** Aucun JavaScript intégré

**🎨 CSS intégré détaillé :**
- Aucun CSS intégré
- Script de maintenance SEO

---

## **FICHIERS DE BASE DE DONNÉES**

### **96. database/schema.sql**
**📍 Emplacement :** `/database/schema.sql`  
**🎯 Fonction :** Schéma de base de données principal  
**🔗 Interactions :**
- Crée la base de données
- Définit toutes les tables
- Utilisé par les migrations

**⚙️ Fonctionnalités :**
- **Base de données** : création avec charset UTF8MB4
- **Tables utilisateurs** : users, roles avec relations
- **Tables contenu** : categories, articles, tags
- **Tables jeux** : games, genres, hardware
- **Tables média** : media, uploads
- **Tables paramètres** : settings, configurations
- **Relations** : clés étrangères et contraintes
- **Index** : optimisation des performances

**🎨 CSS intégré :** Aucun CSS intégré  
**📄 Feuilles de style :**
- Aucune feuille de style

**🔧 Variables utilisées :**
- Aucune variable utilisée

**📱 JavaScript intégré :** Aucun JavaScript intégré

**🎨 CSS intégré détaillé :**
- Aucun CSS intégré
- Fichier SQL de schéma

---

### **97. database/seeds.sql**
**📍 Emplacement :** `/database/seeds.sql`  
**🎯 Fonction :** Données de démonstration et paramètres par défaut  
**🔗 Interactions :**
- Utilise le schéma de base
- Insère les données initiales
- Configure l'application

**⚙️ Fonctionnalités :**
- **Utilisateur admin** : compte administrateur par défaut
- **Paramètres site** : nom, tagline, description, contact
- **Catégories** : catégories par défaut (Actualités, Tests, Guides, etc.)
- **Tags** : tags par défaut (Nintendo, PlayStation, Xbox, etc.)
- **Configuration** : paramètres de base de l'application
- **Données test** : contenu de démonstration

**🎨 CSS intégré :** Aucun CSS intégré  
**📄 Feuilles de style :**
- Aucune feuille de style

**🔧 Variables utilisées :**
- Aucune variable utilisée

**📱 JavaScript intégré :** Aucun JavaScript intégré

**🎨 CSS intégré détaillé :**
- Aucun CSS intégré
- Fichier SQL de données

---

### **98. database/init_settings.sql**
**📍 Emplacement :** `/database/init_settings.sql`  
**🎯 Fonction :** Initialisation des paramètres de l'application  
**🔗 Interactions :**
- Crée la table settings
- Insère les paramètres par défaut
- Utilisé par l'application

**⚙️ Fonctionnalités :**
- **Table settings** : création avec structure complète
- **Paramètres par défaut** : allow_registration, dark_mode, maintenance_mode, default_theme
- **Index** : optimisation des requêtes sur la clé
- **Sécurité** : INSERT IGNORE pour éviter les doublons
- **Timestamps** : created_at et updated_at automatiques
- **Description** : documentation des paramètres

**🎨 CSS intégré :** Aucun CSS intégré  
**📄 Feuilles de style :**
- Aucune feuille de style

**🔧 Variables utilisées :**
- Aucune variable utilisée

**📱 JavaScript intégré :** Aucun JavaScript intégré

**🎨 CSS intégré détaillé :**
- Aucun CSS intégré
- Fichier SQL d'initialisation

---

### **99. database/create_genres_table.sql**
**📍 Emplacement :** `/database/create_genres_table.sql`  
**🎯 Fonction :** Création de la table des genres de jeux  
**🔗 Interactions :**
- Crée la table genres
- Insère les genres par défaut
- Utilisé par l'application

**⚙️ Fonctionnalités :**
- **Table genres** : création avec structure complète
- **Genres par défaut** : Action, Aventure, RPG, Stratégie, etc.
- **Couleurs** : couleurs associées à chaque genre
- **Descriptions** : descriptions détaillées des genres
- **Index** : clé unique sur le nom
- **Charset** : UTF8MB4 pour support Unicode complet

**🎨 CSS intégré :** Aucun CSS intégré  
**📄 Feuilles de style :**
- Aucune feuille de style

**🔧 Variables utilisées :**
- Aucune variable utilisée

**📱 JavaScript intégré :** Aucun JavaScript intégré

**🎨 CSS intégré détaillé :**
- Aucun CSS intégré
- Fichier SQL de création de table

---

### **100. database/update_games_table.sql**
**📍 Emplacement :** `/database/update_games_table.sql`  
**🎯 Fonction :** Mise à jour de la table games avec nouveaux champs  
**🔗 Interactions :**
- Modifie la table games existante
- Ajoute des champs pour les tests et métadonnées
- Optimise les performances avec des index

**⚙️ Fonctionnalités :**
- **Nouveaux champs** : score, is_tested, developer, publisher, pegi_rating
- **Suppression** : champ genre redondant
- **Index** : optimisation des requêtes sur score, is_tested, pegi_rating
- **Mise à jour** : marque les jeux avec score comme testés
- **Documentation** : commentaires détaillés des champs
- **Performance** : index pour les requêtes fréquentes

**🎨 CSS intégré :** Aucun CSS intégré  
**📄 Feuilles de style :**
- Aucune feuille de style

**🔧 Variables utilisées :**
- Aucune variable utilisée

**📱 JavaScript intégré :** Aucun JavaScript intégré

**🎨 CSS intégré détaillé :**
- Aucun CSS intégré
- Fichier SQL de migration

---

### **101. database/update_hardware_table.sql**
**📍 Emplacement :** `/database/update_hardware_table.sql`  
**🎯 Fonction :** Création et configuration de la table hardware  
**🔗 Interactions :**
- Crée la table hardware
- Insère les plateformes par défaut
- Modifie la table games pour ajouter hardware_id

**⚙️ Fonctionnalités :**
- **Table hardware** : création avec structure complète
- **Types** : console, pc, mobile, other
- **Plateformes par défaut** : PC, PlayStation, Xbox, Nintendo, Mobile
- **Relations** : clé étrangère vers games
- **Index** : optimisation des requêtes par type et statut
- **Tri** : sort_order pour l'ordre d'affichage

**🎨 CSS intégré :** Aucun CSS intégré  
**📄 Feuilles de style :**
- Aucune feuille de style

**🔧 Variables utilisées :**
- Aucune variable utilisée

**📱 JavaScript intégré :** Aucun JavaScript intégré

**🎨 CSS intégré détaillé :**
- Aucun CSS intégré
- Fichier SQL de migration

---

### **102. database/update_media_table.sql**
**📍 Emplacement :** `/database/update_media_table.sql`  
**🎯 Fonction :** Mise à jour de la table media pour les jeux  
**🔗 Interactions :**
- Modifie la table media existante
- Ajoute des relations vers les jeux
- Optimise les performances avec des index

**⚙️ Fonctionnalités :**
- **Relation jeux** : ajout de game_id avec clé étrangère
- **Types de média** : cover, screenshot, artwork, other
- **Index** : optimisation des requêtes par jeu et type
- **Cascade** : suppression en cascade des médias
- **Performance** : index pour les requêtes fréquentes
- **Flexibilité** : support de différents types de médias

**🎨 CSS intégré :** Aucun CSS intégré  
**📄 Feuilles de style :**
- Aucune feuille de style

**🔧 Variables utilisées :**
- Aucune variable utilisée

**📱 JavaScript intégré :** Aucun JavaScript intégré

**🎨 CSS intégré détaillé :**
- Aucun CSS intégré
- Fichier SQL de migration

---

### **103. database/update_users_table.sql**
**📍 Emplacement :** `/database/update_users_table.sql`  
**🎯 Fonction :** Mise à jour de la table users avec champ is_active  
**🔗 Interactions :**
- Modifie la table users existante
- Ajoute le champ is_active
- Met à jour les utilisateurs existants

**⚙️ Fonctionnalités :**
- **Champ is_active** : ajout avec valeur par défaut TRUE
- **Mise à jour** : marque tous les utilisateurs existants comme actifs
- **Vérification** : DESCRIBE pour confirmer la modification
- **Sécurité** : IF NOT EXISTS pour éviter les erreurs
- **Compatibilité** : support des versions MySQL récentes
- **Documentation** : commentaires explicatifs

**🎨 CSS intégré :** Aucun CSS intégré  
**📄 Feuilles de style :**
- Aucune feuille de style

**🔧 Variables utilisées :**
- Aucune variable utilisée

**📱 JavaScript intégré :** Aucun JavaScript intégré

**🎨 CSS intégré détaillé :**
- Aucun CSS intégré
- Fichier SQL de migration

---

## **FICHIERS DE CONFIGURATION RESTANTS**

### **104. config/env.example**
**📍 Emplacement :** `/config/env.example`  
**🎯 Fonction :** Template de configuration d'environnement  
**🔗 Interactions :**
- Template pour le fichier .env
- Utilisé par config/config.php
- Définit les variables d'environnement

**⚙️ Fonctionnalités :**
- **Base de données** : configuration DB_HOST, DB_NAME, DB_USER, DB_PASS
- **Application** : BASE_URL, ENV
- **Sécurité** : SESSION_SECRET, CSRF_SECRET
- **Upload** : MAX_FILE_SIZE, ALLOWED_EXTENSIONS
- **Site** : SITE_NAME, SITE_TAGLINE
- **Documentation** : commentaires explicatifs

**🎨 CSS intégré :** Aucun CSS intégré  
**📄 Feuilles de style :**
- Aucune feuille de style

**🔧 Variables utilisées :**
- Aucune variable utilisée

**📱 JavaScript intégré :** Aucun JavaScript intégré

**🎨 CSS intégré détaillé :**
- Aucun CSS intégré
- Fichier de configuration template

---

### **105. config/theme.json**
**📍 Emplacement :** `/config/theme.json`  
**🎯 Fonction :** Configuration du thème actuel  
**🔗 Interactions :**
- Utilisé par l'application pour le thème
- Modifié par l'admin
- Définit le thème actuel

**⚙️ Fonctionnalités :**
- **Thème actuel** : current_theme (Player)
- **Thème par défaut** : default_theme (defaut)
- **Expiration** : expires_at (null)
- **Permanent** : is_permanent (false)
- **Application** : applied_at (timestamp)
- **Configuration** : paramètres de thème

**🎨 CSS intégré :** Aucun CSS intégré  
**📄 Feuilles de style :**
- Aucune feuille de style

**🔧 Variables utilisées :**
- Aucune variable utilisée

**📱 JavaScript intégré :** Aucun JavaScript intégré

**🎨 CSS intégré détaillé :**
- Aucun CSS intégré
- Fichier de configuration JSON

---

### **106. style.css**
**📍 Emplacement :** `/style.css`  
**🎯 Fonction :** Feuille de style temporaire principale  
**🔗 Interactions :**
- Utilisé par l'application
- Définit les styles globaux
- Variables CSS centralisées

**⚙️ Fonctionnalités :**
- **Variables CSS** : couleurs Belgique, système, états
- **Reset CSS** : normalisation des styles
- **Styles globaux** : typographie, couleurs, espacements
- **Responsive** : adaptation mobile
- **Thèmes** : support des thèmes dynamiques
- **Performance** : optimisation des styles

**🎨 CSS intégré :** Styles complets de l'application  
**📄 Feuilles de style :**
- Fichier CSS principal

**🔧 Variables utilisées :**
- Variables CSS : --belgium-red, --belgium-yellow, --belgium-black
- Variables système : --primary, --secondary, --tertiary
- Variables états : --success, --error, --warning, --info

**📱 JavaScript intégré :** Aucun JavaScript intégré

**🎨 CSS intégré détaillé :**
- Variables CSS complètes
- Reset et normalisation
- Styles globaux et responsive
- Support des thèmes

---

### **107. admin.css**
**📍 Emplacement :** `/admin.css`  
**🎯 Fonction :** Feuille de style pour l'interface d'administration  
**🔗 Interactions :**
- Utilisé par l'interface admin
- Définit les styles admin
- Variables CSS spécifiques

**⚙️ Fonctionnalités :**
- **Variables admin** : couleurs, espacements, ombres
- **Interface admin** : styles pour l'administration
- **Composants** : boutons, formulaires, tableaux
- **Responsive** : adaptation mobile
- **Thèmes** : support des thèmes admin
- **Performance** : optimisation des styles

**🎨 CSS intégré :** Styles complets de l'administration  
**📄 Feuilles de style :**
- Fichier CSS admin principal

**🔧 Variables utilisées :**
- Variables admin : --admin-bg, --admin-primary, --admin-secondary
- Variables espacements : --admin-spacing-xs, --admin-spacing-sm
- Variables ombres : --admin-shadow, --admin-shadow-lg

**📱 JavaScript intégré :** Aucun JavaScript intégré

**🎨 CSS intégré détaillé :**
- Variables CSS admin complètes
- Reset et normalisation
- Styles admin et responsive
- Support des thèmes admin

---

### **108. wamp.conf**
**📍 Emplacement :** `/wamp.conf`  
**🎯 Fonction :** Configuration WampServer pour l'application  
**🔗 Interactions :**
- Configuration du serveur web
- Définit le DocumentRoot
- Gère les erreurs

**⚙️ Fonctionnalités :**
- **DocumentRoot** : pointe vers le dossier public/
- **Permissions** : Options Indexes FollowSymLinks
- **Override** : AllowOverride All
- **Accès** : Require all granted
- **Erreurs** : redirection 404, 403, 500
- **Sécurité** : configuration Apache

**🎨 CSS intégré :** Aucun CSS intégré  
**📄 Feuilles de style :**
- Aucune feuille de style

**🔧 Variables utilisées :**
- Aucune variable utilisée

**📱 JavaScript intégré :** Aucun JavaScript intégré

**🎨 CSS intégré détaillé :**
- Aucun CSS intégré
- Fichier de configuration Apache

---

## **ASSETS CSS/JS RESTANTS**

### **109. public/assets/css/base/variables.css**
**📍 Emplacement :** `/public/assets/css/base/variables.css`  
**🎯 Fonction :** Variables CSS centralisées pour l'application  
**🔗 Interactions :**
- Importé par main.css
- Utilisé par tous les autres fichiers CSS
- Définit le design system

**⚙️ Fonctionnalités :**
- **Couleurs Belgique** : rouge, jaune, noir du drapeau
- **Couleurs système** : primary, secondary, tertiary, border, muted
- **États & feedback** : success, error, warning, info
- **Espacements** : xs, sm, md, lg, xl, 2xl, 3xl
- **Bordures & rayons** : sm, normal, lg, xl
- **Ombres** : sm, normal, lg, xl
- **Typographie** : familles, tailles, poids, hauteurs
- **Transitions** : fast, normal, slow
- **Z-index** : dropdown, sticky, fixed, modal, popover, tooltip
- **Breakpoints** : sm, md, lg, xl, xxl
- **Containers** : max-width, max-width-lg
- **Grid** : colonnes, gutter
- **Badges & status** : test, news, guide, draft, published, archived

**🎨 CSS intégré :** Variables CSS complètes  
**📄 Feuilles de style :**
- Fichier de variables CSS

**🔧 Variables utilisées :**
- Variables CSS : --belgium-red, --belgium-yellow, --belgium-black
- Variables système : --primary, --secondary, --tertiary
- Variables espacements : --spacing-xs, --spacing-sm, --spacing-md
- Variables typographie : --font-family, --font-size-base

**📱 JavaScript intégré :** Aucun JavaScript intégré

**🎨 CSS intégré détaillé :**
- Variables CSS complètes et organisées
- Design system cohérent
- Support des thèmes et responsive

---

### **110. public/assets/css/base/reset.css**
**📍 Emplacement :** `/public/assets/css/base/reset.css`  
**🎯 Fonction :** Reset CSS moderne pour normaliser les styles  
**🔗 Interactions :**
- Importé par main.css
- Appliqué à tous les éléments
- Base pour tous les autres styles

**⚙️ Fonctionnalités :**
- **Reset moderne** : basé sur modern-normalize
- **Box-sizing** : border-box pour tous les éléments
- **Marges** : suppression des marges par défaut
- **Typographie** : amélioration du rendu du texte
- **Corps** : font-family, font-size, line-height, color, background
- **Éléments** : h1-h6, listes, liens, images, formulaires
- **Accessibilité** : prefers-reduced-motion, focus-visible
- **Sélection** : couleurs Belgique pour la sélection
- **Performance** : antialiased, grayscale

**🎨 CSS intégré :** Reset CSS complet  
**📄 Feuilles de style :**
- Fichier de reset CSS

**🔧 Variables utilisées :**
- Variables CSS : --line-height-normal, --font-family, --font-size-base
- Variables couleurs : --primary, --background, --belgium-yellow, --belgium-black

**📱 JavaScript intégré :** Aucun JavaScript intégré

**🎨 CSS intégré détaillé :**
- Reset CSS moderne et complet
- Normalisation des styles
- Support de l'accessibilité

---

### **111. public/assets/css/base/typography.css**
**📍 Emplacement :** `/public/assets/css/base/typography.css`  
**🎯 Fonction :** Styles typographiques pour l'application  
**🔗 Interactions :**
- Importé par main.css
- Utilise les variables CSS
- Appliqué aux éléments de texte

**⚙️ Fonctionnalités :**
- **Titres** : title, subtitle, section-title
- **Titres d'articles** : article-title, featured-title, trailers-title
- **Extraits** : article-excerpt, featured-excerpt, trailer-title
- **Logo & branding** : logo-text, logo-subtitle, header-title
- **Statistiques** : stat-number, stat-label
- **Dates & métadonnées** : article-date, trailer-duration
- **Utilitaires** : text-center, text-left, text-right, text-muted
- **Tailles** : text-xs, text-sm, text-base, text-lg, text-xl, text-2xl, text-3xl, text-4xl
- **Poids** : font-normal, font-medium, font-semibold, font-bold
- **Responsive** : adaptation mobile des tailles

**🎨 CSS intégré :** Styles typographiques complets  
**📄 Feuilles de style :**
- Fichier de typographie CSS

**🔧 Variables utilisées :**
- Variables typographie : --font-family-heading, --font-size-4xl, --font-weight-bold
- Variables couleurs : --primary, --text-muted, --belgium-yellow
- Variables espacements : --spacing-md, --spacing-sm, --spacing-xs

**📱 JavaScript intégré :** Aucun JavaScript intégré

**🎨 CSS intégré détaillé :**
- Styles typographiques complets
- Classes utilitaires
- Support responsive

---

### **112. public/assets/css/components/buttons.css**
**📍 Emplacement :** `/public/assets/css/components/buttons.css`  
**🎯 Fonction :** Styles pour tous les boutons de l'application  
**🔗 Interactions :**
- Importé par main.css
- Utilise les variables CSS
- Appliqué aux éléments button et .btn

**⚙️ Fonctionnalités :**
- **Bouton de base** : .btn avec styles communs
- **Variantes couleurs** : primary, secondary, success, warning, danger, info
- **Boutons spécifiques** : login-btn, logout-btn
- **Tailles** : btn-sm, btn-lg, btn-xl
- **Actions** : article-actions, media-actions
- **Toolbar** : toolbar-btn avec états active
- **Tabs** : tab-trigger avec états active et hover
- **Désactivés** : btn:disabled, btn.disabled
- **Icônes** : btn-icon avec tailles
- **Pagination** : styles pour pagination
- **Responsive** : adaptation mobile

**🎨 CSS intégré :** Styles de boutons complets  
**📄 Feuilles de style :**
- Fichier de boutons CSS

**🔧 Variables utilisées :**
- Variables couleurs : --belgium-red, --belgium-yellow, --belgium-black
- Variables espacements : --spacing-sm, --spacing-md, --spacing-lg
- Variables transitions : --transition-normal, --transition-fast
- Variables ombres : --shadow

**📱 JavaScript intégré :** Aucun JavaScript intégré

**🎨 CSS intégré détaillé :**
- Styles de boutons complets
- Variantes et états
- Support responsive

---

## **FICHIERS DE CONFIGURATION RESTANTS**

### **113. public/admin.css**
**📍 Emplacement :** `/public/admin.css`  
**🎯 Fonction :** Feuille de style pour l'interface d'administration  
**🔗 Interactions :**
- Utilisé par l'interface admin
- Définit les styles admin
- Variables CSS spécifiques

**⚙️ Fonctionnalités :**
- **Variables admin** : couleurs, espacements, ombres
- **Interface admin** : styles pour l'administration
- **Composants** : boutons, formulaires, tableaux
- **Responsive** : adaptation mobile
- **Thèmes** : support des thèmes admin
- **Performance** : optimisation des styles

**🎨 CSS intégré :** Styles complets de l'administration  
**📄 Feuilles de style :**
- Fichier CSS admin principal

**🔧 Variables utilisées :**
- Variables admin : --admin-bg, --admin-primary, --admin-secondary
- Variables espacements : --admin-spacing-xs, --admin-spacing-sm
- Variables ombres : --admin-shadow, --admin-shadow-lg

**📱 JavaScript intégré :** Aucun JavaScript intégré

**🎨 CSS intégré détaillé :**
- Variables CSS admin complètes
- Reset et normalisation
- Styles admin et responsive
- Support des thèmes admin

---

### **114. themes.php**
**📍 Emplacement :** `/themes.php`  
**🎯 Fonction :** Routeur temporaire pour la gestion des thèmes  
**🔗 Interactions :**
- Simule l'URI `/admin/themes`
- Délègue au routeur principal
- Fichier temporaire (à supprimer)

**⚙️ Fonctionnalités :**
- **Simulation URI** : `/admin/themes`
- **Délégation** : vers public/index.php
- **Temporaire** : en attente de configuration .htaccess
- **Simplicité** : redirection directe

**🎨 CSS intégré :** Aucun CSS intégré  
**📄 Feuilles de style :**
- Aucune feuille de style

**🔧 Variables utilisées :**
- `$_SERVER['REQUEST_URI']` - URI simulée

**📱 JavaScript intégré :** Aucun JavaScript intégré

**🎨 CSS intégré détaillé :**
- Aucun CSS intégré
- Fichier de routage temporaire

---

### **115. theme-image.php**
**📍 Emplacement :** `/theme-image.php`  
**🎯 Fonction :** Service pour servir les images des thèmes  
**🔗 Interactions :**
- Utilise les paramètres GET
- Gère les types MIME d'images
- Vérifie la sécurité des chemins

**⚙️ Fonctionnalités :**
- **Sécurité** : protection contre les attaques de traversée de répertoire
- **Validation** : vérification des paramètres theme et side
- **Types MIME** : support images (PNG, JPG, GIF, WebP)
- **Cache** : cache 1 an pour les images
- **Performance** : lecture directe des fichiers
- **Sécurité** : vérification des chemins réels

**🎨 CSS intégré :** Aucun CSS intégré  
**📄 Feuilles de style :**
- Aucune feuille de style

**🔧 Variables utilisées :**
- `$theme` - Nom du thème
- `$side` - Côté de l'image (left/right)
- `$imagePath` - Chemin vers l'image
- `$mimeType` - Type MIME de l'image

**📱 JavaScript intégré :** Aucun JavaScript intégré

**🎨 CSS intégré détaillé :**
- Aucun CSS intégré
- Fichier de service d'images de thèmes

---

### **116. genres.php**
**📍 Emplacement :** `/genres.php`  
**🎯 Fonction :** Routeur d'administration des genres  
**🔗 Interactions :**
- Utilise GenresController
- Gère les actions CRUD
- Redirige en cas d'erreur

**⚙️ Fonctionnalités :**
- **Actions CRUD** : index, create, store, edit, update, delete
- **Validation** : vérification des IDs
- **Gestion erreurs** : redirection en cas d'erreur
- **Sécurité** : validation des paramètres
- **Contrôleur** : instanciation directe du contrôleur
- **Routing** : switch pour les actions

**🎨 CSS intégré :** Aucun CSS intégré  
**📄 Feuilles de style :**
- Aucune feuille de style

**🔧 Variables utilisées :**
- `$action` - Action à effectuer
- `$id` - ID de l'élément
- `$controller` - Instance du contrôleur

**📱 JavaScript intégré :** Aucun JavaScript intégré

**🎨 CSS intégré détaillé :**
- Aucun CSS intégré
- Fichier de routage avec contrôleur direct

---

## **VUES PUBLIQUES**

### **60. app/views/home/index.php**
**📍 Emplacement :** `/app/views/home/index.php`  
**🎯 Fonction :** Page d'accueil publique avec articles en vedette et dernières news  
**🔗 Interactions :**
- Utilise `public.php` comme layout
- Variables : `$featuredArticles`, `$latestArticles`, `$trailers`
- Intégration avec `image.php` pour les images
- JavaScript pour navigation par onglets

**⚙️ Fonctionnalités :**
- **Section Articles en avant** : grille 2/3 + 1/3 avec articles principaux
- **Section Dernières news** : onglets paginés (1-10, 11-20, 21-30)
- **Colonne Trailers** : derniers trailers avec overlay play
- **Contenu par défaut** : fallback si pas d'articles
- **Navigation interactive** : clics sur articles et trailers
- **Responsive design** : adaptation mobile

**🎨 CSS intégré :** Aucun CSS intégré  
**📄 Feuilles de style :**
- `/public/assets/css/main.css` (principal)
- `/public/assets/css/layout/public.css` (layout)

**🔧 Variables utilisées :**
- `$featuredArticles` - Articles en vedette (max 6)
- `$latestArticles` - Derniers articles (max 30)
- `$trailers` - Derniers trailers
- `$pageTitle`, `$pageDescription` - Métadonnées SEO

**📱 JavaScript intégré :**
- `showTab(tabName)` - Navigation entre onglets d'articles

---

### **61. app/views/articles/show.php**
**📍 Emplacement :** `/app/views/articles/show.php`  
**🎯 Fonction :** Affichage détaillé d'un article avec métadonnées et navigation  
**🔗 Interactions :**
- Utilise `public.php` comme layout
- Variables : `$article`, `$isDossier`, `$dossierChapters`
- Intégration avec `ImageHelper` et `SeoHelper`
- Liens vers chapitres de dossiers

**⚙️ Fonctionnalités :**
- **Métadonnées article** : auteur, date, statut, jeu associé
- **Hero unifié** : image de couverture + titre + catégorie
- **Contenu responsive** : nettoyage HTML et affichage
- **Navigation chapitres** : pour les dossiers multi-parties
- **Actions admin** : liens d'édition si connecté
- **SEO optimisé** : meta tags dynamiques

**🎨 CSS intégré :** Aucun CSS intégré  
**📄 Feuilles de style :**
- `/public/assets/css/main.css` (principal)
- `/public/assets/css/layout/public.css` (layout)

**🔧 Variables utilisées :**
- `$article` - Objet Article complet
- `$isDossier` - Booléen si c'est un dossier
- `$dossierChapters` - Chapitres du dossier
- `$cleanedContent` - Contenu HTML nettoyé
- `$seoMetaTags` - Tags SEO générés

**📱 JavaScript intégré :** Aucun JavaScript intégré

---

### **62. app/views/categories/show.php**
**📍 Emplacement :** `/app/views/categories/show.php`  
**🎯 Fonction :** Affichage d'une catégorie avec tous ses articles  
**🔗 Interactions :**
- Utilise `public.php` comme layout
- Variables : `$category`, `$articles`
- Intégration avec `image.php` pour les images
- Navigation vers articles individuels

**⚙️ Fonctionnalités :**
- **Informations catégorie** : nom, description, statistiques
- **Articles principaux** : format large (6 premiers)
- **Articles secondaires** : format compact (reste)
- **Badges colorés** : par type de catégorie
- **État vide** : message si aucun article
- **Design responsive** : adaptation mobile

**🎨 CSS intégré :** Styles complets pour la page catégorie  
**📄 Feuilles de style :**
- `/public/assets/css/main.css` (principal)
- Styles intégrés pour cartes et badges

**🔧 Variables utilisées :**
- `$category` - Objet Category
- `$articles` - Tableau des articles de la catégorie

**📱 JavaScript intégré :** Aucun JavaScript intégré

**🎨 CSS intégré détaillé :**
- `.category-info` - Bloc d'informations catégorie
- `.category-badge` - Badges colorés par catégorie
- `.article-card-large` - Cartes d'articles principales
- `.article-card-small` - Cartes d'articles secondaires
- `.empty-state` - État vide
- Responsive design complet

---

### **63. app/views/hardware/index.php**
**📍 Emplacement :** `/app/views/hardware/index.php`  
**🎯 Fonction :** Liste publique de tous les matériels de gaming  
**🔗 Interactions :**
- Utilise `public.php` comme layout
- Variables : `$hardwares`
- Navigation vers détails hardware
- Intégration avec modèles Hardware

**⚙️ Fonctionnalités :**
- **Introduction hardware** : description de la section
- **Grille matériels** : cartes avec informations complètes
- **Badges de type** : console, PC, autre
- **Statistiques** : nombre de jeux associés
- **État vide** : message si aucun hardware
- **Design responsive** : adaptation mobile

**🎨 CSS intégré :** Styles complets pour la page hardware  
**📄 Feuilles de style :**
- `/public/assets/css/main.css` (principal)
- Styles intégrés pour cartes hardware

**🔧 Variables utilisées :**
- `$hardwares` - Tableau des objets Hardware

**📱 JavaScript intégré :** Aucun JavaScript intégré

**🎨 CSS intégré détaillé :**
- `.hardware-intro` - Bloc d'introduction
- `.hardware-grid` - Grille des matériels
- `.hardware-card` - Cartes de matériels
- `.hardware-type-badge` - Badges de type (console, PC, autre)
- `.hardware-stats` - Statistiques par matériel
- `.empty-state` - État vide
- Responsive design complet

---

### **56. app/views/admin/hardware/edit.php**
**📍 Emplacement :** `/app/views/admin/hardware/edit.php`  
**🎯 Fonction :** Formulaire d'édition de hardware avec informations système  
**🔗 Interactions :**
- Utilise `admin.css` pour le style
- Variables : `$hardware`, `$error`, `$csrf_token`, `$types`
- Formulaire POST vers `/hardware.php?action=update&id={id}`

**⚙️ Fonctionnalités :**
- **Navigation** : retour à la liste
- **Messages** : erreurs
- **Informations système** : ID, slug, date création, jeux associés, date modification
- **Formulaire complet** : toutes les sections (pré-remplies)
- **Validation** : côté client et serveur
- **Génération conditionnelle** du slug

**🎨 CSS intégré :** Aucun CSS intégré  
**📄 Feuilles de style :**
- `/admin.css` (principal)

**🔧 Variables utilisées :**
- `$hardware` - Objet hardware à éditer
- `$error` - Message d'erreur
- `$csrf_token` - Token CSRF
- `$types` - Types de hardware disponibles
- `$_POST` - Valeurs du formulaire (priorité sur objet)

**📊 Informations système :**
- **ID** du hardware
- **Slug actuel**
- **Date de création**
- **Nombre de jeux** associés
- **Date de modification** (si applicable)

**⚙️ JavaScript intégré :**
- **Génération conditionnelle** du slug (si vide)
- **Validation côté client** complète
- **Vérification** des champs obligatoires
- **Prévention soumission** si erreurs

**🎨 UX/UI :**
- **Pré-remplissage** des champs
- **Préservation** des valeurs POST en cas d'erreur
- **Messages d'aide** pour chaque champ
- **Sections organisées** (base + fabricant + description)

---

### **57. app/views/admin/genres/index.php**
**📍 Emplacement :** `/app/views/admin/genres/index.php`  
**🎯 Fonction :** Liste et gestion des genres avec couleurs et modal de suppression  
**🔗 Interactions :**
- Utilise `admin.css` pour le style
- Variables : `$genres`, `$totalGenres`, `$search`, `$currentPage`, `$totalPages`
- Actions AJAX pour suppression via modal

**⚙️ Fonctionnalités :**
- **Navigation** : retour dashboard, nouveau genre
- **Messages** : succès et erreurs via GET parameters
- **Statistiques** : total genres
- **Filtres** : recherche par nom
- **Tableau** : ID, couleur, nom, description, jeux associés, actions
- **Pagination** avec paramètres de recherche
- **Modal de suppression** avec confirmation

**🎨 CSS intégré :** Aucun CSS intégré  
**📄 Feuilles de style :**
- `/admin.css` (principal)

**🔧 Variables utilisées :**
- `$genres` - Liste des genres
- `$totalGenres` - Nombre total de genres
- `$search` - Terme de recherche
- `$currentPage`, `$totalPages` - Pagination

**🎯 Gestion des genres :**
- **Aperçu de couleur** avec code hexadécimal
- **Compteurs de jeux** associés
- **Descriptions** affichées
- **Actions** : modifier, supprimer

**🔐 Sécurité :**
- **CSRF tokens** pour suppression
- **Modal de confirmation** avant suppression
- **Validation côté client** et serveur

**⚙️ JavaScript intégré :**
- **Modal de suppression** avec confirmation
- **Gestion des événements** de clic
- **Fermeture modal** en cliquant à l'extérieur
- **Prévention soumission** si erreurs

**📱 Responsive :**
- **Tableau adaptatif**
- **Filtres empilés** sur mobile
- **Actions compactes**

---

### **58. app/views/admin/genres/create.php**
**📍 Emplacement :** `/app/views/admin/genres/create.php`  
**🎯 Fonction :** Formulaire de création de genre avec sélecteur de couleur  
**🔗 Interactions :**
- Utilise `admin.css` pour le style
- Variables : `$error`, `$csrf_token`
- Formulaire POST vers `/genres.php?action=store`

**⚙️ Fonctionnalités :**
- **Navigation** : retour à la liste
- **Messages** : erreurs
- **Formulaire** : nom, description, couleur
- **Validation** : côté client et serveur
- **Sélecteur de couleur** avec synchronisation
- **Validation format** hexadécimal

**🎨 CSS intégré :** Aucun CSS intégré  
**📄 Feuilles de style :**
- `/admin.css` (principal)

**🔧 Variables utilisées :**
- `$error` - Message d'erreur
- `$csrf_token` - Token CSRF
- `$_POST` - Valeurs du formulaire

**📝 Champs du formulaire :**
- **Nom** : obligatoire, nom du genre
- **Description** : optionnel, description détaillée
- **Couleur** : sélecteur de couleur + input texte

**⚙️ JavaScript intégré :**
- **Synchronisation** entre sélecteur couleur et input texte
- **Validation format** hexadécimal (#RRGGBB)
- **Validation côté client** complète
- **Prévention soumission** si erreurs

**🎨 UX/UI :**
- **Sélecteur de couleur** visuel
- **Synchronisation** en temps réel
- **Messages d'aide** pour chaque champ
- **Validation** du format couleur

---

### **59. app/views/admin/genres/edit.php**
**📍 Emplacement :** `/app/views/admin/genres/edit.php`  
**🎯 Fonction :** Formulaire d'édition de genre avec informations système  
**🔗 Interactions :**
- Utilise `admin.css` pour le style
- Variables : `$genre`, `$error`, `$csrf_token`
- Formulaire POST vers `/genres.php?action=update&id={id}`

**⚙️ Fonctionnalités :**
- **Navigation** : retour à la liste
- **Messages** : erreurs
- **Informations système** : ID, dates de création et modification
- **Formulaire** : nom, description, couleur (pré-remplis)
- **Validation** : côté client et serveur
- **Sélecteur de couleur** avec synchronisation

**🎨 CSS intégré :** Aucun CSS intégré  
**📄 Feuilles de style :**
- `/admin.css` (principal)

**🔧 Variables utilisées :**
- `$genre` - Objet genre à éditer
- `$error` - Message d'erreur
- `$csrf_token` - Token CSRF
- `$_POST` - Valeurs du formulaire (priorité sur objet)

**📊 Informations système :**
- **ID** du genre
- **Date de création**
- **Date de modification**

**⚙️ JavaScript intégré :**
- **Synchronisation** entre sélecteur couleur et input texte
- **Validation format** hexadécimal (#RRGGBB)
- **Validation côté client** complète
- **Prévention soumission** si erreurs

**🎨 UX/UI :**
- **Pré-remplissage** des champs
- **Préservation** des valeurs POST en cas d'erreur
- **Sélecteur de couleur** visuel
- **Messages d'aide** pour chaque champ

---
