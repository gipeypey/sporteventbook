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

        <!-- Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            {{ $this->getFooterWidgets() }}
        </div>
    </div>
</x-filament-panels::page>

<!-- Custom Sidebar Override -->
<script src="{{ asset('assets/js/filament-dashboard.js') }}" defer></script>