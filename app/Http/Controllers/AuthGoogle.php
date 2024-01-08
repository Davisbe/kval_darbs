<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

use Exception;

class AuthGoogle extends Controller
{
    function redirect () {
        if (Auth::check()) {
            return back();
        }
        return Socialite::driver('google')->redirect();
    }

    function callback (Request $request) {
        if (Auth::check()) {
            return back();
        }

        try {
            // get user data from Google
            $user = Socialite::driver('google')->user();

            // find user in the database where the social id is the same with the id provided by Google
            $finduser = User::where('google_oauth', $user->id)->first();

            if ($finduser)  // if user found then do this
            {
                // Log the user in
                Auth::login($finduser);
                $request->session()->regenerate();

                return redirect()->route('games_index');
            }
            else
            {
                // if user is found with this gmail address, but oauth wasn't used, then
                // prompt user with the appropriate message
                $finduser_with_email = User::where('email', $user->email)->where('google_oauth', null)->first();
                if ($finduser_with_email) {
                    return redirect()->route('login')->with('fail', 'Tev jāpiesakās, ievadot e-pastu un paroli');
                }

                // if user not found then this is the first time they try to login with Google account
                
                // store needed google info in session, because unique username needs to be created
                session(['temp_google_user' => [
                    'email' => $user->email,
                    'google_oauth' => $user->id,
                ]]);

                return redirect()->route('auth.create_name_index');
            }

        }
        catch (Exception $e)
        {  
            return redirect()->route('login')->with('fail', 'Kaut kas nogāja greizi. Pamēģini vēlreiz mazliet vēlāk.');
        }
    }

    function create_name_index() {
        if (Auth::check()) {
            return redirect()->route('games_index');
        }
        else if (! session()->has('temp_google_user')) {
            return redirect()->route('homepage_index');
        }

        return view('auth/create-name');
    }

    function create_name_check(Request $request) {
        if (Auth::check()) {
            return redirect()->route('games_index');
        }
        else if (! session()->has('temp_google_user')) {
            return redirect()->route('homepage_index');
        }

        $request->validate([
            'username_r'=>'required|min:3|max:20|alpha_dash:ascii|unique:users,name',
        ]);

        // generate a random password, becasue an OAuth user won't use one
        $random_password = Str::random(50);

        $session_google_user = session('temp_google_user');

        $user = new User;
        $user->name = $request->username_r;
        $user->email = $session_google_user['email'];
        $user->google_oauth = $session_google_user['google_oauth'];
        $user->password = Hash::make($random_password);
        $user->profile_picture = 'storage/images/static/profile-pic-placeholder.png';
        $user->markEmailAsVerified(); // email is verified since google was used to log in
        $save = $user->save();

        if ($save) {

            if (Auth::login($user)) {
                $request->session()->regenerate();
            }
            return redirect()->route('games_index');
        }
        else {
            return back()->with('fail','Kaut kas nogāja greizi. Pamēģini vēlreiz mazliet vēlāk.');
        }
    }

}
