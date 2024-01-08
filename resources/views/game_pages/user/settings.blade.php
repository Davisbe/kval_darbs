@extends('game_pages.master')
@section('title', 'MAUC - iestatījumi')
@section('header_title', 'Iestatījumi')
@section('content')
    <div class="padding-lr-15">
        <div class="logout-div">
            <a href="{{ route('auth.logout') }}">Atteikties</a>
        </div>
    </div>
@endsection