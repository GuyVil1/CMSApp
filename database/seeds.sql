-- =====================================================
-- DONNÉES DE DÉMONSTRATION - BELGIUM VIDÉO GAMING
-- =====================================================

USE belgium_video_gaming;

-- =====================================================
-- UTILISATEUR ADMIN PAR DÉFAUT
-- =====================================================
-- Mot de passe: Admin!234
INSERT INTO users (login, email, password_hash, role_id) VALUES 
('admin', 'admin@belgium-video-gaming.be', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1);

-- =====================================================
-- PARAMÈTRES DU SITE
-- =====================================================
INSERT INTO settings (`key`, `value`, `description`) VALUES 
('site_name', 'Belgium Vidéo Gaming', 'Nom du site'),
('site_tagline', 'Actus, tests et trailers', 'Baseline du site'),
('site_description', 'Votre source #1 pour l''actualité jeux vidéo en Belgique', 'Description SEO'),
('contact_email', 'contact@belgium-video-gaming.be', 'Email de contact'),
('contact_address', 'Bruxelles, Belgique', 'Adresse de contact');

-- =====================================================
-- CATÉGORIES PAR DÉFAUT
-- =====================================================
INSERT INTO categories (name, slug, description) VALUES 
('Actualités', 'actualites', 'Toute l''actualité du monde du jeu vidéo'),
('Tests', 'tests', 'Tests et reviews de jeux'),
('Guides', 'guides', 'Guides et tutoriels'),
('eSports', 'esports', 'Actualités eSports et compétitions'),
('Matériel', 'materiel', 'Tests de matériel gaming'),
('Rétro', 'retro', 'Jeux rétro et nostalgie');

-- =====================================================
-- TAGS PAR DÉFAUT
-- =====================================================
INSERT INTO tags (name, slug) VALUES 
('Nintendo', 'nintendo'),
('PlayStation', 'playstation'),
('Xbox', 'xbox'),
('PC', 'pc'),
('Mobile', 'mobile'),
('Indie', 'indie'),
('AAA', 'aaa'),
('RPG', 'rpg'),
('FPS', 'fps'),
('Action', 'action'),
('Aventure', 'aventure'),
('Stratégie', 'strategie'),
('Simulation', 'simulation'),
('Sport', 'sport'),
('Racing', 'racing'),
('Belgique', 'belgique'),
('Europe', 'europe');

-- =====================================================
-- JEUX DE DÉMONSTRATION
-- =====================================================
INSERT INTO games (title, slug, description, platform, genre, release_date) VALUES 
('Metroid Prime 4', 'metroid-prime-4', 'Le retour de Samus Aran dans une nouvelle aventure épique', 'Switch', 'Action-Aventure', '2025-12-31'),
('Cyberpunk 2078', 'cyberpunk-2078', 'Suite du célèbre RPG cyberpunk de CD Projekt RED', 'PC, PS6, Xbox Series X', 'RPG', '2026-06-15'),
('Final Fantasy XVII', 'final-fantasy-xvii', 'Nouvelle épopée dans l''univers Final Fantasy', 'PS6, PC', 'RPG', '2026-03-20'),
('Call of Duty 2025', 'call-of-duty-2025', 'Nouveau volet de la série FPS populaire', 'PC, PS6, Xbox Series X', 'FPS', '2025-11-07'),
('Mario Kart Ultimate', 'mario-kart-ultimate', 'La compilation ultime de Mario Kart', 'Switch', 'Racing', '2025-10-15'),
('Zelda: Echoes of Time', 'zelda-echoes-of-time', 'Nouvelle aventure de Link dans un monde mystérieux', 'Switch', 'Action-Aventure', '2026-01-30');

-- =====================================================
-- ARTICLES DE DÉMONSTRATION
-- =====================================================
INSERT INTO articles (title, slug, excerpt, content, status, author_id, category_id, game_id, published_at) VALUES 
('Test de la nouvelle console PlayStation 6', 'test-playstation-6', 'Une révolution technologique dans le monde du gaming', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.', 'published', 1, 2, NULL, NOW()),
('Cyberpunk 2078 : Date de sortie révélée', 'cyberpunk-2078-date-sortie', 'CD Projekt RED annonce la date officielle', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'published', 1, 1, 2, NOW()),
('Tournoi eSports mondial 2025', 'tournoi-esports-mondial-2025', 'Les meilleurs joueurs s''affrontent pour la gloire', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'published', 1, 4, NULL, NOW()),
('Retour de l''arcade', 'retour-arcade', 'Le renouveau des salles d''arcade en Belgique', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'published', 1, 6, NULL, NOW()),
('Setup gaming ultime', 'setup-gaming-ultime', 'Guide pour construire le PC gaming parfait', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'published', 1, 5, NULL, NOW()),
('Guide d''achat manettes 2025', 'guide-achat-manettes-2025', 'Les meilleures manettes pour chaque plateforme', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'published', 1, 3, NULL, NOW());

-- =====================================================
-- ASSOCIATIONS ARTICLES-TAGS
-- =====================================================
INSERT INTO article_tag (article_id, tag_id) VALUES 
(1, 2), -- PS6
(1, 7), -- AAA
(2, 3), -- PC
(2, 2), -- PS6
(2, 8), -- RPG
(2, 7), -- AAA
(3, 17), -- Europe
(4, 6), -- Indie
(5, 3), -- PC
(5, 5), -- Mobile
(6, 1), -- Nintendo
(6, 2), -- PlayStation
(6, 3); -- Xbox
