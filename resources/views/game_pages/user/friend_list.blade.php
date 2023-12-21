@extends('game_pages.master')
@section('title', 'MAUC - Draugu saraksts')
@section('header_title', 'Draugi')
@section('content')
    <div id="confirmation-window" class="confirmation-window">
        <div id="confirmation-window-content" class="confirmation-window-content col-section">
            <h2>
                {{ __('Apstiprināt:') }}
            </h2>
            <p id="confirmation-window-text">
                {{ __('Vai tiešām vēlaties noņemt $ no draugu saraksta?') }}
            </p>
            <div class="confirm-button-wrapper">
                <button id="confirmation-button-confirm">
                    Jā
                </button>
                <button id="confirmation-button-cancel">
                    Nē
                </button>
            </div>
        </div>
    </div>

    <div class="padding-lr-15 col-section">
        <div class="friends-results col-section">
            @if ($friendlist->count() == 0)
                <div class="row-section">
                    <div class="col-section col-hor-center">
                        <div>
                            {{ __('Jūs neesat pievienojis nevienu draugu.') }}
                        </div>
                    </div>
                </div>
            @else
                @foreach ($friendlist as $friend)
                <div class="friend-result row-section" friend-row-name="{{ $friend->name }}">
                    <div class="friend-info">
                        <div class="friend-result-image">
                            <img src="{{ $friend->profile_picture }}" alt="Profile Picture">
                        </div>
                        <div class="friend-result-text">
                            <a href="{{ $friend->profile_link }}">{{ $friend->name }}</a>
                        </div>
                    </div>
                    <div class="friend-actions">
                        <div class="remove-friend">
                            <button onclick="removeFriendConfirmWindow('{{ $friend->name }}')">
                                {{ __('Noņemt draugu') }}
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            @endif
            
        </div>

    </div>

    <script type='text/javascript'>
        var MESSAGE_FRIEND_REMOVE_CONFIRMATION = "{{ __('Vai tiešām vēlaties noņemt $ no draugu saraksta?') }}";
        var REMOVE_FRIEND_URL = "{{ route('friend.remove', ['name' => '$']) }}";
    </script>
@endsection