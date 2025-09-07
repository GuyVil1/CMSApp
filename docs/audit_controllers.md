# 🎮 AUDIT - CONTRÔLEURS (CONTROLLERS)

## 📋 **Contrôleurs de l'application**

---

### **19. app/controllers/HomeController.php**
**📍 Emplacement :** `/app/controllers/HomeController.php`  
**🎯 Fonction :** Contrôleur principal pour la page d'accueil et l'affichage des articles  
**🔗 Interactions :**
- Hérite de `core/Controller.php`
- Utilise `core/Auth.php`, `core/Database.php`
- Utilise `app/helpers/seo_helper.php`
- Utilise les modèles : `Article`, `Category`, `Game`, `Media`, `Hardware`
- Utilise `app/controllers/SeoController.php`

**⚙️ Méthodes principales :**
- `index()` - Page d'accueil avec articles en vedette et dernières news
- `show($slug)` - Affichage d'un article individuel
- `showChapter($dossierSlug, $chapterSlug)` - Affichage d'un chapitre de dossier
- `hardware($slug)` - Page d'un hardware spécifique
- `category($slug)` - Page d'une catégorie spécifique
- `hardwareList()` - Listing de tous les hardwares
- `getFeaturedArticles()` - Récupération articles en vedette
- `getLatestArticles()` - Récupération dernières news
- `getPopularCategories()` - Récupération catégories populaires
- `getPopularGames()` - Récupération jeux populaires
- `getTrailers()` - Récupération trailers
- `getCurrentTheme()` - Récupération thème actuel
- `isDarkModeEnabled()` - Vérification mode sombre
- `isRegistrationEnabled()` - Vérification inscriptions
- `getRelatedArticles($article)` - Articles liés
- `getPopularArticles()` - Articles populaires
- `getDossierChapters($dossierId)` - Chapitres d'un dossier
- `getDossierChapterBySlug($dossierId, $chapterSlug)` - Chapitre par slug
- `getArticlesByHardware($hardwareId)` - Articles par hardware
- `getGamesByHardware($hardwareId)` - Jeux par hardware
- `getArticlesByCategory($categoryId)` - Articles par catégorie

**🎨 CSS :** Aucun CSS intégré  
**📄 Feuilles de style :** 
- `/public/assets/css/components/article-display.css`
- `/public/assets/css/components/content-modules.css`
- `/public/assets/css/components/article-hero.css`
- `/public/assets/css/components/article-meta.css`
- `/public/assets/css/components/dossier-chapters.css`
- `/public/assets/css/components/chapter-navigation.css`

**🔗 Relations :**
- **Article** - Gestion des articles et dossiers
- **Category** - Affichage par catégorie
- **Game** - Articles liés aux jeux
- **Media** - Images de couverture
- **Hardware** - Pages hardware
- **SeoController** - Meta tags SEO

---

### **20. app/controllers/AuthController.php**
**📍 Emplacement :** `/app/controllers/AuthController.php`  
**🎯 Fonction :** Contrôleur d'authentification (connexion, déconnexion, inscription)  
**🔗 Interactions :**
- Hérite de `core/Controller.php`
- Utilise `core/Auth.php`
- Utilise `app/models/User.php`
- Utilise `app/models/Setting.php`

**⚙️ Méthodes principales :**
- `login()` - Page de connexion
- `logout()` - Déconnexion
- `register()` - Page d'inscription
- `changePassword()` - Changement de mot de passe
- `forbidden()` - Page 403 (accès interdit)

**🎨 CSS :** Aucun CSS intégré  
**📄 Feuilles de style :** Aucune référence directe

**🔐 Sécurité :**
- Validation CSRF avec tokens
- Validation des mots de passe
- Validation des emails
- Vérification des permissions
- Hashage sécurisé des mots de passe

---

### **21. app/controllers/LegalController.php**
**📍 Emplacement :** `/app/controllers/LegalController.php`  
**🎯 Fonction :** Contrôleur pour les pages légales du site  
**🔗 Interactions :**
- Hérite de `core/Controller.php`
- Utilise `core/Auth.php`
- Utilise `app/models/Setting.php`

**⚙️ Méthodes principales :**
- `mentionsLegales()` - Page mentions légales
- `politiqueConfidentialite()` - Page politique de confidentialité
- `cgu()` - Page conditions générales d'utilisation
- `cookies()` - Page politique des cookies
- `getCommonVariables()` - Variables communes
- `isDarkModeEnabled()` - Vérification mode sombre
- `isRegistrationEnabled()` - Vérification inscriptions
- `getLegalContent($template, $title, $subtitle)` - Récupération contenu légal
- `formatLegalAsArticle($content, $title, $subtitle)` - Formatage comme article

