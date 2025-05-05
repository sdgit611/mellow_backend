<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\developerPremiumPrice;

class DeveloperPremiumPriceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DeveloperPremiumPrice::create([
            'name' => 'one time',
            'price' => 20000,
            'status' => 1,
        ]);
        DeveloperPremiumPrice::create([
            'name' => 'monthly',
            'price' => 100,
            'status' => 1,
        ]);

        DeveloperPremiumPrice::create([
            'name' => 'quarterly',
            'price' => 500,
            'status' => 1,
        ]);

        DeveloperPremiumPrice::create([
            'name' => 'yearly',
            'price' => 800,
            'status' => 1,
        ]);
    }
}
