<?php
declare(strict_types=1);

/**
 * Helper de validation moderne et robuste
 * Protection contre les attaques et validation des données
 */
class ValidationHelper
{
    private static array $errors = [];
    private static array $sanitized = [];
    
    /**
     * Valider et nettoyer les données d'entrée
     */
    public static function validate(array $data, array $rules): array
    {
        self::$errors = [];
        self::$sanitized = [];
        
        foreach ($rules as $field => $rule) {
            $value = $data[$field] ?? null;
            self::validateField($field, $value, $rule);
        }
        
        return [
            'valid' => empty(self::$errors),
            'errors' => self::$errors,
            'sanitized' => self::$sanitized
        ];
    }
    
    /**
     * Valider un champ spécifique
     */
    private static function validateField(string $field, $value, array $rules): void
    {
        // Vérifier si le champ est requis
        if (isset($rules['required']) && $rules['required'] && empty($value)) {
            self::$errors[$field] = "Le champ $field est requis";
            return;
        }
        
        // Si le champ est vide et non requis, on passe
        if (empty($value) && !isset($rules['required'])) {
            self::$sanitized[$field] = null;
            return;
        }
        
        // Nettoyer la valeur
        $sanitized = self::sanitize($value, $rules);
        self::$sanitized[$field] = $sanitized;
        
        // Valider selon les règles
        foreach ($rules as $rule => $ruleValue) {
            if ($rule === 'required') continue;
            
            if (!self::applyRule($field, $sanitized, $rule, $ruleValue)) {
                self::$errors[$field] = self::getErrorMessage($field, $rule, $ruleValue);
                break; // Arrêter à la première erreur
            }
        }
    }
    
    /**
     * Nettoyer les données
     */
    private static function sanitize($value, array $rules): mixed
    {
        if (is_string($value)) {
            // Nettoyer les chaînes
            $value = trim($value);
            $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
            
            // Nettoyer selon le type
            if (isset($rules['type'])) {
                switch ($rules['type']) {
                    case 'email':
                        $value = filter_var($value, FILTER_SANITIZE_EMAIL);
                        break;
                    case 'url':
                        $value = filter_var($value, FILTER_SANITIZE_URL);
                        break;
                    case 'int':
                        $value = filter_var($value, FILTER_SANITIZE_NUMBER_INT);
                        break;
                    case 'float':
                        $value = filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                        break;
                }
            }
        }
        
        return $value;
    }
    
    /**
     * Appliquer une règle de validation
     */
    private static function applyRule(string $field, $value, string $rule, $ruleValue): bool
    {
        switch ($rule) {
            case 'min_length':
                return strlen($value) >= $ruleValue;
                
            case 'max_length':
                return strlen($value) <= $ruleValue;
                
            case 'min':
                return is_numeric($value) && $value >= $ruleValue;
                
            case 'max':
                return is_numeric($value) && $value <= $ruleValue;
                
            case 'email':
                return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
                
            case 'url':
                return filter_var($value, FILTER_VALIDATE_URL) !== false;
                
            case 'in':
                return in_array($value, $ruleValue);
                
            case 'regex':
                return preg_match($ruleValue, $value);
                
            case 'type':
                switch ($ruleValue) {
                    case 'int':
                        return is_numeric($value) && (int)$value == $value;
                    case 'float':
                        return is_numeric($value);
                    case 'string':
                        return is_string($value);
                    case 'array':
                        return is_array($value);
                }
                break;
                
            case 'unique':
                // Vérifier l'unicité en base de données
                return self::checkUnique($field, $value, $ruleValue);
                
            case 'file':
                return self::validateFile($value, $ruleValue);
        }
        
        return true;
    }
    
    /**
     * Vérifier l'unicité en base de données
     */
    private static function checkUnique(string $field, $value, array $config): bool
    {
        try {
            $db = new Database();
            $table = $config['table'];
            $column = $config['column'] ?? $field;
            $exclude = $config['exclude'] ?? null;
            
            $sql = "SELECT COUNT(*) FROM $table WHERE $column = ?";
            $params = [$value];
            
            if ($exclude) {
                $sql .= " AND id != ?";
                $params[] = $exclude;
            }
            
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $count = $stmt->fetchColumn();
            
            return $count == 0;
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Valider un fichier uploadé
     */
    private static function validateFile($file, array $config): bool
    {
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            return false;
        }
        
        // Vérifier la taille
        if (isset($config['max_size']) && $file['size'] > $config['max_size']) {
            return false;
        }
        
        // Vérifier le type MIME
        if (isset($config['allowed_types'])) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);
            
            if (!in_array($mimeType, $config['allowed_types'])) {
                return false;
            }
        }
        
        // Vérifier l'extension
        if (isset($config['allowed_extensions'])) {
            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!in_array($extension, $config['allowed_extensions'])) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Obtenir le message d'erreur
     */
    private static function getErrorMessage(string $field, string $rule, $ruleValue): string
    {
        $messages = [
            'min_length' => "Le champ $field doit contenir au moins $ruleValue caractères",
            'max_length' => "Le champ $field ne peut pas dépasser $ruleValue caractères",
            'min' => "Le champ $field doit être supérieur ou égal à $ruleValue",
            'max' => "Le champ $field doit être inférieur ou égal à $ruleValue",
            'email' => "Le champ $field doit être une adresse email valide",
            'url' => "Le champ $field doit être une URL valide",
            'in' => "Le champ $field doit être l'une des valeurs autorisées",
            'regex' => "Le champ $field ne respecte pas le format requis",
            'unique' => "Le champ $field existe déjà",
            'file' => "Le fichier $field n'est pas valide"
        ];
        
        return $messages[$rule] ?? "Le champ $field n'est pas valide";
    }
    
    /**
     * Valider les données CSRF
     */
    public static function validateCSRF(string $token): bool
    {
        if (!isset($_SESSION['csrf_token'])) {
            return false;
        }
        
        return hash_equals($_SESSION['csrf_token'], $token);
    }
    
    /**
     * Générer un token CSRF
     */
    public static function generateCSRF(): string
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Valider les données XSS
     */
    public static function sanitizeXSS(string $input): string
    {
        // Supprimer les balises HTML dangereuses
        $input = strip_tags($input, '<p><br><strong><em><ul><ol><li><a><img>');
        
        // Nettoyer les attributs dangereux
        $input = preg_replace('/on\w+="[^"]*"/i', '', $input);
        $input = preg_replace('/javascript:/i', '', $input);
        
        return $input;
    }
    
    /**
     * Valider les données SQL Injection
     */
    public static function sanitizeSQL(string $input): string
    {
        // Échapper les caractères spéciaux SQL
        $input = addslashes($input);
        
        // Supprimer les commentaires SQL
        $input = preg_replace('/--.*$/m', '', $input);
        $input = preg_replace('/\/\*.*?\*\//s', '', $input);
        
        return $input;
    }
}
