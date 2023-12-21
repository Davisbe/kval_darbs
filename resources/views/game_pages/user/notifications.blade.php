@extends('game_pages.master')
@section('title', 'MAUC - Notifikācijas')
@section('header_title', 'Notifikācijas')
@section('content')
        
    <div class="col-section padding-lr-15">

        <div class="notification-section col-section">
            @if ($friend_request_notifications->count() > 0)
                @foreach ($friend_request_notifications as $friend_request)
                <div class="notification-row col-section" user_friend_request_name="{{ $friend_request->name }}">
                    <div class="notification-top row-section">
                        <button id="friend-notification-deny" class="notification-accept" onclick="denyFriendRequest('{{ $friend_request->name }}')">
                            <div>
                                <x-svg.cross_icon />
                            </div>
                        </button>
                        <div>
                            <x-svg.friend_request_icon /><p>no <a href="{{ $friend_request->profile_link }}" class="hyperlink-text">{{ $friend_request->name }}</a></p>
                        </div>
                        <button id="friend-notification-accept" class="notification-deny" onclick="acceptFriendRequest('{{ $friend_request->name }}')">
                            <div>
                                <x-svg.check_icon />
                            </div>
                        </button>
                    </div>
                    <div id="notification-bottom" class="notification-bottom">
                        <a href="#" class="hyperlink-text"></a>
                    </div>
                </div>
                @endforeach
            
            @else
                {{ __('Nav jaunu notifikāciju.') }}
            @endif
            
        </div>

    </div>

    <script type="text/javascript">
        var ACCEPT_FRIEND_REQUEST_URL = "{{ route('friend.request.accept', ['name' => '$']) }}";
        var DENY_FRIEND_REQUEST_URL = "{{ route('friend.request.deny', ['name' => '$']) }}";
    </script>
@endsection