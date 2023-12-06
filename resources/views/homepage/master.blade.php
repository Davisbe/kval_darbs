<!doctype html>
<html lang="lv">
    <head>
        <title>@yield('title')</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="A web app for orienteering games">
        <link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet'>
        <link rel="stylesheet" type="text/css" href="{{ asset('/css/style_homepage.css') }}">
    </head>

    <body>
        <header class="padding-lr-15">
            <nav>
                <div>
                    <a href="{{ route('homepage_index') }}"><h1>MAUC</h1></a>
                </div>
                <div>
                    <button onclick="toggleShelf()">
                        <x-svg.shelf_open fill="white" />
                    </button>
                </div>
            </nav>
        </header>

        <div id="shelf-wrapper" class="shelf-wrapper-closed">
            <div id="shelf-grid">
            <div>
                <button id="shelf-close" onclick="toggleShelf()">
                    <x-svg.shelf_open fill="white" />
                </button>
            </div>
            <div class="shelf-grid-section-icon">
                <x-svg.user_icon />
            </div>
            <div>
                <a href="{{ route('login') }}">Pieteikties</a>
            </div>
            <div>
                <a href="{{ route('register') }}">Reģistrēties</a>
            </div>
            </div>
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

        <script language="JavaScript" type="text/javascript" src="{{ url('/js/main.js') }}"></script>
    </body>
</html>