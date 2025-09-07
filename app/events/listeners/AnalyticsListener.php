<?php
declare(strict_types=1);

require_once __DIR__ . '/../EventListenerInterface.php';
require_once __DIR__ . '/../EventInterface.php';
require_once __DIR__ . '/../ArticleEvents.php';

/**
 * Listener pour les analytics et statistiques
 */
class AnalyticsListener implements EventListenerInterface
{
    private string $analyticsFile;
    
    public function __construct(string $analyticsFile = null)
    {
        $this->analyticsFile = $analyticsFile ?? __DIR__ . '/../../logs/analytics.log';
        
        // Créer le dossier s'il n'existe pas
        $analyticsDir = dirname($this->analyticsFile);
        if (!is_dir($analyticsDir)) {
            mkdir($analyticsDir, 0755, true);
        }
    }
    
    public function handle(EventInterface $event): void
    {
        switch ($event->getName()) {
            case 'article.viewed':
                $this->trackArticleView($event);
                break;
            case 'user.logged_in':
                $this->trackUserLogin($event);
                break;
        }
    }
    
    public function supports(EventInterface $event): bool
    {
        return in_array($event->getName(), [
            'article.viewed',
            'user.logged_in'
        ]);
    }
    
    public function getPriority(): int
    {
        return 5; // Priorité très faible
    }
    
    private function trackArticleView(EventInterface $event): void
    {
        $data = [
            'type' => 'article_view',
            'article_id' => $event->getArticleId(),
            'user_ip' => $event->getUserIp(),
            'timestamp' => $event->getTimestamp(),
            'date' => date('Y-m-d H:i:s', $event->getTimestamp())
        ];
        
        $this->writeAnalytics($data);
    }
    
    private function trackUserLogin(EventInterface $event): void
    {
        $data = [
            'type' => 'user_login',
            'user_id' => $event->getUserId(),
            'user_ip' => $event->getUserIp(),
            'timestamp' => $event->getTimestamp(),
            'date' => date('Y-m-d H:i:s', $event->getTimestamp())
        ];
        
        $this->writeAnalytics($data);
    }
    
    private function writeAnalytics(array $data): void
    {
        $logLine = json_encode($data, JSON_UNESCAPED_UNICODE) . "\n";
        file_put_contents($this->analyticsFile, $logLine, FILE_APPEND | LOCK_EX);
    }
}
