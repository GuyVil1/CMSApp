# 📊 AUDIT - MODÈLES (MODELS)

## 📋 **Modèles de données de l'application**

---

### **12. app/models/Article.php**
**📍 Emplacement :** `/app/models/Article.php`  
**🎯 Fonction :** Modèle principal pour la gestion des articles avec mise en avant  
**🔗 Interactions :**
- Utilise `core/Database.php` pour les requêtes
- Lié aux modèles User, Category, Game, Media, Tag
- Utilisé par `app/controllers/admin/ArticlesController.php`
- Utilisé par `app/controllers/HomeController.php`

**⚙️ Propriétés :**
- `id`, `title`, `slug`, `excerpt`, `content`, `status`
- `cover_image_id`, `author_id`, `category_id`, `game_id`
- `published_at`, `featured_position`, `created_at`, `updated_at`
- Relations : `author`, `category`, `game`, `cover_image`, `tags`

**⚙️ Méthodes principales :**
- `create($data)` - Création nouvel article
- `update($data)` - Mise à jour article
- `delete()` - Suppression article
- `findById($id)` - Recherche par ID
- `findBySlug($slug)` - Recherche par slug
- `findAll($page, $perPage, $filters)` - Liste avec pagination
- `archive()` - Archivage article
- `publish()` - Publication article
- `draft()` - Mise en brouillon
- `getFeaturedArticles($limit)` - Articles en avant
- `addTags($tagIds)` - Ajout tags
- `generateSlug($title, $excludeId)` - Génération slug unique
- `isPositionAvailable($position, $excludeId)` - Vérification position
- `replaceArticleInPosition($position, $newArticleId)` - Remplacement position
- `freePosition($position)` - Libération position

**🎨 CSS :** Aucun CSS intégré  
**📄 Feuilles de style :** Aucune référence

**🔗 Relations :**
- **User** (author_id) - Auteur de l'article
- **Category** (category_id) - Catégorie de l'article
- **Game** (game_id) - Jeu associé
- **Media** (cover_image_id) - Image de couverture
- **Tag** (many-to-many) - Tags de l'article

---

### **13. app/models/User.php**
**📍 Emplacement :** `/app/models/User.php`  
**🎯 Fonction :** Modèle pour la gestion des utilisateurs et leurs rôles  
**🔗 Interactions :**
- Utilise `core/Database.php` pour les requêtes
- Utilise `core/Auth.php` pour l'authentification
- Utilisé par `app/controllers/admin/UsersController.php`
- Utilisé par `app/controllers/AuthController.php`

**⚙️ Propriétés :**
- `id`, `login`, `email`, `password_hash`, `role_id`, `role_name`
- `created_at`, `updated_at`, `last_login`

**⚙️ Méthodes principales :**
- `findById($id)` - Recherche par ID
- `findByLogin($login)` - Recherche par login/email
- `findByEmail($email)` - Recherche par email
- `findAll()` - Liste tous les utilisateurs
- `create($data)` - Création nouvel utilisateur
- `update($id, $data)` - Mise à jour utilisateur
- `delete($id)` - Suppression utilisateur
- `getRoles()` - Récupération rôles disponibles
- `countWithConditions($query, $params)` - Comptage avec conditions
- `findWithQuery($query, $params)` - Recherche avec requête personnalisée
- `hasRole($role)` - Vérification rôle spécifique
- `hasPermission($requiredRole)` - Vérification permissions
- `toArray()` - Conversion en tableau

**🎨 CSS :** Aucun CSS intégré  
**📄 Feuilles de style :** Aucune référence

**🔐 Sécurité :**
- Hashage mots de passe avec `password_hash()`
- Validation unicité login/email
- Hiérarchie des rôles (admin > editor > author > member)

---

### **14. app/models/Setting.php**
**📍 Emplacement :** `/app/models/Setting.php`  
**🎯 Fonction :** Modèle pour la gestion des paramètres du site  
**🔗 Interactions :**
- Utilise `core/Database.php` pour les requêtes
- Utilisé par `app/controllers/admin/SettingsController.php`
- Utilisé par les layouts pour les paramètres globaux

**⚙️ Propriétés :**
- `id`, `key`, `value`, `description`

