<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Authentification;
use App\Http\Controllers\AuthGoogle;
use App\Http\Controllers\Homepage;
use App\Http\Controllers\UserProfile;
use App\Http\Controllers\GamesInfo;
use App\Http\Controllers\Group;
use App\Http\Controllers\Game;
use App\Http\Controllers\AdminDashboard;
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
Route::middleware(['auth', 'verified', 'is_suspended'])->group(function () {

    // Middleware for checking if user is an admin
    Route::middleware(['is_admin'])->group(function () {
        Route::get('/admin/dashboard', [AdminDashboard::class, 'index'])->name('admin.dashboard');

        Route::get('/admin/create-game', [AdminDashboard::class, 'create_game'])->name('admin.create_game');
        Route::post('/admin/create-game/save', [AdminDashboard::class, 'save_new_game'])->name('admin.save_new_game');

        Route::get('/admin/create-place', [AdminDashboard::class, 'create_place'])->name('admin.create_place');
        Route::post('/admin/create-place/save', [AdminDashboard::class, 'save_place'])->name('admin.save_place');

        Route::get('/admin/list-users', [AdminDashboard::class, 'list_users'])->name('admin.list_users');
        Route::post('/admin/user/suspend', [AdminDashboard::class, 'suspend_user'])->name('admin.suspend_user');

        Route::get('/admin/list-vietas', [AdminDashboard::class, 'list_vietas'])->name('admin.list_vietas');
        Route::get('/admin/vieta/edit/{id}', [AdminDashboard::class, 'edit_vieta'])->name('admin.edit_vieta');
        Route::post('/admin/vieta/update/{id}', [AdminDashboard::class, 'update_vieta'])->name('admin.update_vieta');

        Route::get('/admin/list-games', [AdminDashboard::class, 'list_games'])->name('admin.list_games');
        Route::get('/admin/game/edit/{id}', [AdminDashboard::class, 'edit_game'])->name('admin.edit_game');
        Route::post('/admin/game/update/{id}', [AdminDashboard::class, 'update_game'])->name('admin.update_game');
    });

    // If user is playing a game, redirect to the game
    Route::middleware(['in_active_game'])->group(function () {

        Route::get('/games', [GamesInfo::class, 'index'])->name('games_index');
        Route::get('/games/loadmore', [GamesInfo::class, 'index_load_games'])->name('games_index.loadmore');
        Route::get('/games/{id}', [GamesInfo::class, 'show'])->name('game.show');

        Route::get('/games/{id}/group', [Group::class, 'games_group'])->name('game.group');
        Route::get('/group/{id}/deny', [Group::class, 'group_deny'])->name('group.deny');
        Route::get('/group/{id}/accept', [Group::class, 'group_accept'])->name('group.accept');
        Route::post('/games/{id}/group/toggle-me-ready', [Group::class, 'toggle_user_ready'])->name('group.toggle_user_ready');
        Route::get('/games/{id}/group/poll-users-ready', [Group::class, 'poll_users_ready'])->name('group.poll-users-ready');

        Route::get('/search/users', [UserProfile::class, 'search_users'])->name('users.search');
        Route::get('/search/return', [UserProfile::class, 'return_users'])->name('users.return');

        Route::get('/friends', [UserProfile::class, 'friend_list'])->name('friend.list');
        Route::post('/friends/remove/{name}', [UserProfile::class, 'friend_remove'])->name('friend.remove');

        Route::get('/notifications', [UserProfile::class, 'notifications'])->name('notifications');
        Route::post('/friend/request/send/{name}', [UserProfile::class, 'sendFriendRequest'])->name('friend.request.send');
        Route::post('/friends/request/accept/{name}', [UserProfile::class, 'friend_request_accept'])->name('friend.request.accept');
        Route::post('/friends/request/deny/{name}', [UserProfile::class, 'friend_request_deny'])->name('friend.request.deny');

    });

    Route::get('/user/{name}', [UserProfile::class, 'show'])->name('profile.show');
    Route::get('/user/{name}/edit', [UserProfile::class, 'edit'])->name('profile.edit');
    Route::post('/user/{name}/update', [UserProfile::class, 'update'])->name('profile.update');
    Route::post('/profile/user/delete', [UserProfile::class, 'delete_user'])->name('profile.delete_user');
    Route::get('/settings', [UserProfile::class, 'settings_view'])->name('settings_view');

    Route::get('/games/{id}/group/leave', [Group::class, 'group_leave'])->name('group.leave');
    Route::post('/games/{id}/group/kick-user/{name}', [Group::class, 'remove_member'])->name('group.remove_member');
    Route::post('/games/{id}/group/invite-user/{name}', [Group::class, 'invite_friend'])->name('group.invite_friend');

    Route::get('/game/active', [Game::class, 'index'])->name('active_game.index');
    Route::get('/game/active/group', [Game::class, 'group_view'])->name('active_game.group_view');
    Route::get('/game/active/game-info', [Game::class, 'game_info'])->name('active_game.game_info');
    Route::get('/game/active/places', [Game::class, 'places_list'])->name('active_game.places_list');
    Route::post('/game/active/place/try', [Game::class, 'place_try'])->name('active_game.place_try');
    Route::post('/game/active/place/submit', [Game::class, 'place_submit'])->name('active_game.place_submit');

    Route::get('/game/active/chat', [Game::class, 'chat_view'])->name('active_game.chat_view');
    Route::post('/game/active/chat/send', [Game::class, 'chat_send'])->name('active_game.chat_send');
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
