# 📋 Documentation Exhaustive - Belgium Vidéo Gaming CMS

## 🏗️ **Architecture Générale**

### **Type d'Application**
- **CMS (Content Management System)** pour site de gaming belge
- **Architecture MVC** (Model-View-Controller)
- **Framework PHP personnalisé** avec routage automatique
- **Interface d'administration** complète
- **Système d'authentification** sécurisé
- **CSS modulaire** avec externalisation complète

### **Structure des Dossiers**
```
www/
├── app/                    # Application principale
│   ├── controllers/        # Contrôleurs MVC
│   ├── models/            # Modèles de données
│   ├── views/             # Vues/templates
│   └── helpers/           # Fonctions utilitaires
├── core/                  # Framework core
├── config/                # Configuration
├── database/              # Schéma et données
├── public/                # Assets publics
│   └── assets/css/        # CSS modulaire
│       ├── layout/        # Layout (header, footer, grid)
│       ├── pages/         # Pages spécifiques
│       └── components/    # Composants réutilisables
├── themes/                # Thèmes visuels
├── index.php             # Point d'entrée principal
├── admin.css             # CSS admin (temporaire)
├── style.css             # CSS public (temporaire)
└── *.php                 # Fichiers de routage temporaires
```

---

## 🔧 **FICHIERS CORE (Framework)**

### **`index.php`** - Point d'entrée principal
**Rôle** : Routeur principal de l'application
**Fonctionnalités** :
- Parsing des URLs et routage automatique
- Gestion des namespaces (Admin vs public)
- Conversion des paramètres d'URL en types appropriés
- Gestion des erreurs 404/500
- Initialisation de la session

**Routes supportées** :
- `/` → HomeController::index()
- `/auth/*` → AuthController
- `/admin/*` → Admin\*Controller
- `/admin/articles/*` → Admin\ArticlesController
- `/admin/media/*` → Admin\MediaController
- `/admin/themes/*` → Admin\ThemesController

### **`core/Database.php`** - Gestionnaire de base de données
**Rôle** : Abstraction de la base de données
**Méthodes principales** :
- `connect()` : Connexion PDO
- `query()` : Requêtes SELECT avec paramètres
- `execute()` : Requêtes INSERT/UPDATE/DELETE
- `lastInsertId()` : Récupération du dernier ID inséré

### **`core/Controller.php`** - Classe de base des contrôleurs
**Rôle** : Classe parent pour tous les contrôleurs
**Méthodes** :
- `render()` : Rendu des vues avec données
- `redirectTo()` : Redirection HTTP
- `setFlash()` : Messages flash de session
- `jsonResponse()` : Réponses JSON

### **`core/Auth.php`** - Système d'authentification
**Rôle** : Gestion complète de l'authentification
**Fonctionnalités** :
- Connexion/déconnexion sécurisée
- Sessions avec paramètres de sécurité
- Vérification des permissions
- Logs d'activité
- Tokens CSRF
- Gestion des mots de passe hashés

---

## 🔧 **FICHIERS TEMPORAIRES (Solutions de contournement)**

### **Fichiers de routage temporaires**
**Problème** : Le `.htaccess` ne fonctionne pas correctement sur WAMP pour les routes admin
**Solution** : Fichiers PHP temporaires qui simulent les routes admin

**Fichiers créés** :
- `admin.php` → Simule `/admin/dashboard`
- `articles.php` → Simule `/admin/articles`
- `media.php` → Simule `/admin/media`
- `themes.php` → Simule `/admin/themes`
- `games.php` → Simule `/admin/games`

**Utilisation** : Ces fichiers seront supprimés une fois WAMP configuré correctement

### **Fichiers CSS temporaires**
**Problème** : Le serveur ne peut pas servir les fichiers CSS depuis `public/assets/css/`
**Solution** : Fichiers CSS consolidés à la racine

**Fichiers créés** :
- `admin.css` → Tous les styles admin consolidés
- `style.css` → Tous les styles public consolidés

**Utilisation** : Ces fichiers seront déplacés dans `public/assets/css/` une fois le serveur configuré

---

## 🎮 **CONTROLEURS (Controllers)**

### **`app/controllers/HomeController.php`** - Page d'accueil publique
**Rôle** : Affichage de la page d'accueil
**Fonctionnalités** :
- Récupération des articles en avant (featured)
- Récupération des derniers articles
- Gestion de l'état de connexion
- Affichage des thèmes disponibles

### **`app/controllers/AuthController.php`** - Authentification
**Rôle** : Gestion de l'authentification utilisateur
**Méthodes** :
- `login()` : Connexion utilisateur
- `logout()` : Déconnexion
- `register()` : Inscription (préparé)

### **`app/controllers/admin/DashboardController.php`** - Tableau de bord admin
**Rôle** : Interface d'administration principale
**Fonctionnalités** :
- Statistiques des articles
- Statistiques des médias
- Activité récente
- Liens vers les différentes sections

---

## 🔧 **CORRECTIONS RÉCENTES (Dernière mise à jour)**

