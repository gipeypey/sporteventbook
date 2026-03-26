@props(['sponsors'])

@if($sponsors->count() > 0)
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Section Header -->
        <div class="text-center mb-10">
            <span class="text-violet-600 text-sm font-semibold tracking-wider uppercase">Our Partners</span>
            <h2 class="text-2xl lg:text-3xl font-bold text-gray-900 mt-2">Trusted by Leading Brands</h2>
        </div>

        <!-- Tier 1 Sponsors (Main) -->
        @if($sponsors->where('tier', 1)->count() > 0)
        <div class="mb-12">
            <div class="flex flex-wrap justify-center items-center gap-8 md:gap-16">
                @foreach($sponsors->where('tier', 1) as $sponsor)
                <div class="group">
                    @if($sponsor->url)
                    <a href="{{ $sponsor->url }}" target="_blank" class="block hover:opacity-80 transition">
                        <img src="{{ asset('assets/images/' . $sponsor->logo) }}" alt="{{ $sponsor->name }}" class="h-16 md:h-20 object-contain grayscale group-hover:grayscale-0 transition">
                    </a>
                    @else
                    <img src="{{ asset('assets/images/' . $sponsor->logo) }}" alt="{{ $sponsor->name }}" class="h-16 md:h-20 object-contain grayscale hover:grayscale-0 transition">
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Tier 2 Sponsors -->
        @if($sponsors->where('tier', 2)->count() > 0)
        <div class="mb-10">
            <div class="flex flex-wrap justify-center items-center gap-6 md:gap-10">
                @foreach($sponsors->where('tier', 2) as $sponsor)
                <div class="group">
                    @if($sponsor->url)
                    <a href="{{ $sponsor->url }}" target="_blank" class="block hover:opacity-80 transition">
                        <img src="{{ asset('assets/images/' . $sponsor->logo) }}" alt="{{ $sponsor->name }}" class="h-10 md:h-12 object-contain grayscale group-hover:grayscale-0 transition">
                    </a>
                    @else
                    <img src="{{ asset('assets/images/' . $sponsor->logo) }}" alt="{{ $sponsor->name }}" class="h-10 md:h-12 object-contain grayscale hover:grayscale-0 transition">
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Tier 3 Sponsors -->
        @if($sponsors->where('tier', 3)->count() > 0)
        <div>
            <div class="flex flex-wrap justify-center items-center gap-4 md:gap-6">
                @foreach($sponsors->where('tier', 3) as $sponsor)
                <div class="group">
                    @if($sponsor->url)
                    <a href="{{ $sponsor->url }}" target="_blank" class="block hover:opacity-80 transition">
                        <img src="{{ asset('assets/images/' . $sponsor->logo) }}" alt="{{ $sponsor->name }}" class="h-8 object-contain grayscale group-hover:grayscale-0 transition">
                    </a>
                    @else
                    <img src="{{ asset('assets/images/' . $sponsor->logo) }}" alt="{{ $sponsor->name }}" class="h-8 object-contain grayscale hover:grayscale-0 transition">
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</section>
@endif