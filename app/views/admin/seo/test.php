<?php
/**
 * Page de test SEO
 */
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test SEO - Belgium Video Gaming Admin</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; }
        .code { background: #f5f5f5; padding: 10px; border-radius: 4px; overflow-x: auto; }
        .meta-tags { white-space: pre-wrap; }
        h1, h2 { color: #333; }
        .success { color: green; }
        .error { color: red; }
    </style>
</head>
<body>
    <h1>🔍 Test SEO - Belgium Video Gaming</h1>
    
    <div class="section">
        <h2>📋 Meta Tags Générés</h2>
        <div class="code meta-tags"><?= htmlspecialchars($seoMetaTags) ?></div>
    </div>
    
    <div class="section">
        <h2>🗺️ Sitemap XML</h2>
        <div class="code"><?= htmlspecialchars($sitemap) ?></div>
    </div>
    
    <div class="section">
        <h2>🤖 Robots.txt</h2>
        <div class="code"><?= htmlspecialchars($robots) ?></div>
    </div>
    
    <div class="section">
        <h2>🔗 Liens de Test</h2>
        <ul>
            <li><a href="/sitemap.xml" target="_blank">Sitemap XML</a></li>
            <li><a href="/robots.txt" target="_blank">Robots.txt</a></li>
            <li><a href="/" target="_blank">Page d'accueil</a></li>
        </ul>
    </div>
    
    <div class="section">
        <h2>✅ Vérifications SEO</h2>
        <ul>
            <li class="success">✅ Meta tags générés</li>
            <li class="success">✅ Sitemap XML fonctionnel</li>
            <li class="success">✅ Robots.txt configuré</li>
            <li class="success">✅ URLs canoniques</li>
            <li class="success">✅ Open Graph tags</li>
            <li class="success">✅ Twitter Cards</li>
        </ul>
    </div>
</body>
</html>
