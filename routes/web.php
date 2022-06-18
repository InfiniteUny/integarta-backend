<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Socialite Login URL
// Route::prefix('auth')->name('auth.')->group( function(){
//     Route::get('google', [AuthController::class, 'loginUsingGoogle'])->name('google');
//     Route::get('google/callback', [AuthController::class, 'callbackFromGoogle'])->name('google.callback');
//     Route::get('facebook', [AuthController::class, 'loginUsingFacebook'])->name('facebook');
//     Route::get('facebook/callback', [AuthController::class, 'callbackFromFacebook'])->name('facebook.callback');
// });
