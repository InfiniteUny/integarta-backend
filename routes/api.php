<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\DataController;

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

// Socialite Login URL
Route::prefix('auth')->name('auth.')->group( function(){
    Route::post('login', [AuthController::class, 'loginUsingEmail'])->name('login');
    Route::post('register', [AuthController::class, 'RegisterUsingEmail'])->name('register');
    Route::get('logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('google', [AuthController::class, 'loginUsingGoogle'])->name('google');
    Route::get('google/callback', [AuthController::class, 'callbackFromGoogle'])->name('google.callback');
    Route::get('facebook', [AuthController::class, 'loginUsingFacebook'])->name('facebook');
    Route::get('facebook/callback', [AuthController::class, 'callbackFromFacebook'])->name('facebook.callback');
});
     
Route::middleware('auth:sanctum')->name('data.')->group( function () {
    Route::get('dashboard', [DataController::class, 'dashboard'])->name('dashboard');
    Route::get('myAccount', [DataController::class, 'myAccount'])->name('myAccount');
    Route::get('transactionHistory', [DataController::class, 'transactionHistory'])->name('transactionHistory');
    Route::get('institution', [DataController::class, 'institution'])->name('institution');
    Route::post('request-otp', [DataController::class, 'requestOtp'])->name('requestOtp');
    Route::post('submit-otp', [DataController::class, 'submitOtp'])->name('submitOtp');
    Route::get('edit-type', [DataController::class, 'editType'])->name('editType');
});