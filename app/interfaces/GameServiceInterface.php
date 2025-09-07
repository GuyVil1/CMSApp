<?php
declare(strict_types=1);

/**
 * Interface pour le service des jeux
 */
interface GameServiceInterface
{
    /**
     * Récupérer tous les jeux avec cache
     */
    public function getAllGames(): array;

    /**
     * Récupérer un jeu par ID avec cache
     */
    public function getGameById(int $id): ?array;

    /**
     * Récupérer les jeux populaires avec cache
     */
    public function getPopularGames(int $limit = 5): array;

    /**
     * Rechercher des jeux par nom
     */
    public function searchGames(string $query, int $limit = 10): array;
}
