@extends('game_pages.master')
@section('title', 'MAUC - spēles info')
@section('header_title', 'Spēles info')
@section('content')
    <div class="col-section">
        <div class="image-wide" style="background-image: url('{{ asset($game->picture) }}')">
            <div class="game-img-overlay">
                
            </div> 
            <div class="img-overlay-text">{{ $game->name }}</div>
        </div>

        <div class="padding-lr-15 col-section">
            <table class="game-attribute-table">
                <tr class="game-attribute">
                    <td><x-svg.play_darkbackground_icon /></td>
                    <td class="table-colomntext-right">{{ __('Sākuma laiks:') }}</td>
                    <td id="game-start-time">wdwd</td>
                </tr>
                <tr class="game-attribute">
                    <td><x-svg.stop_icon /></td>
                    <td class="table-colomntext-right">{{ __('Beigu laiks:') }}</td>
                    <td id="game-end-time">wdwdwd</td>
                </tr>
                <tr class="game-attribute">
                    <td><x-svg.players_applied_icon /></td>
                    <td class="table-colomntext-right">{{ __('Pieteikušies spēlētāji:') }}</td>
                    <td>{{ $game->player_count }}</td>
                </tr>
                <tr class="game-attribute">
                    <td><x-svg.meklejama_vieta_icon /></td>
                    <td class="table-colomntext-right">{{ __('Meklējamās vietas:') }}</td>
                    <td>{{ $game->meklejamas_vietas }}</td>
                </tr class="game-attribute">
            </table>

            <div class ="game-description">
                <h1>Apraksts</h1>
                <p>{{ $game->description }}</p>
            </div>

            <div class ="game-results">
                <h1>Spēles rezultāti:</h1>
                
                <div class="game-scoreboard-wrapper results col-section">

                    @foreach ($game_results as $group)
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

    <script>
        var GAME_SHOW_START_TIME = "{{ $game->start_time }}";
        var GAME_SHOW_END_TIME = "{{ $game->end_time }}";
    </script>
@endsection