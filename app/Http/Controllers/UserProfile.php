<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;

class UserProfile extends Controller
{
    public function show($name) {
        $userinfo = User::where('name', $name)
        ->whereNotNull('email_verified_at')
        ->select(['id', 'name', 'profile_picture', 'places_found', 'hide_profile_info'])
        ->firstOrFail();

        $auth_user = User::where('name', Auth::user()->name)
        ->select(['id', 'name'])
        ->firstOrFail();

        $userinfo->gamesCount = $userinfo->gamesHistory()->flatten()->count();
        $userinfo->profile_picture_link = Storage::url('$userinfo->profile_picture');

        if ($userinfo->hide_profile_info == 0 || $auth_user->name == $name) {
            $userinfo->gamesHistory = $userinfo->gamesHistory();
        }
        else {
            $userinfo->gamesHistory = [];
        }

        if ($auth_user->isFriendsWith($userinfo)) {
            $userinfo->showFriendInviteButton = false;
        }
        else {
            $userinfo->showFriendInviteButton = true;
        }

        return view('game_pages/user/profile', compact('userinfo'));
    }

    public function edit($name) {
        if (Auth::user()->name !== $name) {
            return redirect()->route('profile.show', ['name' => $name,]);
        }

        $userinfo = User::where('name', $name)
        ->select(['name', 'profile_picture', 'places_found', 'hide_profile_info', 'google_oauth'])
        ->firstOrFail();

        if (!is_null($userinfo->google_oauth)) {
            $userinfo->google_oauth = true;
        }



        return view('game_pages/user/profile_edit', compact('userinfo'));
    }

    function update(Request $request, $name) {
        if ((Auth::user()->name !== $name)) {
            return redirect()->route('profile.show', ['name' => $name,]);
        }

        $user = User::where('name', $name)->firstOrFail();

        $request->validate([
            'username_r'=>'nullable|min:3|max:20|alpha_dash:ascii|unique:users,name',
            'email_r'=>'nullable|email|max:255|unique:users,email|string',
            'toggle' => 'numeric|in:0,1',
            'profile-picture' => 'nullable|image|mimes:jpeg,png,jpg|max:20480',
        ]);

        if ($request->hasFile('profile-picture')) {
            if ($request->file('profile-picture')->isValid()) {
                $imageName = time().'.'.$name.'.'.'256x256'.'.jpg';
                $image_resize = ImageManager::gd()->read($request->file('profile-picture'));
                $image_resize = $image_resize->coverDown(256, 256)->toJpeg(75)->__toString();
                if (Storage::put('public/images/profile_pictures/'.$imageName, $image_resize, 'public')) {
                    if ($user->profile_picture != 'storage/images/static/profile-pic-placeholder.png') {
                        Storage::delete(str_replace('storage/', 'public/', $user->profile_picture));
                    }
                }
                else {
                    return back()->with('fail','Neizdevās saglabāt profila bildi.');
                }
    
                $user->profile_picture = 'storage/images/profile_pictures/'.$imageName;
            }
        }
        

        if ($request->filled('password_r') || $request->filled('email_r')) {
            if (!is_null($user->google_oauth)) {
                return back()->with('fail','Lietotāji, kuru profils izveidots ar OAuth nevar nomainīt e-pastu vai paroli.');
            }

            $request->validate([
                'password_r_old' => 'required|string'
            ]);

            if (!(Hash::check($request->input('password_r_old'), $user->password))) {
                return back()->with('fail','Tagadējā parole ievadīta nepareiza.');
            }
        }
        
        if ($request->filled('password_r')) {
            $request->validate([
                'password_r'=>'required|min:10|max:50|string',
                'rpassword'=>'required|same:password_r|string'
            ]);
        }

        $toggle = ($request->input('toggle') == 1) ? 1 : 0;
        $user->hide_profile_info = $toggle;

        if ($request->filled('username_r')) {
            $user->name = $request->input('username_r');
        }
        if (is_null($user->google_oauth)) {
            if ($request->filled('password_r')) {
                $user->password = Hash::make($request->input('password_r'));
            }
            if ($request->filled('email_r')) {
                $user->old_email = $user->email;
                $user->email = $request->input('email_r');
                $user->email_verified_at = null;
                $user->sendEmailVerificationNotification();
            }
        }
        
        $user->save();
        return redirect()->route('profile.edit', ['name' => $user->name,])->with('success','Profils rediģēts!');

    }

