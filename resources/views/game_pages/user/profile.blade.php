@extends('game_pages.master')
@section('title', 'MAUC - '.$userinfo->name)

@section('header')
    @extends('game_pages.headers.title_notification')
    @section('header_title', $userinfo->name)'
@endsection

@section('bottom-nav')
    @extends('game_pages.bottom_navs.sett_games_profile')
@endsection

@section('content')
    <div class="padding-lr-15 col-section">
        <div class="row-section profile-card">
            <div class="profile-card-img">
                <img src="{{ asset($userinfo->profile_picture) }}" alt="Profile Picture">
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

        <div class="row-section col-hor-center profile-button-row">
            @if ($userinfo->name == @Auth::user()->name)
            <button onclick="window.location.href='{{ route('profile.edit', ['name' => $userinfo->name,]) }}'">
                <div>
                    <x-svg.edit_profile_icon />
                </div>
            </button>
            <button onclick="window.location.href='{{ route('friend.list') }}'">
                <div>
                    <x-svg.friends_icon />
                </div>
            </button>
            <button onclick="window.location.href='{{ route('users.search') }}'">
                <div>
                    {{ __('Uzaicināt draugus') }}
                </div>
            </button>
            @elseif ($userinfo->showFriendInviteButton)
                <button id="friendRequestButton" onclick="sendFriendRequest()">
                @csrf
                    <div id="friendRequestDiv">
                        {{ ('Nosūtīt draugu pieprasījumu') }}
                    </div>
                </button>
            @endif
        </div>

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

        <div class="col-section">
            <div class="spelu-vesture-heading row-section">
                <div class="spelu-vesture-virsraksts">
                    {{ __('Spēļu vēsture') }}
                </div>
                <div class="spelu-vesture-status row-section">
                    <div>
                    {{ __($userinfo->hide_profile_info == 1 ? 'vēsture privāta' : 'vēsture publiska') }}
                    </div>
                    <div>
                    @if ($userinfo->hide_profile_info == 1)
                        <x-svg.eye_crossed_icon />
                    @else
                        <x-svg.eye_icon />
                    @endif
                    </div>
                </div>
            </div>

            @if (count($userinfo->gamesHistory) > 0)
                @foreach ($userinfo->gamesHistory as $game_date => $game_records)
                <div class="spelu-vesture-datuma-grupa col-section">
                    <div class="spelu-vesture-datums">
                        {{ $game_date }}
                    </div>
                    @foreach ($game_records as $game_record)
                        <div class="spelu-vesture-ieraksts">
                            <div class="speles-ieraksts-bilde" style="background-image: url('{{ asset($game_record->picture) }}')">
                            </div>
                            <div class="speles-ieraksts-nosaukums">
                                {{ $game_record->name }}
                            </div>
                        </div>
                    @endforeach
                </div>
                @endforeach
            @else
            <div class="spelu-vesture-datuma-grupa col-section">
                <div class="spelu-vesture-datums">
                    {{ __('Tukšums..') }}
                </div>
            </div>
            @endif
        </div>
    </div>

    <script type="text/javascript">
        var SEND_FRIEND_REQUEST_URL = "{{ route('friend.request.send', ['name' => $userinfo->name]) }}";
        var MESSAGE_FRIEND_REQUEST_SENT = "{{ __('Draugu pieprasījums nosūtīts') }}";
        var MESSAGE_FRIEND_REQUEST_WAIT = "{{ __('Uzgaidi..') }}";
        var MESSAGE_FRIEND_REQUEST_ERROR = "{{ __('Kļūda') }}";
        var MESSAGE_FRIEND_REQUEST_ACCEPTED = "{{ __('Apsveicam ar jauno draugu!') }}";
        var MESSAGE_FRIEND_REQUEST_ALREADY_SENT = "{{ __('Uzaicinājums jau ir nosūtīts') }}";
    </script>
@endsection