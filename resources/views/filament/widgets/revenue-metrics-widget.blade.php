<div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
    @foreach($this->getRevenueData() as $key => $metric)
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-medium text-gray-600">{{ $metric['label'] }}</h3>
            @if($key === 'today')
                <x-heroicon-o-currency-dollar class="w-5 h-5 text-green-500" />
            @elseif($key === 'this_month')
                <x-heroicon-o-calendar class="w-5 h-5 text-blue-500" />
            @elseif($key === 'total')
                <x-heroicon-o-banknotes class="w-5 h-5 text-purple-500" />
            @else
                <x-heroicon-o-chart-bar class="w-5 h-5 text-orange-500" />
            @endif
        </div>
        
        <div class="flex items-baseline gap-2">
            <p class="text-2xl font-bold text-gray-900">
                Rp {{ number_format($metric['amount'], 0, ',', '.') }}
            </p>
        </div>
        
        @if(isset($metric['growth']))
        <div class="mt-2 flex items-center gap-1">
            @if($metric['growth'] > 0)
                <x-heroicon-m-arrow-trending-up class="w-4 h-4 text-green-500" />
                <span class="text-sm text-green-600 font-medium">+{{ number_format(abs($metric['growth']), 1) }}%</span>
            @elseif($metric['growth'] < 0)
                <x-heroicon-m-arrow-trending-down class="w-4 h-4 text-red-500" />
                <span class="text-sm text-red-600 font-medium">{{ number_format(abs($metric['growth']), 1) }}%</span>
            @else
                <x-heroicon-m-minus class="w-4 h-4 text-gray-400" />
                <span class="text-sm text-gray-500 font-medium">0%</span>
            @endif
            <span class="text-sm text-gray-500">vs yesterday</span>
        </div>
        @endif
    </div>
    @endforeach
</div>
