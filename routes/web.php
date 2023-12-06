<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Authentification;
use App\Http\Controllers\Homepage;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [Homepage::class, 'index'])->name('homepage_index');

Route::get('/login', [Authentification::class, 'login'])->name('login');
Route::get('/register', [Authentification::class, 'register'])->middleware('throttle:12,1')->name('register');
Route::post('/auth/save', [Authentification::class, 'save'])->name('auth.save');
Route::post('/auth/check', [Authentification::class, 'check'])->name('auth.check');
Route::get('/auth/logout', [Authentification::class, 'logout'])->name('auth.logout');

Route::get('/games', function () {
    return view('game_pages/games_index');
})->name('games_index')->middleware(['auth', 'verified']);

/*

Email verification routes
*/

Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
 
    return redirect()->route('games_index');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
 
    return back()->with('message', 'Verifikācijas saite nosūtīta!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');