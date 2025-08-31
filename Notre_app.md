# 🏗️ ARCHITECTURE COMPLÈTE DE L'APPLICATION CMS

## 📋 Vue d'ensemble

**Belgium Vidéo Gaming** est un CMS (Content Management System) développé en PHP 8 avec une architecture MVC personnalisée. L'application gère du contenu multimédia (jeux vidéo, articles, médias) avec un système d'authentification et d'administration complet.

**Technologies :** PHP 8, MySQL, HTML/CSS/JavaScript, Architecture MVC personnalisée
**Environnement :** WAMP (Windows + Apache + MySQL + PHP)
**Base de données :** `belgium_video_gaming`

---

## 🗂️ STRUCTURE DES RÉPERTOIRES

### 📁 **Racine (`/`)**
- **Point d'entrée principal** : `index.php` (redirection vers `/public`)
- **Configuration WAMP** : `wamp.conf`
- **Fichiers de routage** : `games.php`, `articles.php`, `categories.php`, etc.
- **Documentation** : `README.md`, `description_du_site.md`, `Todo.md`

### 📁 **`/core/` - Classes de base du framework**
- **`Controller.php`** : Classe abstraite de base pour tous les contrôleurs
- **`Database.php`** : Gestionnaire de base de données (PDO avec singleton)
- **`Auth.php`** : Système d'authentification et d'autorisation

### 📁 **`/app/` - Application principale (MVC)**
- **`/controllers/`** : Logique métier
- **`/models/`** : Accès aux données
- **`/views/`** : Interface utilisateur
- **`/helpers/`** : Fonctions utilitaires

### 📁 **`/public/` - Point d'entrée public**
- **`index.php`** : Front controller et routeur principal
- **`/assets/`** : Ressources statiques (CSS, JS, images)
- **`/uploads/`** : Fichiers uploadés par les utilisateurs

### 📁 **`/config/` - Configuration**
- **`config.php`** : Configuration principale de l'application
- **`theme.json`** : Configuration des thèmes
- **`.env`** : Variables d'environnement (exemple fourni)

### 📁 **`/database/` - Schéma et migrations**
- **`schema.sql`** : Structure complète de la base de données
- **`seeds.sql`** : Données initiales
- **Scripts de mise à jour** : `update_*.sql`

---

## 🔧 ARCHITECTURE TECHNIQUE

### **1. Système de routage**
```
URL → .htaccess → public/index.php → Contrôleur → Vue
```

**Fichiers clés :**
- **`.htaccess`** : Redirige toutes les requêtes vers `public/index.php`
- **`public/index.php`** : Front controller qui analyse l'URL et instancie le bon contrôleur

**Routes principales :**
- `/` → `HomeController::index()`
- `/admin` → `DashboardController::index()`
- `/games` → `GamesController::index()`
- `/genres` → `GenresController::index()`
- `/articles` → `ArticlesController::index()`
- `/auth/login` → `AuthController::login()`

### **2. Pattern MVC personnalisé**

#### **Modèles (`/app/models/`)**
- **`Game.php`** : Gestion des jeux vidéo avec relations vers genres et hardware
- **`Genre.php`** : Gestion des genres de jeux avec couleurs personnalisées
- **`Article.php`** : Gestion des articles avec système de statuts
- **`User.php`** : Gestion des utilisateurs et rôles
- **`Media.php`** : Gestion des fichiers multimédia
- **`Hardware.php`** : Gestion des plateformes de jeu
- **`Category.php`** : Gestion des catégories d'articles
- **`Tag.php`** : Gestion des tags
- **`Role.php`** : Gestion des rôles utilisateurs
- **`Setting.php`** : Gestion des paramètres du site

#### **Contrôleurs (`/app/controllers/`)**
- **`HomeController.php`** : Page d'accueil et affichage public
- **`AuthController.php`** : Authentification et gestion des sessions
- **`TestController.php`** : Contrôleur de test pour le routage

