<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\SubmitPickUpOrderRequest;
use App\Models\PickUpWeightRange;
use App\Models\PickUpTimeSlot;
use App\Models\PickUpOrder;
use App\Models\PickUpWasteType;
use App\Helpers\ApiResponse;
use App\Mail\PickupOrderMail;
use Illuminate\Support\Facades\Mail;

class PickUpOrderController extends Controller
{

	/**
	 * @OA\Post(
	 *     path="/api/v1/pick-up-orders/submit",
	 *     security={{"bearerAuth":{}}},
	 *     tags={"Pick Up Order"},
	 *     summary="Submit a new pickup order",
	 *     description="Create and submit a pickup order with waste type, weight range, time slot, and address.",
	 *
	 *     @OA\RequestBody(
	 *         required=true,
	 *         @OA\MediaType(
	 *             mediaType="multipart/form-data",
	 *             @OA\Schema(
	 *                 @OA\Property(
	 *                     property="waste_type_id",
	 *                     type="integer",
	 *                     example=1
	 *                 ),
	 *                 @OA\Property(
	 *                     property="weight_range_id",
	 *                     type="integer",
	 *                     example=3
	 *                 ),
	 *                 @OA\Property(
	 *                     property="time_slot_id",
	 *                     type="integer",
	 *                     example=2
	 *                 ),
	 *                 @OA\Property(
	 *                     property="quantity",
	 *                     type="integer",
	 *                     example=5
	 *                 ),
	 *                 @OA\Property(
	 *                     property="e_waste_description",
	 *                     type="string",
	 *                     example="Old laptop and monitor"
	 *                 ),
	 *                 @OA\Property(
	 *                     property="remark",
	 *                     type="string",
	 *                     example="Handle carefully"
	 *                 ),
	 *                 @OA\Property(
	 *                     property="pickup_date",
	 *                     type="string",
	 *                     format="date",
	 *                     example="2025-11-27"
	 *                 ),
	 *                 @OA\Property(
	 *                     property="address",
	 *                     type="string",
	 *                     example="Ang Mo Kio, Singapore"
	 *                 ),
	 *
	 *                 @OA\Property(
	 *                     property="photo",
	 *                     type="string",
	 *                     format="binary",
	 *                     description="Optional photo upload"
	 *                 )
	 *             )
	 *         )
	 *     ),
	 *
	 *     @OA\Response(
	 *         response=200,
	 *         description="Pick up order submitted successfully",
	 *         @OA\JsonContent(
	 *             @OA\Property(property="message", type="string", example="Pick up order submitted successfully"),
	 *             @OA\Property(property="data", type="object")
	 *         )
	 *     ),
	 *     @OA\Response(
	 *         response=400,
	 *         description="Validation error"
	 *     ),
	 *     @OA\Response(
	 *         response=500,
	 *         description="Internal server error"
	 *     )
	 * )
	 */

	public function submit(SubmitPickUpOrderRequest $request)
	{
		try {
			$user = resolve('user');

			$filename = null;

			if ($request->hasFile('photo')) {
				$imageFile = $request->file('photo');
				$filename = time() . '_' . $imageFile->getClientOriginalName();
				$path = $imageFile->storeAs('public/images/pickup_orders', $filename);
			}

			$weightRange = null;
			if (in_array($request->waste_type_id, [PickUpWasteType::GENERAL_PAPER, PickUpWasteType::CONFIDENTIAL_PAPER])) {
				$weightRange = PickUpWeightRange::find($request->weight_range_id);
			}

			$timeSlot = PickUpTimeSlot::find($request->time_slot_id);
			$pickUpOrder = PickUpOrder::create([
				'user_id' 			  => $user->id,
				'waste_type_id'       => $request->waste_type_id,
				'min_weight'          => $weightRange ? $weightRange->min_weight : null,
				'max_weight'          => $weightRange ? $weightRange->max_weight : null,
				'quantity'            => $request->quantity,
				'e_waste_description' => $request->e_waste_description,
				'photo'               => $filename,
				'remark'              => $request->remark,
				'pickup_date'         => $request->pickup_date,
				'pickup_start_time'   => $timeSlot->start_time,
				'pickup_end_time'     => $timeSlot->end_time,
				'address'             => $request->address
			]);

			Mail::to(config('mail.pickup_order.admin'))->cc(config('mail.pickup_order.cc'))->send(new PickupOrderMail($user, $pickUpOrder));

			return ApiResponse::success($pickUpOrder, 'Pick up order submitted successfully');

		} catch (\Exception $e) {
			\Log::error(['Error when submitting pick up order :' => $e->getMessage()]);
			return ApiResponse::error('Error when submitting pick up order');
		}
	}



