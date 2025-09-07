<?php
declare(strict_types=1);

/**
 * Service Category - Logique métier des catégories
 */
require_once __DIR__ . '/../repositories/CategoryRepository.php';
require_once __DIR__ . '/../helpers/MemoryCache.php';
require_once __DIR__ . '/../container/ContainerFactory.php';
require_once __DIR__ . '/../interfaces/CategoryServiceInterface.php';

class CategoryService implements CategoryServiceInterface
{
    private CategoryRepository $categoryRepository;

    public function __construct()
    {
        $this->categoryRepository = ContainerFactory::make('CategoryRepository');
    }

    /**
     * Récupérer toutes les catégories avec cache
     */
    public function getAllCategories(): array
    {
        return MemoryCache::remember('all_categories', function() {
            return $this->categoryRepository->findAll();
        }, 3600); // Cache 1 heure
    }

    /**
     * Récupérer une catégorie par ID avec cache
     */
    public function getCategoryById(int $id): ?array
    {
        $cacheKey = "category_{$id}";
        return MemoryCache::remember($cacheKey, function() use ($id) {
            return $this->categoryRepository->findById($id);
        }, 3600);
    }

    /**
     * Récupérer les catégories populaires avec cache
     */
    public function getPopularCategories(int $limit = 5): array
    {
        $cacheKey = "popular_categories_{$limit}";
        return MemoryCache::remember($cacheKey, function() use ($limit) {
            return $this->categoryRepository->findPopular($limit);
        }, 1800); // Cache 30 minutes
    }
}
