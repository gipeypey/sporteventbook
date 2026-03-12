<x-filament-panels::page>
    <!-- Custom Header -->
    <div class="fi-header">
        <div class="fi-header-content">
            <div class="welcome-message">
                Welcome {{ auth()->user()->name }}
            </div>
            <div class="search-container">
                <input type="text" class="search-input" placeholder="Enter keywords...">
                <div class="notification-icon">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div class="user-profile">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="fi-main">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            {{ $this->getHeaderWidgets() }}
        </div>

        <!-- Chart Filters -->
        <div class="flex flex-wrap gap-4 mb-4 items-center">
            <div class="flex items-center gap-2">
                <label class="text-sm font-medium text-gray-700">Booking Trends:</label>
                <select 
                    id="bookingDaysFilter" 
                    onchange="updateChartDays(this.value)"
                    class="block w-40 rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm"
                >
                    <option value="1">Last 24 Hours</option>
                    <option value="7" selected>Last 7 Days</option>
                    <option value="14">Last 14 Days</option>
                    <option value="30">Last 30 Days</option>
                </select>
            </div>
            
            <div class="flex items-center gap-2">
                <label class="text-sm font-medium text-gray-700">Revenue:</label>
                <select
                    id="revenueMonthsFilter"
                    onchange="updateChartMonths(this.value)"
                    class="block w-40 rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm"
                >
                    <option value="7d">Last 7 Days</option>
                    <option value="14d">Last 14 Days</option>
                    <option value="1">Last Month</option>
                    <option value="3">Last 3 Months</option>
                    <option value="6" selected>Last 6 Months</option>
                </select>
            </div>
        </div>

        <!-- Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            {{ $this->getFooterWidgets() }}
        </div>
    </div>
</x-filament-panels::page>

<!-- Chart Filter Scripts -->
<script>
function updateChartDays(days) {
    // Dispatch event to update BookingChart
    window.livewire.dispatch('updateBookingDays', { days: parseInt(days) });
    
    // Save preference
    localStorage.setItem('bookingDaysFilter', days);
}

function updateChartMonths(months) {
    // Dispatch event to update RevenueChart
    // Handle day-based filters (7d, 14d) and month-based filters (1, 3, 6)
    const value = months;
    const isDays = value.endsWith('d');
    const paramValue = parseInt(value);
    
    if (isDays) {
        window.livewire.dispatch('updateRevenueDays', { days: paramValue });
        localStorage.setItem('revenueMonthsFilter', value);
    } else {
        window.livewire.dispatch('updateRevenueMonths', { months: paramValue });
        localStorage.setItem('revenueMonthsFilter', value);
    }
}

// Load saved preferences on page load
document.addEventListener('DOMContentLoaded', function() {
    const savedDays = localStorage.getItem('bookingDaysFilter') || '7';
    const savedRevenue = localStorage.getItem('revenueMonthsFilter') || '6';

    document.getElementById('bookingDaysFilter').value = savedDays;
    document.getElementById('revenueMonthsFilter').value = savedRevenue;
});
</script>

<!-- Custom Sidebar Override -->
<script src="{{ asset('assets/js/filament-dashboard.js') }}" defer></script>