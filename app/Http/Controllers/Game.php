<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Grupa;
use App\Models\Spele;
use App\Models\User;
use App\Models\Karte;
use App\Models\Vieta;
use App\Models\Location;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

use Location\Bearing\BearingSpherical;
use Location\Coordinate;
use Location\Distance\Vincenty;

use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Storage;

class Game extends Controller
{

    // an active game's main screen
    public function index() {

        $auth_user = User::where('name', Auth::user()->name)
                ->select(['id', 'name'])
                ->firstOrFail();
        
        $game_id = $auth_user->checkIfPlayingGame();
        
        $game = Spele::find($game_id);
        if (!$game) {
            return redirect()->route('profile.show', ['name' => $auth_user->name]);
        }

        $user_group = $auth_user->grupa()
            ->where('spele_id', $game_id)
            ->wherePivot('active', 1)
            ->firstOrFail();
        
        $top_groups = $game->getTopGroups(10);
        $game_places_count = $game->vieta()->count();
        $game_groups_count = $game->grupa()->count();

        $my_group_places_found_count = $user_group->getPlacesFoundCount();
        $my_group_points = $user_group->getPoints();

        $map_list = Karte::with('kartesobjekts')->find($game->karte_id);

        foreach ($top_groups as $group) {
            $group->profile_link = route('profile.show', ['name' => $group->name]);
            $group->profile_picture = asset($group->profile_picture);
            if ($group->punkti == null) {
                $group->punkti = 0;
            }
        }

       
        $findable_places_loc = $user_group->selectPlacesToFind()->get(['garums', 'platums']);
        $findable_places_loc->makeHidden('pivot');

        // set seed, so players won't refresh page to deduct
        // where the exact position of the places are
        mt_srand($game_id);

        // create new obstructed coordinates for each place
        // that will be used to display findable places on map
        foreach ($findable_places_loc as $place) {            
            $original_coords = new Coordinate($place->platums, $place->garums);
            $bearing = mt_rand(0, 360);
            $distance = mt_rand(20, 300);
            $bearingSpherical = new BearingSpherical();

            $new_coords = $bearingSpherical->calculateDestination($original_coords, $bearing, $distance);

            $place->platums = $new_coords->getLat();
            $place->garums = $new_coords->getLng();
        }

        ini_set('serialize_precision', 8);  // don't cut off decimals in json_encode
        $findable_places_loc = json_encode($findable_places_loc);

        return view('game_pages/active_index', compact('top_groups',
            'my_group_places_found_count',
            'my_group_points',
            'map_list',
            'game_places_count',
            'game_groups_count',
            'findable_places_loc'));
    }

    // list the places that users need to find for a specific game
    // (excluding places that already have been found)
    public function places_list() {
        $auth_user = User::where('name', Auth::user()->name)
                ->select(['id', 'name'])
                ->firstOrFail();
        
        $game_id = $auth_user->checkIfPlayingGame();

        $game = Spele::find($game_id);
        if (!$game) {
            return redirect()->route('profile.show', ['name' => $auth_user->name]);
        }

        $user_group = $auth_user->grupa()
            ->where('spele_id', $game_id)
            ->wherePivot('active', 1)
            ->firstOrFail();

        $group_leader = $user_group->getGroupLeader();

        $user_is_leader = false;

        if ($group_leader->name == $auth_user->name) {
            $user_is_leader = true;
        }
        
        $game_places = $user_group->selectPlacesToFind();
        $game_places = $game_places->get(['id', 'name', 'picture', 'sarezgitiba']);
        $game_places->makeHidden('pivot');

        foreach ($game_places as $place) {
            $place->picture = asset($place->picture);
        }

        return view('game_pages/active_places', compact('game_places', 'user_is_leader'));
    }

