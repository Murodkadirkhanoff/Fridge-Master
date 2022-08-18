<?php

use App\Http\Requests\AuthRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;

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

Route::post('login', [\App\Http\Controllers\AuthController::class, 'login']);
Route::get('locations', [\App\Http\Controllers\BookingController::class, 'locations']);
Route::post('calculate', [\App\Http\Controllers\BookingController::class, 'calculate']);
Route::middleware(['auth:sanctum'])->group(function () {

    Route::post('booking', [\App\Http\Controllers\BookingController::class, 'booking']);
    Route::get('my-bookings', [\App\Http\Controllers\BookingController::class, 'myBookings']);
});



