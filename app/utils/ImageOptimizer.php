<?php

/**
 * Optimiseur d'images automatique
 * Conversion WebP + redimensionnement + compression
 */
class ImageOptimizer
{
    // Configuration d'optimisation
    private const MAX_WIDTH = 1920;
    private const MAX_HEIGHT = 1080;
    private const WEBP_QUALITY_ORIGINAL = 75;  // Qualité optimale pour WebP
    private const WEBP_QUALITY_THUMBNAIL = 70; // Qualité pour les miniatures
    private const JPG_QUALITY_FALLBACK = 75;   // Qualité optimale pour JPG
    
    // Formats supportés
    private const SUPPORTED_FORMATS = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    
    // Extensions de sortie
    private const OUTPUT_EXTENSIONS = ['webp', 'jpg'];
    
    // Support WebP
    private static ?bool $webpSupported = null;
    
    /**
     * Optimise une image et crée plusieurs versions
     */
    public static function optimizeImage(string $sourcePath, string $outputDir): array
    {
        try {
            // Vérifier que l'image existe
            if (!file_exists($sourcePath)) {
                throw new Exception("Image source introuvable: {$sourcePath}");
            }
            
            // Vérifier l'extension GD
            if (!extension_loaded('gd')) {
                throw new Exception("Extension GD requise pour l'optimisation");
            }
            
            // Obtenir les informations de l'image
            $imageInfo = getimagesize($sourcePath);
            if (!$imageInfo) {
                throw new Exception("Impossible de lire l'image source");
            }
            
            // Debug: afficher la structure complète de $imageInfo
            error_log("DEBUG ImageOptimizer - Structure imageInfo: " . print_r($imageInfo, true));
            
            // Méthode simplifiée et plus robuste pour obtenir le type MIME
            $mimeType = self::getMimeTypeFromImageInfo($imageInfo);
            $originalWidth = $imageInfo[0] ?? 0;
            $originalHeight = $imageInfo[1] ?? 0;
            
            // Vérifier que les informations sont valides
            if (!$mimeType || !$originalWidth || !$originalHeight) {
                throw new Exception("Informations d'image invalides: type={$mimeType}, largeur={$originalWidth}, hauteur={$originalHeight}. Structure: " . print_r($imageInfo, true));
            }
            
            // Vérifier le format supporté
            if (!in_array($mimeType, self::SUPPORTED_FORMATS)) {
                throw new Exception("Format d'image non supporté: {$mimeType}");
            }
            
            // Créer le nom de base pour les fichiers de sortie
            $baseName = pathinfo($sourcePath, PATHINFO_FILENAME);
            $outputFiles = [];
            
            // 1. Créer la version WebP optimisée (originale) si supporté
            if (self::isWebPSupported()) {
                $webpPath = self::createOptimizedWebP($sourcePath, $outputDir, $baseName, $originalWidth, $originalHeight);
                if ($webpPath) {
                    $outputFiles['webp'] = $webpPath;
                }
            } else {
                error_log("ImageOptimizer::optimizeImage() - Support WebP non disponible, utilisation JPG uniquement");
            }
            
            // 2. Créer la version JPG de fallback (originale)
            $jpgPath = self::createOptimizedJPG($sourcePath, $outputDir, $baseName, $originalWidth, $originalHeight);
            if ($jpgPath) {
                $outputFiles['jpg'] = $jpgPath;
            }
            
            // 3. Créer les thumbnails optimisés - DÉSACTIVÉ pour éviter la surcharge
            // $thumbnails = self::createOptimizedThumbnails($sourcePath, $outputDir, $baseName);
            // $outputFiles['thumbnails'] = $thumbnails;
            
            // 4. Calculer les statistiques d'optimisation
            $originalSize = filesize($sourcePath);
            $optimizedPath = $webpPath ?? $jpgPath ?? $sourcePath;
            $optimizedSize = filesize($optimizedPath);
            $compressionRatio = round((1 - ($optimizedSize / $originalSize)) * 100, 1);
            
            return [
                'success' => true,
                'files' => $outputFiles,
                'original_size' => $originalSize,
                'optimized_size' => $optimizedSize,
                'compression_ratio' => $compressionRatio,
                'formats' => array_keys($outputFiles)
            ];
            
        } catch (Exception $e) {
            error_log("ImageOptimizer::optimizeImage() - Erreur: " . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'files' => []
            ];
        }
    }
    
