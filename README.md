# Belgium Vidéo Gaming - CMS

Un système de gestion de contenu (CMS) moderne pour un site de gaming belge, développé en PHP avec une architecture MVC personnalisée.

## 🚀 Fonctionnalités

### Back-office Administrateur
- **Authentification sécurisée** avec système de sessions
- **Gestion des articles** avec éditeur WYSIWYG modulaire
- **Gestion des médias** avec upload d'images
- **Gestion des catégories, jeux et tags**
- **Système de mise en avant** des articles

### Éditeur WYSIWYG Modulaire
- **Interface plein écran** moderne et intuitive
- **Modules modulaires** : texte, image, vidéo, galerie, tableau, bouton, titre, citation, séparateur
- **Système de colonnes** flexible
- **Prévisualisation en temps réel**
- **Sauvegarde automatique**

### Front-office
- **Design responsive** moderne
- **Système de navigation** intuitif
- **Affichage des articles** avec mise en avant
- **Intégration des médias** optimisée

## 🛠️ Technologies Utilisées

- **Backend** : PHP 8.0+
- **Base de données** : MySQL/MariaDB
- **Frontend** : HTML5, CSS3, JavaScript ES6+
- **Serveur** : WAMP/XAMPP compatible
- **Architecture** : MVC personnalisée

## 📁 Structure du Projet

```
belgium-video-gaming/
├── app/
│   ├── controllers/          # Contrôleurs MVC
│   │   ├── admin/           # Contrôleurs d'administration
│   │   ├── AuthController.php
│   │   └── HomeController.php
│   ├── models/              # Modèles de données
│   │   ├── Article.php
│   │   ├── Category.php
│   │   ├── Game.php
│   │   ├── Media.php
│   │   ├── Setting.php
│   │   ├── Tag.php
│   │   └── User.php
│   ├── views/               # Vues/templates
│   │   ├── admin/          # Interface d'administration
│   │   ├── auth/           # Pages d'authentification
│   │   ├── home/           # Pages publiques
│   │   └── layout/         # Templates de base
│   └── helpers/            # Fonctions utilitaires
├── config/                 # Configuration
│   ├── config.php         # Configuration principale
│   └── env.example        # Exemple de variables d'environnement
├── core/                  # Cœur du framework
│   ├── Auth.php          # Gestion de l'authentification
│   ├── Controller.php    # Classe de base des contrôleurs
│   └── Database.php      # Gestion de la base de données
├── database/             # Scripts de base de données
│   ├── schema.sql        # Structure de la base
│   └── seeds.sql         # Données de test
├── public/               # Fichiers publics
│   ├── assets/           # Assets statiques (CSS, JS, images)
│   ├── js/               # JavaScript
│   │   └── editor/       # Éditeur WYSIWYG modulaire
│   └── uploads/          # Fichiers uploadés
├── docs/                 # Documentation
└── index.php            # Point d'entrée principal
```

## 🚀 Installation

### Prérequis
- PHP 8.0 ou supérieur
- MySQL 5.7 ou supérieur
- Serveur web (Apache/Nginx) ou WAMP/XAMPP
- Composer (optionnel)

### Étapes d'installation

1. **Cloner le repository**
   ```bash
   git clone https://github.com/votre-username/belgium-video-gaming.git
   cd belgium-video-gaming
   ```

2. **Configurer la base de données**
   - Créer une base de données MySQL
   - Importer le fichier `database/schema.sql`
   - Importer le fichier `database/seeds.sql` pour les données de test

3. **Configurer l'application**
   ```bash
   cp config/env.example config/config.php
   ```
   - Modifier `config/config.php` avec vos paramètres de base de données

4. **Configurer le serveur web**
   - Pointer le document root vers le dossier `public/`
   - Ou configurer un virtual host

5. **Permissions**
   ```bash
   chmod 755 public/uploads/
   chmod 644 config/config.php
   ```

### Comptes de test
- **Admin** : `admin@example.com` / `password123`

## 🎯 Utilisation

### Accès au back-office
- URL : `http://votre-domaine/admin`
- Identifiants : voir section "Comptes de test"

### Création d'articles
1. Se connecter au back-office
2. Aller dans "Articles" > "Créer un article"
3. Remplir les informations de base
4. Cliquer sur "Ouvrir l'éditeur plein écran"
5. Utiliser les modules pour créer le contenu
6. Sauvegarder l'article

### Gestion des médias
- Upload d'images via l'interface d'administration
- Gestion des images de couverture des articles
- Système de redimensionnement automatique

## 🔧 Configuration

### Variables d'environnement
Copier `config/env.example` vers `config/config.php` et modifier :

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'votre_base_de_donnees');
define('DB_USER', 'votre_utilisateur');
define('DB_PASS', 'votre_mot_de_passe');
define('SITE_URL', 'http://votre-domaine.com');
```

### Configuration de l'éditeur
L'éditeur WYSIWYG est configuré dans `public/js/editor/` :
- `FullscreenEditor.js` : Éditeur principal
- `modules/` : Modules disponibles (texte, image, etc.)
- `core/` : Composants de base

## 🐛 Dépannage

### Problèmes courants

**L'éditeur ne s'affiche pas**
- Vérifier que tous les fichiers JS sont chargés
- Vider le cache du navigateur
- Vérifier la console pour les erreurs JavaScript

**Erreurs de base de données**
- Vérifier les paramètres de connexion dans `config/config.php`
- S'assurer que la base de données existe et est accessible

**Problèmes d'upload**
- Vérifier les permissions du dossier `public/uploads/`
- Vérifier la configuration PHP pour les uploads

## 🤝 Contribution

1. Fork le projet
2. Créer une branche pour votre fonctionnalité (`git checkout -b feature/AmazingFeature`)
3. Commit vos changements (`git commit -m 'Add some AmazingFeature'`)
4. Push vers la branche (`git push origin feature/AmazingFeature`)
5. Ouvrir une Pull Request

## 📝 Licence

Ce projet est sous licence MIT. Voir le fichier `LICENSE` pour plus de détails.

## 👥 Auteurs

- **Votre Nom** - *Développement initial* - [VotreGitHub](https://github.com/votre-username)

## 🙏 Remerciements

- Inspiration du design moderne
- Communauté PHP pour les bonnes pratiques
- Contributeurs open source

---

**Belgium Vidéo Gaming** - Un CMS moderne pour la communauté gaming belge 🎮
