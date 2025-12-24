<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PickUpTimeSlotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $times = [
			[
				'start_time' => '10:00',
				'end_time' => '12:00'
			],
			[
				'start_time' => '12:00',
				'end_time' => '14:00'
			],
			[
				'start_time' => '14:00',
				'end_time' => '16:00'
			],
			[
				'start_time' => '16:00',
				'end_time' => '18:00'
			],
		];

		foreach ($times as $time) {
			\App\Models\PickUpTimeSlot::firstOrCreate($time);
		}
    }
}