    /**
     * Méthode robuste pour obtenir le type MIME depuis getimagesize()
     */
    private static function getMimeTypeFromImageInfo(array $imageInfo): ?string
    {
        // Méthode 1: Clé 'mime' (standard)
        if (isset($imageInfo['mime']) && !empty($imageInfo['mime'])) {
            return $imageInfo['mime'];
        }
        
        // Méthode 2: Index 2 (type d'image) - plus fiable
        if (isset($imageInfo[2]) && is_int($imageInfo[2])) {
            $mimeTypes = [
                IMAGETYPE_GIF => 'image/gif',
                IMAGETYPE_JPEG => 'image/jpeg',
                IMAGETYPE_PNG => 'image/png',
                IMAGETYPE_WEBP => 'image/webp',
                IMAGETYPE_BMP => 'image/bmp',
                IMAGETYPE_TIFF_II => 'image/tiff',
                IMAGETYPE_TIFF_MM => 'image/tiff'
            ];
            
            if (isset($mimeTypes[$imageInfo[2]])) {
                return $mimeTypes[$imageInfo[2]];
            }
        }
        
        // Méthode 3: Autres clés possibles
        $possibleKeys = ['mime_type', 'mime-type', 'type'];
        foreach ($possibleKeys as $key) {
            if (isset($imageInfo[$key]) && !empty($imageInfo[$key])) {
                return $imageInfo[$key];
            }
        }
        
        // Méthode 4: Fallback avec mime_content_type()
        if (isset($imageInfo[0]) && isset($imageInfo[1])) {
            // Si on a au moins les dimensions, essayer mime_content_type
            // Mais on ne peut pas l'utiliser ici car on n'a pas le chemin du fichier
            // Cette méthode sera utilisée dans createImageFromPath()
        }
        
        return null;
    }
    
    /**
     * Vérifie si le support WebP est disponible
     */
    private static function isWebPSupported(): bool
    {
        if (self::$webpSupported === null) {
            self::$webpSupported = extension_loaded('gd') && function_exists('imagewebp');
        }
        return self::$webpSupported;
    }
    
    /**
     * Crée une version WebP optimisée
     */
     private static function createOptimizedWebP(string $sourcePath, string $outputDir, string $baseName, int $width, int $height): ?string
     {
         try {
             // Créer l'image source
             $sourceImage = self::createImageFromPath($sourcePath);
             if (!$sourceImage) {
                 return null;
             }
             
             // Redimensionner si nécessaire
             $resizedImage = self::resizeImageIfNeeded($sourceImage, $width, $height);
             
             // Convertir en mode RVB si c'est une image palette (pour compatibilité WebP)
             $rgbImage = self::convertToRGB($resizedImage);
             
             // Chemin de sortie
             $outputPath = $outputDir . '/' . $baseName . '_optimized.webp';
             
             // Convertir en WebP avec compression
             $success = imagewebp($rgbImage, $outputPath, self::WEBP_QUALITY_ORIGINAL);
             
             // Nettoyer la mémoire
             imagedestroy($sourceImage);
             if ($resizedImage !== $sourceImage) {
                 imagedestroy($resizedImage);
             }
             if ($rgbImage !== $resizedImage) {
                 imagedestroy($rgbImage);
             }
             
             return $success ? $outputPath : null;
             
         } catch (Exception $e) {
             error_log("Erreur création WebP: " . $e->getMessage());
             return null;
         }
     }
    