    function search_users() {
        return view('game_pages/user/search_users');
    }

    function return_users(Request $request) {
        $query = $request->get('query');

        if (!is_string($query)) {
            return response()->json([]);
        }

        if(!$query || strlen($query) < 2 || strlen($query) > 20) {
            return response()->json([]);
        }
    
        $data = User::where('name', 'LIKE', '%' . $query . '%')
                    ->whereNotNull('email_verified_at')
                    ->where('name', '!=', Auth::user()->name)
                    ->select(['name', 'profile_picture'])
                    ->limit(10)
                    ->get();

        foreach ($data as $user) {
            $user->profile_picture = asset($user->profile_picture);
            $user->profile_link = route('profile.show', ['name' => $user->name]);
        }

        return response()->json($data);
    }

    public function sendFriendRequest(Request $request, $name) {
        $user = User::where('name', $name)
                ->select(['id', 'name'])
                ->firstOrFail();
        
        $auth_user = User::where('name', Auth::user()->name)
                ->select(['id', 'name'])
                ->firstOrFail();

        if ($auth_user->name == $name) {
            return response()->json(['success' => false]);
        }
        // if request already sent, send appropriate response
        if ($auth_user->hasSentFriendRequestTo($user)) {
            return response()->json(['success' => true, 'request_already_sent' => true]);
        }
        // If the other user has sent a friend request to the current user, accept it
        if ($user->hasSentFriendRequestTo($auth_user) && !$auth_user->isFriendsWith($user)) {
            $auth_user->acceptFriendRequestFrom($user);
            return response()->json(['success' => true, 'accepted_fiend' => true]);
        }

        $auth_user->sendFriendRequestTo($user);

        return response()->json(['success' => true]);
    }

    public function friend_list() {
        $auth_user = User::where('name', Auth::user()->name)
                ->select(['id', 'name'])
                ->firstOrFail();
        
        $friendlist = $auth_user->getFriends();

        foreach ($friendlist as $friend) {
            $friend->profile_picture = asset($friend->profile_picture);
            $friend->profile_link = route('profile.show', ['name' => $friend->name]);
        }

        return view('game_pages/user/friend_list', compact('friendlist'));
    }

    public function friend_remove($name) {
        $auth_user = User::where('name', Auth::user()->name)
                ->select(['id', 'name'])
                ->firstOrFail();
        
        $user = User::where('name', $name)
        ->select(['id', 'name'])
        ->firstOrFail();

        if ($auth_user->isFriendsWith($user)) {
            $auth_user->removeFriend($user);
            return response()->json(['success' => true]);
        }
        else {
            return response()->json(['success' => false]);
        }
        
    }

    public function notifications() {

        $authed_user = User::where('name', Auth::user()->name)
                ->select(['id', 'name'])
                ->firstOrFail();
        
        $friend_request_notifications = $authed_user->incomingFriendRequests()->sortBy('name');
        foreach ($friend_request_notifications as $friend) {
            $friend->profile_link = route('profile.show', ['name' => $friend->name]);
        }

        return view('game_pages/user/notifications', compact('friend_request_notifications'));
    }

    public function friend_request_accept($name) {
        $auth_user = User::where('name', Auth::user()->name)
                ->select(['id', 'name'])
                ->firstOrFail();
        
        $user = User::where('name', $name)
        ->select(['id', 'name'])
        ->firstOrFail();

        if ($user->hasSentFriendRequestTo($auth_user) && !$auth_user->isFriendsWith($user)) {
            $auth_user->acceptFriendRequestFrom($user);
            return response()->json(['success' => true]);
        }
        else {
            return response()->json(['success' => false]);
        }
        
    }

    public function friend_request_deny($name) {
        $auth_user = User::where('name', Auth::user()->name)
                ->select(['id', 'name'])
                ->firstOrFail();
        
        $user = User::where('name', $name)
        ->select(['id', 'name'])
        ->firstOrFail();

        if ($user->hasSentFriendRequestTo($auth_user) && !$auth_user->isFriendsWith($user)) {
            $auth_user->denyFriendRequestFrom($user);
            return response()->json(['success' => true]);
        }
        else {
            return response()->json(['success' => false]);
        }
        
    }

}
