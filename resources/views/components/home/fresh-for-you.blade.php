@props(['events'])

@php
    use Illuminate\Support\Facades\Storage;
@endphp

<!-- FRESH FOR YOU SECTION - Vertical Event Cards -->
<section class="bg-[#F6F8FA] px-4 pt-6 pb-6">
    <div class="flex items-center justify-between mb-3">
        <div>
            <div class="font-semibold text-lg text-[#111223] leading-snug">Fresh For You</div>
            <div class="text-[#A6ACAF] text-sm font-normal leading-tight">Jadikan tantangan berarti</div>
        </div>
        <button
            class="bg-white rounded-full px-5 py-2 font-semibold text-[16px] text-[#111223] shadow-sm tracking-normal mr-4 border border-gray-200 transition-colors hover:border-[#FF7A00]">View
            All</button>
    </div>

    <section class="space-y-4 pr-2">
        @foreach ($events as $event)
            <a href="{{ route('events.show', $event->slug) }}"
                class="bg-white rounded-3xl border border-gray-200 p-4 flex items-center gap-4 h-[164px] cursor-pointer transition-all hover:border-[#FF7A00]">
                <!-- Image -->
                <div class="relative w-[120px] h-[140px] flex-shrink-0 mt-2">
                    <img src="{{ asset('storage/' . $event->image) }}" alt="{{ $event->title }}"
                        class="w-full h-full rounded-3xl object-cover" />
                </div>
                <!-- Text -->
                <div class="flex flex-col justify-center flex-1">
                    <p class="text-base font-semibold text-[#18192B] leading-snug mb-2">
                        {{ Str::limit($event->title, 50) }}
                    </p>
                    <div class="flex items-center gap-2 text-sm mt-2">
                        <img src="{{ asset('assets/icons/dollar-circle-gray.svg') }}" class="w-4 h-4" alt="price" />
                        <span class="font-normal">Rp {{ number_format($event->price, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center gap-2 text-sm mt-1">
                        <img src="{{ asset('assets/icons/crown-grey.svg') }}" class="w-4 h-4" alt="category" />
                        <span class="font-normal">{{ $event->category->name }}</span>
                    </div>
                </div>
            </a>
        @endforeach
    </section>
</section>