**⚙️ Méthodes principales :**
- `get($key, $default)` - Récupération option par clé
- `set($key, $value, $description)` - Définition option
- `getAll()` - Récupération toutes les options
- `isEnabled($key)` - Vérification si option activée
- `initDefaults()` - Initialisation options par défaut

**🎨 CSS :** Aucun CSS intégré  
**📄 Feuilles de style :** Aucune référence

**⚙️ Paramètres gérés :**
- `allow_registration` - Autorisation inscriptions
- `dark_mode` - Mode sombre
- `maintenance_mode` - Mode maintenance
- `default_theme` - Thème par défaut
- `footer_tagline` - Phrase d'accroche footer
- `social_*` - Réseaux sociaux
- `header_logo`, `footer_logo` - Logos
- `legal_*_content` - Contenu pages légales

---

### **15. app/models/Category.php**
**📍 Emplacement :** `/app/models/Category.php`  
**🎯 Fonction :** Modèle pour la gestion des catégories d'articles  
**🔗 Interactions :**
- Utilise `core/Database.php` pour les requêtes
- Utilisé par `app/controllers/admin/CategoriesController.php`
- Lié au modèle Article (relation one-to-many)

**⚙️ Propriétés :**
- `id`, `name`, `slug`, `description`, `createdAt`

**⚙️ Méthodes principales :**
- `find($id)` - Recherche par ID
- `findBySlug($slug)` - Recherche par slug
- `findAll()` - Liste toutes les catégories
- `create($data)` - Création nouvelle catégorie
- `update($data)` - Mise à jour catégorie
- `delete()` - Suppression catégorie (avec vérification articles)
- `getArticles($limit, $offset)` - Articles de cette catégorie
- `getArticlesCount()` - Nombre d'articles
- `generateSlug($name)` - Génération slug
- `slugExists($slug, $excludeId)` - Vérification slug unique
- `countWithConditions($query, $params)` - Comptage avec conditions
- `findWithQuery($query, $params)` - Recherche avec requête personnalisée
- `toArray()` - Conversion en tableau

**🎨 CSS :** Aucun CSS intégré  
**📄 Feuilles de style :** Aucune référence

**🔗 Relations :**
- **Article** (one-to-many) - Articles de cette catégorie

---

### **16. app/models/Game.php**
**📍 Emplacement :** `/app/models/Game.php`  
**🎯 Fonction :** Modèle pour la gestion des jeux vidéo avec liens d'achat  
**🔗 Interactions :**
- Utilise `core/Database.php` pour les requêtes
- Utilisé par `app/controllers/admin/GamesController.php`
- Lié aux modèles Article, Media, Hardware, Genre

**⚙️ Propriétés :**
- `id`, `title`, `slug`, `description`, `platform`, `genreId`, `coverImageId`, `hardwareId`
- `releaseDate`, `createdAt`, `score`, `isTested`, `developer`, `publisher`, `pegiRating`
- Liens d'achat : `steamUrl`, `eshopUrl`, `xboxUrl`, `psnUrl`, `epicUrl`, `gogUrl`

**⚙️ Méthodes principales :**
- `find($id)` - Recherche par ID
- `findBySlug($slug)` - Recherche par slug
- `findAll($limit, $offset)` - Liste avec pagination
- `search($query, $limit)` - Recherche par titre
- `count()` - Nombre total de jeux
- `getPlatforms()` - Plateformes uniques
- `getGenres()` - Genres disponibles
- `create($data)` - Création nouveau jeu
- `update($data)` - Mise à jour jeu
- `delete()` - Suppression jeu
- `getArticles($limit)` - Articles liés au jeu
- `getArticlesCount()` - Nombre d'articles
- `getCoverImage()` - Image de couverture
- `getCoverImageUrl()` - URL image de couverture
- `isReleased()` - Vérification si sorti
- `getReleaseStatus()` - Statut de sortie
- `generateSlug($title)` - Génération slug
- `slugExists($slug, $excludeId)` - Vérification slug unique
- `getHardware()` - Hardware associé
- `getHardwareName()` - Nom du hardware
- `getGenre()` - Genre associé
- `getGenreName()` - Nom du genre
- `toArray()` - Conversion en tableau

**🎨 CSS :** Aucun CSS intégré  
**📄 Feuilles de style :** Aucune référence

