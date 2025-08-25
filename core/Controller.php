<?php
declare(strict_types=1);

/**
 * Classe Controller de base
 * Fournit les méthodes communes à tous les contrôleurs
 */

require_once __DIR__ . '/../config/config.php';

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
    protected function sanitizeString(string $input): string
    {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Valider un email
     */
    protected function validateEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
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
}
