<?php
declare(strict_types=1);

/**
 * Interface pour le service des catégories
 */
interface CategoryServiceInterface
{
    /**
     * Récupérer toutes les catégories avec cache
     */
    public function getAllCategories(): array;

    /**
     * Récupérer une catégorie par ID avec cache
     */
    public function getCategoryById(int $id): ?array;

    /**
     * Récupérer les catégories populaires avec cache
     */
    public function getPopularCategories(int $limit = 5): array;
}
