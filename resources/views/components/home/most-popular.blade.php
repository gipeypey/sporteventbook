@props(['events'])

@php
    use Illuminate\Support\Facades\Storage;
@endphp

<!-- MOST POPULAR SECTION - Event Cards -->
<section class="bg-[#F6F8FA] pt-6 pb-2">
    <div class="flex items-center justify-between mb-4 px-4">
        <div>
            <div class="font-semibold text-[20px] text-[#111223] leading-[28px]">Most Popular</div>
            <div class="text-[#A6ACAF] text-sm font-normal leading-tight">Didukung oleh negara</div>
        </div>
        <button
            class="bg-white rounded-full px-5 py-2 font-semibold text-[16px] text-[#111223] shadow-sm tracking-tight border border-gray-200 transition-colors hover:border-[#FF7A00]">View
            All</button>
    </div>
    <div class="overflow-x-auto overflow-y-hidden hide-scrollbar min-h-[300px]">
        <div class="flex gap-4 pb-4 pl-4">
            @foreach ($events as $index => $event)
                <div
                    class="rounded-3xl overflow-hidden shadow-lg h-[280px] w-[200px] flex-shrink-0 bg-white relative border border-gray-200 transition-colors">
                    @if ($event->image)
                        <img src="{{ Storage::url($event->image) }}" class="absolute inset-0 w-full h-full object-cover"
                            alt="{{ $event->title }}" />
                    @else
                        @php
                            $defaultImages = [
                                'assets/images/run.png',
                                'assets/images/boys-boxing.png',
                                'assets/images/women-tennis.png',
                                'assets/images/run.png',
                                'assets/images/boys-boxing.png',
                                'assets/images/women-tennis.png',
                            ];
                            $imageIndex = $index % count($defaultImages);
                        @endphp
                        <img src="{{ asset($defaultImages[$imageIndex]) }}"
                            class="absolute inset-0 w-full h-full object-cover" />
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-b from-transparent via-black/70 to-black"></div>
                    <div class="absolute inset-0 flex flex-col justify-end p-4">
                        <div class="text-white text-sm font-bold mb-2 leading-tight">
                            {{ Str::limit($event->title, 40) }}</div>
                        <div class="flex items-center gap-1 text-white text-xs font-semibold mb-1">
                            <img src="{{ asset('assets/icons/dollar-circle.svg') }}" class="w-3 h-3" />
                            Rp {{ number_format($event->total_prize, 0, ',', '.') }}
                        </div>
                        <div class="flex items-center gap-1 text-white text-xs font-normal mb-3">
                            <img src="{{ asset('assets/icons/crown.svg') }}"
                                class="w-3 h-3" />{{ $event->category->name }}
                        </div>
                        <a href="{{ route('events.show', $event->slug) }}"
                            class="w-full bg-gradient-to-r from-[#F0FF2B] to-[#CDFB35] text-[#18192B] rounded-full py-2 text-sm font-bold flex items-center justify-center">See
                            Event</a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
