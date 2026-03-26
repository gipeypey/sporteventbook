<?php

namespace App\Http\Controllers;

use App\Interfaces\EventCategoryRepositoryInterface;
use App\Interfaces\EventRepositoryInterface;
use App\Models\Setting;
use App\Models\News;
use App\Models\Sponsor;
use App\Models\Runner;

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

        // Get latest news (published)
        $latestNews = News::where('is_published', true)
            ->whereNotNull('published_at')
            ->orderBy('published_at', 'desc')
            ->take(3)
            ->get();

        // Get active sponsors ordered by tier and order
        $sponsors = Sponsor::where('is_active', true)
            ->orderBy('tier')
            ->orderBy('order')
            ->get();

        // Get top runners (Men and Women)
        $topMen = Runner::where('gender', 'men')
            ->where('is_active', true)
            ->orderByDesc('utmb_index_100m')
            ->take(3)
            ->get();

        $topWomen = Runner::where('gender', 'women')
            ->where('is_active', true)
            ->orderByDesc('utmb_index_100m')
            ->take(3)
            ->get();

        return view('home', compact(
            'mostPopularEvents',
            'bestCategories',
            'freshEvents',
            'latestNews',
            'sponsors',
            'topMen',
            'topWomen'
        ));
    }
}
