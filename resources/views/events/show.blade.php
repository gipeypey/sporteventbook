@extends('layouts.main')

@section('navbar')
    <x-navbar />
@endsection

@section('content')
<!-- Page Header -->
<section class="relative py-12 bg-gradient-to-br from-violet-50 via-white to-indigo-50">
    <div class="absolute inset-0 opacity-30">
        <div class="absolute top-20 left-10 w-72 h-72 bg-violet-300 rounded-full mix-blend-multiply filter blur-3xl animate-blob"></div>
        <div class="absolute top-40 right-10 w-72 h-72 bg-indigo-300 rounded-full mix-blend-multiply filter blur-3xl animate-blob animation-delay-2000"></div>
        <div class="absolute -bottom-8 left-1/2 w-72 h-72 bg-pink-300 rounded-full mix-blend-multiply filter blur-3xl animate-blob animation-delay-4000"></div>
    </div>
    
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Back Button -->
        <a href="{{ route('home') }}" class="inline-flex items-center space-x-2 text-gray-600 hover:text-violet-600 transition mb-6">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            <span>Back to Home</span>
        </a>
        
        <div class="grid lg:grid-cols-2 gap-8">
            <!-- Event Image -->
            <div class="relative rounded-2xl overflow-hidden shadow-xl shadow-gray-200/50 bg-gradient-to-br from-violet-100 to-indigo-100">
                @if($event->image && filter_var($event->image, FILTER_VALIDATE_URL))
                <img src="{{ $event->image }}" alt="{{ $event->title }}" class="w-full h-[400px] object-cover" onerror="this.src='https://images.unsplash.com/photo-1552674605-5d28c4e1902c?q=80&w=1000&auto=format&fit=crop'">
                @elseif($event->image && file_exists(public_path($event->image)))
                <img src="{{ asset($event->image) }}" alt="{{ $event->title }}" class="w-full h-[400px] object-cover" onerror="this.src='https://images.unsplash.com/photo-1552674605-5d28c4e1902c?q=80&w=1000&auto=format&fit=crop'">
                @else
                <div class="w-full h-[400px] flex items-center justify-center bg-gradient-to-br from-violet-200 to-indigo-200">
                    <svg class="w-24 h-24 text-violet-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20a1 1 0 01-1 1H6a1 1 0 01-1-1V5a1 1 0 011-1h10.5l4 4v12z"/>
                    </svg>
                </div>
                @endif
                
                <!-- Status Badge -->
                <div class="absolute top-4 left-4">
                    @if($event->status === 'open')
                    <span class="px-4 py-2 bg-green-500 text-white rounded-full text-sm font-semibold shadow-lg">Open Registration</span>
                    @elseif($event->status === 'closed')
                    <span class="px-4 py-2 bg-red-500 text-white rounded-full text-sm font-semibold shadow-lg">Registration Closed</span>
                    @else
                    <span class="px-4 py-2 bg-gray-500 text-white rounded-full text-sm font-semibold shadow-lg">Event Ended</span>
                    @endif
                </div>
            </div>
            
            <!-- Event Info -->
            <div class="flex flex-col justify-center">
                <!-- Category Badge -->
                @if($event->eventCategory)
                <span class="inline-flex items-center px-3 py-1 bg-violet-100 text-violet-600 rounded-full text-sm font-semibold w-fit mb-4">
                    {{ $event->eventCategory->name }}
                </span>
                @endif
                
                <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">{{ $event->title }}</h1>
                
                <p class="text-gray-600 mb-6 line-clamp-3">{{ $event->description }}</p>
                
                <!-- Quick Stats -->
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-xl">
                        <div class="w-10 h-10 bg-violet-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-900">{{ $event->date?->format('d M Y') ?? 'TBA' }}</p>
                            <p class="text-xs text-gray-500">Event Date</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-xl">
                        <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-900">{{ $event->venue?->name ?? 'TBA' }}</p>
                            <p class="text-xs text-gray-500">Venue</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-xl">
                        <div class="w-10 h-10 bg-pink-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-900">{{ $event->bookings_count ?? 0 }}/{{ $event->max_participants }}</p>
                            <p class="text-xs text-gray-500">Registered</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-xl">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-900">Rp {{ number_format($event->price ?? 0, 0, ',', '.') }}</p>
                            <p class="text-xs text-gray-500">Price</p>
                        </div>
                    </div>
                </div>
                
                <!-- CTA Button -->
                @if($event->status === 'open')
                <a href="{{ route('bookings.show', $event->slug) }}" class="inline-flex items-center justify-center space-x-2 px-8 py-4 bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-700 hover:to-indigo-700 text-white font-semibold rounded-xl transition shadow-lg shadow-violet-500/30">
                    <span>Register Now</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </a>
                @else
                <button disabled class="inline-flex items-center justify-center space-x-2 px-8 py-4 bg-gray-200 text-gray-400 font-semibold rounded-xl cursor-not-allowed">
                    <span>Registration Closed</span>
                </button>
                @endif
            </div>
        </div>
    </div>
</section>

