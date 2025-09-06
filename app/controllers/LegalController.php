<?php
declare(strict_types=1);

/**
 * Contrôleur Legal - Pages légales du site
 */

require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../models/Setting.php';

class LegalController extends Controller
{
    /**
     * Initialise les variables communes pour toutes les pages légales
     */
    private function getCommonVariables()
    {
        return [
            'isLoggedIn' => \Auth::isLoggedIn(),
            'user' => \Auth::getUser(),
            'darkMode' => $this->isDarkModeEnabled(),
            'allowRegistration' => $this->isRegistrationEnabled()
        ];
    }

    /**
     * Vérifie si le mode sombre est activé
     */
    private function isDarkModeEnabled(): bool
    {
        return \Setting::isEnabled('dark_mode');
    }

    /**
     * Vérifie si l'inscription est autorisée
     */
    private function isRegistrationEnabled(): bool
    {
        return \Setting::isEnabled('allow_registration');
    }

    public function mentionsLegales()
    {
        $commonVars = $this->getCommonVariables();
        $this->render('layout/public', array_merge($commonVars, [
            'pageTitle' => 'Mentions légales - Belgium Video Gaming',
            'pageDescription' => 'Mentions légales du site Belgium Video Gaming',
            'content' => $this->getLegalContent('mentions-legales', 'Mentions légales', 'Informations légales concernant Belgium Video Gaming')
        ]));
    }

    public function politiqueConfidentialite()
    {
        $commonVars = $this->getCommonVariables();
        $this->render('layout/public', array_merge($commonVars, [
            'pageTitle' => 'Politique de confidentialité - Belgium Video Gaming',
            'pageDescription' => 'Politique de confidentialité et protection des données personnelles',
            'content' => $this->getLegalContent('politique-confidentialite', 'Politique de confidentialité', 'Protection de vos données personnelles')
        ]));
    }

    public function cgu()
    {
        $commonVars = $this->getCommonVariables();
        $this->render('layout/public', array_merge($commonVars, [
            'pageTitle' => 'Conditions générales d\'utilisation - Belgium Video Gaming',
            'pageDescription' => 'Conditions générales d\'utilisation du site Belgium Video Gaming',
            'content' => $this->getLegalContent('cgu', 'Conditions générales d\'utilisation', 'Règles d\'utilisation du site Belgium Video Gaming')
        ]));
    }

    public function cookies()
    {
        $commonVars = $this->getCommonVariables();
        $this->render('layout/public', array_merge($commonVars, [
            'pageTitle' => 'Politique des cookies - Belgium Video Gaming',
            'pageDescription' => 'Politique d\'utilisation des cookies sur Belgium Video Gaming',
            'content' => $this->getLegalContent('cookies', 'Politique des cookies', 'Utilisation des cookies sur Belgium Video Gaming')
        ]));
    }

    /**
     * Génère le contenu HTML pour les pages légales avec le style des articles
     */
    private function getLegalContent($template, $title, $subtitle)
    {
        // Récupérer le contenu depuis la base de données
        // Correction : mapper les templates vers les bonnes clés
        $keyMapping = [
            'mentions-legales' => 'legal_mentions_content',
            'politique-confidentialite' => 'legal_privacy_content',
            'cgu' => 'legal_cgu_content',
            'cookies' => 'legal_cookies_content'
        ];
        
        $settingKey = $keyMapping[$template] ?? 'legal_' . str_replace('-', '_', $template) . '_content';
        $content = \Setting::get($settingKey, '');
        
        // Si le contenu est vide ou null, utiliser le contenu par défaut des fichiers
        if (empty(trim($content))) {
            ob_start();
            include __DIR__ . '/../views/legal/' . $template . '.php';
            $content = ob_get_clean();
        }
        
        // Retourner le contenu formaté comme un article
        return $this->formatLegalAsArticle($content, $title, $subtitle);
    }

    /**
     * Formate le contenu légal avec la structure d'un article
     */
    private function formatLegalAsArticle($content, $title, $subtitle)
    {
        // Si le contenu est vide, utiliser le contenu par défaut
        if (empty(trim($content))) {
            $content = '<p><em>Contenu en cours de rédaction...</em></p>';
        }
        
        return '
        <!-- Métadonnées de la page légale -->
        <div class="article-meta-section">
            <div class="article-meta-grid">
                <!-- Type de page -->
                <div class="meta-item">
                    <span class="meta-icon">📋</span>
                    <span class="meta-label">TYPE</span>
                    <span class="meta-value">Page légale</span>
                </div>
                
                <!-- Date de mise à jour -->
                <div class="meta-item">
                    <span class="meta-icon">📅</span>
                    <span class="meta-label">MISE À JOUR</span>
                    <span class="meta-value">' . date('d/m/Y') . '</span>
                </div>
                
                <!-- Statut -->
                <div class="meta-item">
                    <span class="meta-icon">✅</span>
                    <span class="meta-label">STATUT</span>
                    <span class="meta-value">En vigueur</span>
                </div>
                
                <!-- Site -->
                <div class="meta-item">
                    <span class="meta-icon">🌐</span>
                    <span class="meta-label">SITE</span>
                    <span class="meta-value">Belgium Video Gaming</span>
                </div>
            </div>
        </div>

        <!-- Titre de la page -->
        <div class="article-header">
            <h1 class="article-title">' . htmlspecialchars($title) . '</h1>
            <p class="article-subtitle">' . htmlspecialchars($subtitle) . '</p>
        </div>

        <!-- Contenu de la page -->
        <div class="article-content responsive-content">
            <div class="responsive-container">
                ' . $content . '
            </div>
        </div>';
    }
}
