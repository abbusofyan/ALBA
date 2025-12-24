<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EWasteBinType;

class EWasteBinTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
		$binTypes = ['ICT, Battery, Bulb Bin (3 in 1 Bin)', 'Batteries & Bulbs Bin', 'Batteries Bin'];

		foreach ($binTypes as $binType) {
			$newBinType = EWasteBinType::firstOrCreate([
				'name' => $binType,
			], [
				'name' => $binType,
			]);
		}
    }
}