**Contrôleurs d'administration (`/app/controllers/admin/`) :**
- **`DashboardController.php`** : Tableau de bord administrateur
- **`GamesController.php`** : CRUD des jeux vidéo
- **`GenresController.php`** : CRUD des genres de jeux
- **`ArticlesController.php`** : CRUD des articles
- **`CategoriesController.php`** : CRUD des catégories
- **`TagsController.php`** : CRUD des tags
- **`UsersController.php`** : CRUD des utilisateurs
- **`MediaController.php`** : Gestion des médias
- **`HardwareController.php`** : CRUD du hardware
- **`ThemesController.php`** : Gestion des thèmes
- **`UploadController.php`** : Gestion des uploads

#### **Vues (`/app/views/`)**
- **`/admin/`** : Interface d'administration pour chaque module
- **`/home/`** : Pages publiques
- **`/auth/`** : Pages d'authentification
- **`/layout/`** : Templates d'erreur (404, 500, 403)

### **3. Système de base de données**

#### **Architecture des tables :**
```
users ←→ roles (1:N)
users ←→ articles (1:N)
users ←→ media (1:N)
categories ←→ articles (1:N)
tags ←→ articles (N:N via article_tags)
games ←→ genres (N:1)
games ←→ hardware (N:1)
games ←→ media (N:1)
games ←→ articles (1:N)
```

#### **Tables principales :**
- **`users`** : Utilisateurs avec rôles
- **`roles`** : Rôles (admin, editor, author, member)
- **`articles`** : Articles avec statuts et relations
- **`games`** : Jeux vidéo avec genres et hardware
- **`genres`** : Genres de jeux avec couleurs
- **`hardware`** : Plateformes de jeu
- **`media`** : Fichiers multimédia
- **`categories`** : Catégories d'articles
- **`tags`** : Tags pour les articles

---

## 🔄 FLUX DE DONNÉES

### **1. Requête HTTP**
```
Navigateur → .htaccess → public/index.php
```

### **2. Routage**
```
public/index.php analyse l'URL → Détermine le contrôleur et l'action
```

### **3. Exécution**
```
Contrôleur → Modèle → Base de données → Vue → HTML
```

### **4. Exemple concret (création d'un jeu)**
```
POST /games?action=store
→ GamesController::store()
→ Game::create()
→ Database::execute()
→ Redirection vers /games avec message de succès
```

---

## 🔐 SYSTÈME D'AUTHENTIFICATION

### **Classes impliquées :**
- **`Auth.php`** : Gestion des sessions, tokens CSRF, vérification des rôles
- **`User.php`** : Modèle utilisateur avec méthodes d'authentification
- **`Role.php`** : Gestion des permissions

### **Sécurité :**
- **Sessions PHP** avec régénération d'ID
- **Tokens CSRF** pour les formulaires
- **Hachage des mots de passe** avec `password_hash()`
- **Validation des rôles** pour l'accès administrateur

---

## 📱 INTERFACE UTILISATEUR

### **Frontend public :**
- **CSS principal** : `style.css`
- **Responsive design** avec media queries
- **Navigation** avec menu principal et sous-menus

