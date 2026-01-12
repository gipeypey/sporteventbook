<?php

namespace App\Repositories;

use App\Interfaces\EventCategoryRepositoryInterface;
use App\Models\EventCategory;

class EventCategoryRepository implements EventCategoryRepositoryInterface
{
    public function getAllEventCategories(
        ?string $search,
        ?string $slug,
        ?bool $withEventCount,
        ?int $limit,
    ) {
        $query = EventCategory::where(function ($query) use ($search, $slug) {
            if ($search) {
                $query->search($search);
            }

            if ($slug) {
                $query->bySlug($slug);
            }
        })->withCount('events');

        if ($limit) {
            $query->limit($limit);
        }

        return $query->get();
    }

    public function getBySlug(string $slug)
    {
        return EventCategory::where('slug', $slug)->first();
    }
}
