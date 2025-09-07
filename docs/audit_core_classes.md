# 🏗️ AUDIT - CLASSES CORE

## 📋 **Classes de base de l'architecture MVC**

---

### **4. core/Controller.php**
**📍 Emplacement :** `/core/Controller.php`  
**🎯 Fonction :** Classe de base abstraite pour tous les contrôleurs, fournit les méthodes communes  
**🔗 Interactions :**
- Utilise `config/config.php` pour la configuration
- Utilise `app/helpers/security_helper.php` pour la sécurité
- Héritée par tous les contrôleurs de l'application
- Gère le rendu des vues et la validation des données

**⚙️ Fonctions principales :**
- `render($view, $data)` - Rendu d'une vue
- `renderWithLayout($view, $data)` - Rendu avec layout principal
- `renderPartial($view, $data)` - Rendu partiel (retourne string)
- `redirect($url)` - Redirection
- `jsonResponse($data, $statusCode)` - Réponse JSON
- `notFound($message)` - Erreur 404
- `serverError($message)` - Erreur 500
- `isPost()`, `isGet()` - Vérification méthode HTTP
- `getPostData()`, `getQueryParams()` - Récupération données
- `sanitizeString($input, $maxLength)` - Nettoyage chaîne
- `validateEmail($email)` - Validation email
- `validatePassword($password)` - Validation mot de passe
- `validateUsername($username)` - Validation nom d'utilisateur
- `validateArticleTitle($title)` - Validation titre article
- `validateArticleContent($content)` - Validation contenu article
- `generateSlug($title)` - Génération slug
- `setFlash($type, $message)` - Messages flash
- `getFlash()` - Récupération messages flash
- `validatePostData($requiredFields)` - Validation données POST avec CSRF

**🎨 CSS :** Aucun CSS intégré  
**📄 Feuilles de style :** Aucune référence

---

### **5. core/Database.php**
**📍 Emplacement :** `/core/Database.php`  
**🎯 Fonction :** Gestion de la base de données avec PDO, pattern Singleton  
**🔗 Interactions :**
- Utilise `config/config.php` pour les paramètres de connexion
- Utilisée par tous les modèles et contrôleurs
- Gère les connexions sécurisées avec PDO

**⚙️ Fonctions :**
- `getInstance()` - Instance PDO (singleton)
- `createConnection()` - Création connexion PDO
- `query($sql, $params)` - Requête SELECT (retourne array)
- `queryOne($sql, $params)` - Requête SELECT (retourne une ligne)
- `execute($sql, $params)` - Requête INSERT/UPDATE/DELETE
- `lastInsertId()` - ID dernière insertion
- `beginTransaction()` - Début transaction
- `commit()` - Validation transaction
- `rollback()` - Annulation transaction

**🎨 CSS :** Aucun CSS intégré  
**📄 Feuilles de style :** Aucune référence

**🔧 Configuration PDO :**
- Mode erreur : EXCEPTION
- Mode fetch : ASSOC
- Charset : utf8mb4
- Préparation : Désactivée (sécurité)

---

### **6. core/Auth.php**
**📍 Emplacement :** `/core/Auth.php`  
**🎯 Fonction :** Gestion de l'authentification, sessions sécurisées, rôles et permissions  
**🔗 Interactions :**
- Utilise `config/config.php` pour la configuration
- Utilise `core/Database.php` pour les requêtes
- Utilisée par tous les contrôleurs nécessitant une authentification
- Gère les sessions et la sécurité

**⚙️ Fonctions principales :**
- `init()` - Initialisation sessions sécurisées
- `initSession()` - Initialisation après session_start()
- `login($login, $password)` - Authentification utilisateur
- `logout()` - Déconnexion utilisateur
- `isLoggedIn()` - Vérification connexion
- `getUser()` - Récupération utilisateur connecté
- `getUserId()` - ID utilisateur connecté
- `getUserRole()` - Rôle utilisateur connecté
- `hasRole($role)` - Vérification rôle spécifique
- `hasAnyRole($roles)` - Vérification plusieurs rôles
- `hasAllRoles($roles)` - Vérification tous les rôles
- `hasPermission($requiredRole)` - Vérification permissions (hiérarchie)
- `generateCsrfToken()` - Génération token CSRF
- `verifyCsrfToken($token)` - Vérification token CSRF
- `regenerateCsrfToken()` - Régénération token CSRF
- `logActivity($userId, $action, $details)` - Log d'activité
- `hashPassword($password)` - Hashage mot de passe
- `verifyPassword($password, $hash)` - Vérification mot de passe
- `requireLogin()` - Redirection si non connecté
- `requireRole($role)` - Redirection si pas le bon rôle
- `requirePermission($role)` - Redirection si pas les permissions

**🎨 CSS :** Aucun CSS intégré  
**📄 Feuilles de style :** Aucune référence

**🔐 Sécurité :**
- Sessions sécurisées (httponly, secure, samesite)
- Protection CSRF avec tokens
- Hashage mots de passe (PASSWORD_DEFAULT)
- Régénération ID session
- Logs d'activité
- Hiérarchie des rôles (admin > editor > author > member)

---