**🔗 Relations :**
- **Article** (one-to-many) - Articles sur ce jeu
- **Media** (one-to-many) - Images/vidéos du jeu
- **Hardware** (many-to-one) - Plateforme de jeu
- **Genre** (many-to-one) - Genre du jeu

---

### **17. app/models/Media.php**
**📍 Emplacement :** `/app/models/Media.php`  
**🎯 Fonction :** Modèle pour la gestion des uploads d'images et médias  
**🔗 Interactions :**
- Utilise `core/Database.php` pour les requêtes
- Utilisé par `app/controllers/admin/MediaController.php`
- Utilisé par `app/controllers/admin/UploadController.php`
- Lié aux modèles User, Game

**⚙️ Propriétés :**
- `id`, `filename`, `originalName`, `mimeType`, `size`, `uploadedBy`
- `gameId`, `mediaType`, `createdAt`

**⚙️ Méthodes principales :**
- `find($id)` - Recherche par ID
- `findById($id)` - Alias pour compatibilité
- `search($query, $limit)` - Recherche par texte
- `searchWithFilters($filters, $limit, $offset)` - Recherche avec filtres
- `countWithFilters($filters)` - Comptage avec filtres
- `findAll($limit, $offset)` - Liste avec pagination
- `findAllImages()` - Toutes les images
- `count()` - Nombre total de médias
- `findByMimeType($mimeType, $limit)` - Par type MIME
- `create($data)` - Création nouveau média
- `delete()` - Suppression média (fichier + BDD)
- `getFilePath()` - Chemin du fichier
- `getUrl()` - URL du fichier
- `getThumbnailUrl()` - URL de la vignette
- `fileExists()` - Vérification existence fichier
- `getFormattedSize()` - Taille formatée
- `isImage()` - Vérification si image
- `isVideo()` - Vérification si vidéo
- `findByGame($gameId, $mediaType)` - Médias d'un jeu
- `findCoverByGame($gameId)` - Cover d'un jeu
- `createGameDirectory($gameSlug)` - Création dossier jeu
- `getGameFilePath()` - Chemin fichier pour jeu
- `getGameUrl()` - URL fichier pour jeu
- `deleteThumbnails()` - Suppression vignettes
- `toArray()` - Conversion en tableau

**🎨 CSS :** Aucun CSS intégré  
**📄 Feuilles de style :** Aucune référence

**🔗 Relations :**
- **User** (many-to-one) - Utilisateur qui a uploadé
- **Game** (many-to-one) - Jeu associé

**🖼️ Gestion des fichiers :**
- Uploads sécurisés via uploads.php
- Génération automatique de vignettes
- Gestion des dossiers par jeu
- Suppression automatique des fichiers

---

### **18. app/models/Tag.php**
**📍 Emplacement :** `/app/models/Tag.php`  
**🎯 Fonction :** Modèle pour la gestion des tags d'articles  
**🔗 Interactions :**
- Utilise `core/Database.php` pour les requêtes
- Utilisé par `app/controllers/admin/TagsController.php`
- Lié au modèle Article (relation many-to-many)

**⚙️ Propriétés :**
- `id`, `name`, `slug`

**⚙️ Méthodes principales :**
- `findAll()` - Liste tous les tags
- `findById($id)` - Recherche par ID
- `findBySlug($slug)` - Recherche par slug
- `findByName($name)` - Recherche par nom
- `create($data)` - Création nouveau tag
- `update($id, $data)` - Mise à jour tag
- `delete($id)` - Suppression tag
- `count()` - Nombre total de tags
- `findAllWithArticleCount()` - Tags avec nombre d'articles
- `exists($id)` - Vérification existence
- `slugExists($slug, $excludeId)` - Vérification slug unique
- `countWithConditions($query, $params)` - Comptage avec conditions
- `findWithQuery($query, $params)` - Recherche avec requête personnalisée
- `generateSlug($name)` - Génération slug
- `findByArticleId($articleId)` - Tags d'un article
- `search($search)` - Recherche par nom

**🎨 CSS :** Aucun CSS intégré  
**📄 Feuilles de style :** Aucune référence

**🔗 Relations :**
- **Article** (many-to-many) - Articles avec ce tag

---
