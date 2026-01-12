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

    public function browse(Request $request)
    {
        $search = $request->filled('q') ? trim($request->get('q')) : null;
        $categoryId = $request->filled('category_id') ? (int) $request->get('category_id') : null;

        $eventsList = $this->eventRepository->getAllEvents($search, $categoryId, null, null, 24);

        return view('events.browse', [
            'events' => $eventsList,
            'q' => $search,
            'categoryId' => $categoryId
        ]);
    }


    public function show(string $slug)
    {
        $event = $this->eventRepository->getBySlug($slug);

        return view('events.show', compact('event'));
    }
}
