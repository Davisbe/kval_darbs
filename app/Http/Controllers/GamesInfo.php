<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Spele;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\DB;
use Exception;

class GamesInfo extends Controller
{
    public function index() {
        return view('game_pages/games_index');
    }

    public function show($id) {
        $auth_user = User::where('name', Auth::user()->name)
                ->select(['id', 'name'])
                ->firstOrFail();

        $game = DB::table('speles')
            ->select('speles.id', 'speles.name', 'speles.description', 'speles.picture', 'speles.start_time', 'speles.end_time',
                DB::raw('count(lietotajsgrupa.user_id) as player_count'),
                DB::raw('count(if (lietotajsgrupa.user_id = '.Auth::user()->id.', 1, NULL)) as joined'),
                DB::raw('count(spelevieta.spele_id) as meklejamas_vietas'))
            ->leftJoin('grupas', 'speles.id', '=', 'grupas.spele_id')
            ->leftJoin('spelevieta', 'speles.id', '=', 'spelevieta.spele_id')
            ->leftJoin('lietotajsgrupa', 'grupas.id', '=', 'lietotajsgrupa.grupa_id')
            ->where('speles.id', '=', $id)
            ->groupBy('speles.id', 'speles.name', 'speles.description', 'speles.picture', 'speles.start_time', 'speles.end_time')
            ->get();
        
        // ->select('grupas.id', 'inviter.name as inviter_name', 'grupas.created_at')
        $game_invites = $auth_user->groupInvitesToGame($id);
        foreach ($game_invites as $invite) {
            $invite->inviter_profile_link = route('profile.show', ['name' => $invite->inviter_name]);
        }
        
        $game = $game[0];
        
        return view('game_pages/show_game', compact('game', 'game_invites'));
    }

    public function index_load_games() {
        // gets all games that are not ended, also figures out how many players have joined
        // (i.e. how many players are connected to the groups which are conencted to the game).
        // also figures out if the user has joined the game or not:
        try {
            $games = DB::table('speles')
            ->select('speles.id', 'speles.name', 'speles.picture', 'speles.start_time', 'speles.end_time',
                DB::raw('count(lietotajsgrupa.user_id) as player_count'),
                DB::raw('count(if (lietotajsgrupa.user_id = '.Auth::user()->id.', 1, NULL)) as joined'))
            ->leftJoin('grupas', 'speles.id', '=', 'grupas.spele_id')
            ->leftJoin('lietotajsgrupa', 'grupas.id', '=', 'lietotajsgrupa.grupa_id')
            ->where('speles.end_time', '>', now())
            ->groupBy('speles.id', 'speles.name', 'speles.picture', 'speles.start_time', 'speles.end_time')
            ->orderBy('speles.start_time', 'asc')->paginate(10);

            foreach ($games as $game) {
                $game->picture = asset($game->picture);
                $game->link = route('game.show', ['id' => $game->id]);
            }
        } catch (Exception $e) {
            return response()->json(['success' => false]);
        }
        
        return response()->json(['success' => true, $games]);
    }

    public function test(Request $request) {
        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');
        
        return response()->json(['success' => true, 'data' => [$latitude, $longitude]]);
    }
}
