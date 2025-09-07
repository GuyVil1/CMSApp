<?php
declare(strict_types=1);

/**
 * Interface pour le repository des jeux
 */
interface GameRepositoryInterface
{
    /**
     * Trouver tous les jeux
     */
    public function findAll(): array;

    /**
     * Trouver un jeu par ID
     */
    public function findById(int $id): ?array;

    /**
     * Trouver un jeu par slug
     */
    public function findBySlug(string $slug): ?array;

    /**
     * Trouver les jeux populaires
     */
    public function findPopular(int $limit = 5): array;

    /**
     * Rechercher des jeux par nom
     */
    public function searchByName(string $query, int $limit = 10): array;
}
