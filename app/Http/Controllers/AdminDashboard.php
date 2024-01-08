<?php

namespace App\Http\Controllers;

use App\Models\Karte;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\KartesObjekts;
use App\Models\Spele;
use App\Models\Vieta;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Storage;


class AdminDashboard extends Controller
{
    public function index() {
        return view('admin/dashboard_index');
    }

    public function create_game() {
        $map_list = Karte::with('kartesobjekts')->get();

        $vietas = DB::table('vietas')
            ->select('vietas.id', 'vietas.name', 'vietas.picture', 'vietas.garums', 'vietas.platums', 'vietas.pielaujama_kluda', 'vietas.sarezgitiba',
                DB::raw('count(spelevieta.vieta_id) as vieta_izmantota_count'))
            ->leftJoin('spelevieta', 'vietas.id', '=', 'spelevieta.vieta_id')
            ->groupBy('vietas.id', 'vietas.name', 'vietas.picture', 'vietas.garums', 'vietas.platums', 'vietas.pielaujama_kluda', 'vietas.sarezgitiba')
            ->get();
        
        return view('admin/create_game', compact('map_list', 'vietas'));
    }

    public function save_new_game(Request $request) {
        $request->validate([
            'game_name' => 'required|min:5|max:150',
            'game_description' => 'required|max:2000',
            'game_start' => 'required|date',
            'game_end' => 'required|date|after:game_start',
            'existing_map_selection' => 'required',
            'vieta' => 'required|array|min:1',
            'picture' => 'required|image|mimes:jpeg,png,jpg|max:15000',
        ]);

        $map = null;

        if (!is_numeric($request->input('existing_map_selection'))) {
            $request->validate([
                'map_name' => 'required|min:5|max:300',
                'map_zoom' => 'required|numeric|min:0|max:100',
                'map_longitude' => 'required|numeric|min:-180|max:180',
                'map_latitude' => 'required|numeric|min:-90|max:90',
                'new_map_geojson' => 'required|string|max:65535',
            ]);

            $map = Karte::create([
                'name' => $request->input('map_name'),
                'zoom' => $request->input('map_zoom'),
                'viduspunkts_garums' => $request->input('map_longitude'),
                'viduspunkts_platums' => $request->input('map_latitude')
            ]);
            $map->kartesobjekts()->create([
                'geojson' => $request->input('new_map_geojson')
            ]);
            $map->save();
        } else {
            $map = Karte::findOrFail($request->input('existing_map_selection'));
        }

        $game = null;

        if ($request->file('picture')->isValid()) {
            $imageName = Str::random(8).'.'.time().'.jpg';
            $image_resize = ImageManager::gd()->read($request->file('picture'));
            $image_resize = $image_resize->scaleDown(1440, 1440)->toJpeg(75)->__toString();
            if (Storage::put('public/images/games/'.$imageName, $image_resize, 'public')) {
                $game = Spele::create([
                    'name' => $request->input('game_name'),
                    'description' => $request->input('game_description'),
                    'start_time' => $request->input('game_start'),
                    'end_time' => $request->input('game_end'),
                    'karte_id' => $map->id,
                    'picture' => 'storage/images/games/'.$imageName
                ]);
                $game->save();
            }

        }

        
        if (!is_null($game)) {
            foreach ($request->input('vieta') as $vieta_id) {
                $game->vieta()->attach($vieta_id);
            }
    
            return redirect()->route('admin.dashboard')->with('success', 'Spēle veiksmīgi izveidota!');
        }
        else {
            return redirect()->route('admin.dashboard')->with('fail', 'Kaut kas nogāja greizi, veidojot spēli.');;
        }
        
    }

    public function create_place() {
        return view('admin/create_place');
    }

