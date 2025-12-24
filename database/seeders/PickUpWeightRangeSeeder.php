<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PickUpWeightRangeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
		$min_weight = 50;
        for ($i = 0; $i <= 20; $i++) {
			$max_weight = $min_weight + 10;
			\App\Models\PickUpWeightRange::firstOrCreate([
				'min_weight' => $min_weight,
				'max_weight' => $max_weight,
				'unit' => 'Kg'
			]);

			$min_weight = $max_weight;
        }
    }
}
