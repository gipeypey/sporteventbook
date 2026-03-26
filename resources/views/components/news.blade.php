@props(['news'])

<section id="news" class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Section Header -->
        <div class="flex items-end justify-between mb-10">
            <div>
                <span class="text-violet-600 text-sm font-semibold tracking-wider uppercase">Latest Updates</span>
                <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mt-2">News & Stories</h2>
            </div>
            <a href="#" class="hidden sm:flex items-center space-x-2 text-violet-600 hover:text-violet-700 font-medium transition">
                <span>View All</span>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </a>
        </div>

        <!-- News Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($news as $item)
            <article class="group cursor-pointer">
                <!-- Image -->
                <div class="relative h-48 rounded-2xl overflow-hidden mb-4 bg-gradient-to-br from-violet-100 to-indigo-100">
                    @if($item->image && filter_var($item->image, FILTER_VALIDATE_URL))
                    <img src="{{ $item->image }}" alt="{{ $item->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" onerror="this.parentElement.innerHTML='<div class=\'w-full h-full flex items-center justify-center bg-gradient-to-br from-violet-200 to-indigo-200\'><svg class=\'w-16 h-16 text-violet-300\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'1.5\' d=\'M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z\'/></svg></div>'">
                    @elseif($item->image)
                    <img src="{{ asset($item->image) }}" alt="{{ $item->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" onerror="this.parentElement.innerHTML='<div class=\'w-full h-full flex items-center justify-center bg-gradient-to-br from-violet-200 to-indigo-200\'><svg class=\'w-16 h-16 text-violet-300\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'1.5\' d=\'M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z\'/></svg></div>'">
                    @else
                    <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-violet-200 to-indigo-200">
                        <svg class="w-16 h-16 text-violet-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                        </svg>
                    </div>
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition duration-300"></div>
                </div>

                <!-- Meta -->
                <div class="flex items-center text-sm text-gray-500 mb-2">
                    <span class="px-2.5 py-0.5 bg-violet-100 text-violet-600 rounded-full text-xs font-semibold">News</span>
                    <span class="mx-2">•</span>
                    <span>{{ $item->published_at?->format('d M Y') ?? 'Recent' }}</span>
                </div>

                <!-- Title -->
                <h3 class="text-lg font-bold text-gray-900 group-hover:text-violet-600 transition mb-2 line-clamp-2">
                    {{ $item->title }}
                </h3>

                <!-- Excerpt -->
                <p class="text-gray-500 text-sm line-clamp-2 mb-4">
                    {{ $item->excerpt ?? Str::limit($item->content, 100) }}
                </p>

                <!-- Read More -->
                <div class="flex items-center text-violet-600 font-semibold text-sm group-hover:translate-x-1 transition">
                    Read Story
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </div>
            </article>
            @empty
            <div class="col-span-full">
                <div class="text-center py-12 bg-gray-50 rounded-2xl">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                    </svg>
                    <p class="text-gray-400">No news available at the moment.</p>
                </div>
            </div>
            @endforelse
        </div>

        <!-- Mobile View All -->
        <div class="sm:hidden mt-8 text-center">
            <a href="#" class="inline-flex items-center space-x-2 text-violet-600 hover:text-violet-700 font-medium transition">
                <span>View All News</span>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </a>
        </div>
    </div>
</section>