@extends('admin.master')
@section('title', 'MAUC - admin')
@section('content')

<form enctype="multipart/form-data" id="createNewFindablePlace" accept-charset="utf-8" action="{{ route('admin.update_vieta', ['id'=>$vieta->id]) }}" method="post">
    @csrf
    <div id="c_game_sec2" class="c_game_sec selected padding-lr-15 col-section">
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

        <label for="name">Vietas nosaukums</label>
        <input id="name" name="name" type="text" value="{{ $vieta->name }}">

        <label for="sarezgitiba">Vietas sarežģītība</label>
        <input id="sarezgitiba" name="sarezgitiba" type="text" value="{{ $vieta->sarezgitiba }}">

        <label for="picture">Vietas fotogrāfija</label>
        <input type="file" id="picture" name="picture" accept="image/*">

        <br>

        <label for="acc_error">Vietas pieļaujamā kļūda</label>
        <input id="acc_error" name="acc_error" type="numeric" value="{{ $vieta->pielaujama_kluda }}">

        <label for="longitude">Vietas garums</label>
        <input id="admin_place_longitude" name="longitude" type="number" step="0.000001" value="{{ $vieta->garums }}">

        <label for="latitude">Vietas platums</label>
        <input id="admin_place_latitude" name="latitude" type="number" step="0.000001" value="{{ $vieta->platums }}">

        <br>
        <button type="submit">
            Apstiprināt izmaiņas
        </button>
        <br>

        <div id="map_view_new_place"></div>

    </div>
</form>

<script>
    var ADMIN_EXISING_PLACE_COORDS = { "lat": {!! $vieta->platums !!}, "lng": {!! $vieta->garums !!} };
</script>

@endsection