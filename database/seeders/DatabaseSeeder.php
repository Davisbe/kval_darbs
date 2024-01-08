<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Karte;
use App\Models\Spele;
use App\Models\Grupa;
use App\Models\Vieta;
use App\Models\Role;
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
            'profile_picture' => 'storage/images/static/profile-pic-placeholder.png',
        ]);
        $testing_user->markEmailAsVerified();
        $testing_user->save();

        $testing_user2 = User::create([
            'name' => 'testuser2',
            'email' => 'test2@test',
            'password' => Hash::make('qwerty12345'),
            'profile_picture' => 'storage/images/static/profile-pic-placeholder.png',
        ]);
        $testing_user2->markEmailAsVerified();
        $testing_user2->save();

        $karte = Karte::create([
            'name' => 'Jūrmala',
            'viduspunkts_garums' => 56.962198,
            'viduspunkts_platums' => 23.726692,
            'zoom' => 10,
        ]);
        $karte->save();

        $spele1 = Spele::create([
            'karte_id' => $karte->id,
            'name' => 'Jūrmalas brīnišķīgā spēle',
            'description' => 'Jūrmalas spēle',
            'picture' => 'storage/images/static/profile-pic-placeholder.png',
            'start_time' => Carbon::parse('2021-05-01 00:00:00'),
            'end_time' => Carbon::parse('2021-05-31 23:59:59'),
        ]);
        $spele1->save();

        $spele2 = Spele::create([
            'karte_id' => $karte->id,
            'name' => 'Jūrmalas brīnišķīgā spēle 2',
            'description' => 'Jūrmalas spēle 2',
            'picture' => 'storage/images/static/profile-pic-placeholder.png',
            'start_time' => Carbon::parse('2021-05-01 00:00:00'),
            'end_time' => Carbon::parse('2021-05-31 23:59:59'),
        ]);
        $spele2->save();

        $spele3 = Spele::create([
            'karte_id' => $karte->id,
            'name' => 'Jūrmalas brīnišķīgā spēle 3',
            'description' => 'Jūrmalas spēle 3',
            'picture' => 'storage/images/static/profile-pic-placeholder.png',
            'start_time' => Carbon::parse('2021-06-01 00:00:00'),
            'end_time' => Carbon::parse('2021-06-31 23:59:59'),
        ]);
        $spele3->save();

        $spele4 = Spele::create([
            'karte_id' => $karte->id,
            'name' => 'Jūrmalas brīnišķīgā spēle 4',
            'description' => 'Jūrmalas spēle 4',
            'picture' => 'storage/images/static/profile-pic-placeholder.png',
            'start_time' => Carbon::parse('2023-06-01 00:00:00'),
            'end_time' => Carbon::parse('2024-06-31 23:59:59'),
        ]);
        $spele4->save();

        $spele5 = Spele::create([
            'karte_id' => $karte->id,
            'name' => 'Jūrmalas brīnišķīgā spēle 5',
            'description' => 'Jūrmalas spēle 5',
            'picture' => 'storage/images/static/profile-pic-placeholder.png',
            'start_time' => Carbon::parse('2023-12-09 00:00:00'),
            'end_time' => Carbon::parse('2023-12-10 23:59:59'),
        ]);
        $spele5->save();

        $spele6 = Spele::create([
            'karte_id' => $karte->id,
            'name' => 'Jūrmalas brīnišķīgā spēle 6',
            'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
            'picture' => 'storage/images/static/profile-pic-placeholder.png',
            'start_time' => Carbon::parse('2023-12-21 00:00:00'),
            'end_time' => Carbon::parse('2023-12-21 23:59:59'),
        ]);
        $spele6->save();

        $spele7 = Spele::create([
            'karte_id' => $karte->id,
            'name' => 'Jūrmalas brīnišķīgā spēle 7',
            'description' => 'Šis ir labs labs labs labs labs labs labs labs labs labs labs labs labs labs labs labs labs labs labs labs .',
            'picture' => 'storage/images/static/profile-pic-placeholder.png',
            'start_time' => Carbon::parse('2023-12-21 00:00:00'),
            'end_time' => Carbon::parse('2023-12-21 23:59:59'),
        ]);
        $spele7->save();

        $spele8 = Spele::create([
            'karte_id' => $karte->id,
            'name' => 'Jūrmalas brīnišķīgā spēle 8',
            'description' => 'Šis ir labs labs labs labs labs labs labs labs labs labs labs labs labs labs labs labs labs labs labs labs .',
            'picture' => 'storage/images/static/profile-pic-placeholder.png',
            'start_time' => Carbon::parse('2023-12-18 00:00:00'),
            'end_time' => Carbon::parse('2023-12-19 02:00:00'),
        ]);
        $spele8->save();

        $spele9 = Spele::create([
            'karte_id' => $karte->id,
            'name' => 'Jūrmalas brīnišķīgā spēle 712',
            'description' => 'Šis ir labs labs labs labs labs labs labs labs labs labs labs labs labs labs labs labs labs labs labs labs .',
            'picture' => 'storage/images/static/profile-pic-placeholder.png',
            'start_time' => Carbon::parse('2023-12-01 00:00:00'),
            'end_time' => Carbon::parse('2023-12-30 02:00:00'),
        ]);
        $spele9->save();

        for ($i = 0; $i < 40; $i++) {
            $spele = Spele::create([
                'karte_id' => $karte->id,
                'name' => 'Jūrmalas brīnišķīgā spēle ' . $i,
                'description' => 'Šis ir labs labs labs labs labs labs labs',
                'picture' => 'storage/images/static/profile-pic-placeholder.png',
                'start_time' => Carbon::parse('2024-12-18 00:00:00'),
                'end_time' => Carbon::parse('2024-12-19 02:00:00'),
            ]);
            $spele->save();
        }

        

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
            'active' => -1,
        ]);
        $lietotajsgrupa2 = DB::table('lietotajsgrupa')->insert([
            'user_id' => $testing_user->id,
            'grupa_id' => $grupa2->id,
            'uzaicinats' => 0,
            'apstiprinats' => 1,
            'active' => -1,
        ]);
        $lietotajsgrupa3 = DB::table('lietotajsgrupa')->insert([
            'user_id' => $testing_user->id,
            'grupa_id' => $grupa3->id,
            'uzaicinats' => 0,
            'apstiprinats' => 1,
            'active' => -1,
        ]);
        $lietotajsgrupa4 = DB::table('lietotajsgrupa')->insert([
            'user_id' => $testing_user->id,
            'grupa_id' => $grupa4->id,
            'uzaicinats' => 0,
            'apstiprinats' => 1,
            'active' => -1,
        ]);
        $lietotajs2grupa4 = DB::table('lietotajsgrupa')->insert([
            'user_id' => $testing_user2->id,
            'grupa_id' => $grupa4->id,
            'uzaicinats' => 1,
            'apstiprinats' => -1,
            'active' => -1,
        ]);
        $lietotajsgrupa5 = DB::table('lietotajsgrupa')->insert([
            'user_id' => $testing_user->id,
            'grupa_id' => $grupa5->id,
            'uzaicinats' => 0,
            'apstiprinats' => 1,
            'active' => -1,
        ]);

        $vieta1 = Vieta::create([
            'name' => 'Vieta1',
            'garums' => 23.620509,
            'platums' => 56.941024,
            'pielaujama_kluda' => 20,
            'sarezgitiba' => 1,
            'picture' => 'storage/images/static/profile-pic-placeholder.png',
        ]);

        $vieta1 = Vieta::create([
            'name' => 'Mana vieta1',
            'garums' => 23.600982,
            'platums' => 56.960508,
            'pielaujama_kluda' => 50,
            'sarezgitiba' => 2,
            'picture' => 'storage/images/static/profile-pic-placeholder.png',
        ]);

        $vieta1 = Vieta::create([
            'name' => 'Market vieta1',
            'garums' => 23.603053,
            'platums' => 56.959224,
            'pielaujama_kluda' => 50,
            'sarezgitiba' => 3,
            'picture' => 'storage/images/static/profile-pic-placeholder.png',
        ]);

        $spele4->vieta()->attach([1, 2, 3]);

        $spele4->karte()->associate($karte);

        $admin_role = Role::create ([
            'role' => 'admin',
        ]);
        $admin_role->save();

        $admin_user = User::create([
            'name' => 'adminuser',
            'email' => 'admin@test',
            'password' => Hash::make('qwerty12345'),
            'profile_picture' => 'storage/images/static/profile-pic-placeholder.png',
        ]);
        $admin_user->markEmailAsVerified();
        $admin_user->save();

        $admin_user->role()->attach($admin_role->id);

    }
}
