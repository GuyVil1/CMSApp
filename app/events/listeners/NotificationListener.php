<?php
declare(strict_types=1);

require_once __DIR__ . '/../EventListenerInterface.php';
require_once __DIR__ . '/../EventInterface.php';
require_once __DIR__ . '/../ArticleEvents.php';

/**
 * Listener pour envoyer des notifications
 */
class NotificationListener implements EventListenerInterface
{
    public function handle(EventInterface $event): void
    {
        switch ($event->getName()) {
            case 'article.created':
                $this->notifyNewArticle($event);
                break;
            case 'article.updated':
                $this->notifyArticleUpdate($event);
                break;
            case 'user.registered':
                $this->notifyNewUser($event);
                break;
        }
    }
    
    public function supports(EventInterface $event): bool
    {
        return in_array($event->getName(), [
            'article.created',
            'article.updated',
            'user.registered'
        ]);
    }
    
    public function getPriority(): int
    {
        return 10; // Priorité faible
    }
    
    private function notifyNewArticle(EventInterface $event): void
    {
        $articleId = $event->getArticleId();
        $articleData = $event->getArticleData();
        
        // Simuler l'envoi d'une notification
        $message = "Nouvel article créé: {$articleData['title']} (ID: {$articleId})";
        error_log("NOTIFICATION: {$message}");
        
        // Ici on pourrait envoyer un email, une notification push, etc.
    }
    
    private function notifyArticleUpdate(EventInterface $event): void
    {
        $articleId = $event->getArticleId();
        $newData = $event->getNewData();
        
        $message = "Article mis à jour: {$newData['title']} (ID: {$articleId})";
        error_log("NOTIFICATION: {$message}");
    }
    
    private function notifyNewUser(EventInterface $event): void
    {
        $userId = $event->getUserId();
        $userData = $event->getUserData();
        
        $message = "Nouvel utilisateur inscrit: {$userData['username']} (ID: {$userId})";
        error_log("NOTIFICATION: {$message}");
    }
}
