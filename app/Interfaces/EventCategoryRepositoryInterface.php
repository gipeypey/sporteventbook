<?php

namespace App\Interfaces;

interface EventCategoryRepositoryInterface
{
    public function getAllEventCategories(
        ?string $search,
        ?string $slug,
        ?bool $withEventCount,
        ?int $limit,
    );

    public function getBySlug(string $slug);
}
