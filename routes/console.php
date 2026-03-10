<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Expire unpaid bookings every minute
Schedule::command('bookings:expire')->everyMinute();

// Send payment reminders every 6 hours
Schedule::command('bookings:send-reminders')->cron('0 */6 * * *');