    // submit user's location and provide feedback if the location
    // is close enough to the place that the user is trying to find
    public function place_try(Request $request) {
        $auth_user = User::where('name', Auth::user()->name)
                ->select(['id', 'name'])
                ->firstOrFail();

        $request->validate([
            'place_id' => 'nullable|numeric|min:0',
            'latitude' => 'required|numeric|min:-90|max:90',
            'longitude' => 'required|numeric|min:-180|max:180',
            'precision' => 'required|numeric|max:20'
        ]);

        $auth_user->location()->create([
            'garums' => $request->longitude,
            'platums' => $request->latitude,
            'precizitate' => $request->precision
        ]);

        $game_id = $auth_user->checkIfPlayingGame();

        $user_group = $auth_user->grupa()
            ->where('spele_id', $game_id)
            ->wherePivot('active', 1)
            ->firstOrFail();

        $group_leader = $user_group->getGroupLeader();

        $user_is_leader = false;

        if ($group_leader->name == $auth_user->name) {
            $user_is_leader = true;
        }

        $vieta = Vieta::find($request->place_id);
        $vieta_location = new Coordinate($vieta->platums, $vieta->garums);

        // if user is not the group leader, check if the user is close enough to the place
        // otherwise, leader must check if every active group member is close enough
        if (!$user_is_leader) {
            $locations = $auth_user->getTwoRecentLocations();
            foreach ($locations as $location) {
                $user_location = new Coordinate($location->platums, $location->garums);

                $distance_calculator = new Vincenty();

                $distance = $distance_calculator->getDistance($user_location, $vieta_location);

                if ($distance < $vieta->pielaujama_kluda + $location->precizitate) {
                    return response()->json(['success' => true,
                        'place_close'=> true,
                        'is_leader'=> false,
                        ]);
                }
            }
            return response()->json(['success' => true,
                'place_close'=> false,
                'is_leader'=> false,
                ]);
        }
        else {
            $group_members_close = [];
            $group_members_far = [];

            $group_members = $user_group->getActiveGroupMemberLocations();
            foreach ($group_members as $group_member) {
                foreach ($group_member->location as $location) {
                    $user_location = new Coordinate($location->platums, $location->garums);

                    $distance_calculator = new Vincenty();

                    $distance = $distance_calculator->getDistance($user_location, $vieta_location);

                    if ($distance < $vieta->pielaujama_kluda + $location->precizitate) {
                        $group_members_close[] = $group_member->name;
                        break;
                    }
                }
                if (!in_array($group_member->name, $group_members_close)) {
                    $group_members_far[] = $group_member->name;
                }
            }

            return response()->json(['success' => true,
                'group_members_far'=> $group_members_far,
                'is_leader'=> true,
            ]);
        }

        return response()->json(['success' => false]);
    }

    public function place_submit(Request $request) {
        $auth_user = User::where('name', Auth::user()->name)
                ->select(['id', 'name'])
                ->firstOrFail();

        $request->validate([
            'place_found_id' => 'required|numeric|min:0',
            'place_found_picture' => 'required|image|mimes:jpeg,png,jpg|max:20480',
        ]);

        $game_id = $auth_user->checkIfPlayingGame();

        $user_group = $auth_user->grupa()
            ->where('spele_id', $game_id)
            ->wherePivot('active', 1)
            ->firstOrFail();

        $group_leader = $user_group->getGroupLeader();

        // if user not leader, he cant submit the place as found
        if ($group_leader->name != $auth_user->name) {
            return redirect()->route('active_game.places_list');
        }

        $vieta = Vieta::findOrFail($request->place_found_id);

        // check if the place is conencted to current game
        if (!$vieta->spele()->where('spele_id', $game_id)->exists()) {
            return redirect()->route('active_game.places_list');
        }

        // check if the group has already found this place
        if ($user_group->vieta()->where('vieta_id', $vieta->id)->exists()) {
            return redirect()->route('active_game.places_list');
        }

        // Check if all members are next to the place again
        $vieta_location = new Coordinate($vieta->platums, $vieta->garums);

        $group_members = $user_group->getActiveGroupMemberLocations();
        foreach ($group_members as $group_member) {

            if (count($group_member->location) == 0) {
                return redirect()->route('active_game.places_list');
            }
            
            foreach ($group_member->location as $location) {
                $user_location = new Coordinate($location->platums, $location->garums);

                $distance_calculator = new Vincenty();

                $distance = $distance_calculator->getDistance($user_location, $vieta_location);

                if ($distance > $vieta->pielaujama_kluda + $location->precizitate) {
                    return redirect()->route('active_game.places_list');
                }
            }
        }

        // if picture valid then resize and store it. Finally, mark place as found
        if ($request->file('place_found_picture')->isValid()) {
            $imageName = Str::random(64).'.'.time().'.'.$vieta->id.'.'.$user_group->id.'.jpg';
            $image_resize = ImageManager::gd()->read($request->file('place_found_picture'));
            $image_resize = $image_resize->scaleDown(1440, 1440)->toJpeg(75)->__toString();
            if (Storage::put('public/images/group_places/'.$imageName, $image_resize, 'public')) {
                $user_group->vieta()->attach($vieta->id, ['picture' => 'storage/images/group_places/'.$imageName]);
                return redirect()->route('active_game.index');
            }
            else {
                return redirect()->route('active_game.places_list');
            }
        }

    }

