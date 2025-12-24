<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Bin;
use App\Helpers\Helper;

class RVMSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rvmIds = [
			'20244006993',
			'20244006987',
			'20244006986',
			'20244006991',
			'20244006995',
			'20244006992',
			'20244006994',
			'20244006989',
			'20244006990',
			'20203311395',
			'20240505853',
			'20203311398',
			'20203311397',
			'20203311394',
			'20203311396',
			'20244006985',
			'070447',
			'070450'
		];
		$defaultPostalCode = '368001';

		foreach ($rvmIds as $code) {
			$binExists = Bin::where('code', $code)->exists();
			if (!$binExists) {
				$oneMapAPI = Helper::singaporeOneMapAPI($defaultPostalCode);
				if ($oneMapAPI->successful()) {
					$results = $oneMapAPI->json()['results'] ?? [];
					$location = $results[0];
					Bin::create([
						'code' => $code,
						'qrcode' => $code,
						'bin_type_id' => 1,
						'address' => $location['ADDRESS'],
						'postal_code' => $defaultPostalCode,
						'lat' => $location['LATITUDE'],
						'long' => $location['LONGITUDE'],
						'map_radius' => 200,
						'status' => 1,
						'created_at' => now(),
						'updated_at' => now(),
					]);
				}
			}
		}
    }
}