**🎨 CSS :** Aucun CSS intégré  
**📄 Feuilles de style :** Aucune référence directe

**📄 Pages légales gérées :**
- Mentions légales
- Politique de confidentialité
- Conditions générales d'utilisation
- Politique des cookies

**🔗 Relations :**
- **Setting** - Récupération contenu personnalisé
- **Auth** - Vérification connexion
- **Layout public** - Rendu des pages

---

### **22. app/controllers/SeoController.php**
**📍 Emplacement :** `/app/controllers/SeoController.php`  
**🎯 Fonction :** Contrôleur pour la gestion SEO (sitemap, robots.txt, meta tags)  
**🔗 Interactions :**
- Hérite de `core/Controller.php`
- Utilise `app/helpers/seo_helper.php`

**⚙️ Méthodes principales :**
- `sitemap()` - Génération sitemap XML
- `robots()` - Génération robots.txt
- `homeMetaTags()` - Meta tags page d'accueil
- `categoryMetaTags($category, $baseUrl)` - Meta tags catégorie
- `gameMetaTags($game, $baseUrl)` - Meta tags jeu

**🎨 CSS :** Aucun CSS intégré  
**📄 Feuilles de style :** Aucune référence

**🔍 SEO Features :**
- Sitemap XML automatique
- Robots.txt configuré
- Meta tags Open Graph
- Meta tags Twitter
- URLs canoniques
- Cache headers

---

### **23. app/controllers/admin/SettingsController.php**
**📍 Emplacement :** `/app/controllers/admin/SettingsController.php`  
**🎯 Fonction :** Contrôleur admin pour la gestion des paramètres du site  
**🔗 Interactions :**
- Hérite de `core/Controller.php`
- Utilise `core/Auth.php`
- Utilise `app/models/Setting.php`
- Utilise `config/theme.json`

**⚙️ Méthodes principales :**
- `index()` - Affichage page des paramètres
- `save()` - Sauvegarde des paramètres
- `getAvailableThemes()` - Récupération thèmes disponibles
- `getAvailableLogos()` - Récupération logos disponibles
- `updateCurrentTheme($themeName)` - Mise à jour thème actuel

**🎨 CSS :** Aucun CSS intégré  
**📄 Feuilles de style :** Aucune référence directe

**⚙️ Paramètres gérés :**
- `allow_registration` - Autorisation inscriptions
- `dark_mode` - Mode sombre
- `maintenance_mode` - Mode maintenance
- `default_theme` - Thème par défaut
- `footer_tagline` - Slogan footer
- `social_*` - Liens réseaux sociaux
- `header_logo` / `footer_logo` - Logos
- `legal_*_content` - Contenu pages légales

**🔐 Sécurité :**
- Vérification rôle admin obligatoire
- Validation des données POST
- Gestion des erreurs

---

### **24. app/controllers/admin/ArticlesController.php**
**📍 Emplacement :** `/app/controllers/admin/ArticlesController.php`  
**🎯 Fonction :** Contrôleur admin pour la gestion complète des articles  
**🔗 Interactions :**
- Hérite de `core/Controller.php`
- Utilise `core/Auth.php`, `core/Database.php`
- Utilise `app/models/Article.php`, `app/models/Media.php`

**⚙️ Méthodes principales :**
- `index()` - Liste des articles avec filtres
- `create()` - Formulaire création article
- `store()` - Traitement création article
- `edit($id)` - Formulaire édition article
- `update($id)` - Traitement mise à jour
- `delete($id)` - Suppression article
- `publish($id)` - Publication article
- `draft($id)` - Mise en brouillon
- `archive($id)` - Archivage article
- `saveChapters()` - Sauvegarde chapitres dossier
- `loadChapters($articleId)` - Chargement chapitres
- `updateChapterStatus()` - Mise à jour statut chapitre
- `deleteChapter()` - Suppression chapitre
- `handleImageUpload($file)` - Upload image
- `getFeaturedPositions($excludeArticleId)` - Positions en avant
- `getPositionDescription($position)` - Description position
- `getPositionLabel($position)` - Label position

**🎨 CSS :** Aucun CSS intégré  
**📄 Feuilles de style :** Aucune référence directe

