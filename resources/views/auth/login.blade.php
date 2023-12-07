@extends('homepage.master')
@section('title', 'MAUC - pieteikties')

@section('content')
    <div class="text-section padding-lr-15">
        <h1>Pieteikties</h1>
    </div>

    <form accept-charset="utf-8" action="{{ route('auth.check') }}" method="post">
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

            <label for="email_r">
                @if($errors->has('email_r'))
                <span class="error-msg-label">@error('email_r') {{$message}} @enderror</span>
                @else
                <span class="">E-pasts</span>
                @endif
            </label>
            <input class="form-text-input" id="email_r" type="email" name="email_r">
            

            <label for="password_r">
                @if($errors->has('password_r'))
                <span class="error-msg-label">@error('password_r') {{$message}} @enderror</span>
                @else
                <span class="">Parole</span>
                @endif
            </label>
            <input class="form-text-input" id="password_r" type="password" name="password_r">

            <div class="flex-container-center">
                <button type="submit" class="form-submit-button">
                    {{ __('Ienākt') }}
                </button>
            </div>
            
        </div>
    </form>

    <div id="auth-alt" class="col-section col-hor-center padding-lr-15">
        <div class="hr-div">
            <hr>
        </div>
        <div>
            Neesi reģistrējies? <a href="{{ route('register') }}"><span class="hyperlink-text">Reģistrējies šeit</span></a>
        </div>
        <div class="flex-container-center">
            <button id="google-button" onclick="window.location.href='{{ route('auth.google.redirect') }}'"><img src="{{ asset('images/static/google-logo.png') }}" alt="Google logo"> Pieteikties ar Google</button>
        </div>
    </div>

    

@endsection