<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\User;


class RedirectIfUserPlayingGame
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $auth_user = User::where('name', Auth::user()->name)
                ->select(['id', 'name'])
                ->firstOrFail();
        
        $game_id = $auth_user->checkIfPlayingGame();
        
        if ($game_id != 0) {
            return redirect()->route('active_game.index');
        }
        
        return $next($request);
    }
}
