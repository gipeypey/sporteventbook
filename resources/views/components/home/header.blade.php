<!-- HEADER SECTION - User Profile & Notifications -->
<div class="flex items-center justify-between px-4 pt-6 pb-4 bg-white">
    <div class="flex items-center gap-3">
        <div class="relative flex items-center justify-center">
            <div
                class="w-[60px] h-[60px] rounded-full border border-gray-200 flex items-center justify-center bg-white transition-colors hover:border-[#FF7A00]">
                <div class="w-[50px] h-[50px] rounded-full bg-gray-50 flex items-center justify-center">
                    <img src="{{ asset('assets/images/medal.png') }}" class="w-8 h-8" />
                </div>
            </div>
        </div>
        <div>
            <div class="text-xs text-gray-400 font-poppins">Howdy,</div>
            <div class="font-medium text-lg leading-tight font-poppins">{{ $userName ?? 'New Winner' }}</div>
        </div>
    </div>
    <div class="flex items-center gap-3">
        <button
            class="w-10 h-10 rounded-full bg-white border border-gray-200 flex items-center justify-center transition-colors hover:border-[#FF7A00]">
            <img src="{{ asset('assets/icons/book.svg') }}" class="w-5 h-5" />
        </button>
        <button
            class="w-10 h-10 rounded-full bg-white border border-gray-200 flex items-center justify-center transition-colors hover:border-[#FF7A00]">
            <img src="{{ asset('assets/icons/notification.svg') }}" class="w-5 h-5" />
        </button>
    </div>
</div>
