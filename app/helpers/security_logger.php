<?php
declare(strict_types=1);

/**
 * Helper de logs de sécurité
 * Centralise l'enregistrement des événements de sécurité
 */

class SecurityLogger
{
    // Niveaux de log
    public const LEVEL_INFO = 'INFO';
    public const LEVEL_WARNING = 'WARNING';
    public const LEVEL_ERROR = 'ERROR';
    public const LEVEL_CRITICAL = 'CRITICAL';
    
    // Types d'événements
    public const EVENT_LOGIN_SUCCESS = 'LOGIN_SUCCESS';
    public const EVENT_LOGIN_FAILED = 'LOGIN_FAILED';
    public const EVENT_LOGIN_BLOCKED = 'LOGIN_BLOCKED';
    public const EVENT_UPLOAD_SUCCESS = 'UPLOAD_SUCCESS';
    public const EVENT_UPLOAD_FAILED = 'UPLOAD_FAILED';
    public const EVENT_UPLOAD_BLOCKED = 'UPLOAD_BLOCKED';
    public const EVENT_CSRF_VIOLATION = 'CSRF_VIOLATION';
    public const EVENT_RATE_LIMIT_EXCEEDED = 'RATE_LIMIT_EXCEEDED';
    public const EVENT_VALIDATION_FAILED = 'VALIDATION_FAILED';
    public const EVENT_ADMIN_ACTION = 'ADMIN_ACTION';
    public const EVENT_SUSPICIOUS_ACTIVITY = 'SUSPICIOUS_ACTIVITY';
    
    private static string $logDir = '';
    
    /**
     * Initialiser le logger
     */
    public static function init(): void
    {
        self::$logDir = __DIR__ . '/../../logs/security';
        if (!is_dir(self::$logDir)) {
            mkdir(self::$logDir, 0755, true);
        }
    }
    
    /**
     * Enregistrer un événement de sécurité
     */
    public static function log(string $event, string $level, string $message, array $context = []): void
    {
        self::init();
        
        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'level' => $level,
            'event' => $event,
            'message' => $message,
            'context' => $context,
            'ip' => self::getClientIp(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown',
            'user_id' => self::getCurrentUserId(),
            'session_id' => session_id() ?: 'No session'
        ];
        
        $logFile = self::$logDir . '/security_' . date('Y-m-d') . '.log';
        $logLine = json_encode($logEntry) . "\n";
        
        file_put_contents($logFile, $logLine, FILE_APPEND | LOCK_EX);
        
        // Aussi logger dans les logs système PHP si niveau critique
        if ($level === self::LEVEL_CRITICAL) {
            error_log("SECURITY CRITICAL: {$event} - {$message} - " . json_encode($context));
        }
    }
    
    /**
     * Logger une tentative de connexion réussie
     */
    public static function logLoginSuccess(string $username, int $userId): void
    {
        self::log(
            self::EVENT_LOGIN_SUCCESS,
            self::LEVEL_INFO,
            "Connexion réussie pour l'utilisateur: {$username}",
            [
                'username' => $username,
                'user_id' => $userId,
                'login_time' => date('Y-m-d H:i:s')
            ]
        );
    }
    
    /**
     * Logger une tentative de connexion échouée
     */
    public static function logLoginFailed(string $username, string $reason = 'Identifiants incorrects'): void
    {
        self::log(
            self::EVENT_LOGIN_FAILED,
            self::LEVEL_WARNING,
            "Tentative de connexion échouée pour: {$username} - {$reason}",
            [
                'username' => $username,
                'reason' => $reason,
                'attempt_time' => date('Y-m-d H:i:s')
            ]
        );
    }
    
    /**
     * Logger un blocage de connexion (rate limiting)
     */
    public static function logLoginBlocked(string $ip, array $rateLimitInfo): void
    {
        self::log(
            self::EVENT_LOGIN_BLOCKED,
            self::LEVEL_WARNING,
            "Connexion bloquée par rate limiting",
            [
                'ip' => $ip,
                'rate_limit_info' => $rateLimitInfo,
                'block_time' => date('Y-m-d H:i:s')
            ]
        );
    }
    
    /**
     * Logger un upload réussi
     */
    public static function logUploadSuccess(string $filename, int $fileSize, int $userId = null): void
    {
        self::log(
            self::EVENT_UPLOAD_SUCCESS,
            self::LEVEL_INFO,
            "Upload réussi: {$filename}",
            [
                'filename' => $filename,
                'file_size' => $fileSize,
                'user_id' => $userId,
                'upload_time' => date('Y-m-d H:i:s')
            ]
        );
    }
    
    /**
     * Logger un upload échoué
     */
    public static function logUploadFailed(string $filename, string $reason, int $userId = null): void
    {
        self::log(
            self::EVENT_UPLOAD_FAILED,
            self::LEVEL_WARNING,
            "Upload échoué: {$filename} - {$reason}",
            [
                'filename' => $filename,
                'reason' => $reason,
                'user_id' => $userId,
                'attempt_time' => date('Y-m-d H:i:s')
            ]
        );
    }
    
    /**
     * Logger un upload bloqué (rate limiting)
     */
    public static function logUploadBlocked(string $filename, array $rateLimitInfo, int $userId = null): void
    {
        self::log(
            self::EVENT_UPLOAD_BLOCKED,
            self::LEVEL_WARNING,
            "Upload bloqué par rate limiting: {$filename}",
            [
                'filename' => $filename,
                'rate_limit_info' => $rateLimitInfo,
                'user_id' => $userId,
                'block_time' => date('Y-m-d H:i:s')
            ]
        );
    }
    
