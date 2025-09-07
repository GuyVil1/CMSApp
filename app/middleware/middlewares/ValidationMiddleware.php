<?php
declare(strict_types=1);

require_once __DIR__ . '/../MiddlewareInterface.php';
require_once __DIR__ . '/../RequestInterface.php';
require_once __DIR__ . '/../ResponseInterface.php';
require_once __DIR__ . '/../HttpResponse.php';

/**
 * Middleware de validation des données
 * Valide les données POST et GET selon des règles définies
 */
class ValidationMiddleware implements MiddlewareInterface
{
    private array $validationRules;
    private array $excludedRoutes;
    
    public function __construct(array $validationRules = [], array $excludedRoutes = [])
    {
        $this->validationRules = $validationRules;
        $this->excludedRoutes = $excludedRoutes;
    }
    
    public function handle(RequestInterface $request, callable $next): ResponseInterface
    {
        $uri = $request->getUri();
        
        // Vérifier si la route est exclue
        if ($this->isExcludedRoute($uri)) {
            return $next($request);
        }
        
        // Vérifier s'il y a des règles de validation pour cette route
        $rules = $this->getValidationRules($uri);
        if (empty($rules)) {
            return $next($request);
        }
        
        // Valider les données
        $errors = $this->validateData($request, $rules);
        if (!empty($errors)) {
            return HttpResponse::json([
                'success' => false,
                'errors' => $errors
            ], 400);
        }
        
        // Continuer vers le prochain middleware
        return $next($request);
    }
    
    public function canHandle(RequestInterface $request): bool
    {
        $uri = $request->getUri();
        return !$this->isExcludedRoute($uri) && !empty($this->getValidationRules($uri));
    }
    
    public function getPriority(): int
    {
        return 80; // Priorité moyenne
    }
    
    /**
     * Vérifier si une route est exclue de la validation
     */
    private function isExcludedRoute(string $uri): bool
    {
        foreach ($this->excludedRoutes as $pattern) {
            if ($this->matchRoute($uri, $pattern)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Obtenir les règles de validation pour une route
     */
    private function getValidationRules(string $uri): array
    {
        foreach ($this->validationRules as $pattern => $rules) {
            if ($this->matchRoute($uri, $pattern)) {
                return $rules;
            }
        }
        
        return [];
    }
    
    /**
     * Valider les données selon les règles
     */
    private function validateData(RequestInterface $request, array $rules): array
    {
        $errors = [];
        
        foreach ($rules as $field => $fieldRules) {
            $value = $request->getPostParam($field);
            
            foreach ($fieldRules as $rule => $ruleValue) {
                $error = $this->validateField($field, $value, $rule, $ruleValue);
                if ($error) {
                    $errors[$field][] = $error;
                }
            }
        }
        
        return $errors;
    }
    
    /**
     * Valider un champ selon une règle
     */
    private function validateField(string $field, $value, string $rule, $ruleValue): ?string
    {
        switch ($rule) {
            case 'required':
                if (empty($value)) {
                    return "Le champ {$field} est requis";
                }
                break;
                
            case 'min_length':
                if (strlen($value) < $ruleValue) {
                    return "Le champ {$field} doit contenir au moins {$ruleValue} caractères";
                }
                break;
                
            case 'max_length':
                if (strlen($value) > $ruleValue) {
                    return "Le champ {$field} ne peut pas dépasser {$ruleValue} caractères";
                }
                break;
                
            case 'email':
                if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    return "Le champ {$field} doit être une adresse email valide";
                }
                break;
                
            case 'numeric':
                if (!is_numeric($value)) {
                    return "Le champ {$field} doit être numérique";
                }
                break;
                
            case 'in':
                if (!in_array($value, $ruleValue)) {
                    return "Le champ {$field} doit être l'une des valeurs suivantes: " . implode(', ', $ruleValue);
                }
                break;
        }
        
        return null;
    }
    
    /**
     * Matcher une route avec un pattern
     */
    private function matchRoute(string $uri, string $pattern): bool
    {
        $pattern = str_replace('*', '.*', $pattern);
        // Échapper les délimiteurs de regex
        $pattern = preg_quote($pattern, '/');
        $pattern = '/^' . $pattern . '$/';
        
        return preg_match($pattern, $uri) === 1;
    }
}
