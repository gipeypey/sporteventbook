@props(['topMen', 'topWomen'])

<section id="ranking" class="py-20 bg-gradient-to-b from-gray-50 to-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Section Header -->
        <div class="text-center mb-12">
            <span class="text-violet-600 text-sm font-semibold tracking-wider uppercase">Leaderboard</span>
            <h2 class="text-4xl lg:text-5xl font-bold text-gray-900 mt-2">Top Athletes</h2>
            <p class="text-gray-500 mt-3 max-w-2xl mx-auto">Meet the world's best trail and mountain runners ranked by UTMB Index</p>
        </div>

        <!-- Rankings Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Men's Ranking -->
            <div class="bg-white rounded-2xl p-6 shadow-lg shadow-gray-200/50 border border-gray-100">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-900 flex items-center">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        Men's Ranking
                    </h3>
                    <a href="#" class="text-violet-600 hover:text-violet-700 text-sm font-medium transition">View All</a>
                </div>
                
                <div class="space-y-3">
                    @forelse($topMen as $index => $runner)
                    <div class="flex items-center p-3 rounded-xl hover:bg-gray-50 transition group">
                        <!-- Rank -->
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center mr-4 font-bold text-lg
                            @if($index === 0) bg-gradient-to-br from-yellow-400 to-yellow-500 text-white
                            @elseif($index === 1) bg-gradient-to-br from-gray-300 to-gray-400 text-white
                            @elseif($index === 2) bg-gradient-to-br from-amber-600 to-amber-700 text-white
                            @else bg-gray-100 text-gray-600 @endif">
                            @if($index < 3)
                                {{ $index + 1 }}
                            @else
                                {{ $index + 1 }}
                            @endif
                        </div>
                        
                        <!-- Photo -->
                        <div class="w-12 h-12 rounded-xl overflow-hidden mr-4 bg-gray-100 flex-shrink-0">
                            @if($runner->photo)
                            <img src="{{ asset('assets/images/' . $runner->photo) }}" alt="{{ $runner->name }}" class="w-full h-full object-cover">
                            @else
                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-violet-100 to-indigo-100">
                                <svg class="w-6 h-6 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            @endif
                        </div>
                        
                        <!-- Info -->
                        <div class="flex-1">
                            <div class="font-semibold text-gray-900 group-hover:text-violet-600 transition">{{ $runner->name }}</div>
                            <div class="text-sm text-gray-500">
                                @if($runner->country){{ $runner->country }} • @endif
                                {{ number_format($runner->utmb_index_100m, 0) }} pts
                            </div>
                        </div>
                        
                        <!-- Arrow -->
                        <svg class="w-5 h-5 text-gray-300 group-hover:text-violet-600 group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                    @empty
                    <div class="text-center text-gray-400 py-8">No runners data available</div>
                    @endforelse
                </div>
            </div>

            <!-- Women's Ranking -->
            <div class="bg-white rounded-2xl p-6 shadow-lg shadow-gray-200/50 border border-gray-100">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-900 flex items-center">
                        <div class="w-10 h-10 bg-gradient-to-br from-pink-500 to-pink-600 rounded-xl flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        Women's Ranking
                    </h3>
                    <a href="#" class="text-violet-600 hover:text-violet-700 text-sm font-medium transition">View All</a>
                </div>
                
                <div class="space-y-3">
                    @forelse($topWomen as $index => $runner)
                    <div class="flex items-center p-3 rounded-xl hover:bg-gray-50 transition group">
                        <!-- Rank -->
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center mr-4 font-bold text-lg
                            @if($index === 0) bg-gradient-to-br from-yellow-400 to-yellow-500 text-white
                            @elseif($index === 1) bg-gradient-to-br from-gray-300 to-gray-400 text-white
                            @elseif($index === 2) bg-gradient-to-br from-amber-600 to-amber-700 text-white
                            @else bg-gray-100 text-gray-600 @endif">
                            {{ $index + 1 }}
                        </div>
                        
                        <!-- Photo -->
                        <div class="w-12 h-12 rounded-xl overflow-hidden mr-4 bg-gray-100 flex-shrink-0">
                            @if($runner->photo)
                            <img src="{{ asset('assets/images/' . $runner->photo) }}" alt="{{ $runner->name }}" class="w-full h-full object-cover">
                            @else
                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-pink-100 to-rose-100">
                                <svg class="w-6 h-6 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            @endif
                        </div>
                        
                        <!-- Info -->
                        <div class="flex-1">
                            <div class="font-semibold text-gray-900 group-hover:text-violet-600 transition">{{ $runner->name }}</div>
                            <div class="text-sm text-gray-500">
                                @if($runner->country){{ $runner->country }} • @endif
                                {{ number_format($runner->utmb_index_100m, 0) }} pts
                            </div>
                        </div>
                        
                        <!-- Arrow -->
                        <svg class="w-5 h-5 text-gray-300 group-hover:text-violet-600 group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                    @empty
                    <div class="text-center text-gray-400 py-8">No runners data available</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</section>