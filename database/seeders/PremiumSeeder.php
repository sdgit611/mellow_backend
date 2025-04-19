<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Premium;

class PremiumSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Premium::create([
            'name' => 'priority customer support.',
        ]);
        Premium::create([
            'name' => '30 days money back guarantee.',
        ]);
        Premium::create([
            'name' => 'Smart Matching with Premium Jobs.',
        ]);
        Premium::create([
            'name' => 'Early Access to New Jobs.',
        ]);
        Premium::create([
            'name' => 'Higher Visibility to Recruiters.',
        ]);
        Premium::create([
            'name' => 'Application Processing.',
        ]);
        Premium::create([
            'name' => 'Priority Application Processing.',
        ]);
    }
}
