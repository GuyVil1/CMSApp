<?php
/**
 * Script de mise à jour du sitemap
 * À exécuter via cron ou manuellement pour mettre à jour le sitemap
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/core/Database.php';
require_once __DIR__ . '/app/helpers/seo_helper.php';

try {
    // Générer le nouveau sitemap
    $sitemap = SeoHelper::generateSitemap();
    
    // Écrire le fichier sitemap.xml
    file_put_contents(__DIR__ . '/sitemap.xml', $sitemap);
    
    // Générer le robots.txt
    $robots = SeoHelper::generateRobotsTxt();
    file_put_contents(__DIR__ . '/robots.txt', $robots);
    
    echo "✅ Sitemap et robots.txt mis à jour avec succès !\n";
    echo "📅 Date de mise à jour : " . date('Y-m-d H:i:s') . "\n";
    
    // Afficher quelques statistiques
    $xml = simplexml_load_string($sitemap);
    echo "📊 Nombre d'URLs dans le sitemap : " . count($xml->url) . "\n";
    
} catch (Exception $e) {
    echo "❌ Erreur lors de la mise à jour : " . $e->getMessage() . "\n";
    exit(1);
}
?>
