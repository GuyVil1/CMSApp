<?php
/**
 * Headers de sécurité modernes pour l'application
 * Protection contre les attaques courantes
 */

// Headers de sécurité de base
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');

// Permissions Policy moderne (remplace Feature-Policy)
header('Permissions-Policy: geolocation=(), microphone=(), camera=(), payment=(), usb=(), magnetometer=(), gyroscope=(), fullscreen=(self), sync-xhr=()');

// Cross-Origin Policies
header('Cross-Origin-Embedder-Policy: require-corp');
header('Cross-Origin-Opener-Policy: same-origin');
header('Cross-Origin-Resource-Policy: same-origin');

// Content Security Policy (CSP) renforcé
$csp = "default-src 'self'; " .
       "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://fonts.googleapis.com https://www.youtube.com https://s.ytimg.com; " .
       "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdnjs.cloudflare.com; " .
       "font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com; " .
       "img-src 'self' data: blob: https://i.ytimg.com https://s.ytimg.com; " .
       "connect-src 'self' https://www.youtube.com; " .
       "frame-src 'self' https://www.youtube.com https://youtube.com; " .
       "frame-ancestors 'self'; " .
       "base-uri 'self'; " .
       "form-action 'self'; " .
       "object-src 'none'; " .
       "upgrade-insecure-requests;";

header("Content-Security-Policy: $csp");

// Headers de sécurité supplémentaires
header('X-Permitted-Cross-Domain-Policies: none');
header('X-Download-Options: noopen');

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
