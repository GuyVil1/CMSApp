<?php
declare(strict_types=1);

/**
 * Classe Controller de base
 * Fournit les méthodes communes à tous les contrôleurs
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/helpers/security_helper.php';

abstract class Controller
{
    protected array $data = [];
    
    /**
     * Constructeur
     */
    public function __construct()
    {
        // Initialiser les données communes
        $this->data['site_name'] = Config::get('SITE_NAME');
        $this->data['site_tagline'] = Config::get('SITE_TAGLINE');
        $this->data['base_url'] = Config::get('BASE_URL');
    }
    
    /**
     * Rendre une vue
     */
    protected function render(string $view, array $data = []): void
    {
        // Fusionner les données
        $viewData = array_merge($this->data, $data);
        
        // Ajouter les fonctions de sécurité aux données
        $viewData['escape'] = [SecurityHelper::class, 'escape'];
        $viewData['escapeAttr'] = [SecurityHelper::class, 'escapeAttr'];
        $viewData['sanitize'] = [SecurityHelper::class, 'sanitize'];
        $viewData['cleanForDisplay'] = [SecurityHelper::class, 'cleanForDisplay'];
        
        // Extraire les variables pour la vue
        extract($viewData);
        
        // Inclure la vue
        $viewPath = __DIR__ . "/../app/views/{$view}.php";
        if (!file_exists($viewPath)) {
            throw new Exception("Vue non trouvée: {$view}");
        }
        
        include $viewPath;
    }
    
    /**
     * Rendre une vue avec le layout principal
     */
    protected function renderWithLayout(string $view, array $data = []): void
    {
        // Extraire le contenu de la vue
        ob_start();
        $this->render($view, $data);
        $content = ob_get_clean();
        
        // Rendre avec le layout principal
        $this->render('layout/main', array_merge($data, ['content' => $content]));
    }
    
    /**
     * Rendre une vue partielle (sans layout) et retourner le contenu
     */
    protected function renderPartial(string $view, array $data = []): string
    {
        $viewPath = __DIR__ . '/../app/views/' . $view . '.php';
        
        if (!file_exists($viewPath)) {
            throw new Exception("Vue non trouvée: {$view}");
        }
        
        // Extraire les variables dans le scope local
        extract($data);
        
        // Capturer le contenu
        ob_start();
        include $viewPath;
        $content = ob_get_clean();
        
        return $content;
    }
    
    /**
     * Rediriger vers une URL
     */
    protected function redirect(string $url): void
    {
        header("Location: {$url}");
        exit;
    }
    
    /**
     * Rediriger vers une route interne
     */
    protected function redirectTo(string $route): void
    {
        $baseUrl = Config::get('BASE_URL');
        $this->redirect("{$baseUrl}{$route}");
    }
    
    /**
     * Retourner une réponse JSON
     */
    protected function json(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    /**
     * Retourner une erreur 404
     */
    protected function notFound(string $message = 'Page non trouvée'): void
    {
        http_response_code(404);
        $this->render('layout/404', ['message' => $message]);
        exit;
    }
    
    /**
     * Retourner une erreur 500
     */
    protected function serverError(string $message = 'Erreur interne du serveur'): void
    {
        http_response_code(500);
        $this->render('layout/500', ['message' => $message]);
        exit;
    }
    
    /**
     * Vérifier si la requête est POST
     */
    protected function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }
    
    /**
     * Vérifier si la requête est GET
     */
    protected function isGet(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }
    
    /**
     * Obtenir les données POST
     */
    protected function getPostData(): array
    {
        return $_POST;
    }
    
    /**
     * Obtenir les paramètres GET
     */
    protected function getQueryParams(): array
    {
        return $_GET;
    }
    
    /**
     * Obtenir un paramètre GET
     */
    protected function getQueryParam(string $key, $default = null)
    {
        return $_GET[$key] ?? $default;
    }
    
    /**
     * Obtenir un paramètre POST
     */
    protected function getPostParam(string $key, $default = null)
    {
        return $_POST[$key] ?? $default;
    }
    
    /**
     * Valider et nettoyer une chaîne
     */
    protected function sanitizeString(string $input, int $maxLength = 255): string
    {
        $input = trim($input);
        if (strlen($input) > $maxLength) {
            $input = substr($input, 0, $maxLength);
        }
        return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Valider un email
     */
    protected function validateEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false && strlen($email) <= 255;
    }
    
    /**
     * Valider un mot de passe
     */
    protected function validatePassword(string $password): array
    {
        $errors = [];
        
        if (strlen($password) < 8) {
            $errors[] = 'Le mot de passe doit contenir au moins 8 caractères';
        }
        
        if (strlen($password) > 128) {
            $errors[] = 'Le mot de passe ne peut pas dépasser 128 caractères';
        }
        
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Le mot de passe doit contenir au moins une majuscule';
        }
        
        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = 'Le mot de passe doit contenir au moins une minuscule';
        }
        
        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = 'Le mot de passe doit contenir au moins un chiffre';
        }
        
        return $errors;
    }
    
    /**
     * Valider un nom d'utilisateur
     */
    protected function validateUsername(string $username): array
    {
        $errors = [];
        
        if (strlen($username) < 3) {
            $errors[] = 'Le nom d\'utilisateur doit contenir au moins 3 caractères';
        }
        
        if (strlen($username) > 50) {
            $errors[] = 'Le nom d\'utilisateur ne peut pas dépasser 50 caractères';
        }
        
        if (!preg_match('/^[a-zA-Z0-9_-]+$/', $username)) {
            $errors[] = 'Le nom d\'utilisateur ne peut contenir que des lettres, chiffres, tirets et underscores';
        }
        
        return $errors;
    }
    
    /**
     * Valider un titre d'article
     */
    protected function validateArticleTitle(string $title): array
    {
        $errors = [];
        
        if (strlen($title) < 5) {
            $errors[] = 'Le titre doit contenir au moins 5 caractères';
        }
        
        if (strlen($title) > 200) {
            $errors[] = 'Le titre ne peut pas dépasser 200 caractères';
        }
        
        return $errors;
    }
    
    /**
     * Valider le contenu d'un article
     */
    protected function validateArticleContent(string $content): array
    {
        $errors = [];
        
        if (strlen($content) < 10) {
            $errors[] = 'Le contenu doit contenir au moins 10 caractères';
        }
        
        if (strlen($content) > 50000) {
            $errors[] = 'Le contenu ne peut pas dépasser 50 000 caractères';
        }
        
        return $errors;
    }
    
    /**
     * Générer un slug à partir d'un titre
     */
    protected function generateSlug(string $title): string
    {
        // Convertir en minuscules
        $slug = strtolower($title);
        
        // Remplacer les caractères spéciaux
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
        
        // Remplacer les espaces par des tirets
        $slug = preg_replace('/[\s-]+/', '-', $slug);
        
        // Supprimer les tirets en début et fin
        $slug = trim($slug, '-');
        
        return $slug;
    }
    
    /**
     * Système de Flash Messages
     * Permet d'afficher des messages temporaires entre deux requêtes
     */
    
    /**
     * Définir un message flash
     */
    protected function setFlash(string $type, string $message): void
    {
        // Initialiser le tableau des flash messages s'il n'existe pas
        if (!isset($_SESSION['flash'])) {
            $_SESSION['flash'] = [];
        }
        
        // Ajouter le message
        $_SESSION['flash'][$type] = $message;
    }
    
    /**
     * Définir un message flash de succès
     */
    protected function setFlashSuccess(string $message): void
    {
        $this->setFlash('success', $message);
    }
    
    /**
     * Définir un message flash d'erreur
     */
    protected function setFlashError(string $message): void
    {
        $this->setFlash('error', $message);
    }
    
    /**
     * Définir un message flash d'information
     */
    protected function setFlashInfo(string $message): void
    {
        $this->setFlash('info', $message);
    }
    
    /**
     * Définir un message flash d'avertissement
     */
    protected function setFlashWarning(string $message): void
    {
        $this->setFlash('warning', $message);
    }
    
    /**
     * Récupérer tous les messages flash
     */
    protected function getFlash(): array
    {
        $flash = $_SESSION['flash'] ?? [];
        
        // Supprimer les messages après les avoir récupérés
        unset($_SESSION['flash']);
        
        return $flash;
    }
    
    /**
     * Récupérer un message flash spécifique
     */
    protected function getFlashMessage(string $type): ?string
    {
        $flash = $this->getFlash();
        return $flash[$type] ?? null;
    }
    
    /**
     * Vérifier s'il y a des messages flash
     */
    protected function hasFlash(): bool
    {
        return isset($_SESSION['flash']) && !empty($_SESSION['flash']);
    }
    
    /**
     * Vérifier s'il y a un message flash d'un type spécifique
     */
    protected function hasFlashType(string $type): bool
    {
        return isset($_SESSION['flash'][$type]);
    }
    
    /**
     * Supprimer tous les messages flash
     */
    protected function clearFlash(): void
    {
        unset($_SESSION['flash']);
    }
    
    /**
     * Supprimer un type de message flash spécifique
     */
    protected function clearFlashType(string $type): void
    {
        if (isset($_SESSION['flash'][$type])) {
            unset($_SESSION['flash'][$type]);
        }
    }
    
    /**
     * Récupérer les messages flash pour l'affichage dans les vues
     * Cette méthode est utilisée dans les vues pour afficher les messages
     */
    protected function getFlashForView(): array
    {
        $flash = $_SESSION['flash'] ?? [];
        
        // Ne pas supprimer ici, laisser les vues le faire
        return $flash;
    }
    
    /**
     * Marquer les messages flash comme affichés (à appeler après affichage)
     */
    protected function markFlashAsDisplayed(): void
    {
        unset($_SESSION['flash']);
    }
    
    /**
     * Valider les données POST avec protection CSRF
     */
    protected function validatePostData(array $requiredFields = []): array
    {
        if (!$this->isPost()) {
            throw new Exception('Méthode non autorisée');
        }
        
        // Vérifier le token CSRF
        $csrfToken = $this->getPostParam('csrf_token', '');
        if (!Auth::verifyCsrfToken($csrfToken)) {
            throw new Exception('Token de sécurité invalide');
        }
        
        // Valider les champs requis
        $data = [];
        foreach ($requiredFields as $field) {
            $value = $this->getPostParam($field, '');
            if (empty($value)) {
                throw new Exception("Le champ '{$field}' est requis");
            }
            $data[$field] = $value;
        }
        
        return $data;
    }
    
    /**
     * Nettoyer et valider une chaîne
     */
    protected function cleanString(string $value, int $maxLength = 255): string
    {
        $value = trim($value);
        if (strlen($value) > $maxLength) {
            $value = substr($value, 0, $maxLength);
        }
        return SecurityHelper::cleanForDisplay($value);
    }
    
    /**
     * Valider et nettoyer un email
     */
    protected function validateAndCleanEmail(string $email): string
    {
        $email = trim($email);
        if (!SecurityHelper::validateEmail($email)) {
            throw new Exception('Adresse email invalide');
        }
        return $email;
    }
    
    /**
     * Valider et nettoyer une URL
     */
    protected function validateAndCleanUrl(string $url): string
    {
        $url = trim($url);
        if (!empty($url) && !SecurityHelper::validateUrl($url)) {
            throw new Exception('URL invalide');
        }
        return $url;
    }
    
    /**
     * Valider et nettoyer un slug
     */
    protected function validateAndCleanSlug(string $slug): string
    {
        $slug = trim($slug);
        if (!SecurityHelper::validateSlug($slug)) {
            throw new Exception('Slug invalide (utilisez uniquement des lettres, chiffres et tirets)');
        }
        return $slug;
    }
}
