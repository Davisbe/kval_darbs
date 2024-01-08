@extends('game_pages.active_game_master')
@section('title', 'MAUC - aktīva spēle')
@section('header_title', 'Aktīva spēle')
@section('content')
    
    <div class="padding-lr-15 col-section">
        
        <div class="game-groups-info row-section">

            <div class='game-my-group-info col-section'>

                <div class="game-my-group-info-heading">
                    Tava grupa
                </div>

                <div class ="game-my-group-info-content col-section">
                    <div class="game-info-button">
                        <button onclick="window.location.href='{{ route('active_game.game_info') }}'">
                            Spēles info
                        </button>
                    </div>
                    <div class="group-info-button" onclick="window.location.href='{{ route('active_game.group_view') }}'">
                        <button>
                            <div>
                                <x-svg.group_icon />
                            </div>
                        </button>
                    </div>
                    <div class="game-my-group-info-div">
                        {{ $my_group_places_found_count }}/{{ $game_places_count }} vietas atrastas
                    </div>
                    <div class="game-my-group-info-div">
                        {{ $my_group_points }} punkti
                    </div>
                    
                </div>
                
            </div>

            <div class='game-group-scoreboard col-section'>

                <div class="game-group-scoreboard-heading">
                    {{ $game_groups_count }} Grupas
                </div>

                <div class="game-scoreboard-wrapper col-section">

                    @foreach ($top_groups as $group)
                        <div class="friend-result row-section" group_member_name="">
                            <div class="friend-info">
                                <div class="friend-result-image">
                                    <img src="{{ $group->profile_picture }}" alt="Profile Picture">
                                </div>
                                <div class="friend-result-text">
                                    <a href="{{ $group->profile_link }}">{{ $group->name }}</a>
                                </div>
                                <div class="group-member-owner-icon">
                                    <x-svg.group_owner_icon />
                                </div>

                            </div>
                            <div class="friend-actions">
                                {{ $group->punkti }}
                            </div>
                        </div>
                    @endforeach
                    

                </div>
            </div>
        </div>

    </div>

    <div id="active_game_map">

    </div>

    <script language="JavaScript" type="text/javascript">
        const ACTIVE_GAME_MAP_INFO = {!! json_encode($map_list) !!};
        const ACTIVE_GAME_MAP_PLACES = {!! $findable_places_loc !!};
    </script>
    
@endsection