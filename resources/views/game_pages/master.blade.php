<!doctype html>
<html lang="lv">
    <head>
        <title>@yield('title')</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="A web app for orienteering games">
        <link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet'>
        <link rel="stylesheet" type="text/css" href="{{ asset('/css/style_game_pages.css') }}">
    </head>

    <body>
        @yield('header')

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