<section class="relative min-h-[90vh] flex items-center overflow-hidden bg-gradient-to-br from-violet-50 via-white to-indigo-50">
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-30">
        <div class="absolute top-20 left-10 w-72 h-72 bg-violet-300 rounded-full mix-blend-multiply filter blur-3xl animate-blob"></div>
        <div class="absolute top-40 right-10 w-72 h-72 bg-indigo-300 rounded-full mix-blend-multiply filter blur-3xl animate-blob animation-delay-2000"></div>
        <div class="absolute -bottom-8 left-1/2 w-72 h-72 bg-pink-300 rounded-full mix-blend-multiply filter blur-3xl animate-blob animation-delay-4000"></div>
    </div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <!-- Left Content -->
            <div class="text-center lg:text-left animate-fade-in">
                <div class="inline-flex items-center px-4 py-2 bg-violet-100 rounded-full mb-6">
                    <span class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></span>
                    <span class="text-sm font-medium text-violet-700">Live Registration Open</span>
                </div>
                
                <h1 class="text-5xl lg:text-7xl font-bold text-gray-900 leading-tight mb-6">
                    Find Your Next
                    <span class="block bg-gradient-to-r from-violet-600 to-indigo-600 bg-clip-text text-transparent">Adventure</span>
                </h1>
                
                <p class="text-lg text-gray-600 mb-8 max-w-xl mx-auto lg:mx-0">
                    Discover and book the best running events worldwide. From 5K fun runs to ultra marathons – your next challenge awaits.
                </p>

                <!-- Search Bar -->
                <div class="bg-white p-2 rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100 animate-fade-in-delay">
                    <form action="{{ route('events.browse') }}" method="GET" class="flex flex-col sm:flex-row gap-2">
                        <div class="flex-1 relative">
                            <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input 
                                type="text" 
                                name="search"
                                placeholder="Search events..." 
                                class="w-full pl-12 pr-4 py-3 bg-gray-50 border-0 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-violet-500 focus:bg-white transition"
                            >
                        </div>
                        <div class="flex gap-2">
                            <select name="category" class="px-4 py-3 bg-gray-50 border-0 rounded-xl text-gray-700 focus:outline-none focus:ring-2 focus:ring-violet-500 cursor-pointer">
                                <option value="">All Categories</option>
                                @foreach(\App\Models\EventCategory::all() as $category)
                                <option value="{{ $category->slug }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <button 
                                type="submit"
                                class="px-6 py-3 bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-700 hover:to-indigo-700 text-white font-semibold rounded-xl transition shadow-lg shadow-violet-500/30 flex items-center space-x-2"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Quick Stats -->
                <div class="flex flex-wrap justify-center lg:justify-start gap-8 mt-10 animate-fade-in-delay-2">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-gray-900">500+</div>
                        <div class="text-sm text-gray-500">Events</div>
                    </div>
                    <div class="w-px bg-gray-200"></div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-gray-900">50K+</div>
                        <div class="text-sm text-gray-500">Runners</div>
                    </div>
                    <div class="w-px bg-gray-200"></div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-gray-900">100+</div>
                        <div class="text-sm text-gray-500">Cities</div>
                    </div>
                </div>
            </div>

            <!-- Right Content - Hero Image -->
            <div class="relative animate-fade-in-delay">
                <div class="relative">
                    <!-- Main Image -->
                    <div class="relative rounded-3xl overflow-hidden shadow-2xl shadow-gray-300/50">
                        <img 
                            src="{{ asset('assets/images/hero/hero-run.png') }}" 
                            alt="Runner" 
                            class="w-full h-[500px] object-cover"
                            onerror="this.src='assets/images/hero/hero-run.png'"
                        >
                        <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                        
                        <!-- Floating Card -->
                        <div class="absolute bottom-6 left-6 right-6 bg-white/95 backdrop-blur-sm rounded-2xl p-4 shadow-lg">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 bg-gradient-to-br from-green-400 to-emerald-500 rounded-xl flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="font-semibold text-gray-900">Ready to Start?</div>
                                    <div class="text-sm text-gray-500">Join your first event today</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Decorative Elements -->
                    <div class="absolute -top-4 -right-4 w-24 h-24 bg-gradient-to-br from-violet-400 to-indigo-400 rounded-2xl -z-10"></div>
                    <div class="absolute -bottom-4 -left-4 w-32 h-32 bg-gradient-to-br from-pink-400 to-rose-400 rounded-full -z-10"></div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    @keyframes blob {
        0%, 100% { transform: translate(0, 0) scale(1); }
        33% { transform: translate(30px, -50px) scale(1.1); }
        66% { transform: translate(-20px, 20px) scale(0.9); }
    }
    .animate-blob { animation: blob 7s infinite; }
    .animation-delay-2000 { animation-delay: 2s; }
    .animation-delay-4000 { animation-delay: 4s; }
</style>