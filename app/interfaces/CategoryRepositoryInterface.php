<?php
declare(strict_types=1);

/**
 * Interface pour le repository des catégories
 */
interface CategoryRepositoryInterface
{
    /**
     * Trouver toutes les catégories
     */
    public function findAll(): array;

    /**
     * Trouver une catégorie par ID
     */
    public function findById(int $id): ?array;

    /**
     * Trouver une catégorie par slug
     */
    public function findBySlug(string $slug): ?array;

    /**
     * Trouver les catégories populaires
     */
    public function findPopular(int $limit = 5): array;
}
