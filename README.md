# 🎮 **GameNews Belgium**

> **Site d'actualités jeux vidéo avec identité belge forte**

[![PHP Version](https://img.shields.io/badge/PHP-8.0+-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)
[![Status](https://img.shields.io/badge/Status-Production%20Ready-brightgreen.svg)]()

## 📋 **Description**

**GameNews Belgium** est un site d'actualités spécialisé dans les jeux vidéo, conçu spécifiquement pour le marché belge. L'application propose une plateforme moderne et responsive pour partager les dernières nouvelles du gaming, des tests de jeux, et des analyses de l'industrie.

## ✨ **Fonctionnalités Principales**

### **🎨 Interface Unifiée**
- **Template public cohérent** : Header, bannières thématiques et footer identiques sur toutes les pages
- **Design épuré** : Articles sans décoration inutile, texte en largeur complète
- **Thèmes dynamiques** : Bannières qui changent selon le contexte (gun, folk, Halloween, etc.)
- **Responsive parfait** : Adaptation sur tous les appareils

### **📱 Pages d'Articles**
- **Contenu optimisé** : Texte sans cadres, largeur complète, espacement équilibré
- **Métadonnées complètes** : Informations structurées et accessibles
- **Galerie intégrée** : Images avec système de lightbox fonctionnel
- **Navigation intuitive** : Accès rapide aux différentes sections

### **🖼️ Système de Galerie**
- **Grid responsive** : Disposition automatique des images
- **Lightbox avancé** : Visualisation en plein écran avec navigation clavier
- **Overlay interactif** : Icône de recherche au survol
- **Gestion d'erreurs** : Robustesse et stabilité

### **🔧 Administration Complète**
- **Interface d'administration** : Gestion complète du contenu
- **CRUD complet** : Articles, catégories, jeux, utilisateurs, médias
- **Système d'authentification** : Sécurisation des accès
- **Gestion des thèmes** : Personnalisation des bannières

## 🏗️ **Architecture Technique**

### **Backend**
- **PHP 8+** : Langage principal, orienté objet
- **MySQL** : Base de données relationnelle
- **Architecture MVC** : Modèle-Vue-Contrôleur personnalisé
- **Système de routing** : Gestion intelligente des URLs

### **Frontend**
- **HTML5 sémantique** : Structure claire et accessible
- **CSS3 moderne** : Flexbox, Grid, variables CSS
- **JavaScript ES6+** : Classes, modules et async/await
- **Responsive design** : Mobile-first approach

### **Fonctionnalités Avancées**
- **Éditeur WYSIWYG** : Création de contenu riche
- **Gestion des thèmes** : Bannières dynamiques et personnalisables
- **Système de cache** : Optimisation des performances
- **Upload sécurisé** : Validation et traitement des fichiers

## 🚀 **Installation Rapide**

### **Prérequis**
- PHP 8.0 ou supérieur
- MySQL 5.7 ou supérieur
- Serveur web (Apache/Nginx)
- Composer (optionnel)

### **Installation**

1. **Cloner le dépôt**
   ```bash
   git clone https://github.com/GuyVil1/CMSApp.git
   cd CMSApp
   ```

2. **Configurer la base de données**
   ```bash
   # Créer la base de données
   mysql -u root -p
   CREATE DATABASE belgium_video_gaming;
   
   # Importer le schéma
   mysql -u root -p belgium_video_gaming < database/schema.sql
   ```

3. **Configurer l'application**
   ```bash
   # Copier le fichier de configuration
   cp config/env.example config/.env
   
   # Modifier les paramètres de base de données
   nano config/.env
   ```

4. **Démarrer l'application**
   ```bash
   # Avec PHP intégré
   php -S localhost:8000 -t public
   
   # Ou avec votre serveur web
   # Pointer le DocumentRoot vers le dossier /public
   ```

## 📁 **Structure du Projet**

```
GameNews-Belgium/
├── app/                    # Application principale (MVC)
│   ├── controllers/        # Contrôleurs
│   ├── models/            # Modèles de données
│   ├── views/             # Vues et templates
│   └── helpers/           # Fonctions utilitaires
├── core/                  # Classes de base du framework
├── config/                # Configuration et paramètres
├── database/              # Schéma et migrations
├── public/                # Point d'entrée public
│   ├── assets/            # CSS, JS, images
│   ├── uploads/           # Fichiers uploadés
│   └── index.php          # Front controller
├── themes/                # Thèmes et bannières
└── docs/                  # Documentation
```

## 🎯 **Utilisation**

### **Page d'Accueil**
- **Articles en avant** : Mise en valeur des contenus phares
- **Dernières news** : Flux des actualités récentes
- **Trailers** : Section dédiée aux bandes-annonces

### **Navigation**
- **Menu principal** : Accès aux différentes sections
- **Breadcrumbs** : Navigation contextuelle
- **Recherche** : Trouver rapidement le contenu recherché

### **Administration**
- **Dashboard** : Vue d'ensemble et statistiques
- **Gestion du contenu** : Articles, catégories, médias
- **Utilisateurs** : Gestion des comptes et rôles

## 🔧 **Configuration**

### **Variables d'Environnement**
```env
DB_HOST=localhost
DB_NAME=belgium_video_gaming
DB_USER=root
DB_PASS=
BASE_URL=http://localhost
ENV=local
```

### **Thèmes Disponibles**
- **defaut** : Thème par défaut
- **folk** : Thème folklorique
- **gun** : Thème militaire
- **Halloween** : Thème festif
- **Wave** : Thème ondulé
- **Player** : Thème joueur

## 📊 **Base de Données**

### **Tables Principales**
- **`users`** : Utilisateurs avec rôles
- **`articles`** : Contenu principal avec métadonnées
- **`categories`** : Organisation thématique
- **`games`** : Jeux vidéo avec genres et hardware
- **`media`** : Images, vidéos et fichiers
- **`themes`** : Configuration des thèmes

### **Relations**
- **Articles ↔ Catégories** : Classification thématique
- **Articles ↔ Jeux** : Association contenu-jeu
- **Utilisateurs ↔ Rôles** : Permissions et accès
- **Médias ↔ Contenu** : Fichiers associés

## 🧪 **Tests**

### **Fichiers de Test**
- **Tests unitaires** : Validation des fonctionnalités
- **Tests d'intégration** : Vérification des relations
- **Tests de performance** : Mesure des temps de réponse

### **Exécution des Tests**
```bash
# Tests de base
php test-*.php

# Tests spécifiques
php test-gallery-lightbox.php
php test-texte-largeur-complete.php
```

## 📈 **Performance**

### **Optimisations Implémentées**
- **CSS modulaire** : Chargement par composants
- **Images responsives** : Adaptation automatique des tailles
- **Cache intelligent** : Réduction des requêtes serveur
- **Minification** : Réduction de la taille des fichiers

### **Métriques Cibles**
- **Temps de chargement** : < 2 secondes
- **Score mobile** : > 90/100
- **Score desktop** : > 95/100
- **Uptime** : > 99.9%

## 🔒 **Sécurité**

### **Mesures Implémentées**
- **Authentification sécurisée** : Sessions PHP avec régénération d'ID
- **Validation des données** : Protection contre les injections
- **Upload sécurisé** : Validation des types et tailles
- **Gestion des rôles** : Contrôle d'accès granulaire

### **Bonnes Pratiques**
- **HTTPS obligatoire** en production
- **Validation côté serveur** pour tous les inputs
- **Échappement des sorties** pour la protection XSS
- **Logs de sécurité** pour le monitoring

## 🌟 **Points Forts**

1. **Architecture solide** : MVC bien structuré et maintenable
2. **Design unifié** : Cohérence visuelle sur toutes les pages
3. **Performance optimisée** : Chargement rapide et fluide
4. **Responsive parfait** : Adaptation sur tous les appareils
5. **Fonctionnalités riches** : Galerie, lightbox, thèmes dynamiques
6. **Code maintenable** : Structure claire et documentée
7. **Identité belge** : Authenticité et localisation
8. **Évolutivité** : Facilement extensible et modifiable

## 🔮 **Évolutions Futures**

### **Fonctionnalités Prévues**
- **Système de commentaires** : Interaction avec les lecteurs
- **Newsletter** : Abonnement aux actualités
- **Recherche avancée** : Filtres et recherche sémantique
- **API REST** : Interface pour applications tierces

### **Améliorations Techniques**
- **PWA** : Application web progressive
- **Cache avancé** : Optimisation des performances
- **CDN** : Distribution mondiale du contenu
- **Analytics** : Suivi des performances et de l'engagement

## 📚 **Documentation**

- **[Description du Site](description_du_site.md)** : Vue d'ensemble complète
- **[Architecture de l'App](Notre_app.md)** : Détails techniques
- **[Tâches et TODO](Todo.md)** : Planning et fonctionnalités
- **[Configuration](config/)** : Paramètres et variables

## 🤝 **Contribution**

### **Comment Contribuer**
1. **Fork** le projet
2. **Créer** une branche pour votre fonctionnalité
3. **Commit** vos changements
4. **Push** vers la branche
5. **Créer** une Pull Request

### **Standards de Code**
- **PSR-12** : Standards de codage PHP
- **Commentaires** : Documentation claire du code
- **Tests** : Validation des fonctionnalités
- **Documentation** : Mise à jour des docs

## 📄 **Licence**

Ce projet est sous licence **MIT**. Voir le fichier [LICENSE](LICENSE) pour plus de détails.

## 📞 **Contact**

- **Développeur** : Équipe GameNews Belgium
- **Email** : [contact@gamenews-belgium.be]
- **Site Web** : [https://gamenews-belgium.be]
- **GitHub** : [https://github.com/GuyVil1/CMSApp]

## 🙏 **Remerciements**

- **Communauté PHP** pour les bonnes pratiques
- **Développeurs open source** pour les outils et bibliothèques
- **Testeurs** pour le feedback et les suggestions
- **Utilisateurs** pour l'adoption et l'engagement

---

## 📊 **Statut du Projet**

**🟢 PRODUCTION READY** - Version 2.0 avec template unifié et lightbox galerie

**Dernière mise à jour :** Décembre 2024  
**Version :** 2.0.0  
**Statut :** ✅ Stable et fonctionnel

---

*🎮 GameNews Belgium - L'actualité jeux vidéo en Belgique 🇧🇪*
