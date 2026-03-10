<?php

use App\Http\Controllers\Api\MidtransController;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:30,1')->post('/midtrans-callback', [MidtransController::class, 'callback']);
