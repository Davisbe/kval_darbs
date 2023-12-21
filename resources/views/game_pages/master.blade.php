<!DOCTYPE html>
<html lang="lv">
    <head>
        <title>@yield('title')</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="A web app for orienteering games">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet'>
        <link rel="stylesheet" type="text/css" href="{{ asset('/css/style_game_pages.css') }}">
    </head>
    <body>
        <header class="padding-lr-15">
            <nav>
                <div class="row-lmr-1">
                    <div>
                    </div>
                </div>
                <div class="row-lmr-2">
                    <div>
                        <h1>@yield('header_title')</h1>
                    </div>
                </div>
                <div class="row-lmr-3">
                    <div>
                        <button onclick='window.location.href="{{ route('notifications') }}"'>
                            <x-svg.notification_icon />
                        </button>
                    </div>
                </div>
            </nav>
        </header>

        <div class="padding-lr-15 bottom-nav">
            <nav>
                <div class="row-lmr-1">
                    <div>
                        <button onclick="">
                            <x-svg.settings_icon />
                        </button>
                    </div>
                </div>
                <div class="row-lmr-2">
                    <div>
                        <button onclick="window.location.href='{{ route('games_index') }}'">
                            <x-svg.games_list_icon />
                        </button>
                    </div>
                </div>
                <div class="row-lmr-3">
                    <div>
                        <button onclick="window.location.href='{{ route('profile.show', ['name' => Auth::user()->name,]) }}'">
                        <img src="{{ asset(Auth::user()->profile_picture) }}" alt="">
                        </button>
                    </div>
                </div>
            </nav>
        </div>

        <div class="skip-top-54"></div>
        <div class="main-background">
            <div class="main-content-wrapper">
                @yield('content')
            </div>
            <footer>
                <div><p>Map data &copy; <a href="https://www.openstreetmap.org/copyright" target=”_iHopeThisIsAGoodEnoughCreditForOpenStreetMap”>OpenStreetMap</a> contributors</p></div>
                <div>© Dāvis Safronovs 2023</div>
            </footer>
        </div>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script language="JavaScript" type="text/javascript" src="{{ url('/js/main.js') }}"></script>
    </body>
</html>