<?php

namespace App\Interfaces;

interface EventRepositoryInterface
{
    public function getAllEvents(
        ?string $search,
        ?int $categoryId,
        ?int $venueId,
        ?bool $isFeatured,
        ?int $limit,
    );

    public function getBySlug(string $slug);
}
