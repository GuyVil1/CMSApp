<?php
/**
 * Helper pour la gestion des images
 */

class ImageHelper {
    
    /**
     * Nettoie et corrige les chemins d'images
     */
    public static function cleanImagePath(string $imagePath): string {
        // Supprimer les préfixes incorrects
        $cleanPath = $imagePath;
        
        // Supprimer http://localhost si présent
        $cleanPath = preg_replace('/^https?:\/\/[^\/]+\/public\//', '', $cleanPath);
        
        // Supprimer /public/ si présent au début
        $cleanPath = preg_replace('/^\/public\//', '', $cleanPath);
        
        // Si c'est un appel à uploads.php, extraire le paramètre file
        if (strpos($cleanPath, 'uploads.php?file=') !== false) {
            $cleanPath = urldecode(str_replace('uploads.php?file=', '', $cleanPath));
        }
        
        return $cleanPath;
    }
    
    /**
     * Génère l'URL correcte pour une image
     */
    public static function getImageUrl(string $imagePath): string {
        $cleanPath = self::cleanImagePath($imagePath);
        
        // Vérifier si le fichier existe
        $fullPath = __DIR__ . '/../../public/uploads/' . $cleanPath;
        
        if (file_exists($fullPath)) {
            return "/public/uploads.php?file=" . urlencode($cleanPath);
        }
        
        // Essayer de trouver le fichier thumbnail
        $pathParts = explode('/', $cleanPath);
        $filename = array_pop($pathParts);
        $thumbPath = implode('/', $pathParts) . '/thumb_' . $filename;
        $thumbFullPath = __DIR__ . '/../../public/uploads/' . $thumbPath;
        
        if (file_exists($thumbFullPath)) {
            return "/public/uploads.php?file=" . urlencode($thumbPath);
        }
        
        // Essayer de trouver le fichier dans le dossier parent
        $parentDir = implode('/', $pathParts);
        $parentFullPath = __DIR__ . '/../../public/uploads/' . $parentDir;
        
        if (is_dir($parentFullPath)) {
            $files = scandir($parentFullPath);
            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..' && strpos($file, $filename) !== false) {
                    $foundPath = $parentDir . '/' . $file;
                    return "/public/uploads.php?file=" . urlencode($foundPath);
                }
            }
        }
        
        // Retourner le chemin original si rien n'est trouvé
        return "/public/uploads.php?file=" . urlencode($cleanPath);
    }
    
    /**
     * Nettoie le contenu HTML d'un article
     */
    public static function cleanArticleContent(string $content): string {
        // Rechercher et remplacer tous les chemins d'images
        $patterns = [
            '/src=["\']([^"\']*\.(jpg|jpeg|png|gif|webp))["\']/i',
            '/background-image:\s*url\(["\']?([^"\')\s]+\.(jpg|jpeg|png|gif|webp))["\']?\)/i'
        ];
        
        foreach ($patterns as $pattern) {
            $content = preg_replace_callback($pattern, function($matches) {
                $imagePath = $matches[1];
                $correctUrl = self::getImageUrl($imagePath);
                return str_replace($imagePath, $correctUrl, $matches[0]);
            }, $content);
        }
        
        return $content;
    }
    
    /**
     * Vérifie si une image existe
     */
    public static function imageExists(string $imagePath): bool {
        $cleanPath = self::cleanImagePath($imagePath);
        $fullPath = __DIR__ . '/../../public/uploads/' . $cleanPath;
        
        if (file_exists($fullPath)) {
            return true;
        }
        
        // Essayer thumbnail
        $pathParts = explode('/', $cleanPath);
        $filename = array_pop($pathParts);
        $thumbPath = implode('/', $pathParts) . '/thumb_' . $filename;
        $thumbFullPath = __DIR__ . '/../../public/uploads/' . $thumbPath;
        
        return file_exists($thumbFullPath);
    }
}