    public function chat_view() {
        $auth_user = User::where('name', Auth::user()->name)
                ->select(['id', 'name'])
                ->firstOrFail();

        $game_id = $auth_user->checkIfPlayingGame();

        $game = Spele::findOrFail($game_id);

        $chat_messages = $game->getChatMessagesReverse();

        return view('game_pages/active_game_chat', compact('chat_messages', 'auth_user'));
    }

    public function chat_send(Request $request) {
        $auth_user = User::where('name', Auth::user()->name)
                ->select(['id', 'name'])
                ->firstOrFail();

        $request->validate([
            'message' => 'required|string|max:300|min:1',
        ]);

        $game_id = $auth_user->checkIfPlayingGame();

        $game = Spele::findOrFail($game_id);

        $game->sazina()->create([
            'user_id' => $auth_user->id,
            'text' => $request->message,
        ]);

        return redirect()->route('active_game.chat_view');
    }

    public function group_view() {
        $auth_user = User::where('name', Auth::user()->name)
                ->select(['id', 'name'])
                ->firstOrFail();

        $game_id = $auth_user->checkIfPlayingGame();

        $game_info = Spele::select('id', 'name', 'picture', 'start_time', 'end_time')
            ->findOrFail($game_id);

        $group_user_connection = $auth_user->grupa()
            ->where('spele_id', $game_id)
            ->wherePivot('apstiprinats', 1)
            ->firstOrFail();
        
        $group = Grupa::where('id', $group_user_connection->id)
            ->firstOrFail();

        $group_members = $auth_user->getMyGroupMembersFromGame($game_id);

        // could just use getGroupLeader method, but it's
        // going to be another SELECT query then
        $group_leader = [];

        $is_user_ready = $group->isMemberReady($auth_user->name);

        foreach ($group_members as $member) {
            if ($member->uzaicinats == 0) {
                $group_leader = $member;
            }
            $member->profile_picture = asset($member->profile_picture);
            $member->profile_link = route('profile.show', ['name' => $member->name]);
        }

        $friendlist_invitable = $auth_user->getFriendsExcludingAlreadyInGroup($game_id);
        foreach ($friendlist_invitable as $friend) {
            $friend->profile_picture = asset($friend->profile_picture);
            $friend->profile_link = route('profile.show', ['name' => $friend->name]);
        }

        return view('game_pages/game_active_group', compact('is_user_ready', 'group_members', 'game_info', 'group_leader', 'friendlist_invitable'));

    }

    public function game_info() {

        $auth_user = User::where('name', Auth::user()->name)
                ->select(['id', 'name'])
                ->firstOrFail();

        $game_id = $auth_user->checkIfPlayingGame();
        
        // get the info about the game
        $game = DB::table('speles')
            ->where('speles.id', '=', $game_id)
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
            ->where('speles.id', '=', $game_id)
            ->leftJoin('spelevieta', 'speles.id', '=', 'spelevieta.spele_id')
            ->select(DB::raw('count(spelevieta.spele_id) as meklejamas_vietas'))
            ->first()->meklejamas_vietas;

        return view('game_pages/game_active_info', compact('game'));
    }
}
