<?php

use App\Http\Controllers\Api\OtpBasedAuthController;
use App\Http\Controllers\Api\UserAuthController;
use Illuminate\Http\Request;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::prefix('otp')->group(function () {
    Route::post('send', [OtpBasedAuthController::class, 'sendOtp'])->name('send-otp');
});

Route::prefix('auth')->group(function () {
    Route::post('/login', [OtpBasedAuthController::class, 'login']);
    Route::post('/register', [OtpBasedAuthController::class, 'register']);
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/update-password', [UserAuthController::class, 'updatePassword']);
        Route::post('/update-profile', [UserAuthController::class, 'updateProfile']);
    });
});

