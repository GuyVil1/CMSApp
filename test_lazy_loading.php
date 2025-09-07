<?php
/**
 * Test du lazy loading
 */

echo "<h1>🖼️ TEST LAZY LOADING</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    .test-section { margin: 20px 0; padding: 15px; border: 1px solid #ccc; border-radius: 5px; }
    .image-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin: 20px 0; }
    .image-item { border: 1px solid #ddd; border-radius: 8px; overflow: hidden; }
    .image-item img { width: 100%; height: 200px; object-fit: cover; }
    .stats { background: #f5f5f5; padding: 10px; border-radius: 5px; margin: 10px 0; }
</style>";

echo "<div class='test-section'>";
echo "<h2>📊 STATISTIQUES DU LAZY LOADING</h2>";
echo "<div class='stats' id='lazy-stats'>Chargement...</div>";
echo "</div>";

echo "<div class='test-section'>";
echo "<h2>🖼️ GRILLE D'IMAGES AVEC LAZY LOADING</h2>";
echo "<div class='image-grid'>";

// Générer 20 images de test avec lazy loading
for ($i = 1; $i <= 20; $i++) {
    $imageUrl = "https://picsum.photos/400/300?random=" . $i;
    echo "<div class='image-item'>";
    echo "<img data-lazy='{$imageUrl}' alt='Image test {$i}' class='lazy-responsive'>";
    echo "</div>";
}

echo "</div>";
echo "</div>";

echo "<div class='test-section'>";
echo "<h2>📱 IMAGES RESPONSIVES</h2>";
echo "<div style='display: grid; grid-template-columns: 1fr 1fr; gap: 20px;'>";
echo "<div>";
echo "<h3>Image 1</h3>";
echo "<img data-lazy='https://picsum.photos/600/400?random=21' alt='Image responsive 1' class='lazy-responsive'>";
echo "</div>";
echo "<div>";
echo "<h3>Image 2</h3>";
echo "<img data-lazy='https://picsum.photos/600/400?random=22' alt='Image responsive 2' class='lazy-responsive'>";
echo "</div>";
echo "</div>";
echo "</div>";

echo "<div class='test-section'>";
echo "<h2>🎯 TEST DE PERFORMANCE</h2>";
echo "<p>Ouvrez les DevTools (F12) et regardez l'onglet Network pour voir le lazy loading en action.</p>";
echo "<p>Les images ne se chargent que quand elles entrent dans le viewport.</p>";
echo "</div>";

echo "<script>
// Afficher les statistiques du lazy loading
document.addEventListener('lazyImageLoaded', (e) => {
    const { loadedCount, totalCount } = e.detail;
    document.getElementById('lazy-stats').innerHTML = 
        'Images chargées: ' + loadedCount + '/' + totalCount + ' (' + Math.round((loadedCount/totalCount)*100) + '%)';
});

document.addEventListener('lazyAllImagesLoaded', (e) => {
    const { loadedCount, totalCount } = e.detail;
    document.getElementById('lazy-stats').innerHTML = 
        '✅ Toutes les images chargées! (' + loadedCount + '/' + totalCount + ')';
    document.getElementById('lazy-stats').style.background = '#d4edda';
    document.getElementById('lazy-stats').style.color = '#155724';
});
</script>";
?>
