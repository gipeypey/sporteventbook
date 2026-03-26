@extends('layouts.main')

@section('navbar')
    <x-navbar />
@endsection

@section('content')
<!-- Page Header -->
<section class="relative py-20 bg-gradient-to-br from-violet-50 via-white to-indigo-50">
    <div class="absolute inset-0 opacity-30">
        <div class="absolute top-20 left-10 w-72 h-72 bg-violet-300 rounded-full mix-blend-multiply filter blur-3xl animate-blob"></div>
        <div class="absolute top-40 right-10 w-72 h-72 bg-indigo-300 rounded-full mix-blend-multiply filter blur-3xl animate-blob animation-delay-2000"></div>
        <div class="absolute -bottom-8 left-1/2 w-72 h-72 bg-pink-300 rounded-full mix-blend-multiply filter blur-3xl animate-blob animation-delay-4000"></div>
    </div>
    
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <span class="text-violet-600 text-sm font-semibold tracking-wider uppercase">Explore</span>
        <h1 class="text-4xl lg:text-5xl font-bold text-gray-900 mt-2 mb-4">Browse All Events</h1>
        <p class="text-gray-600 text-lg max-w-2xl mx-auto">Find your next trail running adventure. From 5K fun runs to ultra marathons.</p>
    </div>
</section>

