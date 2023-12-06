@extends('homepage.master')
@section('title', 'MAUC - majaslapa')

@section('content')
    <div class="text-section padding-lr-15">
        <h1>Orientēšanās spēles</h1>
        <p>Parādi savas prasmes un sacenties ar citiem spēlētājiem, meklējot labi un ne tik labi zināmas vietas!</p>
    </div>

    <div id="start-button-section">
        <button onclick="window.location.href='{{ route('login') }}'">
            <h1>
                SĀKT
            </h1>
        </button>
        <div >
            <div>
                <x-svg.road_illustration />
            </div>
        </div>
    </div>

    <div class="text-section padding-lr-15">
        <h1>Informācijai</h1>
        <p>Spēlējot būs nepieciešams transportlīdzeklis distanču dēļ. Var izmantot jeb ko, piemēram, mašīnu vai moci, vai riteni, vai, nezinu, zirgu?</p>
    </div>
@endsection