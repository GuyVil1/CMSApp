# 🎮 Belgium Vidéo Gaming - CMS

Site d'actualité jeux vidéo belge avec CMS intégré en PHP 8 + MySQL.

## 📋 Prérequis

- **WampServer 6** (Apache + PHP 8.x + MySQL)
- **PHP 8.0+** avec extensions : PDO, GD, mbstring
- **MySQL 5.7+** ou MariaDB 10.2+

## 🚀 Installation

### 1. Configuration de la base de données

1. Ouvrir phpMyAdmin (http://localhost/phpmyadmin)
2. Créer une nouvelle base de données : `belgium_video_gaming`
3. Importer le fichier `database/schema.sql`
4. Importer le fichier `database/seeds.sql`

### 2. Configuration de l'application

1. Copier `config/env.example` vers `config/.env`
2. Modifier les paramètres dans `.env` si nécessaire :
   ```env
   DB_HOST=localhost
   DB_NAME=belgium_video_gaming
   DB_USER=root
   DB_PASS=
   BASE_URL=http://localhost
   ```

### 3. Accès à l'application

- **Site public** : http://localhost
- **Back-office** : http://localhost/admin
- **Compte admin** : 
  - Login : `admin`
  - Mot de passe : `Admin!234`

## 🗂️ Structure du projet

```
www/
├── public/                 # Point d'entrée (localhost)
│   ├── index.php          # Front controller
│   ├── .htaccess          # Rewrite rules
│   ├── assets/            # CSS/JS/Images
│   └── uploads/           # Images sécurisées
├── app/
│   ├── controllers/       # Contrôleurs MVC
│   ├── models/            # Modèles de données
│   ├── views/             # Templates PHP
│   └── helpers/           # Utilitaires
├── config/                # Configuration
├── core/                  # Classes de base
├── database/              # Schema + seeds
└── docs/                  # Documentation
```

## 🔐 Sécurité

- **Sessions sécurisées** : httponly, secure, SameSite
- **Requêtes préparées** : PDO partout
- **Protection CSRF** : Tokens sur tous les formulaires
- **Upload sécurisé** : Validation MIME, taille, extensions
- **XSS protection** : htmlspecialchars par défaut

## 🎨 Design

Le design respecte scrupuleusement :
- **Thème belge** : Couleurs nationales (rouge, jaune, noir)
- **Layout responsive** : Mobile-first
- **Structure exacte** : Basée sur app.tsx (export Figma)

## 📱 Fonctionnalités

### Site public
- Page d'accueil avec articles en avant
- Section "Dernières news" avec pagination
- Section "Trailers" avec overlay play
- Recherche et filtres
- Pages articles et jeux

### Back-office CMS
- ✅ Authentification avec rôles
- ✅ CRUD articles avec éditeur WYSIWYG
- ✅ Gestion des médias (upload, vignettes, suppression)
- ⏳ CRUD jeux, utilisateurs, catégories, tags
- ⏳ Paramètres du site

## 🛠️ Développement

### Technologies utilisées
- **Backend** : PHP 8 + MySQL
- **Frontend** : HTML5/CSS3/JS vanilla
- **Architecture** : MVC léger
- **Sécurité** : PDO, CSRF, XSS protection

### Plan d'exécution
1. ✅ **Phase 1** : Structure de base + configuration
2. ✅ **Phase 2** : Authentification + système de rôles
3. 🔄 **Phase 3** : CMS articles + uploads + médias
4. ⏳ **Phase 4** : Site public + design
5. ⏳ **Phase 5** : Optimisations + SEO
6. ⏳ **Phase 6** : Tests + documentation finale

## 📄 Licence

Projet développé pour Belgium Vidéo Gaming.

---

**🇧🇪 Fièrement belge - Made in Belgium**
