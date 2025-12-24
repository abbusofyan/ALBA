<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Bin;
use App\Helpers\ApiResponse;
use App\Services\BinService;
use Illuminate\Support\Facades\DB;

class MapController extends Controller
{
	/**
	 * @OA\Get(
	 *     security={{"bearerAuth":{}}},
	 *     path="/api/v1/map/getNearbyBinLocations",
	 *     tags={"Map"},
	 *     summary="Get nearby bin locations by user's coordinate",
	 *     @OA\Parameter(
	 *         name="lat",
	 *         in="query",
	 *         required=true,
	 *         description="User's Latitude",
	 *         @OA\Schema(type="string")
	 *     ),
	 *     @OA\Parameter(
 	 *         name="lng",
 	 *         in="query",
 	 *         required=true,
 	 *         description="User's Longitude",
 	 *         @OA\Schema(type="string")
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
	public function getNearbyBinLocations(Request $request)
	{
	    try {
			$binLocations = BinService::getNerbyBinLocations($request->lat, $request->lng)->toArray();
			return ApiResponse::success([
				'locations' => $binLocations,
				'total' => count($binLocations)
			], 'Bin locations retrieved successfully');
	    } catch (\Exception $e) {
			return ApiResponse::error($e->getMessage(), 500);
	    }
	}

}
