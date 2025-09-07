<?php
declare(strict_types=1);

/**
 * Interface pour le service des articles
 * Contrat clair pour la logique métier
 */
interface ArticleServiceInterface
{
    /**
     * Récupérer un article par son slug avec cache
     */
    public function getArticleBySlug(string $slug): ?array;

    /**
     * Récupérer un article par son ID avec cache
     */
    public function getArticleById(int $id): ?array;

    /**
     * Récupérer les articles en vedette avec cache
     */
    public function getFeaturedArticles(int $limit = 3): array;

    /**
     * Récupérer les articles récents avec pagination
     */
    public function getRecentArticles(int $page = 1, int $limit = 10): array;

    /**
     * Récupérer les articles par catégorie
     */
    public function getArticlesByCategory(int $categoryId, int $page = 1, int $limit = 10): array;

    /**
     * Récupérer les articles par jeu
     */
    public function getArticlesByGame(int $gameId, int $page = 1, int $limit = 10): array;

    /**
     * Rechercher des articles
     */
    public function searchArticles(string $query, int $page = 1, int $limit = 10): array;

    /**
     * Créer un nouvel article
     */
    public function createArticle(array $data): ?int;

    /**
     * Mettre à jour un article
     */
    public function updateArticle(int $id, array $data): bool;

    /**
     * Supprimer un article
     */
    public function deleteArticle(int $id): bool;
}
