@extends('game_pages.active_game_master')
@section('title', 'MAUC - grupa')
@section('header_title', 'Spēles grupa')
@section('content')
    <div class="image-wide" style="background-image: url('{{ asset($game_info->picture) }}')">
        <div class="game-img-overlay">
            
        </div> 
        <div class="img-overlay-text">{{ $game_info->name }}</div>
    </div>
    <div class="padding-lr-15 col-section">

        <div id="game-group-member-list" class="game-group-member-list col-section">
        @foreach ($group_members as $group_member)
            <div class="friend-result row-section {{ $group_member->active == 0 ? 'ready' : '' }}" group_member_name="{{ $group_member->name }}">
                <div class="friend-info">
                    <div class="friend-result-image">
                        <img src="{{ $group_member->profile_picture }}" alt="Profile Picture">
                    </div>
                    <div class="friend-result-text">
                        <a href="{{ $group_member->profile_link }}">{{ $group_member->name }}</a>
                    </div>
                    @if ($group_leader->name == $group_member->name)
                    <div class="group-member-owner-icon">
                        <x-svg.group_owner_icon />
                    </div>
                    @endif
                    <div class="group-member-ready-icon">
                        <x-svg.check_icon />
                    </div>
                </div>
                <div class="friend-actions">
                    @if (($group_member->name != Auth::user()->name) && ($group_leader->name == Auth::user()->name))
                    <div class="remove-friend">
                        <button onclick="removeGroupMember( '{{ $group_member->name }}' )">
                            {{ __('Izmest') }}
                        </button>
                    </div>
                    @endif
                </div>
            </div>
        @endforeach
        </div>

        @if ($group_leader->name == Auth::user()->name)
        <div class="invite-friends-to-group col-section">
            <h1>{{ _('Uzaicināt draugus grupā') }}</h1>
            @if (count($friendlist_invitable) > 0)
                @foreach ($friendlist_invitable as $friend)
                    <div class="friend-result row-section" friend_name="{{ $friend->name }}">
                        <div class="friend-info">
                            <div class="friend-result-image">
                                <img src="{{ $friend->profile_picture }}" alt="Profile Picture">
                            </div>
                            <div class="friend-result-text">
                                <a href="{{ $friend->profile_link }}">{{ $friend->name }}</a>
                            </div>
                        </div>
                        <div class="friend-actions">
                            @if (($friend->name != Auth::user()->name) && ($group_leader->name == Auth::user()->name))
                            <div class="remove-friend">
                                <button id="friend-group-invite-button" onclick="inviteFriendToGroup( '{{ $friend->name }}' )">
                                    {{ __('Uzaicināt') }}
                                </button>
                            </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            @else
                <div class="info-text-small-centered">{{ ('Šobrīd nav neviena uzaicinājama drauga') }}</div>
            @endif

        </div>
        @endif

        <div class="info-text-small-centered">
            <a href="{{ route('group.leave', ['id' => $game_info->id]) }}" class="hyperlink-text">{{ ('Pamest grupu (un spēli)') }}</a>
        </div>

    </div>
    
    

    <script type="text/javascript">
        var REMOVE_GROUP_MEMBER_URL = "{{ route('group.remove_member', ['id' => $game_info->id, 'name' => '$']) }}";
        var INVITE_FRIEND_TO_GROUP_URL = "{{ route('group.invite_friend', ['id' => $game_info->id, 'name' => '$']) }}";
        var INVITE_FRIEND_TO_GROUP_SENT_MESSAGE = "{{ __('Uzaicinājums nosūtīts') }}";
        var GAME_GROUP_IS_USER_LEADER = "{{ $group_leader->name == Auth::user()->name }}";
        var GAME_GROUP_AUTHED_USER = "{{ Auth::user()->name }}";
        var GAME_GROUP_LEADER_SVG = `<x-svg.group_owner_icon />`;
    </script>
@endsection