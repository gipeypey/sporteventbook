<?php

namespace App\Http\Controllers;

use App\Interfaces\EventCategoryRepositoryInterface;
use App\Interfaces\EventRepositoryInterface;
use App\Models\Setting;

class HomeController extends Controller
{
    public function __construct(
        private EventRepositoryInterface $events,
        private EventCategoryRepositoryInterface $categories
    ) {}

    public function index()
    {
        $mostPopularLimit = (int) Setting::getValue('most_popular_limit', 6);
        $mostPopularEvents = $this->events->getAllEvents(
            null,
            null,
            null,
            true,
            null,
            $mostPopularLimit
        );

        $bestCategories = $this->categories->getAllEventCategories(
            null,
            null,
            true,
            4
        );
        $freshLimit = (int) Setting::getValue('fresh_for_you_limit', 6);

        $freshEvents = $this->events->getAllEvents(
            null,
            null,
            null,
            null,
            $freshLimit
        );

        return view('home', compact('mostPopularEvents', 'bestCategories', 'freshEvents'));
    }
}
