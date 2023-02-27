<?php

use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OtpBasedAuthController;
use App\Http\Controllers\Api\ProblemController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\UserAuthController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\DistrictController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Livewire\Productlist;


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

Route::middleware('auth:sanctum')->get('/user', [UserAuthController::class, 'Userme']);


Route::prefix('otp')->group(function () {
    Route::post('send', [OtpBasedAuthController::class, 'generate'])->name('generate-otp');
    Route::post('verify', [OtpBasedAuthController::class, 'verifyOtp'])->name('verify-otp');
    Route::post('forgot-password', [OtpBasedAuthController::class, 'forgotPasswordOtp'])->name('forget-password-otp');
    Route::post('update-forgot-password', [OtpBasedAuthController::class, 'updateForgotPasswordOtp'])->name('update-forget-password-otp');
});

Route::prefix('auth')->group(function () {
    Route::post('/login', [OtpBasedAuthController::class, 'login']);
    Route::post('/register', [OtpBasedAuthController::class, 'register'])->name('login');
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/update-password', [UserAuthController::class, 'updatePassword']);
        Route::post('/update-profile', [UserAuthController::class, 'updateProfile']);
    });
});

//Services
Route::prefix('services')->middleware(['auth:sanctum'])->group(function () {
    Route::post('/create', [ServiceController::class, 'store']);
    Route::get('/list', [ServiceController::class, 'show']);
    Route::post('/update/{id}', [ServiceController::class, 'update']);
    Route::delete('/delete/{id}', [ServiceController::class, 'destroy']);
});
//District
Route::get('/districts/list', [DistrictController::class, 'list']);
//Brand
Route::prefix('brand')->middleware(['auth:sanctum'])->group(function () {
    Route::post('/create', [BrandController::class, 'store']);
    Route::get('/list', [BrandController::class, 'show']);
    Route::post('/update/{id}', [BrandController::class, 'update']);
    Route::delete('/delete/{id}', [BrandController::class, 'destroy']);
});

Route::prefix('problems')->middleware(['auth:sanctum'])->group(function () {
    Route::post('/create', [ProblemController::class, 'store']);
    Route::get('/list', [ProblemController::class, 'index']);
    Route::post('/update/{id}', [ProblemController::class, 'update']);
    Route::delete('/delete/{id}', [ProblemController::class, 'destroy']);
    Route::get('/show/{serviceid}', [ProblemController::class, 'show']);
});


Route::prefix('cart')->middleware(['auth:sanctum'])->group(function () {
    Route::post('/add/{id}', [CartController::class, 'addToCart']);
    Route::get('/list', [ProblemController::class, 'index']);
    Route::post('/update/{id}', [ProblemController::class, 'update']);
    Route::delete('/delete/{id}', [ProblemController::class, 'destroy']);
    Route::get('/show/{serviceid}', [ProblemController::class, 'show']);
    Route::get('/count', [ProblemController::class, 'cartCount']);
});

// Route::post('/add/{$id},Productlist::class')->name('addTocart');