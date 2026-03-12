<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
        <div>
            <h3 class="text-base font-semibold text-gray-900">🏆 Top Performing Events</h3>
            <p class="text-sm text-gray-500 mt-1">Events with highest revenue</p>
        </div>
    </div>

    @if($this->getHasEvents())
    <div class="divide-y divide-gray-200">
        @foreach($this->getTopEvents() as $index => $event)
        <div class="px-6 py-4 hover:bg-gray-50 transition-colors">
            <div class="flex items-center gap-4">
                <!-- Rank -->
                <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm
                    @if($index === 0) bg-yellow-100 text-yellow-700
                    @elseif($index === 1) bg-gray-200 text-gray-700
                    @elseif($index === 2) bg-orange-100 text-orange-700
                    @else bg-gray-100 text-gray-600 @endif">
                    {{ $index + 1 }}
                </div>

                <!-- Event Info -->
                <div class="flex-1 min-w-0">
                    <h4 class="text-sm font-semibold text-gray-900 truncate">{{ $event['title'] }}</h4>
                    <div class="flex items-center gap-3 mt-1 text-xs text-gray-500">
                        <span>{{ $event['category'] }}</span>
                        <span>•</span>
                        <span>{{ $event['venue'] }}</span>
                        <span>•</span>
                        <span>{{ $event['date'] }}</span>
                    </div>
                </div>

                <!-- Revenue -->
                <div class="text-right">
                    <p class="text-sm font-bold text-green-600">
                        Rp {{ number_format($event['revenue'], 0, ',', '.') }}
                    </p>
                    <p class="text-xs text-gray-500">{{ $event['bookings'] }} bookings</p>
                </div>

                <!-- Capacity Bar -->
                <div class="w-32 flex-shrink-0">
                    <div class="flex items-center justify-between text-xs text-gray-500 mb-1">
                        <span>Capacity</span>
                        <span>{{ number_format($event['filled_percentage'], 0) }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="h-2 rounded-full transition-all duration-500
                            @if($event['filled_percentage'] >= 80) bg-green-500
                            @elseif($event['filled_percentage'] >= 50) bg-yellow-500
                            @else bg-red-500 @endif"
                            style="width: {{ min($event['filled_percentage'], 100) }}%">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="px-6 py-3 bg-gray-50 border-t border-gray-200">
        <a href="{{ route('filament.admin.resources.events.index') }}" 
           class="text-sm font-medium text-primary-600 hover:text-primary-700">
            View all events →
        </a>
    </div>
    @else
    <div class="px-6 py-12 text-center">
        <x-heroicon-o-calendar class="w-12 h-12 text-gray-300 mx-auto mb-3" />
        <p class="text-gray-500">No events yet</p>
    </div>
    @endif
</div>
