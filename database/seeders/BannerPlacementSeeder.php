<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BannerPlacementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $placements = [
			'Homepage',
			'Pop Up Homepage',
			'Search Page',
			'Success Recycle',
			'Reward',
			'Account'
		];

		$dimensions = [
			[
				'width' => 690,
				'height' => 170
			],
			[
				'width' => 510,
				'height' => 600
			],
			[
				'width' => 690,
				'height' => 170
			],
			[
				'width' => 690,
				'height' => 170
			],
			[
				'width' => 690,
				'height' => 170
			],
			[
				'width' => 690,
				'height' => 170
			]
		];

		DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        DB::table('banner_placements')->truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

		foreach ($placements as $key => $placement) {
			\App\Models\BannerPlacement::firstOrCreate([
				'name' => $placement,
				'width' => $dimensions[$key]['width'],
				'height' => $dimensions[$key]['height']
			]);
		}

		\App\Models\BannerPlacement::where('name', 'Search Page')->delete();
    }
}
