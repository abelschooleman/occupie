<?php

use App\Http\Controllers\CreateBookingController;
use App\Http\Controllers\OccupancyRateController;
use App\Http\Controllers\UpdateBookingController;
use App\Http\Middleware\LogRequest;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('daily-occupancy-rates/{date}', [OccupancyRateController::class, 'daily']);
Route::get('monthly-occupancy-rates/{month}', [OccupancyRateController::class, 'monthly']);

Route::post('booking', CreateBookingController::class)
    ->middleware(LogRequest::class)
    ->name('booking.create');

Route::put('booking/{booking}', UpdateBookingController::class)
    ->middleware(LogRequest::class)
    ->name('booking.update');
