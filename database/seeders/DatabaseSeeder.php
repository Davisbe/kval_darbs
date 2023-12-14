<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Karte;
use App\Models\Spele;
use App\Models\Grupa;
use Carbon\Carbon;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(20)->create();

        $admin = User::create([
            'name' => 'admin',
            'email' => 'admin@admin',
            'password' => Hash::make('dwertygEDWuk7u6yFE5t4e45'),
        ]);
        $admin->save();
        $mod = User::create([
            'name' => 'mod',
            'email' => 'mod@mod',
            'password' => Hash::make('3dwertyWFuk7u6y5tDD4e45'),
        ]);
        $mod->save();
        $dev = User::create([
            'name' => 'dev',
            'email' => 'dev@dev',
            'password' => Hash::make('3dwertyWFuk7u6y5tDD4e45'),
        ]);
        $dev->save();

        $testing_user = User::create([
            'name' => 'testuser',
            'email' => 'test@test',
            'password' => Hash::make('qwerty12345'),
        ]);
        $testing_user->markEmailAsVerified();
        $testing_user->save();

        $karte = Karte::create([
            'name' => 'Jūrmala',
            'viduspunkts_garums' => 56.946285,
            'viduspunkts_platums' => 24.105078,
            'zoom' => 12,
        ]);
        $karte->save();

        $spele1 = Spele::create([
            'karte_id' => $karte->id,
            'name' => 'Jūrmalas brīnišķīgā spēle',
            'description' => 'Jūrmalas spēle',
            'picture' => 'images/static/profile-pic-placeholder.png',
            'start_time' => Carbon::parse('2021-05-01 00:00:00'),
            'end_time' => Carbon::parse('2021-05-31 23:59:59'),
        ]);
        $spele1->save();

        $spele2 = Spele::create([
            'karte_id' => $karte->id,
            'name' => 'Jūrmalas brīnišķīgā spēle 2',
            'description' => 'Jūrmalas spēle 2',
            'picture' => 'images/static/profile-pic-placeholder.png',
            'start_time' => Carbon::parse('2021-05-01 00:00:00'),
            'end_time' => Carbon::parse('2021-05-31 23:59:59'),
        ]);
        $spele2->save();

        $spele3 = Spele::create([
            'karte_id' => $karte->id,
            'name' => 'Jūrmalas brīnišķīgā spēle 3',
            'description' => 'Jūrmalas spēle 3',
            'picture' => 'images/static/profile-pic-placeholder.png',
            'start_time' => Carbon::parse('2021-06-01 00:00:00'),
            'end_time' => Carbon::parse('2021-06-31 23:59:59'),
        ]);
        $spele3->save();

        $spele4 = Spele::create([
            'karte_id' => $karte->id,
            'name' => 'Jūrmalas brīnišķīgā spēle 4',
            'description' => 'Jūrmalas spēle 4',
            'picture' => 'images/static/profile-pic-placeholder.png',
            'start_time' => Carbon::parse('2024-06-01 00:00:00'),
            'end_time' => Carbon::parse('2024-06-31 23:59:59'),
        ]);
        $spele4->save();

        $spele5 = Spele::create([
            'karte_id' => $karte->id,
            'name' => 'Jūrmalas brīnišķīgā spēle 5',
            'description' => 'Jūrmalas spēle 5',
            'picture' => 'images/static/profile-pic-placeholder.png',
            'start_time' => Carbon::parse('2023-12-09 00:00:00'),
            'end_time' => Carbon::parse('2023-12-10 23:59:59'),
        ]);
        $spele5->save();

        $grupa1 = Grupa::create([
            'spele_id' => $spele1->id
        ]);
        $grupa1->save();

        $grupa2 = Grupa::create([
            'spele_id' => $spele2->id
        ]);
        $grupa2->save();

        $grupa3 = Grupa::create([
            'spele_id' => $spele3->id
        ]);
        $grupa3->save();

        $grupa4 = Grupa::create([
            'spele_id' => $spele4->id
        ]);
        $grupa4->save();

        $grupa5 = Grupa::create([
            'spele_id' => $spele5->id
        ]);
        $grupa5->save();

        $lietotajsgrupa1 = DB::table('lietotajsgrupa')->insert([
            'user_id' => $testing_user->id,
            'grupa_id' => $grupa1->id,
            'uzaicinats' => 0,
            'apstiprinats' => 1,
            'active' => 1,
        ]);
        $lietotajsgrupa2 = DB::table('lietotajsgrupa')->insert([
            'user_id' => $testing_user->id,
            'grupa_id' => $grupa2->id,
            'uzaicinats' => 0,
            'apstiprinats' => 1,
            'active' => 1,
        ]);
        $lietotajsgrupa3 = DB::table('lietotajsgrupa')->insert([
            'user_id' => $testing_user->id,
            'grupa_id' => $grupa3->id,
            'uzaicinats' => 0,
            'apstiprinats' => 1,
            'active' => 1,
        ]);
        $lietotajsgrupa4 = DB::table('lietotajsgrupa')->insert([
            'user_id' => $testing_user->id,
            'grupa_id' => $grupa4->id,
            'uzaicinats' => 0,
            'apstiprinats' => 1,
            'active' => 1,
        ]);
        $lietotajsgrupa5 = DB::table('lietotajsgrupa')->insert([
            'user_id' => $testing_user->id,
            'grupa_id' => $grupa5->id,
            'uzaicinats' => 0,
            'apstiprinats' => 1,
            'active' => 0,
        ]);

    }
}
