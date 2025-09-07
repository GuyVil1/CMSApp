# 🔧 AUDIT - HELPERS (FONCTIONS UTILITAIRES)

## 📋 **Fonctions utilitaires de l'application**

---

### **7. app/helpers/security_helper.php**
**📍 Emplacement :** `/app/helpers/security_helper.php`  
**🎯 Fonction :** Helper de sécurité centralisé pour la protection XSS, CSRF et validation  
**🔗 Interactions :**
- Utilisé par `core/Controller.php` pour la sécurité
- Utilisé par tous les contrôleurs et vues
- Centralise toutes les fonctions de sécurité de l'application

**⚙️ Fonctions principales :**
- `escape($data)` - Échappement HTML (protection XSS)
- `escapeAttr($data)` - Échappement pour attributs HTML
- `sanitize($data)` - Suppression des balises HTML
- `cleanForDisplay($data)` - Nettoyage et échappement pour affichage
- `validateEmail($email)` - Validation email
- `validateUrl($url)` - Validation URL
- `validateSlug($slug)` - Validation slug (alphanumérique + tirets)
- `generateSlug($text)` - Génération slug sécurisé
- `validateFilename($filename)` - Validation nom de fichier sécurisé
- `validateImageMimeType($mimeType)` - Validation type MIME image
- `getRealMimeType($filepath)` - Récupération type MIME réel
- `validateFileSize($size, $maxSize)` - Validation taille fichier
- `validateImageDimensions($filepath, $maxWidth, $maxHeight)` - Validation dimensions image
- `generateSecureFilename($originalName)` - Génération nom fichier sécurisé
- `containsMaliciousContent($content)` - Détection contenu malveillant
- `sanitizeHtml($content, $allowedTags)` - Nettoyage HTML avec balises autorisées

**🎨 CSS :** Aucun CSS intégré  
**📄 Feuilles de style :** Aucune référence

**🔐 Sécurité :**
- Protection XSS avec htmlspecialchars
- Validation des types MIME réels
- Détection de contenu malveillant
- Nettoyage HTML avec balises autorisées
- Validation des dimensions et tailles d'images

---

### **8. app/helpers/flash_helper.php**
**📍 Emplacement :** `/app/helpers/flash_helper.php`  
**🎯 Fonction :** Helper pour l'affichage des messages flash temporaires  
**🔗 Interactions :**
- Utilisé par les vues pour afficher les messages
- Complète le système de flash messages de `core/Controller.php`
- Gère l'affichage et la suppression des messages

**⚙️ Fonctions :**
- `displayFlashMessages()` - Affichage de tous les messages flash
- `displayFlashMessage($type)` - Affichage d'un type de message spécifique
- `hasFlashMessages()` - Vérification présence de messages
- `hasFlashMessage($type)` - Vérification d'un type spécifique
- `getFlashMessage($type)` - Récupération sans affichage

**🎨 CSS :** Aucun CSS intégré  
**📄 Feuilles de style :** Aucune référence

**💡 Types de messages :**
- success, error, info, warning

---

### **9. app/helpers/image_helper.php**
**📍 Emplacement :** `/app/helpers/image_helper.php`  
**🎯 Fonction :** Helper pour la gestion et le nettoyage des chemins d'images  
**🔗 Interactions :**
- Utilisé par les modèles et contrôleurs pour les images
- Gère les chemins d'images et les thumbnails
- Nettoie le contenu HTML des articles

**⚙️ Fonctions :**
- `cleanImagePath($imagePath)` - Nettoyage des chemins d'images
- `getImageUrl($imagePath)` - Génération URL correcte pour image
- `cleanArticleContent($content)` - Nettoyage contenu HTML d'article
- `imageExists($imagePath)` - Vérification existence image

**🎨 CSS :** Aucun CSS intégré  
**📄 Feuilles de style :** Aucune référence

**🖼️ Gestion des images :**
- Nettoyage des préfixes incorrects
- Gestion des thumbnails
- Recherche dans les dossiers parents
- URLs via uploads.php

---

### **10. app/helpers/navigation_helper.php**
**📍 Emplacement :** `/app/helpers/navigation_helper.php`  
**🎯 Fonction :** Helper pour la gestion des menus de navigation  
**🔗 Interactions :**
- Utilisé par `app/views/components/navbar.php`
- Utilisé par les layouts pour les menus
- Centralise la logique de navigation

**⚙️ Fonctions :**
- `getMainMenus()` - Récupération menus principaux
- `getFooterMenus()` - Récupération menus footer
- `formatFooterName($name)` - Formatage noms pour footer
- `validateNavigation()` - Validation cohérence navigation

**🎨 CSS :** Aucun CSS intégré  
**📄 Feuilles de style :** Aucune référence

**🧭 Menus gérés :**
- Accueil, Actualités, Tests, Dossiers, Trailers, Hardware
- Formatage différent navbar/footer
- Validation de cohérence

---

### **11. app/helpers/seo_helper.php**
**📍 Emplacement :** `/app/helpers/seo_helper.php`  
**🎯 Fonction :** Helper pour l'optimisation SEO (meta tags, sitemap, robots.txt)  
**🔗 Interactions :**
- Utilisé par les contrôleurs pour les meta tags
- Utilisé par `app/controllers/SeoController.php`
- Gère le sitemap et robots.txt

**⚙️ Fonctions principales :**
- `generateMetaTags($data)` - Génération meta tags génériques
- `generateArticleMetaTags($article, $baseUrl)` - Meta tags spécifiques articles
- `generateExcerptFromContent($content, $length)` - Génération excerpt
- `truncateText($text, $length)` - Troncature texte
- `generateKeywords($article)` - Génération mots-clés
- `generateSitemap()` - Génération sitemap XML
- `generateSitemapUrl($url, $priority, $changefreq, $lastmod)` - Entrée sitemap
- `getPublishedArticles()` - Récupération articles pour sitemap
- `getPublishedGames()` - Récupération jeux pour sitemap
- `getCategories()` - Récupération catégories pour sitemap
- `generateRobotsTxt()` - Génération robots.txt

**🎨 CSS :** Aucun CSS intégré  
**📄 Feuilles de style :** Aucune référence

**🔍 SEO Features :**
- Meta tags Open Graph et Twitter
- Sitemap XML automatique
- Robots.txt configuré
- Mots-clés intelligents
- Excerpts automatiques
- URLs canoniques

---
