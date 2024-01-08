<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Auth\Events\Registered;


class Authentification extends Controller
{

    // Rate limiter function.
    // Returns 0 if rate limiter not reached its limits
    // Returns an int number in seconds (remaining cooldown time) if the limit is reached
    protected function customRateLimiterFailed(Request $request, $key_prefix,
                            $default = [6, 60], $fallback = [3, 60]) {

        $key = $key_prefix . $request->session()->getId() . '.' . $request->ip();

        # After first 6 failed attempts in 60 seconds,
        # 'fallback' values are used when the first 'default' attempts are used up
        $limits = [
            'default' => [
                'max_attempts' => $default[0],
                'decay_time' => $default[1]
            ],
            'fallback' => [
                'max_attempts' => $fallback[0],
                'decay_time' => $fallback[1]
            ]
        ];

        if (RateLimiter::tooManyAttempts($key, $limits['fallback']['max_attempts'])) {
            $limit = $limits['fallback'];
        } else {
            $limit = $limits['default'];
        }

        if (RateLimiter::tooManyAttempts($key, $limit['max_attempts'])) {
            $retryAfter = RateLimiter::availableIn($key);
            return $retryAfter;
        }

        RateLimiter::hit($key);
        return 0;
    }

    function login() {
        if (Auth::check()) {
            return redirect()->route('games_index');
        }

        return view('auth/login');
    }

    function register() {
        if (Auth::check()) {
            return back();
        }

        return view('auth/register');
    }

    function save(Request $request) {
        if (Auth::check()) {
            return back();
        }

        $request->validate([
            'username_r'=>'required|min:3|max:20|alpha_dash:ascii|unique:users,name',
            'email_r'=>'required|email|max:255|unique:users,email|string',
            'password_r'=>'required|min:10|max:50|string',
            'rpassword'=>'required|same:password_r|string',
        ]);

        $user = new User;
        $user->name = $request->username_r;
        $user->email = $request->email_r;
        $user->password = Hash::make($request->password_r);
        $user->profile_picture = 'storage/images/static/profile-pic-placeholder.png';
        $save = $user->save();

        $credentials = [
            'email' => $request->email_r,
            'password' => $request->password_r
        ];

        if ($save) {
            // used to send email verification link
            event(new Registered($user));
            
            // log in user, so they can verify their email in the same session
            if (Auth::attempt($credentials)) {
                $request->session()->regenerate();
            }
            return redirect()->route('verification.notice')->with('success', 'Profils izveidots! Verificē savu e-pastu, lai pabeigtu reģistrāciju.');
        }
        else {
            return back()->with('fail','Kaut kas nogāja greizi. Pamēģini vēlreiz mazliet vēlāk.');
        }
    }

    function check(Request $request) {
        if (Auth::check()) {
            return route('games_index');
        }
        
        $request->validate([
            'email_r'=>'required|email|string',
            'password_r'=>'required|min:10|max:50|string'
        ]);

        $credentials = [
            'email' => $request->email_r,
            'password' => $request->password_r
        ];

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('games_index');
        } else {
            $rate_limiter_status = $this->customRateLimiterFailed($request, 'login');
            if ($rate_limiter_status) {
                return back()->with('fail', 'Pārāk daudz mēģinājumu. Lūdzu mēgināt vēlreiz pēc ' . $rate_limiter_status . ' sekundēm.');
            }
        }
        
        return back()->with('fail', 'Nepareiza e-pasta adrese vai parole.');

    }

    function logout() {
        if(Auth::check()) {
            Auth::logout();
        }
        
        return redirect()->route('login');
    }

}
