<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PickUpWasteType;

class PickUpWasteTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $wasteTypes = [
			[
				'name' => 'General Paper',
				'description' => 'Newspapers, magazines, office papers.'
			],
			[
				'name' => 'Confidential Paper',
				'description' => 'Dcocuments requiring secure disposal'
			],
			[
				'name' => 'E_Waste',
				'description' => 'Electronic devices and equipment'
			]
		];

		foreach ($wasteTypes as $wasteType) {
			PickUpWasteType::firstOrCreate($wasteType);
		}
    }
}
