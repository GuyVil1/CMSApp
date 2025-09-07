<?php
declare(strict_types=1);

require_once __DIR__ . '/../EventListenerInterface.php';
require_once __DIR__ . '/../EventInterface.php';

/**
 * Listener pour logger tous les événements
 */
class LoggingListener implements EventListenerInterface
{
    private string $logFile;
    
    public function __construct(string $logFile = null)
    {
        $this->logFile = $logFile ?? __DIR__ . '/../../logs/events.log';
        
        // Créer le dossier de logs s'il n'existe pas
        $logDir = dirname($this->logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
    }
    
    public function handle(EventInterface $event): void
    {
        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s', $event->getTimestamp()),
            'event' => $event->getName(),
            'data' => $event->getData()
        ];
        
        $logLine = json_encode($logEntry, JSON_UNESCAPED_UNICODE) . "\n";
        file_put_contents($this->logFile, $logLine, FILE_APPEND | LOCK_EX);
    }
    
    public function supports(EventInterface $event): bool
    {
        return true; // Logger tous les événements
    }
    
    public function getPriority(): int
    {
        return 50; // Priorité moyenne
    }
}
