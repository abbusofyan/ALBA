<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DistrictSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

		DB::statement('SET FOREIGN_KEY_CHECKS=0;');

		DB::table('districts')->truncate();

		DB::statement('SET FOREIGN_KEY_CHECKS=1;');

		$districts = [
			"Aljunied–Hougang",
		    "Ang Mo Kio",
		    "Bishan–Toa Payoh",
		    "Chua Chu Kang",
		    "East Coast",
		    "Holland–Bukit Panjang",
		    "Jalan Besar",
		    "Jalan Kayu",
		    "Jurong–Clementi–Bukit Batok",
		    "Marine Parade–Braddell Heights",
		    "Marsiling–Yew Tee",
		    "Yishun",
		    "Pasir Ris–Changi",
		    "Punggol",
		    "Sembawang",
		    "Sengkang",
		    "Tampines",
		    "Tanjong Pagar",
		    "West Coast–Jurong West"
		];

		foreach ($districts as $key => $district) {
			\App\Models\District::create([
				'name' => $district
			]);
		}
    }
}
