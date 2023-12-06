@extends('homepage.master')
@section('title', 'MAUC - reģistrēties')

@section('content')
    <div class="text-section padding-lr-15">
        <h1>Reģistrēties</h1>
    </div>

    <form id="registerForm" accept-charset="utf-8" action="{{ route('auth.save') }}" method="post">
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
            <input class="form-text-input" id="username_r" name="username_r" type="text" value="{{ old('username_r') }}">

            <label for="email_r">
                @if($errors->has('email_r'))
                <span class="error-msg-label">{{ $errors->first('email_r') }}</span>
                @else
                <span class="">E-pasts</span>
                @endif
            </label>
            <input class="form-text-input" id="email_r" name="email_r" type="email" value="{{ old('email_r') }}">

            <label for="password_r">
                @if($errors->has('password_r'))
                <span class="error-msg-label">{{ $errors->first('password_r') }}</span>
                @else
                <span class="">Parole</span>
                @endif
            </label>
            <input class="form-text-input" id="password_r" name="password_r" type="password">

            <label for="rpassword">
                @if($errors->has('rpassword'))
                <span class="error-msg-label">{{ $errors->first('rpassword') }}</span>
                @else
                <span class="">Parole atkārtoti</span>
                @endif
            </label>
            <input class="form-text-input" id="rpassword" name="rpassword" type="password">

            <div class="flex-container-center">
                <button type="submit" class="form-submit-button">
                    {{ __('Reģistrēties') }}
                </button>
            </div>
        </div>
    </form>

    <div id="auth-alt" class="col-section col-hor-center padding-lr-15">
        <div class="hr-div">
            <hr>
        </div>
        <div>
            Jau esi reģistrējies? <a href="{{ route('login') }}"><span class="hyperlink-text">Pieteikties šeit</span></a>
        </div>
        <div class="flex-container-center">
            <button id="google-button"><img src="{{ asset('images/static/google-logo.png') }}" alt="Google logo"> Reģistrēties ar Google</button>
        </div>
    </div>

    

@endsection