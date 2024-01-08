@extends('game_pages.active_game_master')
@section('title', 'MAUC - meklējamās vietas')
@section('header_title', 'Meklējamās vietas')
@section('content')
    <div id="place-view-window" class="place-view-window">
        <div id="place-view-window-content" class="place-view-window-content">
            <div class="place-view-window-close">
                <div id="loading_precise_loacation" class="loading-precise-loacation">
                    Cenšamies noteikt precīzu atrašanās vietu...
                </div>
                <button id="place-view-window-close-button">
                    <x-svg.cross_light_icon />
                </button>
            </div>
            <div class="place-view-window-image">
                <img id="place-view-image-tag" src="https://mauclocal.lv/storage/images/static/placeholder-map.png" alt="">
            </div>
            @if ($user_is_leader)

                <div id="users_far_wrapper" class="users-far-wrapper col-section">
                    <div class="users_far_heading">
                        Spēlētāji, kuri ir pārāk tālu no vietas:
                    </div>
                    <div id="users_far_list" class="users-far-list col-section">
                        <div class="users_far_user">
                            name
                        </div>
                    </div>
                </div>

                <form enctype="multipart/form-data" id="submit_place_form" action="{{ route('active_game.place_submit') }}" method="post">
                    @csrf
                    <div class="place-view-window-submit">
                        <div>
                            <button id="place-view-window-submit-button" type="button">
                                <x-svg.game_place_submit_button />
                            </button>
                            <input type="file" id="place-found-picture-input" name="place_found_picture" accept="image/*" style="display: none;">
                            <input id="place-found-id" name="place_found_id" style="display: none;">
                        </div>
                    </div>
                </form>
            @else
                <div class="place-view-window-submit">
                    <div>
                        <button id="place-view-window-submit-button">
                            Iesniegt lokāciju
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="padding-lr-15 col-section">
        <div class="active-game-places-wrapper">
            @foreach ($game_places as $place)
            <div class="active-game-place" style="background-image: url({{ $place->picture }});" div_place_id="{{ $place->id }}">
                <div class="active-game-difficulty">
                    Sarežģītība: {{ $place->sarezgitiba }}
                </div>
            </div>
            @endforeach
        </div>
    </div>
    
    <script>
        var ACTIVE_GAME_PLACE_LIST = {!! json_encode($game_places) !!};
        var ACTIVE_GAME_PLACE_TRY_URL = '{{ route('active_game.place_try') }}';
    </script>
    
@endsection