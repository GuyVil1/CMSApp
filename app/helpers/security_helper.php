<?php
declare(strict_types=1);

/**
 * Helper de sécurité
 * Centralise les fonctions de protection XSS, CSRF et validation
 */

class SecurityHelper
{
    /**
     * Échapper les données pour l'affichage HTML (protection XSS)
     */
    public static function escape(string $data): string
    {
        return htmlspecialchars($data, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
    
    /**
     * Échapper les données pour les attributs HTML
     */
    public static function escapeAttr(string $data): string
    {
        return htmlspecialchars($data, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
    
    /**
     * Nettoyer les données utilisateur (supprimer les balises HTML)
     */
    public static function sanitize(string $data): string
    {
        return strip_tags($data);
    }
    
    /**
     * Nettoyer et échapper les données pour l'affichage
     */
    public static function cleanForDisplay(string $data): string
    {
        return self::escape(trim($data));
    }
    
    /**
     * Valider une adresse email
     */
    public static function validateEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Valider une URL
     */
    public static function validateUrl(string $url): bool
    {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }
    
    /**
     * Valider un slug (alphanumérique + tirets)
     */
    public static function validateSlug(string $slug): bool
    {
        return preg_match('/^[a-z0-9-]+$/', $slug) === 1;
    }
    
    /**
     * Générer un slug sécurisé
     */
    public static function generateSlug(string $text): string
    {
        // Convertir en minuscules
        $text = strtolower($text);
        
        // Remplacer les caractères spéciaux
        $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
        
        // Remplacer les espaces par des tirets
        $text = preg_replace('/[\s-]+/', '-', $text);
        
        // Supprimer les tirets en début/fin
        return trim($text, '-');
    }
    
    /**
     * Valider un nom de fichier sécurisé
     */
    public static function validateFilename(string $filename): bool
    {
        // Vérifier qu'il n'y a pas de caractères dangereux
        if (preg_match('/[\/\\\\:*?"<>|]/', $filename)) {
            return false;
        }
        
        // Vérifier qu'il n'y a pas de points multiples
        if (strpos($filename, '..') !== false) {
            return false;
        }
        
        // Vérifier la longueur
        if (strlen($filename) > 255) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Valider un type MIME d'image
     */
    public static function validateImageMimeType(string $mimeType): bool
    {
        $allowedTypes = [
            'image/jpeg',
            'image/png', 
            'image/gif',
            'image/webp'
        ];
        
        return in_array($mimeType, $allowedTypes);
    }
    
    /**
     * Obtenir le type MIME réel d'un fichier (plus sécurisé que $_FILES['type'])
     */
    public static function getRealMimeType(string $filepath): string
    {
        if (!file_exists($filepath)) {
            return 'application/octet-stream';
        }
        
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $filepath);
        finfo_close($finfo);
        
        return $mimeType ?: 'application/octet-stream';
    }
    
    /**
     * Valider la taille d'un fichier
     */
    public static function validateFileSize(int $size, int $maxSize = 15728640): bool // 15MB par défaut
    {
        return $size <= $maxSize && $size > 0;
    }
    
    /**
     * Valider les dimensions d'une image
     */
    public static function validateImageDimensions(string $filepath, int $maxWidth = 4096, int $maxHeight = 4096): bool
    {
        if (!file_exists($filepath)) {
            return false;
        }
        
        $imageInfo = getimagesize($filepath);
        if ($imageInfo === false) {
            return false;
        }
        
        [$width, $height] = $imageInfo;
        
        return $width <= $maxWidth && $height <= $maxHeight;
    }
    
    /**
     * Validation renforcée du contenu d'image
     */
    public static function validateImageContent(string $filepath): array
    {
        if (!file_exists($filepath)) {
            return ['valid' => false, 'message' => 'Fichier introuvable'];
        }
        
        // Vérifier que c'est bien une image avec getimagesize
        $imageInfo = getimagesize($filepath);
        if ($imageInfo === false) {
            return ['valid' => false, 'message' => 'Fichier corrompu ou non-image'];
        }
        
        // Vérifier que le MIME type détecté correspond à un type d'image autorisé
        $detectedMimeType = $imageInfo['mime'];
        if (!self::validateImageMimeType($detectedMimeType)) {
            return ['valid' => false, 'message' => 'Contenu d\'image invalide détecté'];
        }
        
        // Vérifier les dimensions
        [$width, $height] = $imageInfo;
        if ($width <= 0 || $height <= 0) {
            return ['valid' => false, 'message' => 'Image invalide (dimensions nulles)'];
        }
        
        // Vérifier que l'image n'est pas trop grande
        if ($width > 4096 || $height > 4096) {
            return ['valid' => false, 'message' => 'Image trop grande (max 4096x4096px)'];
        }
        
        return ['valid' => true, 'dimensions' => [$width, $height], 'mime' => $detectedMimeType];
    }
    
    /**
     * Générer un nom de fichier sécurisé
     */
    public static function generateSecureFilename(string $originalName): string
    {
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $timestamp = time();
        $randomString = bin2hex(random_bytes(8));
        
        return "{$timestamp}_{$randomString}.{$extension}";
    }
    
    /**
     * Vérifier si une chaîne contient du contenu malveillant
     */
    public static function containsMaliciousContent(string $content): bool
    {
        $maliciousPatterns = [
            '/<script[^>]*>.*?<\/script>/is',
            '/javascript:/i',
            '/on\w+\s*=/i',
            '/<iframe[^>]*>.*?<\/iframe>/is',
            '/<object[^>]*>.*?<\/object>/is',
            '/<embed[^>]*>/i',
            '/<link[^>]*>/i',
            '/<meta[^>]*>/i'
        ];
        
        foreach ($maliciousPatterns as $pattern) {
            if (preg_match($pattern, $content)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Nettoyer le contenu HTML tout en préservant les balises autorisées
     */
    public static function sanitizeHtml(string $content, array $allowedTags = null): string
    {
        if ($allowedTags === null) {
            $allowedTags = [
                'p', 'br', 'strong', 'em', 'u', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
                'ul', 'ol', 'li', 'a', 'img', 'blockquote', 'code', 'pre'
            ];
        }
        
        // Supprimer les balises non autorisées
        $content = strip_tags($content, '<' . implode('><', $allowedTags) . '>');
        
        // Nettoyer les attributs des balises restantes
        $content = preg_replace_callback('/<([a-z]+)[^>]*>/i', function($matches) {
            $tag = $matches[1];
            $attributes = [];
            
            // Extraire les attributs autorisés selon la balise
            if ($tag === 'a') {
                if (preg_match('/href\s*=\s*["\']([^"\']*)["\']/', $matches[0], $hrefMatches)) {
                    $url = $hrefMatches[1];
                    if (self::validateUrl($url) || strpos($url, '/') === 0) {
                        $attributes[] = 'href="' . self::escapeAttr($url) . '"';
                    }
                }
            } elseif ($tag === 'img') {
                if (preg_match('/src\s*=\s*["\']([^"\']*)["\']/', $matches[0], $srcMatches)) {
                    $src = $srcMatches[1];
                    if (self::validateUrl($src) || strpos($src, '/') === 0) {
                        $attributes[] = 'src="' . self::escapeAttr($src) . '"';
                    }
                }
                if (preg_match('/alt\s*=\s*["\']([^"\']*)["\']/', $matches[0], $altMatches)) {
                    $attributes[] = 'alt="' . self::escapeAttr($altMatches[1]) . '"';
                }
            }
            
            return '<' . $tag . (!empty($attributes) ? ' ' . implode(' ', $attributes) : '') . '>';
        }, $content);
        
        return $content;
    }
}
