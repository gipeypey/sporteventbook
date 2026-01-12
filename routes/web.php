<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DetailsController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/category/{slug}', [CategoryController::class, 'show'])->name('category.show');
Route::get('/browse-events', [EventController::class, 'browse'])->name('events.browse');
Route::get('/event/{slug}', [EventController::class, 'show'])->name('events.show');

Route::get('/event/booking/{slug}', [BookingController::class, 'show'])->name('bookings.show');
Route::get('/event/booking/{slug}/information', [BookingController::class, 'information'])->name('bookings.information');
Route::post('/event/booking/{slug}/information/save', [BookingController::class, 'saveInformation'])->name('bookings.information.save');
Route::get('/event/booking/{slug}/checkout', [BookingController::class, 'checkout'])->name('bookings.checkout');
Route::post('/event/booking/{slug}/payment', [BookingController::class, 'payment'])->name('bookings.payment');
Route::get('/booking-finished', [BookingController::class, 'finished'])->name('bookings.finished');

Route::get('/check-booking', [BookingController::class, 'check'])->name('bookings.check');
Route::post('/check-booking', [BookingController::class, 'checkBooking'])->name('bookings.check.booking');
Route::get('/event/booking/{slug}/ticket', [BookingController::class, 'ticket'])->name('bookings.ticket');