	/**
	 * @OA\Get(
	 *     path="/api/v1/pick-up-orders/get-weight-range",
	 *     security={{"bearerAuth":{}}},
	 *     tags={"Pick Up Order"},
	 *     summary="Get weight range list",
	 *     description="Retrieve all available weight ranges for pickup orders.",
	 *
	 *     @OA\Response(
	 *         response=200,
	 *         description="Weight range fetched successfully",
	 *         @OA\JsonContent(
	 *             @OA\Property(property="message", type="string", example="Weight range fetched successfully"),
	 *             @OA\Property(property="data", type="array", @OA\Items(
	 *                  @OA\Property(property="id", type="integer", example=1),
	 *                  @OA\Property(property="min_weight", type="integer", example=1),
	 *                  @OA\Property(property="max_weight", type="integer", example=5)
	 *             ))
	 *         )
	 *     ),
	 *     @OA\Response(
	 *         response=404,
	 *         description="No weight range found"
	 *     )
	 * )
	 */
	public function getWeightRange() {
		$weightRanges = PickUpWeightRange::all();
		if (!$weightRanges) {
			return ApiResponse::error('No weight range found');
		}
		return ApiResponse::success($weightRanges, 'Weight range fetched successfully');
	}

	/**
	 * @OA\Get(
	 *     path="/api/v1/pick-up-orders/get-time-slot",
	 *     security={{"bearerAuth":{}}},
	 *     tags={"Pick Up Order"},
	 *     summary="Get time slot list",
	 *     description="Retrieve all available pickup time slots.",
	 *
	 *     @OA\Response(
	 *         response=200,
	 *         description="Time slots fetched successfully",
	 *         @OA\JsonContent(
	 *             @OA\Property(property="message", type="string", example="Time slots fetched successfully"),
	 *             @OA\Property(property="data", type="array", @OA\Items(
	 *                  @OA\Property(property="id", type="integer", example=1),
	 *                  @OA\Property(property="start_time", type="string", example="09:00"),
	 *                  @OA\Property(property="end_time", type="string", example="11:00")
	 *             ))
	 *         )
	 *     ),
	 *     @OA\Response(
	 *         response=404,
	 *         description="No time slots found"
	 *     )
	 * )
	 */
	public function getTimeSlot() {
		$timeSlots = PickUpTimeSlot::all();
		if (!$timeSlots) {
			return ApiResponse::error('No time slots found');
		}
		return ApiResponse::success($timeSlots, 'Time slots fetched successfully');
	}

	/**
	 * @OA\Get(
	 *     path="/api/v1/pick-up-orders/get-waste-category",
	 *     security={{"bearerAuth":{}}},
	 *     tags={"Pick Up Order"},
	 *     summary="Get waste categories",
	 *     description="Retrieve all available waste categories.",
	 *
	 *     @OA\Response(
	 *         response=200,
	 *         description="Success",
	 *         @OA\JsonContent(
	 *             type="object",
	 *             @OA\Property(property="success", type="boolean", example=true),
	 *             @OA\Property(property="message", type="string", example="Waste category fetched successfully"),
	 *             @OA\Property(
	 *                 property="data",
	 *                 type="array",
	 *                 @OA\Items(
	 *                     type="object",
	 *                     @OA\Property(property="id", type="integer", example=1),
	 *                     @OA\Property(property="name", type="string", example="Plastic")
	 *                 )
	 *             )
	 *         )
	 *     ),
	 *
	 *     @OA\Response(
	 *         response=404,
	 *         description="Not found"
	 *     )
	 * )
	 */
	public function getWasteCategory() {
		$wasteTypes = PickUpWasteType::all();
		if (!$wasteTypes) {
			return ApiResponse::error('No waste categort found');
		}
		return ApiResponse::success($wasteTypes, 'Waste category fetched successfully');
	}
}
