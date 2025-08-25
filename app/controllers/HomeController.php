<?php
declare(strict_types=1);

/**
 * Contrôleur Home temporaire
 * Pour tester l'authentification
 */

require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../core/Auth.php';

class HomeController extends Controller
{
    /**
     * Page d'accueil temporaire
     */
    public function index(): void
    {
        $user = Auth::getUser();
        
        $this->render('home/index', [
            'user' => $user,
            'isLoggedIn' => Auth::isLoggedIn()
        ]);
    }
}
