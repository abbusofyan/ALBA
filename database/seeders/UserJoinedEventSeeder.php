<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Event;
use App\Models\UserJoinedEvent;
use App\Models\Recycling;
use App\Models\EventRecyclingLog;
use App\Models\Bin;

class UserJoinedEventSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 */
	public function run(): void
	{
		// Get all users and events
		$users = User::whereHas('roles', function ($query) {
			$query->where('name', 'Public');
		})->get();
		$event = Event::where('event_type_id', 4)->first();
		$bins = Bin::all();

		if ($users->isEmpty() || !$event || $bins->isEmpty()) {
			$this->command->info('Not enough data in users, events, or bins table to seed UserJoinedEvents.');
			return;
		}

		foreach ($users as $user) {
			// Make each user join the event
			UserJoinedEvent::factory()->create([
				'user_id' => $user->id,
				'event_id' => $event->id,
			]);

			// Create between 1 to 5 recycling activities for each user in the event
			$recyclingCount = rand(1, 5);
			for ($i = 0; $i < $recyclingCount; $i++) {
				$reward = rand(1, 100);
				$recycling = Recycling::factory()->create([
					'user_id' => $user->id,
					'bin_id' => $bins->random()->id,
					'reward' => $reward
				]);

				$user->increment('point', $reward);

				EventRecyclingLog::factory()->create([
					'user_id' => $user->id,
					'event_id' => $event->id,
					'recycling_id' => $recycling->id,
				]);
			}
		}
	}
}
