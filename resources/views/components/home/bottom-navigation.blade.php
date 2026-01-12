@props(['activeRoute' => 'home'])

<!-- BOTTOM NAVIGATION BAR -->
<div class="fixed bottom-0 left-0 right-0 max-w-[640px] mx-auto bg-white border-t border-gray-200 z-50">
    <div class="grid grid-cols-5 text-xs text-gray-500 py-2">

        <!-- Browse -->
        <a href="{{ url('/') }}"
            class="flex flex-col items-center justify-center cursor-pointer hover:bg-gray-50 rounded-lg p-1 transition-colors">
            <img src="{{ asset('assets/icons/3dcube.svg') }}" class="w-6 h-6 mb-1 {{ $activeRoute === 'home' ? 'nav-icon-active' : '' }}" />
            <span class="{{ $activeRoute === 'home' ? 'text-black font-semibold' : '' }}">Browse</span>
        </a>

        <!-- My Event -->
        <a href="{{ route('bookings.check') }}"
            class="flex flex-col items-center justify-center cursor-pointer hover:bg-gray-50 rounded-lg p-1 transition-colors">
            <img src="{{ asset('assets/icons/event.svg') }}" class="w-6 h-6 mb-1" />
            <span>My Event</span>
        </a>

        <!-- Spacer (tengah) -->
        <div></div>

        <!-- Rewards -->
        <div
            class="flex flex-col items-center justify-center cursor-pointer hover:bg-gray-50 rounded-lg p-1 transition-colors">
            <img src="{{ asset('assets/icons/gift.svg') }}" class="w-6 h-6 mb-1" />
            <span>Rewards</span>
        </div>

        <!-- Helps -->
        <a href="#"
            class="flex flex-col items-center justify-center cursor-pointer hover:bg-gray-50 rounded-lg p-1 transition-colors">
            <img src="{{ asset('assets/icons/setting-2.svg') }}" class="w-6 h-6 mb-1" />
            <span>Helps</span>
        </a>
    </div>

    <!-- Floating Search Button -->
    <div class="absolute -top-6 left-1/2 -translate-x-1/2 cursor-pointer hover:scale-105 transition-transform">
        <a href="{{ route('events.browse') }}">
            <img src="{{ asset('assets/icons/search-nav.svg') }}" class="w-16 h-16" alt="Search" />
        </a>
    </div>
</div>
