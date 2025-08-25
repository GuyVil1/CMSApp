# Application PHP - Système de Gestion de Contenu

## Description
Application PHP de gestion de contenu avec système d'administration, gestion d'articles, médias et utilisateurs.

## Structure du Projet
```
├── app/
│   ├── controllers/     # Contrôleurs de l'application
│   ├── models/         # Modèles de données
│   ├── views/          # Vues et templates
│   └── helpers/        # Fonctions utilitaires
├── config/             # Configuration de l'application
├── core/               # Classes de base (Auth, Controller, Database)
├── database/           # Scripts SQL (schéma et données)
├── public/             # Fichiers publics (CSS, JS, uploads)
└── docs/               # Documentation
```

## Fonctionnalités
- ✅ Système d'authentification
- ✅ Gestion d'articles avec éditeur WYSIWYG
- ✅ Gestion des médias et uploads
- ✅ Interface d'administration
- ✅ Système de catégories et tags
- ✅ Gestion des utilisateurs

## Installation

1. **Cloner le repository**
   ```bash
   git clone [URL_DU_REPO]
   cd [NOM_DU_PROJET]
   ```

2. **Configuration de la base de données**
   - Créer une base de données MySQL
   - Importer le fichier `database/schema.sql`
   - Copier `config/env.example` vers `config/config.php`
   - Modifier les paramètres de connexion dans `config/config.php`

3. **Configuration du serveur web**
   - Pointer le document root vers le dossier `public/`
   - S'assurer que PHP est installé (version 7.4+ recommandée)
   - Activer les extensions PHP : mysqli, gd, fileinfo

4. **Permissions**
   ```bash
   chmod 755 public/uploads/
   chmod 755 public/uploads/article/
   ```

## Utilisation

1. Accéder à l'application via votre navigateur
2. Se connecter à l'interface d'administration
3. Commencer à créer du contenu !

## Technologies Utilisées
- PHP 7.4+
- MySQL
- HTML/CSS/JavaScript
- Éditeur WYSIWYG personnalisé

## Licence
[À définir selon vos besoins]

## Support
Pour toute question ou problème, veuillez ouvrir une issue sur GitHub.
