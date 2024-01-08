@extends('game_pages.active_game_master')
@section('title', 'MAUC - spēles čats')
@section('header_title', 'Spēles čats')
@section('content')
    
    <div class="padding-lr-15 col-section">

        <div class="chat-container">
            <div class="chat-wrapper">
                <div class="chat-messages-wrapper">
                    <div class="chat-messages">

                    @if ($chat_messages->count() != 0)
                        @foreach ($chat_messages as $message)
                            <div class="{{ $message->name == $auth_user->name ? 'chat-message-me' : 'chat-message-other' }}">
                                <div class="chat-message-user">
                                    
                                    @if ($message->name == $auth_user->name)
                                    <div class="chat-message-user-name">
                                        <a href="{{ route('profile.show', ['name' => $message->name]) }}">{{ $message->name }}</a>
                                    </div>
                                    <div class="chat-message-user-image">
                                        <img src="{{ asset($message->profile_picture ) }}" alt="Profile Picture">
                                    </div>
                                    @else
                                    <div class="chat-message-user-image">
                                        <img src="{{ asset($message->profile_picture ) }}" alt="Profile Picture">
                                    </div>
                                    <div class="chat-message-user-name">
                                        <a href="">{{ $message->name }}</a>
                                    </div>

                                    @endif
                                </div>
                                <div class="chat-message-text">
                                    {{ $message->text }}
                                </div>
                            </div>
                        @endforeach
                    @endif
                        
                        
                    </div>
                </div>
            </div>

            <form action="{{ route('active_game.chat_send') }}" method="post">
                    @csrf
            <div class="chat-input-wrapper">
                    <input type="text" name="message" placeholder="Raksti savu ziņu..." required minlength="1" maxlength="300">
                    <button type="submit"><x-svg.send_message_icon /></button>
                
            </div>
            </form>
            

        </div>

    </div>

    <script language="JavaScript" type="text/javascript">

    </script>
    
@endsection