    public function save_place(Request $request) {
        
        $request->validate([
            'name' => 'required|string|min:5|max:300',
            'acc_error' => 'required|numeric|min:0',
            'longitude' => 'required|numeric|min:-180|max:180',
            'latitude' => 'required|numeric|min:-90|max:90',
            'picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:15000',
            'sarezgitiba' => 'required|min:1|max:10',
        ]);

        if ($request->file('picture')->isValid()) {
            $imageName = Str::random(64).'.'.time().'.'.Auth::user()->id.'.jpg';
            $image_resize = ImageManager::gd()->read($request->file('picture'));
            $image_resize = $image_resize->scaleDown(1440, 1440)->toJpeg(75)->__toString();
            if (Storage::put('public/images/findable_places/'.$imageName, $image_resize, 'public')) {
                $new_vieta = Vieta::create([
                    'name' => $request->input('name'),
                    'garums' => $request->input('longitude'),
                    'platums' => $request->input('latitude'),
                    'pielaujama_kluda' => $request->input('acc_error'),
                    'sarezgitiba' => $request->input('sarezgitiba'),
                    'picture' => 'storage/images/findable_places/'.$imageName
                ]);
                $new_vieta->save();

                return redirect()->route('admin.dashboard')->with('success', 'Vieta veiksmīgi izveidota!');
            }
            else {
                return redirect()->route('admin.dashboard')->with('fail', 'Kaut kas nogāja greizi, veidojot vietu.');;
            }
        }

        return view('admin/create_place');
    }

    public function list_users() {
        $users = User::where('email_verified_at', '!=', null)
            ->where('id', '!=', Auth::user()->id)
            ->get()
            ->sortBy('name');
        return view('admin/view_users', compact('users'));
    }

    public function suspend_user(Request $request) {
        $request->validate([
            'user_name' => 'required|string|min:2|max:150',
        ]);

        $user = User::where('name', $request->input('user_name'))
        ->where('id', '!=', Auth::user()->id)
        ->firstOrFail();

        if ($user->suspended_profile) {
            $user->suspended_profile = false;
            $user->save();
        } else {
            $user->suspended_profile = true;
            $user->save();
        }

        return response()->json(['success' => true]);
    }

    public function list_vietas() {
        $vietas = DB::table('vietas')
            ->select('vietas.id', 'vietas.name', 'vietas.picture', 'vietas.garums', 'vietas.platums', 'vietas.pielaujama_kluda', 'vietas.sarezgitiba',
                DB::raw('count(spelevieta.vieta_id) as vieta_izmantota_count'))
            ->leftJoin('spelevieta', 'vietas.id', '=', 'spelevieta.vieta_id')
            ->groupBy('vietas.id', 'vietas.name', 'vietas.picture', 'vietas.garums', 'vietas.platums', 'vietas.pielaujama_kluda', 'vietas.sarezgitiba')
            ->get();
        return view('admin/view_vietas', compact('vietas'));
    }

    public function edit_vieta($id) {

        $vieta = Vieta::findOrFail($id);

        return view('admin/edit_vieta', compact('vieta'));
    }

    public function update_vieta(Request $request, $id) {
        $request->validate([
            'name' => 'required|string|min:5|max:300',
            'acc_error' => 'required|numeric|min:0',
            'longitude' => 'required|numeric|min:-180|max:180',
            'latitude' => 'required|numeric|min:-90|max:90',
            'picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:15000',
            'sarezgitiba' => 'required|min:1|max:10',
        ]);

        $vieta = Vieta::findOrFail($id);

        $vieta->name = $request->input('name');
        $vieta->garums = $request->input('longitude');
        $vieta->platums = $request->input('latitude');
        $vieta->pielaujama_kluda = $request->input('acc_error');
        $vieta->sarezgitiba = $request->input('sarezgitiba');

        if ($request->hasFile('picture')) {
            if ($request->file('picture')->isValid()) {
                $imageName = Str::random(64).'.'.time().'.'.Auth::user()->id.'.jpg';
                $image_resize = ImageManager::gd()->read($request->file('picture'));
                $image_resize = $image_resize->scaleDown(1440, 1440)->toJpeg(75)->__toString();
                if (Storage::put('public/images/findable_places/'.$imageName, $image_resize, 'public')) {
                    if (!is_null($vieta->picture)) {
                        Storage::delete(str_replace('storage/', 'public/', $vieta->picture));
                    }
                    $vieta->picture = 'storage/images/findable_places/'.$imageName;
                } else {
                    return back()->with('fail','Ups, neizdevās saglabāt bildi.');
                }
            }
        }

        $vieta->save();

        return back()->with('success','Vietas izmaiņas saglabātas.');

    }

