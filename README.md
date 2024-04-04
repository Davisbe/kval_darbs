Qualification project for my collage degree.

As a part of the qualification project, the project “Car orienteering game” was created. It is a web site whose primary function is to provide the orientation game activities. Any registered user is capable of entering an orientation game, the process of which is as follows: the user applies for the game, which as a set start time and date, at the beginning of the game the user is given a set of obscure photos with the places to be found, when a place of interest is found the user must take his own photograph of the place and must upload the photograph to the web site. The game has a time limit. The winner or winners are the user with the most locations found. Access to the website is very limited for unregistered users – applying for and starting the orienteering game is only available to registered users. The web site was created using the PHP 8.2.4 programming language with the Laravel 10.31.0 framework. The data is stored in a MySQL database.


<hr>
<p> Priekš PHP nepieciešams GD - https://github.com/Intervention/image?tab=readme-ov-file#:~:text=Supported%20Image%20Libraries-,GD%20Library,-Imagick%20PHP%20extension </p>
<h1>Praktiskai palaišanai serverī:</h1>
<ul>
    <li>Noņem debug:
        APP_ENV=production,
        APP_DEBUG=false</li>
    <li>php artisan migrate</li>
    <li>php artisan db:seed // piem. priekš profiliem ar lietotājvārdiem "admin", "mod", "dev" utt</li>
    <li>composer dump-autoload --optimize</li>
    <li>php artisan config:cache</li>
    <li>php artisan event:cache</li>
    <li>php artisan route:cache</li>
    <li>php artisan view:cache</li>
    <li>php artisan storage:link</li>
</ul>

<p>Tālāk, visas .env konfigurācijas un statisko fotogrāfiju transferi</p>