### **Problèmes résolus** :
1. **Routage admin** : Création de fichiers temporaires pour contourner les problèmes de `.htaccess`
2. **CSS externalisé** : Tous les styles inline ont été déplacés vers des fichiers CSS externes
3. **Conversion de types** : Correction du routage pour convertir automatiquement les paramètres string en int
4. **Page média** : Ajout des styles manquants pour la gestion des médias
5. **Thèmes dynamiques** : Application correcte des thèmes sur la page d'accueil

### **Améliorations apportées** :
- **CSS modulaire** : Organisation en fichiers séparés (variables, reset, typography, etc.)
- **Responsive design** : Toutes les pages sont maintenant responsive
- **Performance** : CSS externalisé améliore les performances
- **Maintenabilité** : Code plus propre et organisé

### **État actuel** :
- ✅ Page d'accueil fonctionnelle avec thèmes dynamiques
- ✅ Connexion admin fonctionnelle
- ✅ Toutes les pages admin fonctionnent (dashboard, articles, médias, thèmes, jeux)
- ✅ CSS externalisé et fonctionnel
- ✅ Création et gestion d'articles opérationnelle
- ✅ Publication/dépublier d'articles fonctionnel

### **`app/controllers/admin/ArticlesController.php`** - Gestion des articles
**Rôle** : CRUD complet des articles
**Méthodes** :
- `index()` : Liste des articles avec pagination
- `create()` : Formulaire de création
- `store()` : Sauvegarde d'un nouvel article
- `edit()` : Formulaire d'édition
- `update()` : Mise à jour d'un article
- `delete()` : Suppression d'un article
- `publish()` : Publication d'un article
- `draft()` : Mise en brouillon
- `archive()` : Archivage

**Fonctionnalités avancées** :
- Upload et gestion d'images de couverture
- Gestion des tags et catégories
- Position en avant (featured)
- Association avec des jeux
- Création automatique de vignettes

### **`app/controllers/admin/MediaController.php`** - Gestion des médias
**Rôle** : Gestion complète des fichiers médias
**Méthodes** :
- `index()` : Liste des médias
- `upload()` : Upload de fichiers
- `delete()` : Suppression de médias
- `search()` : Recherche de médias
- `byType()` : Filtrage par type

**Fonctionnalités** :
- Support JPG, PNG, WebP, GIF
- Création automatique de vignettes
- Gestion des permissions
- Cache temporaire en session
- API JSON pour intégration

### **`app/controllers/admin/ThemesController.php`** - Gestion des thèmes
**Rôle** : Gestion des thèmes visuels
**Fonctionnalités** :
- Liste des thèmes disponibles
- Activation/désactivation
- Upload d'images de thème

### **`app/controllers/admin/GamesController.php`** - Gestion des jeux
**Rôle** : CRUD des jeux avec gestion des genres
**État** : Fonctionnel avec système de genres
**Nouvelles fonctionnalités** :
- Intégration du modèle Genre
- Formulaire avec menu déroulant dynamique des genres
- Gestion du `genre_id` au lieu de `genre` (string)
- Validation et gestion des erreurs avec genres

