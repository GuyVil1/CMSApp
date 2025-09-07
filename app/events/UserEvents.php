<?php
declare(strict_types=1);

require_once __DIR__ . '/BaseEvent.php';

/**
 * Événements liés aux utilisateurs
 */
class UserLoggedInEvent extends BaseEvent
{
    public function __construct(int $userId, string $userIp = null)
    {
        parent::__construct('user.logged_in', [
            'user_id' => $userId,
            'user_ip' => $userIp,
            'logged_in_at' => time()
        ]);
    }
    
    public function getUserId(): int
    {
        return $this->get('user_id');
    }
    
    public function getUserIp(): ?string
    {
        return $this->get('user_ip');
    }
}

class UserLoggedOutEvent extends BaseEvent
{
    public function __construct(int $userId)
    {
        parent::__construct('user.logged_out', [
            'user_id' => $userId,
            'logged_out_at' => time()
        ]);
    }
    
    public function getUserId(): int
    {
        return $this->get('user_id');
    }
}

class UserRegisteredEvent extends BaseEvent
{
    public function __construct(int $userId, array $userData)
    {
        parent::__construct('user.registered', [
            'user_id' => $userId,
            'user_data' => $userData,
            'registered_at' => time()
        ]);
    }
    
    public function getUserId(): int
    {
        return $this->get('user_id');
    }
    
    public function getUserData(): array
    {
        return $this->get('user_data', []);
    }
}
