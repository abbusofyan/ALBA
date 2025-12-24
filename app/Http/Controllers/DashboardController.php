<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Recycling;
use App\Models\UserReward;
use Carbon\Carbon;
use DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
		$totalUsers = User::whereHas('roles', function ($query) {
		    $query->whereIn('name', ['Public', 'School', 'Enterprise']);
		})->count();
		$totalRecyclingPoint = Recycling::sum('reward');
		$totalRecyclingSubmission = Recycling::count();
		$voucherRedeemedThisMonth = UserReward::whereMonth('created_at', Carbon::now()->month)
		    ->whereYear('created_at', Carbon::now()->year)
		    ->count();

		$data = [
			'keyStats' => [
				'totalUsers' => $totalUsers,
				'totalCO2Points' => (int)$totalRecyclingPoint,
				'totalRecyclingSubmissions' => $totalRecyclingSubmission,
				'totalVouchersRedeemed' => $voucherRedeemedThisMonth
			],
			'recyclingOverview' => $this->getMonthlyRecyclingOverview(),
			'rewardsAndVouchers' => $this->getRewardsAndVouchers()
		];

        return Inertia::render('Dashboard/Index', [
			'dashboardData' => $data
		]);
    }

	private function getMonthlyRecyclingOverview()
	{
	    $now = Carbon::now();
	    $currentMonth = $now->month;
	    $currentYear = $now->year;
	    $previousMonthDate = $now->copy()->subMonth();
	    $previousMonth = $previousMonthDate->month;
	    $previousYear = $previousMonthDate->year;

	    // Get total submissions this month
	    $currentTotal = DB::table('recyclings')
	        ->whereYear('created_at', $currentYear)
	        ->whereMonth('created_at', $currentMonth)
	        ->count();

	    // Get total submissions last month
	    $previousTotal = DB::table('recyclings')
	        ->whereYear('created_at', $previousYear)
	        ->whereMonth('created_at', $previousMonth)
	        ->count();

	    // Get number of days in current month
	    $daysInMonth = $now->daysInMonth;

	    $dailyData = [];

	    for ($day = 1; $day <= $daysInMonth; $day++) {
	        $currentDayCount = DB::table('recyclings')
	            ->whereYear('created_at', $currentYear)
	            ->whereMonth('created_at', $currentMonth)
	            ->whereDay('created_at', $day)
	            ->count();

	        $previousDayCount = DB::table('recyclings')
	            ->whereYear('created_at', $previousYear)
	            ->whereMonth('created_at', $previousMonth)
	            ->whereDay('created_at', $day)
	            ->count();

	        $dailyData[] = [
	            'day' => $day,
	            'current' => $currentDayCount,
	            'previous' => $previousDayCount
	        ];
	    }

	    // Calculate trend
	    $trend = $previousTotal > 0
	        ? round((($currentTotal - $previousTotal) / $previousTotal) * 100, 1)
	        : null;

		return [
			'submissionsThisMonth' => [
				'current' => $currentTotal,
				'previous' => $previousTotal,
				'trend' => $trend,
				'dailyData' => $dailyData
			],
			'topActiveBins' => $this->getTopActiveBins(),
			'co2Leaderboard' => $this->getCo2Leaderboard()
		];
	}

	public function getTopActiveBins()
	{
	    $topBins = DB::table('recyclings')
	        ->select(
	            'bins.id',
	            'bins.address as location',
	            'bin_types.name as type',
	            DB::raw('COUNT(recyclings.id) as submissions')
	        )
	        ->join('bins', 'recyclings.bin_id', '=', 'bins.id')
	        ->join('bin_types', 'bins.bin_type_id', '=', 'bin_types.id')
	        ->groupBy('bins.id', 'bins.address', 'bin_types.name')
	        ->orderByDesc('submissions')
	        ->limit(10)
	        ->get();

		return $topBins;
	}

	public function getCo2Leaderboard()
	{
	    $now = Carbon::now();

	    $nameExpression = DB::raw("IF(users.name IS NULL OR users.name = '', CONCAT(users.first_name, ' ', users.last_name), users.name) as name");

	    // Leaderboard for last 7 days
	    $leaderboard7 = DB::table('recyclings')
	        ->join('users', 'users.id', '=', 'recyclings.user_id')
	        ->select(
	            'users.id',
	            $nameExpression,
	            DB::raw('SUM(recyclings.reward) as co2Points'),
	            DB::raw('COUNT(recyclings.id) as submissions')
	        )
	        ->where('recyclings.created_at', '>=', $now->copy()->subDays(7))
	        ->groupBy('users.id', 'name')
	        ->orderByDesc('co2Points')
	        ->limit(10)
	        ->get();

	    // Leaderboard for last 30 days
	    $leaderboard30 = DB::table('recyclings')
	        ->join('users', 'users.id', '=', 'recyclings.user_id')
	        ->select(
	            'users.id',
	            $nameExpression,
	            DB::raw('SUM(recyclings.reward) as co2Points'),
	            DB::raw('COUNT(recyclings.id) as submissions')
	        )
	        ->where('recyclings.created_at', '>=', $now->copy()->subDays(30))
	        ->groupBy('users.id', 'name')
	        ->orderByDesc('co2Points')
	        ->limit(10)
	        ->get();

	    return [
	        'last7Days' => $leaderboard7,
	        'last30Days' => $leaderboard30
	    ];
	}


	public function getRewardsAndVouchers()
	{
	    // Top 5 most redeemed rewards
		$mostRedeemed = DB::table('user_rewards')
		    ->join('rewards', 'rewards.id', '=', 'user_rewards.reward_id')
		    ->select(
		        'rewards.id',
		        'rewards.name',
		        'rewards.price as points_cost',
		        DB::raw('COUNT(user_rewards.id) as redemptions')
		    )
		    ->whereMonth('user_rewards.created_at', now()->month)
		    ->whereYear('user_rewards.created_at', now()->year)
		    ->groupBy('rewards.id', 'rewards.name', 'rewards.price')
		    ->orderByDesc('redemptions')
		    ->limit(10)
		    ->get();

		$lowStock = DB::table('rewards')
	        ->leftJoin('vouchers', function ($join) {
	            $join->on('vouchers.reward_id', '=', 'rewards.id')
	                 ->where('vouchers.status', '!=', 2);
	        })
	        ->select(
	            'rewards.id',
	            'rewards.name',
	            DB::raw('COUNT(vouchers.id) as remaining')
	        )
	        ->groupBy('rewards.id', 'rewards.name')
	        ->orderBy('remaining', 'asc')
	        ->limit(10)
	        ->get();

	    return [
			'mostRedeemedRewards' => $mostRedeemed,
			'lowStockRewards' => $lowStock
		];
	}

    public function components(Request $request)
    {
        return Inertia::render('Dashboard/Component');
    }
}