    public function list_games() {
        $games = Spele::all();
        return view('admin/view_games', compact('games'));
    }

    public function edit_game($id) {
        $map_list = Karte::with('kartesobjekts')->get();

        $vietas = DB::table('vietas')
            ->select('vietas.id', 'vietas.name', 'vietas.picture', 'vietas.garums', 'vietas.platums', 'vietas.pielaujama_kluda', 'vietas.sarezgitiba',
                DB::raw('count(spelevieta.vieta_id) as vieta_izmantota_count'))
            ->leftJoin('spelevieta', 'vietas.id', '=', 'spelevieta.vieta_id')
            ->groupBy('vietas.id', 'vietas.name', 'vietas.picture', 'vietas.garums', 'vietas.platums', 'vietas.pielaujama_kluda', 'vietas.sarezgitiba')
            ->get();

        $game = Spele::findOrFail($id);

        $speles_vietas = $game->vieta()->pluck('id')->toArray();

        return view('admin/edit_game', compact('game', 'vietas', 'map_list', 'speles_vietas'));
    }

    public function update_game(Request $request, $id) {
        $request->validate([
            'game_name' => 'required|min:5|max:150',
            'game_description' => 'required|max:2000',
            'game_start' => 'required|date',
            'game_end' => 'required|date|after:game_start',
            'existing_map_selection' => 'required',
            'vieta' => 'required|array|min:1',
            'picture' => 'nullable|image|mimes:jpeg,png,jpg|max:15000',
        ]);

        $game = Spele::findOrFail($id);

        $map = null;

        if (!is_numeric($request->input('existing_map_selection'))) {
            $request->validate([
                'map_name' => 'required|min:5|max:300',
                'map_zoom' => 'required|numeric|min:0|max:100',
                'map_longitude' => 'required|numeric|min:-180|max:180',
                'map_latitude' => 'required|numeric|min:-90|max:90',
                'new_map_geojson' => 'required|string|max:65535',
            ]);

            $map = Karte::create([
                'name' => $request->input('map_name'),
                'zoom' => $request->input('map_zoom'),
                'viduspunkts_garums' => $request->input('map_longitude'),
                'viduspunkts_platums' => $request->input('map_latitude')
            ]);
            $map->kartesobjekts()->create([
                'geojson' => $request->input('new_map_geojson')
            ]);
            $map->save();
        } else {
            $map = Karte::findOrFail($request->input('existing_map_selection'));
        }

        $game->karte_id = $map->id;
        $game->name = $request->input('game_name');
        $game->description = $request->input('game_description');
        $game->start_time = $request->input('game_start');
        $game->end_time = $request->input('game_end');

        if ($request->hasFile('picture')) {
            if ($request->file('picture')->isValid()) {
                $imageName = Str::random(8).'.'.time().'.jpg';
                $image_resize = ImageManager::gd()->read($request->file('picture'));
                $image_resize = $image_resize->scaleDown(1440, 1440)->toJpeg(75)->__toString();
                if (Storage::put('public/images/games/'.$imageName, $image_resize, 'public')) {
                    if (!is_null($game->picture)) {
                        Storage::delete(str_replace('storage/', 'public/', $game->picture));
                    }
                    $game->picture = 'storage/images/games/'.$imageName;
                } else {
                    return back()->with('fail','Ups, neizdevās saglabāt bildi.');
                }

            }
        }

        $game_places_ids = $game->vieta()->pluck('id')->toArray();

        // Detach all places that are not in the request
        foreach ($game_places_ids as $game_place_id) {
            if (!in_array($game_place_id, $request->input('vieta'))) {
                $game->vieta()->detach($game_place_id);
            }
        }

        // Attach all places that are not in the game
        foreach ($request->input('vieta') as $vieta_id) {
            if (!in_array($vieta_id, $game_places_ids)) {
                $game->vieta()->attach($vieta_id);
            }
        }

        $game->save();

        return back()->with('success','Spēles izmaiņas saglabātas.');

        
    }
}
