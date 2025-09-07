<?php
declare(strict_types=1);

/**
 * Contrôleur d'authentification
 * Gestion des connexions, déconnexions et inscriptions
 */

require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../helpers/rate_limit_helper.php';
require_once __DIR__ . '/../helpers/security_logger_simple.php';

class AuthController extends Controller
{
    /**
     * Page de connexion
     */
    public function login(): void
    {
        // Si déjà connecté, rediriger
        if (Auth::isLoggedIn()) {
            $this->redirectTo('/');
        }
        
        $error = '';
        
        if ($this->isPost()) {
            $login = $this->getPostParam('login', '');
            $password = $this->getPostParam('password', '');
            $csrf_token = $this->getPostParam('csrf_token', '');
            
            // Vérifier les limites de rate limiting pour les connexions
            $clientIp = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            $rateLimitCheck = \RateLimitHelper::checkRateLimit($clientIp, 'login');
            if (!$rateLimitCheck['allowed']) {
                $error = $rateLimitCheck['reason'] . ' (Limite: 3 tentatives par 5 minutes)';
                \SecurityLoggerSimple::logLoginBlocked($clientIp, $rateLimitCheck);
            } elseif (!Auth::verifyCsrfToken($csrf_token)) {
                $error = 'Token de sécurité invalide';
                \SecurityLoggerSimple::logCsrfViolation('login', $csrf_token);
                \RateLimitHelper::recordRequest($clientIp, 'login');
            } elseif (empty($login) || empty($password)) {
                $error = 'Tous les champs sont requis';
                \SecurityLoggerSimple::logLoginFailed($login, 'Champs manquants');
                \RateLimitHelper::recordRequest($clientIp, 'login');
            } else {
                // Tenter la connexion
                if (Auth::login($login, $password)) {
                    \SecurityLoggerSimple::logLoginSuccess($login, Auth::getUserId());
                    \RateLimitHelper::recordRequest($clientIp, 'login');
                    $this->redirectTo('/');
                } else {
                    $error = 'Identifiants incorrects';
                    \SecurityLoggerSimple::logLoginFailed($login, 'Identifiants incorrects');
                    \RateLimitHelper::recordRequest($clientIp, 'login');
                }
            }
        }
        
        $this->render('auth/login', [
            'error' => $error,
            'csrf_token' => Auth::generateCsrfToken()
        ]);
    }
    
    /**
     * Déconnexion
     */
    public function logout(): void
    {
        Auth::logout();
        $this->redirectTo('/');
    }
    
    /**
     * Page d'inscription (optionnelle)
     */
    public function register(): void
    {
        // Si déjà connecté, rediriger
        if (Auth::isLoggedIn()) {
            $this->redirectTo('/');
        }
        
        // Vérifier si les inscriptions sont autorisées
        try {
            require_once __DIR__ . '/../models/Setting.php';
            if (!\Setting::isEnabled('allow_registration')) {
                $this->render('layout/403', [
                    'pageTitle' => 'Inscriptions fermées - Belgium Video Gaming',
                    'pageDescription' => 'Les inscriptions sont temporairement fermées.'
                ]);
                return;
            }
        } catch (Exception $e) {
            // En cas d'erreur, on autorise par défaut
            error_log("Erreur lors de la vérification des inscriptions: " . $e->getMessage());
        }
        
        $error = '';
        $success = '';
        
        if ($this->isPost()) {
            $login = $this->sanitizeString($this->getPostParam('login', ''));
            $email = $this->sanitizeString($this->getPostParam('email', ''));
            $password = $this->getPostParam('password', '');
            $password_confirm = $this->getPostParam('password_confirm', '');
            $csrf_token = $this->getPostParam('csrf_token', '');
            
            // Valider le token CSRF
            if (!Auth::verifyCsrfToken($csrf_token)) {
                $error = 'Token de sécurité invalide';
            } elseif (empty($login) || empty($email) || empty($password)) {
                $error = 'Tous les champs sont requis';
            } elseif (!$this->validateEmail($email)) {
                $error = 'Email invalide';
            } else {
                // Validation avancée du mot de passe
                $passwordErrors = $this->validatePassword($password);
                if (!empty($passwordErrors)) {
                    $error = implode('. ', $passwordErrors);
                }
            }
            
            if (empty($error)) {
                // Validation du nom d'utilisateur
                $usernameErrors = $this->validateUsername($login);
                if (!empty($usernameErrors)) {
                    $error = implode('. ', $usernameErrors);
                }
            }
            
            if (empty($error) && $password !== $password_confirm) {
                $error = 'Les mots de passe ne correspondent pas';
            }
            
            if (empty($error)) {
                // Créer l'utilisateur
                $userData = [
                    'login' => $login,
                    'email' => $email,
                    'password' => $password,
                    'role_id' => 4 // member par défaut
                ];
                
                $user = User::create($userData);
                
                if ($user) {
                    $success = 'Compte créé avec succès ! Vous pouvez maintenant vous connecter.';
                    // Régénérer le token CSRF
                    Auth::regenerateCsrfToken();
                } else {
                    $error = 'Erreur lors de la création du compte';
                }
            }
        }
        
        $this->render('auth/register', [
            'error' => $error,
            'success' => $success,
            'csrf_token' => Auth::generateCsrfToken()
        ]);
    }
    
    /**
     * Page de changement de mot de passe
     */
    public function changePassword(): void
    {
        Auth::requireLogin();
        
        $error = '';
        $success = '';
        
        if ($this->isPost()) {
            $current_password = $this->getPostParam('current_password', '');
            $new_password = $this->getPostParam('new_password', '');
            $new_password_confirm = $this->getPostParam('new_password_confirm', '');
            $csrf_token = $this->getPostParam('csrf_token', '');
            
            // Valider le token CSRF
            if (!Auth::verifyCsrfToken($csrf_token)) {
                $error = 'Token de sécurité invalide';
            } elseif (empty($current_password) || empty($new_password)) {
                $error = 'Tous les champs sont requis';
            } elseif (strlen($new_password) < 8) {
                $error = 'Le nouveau mot de passe doit contenir au moins 8 caractères';
            } elseif ($new_password !== $new_password_confirm) {
                $error = 'Les nouveaux mots de passe ne correspondent pas';
            } else {
                // Vérifier l'ancien mot de passe
                $user = Auth::getUser();
                if (!$user || !Auth::verifyPassword($current_password, $user['password_hash'])) {
                    $error = 'Mot de passe actuel incorrect';
                } else {
                    // Mettre à jour le mot de passe
                    $userModel = User::findById($user['id']);
                    if ($userModel && $userModel->update(['password' => $new_password])) {
                        $success = 'Mot de passe modifié avec succès';
                        Auth::regenerateCsrfToken();
                    } else {
                        $error = 'Erreur lors de la modification du mot de passe';
                    }
                }
            }
        }
        
        $this->render('auth/change_password', [
            'error' => $error,
            'success' => $success,
            'csrf_token' => Auth::generateCsrfToken()
        ]);
    }
    
    /**
     * Page 403 - Accès interdit
     */
    public function forbidden(): void
    {
        http_response_code(403);
        $this->render('layout/403', [
            'message' => 'Accès interdit - Vous n\'avez pas les permissions nécessaires'
        ]);
    }
}