**🔐 Permissions :**
- **Admin** : Accès complet à tous les articles
- **Editor** : Accès limité à ses propres articles
- Restrictions sur publication pour les éditeurs

**📝 Fonctionnalités avancées :**
- Gestion des dossiers avec chapitres
- Système de mise en avant (6 positions)
- Upload d'images avec validation
- Gestion des tags
- Validation avancée des données
- Logs d'activité

---

### **25. app/controllers/admin/DashboardController.php**
**📍 Emplacement :** `/app/controllers/admin/DashboardController.php`  
**🎯 Fonction :** Contrôleur admin pour le tableau de bord  
**🔗 Interactions :**
- Hérite de `core/Controller.php`
- Utilise `core/Auth.php`, `core/Database.php`
- Utilise `app/models/Setting.php`

**⚙️ Méthodes principales :**
- `index()` - Page d'accueil tableau de bord
- `getStats()` - Statistiques du site
- `getOptions()` - Options du site
- `getCurrentThemeName()` - Nom thème actuel

**🎨 CSS :** Aucun CSS intégré  
**📄 Feuilles de style :** Aucune référence directe

**📊 Statistiques affichées :**
- Nombre d'articles
- Nombre d'utilisateurs
- Nombre de jeux
- Nombre de catégories

**⚙️ Options affichées :**
- État des inscriptions
- État du mode sombre
- État du mode maintenance
- Thème actuel

**🔐 Sécurité :**
- Vérification rôle admin obligatoire
- Gestion des erreurs avec valeurs par défaut

---

### **26. app/controllers/admin/CategoriesController.php**
**📍 Emplacement :** `/app/controllers/admin/CategoriesController.php`  
**🎯 Fonction :** Contrôleur admin pour la gestion des catégories (CRUD)  
**🔗 Interactions :**
- Hérite de `core/Controller.php`
- Utilise `core/Auth.php`
- Utilise `app/models/Category.php`

**⚙️ Méthodes principales :**
- `index()` - Liste des catégories avec pagination et recherche
- `create()` - Formulaire création catégorie
- `store()` - Traitement création catégorie
- `edit($id)` - Formulaire édition catégorie
- `update($id)` - Traitement mise à jour catégorie
- `delete($id)` - Suppression catégorie

**🎨 CSS :** Aucun CSS intégré  
**📄 Feuilles de style :** Aucune référence directe

**🔐 Sécurité :**
- Vérification rôle admin obligatoire
- Validation CSRF sur toutes les actions
- Validation des données d'entrée

**📊 Fonctionnalités :**
- Pagination (20 éléments par page)
- Recherche par nom
- Comptage des articles par catégorie
- Génération automatique de slug
- Validation d'unicité des slugs

---

### **27. app/controllers/admin/GamesController.php**
**📍 Emplacement :** `/app/controllers/admin/GamesController.php`  
**🎯 Fonction :** Contrôleur admin pour la gestion des jeux vidéo (CRUD complet)  
**🔗 Interactions :**
- Hérite de `core/Controller.php`
- Utilise `core/Auth.php`
- Utilise `app/models/Game.php`, `app/models/Media.php`, `app/models/Hardware.php`, `app/models/Genre.php`

**⚙️ Méthodes principales :**
- `index()` - Liste des jeux avec filtres et pagination
- `create()` - Formulaire création jeu
- `store()` - Traitement création jeu
- `edit($id)` - Formulaire édition jeu
- `update($id)` - Traitement mise à jour jeu
- `get($id)` - API pour récupérer infos jeu
- `delete($id)` - Suppression jeu

**🎨 CSS :** Aucun CSS intégré  
**📄 Feuilles de style :** Aucune référence directe

**🔐 Sécurité :**
- Vérification rôle admin obligatoire
- Validation CSRF sur toutes les actions
- Validation avancée des données

**📊 Fonctionnalités avancées :**
- **Upload d'images** avec validation
- **Liens d'achat** (Steam, eShop, Xbox, PSN, Epic, GOG)
- **Système de notation** (0-10)
- **Classification PEGI** (3, 7, 12, 16, 18)
- **Informations développeur/éditeur**
- **Filtres** par plateforme et genre
- **Pagination** (20 éléments par page)
- **Recherche** par titre
- **Comptage des articles** par jeu

**🎮 Champs gérés :**
- Titre, slug, description
- Plateforme, genre, hardware
- Date de sortie
- Image de couverture
- Note et statut testé
- Développeur et éditeur
- Classification PEGI
- Liens d'achat multiples

