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

            <div class="game-invites">

                <h1>Uzaicinājumi spēlēt</h1>
                
                @if (count($game_invites) == 0)
                    <div class="info-text-small-centered">{{ ('Tev nav neviena uzaicinājuma uz šo spēli') }}</div>
                @else
                    @foreach ($game_invites as $game_invite)
                    <div class="notification-row col-section" user_friend_request_name="">
                        <div class="notification-top row-section">
                            <button id="friend-notification-deny" class="notification-accept" onclick="">
                                <div>
                                    <x-svg.cross_icon />
                                </div>
                            </button>
                            <div>
                                <x-svg.friend_request_icon /><p>no <a href="" class="hyperlink-text"></a></p>
                            </div>
                            <button id="friend-notification-accept" class="notification-deny" onclick="">
                                <div>
                                    <x-svg.check_icon />
                                </div>
                            </button>
                        </div>
                        <div id="notification-bottom" class="notification-bottom">
                            <a href="#" class="hyperlink-text"></a>
                        </div>
                    </div>
                    @endforeach
                @endif

            </div>

            <div class="flex-container-center pregame-continue-button">
                <button class="form-submit-button" onclick="getLocation()">
                    {{ __('Izveidot grupu, pievienoties') }}
                </button>
            </div>
        </div>
        

    </div>

    <script>
        var GAME_SHOW_START_TIME = "{{ $game->start_time }}";
        var GAME_SHOW_END_TIME = "{{ $game->end_time }}";
    </script>
@endsection