    /**
     * Crée une version JPG optimisée (fallback)
     */
    private static function createOptimizedJPG(string $sourcePath, string $outputDir, string $baseName, int $width, int $height): ?string
    {
        try {
            // Créer l'image source
            $sourceImage = self::createImageFromPath($sourcePath);
            if (!$sourceImage) {
                return null;
            }
            
            // Redimensionner si nécessaire
            $resizedImage = self::resizeImageIfNeeded($sourceImage, $width, $height);
            
            // Chemin de sortie
            $outputPath = $outputDir . '/' . $baseName . '_optimized.jpg';
            
            // Convertir en JPG avec compression
            $success = imagejpeg($resizedImage, $outputPath, self::JPG_QUALITY_FALLBACK);
            
            // Nettoyer la mémoire
            imagedestroy($sourceImage);
            if ($resizedImage !== $sourceImage) {
                imagedestroy($resizedImage);
            }
            
            return $success ? $outputPath : null;
            
        } catch (Exception $e) {
            error_log("Erreur création JPG: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Crée des thumbnails optimisés
     */
    private static function createOptimizedThumbnails(string $sourcePath, string $outputDir, string $baseName): array
    {
        $thumbnails = [];
        $sizes = [
            'small' => [300, 200],
            'medium' => [600, 400],
            'large' => [1200, 800]
        ];
        
        try {
            $sourceImage = self::createImageFromPath($sourcePath);
            if (!$sourceImage) {
                return $thumbnails;
            }
            
                         foreach ($sizes as $size => $dimensions) {
                 [$maxWidth, $maxHeight] = $dimensions;
                 
                 // Créer le thumbnail
                 $thumbnail = self::createThumbnail($sourceImage, $maxWidth, $maxHeight);
                 
                 // Sauvegarder en WebP si supporté
                 if (self::isWebPSupported()) {
                     // Convertir en RVB pour compatibilité WebP
                     $rgbThumbnail = self::convertToRGB($thumbnail);
                     
                     $webpPath = $outputDir . '/' . $baseName . "_{$size}.webp";
                     if (imagewebp($rgbThumbnail, $webpPath, self::WEBP_QUALITY_THUMBNAIL)) {
                         $thumbnails[$size]['webp'] = $webpPath;
                     }
                     
                     if ($rgbThumbnail !== $thumbnail) {
                         imagedestroy($rgbThumbnail);
                     }
                 }
                 
                 // Sauvegarder en JPG (fallback)
                 $jpgPath = $outputDir . '/' . $baseName . "_{$size}.jpg";
                 if (imagejpeg($thumbnail, $jpgPath, self::JPG_QUALITY_FALLBACK)) {
                     $thumbnails[$size]['jpg'] = $jpgPath;
                 }
                 
                 imagedestroy($thumbnail);
             }
            
            imagedestroy($sourceImage);
            
        } catch (Exception $e) {
            error_log("Erreur création thumbnails: " . $e->getMessage());
        }
        
        return $thumbnails;
    }
    
    /**
     * Crée une image depuis un chemin de fichier
     */
    private static function createImageFromPath(string $path): mixed
    {
        // Méthode 1: Utiliser mime_content_type()
        $mimeType = mime_content_type($path);
        
        // Méthode 2: Fallback avec getimagesize() si mime_content_type échoue
        if (!$mimeType || !str_starts_with($mimeType, 'image/')) {
            $imageInfo = getimagesize($path);
            if ($imageInfo) {
                $mimeType = self::getMimeTypeFromImageInfo($imageInfo);
            }
        }
        
        // Méthode 3: Fallback avec l'extension du fichier
        if (!$mimeType || !str_starts_with($mimeType, 'image/')) {
            $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            $extensionMap = [
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
                'webp' => 'image/webp',
                'bmp' => 'image/bmp'
            ];
            $mimeType = $extensionMap[$extension] ?? null;
        }
        
        if (!$mimeType) {
            error_log("ImageOptimizer::createImageFromPath() - Impossible de déterminer le type MIME pour: {$path}");
            return null;
        }
        
        try {
            switch ($mimeType) {
                case 'image/jpeg':
                    return imagecreatefromjpeg($path);
                case 'image/png':
                    // Désactiver les avertissements pour les profils sRGB problématiques
                    $oldErrorReporting = error_reporting();
                    error_reporting($oldErrorReporting & ~E_WARNING);
                    $image = imagecreatefrompng($path);
                    error_reporting($oldErrorReporting);
                    return $image;
                case 'image/webp':
                    return imagecreatefromwebp($path);
                case 'image/gif':
                    return imagecreatefromgif($path);
                case 'image/bmp':
                    return imagecreatefrombmp($path);
                default:
                    error_log("ImageOptimizer::createImageFromPath() - Type MIME non supporté: {$mimeType}");
                    return null;
            }
        } catch (Exception $e) {
            error_log("ImageOptimizer::createImageFromPath() - Erreur lors de la création de l'image: " . $e->getMessage());
            return null;
        }
    }
    
         /**
      * Redimensionne l'image si elle dépasse les dimensions maximales
      */
     private static function resizeImageIfNeeded($image, int $width, int $height): mixed
     {
         // Vérifier si le redimensionnement est nécessaire
         if ($width <= self::MAX_WIDTH && $height <= self::MAX_HEIGHT) {
             return $image;
         }
         
         // Calculer les nouvelles dimensions
         $ratio = min(self::MAX_WIDTH / $width, self::MAX_HEIGHT / $height);
         $newWidth = round($width * $ratio);
         $newHeight = round($height * $ratio);
         
         // Créer la nouvelle image
         $newImage = imagecreatetruecolor($newWidth, $newHeight);
         
         // Préserver la transparence pour PNG et WebP
         imagealphablending($newImage, false);
         imagesavealpha($newImage, true);
         $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
         imagefilledrectangle($newImage, 0, 0, $newWidth, $newHeight, $transparent);
         
         // Redimensionner avec interpolation de haute qualité
         imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
         
         return $newImage;
     }
    
         /**
      * Crée un thumbnail avec les dimensions spécifiées
      */
     private static function createThumbnail($sourceImage, int $maxWidth, int $maxHeight): mixed
     {
         $sourceWidth = imagesx($sourceImage);
         $sourceHeight = imagesy($sourceImage);
         
         // Calculer les dimensions du thumbnail
         $ratio = min($maxWidth / $sourceWidth, $maxHeight / $sourceHeight);
         $thumbWidth = round($sourceWidth * $ratio);
         $thumbHeight = round($sourceHeight * $ratio);
         
         // Créer le thumbnail
         $thumbnail = imagecreatetruecolor($thumbWidth, $thumbHeight);
         
         // Préserver la transparence
         imagealphablending($thumbnail, false);
         imagesavealpha($thumbnail, true);
         $transparent = imagecolorallocatealpha($thumbnail, 255, 255, 255, 127);
         imagefilledrectangle($thumbnail, 0, 0, $thumbWidth, $thumbHeight, $transparent);
         
         // Copier et redimensionner
         imagecopyresampled($thumbnail, $sourceImage, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $sourceWidth, $sourceHeight);
         
         return $thumbnail;
     }
     
     /**
      * Convertit une image palette en mode RVB pour compatibilité WebP
      */
     private static function convertToRGB($image): mixed
     {
         // Vérifier si l'image est en mode palette
         if (imageistruecolor($image)) {
             // L'image est déjà en RVB, pas besoin de conversion
             return $image;
         }
         
         // Obtenir les dimensions
         $width = imagesx($image);
         $height = imagesy($image);
         
         // Créer une nouvelle image en RVB
         $rgbImage = imagecreatetruecolor($width, $height);
         
         // Préserver la transparence
         imagealphablending($rgbImage, false);
         imagesavealpha($rgbImage, true);
         
         // Copier l'image avec conversion automatique
         imagecopy($rgbImage, $image, 0, 0, 0, 0, $width, $height);
         
         return $rgbImage;
     }
    
    /**
     * Nettoie les fichiers temporaires
     */
    public static function cleanupTempFiles(array $files): void
    {
        foreach ($files as $file) {
            if (is_string($file) && file_exists($file)) {
                unlink($file);
            } elseif (is_array($file)) {
                self::cleanupTempFiles($file);
            }
        }
    }
    
    /**
     * Obtient les statistiques d'optimisation
     */
    public static function getOptimizationStats(string $originalPath, string $optimizedPath): array
    {
        if (!file_exists($originalPath) || !file_exists($optimizedPath)) {
            return ['error' => 'Fichiers non trouvés'];
        }
        
        $originalSize = filesize($originalPath);
        $optimizedSize = filesize($optimizedPath);
        $savings = $originalSize - $optimizedSize;
        $compressionRatio = round((1 - ($optimizedSize / $originalSize)) * 100, 1);
        
        return [
            'original_size' => $originalSize,
            'optimized_size' => $optimizedSize,
            'savings_bytes' => $savings,
            'savings_mb' => round($savings / 1024 / 1024, 2),
            'compression_ratio' => $compressionRatio
        ];
    }
}
