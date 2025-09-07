<?php
declare(strict_types=1);

/**
 * Interface pour le repository des articles
 * Contrat clair pour l'accès aux données
 */
interface ArticleRepositoryInterface
{
    /**
     * Trouver un article par son slug
     */
    public function findBySlug(string $slug): ?array;

    /**
     * Trouver un article par son ID
     */
    public function findById(int $id): ?array;

    /**
     * Trouver les articles en vedette
     */
    public function findFeatured(int $limit = 3): array;

    /**
     * Trouver les articles récents
     */
    public function findRecent(int $limit = 10, int $offset = 0): array;

    /**
     * Trouver les articles par catégorie
     */
    public function findByCategory(int $categoryId, int $limit = 10, int $offset = 0): array;

    /**
     * Trouver les articles par jeu
     */
    public function findByGame(int $gameId, int $limit = 10, int $offset = 0): array;

    /**
     * Rechercher des articles
     */
    public function search(string $query, int $limit = 10, int $offset = 0): array;

    /**
     * Compter le nombre d'articles
     */
    public function count(array $filters = []): int;
}
