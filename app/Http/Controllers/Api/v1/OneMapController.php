<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\ApiResponse;
use App\Helpers\Helper;

class OneMapController extends Controller
{
	/**
 * @OA\Get(
 *     path="/api/v1/onemap/getLocationByPostalCode/{postal_code}",
 *     operationId="getLocationByPostalCode",
 *     tags={"OneMap"},
 *     summary="Get location detail from Singapore OneMap by postal code",
 *     description="Retrieves a location's details from the OneMap API using a 6-digit Singapore postal code.",
 *     @OA\Parameter(
 *         name="postal_code",
 *         in="path",
 *         required=true,
 *         description="Singapore 6-digit postal code",
 *         @OA\Schema(type="string", example="569933")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Location retrieved successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Location retrieved successfully"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(
 *                     property="location",
 *                     type="object",
 *                     description="Location result from OneMap",
 *                     @OA\Property(property="POSTAL", type="string", example="569933"),
 *                     @OA\Property(property="LATITUDE", type="string", example="1.375"),
 *                     @OA\Property(property="LONGITUDE", type="string", example="103.845"),
 *                     @OA\Property(property="BUILDING", type="string", example="ANG MO KIO HUB"),
 *                     @OA\Property(property="ADDRESS", type="string", example="53 Ang Mo Kio Avenue 3"),
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Postal code invalid",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Postal code invalid."),
 *         )
 *     ),
 *     @OA\Response(
 *         response=502,
 *         description="Failed to connect to OneMap API",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Failed to connect to OneMap API."),
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal Server Error",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="An unexpected error occurred."),
 *         )
 *     )
 * )
 */
	public function getLocationByPostalCode($postal_code)
	{
	    try {
	        $oneMapAPI = Helper::singaporeOneMapAPI($postal_code);

	        if (!$oneMapAPI->successful()) {
				throw new \Exception('Failed to connect to OneMap API.', 502);
	        }

	        $results = $oneMapAPI->json()['results'] ?? [];

	        if (empty($results)) {
				throw new \Exception('Postal code invalid.', 404);
	        }

			return ApiResponse::success([
				'location' => $results[0]
			], 'Location retrieved successfully');

	    } catch (\Exception $e) {
			return ApiResponse::error($e->getMessage(), 500);
	    }
	}
}