### **`app/controllers/admin/GenresController.php`** - Gestion des genres (NOUVEAU)
**Rôle** : CRUD complet des genres de jeux
**Fonctionnalités** :
- Liste des genres avec comptage des jeux
- Création de nouveaux genres
- Édition des genres existants
- Suppression sécurisée (vérification d'usage)
- Interface d'administration complète
- Gestion des couleurs personnalisées
- Validation des données

### **`app/controllers/admin/UploadController.php`** - Upload général
**Rôle** : Gestion des uploads (préparé)
**État** : Structure de base créée

---

## 📊 **MODELES (Models)**

### **`app/models/Article.php`** - Modèle Article
**Rôle** : Gestion des données des articles
**Propriétés** :
- id, title, excerpt, content
- category_id, game_id, featured_position
- cover_image_id, status, created_at, updated_at

**Méthodes principales** :
- `create()` : Création d'un article
- `update()` : Mise à jour
- `delete()` : Suppression
- `findById()` : Recherche par ID
- `findAll()` : Liste avec pagination
- `findFeatured()` : Articles en avant
- `findByStatus()` : Filtrage par statut

### **`app/models/Media.php`** - Modèle Media
**Rôle** : Gestion des fichiers médias
**Propriétés** :
- id, filename, original_name
- mime_type, size, uploaded_by

**Méthodes** :
- `create()` : Enregistrement d'un média
- `getUrl()` : URL publique
- `getThumbnailUrl()` : URL de la vignette
- `getFormattedSize()` : Taille formatée

### **`app/models/Category.php`** - Modèle Catégorie
**Rôle** : Gestion des catégories d'articles
**État** : Structure de base

### **`app/models/Game.php`** - Modèle Jeu
**Rôle** : Gestion des jeux associés aux articles
**État** : Structure de base
**Nouvelles fonctionnalités** :
- Association avec les genres de jeux
- Gestion du `genre_id` au lieu de `genre` (string)
- Méthodes `getGenre()`, `getGenreName()` pour récupérer les informations du genre

### **`app/models/Genre.php`** - Modèle Genre (NOUVEAU)
**Rôle** : Gestion des genres de jeux
**Propriétés** :
- id, name, description, color
- created_at, updated_at

**Méthodes principales** :
- `findAll()` : Liste de tous les genres
- `find($id)` : Recherche par ID
- `create($data)` : Création d'un genre
- `update($id, $data)` : Mise à jour
- `delete($id)` : Suppression (avec vérification d'usage)
- `findAllWithGameCount()` : Genres avec nombre de jeux
- `search($query)` : Recherche par nom

### **`app/models/Tag.php`** - Modèle Tag
**Rôle** : Gestion des tags d'articles
**État** : Structure de base

### **`app/models/User.php`** - Modèle Utilisateur
**Rôle** : Gestion des utilisateurs
**État** : Structure de base

### **`app/models/Setting.php`** - Modèle Configuration
**Rôle** : Gestion des paramètres du site
**État** : Structure de base

---

## 🎨 **VUES (Views)**

### **`app/views/home/index.php`** - Page d'accueil
**Rôle** : Interface publique principale
**Sections** :
- Header avec navigation et authentification
- Section "En avant" (articles mis en avant)
- Section "Derniers articles"
- Footer

**Classes CSS identifiées** :
#### **Variables CSS (Root)**
```css
:root {
    --belgium-red: #CC0000;
    --belgium-yellow: #E6B800;
    --belgium-black: #000000;
    --primary: #1a1a1a;
    --secondary: #2d2d2d;
    --tertiary: #404040;
    --border: #e5e5e5;
    --muted: #f5f5f5;
    --background: #ffffff;
    --text: #ffffff;
    --text-muted: #a0a0a0;
    --success: #44ff44;
    --error: #ff4444;
    --warning: #ffaa00;
}
```

#### **Layout & Structure**
```css
/* Conteneurs principaux */
.container, .main-layout, .main-content
.header, .header-content, .footer
.admin-container, .admin-header

/* Grilles et layouts */
.stats-grid, .news-layout, .featured-grid
.media-grid, .articles-grid
```

#### **Navigation & Header**
```css
/* Logo et branding */
.logo, .logo-icon, .logo-text, .logo-subtitle
.header-title

/* Navigation */
.nav, .nav-links, .nav-link
.admin-nav, .admin-nav-item
```

#### **Boutons & Actions**
```css
/* Boutons principaux */
.btn, .btn-success, .btn-warning, .btn-danger, .btn-info, .btn-secondary
.btn-sm

/* Boutons spécifiques */
.login-btn, .logout-btn
.article-actions, .media-actions
```

#### **Formulaires**
```css
/* Structure des formulaires */
.form-container, .form-section, .form-group
.form-label, .form-input, .form-textarea, .form-select
.form-submit, .form-checkbox

/* Filtres */
.filters, .upload-section, .upload-area
```

#### **Articles & Contenu**
```css
/* Sections d'articles */
.featured-section, .featured-grid, .featured-item
.featured-left, .featured-right, .featured-main, .featured-bottom
.featured-small, .featured-overlay, .featured-content
.featured-badge, .featured-title, .featured-excerpt

/* Cartes d'articles */
.article-card, .article-image, .article-content
.article-header, .article-badge, .article-date
.article-title, .article-excerpt

/* Badges de statut */
.badge-test, .badge-news, .badge-guide
.status, .status-draft, .status-published, .status-archived
.featured-position
```

#### **Médias & Images**
```css
/* Gestion des médias */
.media-card, .media-preview, .media-content
.upload-zone, .upload-progress, .upload-icon
.upload-text, .upload-hint

/* Images et vignettes */
.article-image, .featured-image, .trailer-image
```

#### **Trailers & Vidéos**
```css
/* Section trailers */
.trailers-header, .trailers-icon, .trailers-title
.trailers-container, .trailer-item, .trailer-overlay
.trailer-play, .trailer-play-icon, .trailer-duration
.trailer-title
```

#### **Sections & Tabs**
```css
/* Sections générales */
.section, .section-header, .section-line, .section-title
.news-tabs, .tabs-list, .tab-trigger, .tab-content

/* Lignes décoratives */
.section-line.yellow, .section-line.red
```

#### **Statistiques & Dashboard**
```css
/* Cartes de statistiques */
.stat-card, .stat-number, .stat-label
.user-info, .user-details, .user-detail
```

#### **Tableaux & Listes**
```css
/* Tableaux */
.table-container, .articles-table, .article-row
.actions, .pagination
```

#### **Utilitaires**
```css
/* Espacement */
.text-center, .mb-4, .mt-4

/* États */
.dragover, .active, .current, .hover
```

### **`app/views/auth/login.php`** - Page de connexion
**Rôle** : Formulaire d'authentification
**Classes CSS** :
```css
.auth-container, .auth-form, .form-group
.form-input, .form-button, .error-message
```

### **`app/views/admin/dashboard/index.php`** - Tableau de bord admin
**Rôle** : Interface d'administration principale
**Classes CSS** :
```css
.admin-container, .admin-header, .stats-grid
.stat-card, .stat-number, .stat-label
.admin-nav, .admin-nav-item
```

### **`app/views/admin/articles/index.php`** - Liste des articles
**Rôle** : Gestion des articles
**Classes CSS** :
```css
.articles-header, .articles-table, .article-row
.article-actions, .btn-success, .btn-warning, .btn-danger
.status-badge, .pagination
```

### **`app/views/admin/articles/form.php`** - Formulaire article
**Rôle** : Création/édition d'articles
**Classes CSS** :
```css
.form-container, .form-section, .form-group
.form-label, .form-input, .form-textarea
.form-select, .form-checkbox, .form-submit
.media-picker, .tag-selector
```

### **`app/views/admin/media/index.php`** - Gestion des médias
**Rôle** : Interface de gestion des médias
**Classes CSS** :
```css
.media-container, .media-grid, .media-item
.media-upload, .media-preview, .media-actions
.upload-zone, .upload-progress
```

### **`app/views/admin/genres/index.php`** - Liste des genres (NOUVEAU)
**Rôle** : Interface de gestion des genres
**Fonctionnalités** :
- Tableau des genres avec informations détaillées
- Affichage des couleurs personnalisées
- Comptage des jeux par genre
- Actions de modification et suppression
- Modal de confirmation pour suppression
- Navigation vers création/édition

### **`app/views/admin/genres/create.php`** - Création de genre (NOUVEAU)
**Rôle** : Formulaire de création de genre
**Fonctionnalités** :
- Formulaire avec validation
- Sélecteur de couleur avec preview
- Synchronisation color picker / champ texte
- Validation hexadécimale
- Interface responsive

### **`app/views/admin/genres/edit.php`** - Édition de genre (NOUVEAU)
**Rôle** : Formulaire de modification de genre
**Fonctionnalités** :
- Édition des informations existantes
- Affichage des informations système
- Validation et gestion des erreurs
- Interface cohérente avec la création

---

## 🎨 **ANALYSE CSS ET RECOMMANDATIONS**

### **Styles Actuellement Inclus**
Tous les styles sont actuellement codés directement dans les fichiers PHP avec des balises `<style>`.

### **Classes CSS Identifiées**

#### **Layout & Structure**
- `.container`, `.header`, `.main-content`, `.footer`
- `.admin-container`, `.admin-header`, `.admin-content`

#### **Navigation**
- `.nav`, `.nav-links`, `.nav-link`, `.logo`
- `.admin-nav`, `.admin-nav-item`

#### **Boutons & Actions**
- `.btn`, `.btn-success`, `.btn-warning`, `.btn-danger`, `.btn-info`
- `.login-btn`, `.logout-btn`
- `.article-actions`, `.media-actions`

#### **Formulaires**
- `.form-container`, `.form-group`, `.form-label`
- `.form-input`, `.form-textarea`, `.form-select`
- `.form-submit`, `.form-checkbox`

#### **Articles & Contenu**
- `.article-card`, `.article-title`, `.article-excerpt`
- `.featured-section`, `.featured-grid`, `.featured-item`
- `.articles-grid`, `.articles-section`

#### **Médias**
- `.media-grid`, `.media-item`, `.media-preview`
- `.upload-zone`, `.upload-progress`

#### **Utilitaires**
- `.text-center`, `.mb-4`, `.mt-4`
- `.status-badge`, `.pagination`

### **Recommandations d'Organisation CSS**

#### **Structure Proposée**
```
public/assets/css/
├── base/
│   ├── reset.css          # Reset/normalize
│   ├── typography.css     # Styles de texte
│   └── variables.css      # Variables CSS
├── components/
│   ├── buttons.css        # Styles des boutons
│   ├── forms.css          # Styles des formulaires
│   ├── navigation.css     # Styles de navigation
│   └── cards.css          # Styles des cartes
├── layout/
│   ├── grid.css           # Système de grille
│   ├── header.css         # Styles du header
│   └── footer.css         # Styles du footer
├── pages/
│   ├── home.css           # Styles page d'accueil
│   ├── admin.css          # Styles admin
│   └── auth.css           # Styles authentification
└── main.css               # Fichier principal
```

#### **Avantages de cette Organisation**
1. **Maintenabilité** : Styles organisés par fonction
2. **Réutilisabilité** : Composants modulaires
3. **Performance** : Chargement optimisé
4. **Évolutivité** : Facile d'ajouter de nouveaux styles

#### **Plan de Migration CSS Recommandé**

**Phase 1 : Extraction des variables**
```css
/* public/assets/css/base/variables.css */
:root {
    /* Couleurs Belgique */
    --belgium-red: #CC0000;
    --belgium-yellow: #E6B800;
    --belgium-black: #000000;
    
    /* Couleurs système */
    --primary: #1a1a1a;
    --secondary: #2d2d2d;
    --tertiary: #404040;
    --border: #e5e5e5;
    --muted: #f5f5f5;
    --background: #ffffff;
    --text: #ffffff;
    --text-muted: #a0a0a0;
    
    /* États */
    --success: #44ff44;
    --error: #ff4444;
    --warning: #ffaa00;
    
    /* Espacements */
    --spacing-xs: 0.25rem;
    --spacing-sm: 0.5rem;
    --spacing-md: 1rem;
    --spacing-lg: 1.5rem;
    --spacing-xl: 2rem;
    
    /* Bordures */
    --border-radius: 8px;
    --border-radius-sm: 4px;
    --border-radius-lg: 15px;
}
```

**Phase 2 : Composants réutilisables**
```css
/* public/assets/css/components/buttons.css */
.btn {
    display: inline-flex;
    align-items: center;
    gap: var(--spacing-sm);
    padding: var(--spacing-sm) var(--spacing-md);
    border: none;
    border-radius: var(--border-radius-sm);
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
}

.btn-primary { background: var(--belgium-red); color: white; }
.btn-secondary { background: var(--belgium-yellow); color: var(--belgium-black); }
.btn-success { background: var(--success); color: white; }
.btn-warning { background: var(--warning); color: white; }
.btn-danger { background: var(--error); color: white; }
```

**Phase 3 : Layouts spécifiques**
```css
/* public/assets/css/pages/home.css */
.featured-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    max-height: 80vh;
    border-radius: var(--border-radius);
    overflow: hidden;
    border: 2px solid var(--border);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

/* public/assets/css/pages/admin.css */
.admin-container {
    background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
    min-height: 100vh;
    color: var(--text);
}
```

**Phase 4 : Optimisation**
- Minification des fichiers CSS
- Compression gzip
- Cache des navigateurs
- Chargement asynchrone

---

## 🔧 **CONFIGURATION**

### **`config/config.php`** - Configuration principale
**Rôle** : Paramètres globaux de l'application
**Contenu** :
- Configuration de la base de données
- Paramètres de sécurité
- Configuration des sessions
- URLs de base

### **`config/theme.json`** - Configuration des thèmes
**Rôle** : Définition des thèmes disponibles
**Structure** :
- Liste des thèmes
- Images associées
- Paramètres de configuration

### **`config/env.example`** - Template d'environnement
**Rôle** : Exemple de configuration d'environnement

---

## 🗄️ **BASE DE DONNÉES**

### **`database/schema.sql`** - Schéma de base de données
**Tables principales** :
- `users` : Utilisateurs
- `articles` : Articles
- `categories` : Catégories
- `tags` : Tags
- `games` : Jeux
- `media` : Fichiers médias
- `article_tag` : Relation articles-tags
- `settings` : Paramètres du site

### **`database/seeds.sql`** - Données de test
**Rôle** : Données initiales pour le développement

### **`database/create_genres_table.sql`** - Création de la table genres (NOUVEAU)
**Rôle** : Création de la table des genres de jeux
**Structure** :
```sql
CREATE TABLE genres (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) UNIQUE NOT NULL,
  description TEXT,
  color VARCHAR(7) DEFAULT '#007bff',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```
**Données initiales** : 15 genres prédéfinis (Action, RPG, Stratégie, etc.)

### **`database/update_games_table.sql`** - Mise à jour de la table games (NOUVEAU)
**Rôle** : Ajout de la colonne genre_id à la table games
**Modifications** :
- Ajout de `genre_id INT` avec clé étrangère
- Contrainte `fk_games_genre` vers `genres(id)`
- Mise à jour des jeux existants avec genre par défaut

---

## 📁 **ASSETS PUBLICS**

### **`public/assets/`** - Ressources publiques
- `css/` : Styles (à organiser)
- `js/` : Scripts JavaScript
- `images/` : Images par défaut

---

## 🚀 **JAVASCRIPT & FONCTIONNALITÉS AVANCÉES**

### **`public/js/wysiwyg-editor.js`** - Éditeur WYSIWYG de base
**Rôle** : Éditeur de texte riche pour les articles
**Fonctionnalités** :
- Formatage de texte (gras, italique, souligné)
- Listes (à puces et numérotées)
- Insertion de liens et images
- Titres (H2, H3)
- Interface sans dépendances externes
- Raccourcis clavier (Ctrl+B, Ctrl+I, Ctrl+U)

**Classes CSS associées** :
```css
.wysiwyg-toolbar, .toolbar-group, .toolbar-btn
.wysiwyg-editor, .editor-content
```

### **`public/js/advanced-wysiwyg.js`** - Éditeur WYSIWYG avancé
**Rôle** : Éditeur complet avec fonctionnalités étendues
**Fonctionnalités** :
- Éditeur plein écran
- Gestion des médias intégrée
- Sauvegarde automatique
- Mode prévisualisation
- Gestion des thèmes
- Export en différents formats

### **`public/js/editor/`** - Système d'édition modulaire

#### **`editor-loader.js`** - Chargeur d'éditeur
**Rôle** : Initialisation et configuration des éditeurs
**Fonctionnalités** :
- Détection automatique du type d'éditeur
- Chargement des modules nécessaires
- Configuration dynamique

#### **`FullscreenEditor.js`** - Éditeur plein écran
**Rôle** : Éditeur avancé en mode plein écran
**Fonctionnalités** :
- Interface immersive
- Barre d'outils flottante
- Gestion des raccourcis clavier
- Sauvegarde en temps réel
- Mode distraction-free

#### **`editor/core/`** - Modules de base
- **`BaseModule.js`** : Classe de base pour les modules
- **`StyleManager.js`** : Gestion des styles d'édition

#### **`editor/modules/`** - Modules spécialisés
- Modules pour différentes fonctionnalités d'édition
- Système modulaire extensible

### **`public/js/test-editor.js`** - Tests de l'éditeur
**Rôle** : Tests et validation des éditeurs
**Fonctionnalités** :
- Tests unitaires
- Validation des fonctionnalités
- Tests de performance

---

## 🎨 **SYSTÈME DE THÈMES**

### **`config/theme.json`** - Configuration des thèmes
**Rôle** : Définition des thèmes disponibles
**Structure** :
```json
{
  "themes": [
    {
      "name": "defaut",
      "display_name": "Thème par défaut",
      "description": "Thème principal du site",
      "images": {
        "left": "themes/defaut/left.png",
        "right": "themes/defaut/right.png"
      }
    }
  ]
}
```

### **`theme-image.php`** - Générateur d'images de thème
**Rôle** : Service de génération d'images de thème
**Fonctionnalités** :
- Génération dynamique d'images
- Support des formats PNG/JPEG
- Cache des images générées
- Gestion des erreurs

### **`themes/`** - Dossier des thèmes
**Structure** :
```
themes/
├── defaut/
│   ├── left.png
│   └── right.png
└── folk/
    ├── left.png
    └── right.png
```

---

## 🔧 **FICHIERS UTILITAIRES**

### **`image.php`** - Gestionnaire d'images
**Rôle** : Service de traitement d'images
**Fonctionnalités** :
- Redimensionnement automatique
- Création de vignettes
- Optimisation des formats
- Cache des images traitées

### **`info.php`** - Informations système
**Rôle** : Affichage des informations PHP
**Utilisation** : Debug et diagnostic

### **`wamp.conf`** - Configuration WAMP
**Rôle** : Configuration du serveur local
**Contenu** : Paramètres Apache/MySQL

### **`public/uploads/`** - Fichiers uploadés
- Images d'articles
- Vignettes générées
- Médias uploadés

---

## 🎯 **FONCTIONNALITÉS PRINCIPALES**

### **✅ Implémentées**
1. **Authentification** : Connexion/déconnexion sécurisée
2. **Gestion des articles** : CRUD complet avec statuts
3. **Gestion des médias** : Upload et gestion de fichiers
4. **Interface admin** : Tableau de bord et navigation
5. **Système de thèmes** : Gestion des thèmes visuels
6. **Sécurité** : Sessions sécurisées, CSRF, permissions

### **🔄 En cours/Préparées**
1. **Gestion des jeux** : CRUD des jeux
2. **Gestion des catégories** : Organisation des articles
3. **Système de tags** : Étiquetage des articles
4. **Gestion des utilisateurs** : Multi-utilisateurs
5. **Paramètres du site** : Configuration dynamique

### **📋 À développer**
1. **Organisation CSS** : Séparation des styles
2. **API REST** : Interface programmatique
3. **Cache** : Optimisation des performances
4. **SEO** : Optimisation pour les moteurs de recherche
5. **Tests** : Tests automatisés

---

## 🚀 **RECOMMANDATIONS D'AMÉLIORATION**

### **Priorité Haute**
1. **Organiser les CSS** : Séparer les styles en fichiers modulaires
2. **Optimiser les requêtes** : Ajouter des index en base
3. **Sécuriser les uploads** : Validation renforcée des fichiers

### **Priorité Moyenne**
1. **Ajouter des tests** : Tests unitaires et d'intégration
2. **Optimiser les performances** : Cache et compression
3. **Améliorer l'UX** : Animations et transitions

### **Priorité Basse**
1. **API REST** : Interface pour applications tierces
2. **Multi-langues** : Support international
3. **Analytics** : Statistiques d'utilisation

---

## 📝 **NOTES DE DÉVELOPPEMENT**

### **Conventions de Nommage**
- **Contrôleurs** : PascalCase + "Controller"
- **Modèles** : PascalCase (singulier)
- **Vues** : snake_case
- **Méthodes** : camelCase
- **Variables** : snake_case

### **Sécurité**
- Sessions sécurisées avec httponly et secure
- Protection CSRF sur tous les formulaires
- Validation des uploads de fichiers
- Échappement des données utilisateur

### **Performance**
- Pagination sur les listes
- Optimisation des requêtes SQL
- Compression des images
- Cache des vignettes

---

---

## 📊 **STATISTIQUES DU PROJET**

### **Fichiers analysés** : 50+ fichiers
### **Lignes de code** : ~15,000 lignes
### **Classes CSS identifiées** : 150+ classes
### **Fonctionnalités implémentées** : 25+ fonctionnalités

### **Répartition par type** :
- **PHP** : 60% (Contrôleurs, Modèles, Core)
- **CSS** : 25% (Styles inline dans les vues)
- **JavaScript** : 10% (Éditeurs WYSIWYG)
- **HTML** : 5% (Templates et vues)

---

## 🎯 **ROADMAP DÉVELOPPEMENT**

### **Phase 1 : Optimisation CSS (Priorité Haute)** ✅ **TERMINÉE**
1. **✅ Extraction des styles** : Structure CSS modulaire créée
2. **✅ Organisation modulaire** : Dossiers base, components, layout, pages
3. **✅ Variables CSS** : 147 variables centralisées dans `variables.css`
4. **✅ Composants réutilisables** : Boutons, typographie, utilitaires

**Fichiers créés :**
- `public/assets/css/base/variables.css` - Variables CSS centralisées
- `public/assets/css/base/reset.css` - Reset CSS moderne
- `public/assets/css/base/typography.css` - Styles de typographie
- `public/assets/css/components/buttons.css` - Styles de boutons
- `public/assets/css/main.css` - Fichier principal avec imports
- `public/assets/css/test-organization.html` - Page de test

**Prochaines étapes :**
- Créer les fichiers layout (grid, header, footer)
- Créer les fichiers pages (home, admin, auth)
- Extraire les styles des vues PHP vers les fichiers CSS

### **Phase 2 : Amélioration des fonctionnalités**
1. **Gestion des jeux** : Finaliser le CRUD des jeux
2. **Système de tags** : Implémenter l'étiquetage
3. **Gestion des catégories** : Organisation des articles
4. **API REST** : Interface programmatique

### **Phase 3 : Performance et sécurité**
1. **Cache** : Mise en cache des requêtes
2. **Optimisation images** : Compression automatique
3. **Sécurité renforcée** : Validation des uploads
4. **Tests automatisés** : Tests unitaires et d'intégration

### **Phase 4 : Expérience utilisateur**
1. **Interface responsive** : Optimisation mobile
2. **Animations** : Transitions et micro-interactions
3. **Accessibilité** : Support WCAG
4. **SEO** : Optimisation pour les moteurs de recherche

---

## 🔍 **POINTS D'ATTENTION**

### **Sécurité**
- ✅ Sessions sécurisées
- ✅ Protection CSRF
- ⚠️ Validation des uploads à renforcer
- ⚠️ Échappement des données à vérifier

### **Performance**
- ✅ Pagination des listes
- ⚠️ CSS non optimisé (inline)
- ⚠️ Pas de cache des requêtes
- ⚠️ Images non compressées

### **Maintenabilité**
- ✅ Architecture MVC claire
- ✅ Code bien structuré
- ⚠️ CSS mélangé avec HTML
- ⚠️ Pas de tests automatisés

### **Évolutivité**
- ✅ Système modulaire
- ✅ Framework extensible
- ✅ Base de données normalisée
- ✅ API préparée

---

## 📝 **NOTES POUR LE DÉVELOPPEMENT FUTUR**

### **Conventions à respecter**
1. **Nommage** : PascalCase pour les classes, camelCase pour les méthodes
2. **Documentation** : Commenter chaque méthode publique
3. **Sécurité** : Toujours valider les entrées utilisateur
4. **Performance** : Optimiser les requêtes SQL

### **Bonnes pratiques**
1. **CSS** : Utiliser les variables CSS et les composants modulaires
2. **JavaScript** : Éviter les dépendances externes inutiles
3. **PHP** : Respecter les standards PSR
4. **Base de données** : Utiliser les transactions pour les opérations critiques

### **Outils recommandés**
1. **CSS** : PostCSS pour l'optimisation
2. **JavaScript** : ESLint pour la qualité du code
3. **PHP** : PHPStan pour l'analyse statique
4. **Tests** : PHPUnit pour les tests unitaires

---

*Documentation mise à jour le : 2024-01-XX*
*Version de l'application : 1.0*
*Dernière analyse : [Date actuelle]*

---

## 📞 **CONTACT & SUPPORT**

Pour toute question ou amélioration de cette documentation :
- **Développeur** : Assistant IA
- **Projet** : Belgium Vidéo Gaming CMS
- **Repository** : https://github.com/GuyVil1/CMSApp.git

---

**🎮 Cette documentation est votre référence pour tous les développements futurs sur l'application Belgium Vidéo Gaming !**

---

## 🔧 **CORRECTIONS ET AMÉLIORATIONS RÉCENTES**

### **1. Correction de l'offset CSS dans l'aperçu des articles**
- **Problème identifié** : La classe CSS `.content-module` avait des marges et paddings qui causaient un décalage entre l'éditeur et l'aperçu des articles
- **Solution appliquée** : Suppression des propriétés `margin: 20px 0` et `padding: 15px` de la classe `.content-module` dans `FullscreenEditor.js`
- **Résultat** : L'aperçu des articles correspond maintenant parfaitement à l'éditeur

### **2. Amélioration du module Image avec contrôles de padding indépendants**
- **Fonctionnalité ajoutée** : Contrôles de padding individuels pour les 4 directions (haut, droite, bas, gauche)
- **Implémentation** :
  - Extension de `ImageModule.js` avec propriété `padding` dans `imageData`
  - Interface utilisateur avec 4 champs numériques et boutons de presets
  - Méthode `getPaddingStyle()` pour générer le CSS dynamique
  - Styles CSS dédiés dans `public/assets/css/components/image-module.css`
- **Avantages** : Affinage précis du design des images dans l'éditeur

### **3. Correction du système de drag & drop (v5)**
- **Problème identifié** : Le drag & drop des modules depuis la sidebar vers les colonnes ne fonctionnait toujours pas après les corrections précédentes
- **Cause identifiée** : 
  - Duplication des listeners `dragstart` causant des conflits
  - Appel incorrect de `getData()` dans `dragover` (les données ne sont pas encore disponibles à ce moment-là)
- **Solution appliquée** : 
  - Suppression du listener `dragstart` dupliqué
  - Suppression de la logique `getData()` dans `dragover` qui causait des erreurs
  - Conservation de `e.preventDefault()` systématique dans `dragover` pour permettre le drop
- **Modifications apportées** :
  - `FullscreenEditor.js` : Nettoyage des listeners dupliqués, simplification de `dragover`
- **Résultat attendu** : Le drag & drop des modules depuis la sidebar vers les colonnes devrait maintenant fonctionner correctement

### **4. Historique des corrections drag & drop**
- **v1** : Tentative de correction du `dropEffect` et gestion des drops dans les colonnes vides
- **v2** : Correction du conflit `dropEffect`, refactorisation de `moveModuleToPosition()` et suppression du listener `drop` dupliqué
- **v3** : Correction du conflit entre listeners `dragend` pour rétablir le drag & drop depuis la sidebar
- **v4** : Correction du `preventDefault()` manquant dans `dragover` et amélioration de la gestion des drops sur le modal entier
- **v5** : Suppression des listeners `dragstart` dupliqués et de l'appel incorrect de `getData()` dans `dragover`

### **5. Correction de la bibliothèque de médias**
- **Problème identifié** : La bibliothèque de médias n'affichait qu'une petite partie des médias uploadés et les filtres ne fonctionnaient pas
- **Causes identifiées** :
  - Méthodes manquantes dans le modèle Media (`search()`, `findById()`)
  - Limite d'affichage trop basse (20 médias au lieu de 100+)
  - Filtres par jeu, catégorie et type non implémentés
- **Solutions appliquées** :
  - Ajout des méthodes manquantes dans `app/models/Media.php`
  - Implémentation de `searchWithFilters()` avec support des filtres avancés
  - Augmentation de la limite par défaut à 100 médias
  - Ajout du bouton "Charger plus" pour la pagination
  - Amélioration de l'API MediaLibraryAPI avec logs de débogage
- **Résultat** : Bibliothèque de médias 100% fonctionnelle avec filtres et affichage de 100+ médias

### **6. Système d'affichage des articles**
- **Fonctionnalité ajoutée** : Affichage public des articles créés avec l'éditeur modulaire
- **Implémentation** :
  - **HomeController** : Méthode `show(int $id)` pour afficher un article individuel par ID
  - **Vue article** : `app/views/articles/show.php` avec mise en page responsive
  - **CSS dédié** : `public/assets/css/components/article-display.css` pour le style des articles
  - **JavaScript** : `public/js/article-renderer.js` pour le rendu des modules de contenu
- **Fonctionnalités** :
  - Rendu automatique de tous les types de modules (texte, image, vidéo, galerie, tableau, citation, séparateur)
  - Affichage des métadonnées (auteur, catégorie, jeu, tags, date)
  - Articles liés et populaires en sidebar
  - Support des thèmes visuels
  - Gestion des erreurs et modules inconnus
  - Sécurité HTML (nettoyage des scripts dangereux)
- **Routes** : `/article/{id}` pour accéder aux articles (ex: `/article/1`, `/article/2`)
- **Navigation** : Tous les articles de la page d'accueil sont cliquables et redirigent vers leur page de lecture
- **Avantages** : Les articles créés avec l'éditeur modulaire sont maintenant visibles publiquement avec un rendu parfait

### **7. Système de gestion des genres de jeux (NOUVEAU)**
- **Fonctionnalité ajoutée** : Système complet de gestion des genres de jeux avec interface d'administration
- **Implémentation** :
  - **Base de données** : 
    - Table `genres` avec structure complète (id, name, description, color, timestamps)
    - Mise à jour de la table `games` avec colonne `genre_id` et clé étrangère
    - 15 genres prédéfinis (Action, RPG, Stratégie, Sport, etc.)
  - **Modèle Genre** : `app/models/Genre.php` avec méthodes CRUD complètes
  - **Contrôleur admin** : `app/controllers/admin/GenresController.php` pour la gestion
  - **Interface d'administration** : Vues complètes (index, create, edit) avec gestion des couleurs
  - **Intégration jeux** : Formulaire des jeux avec menu déroulant dynamique des genres
- **Fonctionnalités** :
  - CRUD complet des genres (création, lecture, mise à jour, suppression)
  - Gestion des couleurs personnalisées avec color picker
  - Validation et prévention de suppression des genres utilisés
  - Comptage des jeux par genre
  - Interface responsive et intuitive
  - Navigation intégrée dans le dashboard admin
- **Avantages** : 
  - Classification organisée des jeux par genre
  - Interface d'administration professionnelle
  - Flexibilité pour ajouter/modifier les genres
  - Cohérence avec le système existant
- **Fichiers créés** :
  - `database/create_genres_table.sql` : Création de la table et données initiales
  - `database/update_games_table.sql` : Mise à jour de la table games
  - `app/models/Genre.php` : Modèle de données
  - `app/controllers/admin/GenresController.php` : Contrôleur d'administration
  - `app/views/admin/genres/` : Vues d'administration (index, create, edit)
  - `genres.php` : Routeur d'administration
  - `test-genres.php` : Fichier de test et validation

---

*Dernière mise à jour : 2024-01-XX - Système de gestion des genres de jeux et améliorations complètes*
