<?php

namespace App\Http\Controllers\Api\v1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Reward;
use App\Models\UserReward;
use App\Models\Setting;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\RewardRedeemedMail;

class RewardController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/v1/rewards/getAll",
     *     summary="Get a paginated list of rewards",
     *     security={{"bearerAuth":{}}},
     *     tags={"Rewards"},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="The page number to retrieve.",
     *         @OA\Schema(
     *             type="integer",
     *             default=1
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Success"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(
     *                     property="data",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="label", type="string", example="Reward Label"),
     *                         @OA\Property(property="image", type="string", example="image.png"),
     *                         @OA\Property(property="price", type="number", format="float", example=100.00)
     *                     )
     *                 ),
     *                 @OA\Property(property="first_page_url", type="string", example="http://localhost/api/v1/rewards/getAll?page=1"),
     *                 @OA\Property(property="from", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=5),
     *                 @OA\Property(property="last_page_url", type="string", example="http://localhost/api/v1/rewards/getAll?page=5"),
     *                 @OA\Property(
     *                      property="links",
     *                      type="array",
     *                      @OA\Items(
     *                          type="object",
     *                          @OA\Property(property="url", type="string", nullable=true, example="http://localhost/api/v1/rewards/getAll?page=1"),
     *                          @OA\Property(property="label", type="string", example="&laquo; Previous"),
     *                          @OA\Property(property="active", type="boolean", example=false)
     *                      )
     *                 ),
     *                 @OA\Property(property="next_page_url", type="string", nullable=true, example="http://localhost/api/v1/rewards/getAll?page=2"),
     *                 @OA\Property(property="path", type="string", example="http://localhost/api/v1/rewards/getAll"),
     *                 @OA\Property(property="per_page", type="integer", example=10),
     *                 @OA\Property(property="prev_page_url", type="string", nullable=true, example=null),
     *                 @OA\Property(property="to", type="integer", example=10),
     *                 @OA\Property(property="total", type="integer", example=50)
     *             ),
     *             @OA\Property(property="errors", type="object", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function getAll(Request $request)
    {
        $rewards = Reward::withCount(['vouchers' => function ($query) {
            $query->where('status', '!=', Voucher::STATUS_REDEEMED);
        }])->where('status', Reward::STATUS_ACTIVE)->orderBy('price', 'ASC')->select('id', 'name', 'label', 'image', 'price')->paginate(10);
        return ApiResponse::success($rewards);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/rewards/detail/{reward}",
     *     summary="Get a single reward by ID",
     *     security={{"bearerAuth":{}}},
     *     tags={"Rewards"},
     *     @OA\Parameter(
     *         name="reward",
     *         in="path",
     *         description="ID of the reward to retrieve",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Success"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="label", type="string", example="Reward Label"),
     *                 @OA\Property(property="image", type="string", example="image.png"),
     *                 @OA\Property(property="price", type="number", format="float", example=100.00)
     *             ),
     *             @OA\Property(property="errors", type="object", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Reward not found"
     *     )
     * )
     */
    public function detail($rewardId)
    {
        $reward = Reward::withCount(['vouchers' => function ($query) {
            $query->where('status', '!=', 2);
        }])->select('id', 'name', 'label', 'image', 'price', 'description', 'tnc', 'status')->find($rewardId);
        return ApiResponse::success($reward);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/rewards/{reward}/redeem",
     *     summary="Redeem a reward",
     *     security={{"bearerAuth":{}}},
     *     tags={"Rewards"},
     *     @OA\Parameter(
     *         name="reward",
     *         in="path",
     *         description="ID of the reward to redeem",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Reward redeemed successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Reward redeemed successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="reward_id", type="integer", example=1),
     *                 @OA\Property(property="user_id", type="integer", example=1),
     *                 @OA\Property(property="code", type="string", example="VOUCHER-CODE"),
     *                 @OA\Property(property="status", type="integer", example=1),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time"),
     *                 @OA\Property(property="status_text", type="string", example="Active")
     *             ),
     *             @OA\Property(property="errors", type="object", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error (e.g., no vouchers, insufficient points)"
     *     ),
     *      @OA\Response(
     *         response=404,
     *         description="Reward not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error"
     *     )
     * )
     */
    public function redeem(Request $request, $rewardID)
    {
        $reward = Reward::find($rewardID);
		if (!$reward) {
			return ApiResponse::error('Reward not found');
		}
		
        $user = resolve('user');

		$alreadyRedeemed = UserReward::where('user_id', $user->id)
		    ->where('reward_id', $rewardID)
		    ->whereMonth('created_at', now()->month)
		    ->whereYear('created_at', now()->year)
		    ->exists();

		if ($alreadyRedeemed) {
		    return ApiResponse::error('You have already redeemed this reward this month.', 422);
		}

        if ($reward->remaining_vouchers < 1) {
            return ApiResponse::error('No more vouchers available for this reward.', 422);
        }

		// Fetch monthly point limit from settings
		$setting = Setting::first();
		$maxMonthlyPoints = $setting->user_max_monthly_redemption ?? 0;

		// Calculate total points already spent this month
		$monthlyPointsUsed = UserReward::where('user_id', $user->id)
			->whereMonth('user_rewards.created_at', now()->month)
			->whereYear('user_rewards.created_at', now()->year)
			->join('rewards', 'user_rewards.reward_id', '=', 'rewards.id')
			->sum('rewards.price');

		// Check if redeeming this reward would exceed the monthly limit
		if (($monthlyPointsUsed + $reward->price) > $maxMonthlyPoints) {
			return ApiResponse::error('You have reached your monthly redemption point limit.', 422);
		}

        if ($user->point < $reward->price) {
            return ApiResponse::error('You do not have enough points to redeem this reward.', 422);
        }

        try {
            DB::beginTransaction();

            $voucher = $reward->vouchers()->where('status', 1)->first();

            if (!$voucher) {
                DB::rollBack();
                return ApiResponse::error('No available voucher found for this reward at this moment.', 422);
            }

            UserReward::create([
                'user_id' => $user->id,
                'reward_id' => $reward->id,
                'voucher_id' => $voucher->id,
            ]);

            $user->point -= $reward->price;
            $user->save();

            $voucher->status = 2;
            $voucher->save();

            DB::commit();

            Mail::to($user->email)->send(new RewardRedeemedMail($user, $reward, $voucher));

            return ApiResponse::success([
                'voucher' => $voucher,
                'user_point' => $user->point,
                'reward_price' => $reward->price,
            ], 'Reward redeemed successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponse::error($e->getMessage(), 500);
        }
    }
}
