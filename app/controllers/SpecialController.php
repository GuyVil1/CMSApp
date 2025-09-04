<?php
declare(strict_types=1);

/**
 * Contrôleur spécial pour les routes utilitaires
 */

require_once __DIR__ . '/../../core/Controller.php';

class SpecialController extends \Controller
{
    /**
     * Gérer la route uploads.php
     */
    public function uploads(): void
    {
        // Inclure et exécuter le fichier uploads.php
        $uploadsPath = __DIR__ . '/../../public/uploads.php';
        
        if (file_exists($uploadsPath)) {
            require_once $uploadsPath;
        } else {
            http_response_code(404);
            echo 'Fichier uploads.php non trouvé';
        }
    }
}
