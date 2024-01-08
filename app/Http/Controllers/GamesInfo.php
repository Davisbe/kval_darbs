<?php

namespace App\Http\Controllers;

use App\Models\Grupa;
use Illuminate\Http\Request;
use App\Models\Spele;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

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
        
        // get the info about the game
        $game = DB::table('speles')
            ->where('speles.id', '=', $id)
            ->select('speles.id', 'speles.name', 'speles.description', 'speles.picture', 'speles.start_time', 'speles.end_time',
                DB::raw('count(lietotajsgrupa.user_id) as player_count'),
                DB::raw('count(if (lietotajsgrupa.user_id = '.Auth::user()->id.', 1, NULL)) as joined'))
            ->leftJoin('grupas', 'speles.id', '=', 'grupas.spele_id')
            ->leftJoin('lietotajsgrupa', function($join) {
                $join->on('grupas.id', '=', 'lietotajsgrupa.grupa_id')
                    ->where('lietotajsgrupa.apstiprinats', '=', 1);
            })
            ->groupBy('speles.id', 'speles.name', 'speles.description', 'speles.picture', 'speles.start_time', 'speles.end_time')
            ->first();
        

        $game->meklejamas_vietas = DB::table('speles')
            ->where('speles.id', '=', $id)
            ->leftJoin('spelevieta', 'speles.id', '=', 'spelevieta.spele_id')
            ->select(DB::raw('count(spelevieta.spele_id) as meklejamas_vietas'))
            ->first()->meklejamas_vietas;

        if ($game->end_time < now()) {
            $game_obj = Spele::findOrFail($id);

            $game_results = $game_obj->getGameResults();

            foreach ($game_results as $group) {
                $group->profile_link = route('profile.show', ['name' => $group->name]);
                $group->profile_picture = asset($group->profile_picture);
            }

            return view('game_pages/show_ended_game', compact('game', 'game_results'));
        } else {
            // gets all invites from other users to the game
            $game_invites = $auth_user->groupInvitesToGame($id);
            foreach ($game_invites as $invite) {
                $invite->inviter_profile_link = route('profile.show', ['name' => $invite->inviter_name]);
            }

            // check if the user is part of a group in the game
            $game->isPartOfAGroup = $auth_user->isPartOfAGroup($id);
            
            return view('game_pages/show_game', compact('game', 'game_invites'));
        }
        
        
    }

    public function index_load_games() {
        // gets all games that are not ended, also figures out how many players have joined
        // (i.e. how many players are connected to the groups which are conencted to the game).
        // also figures out if the user has joined the game or not:
        try {
            $games = DB::table('speles')
            ->select('speles.id', 'speles.name', 'speles.picture', 'speles.start_time', 'speles.end_time',
                DB::raw('count(lietotajsgrupa.user_id) as player_count'),
                DB::raw('count(if (lietotajsgrupa.user_id = '.Auth::user()->id.' and lietotajsgrupa.apstiprinats = 1, 1, NULL)) as joined'))
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
}
