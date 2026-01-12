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
                    class="rounded-[22px] bg-white border border-gray-200 h-[145px] w-[130px] flex-shrink-0 flex flex-col items-start justify-center p-[14px] transition-colors hover:border-[#FF7A00] cursor-pointer">
                    <div
                        class="bg-gray-50 border-0 flex items-center justify-center mb-5 mt-0 circular-icon w-[60px] h-[60px]">

                        <img src="{{ asset('storage/' . $category->icon) }}" alt="{{ $category->name }}" />
                    </div>
                    <div class="text-left">
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
