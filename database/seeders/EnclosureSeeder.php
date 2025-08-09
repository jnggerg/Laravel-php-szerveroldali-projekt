<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Faker\Factory;
use App\Models\User;
use App\Models\Enclosure;
use App\Models\Animal;

class EnclosureSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Factory::create();
        $users = User::all();

        for ($i = 0; $i < 10; $i++) {
            $limit = $faker->numberBetween(1,7);
            $enclosure = Enclosure::create([
                'name' => $faker->unique()->word(),
                'limit' => $limit,
                'feeding_at' => $faker->time('H:i'),
            ]);
            $has_predator = $faker->boolean(30);  #30% esély hogy tartalmaz ragadozo állatot
            $enclosure->users()->attach($faker->randomElements($users->pluck('id')->toArray(), rand(1,5))); # gondozó és enclosure relációk össekötése

            $limit = $limit - rand(0, $limit-2); #random mennyiségű állat, ne legyen minding tele
            for($j = 0; $j < $limit; $j++) {            # Minden enclosurebe $limit számu animal seedelése
                $born = $faker->dateTimeBetween("-20 years", "now");
                Animal::create([
                    'name' => $faker->name(),
                    'species' => $faker->word(),
                    'is_predator' => $has_predator,       #ha tartalmaz ragadozot ez a kifuto, akkor legyen minden állat ragadozo
                    'born_at' => $born,
                    'deleted_at' => $faker->optional(0.3)->dateTimeBetween($born, 'now'),   #30% esély hogy archivált (soft deleted) az állat
                    'kep' => null,                              #csak a manuálisan hozzáadottaknak kép, a seedelt mindig placeholder
                    'enclosure_id' => $enclosure->id            #hozzákötjük az állatot az enclosurehez
                ]);

            }
        }
    }
}
