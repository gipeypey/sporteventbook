<?php

namespace App\Providers;

use App\Interfaces\BookingRepositoryInterface;
use App\Interfaces\EventCategoryRepositoryInterface;
use App\Interfaces\EventRepositoryInterface;
use App\Repositories\BookingRepository;
use App\Repositories\EventCategoryRepository;
use App\Repositories\EventRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    
    public function register(): void
    {
        $this->app->bind(EventCategoryRepositoryInterface::class, EventCategoryRepository::class);
        $this->app->bind(BookingRepositoryInterface::class, BookingRepository::class);
        $this->app->bind(EventRepositoryInterface::class, EventRepository::class);
    }


    public function boot(): void
    {
        
    }
}