<!-- Event Details Section -->
<section class="py-12 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-8">
                <!-- About Event -->
                <div class="bg-gray-50 rounded-2xl p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">About This Event</h2>
                    <p class="text-gray-600 leading-relaxed">{{ $event->description }}</p>
                </div>
                
                <!-- Event Prizes -->
                @if($event->prizes && $event->prizes->count() > 0)
                <div>
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Event Prizes</h2>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        @foreach($event->prizes as $prize)
                        <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm hover:shadow-md transition">
                            @if($prize->image && file_exists(public_path($prize->image)))
                            <img src="{{ asset($prize->image) }}" alt="{{ $prize->name }}" class="w-full h-32 object-cover rounded-lg mb-3">
                            @else
                            <div class="w-full h-32 bg-gradient-to-br from-violet-100 to-indigo-100 rounded-lg mb-3 flex items-center justify-center">
                                <svg class="w-12 h-12 text-violet-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
                                </svg>
                            </div>
                            @endif
                            <h3 class="font-semibold text-gray-900 text-sm mb-1">{{ $prize->name }}</h3>
                            <p class="text-xs text-gray-500">{{ $prize->given_by ?? 'Event Organizer' }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
                
                <!-- Distances -->
                @if($event->prizes && $event->prizes->count() > 0)
                <div>
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Available Distances</h2>
                    <div class="flex flex-wrap gap-3">
                        @foreach($event->prizes as $prize)
                        <span class="px-4 py-2 bg-violet-50 text-violet-600 rounded-xl font-semibold text-sm">
                            {{ $prize->distance }}K
                        </span>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
            
            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Event Info Card -->
                <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Event Information</h3>
                    
                    <div class="space-y-4">
                        <div class="flex items-start space-x-3">
                            <svg class="w-5 h-5 text-violet-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Date</p>
                                <p class="text-sm text-gray-500">{{ $event->date?->format('d F Y') ?? 'TBA' }}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-3">
                            <svg class="w-5 h-5 text-violet-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            </svg>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Venue</p>
                                <p class="text-sm text-gray-500">{{ $event->venue?->name ?? 'TBA' }}</p>
                                @if($event->venue?->address)
                                <p class="text-xs text-gray-400 mt-1">{{ $event->venue->address }}</p>
                                @endif
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-3">
                            <svg class="w-5 h-5 text-violet-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            </svg>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Location</p>
                                <p class="text-sm text-gray-500">{{ $event->venue?->city ?? 'TBA' }}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-3">
                            <svg class="w-5 h-5 text-violet-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Category</p>
                                <p class="text-sm text-gray-500">{{ $event->eventCategory?->name ?? 'TBA' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Registration Progress -->
                <div class="bg-gradient-to-br from-violet-50 to-indigo-50 rounded-2xl p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Registration Progress</h3>
                    
                    <div class="mb-2">
                        <div class="flex justify-between text-sm mb-2">
                            <span class="text-gray-600">{{ $event->bookings_count ?? 0 }} registered</span>
                            <span class="text-gray-600">{{ $event->max_participants }} slots</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-gradient-to-r from-violet-600 to-indigo-600 h-3 rounded-full transition-all" style="width: {{ $event->max_participants > 0 ? (($event->bookings_count ?? 0) / $event->max_participants) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                    
                    <p class="text-xs text-gray-500 mt-3">
                        @php
                            $remaining = $event->max_participants - ($event->bookings_count ?? 0);
                        @endphp
                        {{ $remaining }} slots remaining
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Related Events -->
@if(isset($relatedEvents) && $relatedEvents->count() > 0)
<section class="py-12 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Related Events</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($relatedEvents->take(3) as $relatedEvent)
            <a href="{{ route('events.show', $relatedEvent) }}" class="group block bg-white rounded-2xl overflow-hidden border border-gray-100 shadow-sm hover:shadow-xl hover:shadow-gray-200/50 transition-all duration-300 hover:-translate-y-1">
                <div class="relative h-40 overflow-hidden bg-gradient-to-br from-violet-100 to-indigo-100">
                    @if($relatedEvent->image && filter_var($relatedEvent->image, FILTER_VALIDATE_URL))
                    <img src="{{ $relatedEvent->image }}" alt="{{ $relatedEvent->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                    @elseif($relatedEvent->image && file_exists(public_path($relatedEvent->image)))
                    <img src="{{ asset($relatedEvent->image) }}" alt="{{ $relatedEvent->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                    @else
                    <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-violet-200 to-indigo-200">
                        <svg class="w-12 h-12 text-violet-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20a1 1 0 01-1 1H6a1 1 0 01-1-1V5a1 1 0 011-1h10.5l4 4v12z"/>
                        </svg>
                    </div>
                    @endif
                </div>
                <div class="p-4">
                    <h3 class="font-bold text-gray-900 text-sm mb-2 group-hover:text-violet-600 transition line-clamp-2">{{ $relatedEvent->title }}</h3>
                    <p class="text-xs text-gray-500">{{ $relatedEvent->date?->format('d M Y') ?? 'TBA' }}</p>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</section>
@endif
@endsection

@section('footer')
    <x-footer />
@endsection