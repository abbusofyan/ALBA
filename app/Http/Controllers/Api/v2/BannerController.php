<?php

namespace App\Http\Controllers\Api\v2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BannerPlacement;
use App\Helpers\ApiResponse;
use Illuminate\Support\Facades\DB;

class BannerController extends Controller
{

	/**
	 * @OA\Get(
	 *     path="/api/v2/banners",
	 *     tags={"Banner V2"},
	 *     summary="Get banners",
	 *     @OA\Parameter(
	 *         name="banner_placement_id",
	 *         in="query",
	 *         description="Filter by Banner Placement ID",
	 *         required=false,
	 *         @OA\Schema(type="integer")
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
	        $bannerPlacementId = $request->query('banner_placement_id');

	        $query = BannerPlacement::with('banners');

	        if ($bannerPlacementId) {
	            $query->where('id', $bannerPlacementId);
	        }

	        $banners = $query->get();

	        return ApiResponse::success($banners, 'Banners retrieved successfully');
	    } catch (\Exception $e) {
	        return ApiResponse::error($e->getMessage(), 500);
	    }
	}


}