<!-- Filters & Search Section -->
<section class="sticky top-16 z-40 bg-white/80 backdrop-blur-md border-b border-gray-100 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <form action="{{ route('events.browse') }}" method="GET" class="flex flex-col lg:flex-row gap-4">
            <!-- Search Input -->
            <div class="flex-1 relative">
                <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input 
                    type="text" 
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search events by name or location..." 
                    class="w-full pl-12 pr-4 py-3 bg-gray-50 border-0 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-violet-500 focus:bg-white transition"
                >
            </div>

            <!-- Category Filter -->
            <div class="relative">
                <select 
                    name="category" 
                    class="w-full lg:w-48 px-4 py-3 bg-gray-50 border-0 rounded-xl text-gray-700 focus:outline-none focus:ring-2 focus:ring-violet-500 cursor-pointer appearance-none"
                    style="background-image: url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236B7280'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E\"); background-repeat: no-repeat; background-position: right 0.75rem center; background-size: 1.25rem;"
                >
                    <option value="">All Categories</option>
                    @foreach(\App\Models\EventCategory::all() as $category)
                    <option value="{{ $category->slug }}" {{ request('category') == $category->slug ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <!-- Status Filter -->
            <div class="relative">
                <select 
                    name="status" 
                    class="w-full lg:w-40 px-4 py-3 bg-gray-50 border-0 rounded-xl text-gray-700 focus:outline-none focus:ring-2 focus:ring-violet-500 cursor-pointer appearance-none"
                    style="background-image: url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236B7280'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E\"); background-repeat: no-repeat; background-position: right 0.75rem center; background-size: 1.25rem;"
                >
                    <option value="">All Status</option>
                    <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open</option>
                    <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Full</option>
                    <option value="ended" {{ request('status') == 'ended' ? 'selected' : '' }}>Ended</option>
                </select>
            </div>

            <!-- Sort -->
            <div class="relative">
                <select 
                    name="sort" 
                    class="w-full lg:w-48 px-4 py-3 bg-gray-50 border-0 rounded-xl text-gray-700 focus:outline-none focus:ring-2 focus:ring-violet-500 cursor-pointer appearance-none"
                    style="background-image: url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236B7280'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E\"); background-repeat: no-repeat; background-position: right 0.75rem center; background-size: 1.25rem;"
                >
                    <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest</option>
                    <option value="soonest" {{ request('sort') == 'soonest' ? 'selected' : '' }}>Soonest</option>
                    <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Most Popular</option>
                </select>
            </div>

            <!-- Submit Button -->
            <button 
                type="submit"
                class="px-6 py-3 bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-700 hover:to-indigo-700 text-white font-semibold rounded-xl transition shadow-lg shadow-violet-500/30 flex items-center justify-center space-x-2"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
                <span>Filter</span>
            </button>
        </form>
    </div>
</section>

<!-- Events Grid -->
<section class="py-12 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Results Count -->
        <div class="flex items-center justify-between mb-8">
            <p class="text-gray-600">
                <span class="font-bold text-gray-900">{{ $events->total() }}</span> 
                events found
            </p>
            <div class="flex items-center space-x-2 text-sm text-gray-500">
                <span>View:</span>
                <button class="p-2 rounded-lg bg-violet-100 text-violet-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                    </svg>
                </button>
                <button class="p-2 rounded-lg hover:bg-gray-100 text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Events Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($events as $event)
            <a href="{{ route('events.show', $event) }}" class="group block bg-white rounded-2xl overflow-hidden border border-gray-100 shadow-sm hover:shadow-xl hover:shadow-gray-200/50 transition-all duration-300 hover:-translate-y-1">
                <!-- Event Image -->
                <div class="relative h-48 overflow-hidden bg-gradient-to-br from-violet-100 to-indigo-100">
                    @if($event->image_url)
                    <img src="{{ $event->image_url }}" alt="{{ $event->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" onerror="this.parentElement.innerHTML='<div class=\'w-full h-full flex items-center justify-center bg-gradient-to-br from-violet-200 to-indigo-200\'><svg class=\'w-16 h-16 text-violet-300\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'1.5\' d=\'M19 20a1 1 0 01-1 1H6a1 1 0 01-1-1V5a1 1 0 011-1h10.5l4 4v12z\'/></svg></div>'">
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
{{ $event->date?->format('d M Y') ?? 'TBA' }}
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
            @empty
            <div class="col-span-full">
                <div class="text-center py-16 bg-gray-50 rounded-2xl">
                    <svg class="w-20 h-20 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">No events found</h3>
                    <p class="text-gray-500 mb-6">Try adjusting your filters or search criteria</p>
                    <a href="{{ route('events.browse') }}" class="inline-flex items-center space-x-2 text-violet-600 hover:text-violet-700 font-medium transition">
                        <span>Clear all filters</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </a>
                </div>
            </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($events->hasPages())
        <div class="mt-12">
            {{ $events->links('vendor.pagination.tailwind') }}
        </div>
        @endif
    </div>
</section>

<!-- Registration CTA -->
<section class="py-20 bg-gradient-to-br from-violet-600 via-indigo-600 to-purple-700 relative overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-0 left-0 w-96 h-96 bg-white rounded-full mix-blend-overlay filter blur-3xl"></div>
        <div class="absolute bottom-0 right-0 w-96 h-96 bg-white rounded-full mix-blend-overlay filter blur-3xl"></div>
    </div>
    <div class="relative max-w-4xl mx-auto px-4 text-center">
        <h2 class="text-3xl lg:text-4xl font-bold text-white mb-4">Ready to Start Your Adventure?</h2>
        <p class="text-violet-100 text-lg mb-8">Create a free account to access detailed statistics, track your race history, and unlock exclusive rewards.</p>
        @guest
        <a href="{{ route('register') }}" class="inline-flex items-center space-x-2 bg-white hover:bg-gray-100 text-violet-600 px-8 py-4 rounded-xl font-bold text-lg transition shadow-lg shadow-violet-900/30">
            <span>Create Free Account</span>
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
            </svg>
        </a>
        @else
        <a href="{{ auth()->user()->is_admin ? '/admin' : '/venue-owner' }}" class="inline-flex items-center space-x-2 bg-white hover:bg-gray-100 text-violet-600 px-8 py-4 rounded-xl font-bold text-lg transition shadow-lg shadow-violet-900/30">
            <span>Go to Dashboard</span>
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
            </svg>
        </a>
        @endguest
    </div>
</section>
@endsection

@section('footer')
    <x-footer />
@endsection