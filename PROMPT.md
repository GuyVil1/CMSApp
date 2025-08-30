# 🎯 MISSION
Recréer from scratch un site d'actualité jeux vidéo belge avec **CMS intégré** en **PHP 8 + MySQL** (WAMP en local, OVH en prod). Pas de framework (ni Laravel ni Symfony). Architecture MVC légère, sécurité prioritaire (PDO, CSRF, XSS, uploads). Le site public est moderne et responsive ; le back‑office offre un CRUD articles + médias + jeux + utilisateurs (rôles). Tout doit fonctionner en local avec WampServer 6.

# ⚙️ ENVIRONNEMENT
- Local: WampServer 6 (Apache + PHP 8.x + MySQL), hôte `localhost`, DB à créer: `belgium_video_gaming`, user `root`, password vide.
- Prod: OVH mutualisé (PHP 8.x). Prévoir `.env` (ou `config.php`) non versionné.
- Pas de dépendances payantes. CDN gratuits autorisés (Google Fonts, TinyMCE CDN, Lucide icônes, etc.).
- Frontend en HTML5/CSS3/JS vanilla (pas de bundler). Design mobile‑first, Grid/Flex, variables CSS.

# ✅ CRITÈRES D'ACCEPTATION (haut niveau)
1) Auth robuste (register/login/logout) avec `password_hash`, `password_verify`, sessions sécurisées (httponly/secure/SameSite).
2) Rôles: `admin`, `editor`, `author`, `member` (lecture). Contrôles d'accès stricts.
3) CMS: CRUD Articles, Médias (upload images + vignettes), Jeux (métadonnées), Catégories, Tags, Utilisateurs, Hardware (plateformes).
4) Site public: Accueil (À la une + dernières news), liste articles, détail, recherche, catégories/tags, trailers (section visuelle), pages jeux.
5) **Éditeur d'article modulaire** : Système d'édition avancé avec modules (texte, image, vidéo, galerie, etc.), drag & drop, intégration médias.
6) Sécurité: CSRF tokens sur actions POST, PDO préparé partout, XSS protégé, validation serveurs, upload MIME réel + taille, génération noms sûrs.
7) SEO/Perf: slugs uniques, meta dynamiques, sitemap.xml, robots.txt, images optimisées, pagination SQL, OPcache‑ready, cache simple page d'accueil.
8) Accessibilité: alt text, ARIA de base, contrastes OK, navigation clavier.
9) Documentation: README clair + `.env.example` + `database/schema.sql` + `database/seeds.sql`.
10) Zéro question bloquante: prends des décisions raisonnables, documente-les dans le README (section "Assumptions").

# 🗂 STRUCTURE PROJET (exigée)
belgium-video-gaming/
├── public/
│   ├── index.php                  # Front controller (routeur minimal)
│   ├── .htaccess                  # Rewrite vers index.php
│   ├── assets/
│   │   ├── css/main.css
│   │   ├── js/main.js
│   │   └── images/
│   ├── uploads/                   # (protégé via deny scripts)
│   ├── sitemap.xml                # généré
│   └── robots.txt
├── app/
│   ├── controllers/               # HomeController, ArticleController, GameController, Admin/*
│   ├── models/                    # User, Article, Media, Game, Category, Tag, Setting, ActivityLog, Hardware
│   ├── views/
│   │   ├── layout/                # header.php, footer.php, admin_layout.php
│   │   ├── public/                # home.php, article_list.php, article_show.php, search.php, game_list.php, game_show.php
│   │   └── admin/                 # dashboard.php, articles_*.php, media_*.php, games_*.php, users_*.php, categories_*.php, tags_*.php, hardware_*.php, settings.php
│   └── helpers/                   # db.php (PDO), auth.php, csrf.php, validation.php, slugify.php, upload.php, sanitizer.php
├── config/
│   ├── config.php                 # lit .env si présent
│   └── .env.example
├── core/
│   ├── Router.php                 # mini routeur GET/POST
│   ├── Controller.php
│   ├── Database.php
│   └── Auth.php
├── database/
│   ├── schema.sql
│   └── seeds.sql
└── docs/
    └── README.md

# 🎨 ÉDITEUR MODULAIRE AVANCÉ (NOTRE FIERTÉ ! 🚀)
## Architecture
- **Localisation** : `public/js/editor/`
- **Modules disponibles** : Texte, Image, Vidéo, Séparateur, Titre, Citation, Bouton, Tableau, Galerie, Liste
- **Fonctionnalités** : Drag & drop, sections multi-colonnes (1, 2, 3 colonnes), sauvegarde/chargement du contenu
- **Intégration médias** : Sélecteur de médias intégré dans ImageModule et GalleryModule
- **API médias** : MediaLibraryAPI pour l'intégration avec la bibliothèque de médias
- **Rendu HTML** : Chaque module génère du HTML avec des classes `content-module-*`

