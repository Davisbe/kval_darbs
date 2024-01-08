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
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
        crossorigin=""/>
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
        crossorigin=""></script>

    </head>
    <body>
        <header class="padding-lr-15">
            <nav>
                <div class="row-lmr-1">
                    <div onclick="window.location.href='{{ route('active_game.index') }}'">
                        <x-svg.back_arrow_icon />
                    </div>
                </div>
                <div class="row-lmr-2">
                    <div>
                        <h1>@yield('header_title')</h1>
                    </div>
                </div>
                <div class="row-lmr-3">
                    <div onclick="window.location.href='{{ route('active_game.chat_view') }}'">
                        <x-svg.chat_icon />
                    </div>
                </div>
            </nav>
        </header>

        <div class="padding-lr-15 bottom-nav">
            <nav>
                <div class="row-lmr-1">
                    <div>
                        <button onclick="window.location.href='{{ route('settings_view') }}'">
                            <x-svg.settings_icon />
                        </button>
                    </div>
                </div>
                <div class="row-lmr-2">
                    <div>
                        <button onclick="window.location.href='{{ route('active_game.places_list') }}'">
                            <x-svg.meklejama_vieta_big_icon />
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