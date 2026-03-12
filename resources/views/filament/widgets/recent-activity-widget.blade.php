<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
        <div>
            <h3 class="text-base font-semibold text-gray-900">🔔 Recent Activity</h3>
            <p class="text-sm text-gray-500 mt-1">Latest bookings in real-time</p>
        </div>
        <a href="{{ route('filament.admin.resources.bookings.index') }}" 
           class="text-sm font-medium text-primary-600 hover:text-primary-700">
            View all
        </a>
    </div>

    @if($this->getHasActivities())
    <div class="divide-y divide-gray-200">
        @foreach($this->getRecentActivities() as $activity)
        <div class="px-6 py-4 hover:bg-gray-50 transition-colors">
            <div class="flex items-center gap-4">
                <!-- Icon based on status -->
                <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center
                    @if($activity['status_color'] === 'success') bg-green-100
                    @elseif($activity['status_color'] === 'warning') bg-yellow-100
                    @elseif($activity['status_color'] === 'danger') bg-red-100
                    @else bg-gray-100 @endif">
                    
                    @if($activity['checked_in'])
                        <x-heroicon-o-check-circle class="w-5 h-5 
                            @if($activity['status_color'] === 'success') text-green-600
                            @else text-gray-600 @endif" />
                    @elseif($activity['status_color'] === 'success')
                        <x-heroicon-o-check-badge class="w-5 h-5 text-green-600" />
                    @elseif($activity['status_color'] === 'warning')
                        <x-heroicon-o-clock class="w-5 h-5 text-yellow-600" />
                    @else
                        <x-heroicon-o-x-circle class="w-5 h-5 text-red-600" />
                    @endif
                </div>

                <!-- Activity Info -->
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <p class="text-sm font-medium text-gray-900">{{ $activity['customer_name'] }}</p>
                        <span class="text-xs text-gray-400">•</span>
                        <p class="text-xs text-gray-500 truncate">{{ $activity['event_title'] }}</p>
                    </div>
                    <div class="flex items-center gap-3 mt-1 text-xs">
                        <span class="text-gray-500">{{ $activity['code'] }}</span>
                        <span class="text-gray-300">•</span>
                        <span class="text-gray-500">{{ $activity['time_diff'] }}</span>
                    </div>
                </div>

                <!-- Amount -->
                <div class="text-right">
                    <p class="text-sm font-semibold text-gray-900">
                        Rp {{ number_format($activity['amount'], 0, ',', '.') }}
                    </p>
                </div>

                <!-- Status Badge -->
                <div class="flex-shrink-0">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        @if($activity['status_color'] === 'success') bg-green-100 text-green-800
                        @elseif($activity['status_color'] === 'warning') bg-yellow-100 text-yellow-800
                        @elseif($activity['status_color'] === 'danger') bg-red-100 text-red-800
                        @else bg-gray-100 text-gray-800 @endif">
                        {{ $activity['status'] }}
                    </span>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="px-6 py-12 text-center">
        <x-heroicon-o-bell class="w-12 h-12 text-gray-300 mx-auto mb-3" />
        <p class="text-gray-500">No recent activity</p>
    </div>
    @endif
</div>
