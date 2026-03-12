<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
    <div class="mb-6">
        <h3 class="text-base font-semibold text-gray-900">📈 Conversion Analytics</h3>
        <p class="text-sm text-gray-500 mt-1">Booking funnel and conversion metrics</p>
    </div>

    @php
        $data = $this->getConversionData();
    @endphp

    <!-- Conversion Funnel -->
    <div class="space-y-4 mb-6">
        <!-- Total Bookings -->
        <div>
            <div class="flex items-center justify-between text-sm mb-2">
                <span class="text-gray-600">Total Bookings</span>
                <span class="font-semibold text-gray-900">{{ $data['total_bookings'] }}</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-3">
                <div class="bg-blue-500 h-3 rounded-full transition-all duration-500" style="width: 100%"></div>
            </div>
        </div>

        <!-- Successful Payments -->
        <div>
            <div class="flex items-center justify-between text-sm mb-2">
                <span class="text-gray-600">Successful Payments</span>
                <span class="font-semibold text-green-600">{{ $data['successful'] }} ({{ number_format($data['success_rate'], 1) }}%)</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-3">
                <div class="bg-green-500 h-3 rounded-full transition-all duration-500" style="width: {{ $data['success_rate'] }}%"></div>
            </div>
        </div>

        <!-- Pending Payments -->
        <div>
            <div class="flex items-center justify-between text-sm mb-2">
                <span class="text-gray-600">Pending Payments</span>
                <span class="font-semibold text-yellow-600">{{ $data['pending'] }} ({{ number_format($data['pending_rate'], 1) }}%)</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-3">
                <div class="bg-yellow-500 h-3 rounded-full transition-all duration-500" style="width: {{ $data['pending_rate'] }}%"></div>
            </div>
        </div>

        <!-- Failed/Cancelled -->
        <div>
            <div class="flex items-center justify-between text-sm mb-2">
                <span class="text-gray-600">Failed/Cancelled</span>
                <span class="font-semibold text-red-600">{{ $data['failed'] }} ({{ number_format($data['failure_rate'], 1) }}%)</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-3">
                <div class="bg-red-500 h-3 rounded-full transition-all duration-500" style="width: {{ $data['failure_rate'] }}%"></div>
            </div>
        </div>
    </div>

    <!-- Key Metrics Grid -->
    <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-200">
        <!-- Check-in Rate -->
        <div class="text-center p-3 bg-purple-50 rounded-lg">
            <div class="text-2xl font-bold text-purple-600">{{ number_format($data['check_in_rate'], 1) }}%</div>
            <div class="text-xs text-purple-700 mt-1">Check-in Rate</div>
            <div class="text-xs text-purple-600 mt-0.5">{{ $data['checked_in'] }} checked in</div>
        </div>

        <!-- Capacity Utilization -->
        <div class="text-center p-3 bg-orange-50 rounded-lg">
            <div class="text-2xl font-bold text-orange-600">{{ number_format($data['capacity_utilization'], 1) }}%</div>
            <div class="text-xs text-orange-700 mt-1">Capacity Used</div>
            <div class="text-xs text-orange-600 mt-0.5">{{ $data['total_capacity'] }} total slots</div>
        </div>
    </div>

    <!-- Insights -->
    <div class="mt-4 p-4 bg-blue-50 rounded-lg">
        <div class="flex items-start gap-3">
            <x-heroicon-o-light-bulb class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" />
            <div class="text-sm text-blue-800">
                @if($data['success_rate'] >= 80)
                    <strong>Great job!</strong> You have a high payment success rate of {{ number_format($data['success_rate'], 1) }}%.
                @elseif($data['success_rate'] >= 60)
                    <strong>Good performance.</strong> Consider sending payment reminders to {{ $data['pending'] }} pending bookings.
                @else
                    <strong>Attention needed.</strong> {{ number_format($data['pending_rate'], 1) }}% of bookings are still pending payment.
                @endif
            </div>
        </div>
    </div>
</div>
