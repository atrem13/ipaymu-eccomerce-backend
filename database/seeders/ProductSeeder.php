<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = \Faker\Factory::create();
        for ($i = 0; $i < 50; $i++){
            DB::table("products")->insert([
                "id" => $faker->uuid(),
                "name" => $faker->company(),
                "description" => $faker->sentence(),
                "purchase_price" => $faker->numberBetween(5000, 100000),
                "sell_price" => $faker->numberBetween(5000, 150000),
            ]);
        }
    }
}
