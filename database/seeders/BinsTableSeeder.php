<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use App\Helpers\Helper;
use App\Models\Bin;

class BinsTableSeeder extends Seeder
{
	public function run(): void
	{
		$faker = Faker::create();
		$postalCodes = [
			"238858",
			"118553",
			"498800",
			"546080",
			"498794",
			"737736",
			"018926",
			"718919",
			"098322",
			"757752",
			"629122",
			"569830",
			"189970",
			"189969",
			"018956",
			"440085",
			"768442",
			"018906",
			"129817",
			"018951",
			"440082",
			"440081",
			"440083",
			"440087",
			"440086",
			"440080",
			"569933"
		];

		foreach ($postalCodes as $postalCode) {
			$oneMapAPI = Helper::singaporeOneMapAPI($postalCode);
			if ($oneMapAPI->successful()) {
				$results = $oneMapAPI->json()['results'] ?? [];
				foreach ($results as $location) {
					$code = Bin::generateCode();
					$bin = Bin::create([
						'code' => $code,
						'bin_type_id' => $faker->randomElement([1, 2, 3, 4]),
						'address' => $location['ADDRESS'],
						'postal_code' => $location['POSTAL'],
						'lat' => $location['LATITUDE'],
						'long' => $location['LONGITUDE'],
						'map_radius' => $faker->randomElement([10, 50, 100, 200]),
						'status' => $faker->randomElement([0, 1]),
						'created_at' => now(),
						'updated_at' => now(),
					]);
				}
			}
		}
	}
}
