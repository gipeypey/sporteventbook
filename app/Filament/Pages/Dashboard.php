<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\BookingChart;
use App\Filament\Widgets\BookingStatusChart;
use App\Filament\Widgets\DashboardStats;
use App\Filament\Widgets\EventCategoryChart;
use App\Filament\Widgets\RevenueChart;
use Filament\Actions\Action;
use Filament\Pages\Dashboard as BaseDashboard;
use BackedEnum;

class Dashboard extends BaseDashboard
{
    protected static BackedEnum | string | null $navigationIcon = 'heroicon-o-home';
    protected string $view = 'filament.pages.dashboard';

    protected function getHeaderWidgets(): array
    {
        return $this->getStatsWidgets();
    }

    protected function getFooterWidgets(): array
    {
        return $this->getChartWidgets();
    }

    protected function getHeaderActions(): array
    {
        return [
            $this->createSearchAction(),
            $this->createNotificationAction(),
        ];
    }

    private function getStatsWidgets(): array
    {
        return [DashboardStats::class];
    }

    private function getChartWidgets(): array
    {
        return [
            BookingChart::class,
            RevenueChart::class,
            BookingStatusChart::class,
            EventCategoryChart::class,
        ];
    }

    private function createSearchAction(): Action
    {
        return Action::make('search')
            ->label('Enter keywords...')
            ->icon('heroicon-o-magnifying-glass')
            ->color('gray')
            ->size('lg')
            ->extraAttributes([
                'class' => 'search-input',
                'placeholder' => 'Enter keywords...',
            ]);
    }

    private function createNotificationAction(): Action
    {
        return Action::make('notifications')
            ->icon('heroicon-o-bell')
            ->color('gray')
            ->size('lg')
            ->extraAttributes([
                'class' => 'notification-icon',
            ]);
    }
}
