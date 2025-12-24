<?php

namespace App\Services;

use App\Models\WasteType;
use App\Models\EventWasteType;
use App\Models\Event;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class EventService
{
	public static function syncEventWasteTypes($eventId, $request)
	{
		try {
			$updatedWasteTypeIds = [];

			foreach ($request as $wasteTypeData) {
				$wasteType = WasteType::firstOrCreate(['name' => $wasteTypeData['name']]);

				EventWasteType::updateOrCreate(
					[
						'event_id' => $eventId,
						'waste_type_id' => $wasteType->id
					],
					[
						'price' => $wasteTypeData['price']
					]
				);

				$updatedWasteTypeIds[] = $wasteType->id;
			}

			EventWasteType::where('event_id', $eventId)
				->whereNotIn('waste_type_id', $updatedWasteTypeIds)
				->delete();

			return true;
		} catch (\Exception $e) {
			Log::error('Failed to save event waste types: ' . $e->getMessage());
			return false;
		}
	}

	public static function getLeaderboard(Event $event, User $user) {
		$leaderboardData = DB::table('event_recycling_logs')
			->join('users', 'event_recycling_logs.user_id', '=', 'users.id')
			->join('recyclings', 'event_recycling_logs.recycling_id', '=', 'recyclings.id')
			->where('event_recycling_logs.event_id', $event->id)
			->select(
				'users.id',
				'users.name',
				'users.username',
				'users.first_name',
				'users.last_name',
				'users.display_name',
				DB::raw('SUM(recyclings.reward) as total_points')
			)
			->groupBy('users.id', 'users.name', 'users.username', 'users.first_name', 'users.last_name')
			->orderBy('total_points', 'desc')
			->get();

		$rankedUsers = $leaderboardData->map(function ($item, $key) {
			$item->rank = $key + 1;
			return $item;
		});

		$currentUserData = $rankedUsers->firstWhere('id', $user->id);

		$myRank = null;
		$myPoints = 0;
		if ($currentUserData) {
			$myRank = $currentUserData->rank;
			$myPoints = (float) $currentUserData->total_points;
		}

		$totalContributors = $rankedUsers->count();

		$getInitials = function ($firstName, $lastName, $fallbackName) {
			if (!empty($firstName) || !empty($lastName)) {
				$first = !empty($firstName) ? strtoupper(substr($firstName, 0, 1)) : '';
				$last = !empty($lastName) ? strtoupper(substr($lastName, 0, 1)) : '';
				return $first . $last;
			}

			$parts = explode(' ', trim($fallbackName));
			$initials = '';
			foreach ($parts as $part) {
				$initials .= strtoupper(substr($part, 0, 1));
			}
			return substr($initials, 0, 2);
		};


		$topContributors = $rankedUsers->take(20)->map(function ($contributor) use ($getInitials) {
			return [
				'rank' => $contributor->rank,
				'name' => $contributor->display_name,
				'initials' => $getInitials($contributor->first_name, $contributor->last_name, $contributor->name),
				'total_points' => (float) $contributor->total_points
			];
		});

		$myDataForList = null;
		if ($currentUserData) {
			$myDataForList = [
				'name' => $currentUserData->username . ' (You)',
				'initials' => $getInitials($currentUserData->first_name, $currentUserData->last_name, $currentUserData->name),
				'total_points' => (float) $currentUserData->total_points
			];
		}

		$response = [
			'total_points' => $rankedUsers->sum('total_points'),
			'my_impact' => [
				'total_points' => $myPoints,
			],
			'leaderboard_position' => [
				'my_rank' => $myRank,
				'total_contributors' => $totalContributors,
			],
			'top_contributors' => $topContributors,
			'my_rank_data' => $myDataForList
		];

		return $response;
	}

	public static function getAllPublicEvents($request) {
		$userLat = $request->lat;
		$userLng = $request->long;

		$events = Event::where('status', Event::STATUS_ACTIVE)->select(
		   'events.id', 'events.code', 'events.name', 'events.description',
		   'events.date_start', 'events.date_end', 'events.district_id',
		   'events.time_start', 'events.time_end', 'events.address', 'events.postal_code',
		   'events.lat', 'events.long', 'events.image',
		   'districts.name as district_name',
		   DB::raw("(
			   6371 * acos(
				   cos(radians(?)) *
				   cos(radians(events.lat)) *
				   cos(radians(events.long) - radians(?)) +
				   sin(radians(?)) *
				   sin(radians(events.lat))
			   )
		   ) AS distance")
	   )
	   ->leftJoin('districts', 'events.district_id', '=', 'districts.id')

	   // Filter by event_type_id if present
	   ->when($request->has('event_type_id'), function ($query) use ($request) {
		   $query->where('event_type_id', $request->event_type_id);
	   })

	   // Filter by district_id if present
	   ->when($request->has('district_id'), function ($query) use ($request) {
		   $query->where('district_id', $request->district_id);
	   })

	   // Exclude past events
	   ->where('events.date_end', '>=', now()->toDateString())

	   // Custom ordering logic based on event_type_id
	   ->when($request->event_type_id == 4, function ($query) {
		   $query->orderBy('events.created_at', 'desc');
	   })
	   ->when($request->event_type_id == 1, function ($query) {
		   $query->orderBy('events.date_start', 'asc');
	   })
	   ->when(($request->event_type_id != 4 && $request->event_type_id != 1) && ($userLat && $userLng), function ($query) use ($userLat, $userLng) {
		   $query->orderByRaw("
			   6371 * acos(
				   cos(radians(?)) *
				   cos(radians(lat)) *
				   cos(radians(`long`) - radians(?)) +
				   sin(radians(?)) *
				   sin(radians(lat))
			   )
		   ASC", [$userLat, $userLng, $userLat]);
	   })

	   // Bind lat/lng for the SELECT distance calculation
	   ->addBinding([$userLat, $userLng, $userLat], 'select')

	   ->get()
	   ->map(function ($event) {
		   $event->is_ongoing = now()->between($event->date_start, $event->date_end);
		   return $event;
	   });
	   return $events;
	}
}
