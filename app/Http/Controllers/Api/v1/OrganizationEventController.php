<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use Carbon\Carbon;
use App\Helpers\ApiResponse;
use App\Services\EventService;

class OrganizationEventController extends Controller
{
	/**
	 * @OA\Get(
	 *     path="/api/v1/organization-events/upcoming",
	 *     security={{"bearerAuth":{}}},
	 *     tags={"Organization Event"},
	 *     summary="Get list of events with optional filters",
	 *     @OA\Parameter(
	 *         name="event_type_id",
	 *         in="query",
	 *         required=false,
	 *         description="Filter by event type ID",
	 *         @OA\Schema(type="integer")
	 *     ),
	 *     @OA\Parameter(
	 *         name="district_id",
	 *         in="query",
	 *         required=false,
	 *         description="Filter by district ID",
	 *         @OA\Schema(type="integer")
	 *     ),
	 *     @OA\Parameter(
	 *         name="lat",
	 *         in="query",
	 *         required=false,
	 *         description="User's latitude for proximity-based sorting",
	 *         @OA\Schema(type="number", format="float", example=-6.120481)
	 *     ),
	 *     @OA\Parameter(
	 *         name="long",
	 *         in="query",
	 *         required=false,
	 *         description="User's longitude for proximity-based sorting",
	 *         @OA\Schema(type="number", format="float", example=108.880863)
	 *     ),
	 *     @OA\Response(
	 *         response=200,
	 *         description="Success",
	 *         @OA\JsonContent(
	 *             @OA\Property(property="message", type="string"),
	 *             @OA\Property(property="data", type="object")
	 *         )
	 *     )
	 * )
	 */
    public function upcoming(Request $request) {
        // $user = resolve('user');
        // $today = Carbon::today();
		//
        // $upcomingEvents = Event::where('user_id', $user->id)
        //     ->whereDate('date_end', '>=', $today)
        //     ->orderBy('date_start', 'asc')
        //     ->get();

		$event = EventService::getAllPublicEvents($request);
        return ApiResponse::success(['events' => $event], 'Event(s) retrieved successfully');
    }

	/**
	 * @OA\Get(
	 *     path="/api/v1/organization-events/history",
	 *     tags={"Organization Event"},
	 *     security={{"bearerAuth":{}}},
	 *     @OA\Response(
	 *         response=200,
	 *         description="List of service options",
	 *         @OA\JsonContent(
	 *             type="object",
	 *             @OA\Property(property="status", type="boolean", example=true),
	 *             @OA\Property(property="message", type="string", example="service options fetched successfully"),
	 *         )
	 *     )
	 * )
	 */
    public function history() {
        $user = resolve('user');
        $today = Carbon::today();

        $pastEvents = Event::where('user_id', $user->id)
            ->whereDate('date_end', '<', $today)
            ->orderBy('date_start', 'desc')
            ->get();

        return ApiResponse::success(['events' => $pastEvents]);
    }


	/**
	 * @OA\Get(
	 *     path="/api/v1/organization-events/{eventId}",
	 *     tags={"Organization Event"},
	 *     security={{"bearerAuth":{}}},
	 *     @OA\Parameter(
	 *         name="eventId",
	 *         in="path",
	 *         required=true,
	 *         description="ID of the event",
	 *         @OA\Schema(type="integer", example=1)
	 *     ),
	 *     @OA\Response(
	 *         response=200,
	 *         description="List of service options",
	 *         @OA\JsonContent(
	 *             type="object",
	 *             @OA\Property(property="status", type="boolean", example=true),
	 *             @OA\Property(property="message", type="string", example="service options fetched successfully"),
	 *         )
	 *     )
	 * )
	 */
	public function detail($id)
	{
		try {
			$event = Event::select('id', 'code', 'name', 'description', 'lat', 'long', 'date_start', 'date_end', 'time_start', 'time_end', 'address', 'postal_code', 'event_type_id', 'district_id', 'image', 'use_all_bins')
				->with([
					'type',
					'district',
					'eventWasteType.wasteType',
				])
				->find($id);

			if (!$event) {
				return ApiResponse::error('Event not found', 404);
			}

			$user = resolve('user');
			$leaderboard = EventService::getLeaderboard($event, $user);

			return ApiResponse::success([
				'event' => $event,
				'leaderboard' => $leaderboard
			], 'Event detail retrieved successfully');
		} catch (\Exception $e) {
			return ApiResponse::error($e->getMessage(), 500);
		}
	}
}
