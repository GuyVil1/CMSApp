<?php
declare(strict_types=1);

/**
 * Service Game - Logique métier des jeux
 */
require_once __DIR__ . '/../repositories/GameRepository.php';
require_once __DIR__ . '/../helpers/MemoryCache.php';
require_once __DIR__ . '/../container/ContainerFactory.php';
require_once __DIR__ . '/../interfaces/GameServiceInterface.php';

class GameService implements GameServiceInterface
{
    private GameRepository $gameRepository;

    public function __construct()
    {
        $this->gameRepository = ContainerFactory::make('GameRepository');
    }

    /**
     * Récupérer tous les jeux avec cache
     */
    public function getAllGames(): array
    {
        return MemoryCache::remember('all_games', function() {
            return $this->gameRepository->findAll();
        }, 3600); // Cache 1 heure
    }

    /**
     * Récupérer un jeu par ID avec cache
     */
    public function getGameById(int $id): ?array
    {
        $cacheKey = "game_{$id}";
        return MemoryCache::remember($cacheKey, function() use ($id) {
            return $this->gameRepository->findById($id);
        }, 3600);
    }

    /**
     * Récupérer les jeux populaires avec cache
     */
    public function getPopularGames(int $limit = 5): array
    {
        $cacheKey = "popular_games_{$limit}";
        return MemoryCache::remember($cacheKey, function() use ($limit) {
            return $this->gameRepository->findPopular($limit);
        }, 1800); // Cache 30 minutes
    }

    /**
     * Rechercher des jeux par nom
     */
    public function searchGames(string $query, int $limit = 10): array
    {
        $cacheKey = "search_games_{$query}_{$limit}";
        return MemoryCache::remember($cacheKey, function() use ($query, $limit) {
            return $this->gameRepository->searchByName($query, $limit);
        }, 300); // Cache 5 minutes
    }
}