## Modules principaux
- **FullscreenEditor** : Classe principale de l'éditeur
- **BaseModule** : Classe de base pour tous les modules
- **ModuleFactory** : Factory pour créer les modules
- **StyleManager** : Gestion des styles CSS
- **MediaLibraryAPI** : API pour l'intégration médias

## Fonctionnalités avancées
- **Drag & Drop** : Glisser-déposer des modules vers les sections/colonnes
- **Sélection multiple** : Sélection de plusieurs images pour les galeries
- **Prévisualisation temps réel** : Affichage immédiat des modifications
- **Sauvegarde automatique** : Sauvegarde du contenu en JSON
- **Chargement intelligent** : Rechargement du contenu sauvegardé

# 🗃 BASE DE DONNÉES (schema.sql — créer la DB)
-- Users & roles
CREATE TABLE roles (
  id TINYINT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(20) UNIQUE NOT NULL
);
INSERT INTO roles (name) VALUES ('admin'),('editor'),('author'),('member');

CREATE TABLE users (
  id INT PRIMARY KEY AUTO_INCREMENT,
  login VARCHAR(60) UNIQUE NOT NULL,
  email VARCHAR(120) UNIQUE NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  role_id TINYINT NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NULL,
  last_login DATETIME NULL,
  FOREIGN KEY (role_id) REFERENCES roles(id)
);

