<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;

class IsSuspended
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $auth_user = User::find(Auth::user()->id);
        
        // If the user has been suspended, check if playing gmame and leave it
        if ($auth_user->suspended_profile) {
            $game_id = $auth_user->checkIfPlayingGame();
            if ($game_id != 0) {
                $auth_user->leaveGroupFromGame($game_id);
            }

            // ..then log out
            Auth::logout();
            return redirect()->route('login')->with('fail', 'Jūsu profila darbība ir apturēta.');
        }
            
        return $next($request);
    }
}
