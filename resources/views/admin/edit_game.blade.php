@extends('admin.master')
@section('title', 'MAUC - admin')
@section('content')
    <div class="admin-dashboard-selection col-section">
        <div class="admin-selection-heading">
            Izvēlnes - rediģēt spēli
        </div>

        <div class="admin-selection-content row-section">
            <button onclick="createGameSectionSwitch('c_game_sec1')">
                <div id="button_c_game_sec1" class="admin-selection-button selected">
                    Pamatinformācija
                </div>
            </button>
            <button onclick="createGameSectionSwitch('c_game_sec2')">
                <div id="button_c_game_sec2" class="admin-selection-button">
                    Karte
                </div>
            </button>
            <button onclick="createGameSectionSwitch('c_game_sec3')">
                <div id="button_c_game_sec3" class="admin-selection-button">
                    Vietas
                </div>
            </button>
            <button onclick="createGameSectionSwitch('c_game_sec4')">
                <div id="button_c_game_sec4" class="admin-selection-button">
                    Pabeigt
                </div>
            </button>
        </div>
    </div>

    <form id="admin_save_new_game" enctype="multipart/form-data" accept-charset="utf-8" action="{{ route('admin.update_game', ['id'=>$game->id]) }}" method="post">
        @csrf

        <div id="c_game_sec1" class="c_game_sec selected padding-lr-15 col-section">
            @if(Session::get('success'))
                <div class="success-msg">
                    {{ Session::get('success') }}
                </div>
            @endif
            @if(Session::get('fail'))
                <div class="error-msg">
                    {{ Session::get('fail') }}
                </div>
            @endif
            @if($errors)
                @foreach ($errors->all() as $error)
                    <div class="error-msg">
                        {{ $error }}
                    </div>
                @endforeach
            @endif
            
            <br>
            <div><a href="{{ route('game.show', ['id'=>$game->id]) }}">Skatīt spēles publisko informāciju</a></div>
            <br>
            <label for="game_name">Spēles nosaukums</label>
            <input id="game_name" name="game_name" type="text" value="{{ $game->name }}">

            <label for="game_description">Spēles apraksts</label>
            <input id="game_description" name="game_description" type="textarea" value="{{ $game->description }}">

            <label for="game_start">Spēles sākums (formāts: YYYY-MM-DD HH:MM:SS)</label>
            <input id="game_start" name="game_start" type="text" value="{{ $game->start_time }}">

            <label for="game_end">Spēles sākums (formāts: YYYY-MM-DD HH:MM:SS)</label>
            <input id="game_end" name="game_end" type="text" value="{{ $game->end_time }}">

            <label for="picture">Vietas fotogrāfija</label>
            <input type="file" id="picture" name="picture" accept="image/*">
        </div>

        <div id="c_game_sec2" class="c_game_sec padding-lr-15 col-section">

            <label for="existingMapSelection">Izvēlēties jau eksistējošu karti</label>
            <select id="existingMapSelection" name="existing_map_selection">
                <option>- Izveidot savu karti -</option>
                @foreach ($map_list as $map)
                    <option {{ $map->id == $game->karte_id ? 'selected="selected"' : '' }} value="{{ $map->id }}">{{ $map->name }}</option>
                @endforeach
                
            </select>

            <label for="map_name">Kartes nosaukums</label>
            <input id="map_name" name="map_name" type="text" value="{{ old('map_name') }}">

            <br>

            <label for="map_zoom">Kartes noklusētais pietuvinājums</label>
            <input id="map_zoom" name="map_zoom" type="numeric" value="{{ old('map_zoom') }}">

            <label for="map_longitude">Kartes centra garums</label>
            <input id="map_longitude" name="map_longitude" type="number" step="0.000001" value="{{ old('map_longitude') }}">

            <label for="map_latitude">Kartes centra platums</label>
            <input id="map_latitude" name="map_latitude" type="number" step="0.000001" value="{{ old('map_latitude') }}">
            
            <button id="set_map_inputs_current" type="button">Iestatīt tagadējās kartes vērtības</button>

            <br>


            <label for="mapColorSelection">Kartes poligonu krāsas</label>
            <select id="mapColorSelection">
                <option value="#7fab76">Spēlēšanas zona</option>
                <option value="#ab7676">Aizliegta zona</option>
            </select>
            <div id="map_edit"></div>
            <input type="hidden" name="new_map_geojson" id="new_map_geojson_input">

        </div>

        <div id="c_game_sec3" class="c_game_sec padding-lr-15 col-section places-list-table-wrapper">
            <table>
                <tr>
                    <th>Nosaukums</th>
                    <th>Attēls</th>
                    <th>Garums</th>
                    <th>Platums</th>
                    <th>Pielaujama kļūda</th>
                    <th>Sarezģītība</th>
                    <th>Izmantota reizes</th>
                    <th>Izmantot spēlē</th>
                </tr>
                @foreach ($vietas as $vieta)
                    <tr>
                        <td>{{ $vieta->name }}</td>
                        <td><img src="{{ asset($vieta->picture) }}" alt=""></td>
                        <td>{{ $vieta->garums }}</td>
                        <td>{{ $vieta->platums }}</td>
                        <td>{{ $vieta->pielaujama_kluda }}</td>
                        <td>{{ $vieta->sarezgitiba }}</td>
                        <td>{{ $vieta->vieta_izmantota_count }}</td>
                        <td><input type="checkbox" name="vieta[]" value="{{ $vieta->id }}" {{ in_array($vieta->id, $speles_vietas) ? 'checked' : '' }}></td>
                    </tr>
                @endforeach
            </table>

        </div>

        <div id="c_game_sec4" class="c_game_sec padding-lr-15 col-section">
            <button id="admin_update_game_button_submit" type="submit">
                Saglabāt izmaiņas
            </button>
        </div>
    
    </form>

    <script>
        var ADMIN_MAPS_INFO = {!! json_encode($map_list) !!};
        var ADMIN_EDIT_GAME_TIMEINFO = { 'start_time': "{{ $game->start_time }}", 'end_time': "{{ $game->end_time }}" };
    </script>

@endsection