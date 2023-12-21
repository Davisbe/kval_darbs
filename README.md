<h1>Pirms palaišanas produkcijā, apskatīt komentārus ar "!!!" prefiksu</h1>
<p> Ā jā, vajadzēs arī pārbaudīt, lai priekš PHP ir GD - https://github.com/Intervention/image?tab=readme-ov-file#:~:text=Supported%20Image%20Libraries-,GD%20Library,-Imagick%20PHP%20extension </p>
<h1>Citi soļi produkcijai:</h1>
<ul>
    <li>Noņem debug:
        APP_ENV=production,
        APP_DEBUG=false, u.c. kodā ieliktas debug lietas, kā dd() funkcijas</li>
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