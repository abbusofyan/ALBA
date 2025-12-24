<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Bin;
use App\Models\BinType;
use App\Models\Recycling;
use Carbon\Carbon;
use App\Helpers\ApiResponse;
use Illuminate\Support\Facades\DB;

class OrganizationDashboardController extends Controller
{
	/**
     * @OA\Get(
     *     path="/api/v1/organization-dashboard",
     *     summary="Get organization dashboard data",
     *     description="Fetch dashboard data including total reward, daily/monthly reward, total users, bin statistics, leaderboard, and recent recycling activity.",
     *     tags={"Organization Dashboard"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Dashboard data fetched successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Dashboard data fetched successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="total_reward", type="integer", example=1200),
     *                 @OA\Property(property="daily_reward", type="integer", example=50),
     *                 @OA\Property(property="monthly_reward", type="integer", example=300),
     *                 @OA\Property(property="total_users", type="integer", example=10),
     *                 @OA\Property(property="bins", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="leaderboard", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="recycling_activity", type="array", @OA\Items(type="object"))
     *             )
     *         )
     *     )
     * )
     */

	public function index()
	{
	    $user = resolve('user');

	    // Get bin IDs for this organization
	    $binIds = Bin::where('organization_id', $user->id)->pluck('id');

	    if ($binIds->isEmpty()) {
	        return response()->json([
	            'total_reward'   => 0,
	            'daily_reward'   => 0,
	            'monthly_reward' => 0,
	            'total_users'    => 0,
	            'bins'           => [],
	            'leaderboard'    => [],
	            'recycling_activity' => [],
	        ]);
	    }

	    // --- 1. GLOBAL TOTALS ---
	    $totals = Recycling::whereIn('bin_id', $binIds)
	        ->selectRaw('
	            COALESCE(SUM(reward), 0) as total_reward,
	            COALESCE(SUM(CASE WHEN DATE(created_at) = CURDATE() THEN reward ELSE 0 END), 0) as daily_reward,
	            COALESCE(SUM(CASE WHEN YEAR(created_at) = YEAR(CURDATE())
	                               AND MONTH(created_at) = MONTH(CURDATE())
	                               THEN reward ELSE 0 END), 0) as monthly_reward,
	            COUNT(DISTINCT user_id) as total_users
	        ')
	        ->first();

		$bins = BinType::select([
                'bin_types.id as bin_type_id',
                'bin_types.name',
                'bin_types.image',
				'bin_types.icon',
                DB::raw('COUNT(DISTINCT bins.id) as active_bins_count'),
                DB::raw('COALESCE(SUM(recyclings.reward), 0) as total_reward')
            ])
            ->leftJoin('bins', function($join) use ($user) {
                $join->on('bin_types.id', '=', 'bins.bin_type_id')
                     ->where('bins.organization_id', '=', $user->id);
            })
            ->leftJoin('recyclings', 'bins.id', '=', 'recyclings.bin_id')
            ->groupBy('bin_types.id', 'bin_types.name', 'bin_types.image')
            ->havingRaw('COUNT(DISTINCT bins.id) > 0')
            ->orderBy('bin_types.name')
			->take(4)
            ->get()
            ->map(function ($item) {
                return [
					'bin_type' => $item,
                    'total_active_bins' => (int) $item->active_bins_count,
                    'total_reward' => number_format((float) $item->total_reward, 2)
                ];
            });

	    // --- 3. LEADERBOARD (Top 5 contributors) ---
	    $leaderboard = Recycling::whereIn('bin_id', $binIds)
	        ->select(
	            'user_id',
	            DB::raw('SUM(reward) as total_reward')
	        )
	        ->groupBy('user_id')
	        ->orderByDesc('total_reward')
	        ->take(5)
	        ->with('user:id,name') // eager load user
	        ->get();

	    // --- 4. RECYCLING ACTIVITY (Latest 4 recyclings) ---
	    $recyclingActivity = Recycling::whereIn('bin_id', $binIds)
	        ->with(['user:id,name', 'bin:id,code,bin_type_id', 'bin.type:id,name'])
	        ->latest()
	        ->take(4)
	        ->get();

		return ApiResponse::success([
			'total_reward'       => (int) $totals->total_reward,
	        'daily_reward'       => (int) $totals->daily_reward,
	        'monthly_reward'     => (int) $totals->monthly_reward,
	        'total_users'        => (int) $totals->total_users,
	        'bins'               => $bins,
	        'leaderboard'        => $leaderboard,
	        'recycling_activity' => $recyclingActivity,
		], 'Dashboard data fetched successfully');
	}

	/**
     * @OA\Get(
     *     path="/api/v1/organization-dashboard/bin-type",
     *     summary="Get all bin types with statistics",
     *     description="Fetch all bin types and their active bins count & total rewards for the organization.",
     *     tags={"Organization Dashboard"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Bins data fetched successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Bins data fetched successfully"),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="bin_type", type="object",
     *                         @OA\Property(property="bin_type_id", type="integer", example=1),
     *                         @OA\Property(property="name", type="string", example="Plastic"),
     *                         @OA\Property(property="image", type="string", example="/images/plastic.png"),
     *                         @OA\Property(property="icon", type="string", example="plastic-icon")
     *                     ),
     *                     @OA\Property(property="total_active_bins", type="integer", example=5),
     *                     @OA\Property(property="total_reward", type="string", example="120.00")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
	public function bintypes() {
		$user = resolve('user');
		$bins = BinType::select([
                'bin_types.id as bin_type_id',
                'bin_types.name',
                'bin_types.image',
				'bin_types.icon',
                DB::raw('COUNT(DISTINCT bins.id) as active_bins_count'),
                DB::raw('COALESCE(SUM(recyclings.reward), 0) as total_reward')
            ])
            ->leftJoin('bins', function($join) use ($user) {
                $join->on('bin_types.id', '=', 'bins.bin_type_id')
                     ->where('bins.organization_id', '=', $user->id);
            })
            ->leftJoin('recyclings', 'bins.id', '=', 'recyclings.bin_id')
            ->groupBy('bin_types.id', 'bin_types.name', 'bin_types.image')
            ->havingRaw('COUNT(DISTINCT bins.id) > 0')
            ->orderBy('bin_types.name')
            ->get()
            ->map(function ($item) {
                return [
					'bin_type' => $item,
                    'total_active_bins' => (int) $item->active_bins_count,
                    'total_reward' => number_format((float) $item->total_reward, 2)
                ];
            });
		return ApiResponse::success($bins, 'Bins data fetched successfully');
	}


	/**
     * @OA\Get(
     *     path="/api/v1/organization-dashboard/bin-type/{binTypeId}",
     *     summary="Get recycling activities for a specific bin type",
     *     description="Fetch recycling activities for a specific bin type within the organization, grouped by month and paginated.",
     *     tags={"Organization Dashboard"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="binTypeId",
     *         in="path",
     *         required=true,
     *         description="Bin type ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         description="Page number for pagination",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         required=false,
     *         description="Number of results per page",
     *         @OA\Schema(type="integer", example=20)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Recycling data fetched successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Recycling data fetched successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="bin_type", type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Plastic"),
     *                     @OA\Property(property="image", type="string", example="/images/plastic.png"),
     *                     @OA\Property(property="icon", type="string", example="plastic-icon"),
     *                     @OA\Property(property="total_active_bins", type="integer", example=3),
     *                     @OA\Property(property="total_reward", type="string", example="450.00")
     *                 ),
     *                 @OA\Property(property="recycling_activities", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="month", type="string", example="SEPTEMBER"),
     *                         @OA\Property(property="month_key", type="string", example="2025-09"),
     *                         @OA\Property(property="activities", type="array",
     *                             @OA\Items(
     *                                 @OA\Property(property="id", type="integer", example=101),
     *                                 @OA\Property(property="bin_code", type="string", example="BIN123"),
     *                                 @OA\Property(property="reward", type="string", example="10 points"),
     *                                 @OA\Property(property="nickname", type="string", example="JohnD"),
     *                                 @OA\Property(property="formatted_date", type="string", example="04/09/25 | 1:00pm"),
     *                                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-09-04T06:32:00Z")
     *                             )
     *                         )
     *                     )
     *                 ),
     *                 @OA\Property(property="pagination", type="object",
     *                     @OA\Property(property="current_page", type="integer", example=1),
     *                     @OA\Property(property="last_page", type="integer", example=5),
     *                     @OA\Property(property="per_page", type="integer", example=20),
     *                     @OA\Property(property="total", type="integer", example=100),
     *                     @OA\Property(property="has_more_pages", type="boolean", example=true)
     *                 )
     *             )
     *         )
     *     )
     * )
     */

	 public function binTypeRecycling(Request $request, int $binTypeId)
	 {
	     try {
	         $user = resolve('user');
	         $organizationId = $user->id;
	         $page = $request->get('page', null);
	         $perPage = $request->get('per_page', null);
	         $recentMonth = $request->get('recent_month', null); // ✅ add this

	         // Get bin type details
	         $binTypeDetails = $this->getBinTypeDetails($organizationId, $binTypeId);

	         if (!$binTypeDetails) {
	             throw new \Exception("Bin type not found");
	         }

	         // Get paginated recycling activities
	         $recyclingData = $this->getRecyclingActivitiesPaginated(
	             $organizationId,
	             $binTypeId,
	             $page,
	             $perPage,
	             $recentMonth // ✅ pass it here
	         );

	         return ApiResponse::success([
	             'bin_type' => $binTypeDetails,
	             'recycling_activities' => $recyclingData['activities'],
	             'pagination' => $recyclingData['pagination']
	         ], 'Recycling data fetched successfully');

	     } catch (\Exception $e) {
	         return ApiResponse::error($e->getMessage());
	     }
	 }


	public function getBinTypeDetails($organizationId, $binTypeId)
    {
        $binType = BinType::select([
                'bin_types.id',
                'bin_types.name',
                'bin_types.image',
				'bin_types.icon',
                DB::raw('COUNT(DISTINCT bins.id) as total_active_bins'),
                DB::raw('COALESCE(SUM(recyclings.reward), 0) as total_reward')
            ])
            ->leftJoin('bins', function($join) use ($organizationId) {
                $join->on('bin_types.id', '=', 'bins.bin_type_id')
                     ->where('bins.organization_id', '=', $organizationId);
            })
            ->leftJoin('recyclings', 'bins.id', '=', 'recyclings.bin_id')
            ->where('bin_types.id', $binTypeId)
            ->groupBy('bin_types.id', 'bin_types.name', 'bin_types.image')
            ->first();

        if (!$binType) {
            return null;
        }

		$binType->bins = Bin::where('organization_id', $organizationId)
	        ->where('bin_type_id', $binTypeId)
	        ->pluck('code')
	        ->toArray();

        return $binType;
    }

	public function getRecyclingActivitiesPaginated($organizationId, $binTypeId, $page = null, $perPage = null, $recentMonth = null)
	{
	    $query = Recycling::select([
	            'recyclings.id',
	            'recyclings.reward',
	            'recyclings.created_at',
	            'bins.code as bin_code',
	            'users.display_name as nickname'
	        ])
	        ->join('bins', 'recyclings.bin_id', '=', 'bins.id')
	        ->join('bin_types', 'recyclings.bin_type_id', '=', 'bin_types.id')
	        ->join('users', 'recyclings.user_id', '=', 'users.id')
	        ->where('bins.organization_id', $organizationId)
	        ->where('recyclings.bin_type_id', $binTypeId)
	        ->orderBy('recyclings.created_at', 'desc');

	    // ✅ Apply recent month filter if provided
	    if (!empty($recentMonth) && is_numeric($recentMonth)) {
	        $fromDate = now()->subMonths($recentMonth);
	        $query->where('recyclings.created_at', '>=', $fromDate);
	    }

	    // If perPage or page is null/empty/zero, return all
	    if (empty($perPage) || $perPage <= 0 || empty($page) || $page <= 0) {
	        $results = $query->get();
	    } else {
	        $results = $query->paginate($perPage, ['*'], 'page', $page);
	    }

	    // Group by month
	    $collection = $results instanceof \Illuminate\Pagination\LengthAwarePaginator
	        ? $results->getCollection()
	        : $results;

	    $groupedByMonth = $collection->groupBy(function($recycling) {
	        return $recycling->created_at ? $recycling->created_at->format('Y-m') : 'unknown';
	    });

	    $activities = [];
	    foreach ($groupedByMonth as $monthKey => $monthRecyclings) {
	        $monthName = $monthRecyclings->first()->created_at ?
	            strtoupper($monthRecyclings->first()->created_at->format('F')) : 'UNKNOWN';

	        $monthActivities = $monthRecyclings->map(function($recycling) {
	            return [
	                'id' => $recycling->id,
	                'bin_code' => $recycling->bin_code,
	                'reward' => number_format((float) $recycling->reward, 0) . ' points',
	                'nickname' => $recycling->nickname,
	                'formatted_date' => $recycling->created_at ?
	                    $recycling->created_at->format('d/m/y | g:ia') : '',
	                'created_at' => $recycling->created_at
	            ];
	        });

	        $activities[] = [
	            'month' => $monthName,
	            'month_key' => $monthKey,
	            'activities' => $monthActivities->toArray()
	        ];
	    }

	    return [
	        'activities' => $activities,
	        'pagination' => $results instanceof \Illuminate\Pagination\LengthAwarePaginator
	            ? [
	                'current_page' => $results->currentPage(),
	                'last_page' => $results->lastPage(),
	                'per_page' => $results->perPage(),
	                'total' => $results->total(),
	                'has_more_pages' => $results->hasMorePages()
	            ]
	            : null
	    ];
	}



}
