<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\BinType;
use Faker\Factory as Faker;

class BinTypeSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 */
	public function run(): void
	{
		$faker = Faker::create();

		$binTypes = ['RVM', 'Retailer E-bin', 'Recycling Bin', 'Quarterly E-waste Collection Drive', 'Permanent E-bin'];

		foreach ($binTypes as $binType) {
			$newBinType = BinType::firstOrCreate([
				'name' => $binType,
			], [
				'name' => $binType,
				'fixed_qrcode' => $faker->randomElement([0, 1]),
				'point' => $faker->numberBetween(10, 100)
			]);

			$wasteTypes = \App\Models\WasteType::all()->toArray();
			$wasteTypeIds = [];
			for ($j = 0; $j < $faker->randomElement([3, 4, 5, 6]); $j++) {
				$randomKey = $faker->numberBetween(0, count($wasteTypes) - 1);
				$randomWasteTypeId = $wasteTypes[$randomKey]['id'];
				if (!in_array($randomWasteTypeId, $wasteTypeIds)) {
					array_push($wasteTypeIds, $randomWasteTypeId);
				} else {
					$j--;
				}
			}
			$newBinType->wasteTypes()->attach($wasteTypeIds);
		}
	}
}
