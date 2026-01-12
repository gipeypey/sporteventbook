<?php

namespace App\Filament\Widgets;

use App\Models\EventCategory;
use Filament\Widgets\ChartWidget;

class EventCategoryChart extends ChartWidget
{
    protected ?string $heading = 'Events by Category';

    protected static ?int $sort = 4;

    protected ?string $maxHeight = '350px';

    protected function getData(): array
    {
        $user = auth()->user();

        $categories = EventCategory::withCount([
            'events' => function ($query) use ($user) {
                if ($user && $user->isVenueOwner()) {
                    $query->whereHas('venue', function ($q) use ($user) {
                        $q->where('user_id', $user->id);
                    });
                }
            }
        ])
            ->orderBy('events_count', 'desc')
            ->get();

        $labels = $categories->pluck('name')->toArray();
        $data = $categories->pluck('events_count')->toArray();

        // Define colors for categories
        $colors = ['#f97316', '#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#14b8a6'];

        return [
            'datasets' => [
                [
                    'data' => $data,
                    'backgroundColor' => array_slice($colors, 0, count($data)),
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                ],
            ],
            'scales' => [
                'x' => [
                    'grid' => [
                        'display' => false,
                    ],
                    'ticks' => [
                        'display' => false,
                    ],
                ],
                'y' => [
                    'grid' => [
                        'display' => false,
                    ],
                    'ticks' => [
                        'display' => false,
                    ],
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
