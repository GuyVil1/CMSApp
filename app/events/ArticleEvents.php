<?php
declare(strict_types=1);

require_once __DIR__ . '/BaseEvent.php';

/**
 * Événements liés aux articles
 */
class ArticleCreatedEvent extends BaseEvent
{
    public function __construct(int $articleId, array $articleData)
    {
        parent::__construct('article.created', [
            'article_id' => $articleId,
            'article_data' => $articleData,
            'created_at' => time()
        ]);
    }
    
    public function getArticleId(): int
    {
        return $this->get('article_id');
    }
    
    public function getArticleData(): array
    {
        return $this->get('article_data', []);
    }
}

class ArticleUpdatedEvent extends BaseEvent
{
    public function __construct(int $articleId, array $oldData, array $newData)
    {
        parent::__construct('article.updated', [
            'article_id' => $articleId,
            'old_data' => $oldData,
            'new_data' => $newData,
            'updated_at' => time()
        ]);
    }
    
    public function getArticleId(): int
    {
        return $this->get('article_id');
    }
    
    public function getOldData(): array
    {
        return $this->get('old_data', []);
    }
    
    public function getNewData(): array
    {
        return $this->get('new_data', []);
    }
}

class ArticleDeletedEvent extends BaseEvent
{
    public function __construct(int $articleId, array $articleData)
    {
        parent::__construct('article.deleted', [
            'article_id' => $articleId,
            'article_data' => $articleData,
            'deleted_at' => time()
        ]);
    }
    
    public function getArticleId(): int
    {
        return $this->get('article_id');
    }
    
    public function getArticleData(): array
    {
        return $this->get('article_data', []);
    }
}

class ArticleViewedEvent extends BaseEvent
{
    public function __construct(int $articleId, string $userIp = null)
    {
        parent::__construct('article.viewed', [
            'article_id' => $articleId,
            'user_ip' => $userIp,
            'viewed_at' => time()
        ]);
    }
    
    public function getArticleId(): int
    {
        return $this->get('article_id');
    }
    
    public function getUserIp(): ?string
    {
        return $this->get('user_ip');
    }
}
