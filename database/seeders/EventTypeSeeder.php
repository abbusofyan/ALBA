<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EventTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
		$eventTypes = ['E-Drive', 'Cash For Trash', 'Private Event', 'ALBA Event'];

		foreach ($eventTypes as $eventType) {
			\App\Models\EventType::firstOrCreate([
				'name' => $eventType,
			], [
				'name' => $eventType,
			]);
		}
    }
}
