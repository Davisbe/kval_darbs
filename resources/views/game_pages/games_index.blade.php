@extends('game_pages.master')
@section('title', 'MAUC - spēles')
@section('header_title', 'Spēles')
@section('content')
    <div class="padding-lr-15">
        <div id="game-record-container" class="col-section">
        </div>
    </div>
    <script type="text/javascript">
        var LOAD_MORE_AVAILABLE_GAMES_URL = "{{ route('games_index.loadmore') }}";
        var MESSAGE_NO_MORE_GAMES = "{{ __('Pieejamo spēļu saraksta beigas') }}";
        var MESSAGE_LOADING_GAMES = '';
        var GAME_INDEX_FASTFORWARD_ICON = `<x-svg.start_fastforward_icon />`;
        var GAME_INDEX_PLAY_ICON = `<x-svg.play_darkbackground_icon />`;
        var GAME_INDEX_AVAILABLE_GAMES_HTML = `
        <div class="game-record-container $RECORD_GAME_ACTIVE_CLASS$ $RECORD_GAME_JOINED_CLASS$">

            <div class="game-record-top" style="background-image: url('$RECORD_IMAGE$');" onclick="window.location.href='$RECORD_GAME_LINK$'">
                <div class="game-record-group">
                    <x-svg.group_icon />
                </div>
            </div>
            <div class="game-record-time game-record-end">
                <div>
                    $RECORD_TIME_END_DATE$
                </div>
                <div class="game-record-time-svg">
                    <x-svg.stop_icon />
                </div>
                <div>
                $RECORD_TIME_END_TIME$
                </div>
            </div>
            <div class="game-record-time game-record-start">
                <div>
                    $RECORD_TIME_START_DATE$
                </div>
                <!--
        CHANGE SVG WITH JS WHEN SWITCHING TO ACTIVE GAME
                -->
                <div class="game-record-time-svg">
                    $RECORD_START_SVG$
                </div>
                <div>
                    $RECORD_TIME_START_TIME$
                </div>
            </div>
            
            <div class="game-record-bottom">
                <div class="game-record-name">
                    $RECORD_NAME$
                </div>
                <div class="game-record-players">
                    <div class="game-record-playercount">
                        $RECORD_PLAYER_COUNT$
                    </div>
                    <div class="game-record-playercount-logo">
                        <x-svg.players_applied_icon />
                    </div>
                </div>
            </div>
        </div>
        `;
    </script>
@endsection