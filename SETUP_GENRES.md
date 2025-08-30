# 🎯 Mise en place du système de genres

## 📋 Prérequis

1. **WAMP démarré** avec MySQL actif
2. **Base de données** `belgium_video_gaming` créée
3. **Accès administrateur** à la base de données

## 🗄️ Étape 1 : Créer la table des genres

Exécutez le script SQL suivant dans phpMyAdmin ou votre client MySQL :

```sql
-- Créer la table genres
source database/create_genres_table.sql
```

**Ou copiez-collez directement :**

```sql
-- Création de la table genres pour les jeux
CREATE TABLE IF NOT EXISTS `genres` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text,
  `color` varchar(7) DEFAULT '#007bff',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertion des genres de base
INSERT INTO `genres` (`name`, `description`, `color`) VALUES
('Action', 'Jeux d\'action avec des combats et des mouvements rapides', '#dc3545'),
('Aventure', 'Jeux d\'exploration et de découverte', '#28a745'),
('RPG', 'Jeux de rôle avec progression de personnage', '#6f42c1'),
('Stratégie', 'Jeux de réflexion et de planification', '#fd7e14'),
('Sport', 'Jeux sportifs et de compétition', '#20c997'),
('Course', 'Jeux de course et de conduite', '#17a2b8'),
('Plateforme', 'Jeux de plateforme et de saut', '#ffc107'),
('FPS', 'Jeux de tir à la première personne', '#e83e8c'),
('MMO', 'Jeux en ligne massivement multijoueur', '#6c757d'),
('Indépendant', 'Jeux développés par des studios indépendants', '#6f42c1'),
('Fighting', 'Jeux de combat et de bagarre', '#dc3545'),
('Puzzle', 'Jeux de réflexion et de logique', '#20c997'),
('Simulation', 'Jeux de simulation réaliste', '#17a2b8'),
('Horreur', 'Jeux d\'horreur et de survie', '#343a40'),
('Musical', 'Jeux de rythme et de musique', '#e83e8c');
```

## 🔄 Étape 2 : Mettre à jour la table des jeux

Exécutez le script SQL suivant :

```sql
-- Ajouter la colonne genre_id à la table games
source database/update_games_table.sql
```

**Ou copiez-collez directement :**

```sql
-- Ajout de la colonne genre_id à la table games
ALTER TABLE `games` ADD COLUMN `genre_id` int(11) NULL AFTER `description`;

-- Ajout de la clé étrangère
ALTER TABLE `games` ADD CONSTRAINT `fk_games_genre` FOREIGN KEY (`genre_id`) REFERENCES `genres`(`id`) ON DELETE SET NULL;

-- Mise à jour des jeux existants avec des genres par défaut
UPDATE `games` SET `genre_id` = (SELECT `id` FROM `genres` WHERE `name` = 'Action' LIMIT 1) WHERE `genre_id` IS NULL;
```

## 🧪 Étape 3 : Tester la configuration

1. **Test de connexion** : Accédez à `http://localhost/test-db-connection.php`
2. **Test du système** : Accédez à `http://localhost/test-genres.php`

## 🎮 Étape 4 : Utiliser le système

### Administration des genres
- **Liste des genres** : `http://localhost/genres.php`
- **Créer un genre** : `http://localhost/genres.php?action=create`
- **Modifier un genre** : `http://localhost/genres.php?action=edit&id=X`

### Administration des jeux
- **Liste des jeux** : `http://localhost/games.php`
- **Créer un jeu** : `http://localhost/games.php?action=create`
- **Modifier un jeu** : `http://localhost/games.php?action=edit&id=X`

## 🔧 Dépannage

### Erreur "Table 'genres' doesn't exist"
- Vérifiez que le script `create_genres_table.sql` a été exécuté
- Vérifiez que vous êtes dans la bonne base de données

### Erreur "Column 'genre_id' doesn't exist"
- Vérifiez que le script `update_games_table.sql` a été exécuté
- Vérifiez que la table `games` existe

### Erreur de connexion à la base
- Vérifiez que WAMP est démarré
- Vérifiez que le service MySQL est actif
- Vérifiez les identifiants dans `config/config.php`

### Erreur "Failed opening required 'core/Router.php'"
- ✅ **Résolu** : Le fichier a été corrigé

## 📁 Fichiers créés

- `app/models/Genre.php` - Modèle de données
- `app/controllers/admin/GenresController.php` - Contrôleur d'administration
- `app/views/admin/genres/` - Vues d'administration
- `genres.php` - Routeur d'administration
- `database/create_genres_table.sql` - Création de la table
- `database/update_games_table.sql` - Mise à jour de la table games
- `test-genres.php` - Fichier de test
- `test-db-connection.php` - Test de connexion

## 🎉 Résultat attendu

Après ces étapes, vous devriez avoir :
- ✅ Une table `genres` avec 15 genres prédéfinis
- ✅ Une table `games` avec une colonne `genre_id`
- ✅ Une interface d'administration complète pour les genres
- ✅ Des formulaires de jeux avec sélection de genre
- ✅ Un affichage des jeux avec couleurs de genre

## 🚀 Prochaines étapes

1. **Créer des genres personnalisés** via l'interface d'administration
2. **Assigner des genres** aux jeux existants
3. **Tester la création** de nouveaux jeux avec genres
4. **Personnaliser les couleurs** des genres selon vos préférences
