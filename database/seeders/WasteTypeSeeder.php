<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WasteTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $wasteTypes = [
			'Printer',
			'Computer & Laptop',
			'Mobile & Tablet',
			'TV & Desktop Monitor',
			'Network & Set-top Box',
			'Bulb & Battery',
			'Household Battery',
			'Bulb',
			'PP3 Battery',
			'AAA Battery',
			'1,5V Battery',
			'Empty Water Bottle',
			'Empty Alumunium Can',
			'Paper',
			'Metal',
			'Plastic',
			'Glass'
		];

		DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        DB::table('waste_types')->truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

		foreach ($wasteTypes as $wasteTypeName) {
			\App\Models\WasteType::firstOrCreate([
				'name' => $wasteTypeName
			]);
		}
    }
}
