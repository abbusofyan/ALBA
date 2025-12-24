<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Http\Requests\SubmitRecyclingFormRequest;
use App\Models\Recycling;
use App\Models\Bin;
use App\Models\User;
use App\Models\EventRecyclingLog;
use App\Models\Setting;
use App\Helpers\ApiResponse;
use Carbon\Carbon;
use DB;

class RecyclingController extends Controller
{

	/**
     * @OA\get(
     *     security={{"bearerAuth":{}}},
     *     path="/api/v1/recyclings/getAll",
     *     tags={"Recycling"},
     *     description="Get all recycling history",
     *     summary="Get recyclings",
     *     @OA\Response(
     *          response=200,
     *          description="Success"
     *      )
     * )
     */
	public function getAll(Request $request)
	{
	    try {
			$recyclings = Recycling::with('bin', 'user')->get()->toArray();
			return ApiResponse::success(['recyclings' => $recyclings], 'Recycling data retrieved successfully');
	    } catch (\Exception $e) {
			return ApiResponse::error($e->getMessage(), 500);
	    }
	}

	/**
	 * @OA\Post(
	 *     path="/api/v1/recyclings/submit",
	 *     security={{"bearerAuth":{}}},
	 *     tags={"Recycling"},
	 *     summary="Submit recycling data",
	 *     description="Submit recycling data",
	 *     @OA\RequestBody(
	 *         required=true,
	 *         @OA\MediaType(
	 *             mediaType="multipart/form-data",
	 *             @OA\Schema(
	 *                 required={"bin_id", "photo"},
	 *                 @OA\Property(property="bin_id", type="integer", example=5),
	 *                 @OA\Property(
	 *                     property="photo",
	 *                     type="file",
	 *                     format="binary"
	 *                 )
	 *             )
	 *         )
	 *     ),
	 *     @OA\Response(response=200, description="Success"),
	 *     @OA\Response(response=422, description="Validation error")
	 * )
	 */


	 public function submit(SubmitRecyclingFormRequest $request)
	 {
	     DB::beginTransaction();

	     try {
	         $user = auth()->user();
	         $bin = Bin::with('type')->findOrFail($request->bin_id);
	         $event = $this->getRelevantEvent($bin);

	         $reward = $this->calculateReward($user, $bin, $event);
	         $photoFilename = $this->handlePhotoUpload($request);

	         $recycling = Recycling::create([
	             'user_id' => $user->id,
	             'bin_id' => $bin->id,
	             'bin_type_id' => $bin->bin_type_id,
	             'reward' => $reward,
	             'photo' => $photoFilename,
	         ]);

	         $user->increment('point', $reward);

	         if ($this->shouldAddEventRecyclingLog($user, $event, $bin)) {
	             EventRecyclingLog::create([
	                 'event_id' => $event->id,
	                 'user_id' => $user->id,
	                 'recycling_id' => $recycling->id,
	             ]);
	         }

	         DB::commit();

	         return ApiResponse::success([
	             'additional_point' => $reward,
	             'total_point' => $user->point,
	             'bin_type' => $bin->type->name,
	         ], 'Recycling submitted successfully');

	     } catch (\Throwable $e) {
	         DB::rollBack();
	         return ApiResponse::error($e->getMessage(), 500);
	     }
	 }

	 /**
	  * Get the most relevant event for the bin.
	  */
	 private function getRelevantEvent($bin)
	 {
	     return $bin->ongoingEvents->first() ?? $bin->type->ongoingEvents()->first();
	 }

	 /**
	  * Handle photo upload if present.
	  */
	 private function handlePhotoUpload($request)
	 {
	     if (!$request->hasFile('photo')) {
	         return null;
	     }

	     $image = $request->file('photo');
	     $filename = time() . '_' . $image->getClientOriginalName();
	     $image->storeAs('public/photos/recycling/', $filename);

	     return $filename;
	 }

	 /**
	  * Calculate how many reward points should be given.
	  */
	 private function calculateReward($user, $bin, $event)
	 {
	     $defaultReward = $bin->type->point;
	     $reward = $defaultReward;

	     if ($event) {
	         if ($event->needs_join && $user->hasJoinedThisEvent($event->id)) {
	             $reward = $bin->point;
	         } elseif (!$event->needs_join) {
	             $reward = $bin->point;
	         }
	     }

	     $maxDaily = Setting::first()->user_max_daily_reward;
	     $earnedToday = $user->todayRecycling()->sum('reward');
	     $remaining = $maxDaily - $earnedToday;

	     return max(0, min($reward, $remaining));
	 }

	 /**
	  * Determine if we should add a recycling log for the event.
	  */
	 private function shouldAddEventRecyclingLog($user, $event, $bin)
	 {
	     if (!$event) {
	         return false;
	     }

	     if (!$event->needs_join) {
	         return true;
	     }

	     return $user->hasJoinedThisEvent($event->id);
	 }

}
