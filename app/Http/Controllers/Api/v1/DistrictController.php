<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\District;
use App\Helpers\ApiResponse;
use App\Services\BinService;
use Illuminate\Support\Facades\DB;

class DistrictController extends Controller
{
	/**
	 * @OA\Get(
	 *     path="/api/v1/districts",
	 *     security={{"bearerAuth":{}}},
	 *     tags={"District"},
	 *     summary="Get list of districts (optional search by name)",
	 *     @OA\Parameter(
	 *         name="name",
	 *         in="query",
	 *         required=false,
	 *         description="Search district by name",
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
	 public function get(Request $request)
	 {
	     try {
	         $districts = District::query()
	             ->when($request->filled('name'), function ($query) use ($request) {
	                 $query->where('name', 'like', '%' . $request->name . '%');
	             })
	             ->get();

	         return ApiResponse::success([
	             'districts' => $districts,
	             'total' => count($districts)
	         ], 'District retrieved successfully');
	     } catch (\Exception $e) {
	         return ApiResponse::error($e->getMessage(), 500);
	     }
	 }

}
