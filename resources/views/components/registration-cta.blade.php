<section class="py-20 bg-gradient-to-br from-violet-600 via-indigo-600 to-purple-700 relative overflow-hidden">
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-0 left-0 w-96 h-96 bg-white rounded-full mix-blend-overlay filter blur-3xl"></div>
        <div class="absolute bottom-0 right-0 w-96 h-96 bg-white rounded-full mix-blend-overlay filter blur-3xl"></div>
    </div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <!-- Left Content -->
            <div class="text-white">
                <h2 class="text-3xl lg:text-4xl font-bold mb-4 leading-tight">
                    Track Your Running Journey
                </h2>
                <p class="text-violet-100 text-lg mb-8 leading-relaxed">
                    Create a free account to access detailed statistics, track your race history, 
                    compare performance with fellow runners, and unlock exclusive rewards.
                </p>
                
                <div class="flex flex-wrap gap-4 mb-8">
                    <div class="flex items-center space-x-2">
                        <div class="w-6 h-6 bg-white/20 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <span class="text-sm">Race History</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-6 h-6 bg-white/20 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <span class="text-sm">Performance Stats</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-6 h-6 bg-white/20 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <span class="text-sm">Exclusive Rewards</span>
                    </div>
                </div>

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

            <!-- Right Content - Dashboard Preview -->
            <div class="relative">
                <div class="bg-white/10 backdrop-blur-md rounded-2xl p-6 shadow-2xl border border-white/20">
                    <!-- Dashboard Header -->
                    <div class="flex items-center space-x-4 mb-6">
                        <div class="w-14 h-14 rounded-xl bg-white/20 flex items-center justify-center">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-white font-bold text-lg">My Dashboard</h3>
                            <p class="text-violet-200 text-sm">Track your progress</p>
                        </div>
                    </div>

                    <!-- Stats Grid -->
                    <div class="grid grid-cols-2 gap-3 mb-4">
                        <div class="bg-white/10 rounded-xl p-4">
                            <div class="text-white text-xl font-bold">0</div>
                            <div class="text-violet-200 text-xs">Races</div>
                        </div>
                        <div class="bg-white/10 rounded-xl p-4">
                            <div class="text-white text-xl font-bold">0</div>
                            <div class="text-violet-200 text-xs">Stones</div>
                        </div>
                        <div class="bg-white/10 rounded-xl p-4">
                            <div class="text-white text-xl font-bold">-</div>
                            <div class="text-violet-200 text-xs">Index</div>
                        </div>
                        <div class="bg-white/10 rounded-xl p-4">
                            <div class="text-white text-xl font-bold">0</div>
                            <div class="text-violet-200 text-xs">Upcoming</div>
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    <div class="bg-white/10 rounded-xl p-4">
                        <h4 class="text-white font-semibold mb-3 text-sm">Recent Activity</h4>
                        <div class="text-violet-200 text-sm">No recent activity</div>
                    </div>
                </div>

                <!-- Decorative Elements -->
                <div class="absolute -top-4 -right-4 w-24 h-24 bg-white/10 rounded-full blur-xl"></div>
                <div class="absolute -bottom-4 -left-4 w-32 h-32 bg-white/10 rounded-full blur-xl"></div>
            </div>
        </div>
    </div>
</section>