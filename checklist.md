# 🚀 CHECKLIST DÉPLOIEMENT - Belgium Vidéo Gaming v1.0.0

## ⚠️ **ATTENTION : NE DÉPLOYEZ PAS SANS AVOIR COCHÉ TOUTES LES CASES !**

---

## 🛡️ **1. SÉCURITÉ - CRITIQUE (30 min)**

### **A. Validation des Uploads**
- [ ] **UploadController.php** : Types de fichiers limités aux images uniquement
- [ ] **UploadController.php** : Taille maximale limitée (ex: 5MB)
- [ ] **UploadController.php** : Noms de fichiers sécurisés (pas d'espaces, caractères spéciaux)
- [ ] **UploadController.php** : Pas d'exécution de code dans les uploads
- [ ] **Dossier uploads/** : Permissions 755 (lecture/écriture pour le serveur)
- [ ] **Test** : Essayer d'uploader un fichier .php (doit être rejeté)

### **B. Protection CSRF**
- [ ] **Tous les formulaires admin** : Tokens CSRF présents
- [ ] **Validation côté serveur** : Tokens vérifiés avant traitement
- [ ] **Formulaires vérifiés** :
  - [ ] Articles (création/édition)
  - [ ] Utilisateurs (création/édition)
  - [ ] Jeux (création/édition)
  - [ ] Catégories (création/édition)
  - [ ] Tags (création/édition)
  - [ ] Paramètres (sauvegarde)
  - [ ] Uploads (upload de fichiers)

### **C. Sanitisation des Entrées**
- [ ] **Toutes les sorties** : htmlspecialchars() appliqué
- [ ] **Toutes les entrées** : strip_tags() ou validation appropriée
- [ ] **Base de données** : PDO avec requêtes préparées
- [ ] **Pas d'évaluation** : eval() ou exec() interdits
- [ ] **Test** : Essayer d'injecter du HTML/JS dans un formulaire

---

## ⚙️ **2. CONFIGURATION PRODUCTION - CRITIQUE (15 min)**

### **A. Base de Données**
- [ ] **config/config.php** : Identifiants de production (pas localhost)
- [ ] **Mot de passe** : Fort et unique
- [ ] **Nom de base** : Sécurisé (pas "test" ou "dev")
- [ ] **Charset** : UTF-8 configuré
- [ ] **Connexion** : Testée en local avec les vrais identifiants

### **B. Chemins et Permissions**
- [ ] **Chemins absolus** : Configurés pour la production
- [ ] **Dossier public/uploads/** : Permissions 755
- [ ] **Fichiers de config** : Permissions 644 (lecture seule)
- [ ] **Pas d'accès direct** : Aux fichiers .php sensibles
- [ ] **Test** : Vérifier que les chemins fonctionnent

### **C. Variables d'Environnement**
- [ ] **Base de données** : Variables d'environnement sécurisées
- [ ] **Clés secrètes** : Générées et sécurisées
- [ ] **URLs** : Configurées pour la production
- [ ] **Debug** : Mode debug désactivé

---

## 🚨 **3. GESTION DES ERREURS - CRITIQUE (10 min)**

### **A. Affichage des Erreurs**
- [ ] **display_errors** : OFF en production
- [ ] **log_errors** : ON
- [ ] **error_reporting** : Configuré appropriément
- [ ] **Test** : Vérifier qu'aucune erreur PHP ne s'affiche

### **B. Pages d'Erreur**
- [ ] **404.php** : Fonctionnelle et personnalisée
- [ ] **500.php** : Fonctionnelle et personnalisée
- [ ] **403.php** : Fonctionnelle et personnalisée
- [ ] **Test** : Vérifier que les pages d'erreur s'affichent correctement

### **C. Logs d'Erreur**
- [ ] **Dossier logs/** : Créé et accessible en écriture
- [ ] **Rotation des logs** : Configurée
- [ ] **Niveau de log** : Approprié pour la production

---

## 🔧 **4. MODE MAINTENANCE - CRITIQUE (5 min)**

### **A. Fonctionnement**
- [ ] **Activation** : Via panneau admin fonctionne
- [ ] **Désactivation** : Via panneau admin fonctionne
- [ ] **Page de maintenance** : S'affiche correctement
- [ ] **Accès admin** : Fonctionne en mode maintenance
- [ ] **Bypass** : Pour les administrateurs opérationnel

### **B. Test Complet**
- [ ] **Mode normal** : Site accessible
- [ ] **Mode maintenance** : Page de maintenance s'affiche
- [ ] **Connexion admin** : Fonctionne en mode maintenance
- [ ] **Désactivation** : Retour au mode normal

---

## 📊 **5. FONCTIONNALITÉS CRITIQUES - IMPORTANT (20 min)**

### **A. Administration**
- [ ] **Connexion admin** : Fonctionne
- [ ] **Création d'articles** : Fonctionne
- [ ] **Upload d'images** : Fonctionne
- [ ] **Gestion des utilisateurs** : Fonctionne
- [ ] **Paramètres** : Sauvegarde fonctionne

### **B. Frontend**
- [ ] **Page d'accueil** : S'affiche correctement
- [ ] **Articles** : Affichage et navigation
- [ ] **Images** : Chargement et optimisation
- [ ] **Thèmes** : Application correcte
- [ ] **Responsive** : Fonctionne sur mobile

### **C. SEO**
- [ ] **robots.txt** : Accessible et correct
- [ ] **sitemap.xml** : Accessible et à jour
- [ ] **Meta tags** : Dynamiques et corrects
- [ ] **URLs** : Propres et fonctionnelles

---

## 🧪 **6. TESTS FINAUX - IMPORTANT (15 min)**

### **A. Tests de Sécurité**
- [ ] **Injection SQL** : Tentative d'injection (doit échouer)
- [ ] **XSS** : Tentative d'injection de script (doit échouer)
- [ ] **Upload malveillant** : Fichier .php (doit être rejeté)
- [ ] **CSRF** : Tentative sans token (doit échouer)

### **B. Tests de Performance**
- [ ] **Temps de chargement** : < 3 secondes
- [ ] **Images** : Optimisées et rapides
- [ ] **Base de données** : Requêtes rapides
- [ ] **Mémoire** : Pas de fuites

### **C. Tests de Compatibilité**
- [ ] **Navigateurs** : Chrome, Firefox, Safari, Edge
- [ ] **Mobile** : Responsive design
- [ ] **Tablette** : Affichage correct
- [ ] **Résolutions** : Différentes tailles d'écran

---

## 📋 **7. PRÉPARATION DÉPLOIEMENT - IMPORTANT (10 min)**

### **A. Backup**
- [ ] **Base de données** : Sauvegardée
- [ ] **Fichiers** : Sauvegardés
- [ ] **Configuration** : Sauvegardée
- [ ] **Test de restauration** : Effectué

### **B. Documentation**
- [ ] **Identifiants** : Notés et sécurisés
- [ ] **URLs** : Notées
- [ ] **Procédure** : Documentée
- [ ] **Contacts** : Notés

### **C. Plan de Rollback**
- [ ] **Procédure** : Définie
- [ ] **Temps** : Estimé
- [ ] **Responsable** : Identifié
- [ ] **Test** : Effectué

---

## 🚀 **8. DÉPLOIEMENT - FINAL (30 min)**

### **A. Upload des Fichiers**
- [ ] **Fichiers** : Uploadés sur le serveur
- [ ] **Permissions** : Configurées correctement
- [ ] **Chemins** : Vérifiés
- [ ] **Accès** : Testé

### **B. Base de Données**
- [ ] **Import** : Base de données importée
- [ ] **Connexion** : Testée
- [ ] **Données** : Vérifiées
- [ ] **Performance** : Testée

### **C. Configuration**
- [ ] **Variables** : Configurées
- [ ] **Chemins** : Ajustés
- [ ] **Permissions** : Vérifiées
- [ ] **Sécurité** : Activée

### **D. Tests Post-Déploiement**
- [ ] **Site** : Accessible
- [ ] **Admin** : Fonctionne
- [ ] **Uploads** : Fonctionnent
- [ ] **Mode maintenance** : Activé

---

## ✅ **VALIDATION FINALE**

### **AVANT DE DÉPLOYER, VÉRIFIEZ :**
- [ ] **Toutes les cases** de sécurité sont cochées
- [ ] **Toutes les cases** de configuration sont cochées
- [ ] **Toutes les cases** de gestion d'erreurs sont cochées
- [ ] **Toutes les cases** de mode maintenance sont cochées
- [ ] **Tous les tests** sont passés
- [ ] **Backup** est effectué
- [ ] **Plan de rollback** est prêt

### **SI UNE SEULE CASE N'EST PAS COCHÉE :**
- ❌ **STOP** - Ne déployez pas
- 🔧 **Corrigez** le problème
- 🧪 **Retestez** en local
- ✅ **Relancez** la checklist

---

## 🎯 **RÉSUMÉ**

**Temps total estimé** : 2-3 heures
**Priorité** : Sécurité > Configuration > Erreurs > Maintenance
**Règle d'or** : Une seule case non cochée = STOP

**Cette checklist est votre filet de sécurité. Respectez-la !** 🛡️
