# 🧹 Guide de Maintenance

## 📋 Scripts disponibles

### 1. 📊 Monitoring des performances
```bash
php scripts/monitor.php
```
**Fonctionnalités :**
- Surveillance de l'utilisation mémoire
- Statistiques du cache
- Métriques de la base de données
- Charge système
- Taille des logs

### 2. 🧹 Maintenance automatique
```bash
php scripts/maintenance.php
```
**Fonctionnalités :**
- Nettoyage des logs anciens (>30 jours)
- Suppression du cache expiré (>24h)
- Optimisation des tables de base de données
- Nettoyage des fichiers temporaires

## ⚙️ Configuration

### Fichier de performance
`config/performance.php` - Configuration des optimisations

### Paramètres configurables :
- **Cache** : TTL, limite mémoire, driver
- **Logs** : Niveau, rotation, compression
- **Middleware** : Rate limiting, sécurité
- **Base de données** : Pool de connexions, cache des requêtes
- **Événements** : Traitement asynchrone, taille des lots

## 🔧 Tâches de maintenance

### Quotidiennes
- [ ] Vérifier les logs d'erreur
- [ ] Surveiller l'utilisation mémoire
- [ ] Contrôler les performances

### Hebdomadaires
- [ ] Exécuter le script de maintenance
- [ ] Analyser les métriques
- [ ] Nettoyer les fichiers temporaires

### Mensuelles
- [ ] Audit de sécurité
- [ ] Optimisation de la base de données
- [ ] Mise à jour des dépendances

## 📊 Métriques importantes

### Performance
- **Temps de réponse** : < 200ms
- **Utilisation mémoire** : < 80%
- **Taux de cache** : > 90%
- **Charge système** : < 2.0

### Sécurité
- **Tentatives d'intrusion** : 0
- **Erreurs d'authentification** : < 5/jour
- **Requêtes suspectes** : 0

## 🚨 Alertes

### Niveau critique
- Utilisation mémoire > 90%
- Charge système > 5.0
- Erreurs 500 > 10/heure

### Niveau warning
- Utilisation mémoire > 80%
- Charge système > 3.0
- Erreurs 404 > 100/heure

## 📝 Logs

### Emplacements
- `app/logs/` - Logs applicatifs
- `logs/security/` - Logs de sécurité
- `app/cache/` - Cache et sessions

### Rotation
- **Logs applicatifs** : 30 jours
- **Logs de sécurité** : 90 jours
- **Cache** : 24 heures

## 🔄 Automatisation

### Cron jobs recommandés
```bash
# Maintenance quotidienne à 2h du matin
0 2 * * * php /path/to/scripts/maintenance.php

# Monitoring toutes les 5 minutes
*/5 * * * * php /path/to/scripts/monitor.php >> /var/log/bvg-monitor.log
```

### Scripts de surveillance
- Surveillance des services
- Vérification de l'espace disque
- Contrôle de la connectivité base de données
- Test de l'API

## 🛠️ Dépannage

### Problèmes courants
1. **Mémoire insuffisante** : Augmenter la limite PHP
2. **Cache corrompu** : Vider le cache manuellement
3. **Logs volumineux** : Exécuter la maintenance
4. **Performance dégradée** : Optimiser la base de données

### Commandes utiles
```bash
# Vider le cache
rm -rf app/cache/*

# Vérifier les permissions
find . -type f -exec chmod 644 {} \;
find . -type d -exec chmod 755 {} \;

# Tester la connectivité
php -r "require 'core/Database.php'; echo 'DB OK';"
```