### **Interface d'administration :**
- **CSS admin** : `admin.css` (styles spécifiques à l'admin)
- **Tableaux de données** avec pagination et filtres
- **Formulaires** avec validation côté client et serveur
- **Modales** pour les confirmations de suppression

### **JavaScript :**
- **Génération automatique de slugs**
- **Prévisualisation d'images**
- **Validation de formulaires**
- **Gestion des suppressions** avec confirmation

---

## 🗄️ GESTION DES MÉDIAS

### **Système d'upload :**
- **`UploadController.php`** : Gestion des uploads de fichiers
- **`Media.php`** : Modèle pour les fichiers
- **`/public/uploads/`** : Stockage des fichiers
- **Types supportés** : JPG, PNG, GIF, WEBP
- **Taille maximale** : 5MB par défaut

### **Gestion des images :**
- **Redimensionnement automatique** (si configuré)
- **Génération de thumbnails**
- **Association aux articles et jeux**

---

## 🎨 SYSTÈME DE THÈMES

### **Structure :**
- **`/themes/`** : Dossiers de thèmes
- **`theme.json`** : Configuration des thèmes
- **`ThemesController.php`** : Gestion des thèmes
- **Images de thème** : `left.png`, `right.png` par thème

### **Thèmes disponibles :**
- **`defaut/`** : Thème par défaut
- **`folk/`** : Thème folklorique
- **`Wave/`** : Thème ondulé

---

## 🧪 SYSTÈME DE TEST

### **Fichiers de test :**
- **`test-genres.php`** : Test du système de genres
- **`test-db-connection.php`** : Test de connexion à la base
- **`test-genres-direct.php`** : Test direct du contrôleur
- **`test-genre-creation.php`** : Test de création de genre

### **Utilisation :**
- **Tests unitaires** pour valider les fonctionnalités
- **Tests d'intégration** pour vérifier les relations
- **Tests de performance** pour la base de données

---

## 🔧 CONFIGURATION ET DÉPLOIEMENT

### **Variables d'environnement :**
```php
DB_HOST=localhost
DB_NAME=belgium_video_gaming
DB_USER=root
DB_PASS=
BASE_URL=http://localhost
ENV=local
```

### **Configuration de production :**
- **Désactiver l'affichage des erreurs**
- **Utiliser des secrets forts** pour les sessions
- **Configurer HTTPS**
- **Optimiser la base de données**

---

## 📊 RELATIONS ENTRE MODULES

### **Jeux vidéo :**
```
Game ←→ Genre (N:1)
Game ←→ Hardware (N:1)
Game ←→ Media (N:1)
Game ←→ Article (1:N)
```

### **Articles :**
```
Article ←→ User (N:1)
Article ←→ Category (N:1)
Article ←→ Game (N:1)
Article ←→ Tag (N:N)
Article ←→ Media (N:1)
```

### **Utilisateurs :**
```
User ←→ Role (N:1)
User ←→ Article (1:N)
User ←→ Media (1:N)
```

---

## 🚀 POINTS D'ENTRÉE PRINCIPAUX

### **URLs publiques :**
- **`/`** : Page d'accueil
- **`/article/{slug}`** : Affichage d'un article
- **`/games`** : Liste des jeux
- **`/categories`** : Liste des catégories

### **URLs d'administration :**
- **`/admin`** : Tableau de bord
- **`/games`** : Gestion des jeux
- **`/genres`** : Gestion des genres
- **`/articles`** : Gestion des articles
- **`/users`** : Gestion des utilisateurs

---

## 🔍 DÉBOGAGE ET MAINTENANCE

### **Logs :**
- **`error_log()`** pour le débogage
- **Affichage des erreurs** en mode local
- **Traçage des routes** dans `public/index.php`

### **Maintenance :**
- **Scripts SQL** pour les mises à jour
- **Fichiers de test** pour valider les fonctionnalités
- **Documentation** complète dans `description_du_site.md`

---

## 📈 ÉVOLUTIONS FUTURES

### **Fonctionnalités prévues :**
- **API REST** pour l'intégration externe
- **Système de cache** pour les performances
- **Gestion des commentaires** sur les articles
- **Système de newsletter**
- **Analytics** et statistiques d'usage

### **Améliorations techniques :**
- **Migration vers PHP 8.2+**
- **Optimisation des requêtes** base de données
- **Système de cache** Redis/Memcached
- **Tests automatisés** avec PHPUnit

---

## 🎯 POINTS CLÉS POUR LA REPRISE

### **1. Architecture MVC personnalisée**
- **Pas de framework externe** : tout est développé en interne
- **Pattern singleton** pour la base de données
- **Autoloader simple** dans `public/index.php`

### **2. Système de routage**
- **Front controller** dans `public/index.php`
- **Redirection .htaccess** vers le point d'entrée
- **Gestion des paramètres** via `$_GET['action']`

### **3. Base de données**
- **PDO avec requêtes préparées**
- **Relations complexes** entre les tables
- **Scripts de migration** dans `/database/`

### **4. Authentification**
- **Sessions PHP natives**
- **Système de rôles** avec permissions
- **Tokens CSRF** pour la sécurité

### **5. Gestion des médias**
- **Upload de fichiers** avec validation
- **Stockage organisé** dans `/public/uploads/`
- **Association aux contenus** (jeux, articles)

---

## 🔗 LIENS UTILES

- **Documentation complète** : `description_du_site.md`
- **Tâches à faire** : `Todo.md`
- **Configuration** : `config/config.php`
- **Schéma DB** : `database/schema.sql`
- **Tests** : `test-*.php`

---

*Ce document est destiné à faciliter la reprise du développement et la compréhension de l'architecture complète de l'application.*

