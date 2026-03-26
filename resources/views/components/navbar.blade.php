<nav class="bg-white/80 backdrop-blur-md border-b border-gray-100 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <!-- Logo -->
            <div class="flex items-center space-x-3">
                <a href="{{ route('home') }}" class="flex items-center space-x-2">
                    <div class="w-10 h-10 bg-gradient-to-br from-violet-600 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-violet-500/30">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <span class="text-xl font-bold bg-gradient-to-r from-violet-600 to-indigo-600 bg-clip-text text-transparent">SportEventBook</span>
                </a>
            </div>

            <!-- Desktop Navigation -->
            <div class="hidden md:flex items-center space-x-1">
                <a href="{{ route('home') }}" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-violet-600 hover:bg-violet-50 rounded-lg transition">Home</a>
                <a href="{{ route('home') }}#events" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-violet-600 hover:bg-violet-50 rounded-lg transition">Events</a>
                <a href="{{ route('home') }}#ranking" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-violet-600 hover:bg-violet-50 rounded-lg transition">Ranking</a>
                <a href="{{ route('home') }}#news" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-violet-600 hover:bg-violet-50 rounded-lg transition">News</a>
            </div>

            <!-- Right Side Actions -->
            <div class="flex items-center space-x-3">
                @auth
                    <a href="{{ auth()->user()->is_admin ? '/admin' : '/venue-owner' }}" class="flex items-center space-x-2 px-4 py-2 bg-violet-600 hover:bg-violet-700 text-white text-sm font-medium rounded-lg transition shadow-lg shadow-violet-500/30">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <span>Dashboard</span>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-violet-600 transition">Sign In</a>
                    <a href="{{ route('register') }}" class="px-4 py-2 text-sm font-medium bg-violet-600 hover:bg-violet-700 text-white rounded-lg transition shadow-lg shadow-violet-500/30">Get Started</a>
                @endauth
            </div>
        </div>
    </div>
</nav>