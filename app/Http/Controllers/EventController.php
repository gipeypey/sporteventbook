<?php

namespace App\Http\Controllers;

use App\Interfaces\EventRepositoryInterface;
use Illuminate\Http\Request;

class EventController extends Controller
{
    private EventRepositoryInterface $eventRepository;

    public function __construct(
        EventRepositoryInterface $eventRepository,
    ) {
        $this->eventRepository = $eventRepository;
    }

    public function index(Request $request)
    {
        $search = $request->filled('search') ? trim($request->get('search')) : null;
        $categorySlug = $request->filled('category') ? $request->get('category') : null;
        $status = $request->filled('status') ? $request->get('status') : null;
        $sort = $request->filled('sort') ? $request->get('sort') : 'latest';

        // Get category ID from slug
        $categoryId = null;
        if ($categorySlug) {
            $category = \App\Models\EventCategory::where('slug', $categorySlug)->first();
            $categoryId = $category?->id;
        }

        // Build query
        $query = \App\Models\Event::with(['eventCategory', 'venue', 'prizes', 'bookings']);
        
        // Search filter
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('venue', function($v) use ($search) {
                      $v->where('name', 'like', "%{$search}%");
                  });
            });
        }
        
        // Category filter
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }
        
        // Status filter
        if ($status) {
            if ($status === 'open') {
                $query->where('status', 'open')->where('max_participants', '>', 0);
            } elseif ($status === 'closed') {
                $query->where(function($q) {
                    $q->where('status', 'closed')->orWhere('max_participants', '<=', 0);
                });
            } elseif ($status === 'ended') {
                $query->where('status', 'ended');
            }
        }
        
        // Sorting
        if ($sort === 'soonest') {
            $query->orderBy('start_date', 'asc');
        } elseif ($sort === 'popular') {
            $query->withCount('bookings')->orderBy('bookings_count', 'desc');
        } else {
            // latest
            $query->orderBy('created_at', 'desc');
        }

        $events = $query->paginate(12)->withQueryString();

        return view('events.index', compact('events'));
    }

    public function browse(Request $request)
    {
        return $this->index($request);
    }


    public function show(string $slug)
    {
        $event = $this->eventRepository->getBySlug($slug);

        return view('events.show', compact('event'));
    }
}
