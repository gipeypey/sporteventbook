<?php

namespace App\Http\Controllers;

use App\Interfaces\EventRepositoryInterface;
use App\Interfaces\EventCategoryRepositoryInterface;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct(
        private EventCategoryRepositoryInterface $categories,
        private EventRepositoryInterface $events
    ) {}

    public function show($slug)
    {
        $category = $this->categories->getBySlug($slug);

        if (!$category) {
            abort(404);
        }

        $eventsList = $this->events->getAllEvents(null, $category->id, null, null, null);

        return view('categories.show', compact('category', 'eventsList'));
    }
}
