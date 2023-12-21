<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Authentification;
use App\Http\Controllers\AuthGoogle;
use App\Http\Controllers\Homepage;
use App\Http\Controllers\UserProfile;
use App\Http\Controllers\GamesInfo;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

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

// !!! debug database logs
Event::listen('Illuminate\Database\Events\QueryExecuted', function ($query) {
    Log::info( json_encode($query->sql) );
    Log::info( json_encode($query->bindings) );
    Log::info( json_encode($query->time)   );
});

Route::get('/', [Homepage::class, 'index'])->name('homepage_index');

// Local authentication routes
Route::get('/login', [Authentification::class, 'login'])->name('login');
Route::get('/register', [Authentification::class, 'register'])->middleware('throttle:12,1')->name('register');
Route::post('/auth/save', [Authentification::class, 'save'])->name('auth.save');
Route::post('/auth/check', [Authentification::class, 'check'])->name('auth.check');
Route::get('/auth/logout', [Authentification::class, 'logout'])->name('auth.logout');

// Google authentication routes
Route::get('/auth/google/callback', [AuthGoogle::class, 'callback']);
Route::get('/auth/google/redirect', [AuthGoogle::class, 'redirect'])->name('auth.google.redirect');
Route::get('/auth/google/create-name', [AuthGoogle::class, 'create_name_index'])->name('auth.create_name_index');
Route::post('/auth/google/name/check', [AuthGoogle::class, 'create_name_check'])->name('auth.create_name_check');

// Middleware group for authenticated users with verified emails
Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/games', [GamesInfo::class, 'index'])->name('games_index');
    Route::get('/games/loadmore', [GamesInfo::class, 'index_load_games'])->name('games_index.loadmore');
    Route::get('/games/{id}', [GamesInfo::class, 'show'])->name('game.show');
    Route::post('/games/test', [GamesInfo::class, 'test'])->name('game.test');

    Route::get('/user/{name}', [UserProfile::class, 'show'])->name('profile.show');
    Route::get('/user/{name}/edit', [UserProfile::class, 'edit'])->name('profile.edit');
    Route::post('/user/{name}/update', [UserProfile::class, 'update'])->name('profile.update');

    Route::get('/search/users', [UserProfile::class, 'search_users'])->name('users.search');
    Route::get('/search/return', [UserProfile::class, 'return_users'])->name('users.return');

    Route::get('/friends', [UserProfile::class, 'friend_list'])->name('friend.list');
    Route::post('/friends/remove/{name}', [UserProfile::class, 'friend_remove'])->name('friend.remove');

    Route::get('/notifications', [UserProfile::class, 'notifications'])->name('notifications');
    Route::post('/friend/request/send/{name}', [UserProfile::class, 'sendFriendRequest'])->name('friend.request.send');
    Route::post('/friends/request/accept/{name}', [UserProfile::class, 'friend_request_accept'])->name('friend.request.accept');
    Route::post('/friends/request/deny/{name}', [UserProfile::class, 'friend_request_deny'])->name('friend.request.deny');

});

/*
Email verification routes
*/
Route::middleware(['auth'])->group(function () {

    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');
    
    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
     
        return redirect()->route('games_index');
    })->middleware(['signed'])->name('verification.verify');
    
    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
     
        return back()->with('success', 'Verifikācijas saite nosūtīta!');
    })->middleware(['throttle:6,1'])->name('verification.send');

});
