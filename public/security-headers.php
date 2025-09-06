<?php
/**
 * Headers de sécurité pour l'application
 * À inclure dans le fichier principal (index.php)
 */

// Headers de sécurité
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN'); // Permet les iframes de même origine
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: geolocation=(), microphone=(), camera=()');

// Content Security Policy (CSP) avec support YouTube
$csp = "default-src 'self'; " .
       "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://fonts.googleapis.com https://www.youtube.com https://s.ytimg.com; " .
       "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; " .
       "font-src 'self' https://fonts.gstatic.com; " .
       "img-src 'self' data: blob: https://i.ytimg.com https://s.ytimg.com; " .
       "connect-src 'self'; " .
       "frame-src 'self' https://www.youtube.com https://youtube.com; " . // Autorise YouTube
       "frame-ancestors 'self';"; // Permet les iframes de même origine

header("Content-Security-Policy: $csp");

// Headers de cache pour les fichiers statiques
$requestUri = $_SERVER['REQUEST_URI'] ?? '';
if (preg_match('/\.(css|js|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$/', $requestUri)) {
    header('Cache-Control: public, max-age=31536000'); // 1 an
    header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');
} else {
    // Pas de cache pour les pages dynamiques
    header('Cache-Control: no-cache, no-store, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');
}
?>
