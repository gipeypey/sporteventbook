@extends('layouts.main')

@section('content')
    <div class="bg-[#F4F7FF] font-sans">
        <div class="relative flex flex-col w-full max-w-[640px] min-h-screen mx-auto bg-[#F4F7FF] overflow-x-hidden pb-28">
            <!-- Header -->
            <header class="flex justify-between items-center py-6 px-4">
                <a href="{{ url('/') }}">
                    <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow">
                        <img src="{{ asset('assets/icons/arrow-left.svg') }}" alt="Back" />
                    </div>
                </a>
                <h1 class="text-lg font-semibold text-gray-800">Browse by Category</h1>
                <a href="#">
                    <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow">
                        <img src="{{ asset('assets/icons/search-status.svg') }}" alt="Search" />
                    </div>
                </a>
            </header>

            <!-- Main Content -->
            <main class="px-4 mt-2">
                <div class="mb-5">
                    <h2 class="text-xl font-semibold text-gray-900">{{ $category->name }}</h2>
                    <p class="text-gray-600 mt-1">{{ number_format($eventsList->count()) }} Events</p>
                </div>

                <!-- Events Grid - Masonry Layout (2 columns) -->
                @if ($eventsList->count() > 0)
                    <div class="flex space-x-4">
                        @php
                            $left = $eventsList->values()->filter(fn($e, $i) => $i % 2 === 0);
                            $right = $eventsList->values()->filter(fn($e, $i) => $i % 2 === 1);
                        @endphp

                        <!-- Left Column -->
                        <div class="flex flex-col space-y-4 w-1/2">
                            @foreach ($left as $event)
                                <a href="{{ route('events.show', $event->slug) }}"
                                    class="card rounded-2xl overflow-hidden relative text-white shadow-lg w-[168px] h-[250px]">
                                    @php
                                        $img = $event->image
                                            ? Storage::url($event->image)
                                            : asset('assets/images/run3.png');
                                    @endphp
                                    <img src="{{ $img }}" alt="{{ $event->title }}"
                                        class="w-full h-full object-cover" />
                                    <div class="absolute bottom-0 left-0 right-0 p-3">
                                        <h3 class="font-bold leading-tight">{{ Str::limit($event->title, 24) }}</h3>
                                        <div class="flex items-center mt-2">
                                            <img src="{{ asset('assets/icons/dollar-circle.svg') }}" alt="Price"
                                                class="w-5 h-5" />
                                            <p class="ml-1.5 font-semibold text-sm">Rp
                                                {{ number_format($event->price, 0, ',', '.') }}
                                            </p>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>

                        <!-- Right Column -->
                        <div class="flex flex-col space-y-4 w-1/2">
                            @foreach ($right as $event)
                                <a href="{{ route('events.show', $event->slug) }}"
                                    class="card rounded-2xl overflow-hidden relative text-white shadow-lg w-[168px] h-[250px] {{ $loop->first ? '-mt-20' : '' }}">
                                    @php
                                        $img = $event->image
                                            ? Storage::url($event->image)
                                            : asset('assets/images/power3.png');
                                    @endphp
                                    <img src="{{ $img }}" alt="{{ $event->title }}"
                                        class="w-full h-full object-cover" />
                                    <div class="absolute bottom-0 left-0 right-0 p-3">
                                        <h3 class="font-bold leading-tight">{{ Str::limit($event->title, 24) }}</h3>
                                        <div class="flex items-center mt-2">
                                            <img src="{{ asset('assets/icons/dollar-circle.svg') }}" alt="Price"
                                                class="w-5 h-5" />
                                            <p class="ml-1.5 font-semibold text-sm">Rp
                                                {{ number_format($event->price, 0, ',', '.') }}
                                            </p>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @else
                    <!-- No Events Found -->
                    <div class="flex flex-col items-center justify-center py-12 text-center min-h-[300px]">
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Belum Ada Event</h3>
                        <p class="text-gray-600 mb-6 max-w-sm mx-auto">Kategori "{{ $category->name }}" belum memiliki
                            event yang tersedia.</p>
                    </div>
                @endif
            </main>

            <!-- Bottom Button -->
            <div class="absolute bottom-0 left-0 right-0 max-w-lg mx-auto p-4 z-10">
                <button
                    class="mx-auto px-8 py-3 bg-[#D4FF00] text-black font-bold rounded-full flex items-center justify-center text-base shadow-lg">
                    <img src="{{ asset('assets/icons/search-status.svg') }}" alt="Filters"
                        class="w-5 h-5 mr-2 filter brightness-0" />
                    <span>Set Filters (12)</span>
                </button>
            </div>

        </div>
    </div>
@endsection
