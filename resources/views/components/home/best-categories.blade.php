@props(['categories'])



<!-- BEST CATEGORIES SECTION -->
<section class="bg-white shadow-sm">
    <div class="px-4 pt-4 pb-0">
        <div class="flex items-center justify-between mb-3">
            <div>
                <div class="font-semibold text-[20px] text-[#111223] leading-[28px]">Best Categories</div>
            </div>
        </div>
    </div>
    <div class="overflow-x-auto overflow-y-hidden hide-scrollbar">
        <div class="flex gap-5 pb-6 pl-4">
            @foreach ($categories as $category)
                <a href="{{ route('category.show', $category->slug) }}"
                    class="rounded-[22px] bg-white border border-gray-200 h-[180px] w-[160px] flex-shrink-0 flex flex-col items-center justify-start pt-5 transition-colors hover:border-[#FF7A00] cursor-pointer">
                    <div
                        class="bg-gray-50 border-0 flex items-center justify-center mb-4 mt-0 rounded-[16px] w-[80px] h-[80px]">

                        <img src="{{ asset('storage/' . $category->icon) }}" alt="{{ $category->name }}" class="w-[45px] h-[45px] object-contain" />
                    </div>
                    <div class="text-center px-3">
                        <div class="font-semibold text-base text-[#18192B] leading-tight mb-1">
                            {{ $category->name }}</div>
                        <div class="text-xs text-[#9BA4A6] font-normal leading-tight">
                            {{ $category->events_count }} Events</div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>
