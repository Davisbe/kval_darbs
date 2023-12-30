@extends('game_pages.master')
@section('title', 'MAUC - rediģēt profilu')
@section('header_title', $userinfo->name)
@section('content')
<form enctype="multipart/form-data" id="updateProfileForm" accept-charset="utf-8" action="{{ route('profile.update', ['name' => $userinfo->name]) }}" method="post">
@csrf
    <div class="padding-lr-15 col-section">
        <div class="row-section profile-card">
            
            <div class="profile-card-img edit-profile">
                <label for="profile-picture" class="edit-profile">
                    <img id="profile-image-holder" src="{{ asset($userinfo->profile_picture) }}" alt="Profile Picture">
                    <div id="pfp-overlay" class="pfp-overlay"></div>
                    <div id="pfp-overlay-text" class="pfp-overlay-text">{{ __('Nomainīt bildi') }}</div>
                </label>
                <input type="file" id="profile-picture" name="profile-picture" accept="image/*" style="display: none;" onchange="handleProfilePicture(this.files[0])">
            </div>

            <div class="profile-card-info">
                <div>
                    <div class="col-section col-hor-center">
                        <div>
                            {{ $userinfo->places_found }}
                        </div>
                        <div>
                            {{ __('atrastas vietas') }}
                        </div>
                    </div>
                </div>
                <div>
                    <div class="col-section col-hor-center">
                        <div>
                            {{ $userinfo->gamesCount }}
                        </div>
                        <div>
                            {{ __('izspēlētas spēles') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-section">
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
            @if($errors->has('profile-picture'))
                <div class="error-msg">
                    {{ $errors->first('profile-picture') }}
                </div>
            @endif
            @if($errors->has('password_r_old'))
                <div class="error-msg">
                    {{ $errors->first('password_r_old') }}
                </div>
            @endif
            @if($errors->has('toggle'))
                <div class="error-msg">
                    {{ $errors->first('toggle') }}
                </div>
            @endif
            <div class="spelu-vesture-heading">
                <div class="spelu-vesture-virsraksts">
                    {{ __('Spēļu vēsture') }}
                </div>
                <div class="toggle">
                    <input type="radio" name="toggle" id="private" value="1" {{ $userinfo->hide_profile_info == 1 ? 'checked' : '' }}>
                    <label for="private">
                        <div>{{ __('Privāta') }}</div>
                        <div><x-svg.eye_crossed_icon /></div>
                    </label>
                    <input type="radio" name="toggle" id="public" value="0" {{ $userinfo->hide_profile_info == 1 ? '' : 'checked' }}>
                    <label for="public">
                        <div>{{ __('Publiska') }}</div>
                        <div><x-svg.eye_icon /></div>
                    </label>
                </div>
            </div>

            <div class="col-section">
            <label for="username_r">
                @if($errors->has('username_r'))
                <span class="error-msg-label">@error('username_r') {{$message}} @enderror</span>
                @else
                <span class="">Lietotājvārds</span>
                @endif
            </label>

            <input class="form-text-input" id="username_r" name="username_r" type="text" value="{{ old('username_r') }}">
            
            @if (! $userinfo->google_oauth)
            <label for="email_r">
                @if($errors->has('email_r'))
                <span class="error-msg-label">{{ $errors->first('email_r') }}</span>
                @else
                <span class="">E-pasts (būs nepieciešama atkārtota e-pasta verifikācija)</span>
                @endif
            </label>
            <input class="form-text-input" id="email_r" name="email_r" type="email" value="{{ old('email_r') }}">

            <div class="spelu-vesture-heading">
                <div class="spelu-vesture-virsraksts">
                    {{ __('Paroles maiņa') }}
                </div>
            </div>
            
                <label for="password_r">
                    @if($errors->has('password_r'))
                    <span class="error-msg-label">{{ $errors->first('password_r') }}</span>
                    @else
                    <span class="">Jauna parole</span>
                    @endif
                </label>
                <input class="form-text-input" id="password_r" name="password_r" type="password">

                <label for="rpassword">
                    @if($errors->has('rpassword'))
                    <span class="error-msg-label">{{ $errors->first('rpassword') }}</span>
                    @else
                    <span class="">Jaunā parole atkārtoti</span>
                    @endif
                </label>
                <input class="form-text-input" id="rpassword" name="rpassword" type="password">

                <label for="password_r_old">
                    @if($errors->has('password_r_old'))
                    <span class="error-msg-label">{{ $errors->first('password_r_old') }}</span>
                    @else
                    <span class="">Tagadējā parole</span>
                    @endif
                </label>
                <input class="form-text-input" id="password_r_old" name="password_r_old" type="password">
                @endif

                <div class="flex-container-center">
                    <button type="submit" class="form-submit-button">
                        {{ __('Saglabāt') }}
                    </button>
                </div>
            </form>
        </div>
        </div>
    </div>

    <script type="text/javascript">
        var MESSAGE_PROFILE_PICTURE_EDIT_PROCESSING = "{{ __('Uzgaidi..') }}";
        var MESSAGE_PROFILE_PICTURE_EDIT_ERROR = "{{ __('Izvēlies citu foto') }}";
    </script>
@endsection