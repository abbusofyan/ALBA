<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\UserJoinedEvent;
use App\Helpers\ApiResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Services\EventService;

class EventController extends Controller
{
	/**
	 * @OA\Get(
	 *     path="/api/v1/events",
	 *     security={{"bearerAuth":{}}},
	 *     tags={"Event"},
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

	 public function get(Request $request)
	 {
	     try {
	         $events = EventService::getAllPublicEvents($request);
	         return ApiResponse::success([
	             'events' => $events,
	             'total' => count($events)
	         ], 'Event(s) retrieved successfully');

	     } catch (\Exception $e) {
	         return ApiResponse::error($e->getMessage(), 500);
	     }
	 }


	/**
	 * @OA\Get(
	 *     path="/api/v1/events/{id}",
	 *     security={{"bearerAuth":{}}},
	 *     tags={"Event"},
	 *     summary="Get event detail by ID",
	 *     @OA\Parameter(
	 *         name="id",
	 *         in="path",
	 *         required=true,
	 *         description="ID of the event to retrieve",
	 *         @OA\Schema(type="integer")
	 *     ),
	 *     @OA\Response(
	 *         response=200,
	 *         description="Success",
	 *         @OA\JsonContent(
	 *             @OA\Property(property="message", type="string"),
	 *             @OA\Property(property="data", type="object")
	 *         )
	 *     ),
	 *     @OA\Response(
	 *         response=404,
	 *         description="Event not found"
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
					'eventBins.bin.type' => function ($query) {
						$query->select('id', 'name', 'image', 'icon');
					}
				])
				->find($id);

			if (!$event) {
				return ApiResponse::error('Event not found', 404);
			}

			return ApiResponse::success($event, 'Event detail retrieved successfully');
		} catch (\Exception $e) {
			return ApiResponse::error($e->getMessage(), 500);
		}
	}

	/**
	 * @OA\Post(
	 *     path="/api/v1/events/join",
	 *     security={{"bearerAuth":{}}},
	 *     tags={"Event"},
	 *     summary="Join private event with event code",
	 *     @OA\RequestBody(
	 *         required=true,
	 *         @OA\MediaType(
	 *             mediaType="application/json",
	 *             @OA\Schema(
	 *                 @OA\Property(
	 *                      type="object",
	 *                      @OA\Property(
	 *                          property="code",
	 *                          type="string",
	 *                          description="required for private event"
	 *                      ),
	 *                      @OA\Property(
	 *                          property="event_id",
	 *                          type="string",
	 *                          description="required for ALBA event"
	 *                      )
	 *                 ),
	 *                 example={
	 *                     "code":"EventAlba2025",
	 *                     "event_id":"1",
	 *                }
	 *             )
	 *         )
	 *      ),
	 *     @OA\RequestBody(
	 *         required=true,
	 *         @OA\MediaType(
	 *             mediaType="application/json",
	 *             @OA\Schema(
	 *                 @OA\Property(
	 *                      type="object",
	 *                      @OA\Property(
	 *                          property="Password",
	 *                          type="string"
	 *                      )
	 *                 ),
	 *                 example={
	 *                     "password":"password123"
	 *                }
	 *             )
	 *         )
	 *      ),
	 *      @OA\Response(
	 *          response=200,
	 *          description="Successful operation"
	 *       )
	 * )
	 */
	public function join(Request $request)
	{
		$validator = Validator::make($request->all(), [
		    'code' => 'required_without:event_id',
		    'event_id' => 'required_without:code',
		]);

		if ($validator->fails()) {
			return ApiResponse::validation($validator->errors());
		}

		try {

			if ($request->code) {
				$event = Event::whereRaw('BINARY secret_code = ?', [$request->code])->first();
				if (!$event) {
					throw new \Exception('Private Event code not found.', 400);
				}
			}

			if ($request->event_id) {
				$event = Event::where('id', $request->event_id)
			              ->where('event_type_id', 4)
			              ->first();
				if (!$event) {
					throw new \Exception('ALBA Event not found.', 400);
				}
			}

			$user = resolve('user');

			$hasJoinedEvent = UserJoinedEvent::where('user_id', $user->id)->where('event_id', $event->id)->exists();
			if ($hasJoinedEvent) {
				return ApiResponse::success($event, 'Joined successfully!');
			}

			$ongoingEvent = UserJoinedEvent::where('user_id', $user->id)
				->whereHas('event', function ($query) {
					$query->whereDate('date_end', '>=', now()->toDateString());
				})
				->first();

			if ($ongoingEvent) {
				throw new \Exception('You can only join one event at a time. Please finish your current event before joining another.', 400);
			}

			$joinedEvent = UserJoinedEvent::firstOrCreate([
				'user_id' => $user->id,
				'event_id' => $event->id,
			]);

			if (!$joinedEvent->wasRecentlyCreated) {
				throw new \Exception('You have already joined this event.', 400);
			}

			return ApiResponse::success($event, 'Joined successfully!');
		} catch (\Exception $e) {
			return ApiResponse::error($e->getMessage(), $e->getCode());
		}
	}

	/**
	 * @OA\Get(
	 *     path="/api/v1/events/{id}/leaderboard",
	 *     security={{"bearerAuth":{}}},
	 *     tags={"Event"},
	 *     summary="Get event leaderboard",
	 *     @OA\Parameter(
	 *         name="id",
	 *         in="path",
	 *         required=true,
	 *         description="ID of the event",
	 *         @OA\Schema(type="integer")
	 *     ),
	 *     @OA\Response(
	 *         response=200,
	 *         description="Success",
	 *         @OA\JsonContent(
	 *             @OA\Property(property="message", type="string"),
	 *             @OA\Property(property="data", type="object")
	 *         )
	 *     ),
	 *     @OA\Response(
	 *         response=404,
	 *         description="Event not found"
	 *     )
	 * )
	 */
	public function leaderboard(Request $request, $id)
	{
		try {
			$user = resolve('user');

			$event = Event::find($id);
			if (!$event) {
				return ApiResponse::error('Event not found', 404);
			}

			$response = EventService::getLeaderboard($event, $user);

			return ApiResponse::success($response, 'Leaderboard data retrieved successfully');
		} catch (\Exception $e) {
			return ApiResponse::error($e->getMessage(), $e->getCode() ?: 500);
		}
	}
}