---

### **28. app/controllers/admin/UsersController.php**
**📍 Emplacement :** `/app/controllers/admin/UsersController.php`  
**🎯 Fonction :** Contrôleur admin pour la gestion des utilisateurs (CRUD, rôles)  
**🔗 Interactions :**
- Hérite de `core/Controller.php`
- Utilise `core/Auth.php`
- Utilise `app/models/User.php`, `app/models/Role.php`

**⚙️ Méthodes principales :**
- `index()` - Liste des utilisateurs avec filtres et pagination
- `create()` - Formulaire création utilisateur
- `store()` - Traitement création utilisateur
- `edit($id)` - Formulaire édition utilisateur
- `update($id)` - Traitement mise à jour utilisateur
- `delete($id)` - Suppression utilisateur

**🎨 CSS :** Aucun CSS intégré  
**📄 Feuilles de style :** Aucune référence directe

**🔐 Sécurité :**
- Vérification rôle admin obligatoire
- Validation CSRF sur toutes les actions
- Protection contre auto-suppression
- Validation des mots de passe

**📊 Fonctionnalités :**
- **Pagination** (20 éléments par page)
- **Recherche** par login ou email
- **Filtrage** par rôle
- **Gestion des rôles** (admin, editor, member)
- **Statistiques** (nombre d'admins)
- **Validation** des données utilisateur
- **Mots de passe** optionnels en édition

**👥 Gestion des rôles :**
- Affichage du rôle de chaque utilisateur
- Modification des rôles
- Statistiques par rôle

---

### **29. app/controllers/admin/MediaController.php**
**📍 Emplacement :** `/app/controllers/admin/MediaController.php`  
**🎯 Fonction :** Contrôleur admin pour la gestion avancée des médias  
**🔗 Interactions :**
- Hérite de `core/Controller.php`
- Utilise `core/Auth.php`
- Utilise `app/models/Media.php`, `app/models/Game.php`, `app/models/Hardware.php`
- Utilise `app/utils/ImageOptimizer.php`
- Utilise `app/helpers/security_helper.php`

**⚙️ Méthodes principales :**
- `index()` - Liste des médias avec pagination
- `upload()` - Upload d'images avec optimisation
- `searchGames()` - Recherche de jeux pour autocomplétion
- `delete($id)` - Suppression média
- `search()` - Recherche de médias avec filtres
- `byType()` - Médias par type
- `get()` - Récupération média par ID
- `getGames()` - Liste des jeux pour filtres

**🎨 CSS :** Aucun CSS intégré  
**📄 Feuilles de style :** Aucune référence directe

**🔐 Sécurité :**
- Validation CSRF sur toutes les actions
- Validation avancée des fichiers
- Vérification des types MIME
- Validation des dimensions

**📸 Fonctionnalités avancées :**
- **Upload sécurisé** avec validation renforcée
- **Optimisation automatique** des images (WebP, JPG)
- **Création de vignettes** automatique
- **Gestion par jeu** (dossiers organisés)
- **Recherche avancée** avec filtres
- **API complète** pour intégration
- **Gestion d'erreurs** contextuelle
- **Cache temporaire** des uploads

**⚙️ Configuration :**
- Types autorisés : JPG, PNG, WebP, GIF
- Taille max : 4MB
- Dimensions max : 4096x4096 pixels
- Vignettes : 320x240 pixels
- Compression automatique

**📁 Organisation :**
- Images générales : `/uploads/article/`
- Images de jeux : `/uploads/games/{slug}/`
- Vignettes : préfixe `thumb_`

---

### **30. app/controllers/admin/HardwareController.php**
**📍 Emplacement :** `/app/controllers/admin/HardwareController.php`  
**🎯 Fonction :** Contrôleur admin pour la gestion des plateformes de jeux (CRUD)  
**🔗 Interactions :**
- Hérite de `core/Controller.php`
- Utilise `core/Auth.php`
- Utilise `app/models/Hardware.php`

**⚙️ Méthodes principales :**
- `index()` - Liste des hardware avec filtres et pagination
- `create()` - Formulaire création hardware
- `store()` - Traitement création hardware
- `edit($id)` - Formulaire édition hardware
- `update($id)` - Traitement mise à jour hardware
- `delete($id)` - Suppression hardware

**🎨 CSS :** Aucun CSS intégré  
**📄 Feuilles de style :** Aucune référence directe

**🔐 Sécurité :**
- Vérification rôle admin obligatoire
- Validation CSRF sur toutes les actions
- Validation des données d'entrée

**📊 Fonctionnalités :**
- **Pagination** (20 éléments par page)
- **Recherche** par nom ou fabricant
- **Filtrage** par type de hardware
- **Types de hardware** : console, pc, mobile, etc.
- **Gestion des fabricants** (Sony, Microsoft, Nintendo, etc.)
- **Année de sortie** et description
- **Statut actif/inactif**
- **Ordre de tri** personnalisable

**🔧 Champs gérés :**
- Nom, slug, type, fabricant
- Année de sortie, description
- Statut actif, ordre de tri

---

### **31. app/controllers/admin/GenresController.php**
**📍 Emplacement :** `/app/controllers/admin/GenresController.php`  
**🎯 Fonction :** Contrôleur admin pour la gestion des genres de jeux (CRUD)  
**🔗 Interactions :**
- Hérite de `core/Controller.php`
- Utilise `core/Auth.php`
- Utilise `app/models/Genre.php`

**⚙️ Méthodes principales :**
- `index()` - Liste des genres avec pagination et recherche
- `create()` - Formulaire création genre
- `store()` - Traitement création genre
- `edit($id)` - Formulaire édition genre
- `update($id)` - Traitement mise à jour genre
- `delete($id)` - Suppression genre

**🎨 CSS :** Aucun CSS intégré  
**📄 Feuilles de style :** Aucune référence directe

**🔐 Sécurité :**
- Vérification rôle admin obligatoire
- Validation CSRF (temporairement désactivée pour tests)
- Validation des données d'entrée

**📊 Fonctionnalités :**
- **Pagination** (20 éléments par page)
- **Recherche** par nom
- **Comptage des jeux** par genre
- **Système de couleurs** pour l'interface
- **Validation des couleurs** hexadécimales

**🎨 Champs gérés :**
- Nom, description, couleur
- Validation format couleur (#rrggbb)

**⚠️ Note :** Sécurité temporairement désactivée pour les tests

---

### **32. app/controllers/admin/TagsController.php**
**📍 Emplacement :** `/app/controllers/admin/TagsController.php`  
**🎯 Fonction :** Contrôleur admin pour la gestion des tags (CRUD)  
**🔗 Interactions :**
- Hérite de `core/Controller.php`
- Utilise `core/Auth.php`
- Utilise `app/models/Tag.php`

**⚙️ Méthodes principales :**
- `index()` - Liste des tags avec filtres et pagination
- `create()` - Formulaire création tag
- `store()` - Traitement création tag
- `edit($id)` - Formulaire édition tag
- `update($id)` - Traitement mise à jour tag
- `searchTags()` - Recherche de tags pour autocomplétion
- `delete($id)` - Suppression tag

**🎨 CSS :** Aucun CSS intégré  
**📄 Feuilles de style :** Aucune référence directe

**🔐 Sécurité :**
- Vérification rôle admin obligatoire
- Validation CSRF sur toutes les actions
- Validation des données d'entrée

**📊 Fonctionnalités :**
- **Pagination** (20 éléments par page)
- **Recherche** par nom
- **Comptage des articles** par tag
- **API de recherche** pour autocomplétion
- **Validation des slugs** d'unicité

**🏷️ Champs gérés :**
- Nom (max 80 caractères)
- Slug (max 120 caractères)
- Validation d'unicité des slugs

**🔍 API :**
- `searchTags()` - Recherche pour autocomplétion
- Réponse JSON avec résultats

---

### **33. app/controllers/admin/ThemesController.php**
**📍 Emplacement :** `/app/controllers/admin/ThemesController.php`  
**🎯 Fonction :** Contrôleur admin pour la gestion des thèmes visuels  
**🔗 Interactions :**
- Hérite de `core/Controller.php`
- Utilise `core/Auth.php`, `core/Database.php`
- Utilise `config/theme.json`

**⚙️ Méthodes principales :**
- `index()` - Liste des thèmes disponibles
- `apply()` - Application d'un thème
- `scanThemes()` - Scanner les thèmes disponibles
- `getCurrentTheme()` - Récupérer le thème actuel

**🎨 CSS :** Aucun CSS intégré  
**📄 Feuilles de style :** Aucune référence directe

**🔐 Sécurité :**
- Vérification rôle admin obligatoire
- Validation des thèmes existants
- Confirmation pour thèmes permanents

**🎨 Fonctionnalités :**
- **Scanner automatique** des thèmes disponibles
- **Validation des images** (left.png, right.png)
- **Application temporaire** ou permanente
- **Gestion des dates d'expiration**
- **Logs d'activité** pour les changements
- **Configuration JSON** persistante

**📁 Structure des thèmes :**
- Dossier `/themes/{nom}/`
- Images obligatoires : `left.png`, `right.png`
- Configuration dans `config/theme.json`

**⚙️ Types d'application :**
- **Temporaire** : avec date d'expiration
- **Permanent** : avec confirmation obligatoire
- **Par défaut** : thème de base

---

### **34. app/controllers/admin/UploadController.php**
**📍 Emplacement :** `/app/controllers/admin/UploadController.php`  
**🎯 Fonction :** Contrôleur admin pour l'upload d'images avec génération de miniatures  
**🔗 Interactions :**
- Hérite de `core/Controller.php`
- Utilise `core/Auth.php`

**⚙️ Méthodes principales :**
- `image()` - Upload d'image principal
- `validateImage($file)` - Validation de l'image
- `processImage($file, $type)` - Traitement et sauvegarde
- `createThumbnail($sourcePath, $uploadDir, $filename)` - Création miniature
- `createImageFromFile($filepath, $mimeType)` - Création image source
- `saveImage($image, $filepath, $mimeType)` - Sauvegarde image
- `jsonResponse($data, $statusCode)` - Réponse JSON

**🎨 CSS :** Aucun CSS intégré  
**📄 Feuilles de style :** Aucune référence directe

**🔐 Sécurité :**
- Vérification rôles admin/editor
- Validation des types MIME
- Validation des extensions
- Vérification de la taille

**📸 Fonctionnalités :**
- **Upload sécurisé** d'images
- **Validation complète** (taille, type, extension)
- **Génération automatique** de miniatures (300x300)
- **Support multi-formats** (JPG, PNG, GIF, WebP)
- **Préservation transparence** (PNG, GIF)
- **Organisation par type** (article, game, etc.)
- **Réponse JSON** structurée

**⚙️ Configuration :**
- Taille max : 5MB
- Miniatures : 300x300 pixels
- Qualité : 85% (JPG), 8 (PNG)
- Types autorisés : JPG, PNG, GIF, WebP

**📁 Organisation :**
- Dossier : `/public/uploads/{type}/`
- Miniatures : préfixe `thumb_`

---

### **35. app/controllers/SpecialController.php**
**📍 Emplacement :** `/app/controllers/SpecialController.php`  
**🎯 Fonction :** Contrôleur spécial pour les routes utilitaires  
**🔗 Interactions :**
- Hérite de `core/Controller.php`
- Utilise `public/uploads.php`

**⚙️ Méthodes principales :**
- `uploads()` - Gestion de la route uploads.php

**🎨 CSS :** Aucun CSS intégré  
**📄 Feuilles de style :** Aucune référence directe

**🔧 Fonctionnalités :**
- **Proxy pour uploads.php** - Inclusion du fichier uploads
- **Gestion d'erreurs** - 404 si fichier manquant
- **Route utilitaire** - Simplification du routage

---

### **36. app/controllers/TestController.php**
**📍 Emplacement :** `/app/controllers/TestController.php`  
**🎯 Fonction :** Contrôleur de test pour vérifier le routage  
**🔗 Interactions :**
- Hérite de `core/Controller.php`
- Utilise `config/config.php`

**⚙️ Méthodes principales :**
- `index()` - Page de test principale
- `genres()` - Test route genres
- `games()` - Test route games

**🎨 CSS :** Aucun CSS intégré  
**📄 Feuilles de style :** Aucune référence directe

**🧪 Fonctionnalités de test :**
- **Vérification du routage** - Test des routes
- **Informations système** - PHP, serveur, BDD
- **Liens de test** - Navigation vers toutes les sections
- **Debug du routeur** - Affichage des informations de routage

**🔗 Liens de test inclus :**
- `/genres` - Gestion des genres
- `/games` - Gestion des jeux
- `/admin` - Tableau de bord
- `/articles` - Gestion des articles
- `/categories` - Gestion des catégories
- `/hardware` - Gestion du hardware
- `/media` - Gestion des médias
- `/users` - Gestion des utilisateurs

**📊 Informations affichées :**
- Version PHP
- Logiciel serveur
- Nom de la base de données
- URL de la requête
- Contrôleur et action

---
