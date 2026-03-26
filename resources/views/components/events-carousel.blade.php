@props(['events' => []])

@if($events->count() > 0)
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Section Header -->
        <div class="flex items-end justify-between mb-10">
            <div>
                <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-2">Popular Events</h2>
                <p class="text-gray-500">Join thousands of runners in amazing events</p>
            </div>
            <a href="{{ route('events.browse') }}" class="hidden sm:inline-flex items-center space-x-2 px-5 py-2.5 bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-700 hover:to-indigo-700 text-white font-semibold rounded-xl transition shadow-lg shadow-violet-500/30">
                <span>View All Events</span>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </a>
        </div>

        <!-- Events Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($events->take(6) as $event)
            <a href="{{ route('events.show', $event) }}" class="group block bg-white rounded-2xl overflow-hidden border border-gray-100 shadow-sm hover:shadow-xl hover:shadow-gray-200/50 transition-all duration-300 hover:-translate-y-1">
                <!-- Event Image -->
                <div class="relative h-48 overflow-hidden bg-gradient-to-br from-violet-100 to-indigo-100">
                    @if($event->image && filter_var($event->image, FILTER_VALIDATE_URL))
                    <img src="{{ $event->image }}" alt="{{ $event->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" onerror="this.parentElement.innerHTML='<div class=\'w-full h-full flex items-center justify-center bg-gradient-to-br from-violet-200 to-indigo-200\'><svg class=\'w-16 h-16 text-violet-300\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'1.5\' d=\'M19 20a1 1 0 01-1 1H6a1 1 0 01-1-1V5a1 1 0 011-1h10.5l4 4v12z\'/></svg></div>'">
                    @elseif($event->image && file_exists(public_path($event->image)))
                    <img src="{{ asset($event->image) }}" alt="{{ $event->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" onerror="this.parentElement.innerHTML='<div class=\'w-full h-full flex items-center justify-center bg-gradient-to-br from-violet-200 to-indigo-200\'><svg class=\'w-16 h-16 text-violet-300\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'1.5\' d=\'M19 20a1 1 0 01-1 1H6a1 1 0 01-1-1V5a1 1 0 011-1h10.5l4 4v12z\'/></svg></div>'">
                    @else
                    <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-violet-200 to-indigo-200">
                        <svg class="w-16 h-16 text-violet-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20a1 1 0 01-1 1H6a1 1 0 01-1-1V5a1 1 0 011-1h10.5l4 4v12z"/>
                        </svg>
                    </div>
                    @endif
                    
                    <!-- Category Badge -->
                    @if($event->eventCategory)
                    <div class="absolute top-3 left-3 px-3 py-1 bg-white/95 backdrop-blur-sm rounded-full text-xs font-semibold text-violet-600 shadow-sm">
                        {{ $event->eventCategory->name }}
                    </div>
                    @endif
                    
                    <!-- Status Badge -->
                    @if($event->status === 'closed' || $event->max_participants <= 0)
                    <div class="absolute top-3 right-3 px-3 py-1 bg-red-500 text-white rounded-full text-xs font-semibold">Full</div>
                    @elseif($event->status === 'ended')
                    <div class="absolute top-3 right-3 px-3 py-1 bg-gray-500 text-white rounded-full text-xs font-semibold">Ended</div>
                    @else
                    <div class="absolute top-3 right-3 px-3 py-1 bg-green-500 text-white rounded-full text-xs font-semibold">Open</div>
                    @endif
                </div>

                <!-- Event Details -->
                <div class="p-5">
                    <h3 class="font-bold text-gray-900 text-lg mb-3 group-hover:text-violet-600 transition line-clamp-2">{{ $event->title }}</h3>
                    
                    <div class="space-y-2 mb-4">
                        <!-- Date -->
                        <div class="flex items-center text-sm text-gray-500">
                            <svg class="w-4 h-4 mr-2 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            {{ $event->start_date?->format('d M Y') ?? 'TBA' }}
                        </div>
                        
                        <!-- Location -->
                        <div class="flex items-center text-sm text-gray-500">
                            <svg class="w-4 h-4 mr-2 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span class="truncate">{{ $event->venue?->name ?? 'TBA' }}</span>
                        </div>
                    </div>

                    <!-- Distances -->
                    <div class="flex flex-wrap gap-2 mb-4">
                        @forelse($event->prizes ?? [] as $prize)
                        <span class="px-2.5 py-1 bg-violet-50 text-violet-600 rounded-lg text-xs font-semibold">
                            {{ $prize->distance }}K
                        </span>
                        @empty
                        <span class="text-gray-400 text-xs">No distances</span>
                        @endforelse
                    </div>

                    <!-- Bottom Bar -->
                    <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                        <span class="text-sm text-gray-500">
                            @if($event->max_participants > 0)
                                {{ $event->bookings_count ?? 0 }}/{{ $event->max_participants }} registered
                            @else
                                Unlimited spots
                            @endif
                        </span>
                        <span class="text-violet-600 font-semibold text-sm group-hover:translate-x-1 transition inline-flex items-center">
                            Details
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </span>
                    </div>
                </div>
            </a>
            @endforeach
        </div>

        <!-- Mobile View All -->
        <div class="sm:hidden mt-8 text-center">
            <a href="{{ route('events.browse') }}" class="inline-flex items-center space-x-2 text-violet-600 hover:text-violet-700 font-medium transition">
                <span>View All Events</span>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </a>
        </div>
    </div>
</section>
@endif