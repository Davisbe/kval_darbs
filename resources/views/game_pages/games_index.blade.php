@extends('game_pages.master')
@section('title', 'MAUC - spēles')

@section('header')
    @extends('game_pages.headers.title_notification')
    @section('header_title', 'Spēles')
@endsection

@section('bottom-nav')
    @extends('game_pages.bottom_navs.sett_games_profile')
@endsection

@section('content')
    <div class="padding-lr-15">
        <div class="game-record-container game-active game-joined">

            <div class="game-record-top">
                <div class="game-record-group">
                    <x-svg.group_icon />
                </div>
            </div>
            <div class="game-record-time game-record-end">
                <div>
                    21/01/2024
                </div>
                <div class="game-record-time-svg">
                    <x-svg.stop_icon />
                </div>
                <div>
                    01:00
                </div>
            </div>
            <div class="game-record-time game-record-start">
                <div>
                    20/01/2024
                </div>
                <!--
        CHANGE SVG WITH JS WHEN SWITCHING TO ACTIVE GAME
                -->
                <div class="game-record-time-svg">
                    <x-svg.play_darkbackground_icon />
                </div>
                <div>
                    23:00
                </div>
            </div>
            
            <div class="game-record-bottom">
                <div class="game-record-name">
                    Lorem ipsum utt utt etc etc
                </div>
                <div class="game-record-players">
                    <div class="game-record-playercount">
                        25
                    </div>
                    <div class="game-record-playercount-logo">
                        <x-svg.players_applied_icon />
                    </div>
                </div>
            </div>
        </div>
        
    </div>
@endsection