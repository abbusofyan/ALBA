<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EventType;
use App\Helpers\ApiResponse;
use App\Services\BinService;
use Illuminate\Support\Facades\DB;

class EventTypeController extends Controller
{
	/**
	 * @OA\Get(
	 *     security={{"bearerAuth":{}}},
	 *     path="/api/v1/event-types",
	 *     tags={"Event Type"},
	 *     summary="Get list of event type",
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
	public function get()
	{
	    try {
			$eventTypes = EventType::all();
			return ApiResponse::success([
				'event_types' => $eventTypes,
				'total' => count($eventTypes)
			], 'Event Type retrieved successfully');
	    } catch (\Exception $e) {
			return ApiResponse::error($e->getMessage(), 500);
	    }
	}

}