-- Content
CREATE TABLE categories (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(80) NOT NULL,
  slug VARCHAR(120) UNIQUE NOT NULL,
  description TEXT NULL,
  color VARCHAR(7) DEFAULT '#000000',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE tags (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(80) NOT NULL,
  slug VARCHAR(120) UNIQUE NOT NULL
);

CREATE TABLE hardware (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(100) NOT NULL,
  slug VARCHAR(120) UNIQUE NOT NULL,
  description TEXT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE games (
  id INT PRIMARY KEY AUTO_INCREMENT,
  title VARCHAR(160) NOT NULL,
  slug VARCHAR(200) UNIQUE NOT NULL,
  description TEXT NULL,
  hardware_id INT NULL,
  genre VARCHAR(100) NULL,
  cover_image VARCHAR(255) NULL,
  release_date DATE NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (hardware_id) REFERENCES hardware(id)
);

CREATE TABLE media (
  id INT PRIMARY KEY AUTO_INCREMENT,
  filename VARCHAR(255) NOT NULL,
  original_name VARCHAR(255) NOT NULL,
  mime_type VARCHAR(100) NOT NULL,
  size INT NOT NULL,
  uploaded_by INT NOT NULL,
  game_id INT NULL,
  media_type ENUM('cover', 'screenshot', 'trailer', 'news', 'event', 'other') DEFAULT 'other',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (uploaded_by) REFERENCES users(id),
  FOREIGN KEY (game_id) REFERENCES games(id)
);

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
  featured_position TINYINT NULL,
  published_at DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NULL,
  FOREIGN KEY (cover_image_id) REFERENCES media(id),
  FOREIGN KEY (author_id) REFERENCES users(id),
  FOREIGN KEY (category_id) REFERENCES categories(id),
  FOREIGN KEY (game_id) REFERENCES games(id),
  INDEX (status), INDEX (author_id), INDEX (category_id), INDEX (game_id), INDEX (featured_position)
);

CREATE TABLE article_tag (
  article_id INT NOT NULL,
  tag_id INT NOT NULL,
  PRIMARY KEY(article_id, tag_id),
  FOREIGN KEY (article_id) REFERENCES articles(id),
  FOREIGN KEY (tag_id) REFERENCES tags(id)
);

-- Settings & activity
CREATE TABLE settings (
  id INT PRIMARY KEY AUTO_INCREMENT,
  `key` VARCHAR(100) UNIQUE NOT NULL,
  `value` TEXT NULL,
  description VARCHAR(255) NULL
);

CREATE TABLE activity_logs (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  user_id INT NULL,
  action VARCHAR(80) NOT NULL,
  details TEXT NULL,
  ip_address VARCHAR(45) NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Indices utiles
CREATE INDEX idx_articles_slug ON articles(slug);
CREATE INDEX idx_games_slug ON games(slug);
CREATE INDEX idx_hardware_slug ON hardware(slug);

# 🌱 DONNÉES DE DÉMO (seeds.sql)
-- Créer un admin par défaut (mot de passe: Admin!234)
INSERT INTO users (login,email,password_hash,role_id)
VALUES ('admin','admin@example.com', '{A REMPLACER PAR HASH BCRYPT}', 1);

INSERT INTO settings (`key`,`value`,`description`)
VALUES ('site_name','Jeux Vidéo Belgium','Nom du site'),
       ('site_tagline','Actus, tests et trailers','Baseline');

-- Catégories/tags/games d'exemple
INSERT INTO categories (name,slug,color) VALUES ('Actualités','actualites','#ff0000'),('Tests','tests','#00ff00'),('Guides','guides','#0000ff');
INSERT INTO tags (name,slug) VALUES ('Nintendo','nintendo'),('PlayStation','playstation'),('PC','pc');
INSERT INTO hardware (name,slug) VALUES ('Nintendo Switch','nintendo-switch'),('PlayStation 5','playstation-5'),('PC','pc');
INSERT INTO games (title,slug,hardware_id,genre) VALUES
('Metroid Prime 4','metroid-prime-4',1,'Action-Adventure');

# 🔐 SÉCURITÉ (obligatoire)
- Sessions: `session_regenerate_id(true)` au login, cookies `httponly`, `secure` (en HTTPS), `SameSite=Lax`.
- Auth: `password_hash(PASSWORD_DEFAULT)` et `password_verify`.
- CSRF: token par formulaire POST (génération + vérif + rotation partielle).
- SQL: **PDO + requêtes préparées** partout, jamais de variables interpolées.
- XSS: `htmlspecialchars` à l'affichage. Si WYSIWYG, passer par un **sanitizer** (ex: HTML Purifier) avec whitelist stricte (p balises basiques, ul/ol/li, a[href rel noopener], strong/em, figure/img avec classes limitées).
- Upload: vérifier **MIME réel** via `finfo`, extensions autorisées (jpg/jpeg/png/webp/gif), taille max (ex 4 Mo), renommage aléatoire (hash + timestamp), génération vignettes (GD) en 3 tailles (thumb 320px, medium 800px, full original). Aucune exécution dans `/uploads` (deny scripts via .htaccess).
- Autorisations: middleware simple qui bloque l'accès si rôle insuffisant OU si l'utilisateur n'est pas owner de la ressource quand c'est requis.

# 🖥️ PAGES PUBLIQUES (UX)
- Accueil:
  - **À la une**: 1 article principal (2/3 width) badge "À la une" + overlay.
  - **Dernières news**: liste paginée avec onglets (1–10, 11–20, 21–30) côté client (JS) s'appuyant sur pagination serveur.
  - **Trailers**: colonne droite avec cartes vidéo (thumbnail + bouton "play" overlay).
- Liste articles: filtres (catégorie, tag, plateforme), recherche (titre/contenu), pagination.
- Détail article: titre, date, auteur, catégorie, tags, image de couverture, contenu, articles liés (même catégorie ou mêmes tags).
- Jeux: index (recherche par plateforme/genre), page jeu (métadonnées + articles liés).
- Navigation: header moderne (logo, nom du site, lien connexion/back‑office), footer 3 colonnes (À propos, Navigation, Newsletter).
- Performance: lazy‑loading images, `<picture>` avec `srcset` pour tailles.

# 🛠️ BACK‑OFFICE (CMS)
- Dashboard: cartes stats (nb articles total / publiés / brouillons, derniers uploads, dernières actions).
- Articles: liste avec filtres statut/auteur/date; créer/éditer: titre, slug auto (modifiable), excerpt, contenu (éditeur modulaire), cover (picker depuis médias), catégorie, tags, jeu lié, boutons Enregistrer/Publi/Archiver.
- Médias: uploader, lister, supprimer, récupérer URL/ID; générer vignettes; organisation par jeux.
- Jeux: CRUD avec cover, plateforme, genre, date de sortie; lien articles associés.
- Hardware: CRUD des plateformes (Nintendo Switch, PlayStation 5, PC, etc.).
- Utilisateurs: CRUD (admin), reset mot de passe, changement rôle.
- Catégories/Tags: CRUD.
- Paramètres: site_name, tagline, meta par défaut.
- Logs d'activité: dernières actions (publication, suppression, login…).

## 💡 INSPIRATIONS DESIGN

- **IGN** pour la structure des articles
- **Polygon** pour le style éditorial
- **Kotaku** pour l'approche gaming
- **Eurogamer** pour l'aspect européen
- 🔴 Le dossier **Visuels/visual_target.png** contient la maquette visuelle de la page d'accueil. C'est le **cadrage graphique de référence** (mise en page, couleurs, proportions). Le design doit la respecter scrupuleusement.
- 🔴 Le fichier **app.tsx** est un export issu de Figma. Il fournit la structure exacte du layout de la page d'accueil (sous forme de code React). Il ne doit pas être utilisé comme code React, mais comme **guide strict** pour reproduire le visuel dans notre CMS (HTML5 + CSS3 vanilla).
- 🔴 Toute proposition de design doit donc se conformer à ces deux fichiers (et non être inventée).

# 🎨 DESIGN
- Variables CSS (palette):
- Thème clair/sombre optionnel (classe `theme-dark` sur `<html>`).
- Grilles responsives, cartes avec ombres douces, badges, toasts (JS simple).
- Icônes: Lucide (SVG inline ou CDN).
- Typo: Google Fonts (ex: Inter + Bangers pour titres gaming si souhaité).

# 🔎 ROUTAGE (exemple)
GET /
GET /articles
GET /article/{slug}
GET /search?q=
GET /category/{slug}
GET /tag/{slug}
GET /games
GET /game/{slug}

-- Admin (préfixe /admin)
GET /admin
GET|POST /admin/login
POST /admin/logout
CRUD: /admin/articles, /admin/media, /admin/games, /admin/hardware, /admin/users, /admin/categories, /admin/tags, /admin/settings

# 🧪 TESTS MANUELS (acceptation)
- Créer admin (seed), login, créer article brouillon + upload image, publier → visible sur / et /articles.
- Vérifier CSRF (soumission sans token → rejet).
- Vérifier XSS (injection script → échappée/sanitized).
- Upload image invalide (exe/pdf) → refusé.
- Rôles: author ne peut pas publier si politique = éditeur only (configurable), editor/admin oui.
- Sitemap généré et accessible, robots.txt présent.
- **Éditeur modulaire** : Test du drag & drop, intégration médias, sauvegarde/chargement du contenu.

# 📄 LIVRABLES OBLIGATOIRES
- README (installation locale WAMP, configuration `.env`, import `schema.sql` + `seeds.sql`, comptes de test, consignes OVH).
- `.env.example` (DB_HOST, DB_NAME, DB_USER, DB_PASS, BASE_URL, ENV=local|prod).
- `database/schema.sql` + `database/seeds.sql`.
- Code commenté là où nécessaire (helpers sécurité, upload, sanitizer).
- Script PHP de génération `sitemap.xml`.

# 🚦 PLAN D'EXÉCUTION (Cursor, étape par étape)
1) **Init repo & structure** (arborescence ci‑dessus, fichiers vides essentiels).
2) **DB**: écrire `schema.sql`, `seeds.sql` (inclure hash bcrypt réel pour Admin!234).
3) **Config/Database**: `.env.example`, `config.php`, `Database.php`, `helpers/db.php`.
4) **Auth**: `Auth.php`, helpers `auth.php`, sessions sécurisées, pages login/register/logout, middleware rôle.
5) **Router minimal** + `Controller.php` + layout public/admin.
6) **Modèles** (User, Article, Media, Game, Category, Tag, Setting, ActivityLog, Hardware) + méthodes CRUD (PDO préparé).
7) **CMS Articles** (list/create/edit/update/delete/toggle) + CSRF + slugs + validation.
8) **Uploads** (helpers upload + GD vignettes + vue choix cover).
9) **CMS Jeux/Hardware/Cats/Tags/Users/Settings** (CRUD).
10) **Éditeur modulaire** (système d'édition avancé avec modules, drag & drop, intégration médias).
11) **Frontend public** (Accueil, listes, détail, recherche, jeux) + pagination + SEO meta.
12) **Sitemap/robots** + `search.php` performant (INDEX SQL + LIKE/FTS selon MySQL).
13) **Polish sécurité** (sanitizer si TinyMCE), toasts, 404/500, logs activité.
14) **README final** (assumptions + guide OVH: .htaccess, PHP version, OPcache).

# 📌 RÈGLES DE CODAGE
- PHP 8 strict_types quand pertinent, PSR‑12, namespaces simples si utiles.
- Aucune requête SQL non préparée. `htmlspecialchars` par défaut à l'affichage.
- Aucune dépendance Composer obligatoire (optionnel pour HTML Purifier si disponible, sinon sanitizer maison restrictif).
- Commits atomiques par étape du plan avec messages clairs.

# 📝 NOTES
- Si `app.tsx` (Figma export) est fourni: l'utiliser comme **référence visuelle** pour l'accueil, reproduite en HTML/CSS vanilla.
- Privilégier GD (Imagick parfois indispo sur OVH).
- Prendre des décisions sans bloquer ; documenter les choix dans README/Assumptions.
- **Éditeur modulaire** : Système d'édition avancé développé from scratch, notre fierté technique !


Important, créer l'application à la racine de ce dossier, la page d'acceuil sera accessible en indiquant simplement localhost dans le navigateur