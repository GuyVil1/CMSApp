<?php
declare(strict_types=1);

/**
 * Helper SEO
 * Gestion des meta tags, sitemap, et optimisations SEO
 */

class SeoHelper
{
    /**
     * Générer les meta tags pour une page
     */
    public static function generateMetaTags(array $data = []): string
    {
        $title = $data['title'] ?? 'Belgium Video Gaming - Actualité Gaming Belgique';
        $description = $data['description'] ?? 'On joue, on observe, on t\'éclaire. Pas de pub, pas de langue de bois — juste notre regard de passionnés, pour affiner le tien.';
        $keywords = $data['keywords'] ?? 'gaming, jeux vidéo, belgique, actualité, tests, dossiers, passionnés';
        $url = $data['url'] ?? 'https://belgium-video-gaming.be';
        $image = $data['image'] ?? '/public/assets/images/default-featured.jpg';
        $type = $data['type'] ?? 'website';
        
        // Limiter la description à 160 caractères (recommandation Google)
        $description = self::truncateText($description, 160);
        
        $metaTags = "
        <!-- Meta tags SEO -->
        <title>{$title}</title>
        <meta name=\"description\" content=\"{$description}\">
        <meta name=\"keywords\" content=\"{$keywords}\">
        <meta name=\"author\" content=\"GameNews\">
        <meta name=\"robots\" content=\"index, follow\">
        
        <!-- Open Graph / Facebook -->
        <meta property=\"og:type\" content=\"{$type}\">
        <meta property=\"og:url\" content=\"{$url}\">
        <meta property=\"og:title\" content=\"{$title}\">
        <meta property=\"og:description\" content=\"{$description}\">
        <meta property=\"og:image\" content=\"{$image}\">
        <meta property=\"og:site_name\" content=\"Belgium Video Gaming\">
        
        <!-- Twitter -->
        <meta property=\"twitter:card\" content=\"summary_large_image\">
        <meta property=\"twitter:url\" content=\"{$url}\">
        <meta property=\"twitter:title\" content=\"{$title}\">
        <meta property=\"twitter:description\" content=\"{$description}\">
        <meta property=\"twitter:image\" content=\"{$image}\">
        
        <!-- Canonical URL -->
        <link rel=\"canonical\" href=\"{$url}\">
        ";
        
        return $metaTags;
    }
    
    /**
     * Générer les meta tags pour un article
     */
    public static function generateArticleMetaTags($article, $baseUrl = 'https://belgium-video-gaming.be'): string
    {
        $title = htmlspecialchars($article->getTitle());
        $description = $article->getExcerpt() ? 
            htmlspecialchars($article->getExcerpt()) : 
            self::generateExcerptFromContent($article->getContent());
        
        $url = $baseUrl . '/article/' . $article->getSlug();
        $image = $article->getCoverImageId() ? 
            $baseUrl . '/public/uploads.php?file=article/' . $article->getCoverImageId() : 
            $baseUrl . '/public/assets/images/default-featured.jpg';
        
        // Ajouter des mots-clés basés sur la catégorie et le contenu
        $keywords = self::generateKeywords($article);
        
        return self::generateMetaTags([
            'title' => $title . ' - Belgium Video Gaming',
            'description' => $description,
            'keywords' => $keywords,
            'url' => $url,
            'image' => $image,
            'type' => 'article'
        ]);
    }
    
    /**
     * Générer un excerpt à partir du contenu si aucun excerpt n'est défini
     */
    public static function generateExcerptFromContent(string $content, int $length = 160): string
    {
        // Supprimer les balises HTML
        $text = strip_tags($content);
        
        // Supprimer les espaces multiples
        $text = preg_replace('/\s+/', ' ', $text);
        
        // Tronquer à la longueur souhaitée
        return self::truncateText(trim($text), $length);
    }
    
    /**
     * Tronquer un texte à une longueur donnée
     */
    public static function truncateText(string $text, int $length): string
    {
        if (strlen($text) <= $length) {
            return $text;
        }
        
        // Tronquer au dernier espace avant la limite
        $truncated = substr($text, 0, $length);
        $lastSpace = strrpos($truncated, ' ');
        
        if ($lastSpace !== false) {
            $truncated = substr($truncated, 0, $lastSpace);
        }
        
        return $truncated . '...';
    }
    
