@extends('homepage.master')
@section('title', 'MAUC - pēdējais solis')

@section('content')
    <div class="text-section padding-lr-15">
        <h1>Pēdējais solis..</h1>
        <p>Izveido savu lietotājvārdu</p>
    </div>

    <form accept-charset="utf-8" action="{{ route('auth.create_name_check') }}" method="post">
        @csrf
        <div class="col-section padding-lr-15">
            @if(Session::get('fail'))
                <div class="error-msg">
                    {{ Session::get('fail') }}
                </div>
            @endif
            @if(Session::get('success'))
                <div class="success-msg">
                    {{ Session::get('success') }}
                </div>
            @endif

            <label for="username_r">
                @if($errors->has('username_r'))
                <span class="error-msg-label">@error('username_r') {{$message}} @enderror</span>
                @else
                <span class="">Lietotājvārds</span>
                @endif
            </label>
            <input class="form-text-input" id="username_r" type="text" name="username_r">
            
            <div class="flex-container-center">
                <button type="submit" class="form-submit-button">
                    {{ __('Gatavs') }}
                </button>
            </div>
            
        </div>
    </form>

    

@endsection