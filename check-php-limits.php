<?php
/**
 * Vérification des limites PHP
 */

echo "<h1>🔍 Limites PHP actuelles</h1>";

// Limites d'upload
echo "<h2>📁 Limites d'upload</h2>";
echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>Paramètre</th><th>Valeur actuelle</th><th>Recommandé</th></tr>";

$limits = [
    'upload_max_filesize' => '8M',
    'post_max_size' => '8M',
    'max_file_uploads' => '20',
    'max_execution_time' => '300',
    'memory_limit' => '256M'
];

foreach ($limits as $setting => $recommended) {
    $current = ini_get($setting);
    $status = ($current >= $recommended) ? '✅' : '❌';
    
    echo "<tr>";
    echo "<td><strong>{$setting}</strong></td>";
    echo "<td>{$current}</td>";
    echo "<td>{$recommended}</td>";
    echo "</tr>";
}

echo "</table>";

// Informations sur le serveur
echo "<h2>🖥️ Informations serveur</h2>";
echo "<p><strong>Version PHP:</strong> " . phpversion() . "</p>";
echo "<p><strong>Serveur:</strong> " . $_SERVER['SERVER_SOFTWARE'] . "</p>";
echo "<p><strong>OS:</strong> " . php_uname() . "</p>";

// Vérification des extensions
echo "<h2>🔧 Extensions importantes</h2>";
$extensions = ['gd', 'fileinfo', 'mbstring', 'pdo_mysql'];
foreach ($extensions as $ext) {
    $status = extension_loaded($ext) ? '✅' : '❌';
    echo "<p>{$status} {$ext}</p>";
}

echo "<h2>📝 Recommandations</h2>";
echo "<p>Si <code>upload_max_filesize</code> est inférieur à 8M, vous devez modifier votre <code>php.ini</code>.</p>";
echo "<p>Sur WAMP, le fichier se trouve généralement dans : <code>C:\\wamp64\\bin\\php\\php8.x.x\\php.ini</code></p>";
?>