    /**
     * Générer des mots-clés pour un article
     */
    public static function generateKeywords($article): string
    {
        $keywords = ['gaming', 'jeux vidéo', 'belgique'];
        
        // Ajouter des mots-clés basés sur la catégorie
        if ($article->getCategoryId()) {
            // TODO: Récupérer le nom de la catégorie
            $keywords[] = 'actualité gaming';
        }
        
        // Ajouter des mots-clés basés sur le titre
        $titleWords = explode(' ', strtolower($article->getTitle()));
        foreach ($titleWords as $word) {
            if (strlen($word) > 3 && !in_array($word, ['le', 'la', 'les', 'un', 'une', 'des', 'du', 'de', 'et', 'ou', 'à', 'sur', 'dans', 'pour', 'avec'])) {
                $keywords[] = $word;
            }
        }
        
        return implode(', ', array_unique($keywords));
    }
    
    /**
     * Générer le sitemap XML
     */
    public static function generateSitemap(): string
    {
        $baseUrl = 'https://belgium-video-gaming.be';
        $sitemap = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        
        // Page d'accueil
        $sitemap .= self::generateSitemapUrl($baseUrl, '1.0', 'daily');
        
        // Articles publiés
        $articles = self::getPublishedArticles();
        foreach ($articles as $article) {
            $url = $baseUrl . '/article/' . $article['slug'];
            $lastmod = date('Y-m-d', strtotime($article['updated_at']));
            $sitemap .= self::generateSitemapUrl($url, '0.8', 'weekly', $lastmod);
        }
        
        // Jeux
        $games = self::getPublishedGames();
        foreach ($games as $game) {
            $url = $baseUrl . '/game/' . $game['slug'];
            $lastmod = date('Y-m-d', strtotime($game['updated_at']));
            $sitemap .= self::generateSitemapUrl($url, '0.7', 'monthly', $lastmod);
        }
        
        // Catégories
        $categories = self::getCategories();
        foreach ($categories as $category) {
            $url = $baseUrl . '/category/' . $category['slug'];
            $sitemap .= self::generateSitemapUrl($url, '0.6', 'monthly');
        }
        
        $sitemap .= '</urlset>';
        
        return $sitemap;
    }
    
    /**
     * Générer une entrée URL pour le sitemap
     */
    private static function generateSitemapUrl(string $url, string $priority, string $changefreq, string $lastmod = null): string
    {
        $entry = "  <url>\n";
        $entry .= "    <loc>{$url}</loc>\n";
        $entry .= "    <priority>{$priority}</priority>\n";
        $entry .= "    <changefreq>{$changefreq}</changefreq>\n";
        
        if ($lastmod) {
            $entry .= "    <lastmod>{$lastmod}</lastmod>\n";
        }
        
        $entry .= "  </url>\n";
        
        return $entry;
    }
    
    /**
     * Récupérer les articles publiés pour le sitemap
     */
    private static function getPublishedArticles(): array
    {
        try {
            $db = Database::getInstance();
            $sql = "SELECT slug, updated_at FROM articles WHERE status = 'published' ORDER BY published_at DESC";
            $stmt = $db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erreur lors de la récupération des articles pour le sitemap: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Récupérer les jeux publiés pour le sitemap
     */
    private static function getPublishedGames(): array
    {
        try {
            $db = Database::getInstance();
            $sql = "SELECT slug, updated_at FROM games WHERE status = 'published' ORDER BY created_at DESC";
            $stmt = $db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erreur lors de la récupération des jeux pour le sitemap: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Récupérer les catégories pour le sitemap
     */
    private static function getCategories(): array
    {
        try {
            $db = Database::getInstance();
            $sql = "SELECT slug FROM categories WHERE status = 'active' ORDER BY name ASC";
            $stmt = $db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erreur lors de la récupération des catégories pour le sitemap: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Générer le robots.txt
     */
    public static function generateRobotsTxt(): string
    {
        $robots = "User-agent: *\n";
        $robots .= "Allow: /\n";
        $robots .= "Disallow: /admin/\n";
        $robots .= "Disallow: /public/uploads/\n";
        $robots .= "Disallow: /core/\n";
        $robots .= "Disallow: /app/\n";
        $robots .= "Disallow: /config/\n";
        $robots .= "Disallow: /database/\n";
        $robots .= "Disallow: /themes/\n";
        $robots .= "Disallow: /docs/\n";
        $robots .= "Disallow: /*.sql\n";
        $robots .= "Disallow: /*.md\n";
        $robots .= "Disallow: /*.txt\n";
        $robots .= "Disallow: /*.log\n";
        $robots .= "\n";
        $robots .= "Sitemap: https://belgium-video-gaming.be/sitemap.xml\n";
        
        return $robots;
    }
}
