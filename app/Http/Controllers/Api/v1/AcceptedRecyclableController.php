<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WasteType;
use App\Helpers\ApiResponse;

class AcceptedRecyclableController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/v1/accepted-recyclables/getAll",
     *     security={{"bearerAuth":{}}},
     *     tags={"Accepted Recyclables"},
     *     summary="Get waste types",
     *     description="Retrieve waste types with optional keyword filtering.",
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         required=false,
     *         description="Search by keyword in waste type name or description",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Waste types retrieved successfully"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
    public function getAll(Request $request)
    {
        try {
            $keyword = $request->input('search');

            $query = WasteType::query();

            if ($keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            }

            $wasteTypes = $query->get();

            return ApiResponse::success(['waste_types' => $wasteTypes], 'Waste types retrieved successfully');
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 500);
        }
    }
}
