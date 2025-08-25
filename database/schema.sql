-- =====================================================
-- SCHÉMA DE BASE DE DONNÉES - BELGIUM VIDÉO GAMING
-- =====================================================

-- Créer la base de données
CREATE DATABASE IF NOT EXISTS belgium_video_gaming
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE belgium_video_gaming;

-- =====================================================
-- TABLES UTILISATEURS ET RÔLES
-- =====================================================

-- Table des rôles
CREATE TABLE roles (
  id TINYINT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(20) UNIQUE NOT NULL
);

-- Insérer les rôles par défaut
INSERT INTO roles (name) VALUES 
('admin'),
('editor'), 
('author'),
('member');

-- Table des utilisateurs
CREATE TABLE users (
  id INT PRIMARY KEY AUTO_INCREMENT,
  login VARCHAR(60) UNIQUE NOT NULL,
  email VARCHAR(120) UNIQUE NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  role_id TINYINT NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
  last_login DATETIME NULL,
  FOREIGN KEY (role_id) REFERENCES roles(id)
);

-- =====================================================
-- TABLES DE CONTENU
-- =====================================================

-- Table des catégories
CREATE TABLE categories (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(80) NOT NULL,
  slug VARCHAR(120) UNIQUE NOT NULL,
  description TEXT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Table des tags
CREATE TABLE tags (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(80) NOT NULL,
  slug VARCHAR(120) UNIQUE NOT NULL
);

-- Table des jeux
CREATE TABLE games (
  id INT PRIMARY KEY AUTO_INCREMENT,
  title VARCHAR(160) NOT NULL,
  slug VARCHAR(200) UNIQUE NOT NULL,
  description TEXT NULL,
  platform VARCHAR(100) NULL,
  genre VARCHAR(100) NULL,
  cover_image_id INT NULL,
  release_date DATE NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Table des médias
CREATE TABLE media (
  id INT PRIMARY KEY AUTO_INCREMENT,
  filename VARCHAR(255) NOT NULL,
  original_name VARCHAR(255) NOT NULL,
  mime_type VARCHAR(100) NOT NULL,
  size INT NOT NULL,
  uploaded_by INT NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (uploaded_by) REFERENCES users(id)
);

-- Table des articles
CREATE TABLE articles (
  id INT PRIMARY KEY AUTO_INCREMENT,
  title VARCHAR(200) NOT NULL,
  slug VARCHAR(220) UNIQUE NOT NULL,
  excerpt TEXT NULL,
  content MEDIUMTEXT NOT NULL,
  status ENUM('draft','published','archived') NOT NULL DEFAULT 'draft',
  cover_image_id INT NULL,
  author_id INT NOT NULL,
  category_id INT NULL,
  game_id INT NULL,
  published_at DATETIME NULL,
  featured_position TINYINT NULL COMMENT 'Position 1-6 pour mise en avant, NULL si pas en avant',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (cover_image_id) REFERENCES media(id),
  FOREIGN KEY (author_id) REFERENCES users(id),
  FOREIGN KEY (category_id) REFERENCES categories(id),
  FOREIGN KEY (game_id) REFERENCES games(id),
  INDEX (status), 
  INDEX (author_id), 
  INDEX (category_id), 
  INDEX (game_id)
);

-- Table de liaison articles-tags
CREATE TABLE article_tag (
  article_id INT NOT NULL,
  tag_id INT NOT NULL,
  PRIMARY KEY(article_id, tag_id),
  FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE,
  FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
);

-- =====================================================
-- TABLES DE CONFIGURATION ET ACTIVITÉ
-- =====================================================

-- Table des paramètres
CREATE TABLE settings (
  id INT PRIMARY KEY AUTO_INCREMENT,
  `key` VARCHAR(100) UNIQUE NOT NULL,
  `value` TEXT NULL,
  description VARCHAR(255) NULL
);

-- Table des logs d'activité
CREATE TABLE activity_logs (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  user_id INT NULL,
  action VARCHAR(80) NOT NULL,
  details TEXT NULL,
  ip_address VARCHAR(45) NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id)
);

-- =====================================================
-- INDEX UTILES
-- =====================================================

CREATE INDEX idx_articles_slug ON articles(slug);
CREATE INDEX idx_games_slug ON games(slug);
CREATE INDEX idx_categories_slug ON categories(slug);
CREATE INDEX idx_tags_slug ON tags(slug);
CREATE INDEX idx_articles_status_published ON articles(status, published_at);
CREATE INDEX idx_articles_featured_position ON articles(featured_position);
CREATE INDEX idx_articles_created_at ON articles(created_at);
