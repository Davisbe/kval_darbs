@extends('admin.master')
@section('title', 'MAUC - admin')
@section('content')
<form enctype="multipart/form-data" id="createNewFindablePlace" accept-charset="utf-8" action="{{ route('admin.save_place') }}" method="post">
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
        <input id="name" name="name" type="text" value="{{ old('name') }}">

        <label for="sarezgitiba">Vietas sarežģītība</label>
        <input id="sarezgitiba" name="sarezgitiba" type="text" value="{{ old('sarezgitiba') }}">

        <label for="picture">Vietas fotogrāfija</label>
        <input type="file" id="picture" name="picture" accept="image/*">

        <br>

        <label for="acc_error">Vietas pieļaujamā kļūda</label>
        <input id="acc_error" name="acc_error" type="numeric" value="{{ old('acc_error') }}">

        <label for="longitude">Vietas garums</label>
        <input id="admin_place_longitude" name="longitude" type="number" step="0.000001" value="{{ old('longitude') }}">

        <label for="latitude">Vietas platums</label>
        <input id="admin_place_latitude" name="latitude" type="number" step="0.000001" value="{{ old('latitude') }}">

        <br>
        <button type="submit">
            Izveidot vietu
        </button>
        <br>

        <div id="map_view_new_place"></div>

    </div>
</form>

@if (!is_null(old('longitude')) && !is_null(old('latitude')))
    <script>
        var ADMIN_EXISING_PLACE_COORDS = { "lat": {!! old('latitude') !!}, "lng": {!! old('longitude') !!} };
    </script>

@endif

@endsection