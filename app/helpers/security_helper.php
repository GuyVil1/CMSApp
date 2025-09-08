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
     * Extraire la valeur d'un attribut HTML en gérant les guillemets imbriqués
     */
    public static function extractAttributeValue(string $html, string $attributeName): ?string
    {
        // Pattern simplifié pour trouver l'attribut
        $pattern = '/' . preg_quote($attributeName, '/') . '\s*=\s*(["\'])(.*?)(\1)/';
        
        if (preg_match($pattern, $html, $matches)) {
            return $matches[2];
        }
        
        return null;
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
        // Vérifier d'abord les patterns malveillants généraux
        $maliciousPatterns = [
            '/<script[^>]*>.*?<\/script>/is',
            '/javascript:/i',
            '/<object[^>]*>.*?<\/object>/is',
            '/<embed[^>]*>/i',
            '/<meta[^>]*>/i'
        ];
        
        foreach ($maliciousPatterns as $pattern) {
            if (preg_match($pattern, $content)) {
                return true;
            }
        }
        
        // Vérifier les iframes - autoriser seulement YouTube et Vimeo
        if (preg_match('/<iframe[^>]*>.*?<\/iframe>/is', $content)) {
            // Extraire tous les iframes
            preg_match_all('/<iframe[^>]*src=["\']([^"\']*)["\'][^>]*>.*?<\/iframe>/is', $content, $matches);
            
            foreach ($matches[1] as $src) {
                // Autoriser seulement YouTube et Vimeo
                if (!preg_match('/^(https?:\/\/)?(www\.)?(youtube\.com\/embed\/|youtu\.be\/|player\.vimeo\.com\/video\/)/i', $src)) {
                    return true; // iframe non autorisé
                }
            }
        }
        
        return false;
    }
    
    /**
     * Nettoyer les iframes pour autoriser seulement YouTube et Vimeo
     */
    private static function sanitizeIframes(string $content): string
    {
        return preg_replace_callback('/<iframe[^>]*>.*?<\/iframe>/is', function($matches) {
            $iframe = $matches[0];
            
            // Extraire l'attribut src
            if (preg_match('/src\s*=\s*["\']([^"\']*)["\']/', $iframe, $srcMatches)) {
                $src = $srcMatches[1];
                
                // Vérifier si c'est YouTube ou Vimeo
                if (preg_match('/^(https?:\/\/)?(www\.)?(youtube\.com\/embed\/|youtu\.be\/|player\.vimeo\.com\/video\/)/i', $src)) {
                    // Nettoyer l'iframe en gardant seulement les attributs autorisés
                    $cleanIframe = '<iframe';
                    
                    // Attributs autorisés pour les iframes vidéo
                    $allowedAttrs = ['src', 'width', 'height', 'frameborder', 'allow', 'allowfullscreen'];
                    
                    foreach ($allowedAttrs as $attr) {
                        if (preg_match('/' . $attr . '\s*=\s*["\']([^"\']*)["\']/', $iframe, $attrMatches)) {
                            $cleanIframe .= ' ' . $attr . '="' . self::escapeAttr($attrMatches[1]) . '"';
                        }
                    }
                    
                    $cleanIframe .= '></iframe>';
                    return $cleanIframe;
                }
            }
            
            // Si ce n'est pas YouTube/Vimeo, supprimer l'iframe
            return '';
        }, $content);
    }
    
    /**
     * Nettoyer le contenu HTML tout en préservant les balises autorisées
     */
    public static function sanitizeHtml(string $content, array $allowedTags = null): string
    {
        if ($allowedTags === null) {
            $allowedTags = [
                'p', 'br', 'strong', 'em', 'u', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
                'ul', 'ol', 'li', 'a', 'img', 'blockquote', 'code', 'pre', 'iframe', 'div', 'link', 'button', 'span'
            ];
        }
        
        // Traitement spécial pour les iframes YouTube/Vimeo
        $content = self::sanitizeIframes($content);
        
        // Supprimer les balises non autorisées d'abord (préserve le contenu)
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
            } elseif ($tag === 'link') {
                if (preg_match('/href\s*=\s*["\']([^"\']*)["\']/', $matches[0], $hrefMatches)) {
                    $url = $hrefMatches[1];
                    if (self::validateUrl($url) || strpos($url, '/') === 0) {
                        $attributes[] = 'href="' . self::escapeAttr($url) . '"';
                    }
                }
                if (preg_match('/rel\s*=\s*["\']([^"\']*)["\']/', $matches[0], $relMatches)) {
                    $attributes[] = 'rel="' . self::escapeAttr($relMatches[1]) . '"';
                }
                if (preg_match('/type\s*=\s*["\']([^"\']*)["\']/', $matches[0], $typeMatches)) {
                    $attributes[] = 'type="' . self::escapeAttr($typeMatches[1]) . '"';
                }
            } elseif ($tag === 'button') {
                $onclickValue = self::extractAttributeValue($matches[0], 'onclick');
                if ($onclickValue !== null) {
                    $attributes[] = 'onclick="' . self::escapeAttr($onclickValue) . '"';
                }
                if (preg_match('/type\s*=\s*["\']([^"\']*)["\']/', $matches[0], $typeMatches)) {
                    $attributes[] = 'type="' . self::escapeAttr($typeMatches[1]) . '"';
                }
                if (preg_match('/class\s*=\s*["\']([^"\']*)["\']/', $matches[0], $classMatches)) {
                    $attributes[] = 'class="' . self::escapeAttr($classMatches[1]) . '"';
                }
                if (preg_match('/style\s*=\s*["\']([^"\']*)["\']/', $matches[0], $styleMatches)) {
                    $attributes[] = 'style="' . self::escapeAttr($styleMatches[1]) . '"';
                }
            } elseif ($tag === 'span') {
                $onclickValue = self::extractAttributeValue($matches[0], 'onclick');
                if ($onclickValue !== null) {
                    $attributes[] = 'onclick="' . self::escapeAttr($onclickValue) . '"';
                }
                if (preg_match('/class\s*=\s*["\']([^"\']*)["\']/', $matches[0], $classMatches)) {
                    $attributes[] = 'class="' . self::escapeAttr($classMatches[1]) . '"';
                }
                if (preg_match('/style\s*=\s*["\']([^"\']*)["\']/', $matches[0], $styleMatches)) {
                    $attributes[] = 'style="' . self::escapeAttr($styleMatches[1]) . '"';
                }
            } elseif ($tag === 'div') {
                // Support complet pour les divs modulaires
                if (preg_match('/class\s*=\s*["\']([^"\']*)["\']/', $matches[0], $classMatches)) {
                    $attributes[] = 'class="' . self::escapeAttr($classMatches[1]) . '"';
                }
                if (preg_match('/style\s*=\s*["\']([^"\']*)["\']/', $matches[0], $styleMatches)) {
                    $attributes[] = 'style="' . self::escapeAttr($styleMatches[1]) . '"';
                }
                // Attributs data-* pour les modules
                if (preg_match('/data-module-id\s*=\s*["\']([^"\']*)["\']/', $matches[0], $dataMatches)) {
                    $attributes[] = 'data-module-id="' . self::escapeAttr($dataMatches[1]) . '"';
                }
                if (preg_match('/data-module-type\s*=\s*["\']([^"\']*)["\']/', $matches[0], $dataMatches)) {
                    $attributes[] = 'data-module-type="' . self::escapeAttr($dataMatches[1]) . '"';
                }
                if (preg_match('/data-columns\s*=\s*["\']([^"\']*)["\']/', $matches[0], $dataMatches)) {
                    $attributes[] = 'data-columns="' . self::escapeAttr($dataMatches[1]) . '"';
                }
                if (preg_match('/data-section-id\s*=\s*["\']([^"\']*)["\']/', $matches[0], $dataMatches)) {
                    $attributes[] = 'data-section-id="' . self::escapeAttr($dataMatches[1]) . '"';
                }
                if (preg_match('/data-column\s*=\s*["\']([^"\']*)["\']/', $matches[0], $dataMatches)) {
                    $attributes[] = 'data-column="' . self::escapeAttr($dataMatches[1]) . '"';
                }
                if (preg_match('/data-module-data\s*=\s*["\']([^"\']*)["\']/', $matches[0], $dataMatches)) {
                    $attributes[] = 'data-module-data="' . self::escapeAttr($dataMatches[1]) . '"';
                }
            } elseif ($tag === 'h1' || $tag === 'h2' || $tag === 'h3' || $tag === 'h4' || $tag === 'h5' || $tag === 'h6') {
                // Support pour les titres modulaires
                if (preg_match('/class\s*=\s*["\']([^"\']*)["\']/', $matches[0], $classMatches)) {
                    $attributes[] = 'class="' . self::escapeAttr($classMatches[1]) . '"';
                }
                if (preg_match('/style\s*=\s*["\']([^"\']*)["\']/', $matches[0], $styleMatches)) {
                    $attributes[] = 'style="' . self::escapeAttr($styleMatches[1]) . '"';
                }
            } elseif ($tag === 'iframe') {
                // Support pour les iframes (vidéos)
                if (preg_match('/src\s*=\s*["\']([^"\']*)["\']/', $matches[0], $srcMatches)) {
                    $src = $srcMatches[1];
                    if (self::validateUrl($src) || strpos($src, '/') === 0) {
                        $attributes[] = 'src="' . self::escapeAttr($src) . '"';
                    }
                }
                if (preg_match('/frameborder\s*=\s*["\']([^"\']*)["\']/', $matches[0], $frameMatches)) {
                    $attributes[] = 'frameborder="' . self::escapeAttr($frameMatches[1]) . '"';
                }
                if (preg_match('/allowfullscreen/', $matches[0])) {
                    $attributes[] = 'allowfullscreen';
                }
                if (preg_match('/title\s*=\s*["\']([^"\']*)["\']/', $matches[0], $titleMatches)) {
                    $attributes[] = 'title="' . self::escapeAttr($titleMatches[1]) . '"';
                }
            }
            
            return '<' . $tag . (!empty($attributes) ? ' ' . implode(' ', $attributes) : '') . '>';
        }, $content);
        
        return $content;
    }
}