    /**
     * Logger une violation CSRF
     */
    public static function logCsrfViolation(string $action, string $token = null): void
    {
        self::log(
            self::EVENT_CSRF_VIOLATION,
            self::LEVEL_ERROR,
            "Violation CSRF détectée pour l'action: {$action}",
            [
                'action' => $action,
                'token' => $token ? substr($token, 0, 10) . '...' : 'No token',
                'violation_time' => date('Y-m-d H:i:s')
            ]
        );
    }
    
    /**
     * Logger un dépassement de rate limit
     */
    public static function logRateLimitExceeded(string $type, array $rateLimitInfo): void
    {
        self::log(
            self::EVENT_RATE_LIMIT_EXCEEDED,
            self::LEVEL_WARNING,
            "Rate limit dépassé pour: {$type}",
            [
                'type' => $type,
                'rate_limit_info' => $rateLimitInfo,
                'exceeded_time' => date('Y-m-d H:i:s')
            ]
        );
    }
    
    /**
     * Logger un échec de validation
     */
    public static function logValidationFailed(string $field, string $value, string $reason): void
    {
        self::log(
            self::EVENT_VALIDATION_FAILED,
            self::LEVEL_WARNING,
            "Validation échouée pour le champ: {$field}",
            [
                'field' => $field,
                'value' => substr($value, 0, 100), // Limiter la taille
                'reason' => $reason,
                'validation_time' => date('Y-m-d H:i:s')
            ]
        );
    }
    
    /**
     * Logger une action administrative
     */
    public static function logAdminAction(string $action, array $details = []): void
    {
        self::log(
            self::EVENT_ADMIN_ACTION,
            self::LEVEL_INFO,
            "Action administrative: {$action}",
            array_merge($details, [
                'action' => $action,
                'admin_time' => date('Y-m-d H:i:s')
            ])
        );
    }
    
    /**
     * Logger une activité suspecte
     */
    public static function logSuspiciousActivity(string $description, array $context = []): void
    {
        self::log(
            self::EVENT_SUSPICIOUS_ACTIVITY,
            self::LEVEL_CRITICAL,
            "Activité suspecte détectée: {$description}",
            array_merge($context, [
                'description' => $description,
                'detection_time' => date('Y-m-d H:i:s')
            ])
        );
    }
    
    /**
     * Obtenir l'adresse IP du client
     */
    private static function getClientIp(): string
    {
        $ipKeys = ['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR'];
        
        foreach ($ipKeys as $key) {
            if (!empty($_SERVER[$key])) {
                $ip = $_SERVER[$key];
                if (strpos($ip, ',') !== false) {
                    $ip = trim(explode(',', $ip)[0]);
                }
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    }
    
    /**
     * Obtenir l'ID de l'utilisateur actuel
     */
    private static function getCurrentUserId(): ?int
    {
        if (class_exists('Auth') && method_exists('Auth', 'getUserId')) {
            return Auth::getUserId();
        }
        return null;
    }
    
    /**
     * Obtenir les logs de sécurité pour une période
     */
    public static function getLogs(string $date = null, string $level = null, string $event = null): array
    {
        self::init();
        
        $date = $date ?: date('Y-m-d');
        $logFile = self::$logDir . '/security_' . $date . '.log';
        
        if (!file_exists($logFile)) {
            return [];
        }
        
        $logs = [];
        $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            $logEntry = json_decode($line, true);
            if (!$logEntry) continue;
            
            // Filtrer par niveau si spécifié
            if ($level && $logEntry['level'] !== $level) continue;
            
            // Filtrer par événement si spécifié
            if ($event && $logEntry['event'] !== $event) continue;
            
            $logs[] = $logEntry;
        }
        
        // Trier par timestamp décroissant
        usort($logs, function($a, $b) {
            return strtotime($b['timestamp']) - strtotime($a['timestamp']);
        });
        
        return $logs;
    }
    
    /**
     * Obtenir les statistiques de sécurité
     */
    public static function getStats(string $date = null): array
    {
        $date = $date ?: date('Y-m-d');
        $logs = self::getLogs($date);
        
        $stats = [
            'total_events' => count($logs),
            'by_level' => [],
            'by_event' => [],
            'by_hour' => [],
            'unique_ips' => [],
            'unique_users' => []
        ];
        
        foreach ($logs as $log) {
            // Par niveau
            $stats['by_level'][$log['level']] = ($stats['by_level'][$log['level']] ?? 0) + 1;
            
            // Par événement
            $stats['by_event'][$log['event']] = ($stats['by_event'][$log['event']] ?? 0) + 1;
            
            // Par heure
            $hour = date('H', strtotime($log['timestamp']));
            $stats['by_hour'][$hour] = ($stats['by_hour'][$hour] ?? 0) + 1;
            
            // IPs uniques
            if (!in_array($log['ip'], $stats['unique_ips'])) {
                $stats['unique_ips'][] = $log['ip'];
            }
            
            // Utilisateurs uniques
            if ($log['user_id'] && !in_array($log['user_id'], $stats['unique_users'])) {
                $stats['unique_users'][] = $log['user_id'];
            }
        }
        
        return $stats;
    }
    
    /**
     * Nettoyer les anciens logs (plus de 30 jours)
     */
    public static function cleanOldLogs(int $daysToKeep = 30): void
    {
        self::init();
        
        $cutoff = time() - ($daysToKeep * 24 * 60 * 60);
        $files = glob(self::$logDir . '/security_*.log');
        
        foreach ($files as $file) {
            if (filemtime($file) < $cutoff) {
                unlink($file);
            }
        }
    }
}
