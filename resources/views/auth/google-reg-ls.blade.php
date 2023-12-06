@extends('homepage.master')
@section('title', 'MAUC - pieteikties')

@section('content')
    <div class="text-section padding-lr-15">
        <h1>Pēdējais solis..</h1>
    </div>

    <form action="" class="">
        <div class="col-section padding-lr-15">
            <label for="username">Izveido savu lietotājvārdu</label>
            <input class="form-text-input" id="username" type="text">

            <div class="flex-container-center">
                <input class="form-submit-button" id="login-submit" type="button" value="IENĀKT">
            </div>
        </div>
    </form>

@endsection