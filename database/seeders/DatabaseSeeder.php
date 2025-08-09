<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory(10)->create();
        User::factory()->create([ #Admin jogkörök teszteléséhez
            'name' => 'TestAdmin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('123'),
            'admin' => true,
        ]);
        User::factory()->create([
            'name' => 'TestUser',
            'email' => 'test@test.com',
            'password' => Hash::make('123'),
            'admin' => false,
        ]);
        $this->call([
            EnclosureSeeder::class,         #Enclosure seederben seedeljük az Animalokat is
        ]);

    }
}
