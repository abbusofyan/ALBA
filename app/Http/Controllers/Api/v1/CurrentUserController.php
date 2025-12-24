<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\ApiController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Intervention\Image\Laravel\Facades\Image;
use App\Helpers\ApiResponse;
use App\Models\Setting;
use App\Models\Recycling;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\UserReward;
use App\Models\Reward;
use App\Models\Event;
use App\Models\EventRecyclingLog;
use App\Models\UserJoinedEvent;
use App\Models\Voucher;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendEmailVerificationCode;
use App\Http\Requests\UpdateCurrentUserFormRequest;

class CurrentUserController extends ApiController
{
    /**
     * @OA\Get(
     *     security={{"bearerAuth":{}}},
     *     path="/api/v1/currentUser",
     *     tags={"Current User"},
     *     summary="Get current user",
     *     description="Get current user",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       )
     * )
     */
    public function currentUser(Request $request)
    {
        try {
            $user = resolve('user');
            if ($user->roles->count() > 0) $user->role_name = $user->roles->first()->name;
            return $this->result($user);
        } catch (\Exception $e) {
            return $this->result('', $e->getMessage(), $e->getCode());
        }
    }

    /**
     * @OA\Get(
     *     security={{"bearerAuth":{}}},
     *     path="/api/v1/currentUser/events/joined",
     *     tags={"Current User"},
     *     summary="Get all joined events",
     *     description="Get all joined events",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       )
     * )
     */
    public function joinedEvents()
    {
        try {
            $user = resolve('user');
            $events = $user->joinedEvents()->with('type')->where('date_end', '>=', Carbon::now())->get();
            return ApiResponse::success([
                'events' => $events,
            ], 'Events retrieved successfully');
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }


    /**
     * @OA\Get(
     *     security={{"bearerAuth":{}}},
     *     path="/api/v1/currentUser/events/joined/{event_id}",
     *     tags={"Current User"},
     *     summary="Get detail of joined event",
     *     description="Get detail of joined event",
     *     @OA\Parameter(
     *         name="event_id",
     *         in="path",
     *         description="Event id",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       )
     * )
     */
    public function detailJoinedEvent($event_id)
    {
        try {
            $user = resolve('user');
            $userEventExists = UserJoinedEvent::where('user_id', $user->id)->where('event_id', $event_id)->exists();
            if (!$userEventExists) {
                throw new \Exception("User has not joined this event");
            }
            $event = Event::find($event_id);
            $userEventPoint = EventRecyclingLog::join('recyclings', 'event_recycling_logs.recycling_id', '=', 'recyclings.id')
                ->where('event_recycling_logs.user_id', $user->id)
                ->where('event_recycling_logs.event_id', $event_id)
                ->selectRaw('SUM(recyclings.reward) as total_reward')
                ->value('total_reward');
            return ApiResponse::success([
                'event' => $event,
                'user_event_point' => (int)$userEventPoint
            ], 'Event detail retrieved successfully');
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }


    /**
     * @OA\Post (
     *     security={{"bearerAuth":{}}},
     *     path="/api/v1/currentUser/Update",
     *     tags={"Current User"},
     *     description="Update current user",
     *     summary="Update current user",
     *      @OA\RequestBody(
     *          required=true,
     *          description="Media file to upload",
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="first_name",
     *                      type="string"
     *                  ),
     *                  @OA\Property(
     *                      property="last_name",
     *                      type="string"
     *                  ),
     *                  @OA\Property(
     *                      property="display_name",
     *                      type="string"
     *                  ),
     *                  @OA\Property(
     *                      property="email",
     *                      type="string"
     *                  ),
     *                  @OA\Property(
     *                      property="phone",
     *                      type="string"
     *                  ),
     *                  @OA\Property(
     *                      property="dob",
     *                      type="date"
     *                  ),
     *                  @OA\Property(
     *                      property="address",
     *                      type="string"
     *                  ),
     *                  @OA\Property(
     *                      property="postal_code",
     *                      type="string"
     *                  )
     *              )
     *          )
     *      ),
     *     @OA\Response(
     *          response=200,
     *          description="Success"
     *      )
     * )
     *
     */
    public function update(UpdateCurrentUserFormRequest $request)
    {

        try {
            $user = resolve('user');
            if (empty($user)) throw new \Exception('We apologize, but we are unable to locate the user you are searching for at this time.', 404);

            $fullname = $request->first_name . ' ' . $request->last_name;

            $user->name = $fullname;
            $user->first_name = $request->input('first_name');
            $user->last_name = $request->input('last_name');
            $user->dob = $request->input('dob');
            $user->address = $request->input('address');
            $user->postal_code = $request->input('postal_code');

            if ($request->input('phone')) {
                $user->phone = $request->input('phone');
            }
            if ($request->input('email')) {
                $user->email = $request->input('email');
            }
            if ($request->input('display_name')) {
                $user->display_name = $request->input('display_name');
            }

            $user->save();
            return ApiResponse::success(['user' => $user], 'User updated successfully');
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @OA\Post(
     *     security={{"bearerAuth":{}}},
     *     path="/api/v1/currentUser/ChangePassword",
     *     tags={"Current User"},
     *     summary="Change Password",
     *     description="Change Password",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                      type="object",
     *                      @OA\Property(
     *                          property="password",
     *                          type="string"
     *                      ),
     *                       @OA\Property(
     *                           property="new_password",
     *                           type="string"
     *                       ),
     *                        @OA\Property(
     *                            property="new_password_confirmation",
     *                            type="string"
     *                        )
     *                 ),
     *                 example={
     *                     "password":"2135498782",
     *                     "new_password":"123456789",
     *                     "new_password_confirmation":"123456789",
     *                }
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       )
     * )
     */
    public function changePassword(Request $request)
    {
        try {
            $user = resolve('user');
            if (empty($user)) throw new \Exception('We apologize, but we are unable to locate the user you are searching for at this time.', 404);
            $validator = Validator::make($request->all(), [
                'password' => ['required', 'string', function ($attribute, $value, $fail) use ($user) {
                    if (!Hash::check($value, $user->password)) $fail('Your password is incorrect.');
                }],
                'new_password' => $this->passwordRules()
            ]);
            if ($validator->fails()) return $this->result('', $validator->errors(), 400);
            $user->password = Hash::make($request->input('new_password'));
            $user->save();
            return $this->result(['user' => $user]);
        } catch (\Exception $e) {
            return $this->result('', $e->getMessage(), $e->getCode());
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/currentUser/reward/{status}",
     *     summary="Get active or expired rewards for the current user",
     *     security={{"bearerAuth":{}}},
     *     tags={"Current User"},
     *     @OA\Parameter(
     *         name="status",
     *         in="path",
     *         description="The status of the rewards to retrieve (active or expired)",
     *         @OA\Schema(
     *             type="string",
     *             enum={null, "active", "expired"}
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="User rewards retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="rewards",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="name", type="string", example="Reward Name"),
     *                         @OA\Property(property="label", type="string", example="Reward Label"),
     *                         @OA\Property(property="price", type="number", format="float", example=100.00),
     *                         @OA\Property(property="image", type="string", example="image.png")
     *                     )
     *                 )
     *             ),
     *             @OA\Property(property="errors", type="object", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *      @OA\Response(
     *         response=400,
     *         description="Invalid status provided"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error"
     *     )
     * )
     */
    public function getUserRewards(Request $request, $status = null)
    {
        try {
            $user = resolve('user');
            $userRewards = $user->rewards()
                ->whereHas('reward', function ($query) use ($status) {
                    if ($status === 'active') {
                        $query->active();
                    }
                    if ($status == 'expired') {
                        $query->expired();
                    }
                })
                ->with(['reward' => function ($query) {
                    $query->select('id', 'name', 'label', 'price', 'image', 'expired_date');
                }])
                // ->with(['voucher' => function ($query) {
                //     $query->select('id', 'code');
                // }])
                ->get()
                // ->pluck('voucher')
                ->filter();

            return ApiResponse::success([
                'rewards' => $userRewards
            ], 'User rewards retrieved successfully');
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @OA\Get(
     *     security={{"bearerAuth":{}}},
     *     path="/api/v1/currentUser/points-history",
     *     tags={"Current User"},
     *     summary="Get user points history",
     *     description="Retrieves a formatted history of the user's earned and redeemed points, grouped by month and year.",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(
     *                         property="month_year",
     *                         type="string",
     *                         example="JUL 2024"
     *                     ),
     *                     @OA\Property(
     *                         property="transactions",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(
     *                                 property="title",
     *                                 type="string",
     *                                 example="Earn points"
     *                             ),
     *                             @OA\Property(
     *                                 property="type",
     *                                 type="string",
     *                                 example="earned"
     *                             ),
     *                             @OA\Property(
     *                                 property="points",
     *                                 type="number",
     *                                 format="float",
     *                                 example=100.00
     *                             ),
     *                             @OA\Property(
     *                                 property="date",
     *                                 type="string",
     *                                 example="04 Jul 2024 | 3:30pm"
     *                             )
     *                         )
     *                     )
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Points history retrieved successfully"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error"
     *     )
     * )
     */
    public function pointsHistory(Request $request)
    {
        try {
            $user = resolve('user');

            $earnedPoints = DB::table('recyclings')
                ->where('user_id', $user->id)
                ->select('reward as points', 'created_at', DB::raw("'Earn points' as title, 'earned' as type"));

            $redeemedPoints = DB::table('user_rewards')
                ->join('rewards', 'user_rewards.reward_id', '=', 'rewards.id')
                ->where('user_rewards.user_id', $user->id)
                ->select('rewards.price as points', 'user_rewards.created_at', DB::raw("'Points redeemed' as title, 'redeemed' as type"));

            $history = $earnedPoints->unionAll($redeemedPoints)
                ->orderBy('created_at', 'desc')
                ->get();

            $groupedHistory = $history->groupBy(function ($item) {
                return \Carbon\Carbon::parse($item->created_at)->format('M Y');
            });

            $formattedHistory = $groupedHistory->map(function ($items, $monthYear) {
                return [
                    'month_year' => strtoupper($monthYear),
                    'transactions' => $items->map(function ($item) {
                        $points = (float) $item->points;
                        return [
                            'title' => $item->title,
                            'type' => $item->type,
                            'points' => $item->type === 'redeemed' ? -$points : $points,
                            'date' => \Carbon\Carbon::parse($item->created_at)->format('d M Y | g:ia'),
                        ];
                    })
                ];
            })->values();

            return ApiResponse::success($formattedHistory, 'Points history retrieved successfully');
        } catch (\Exception $e) {
            Log::error($e);
            return ApiResponse::error('An error occurred while fetching points history.', 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/currentUser/reward/{voucherId}/detail",
     *     summary="Get Voucher detail",
     *     security={{"bearerAuth":{}}},
     *     tags={"Current User"},
     *     @OA\Parameter(
     *         name="voucherId",
     *         in="path",
     *         description="Voucher ID",
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
     *             @OA\Property(property="message", type="string", example="User reward detail retrieved successfully"),
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="errors", type="object", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *      @OA\Response(
     *         response=404,
     *         description="Not Found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error"
     *     )
     * )
     */
    public function getRewardDetail(Request $request, $voucherId)
    {
        try {
            $user = resolve('user');
            $userReward = UserReward::where('user_id', $user->id)
                ->where('voucher_id', $voucherId)
                ->latest()
                ->first();

            if (!$userReward) {
                return ApiResponse::error('User has not redeemed this reward or reward not found.', 404);
            }

            $userReward->load(['reward', 'voucher']);

            return ApiResponse::success([
                'reward' => $userReward->reward,
                'voucher' => $userReward->voucher,
            ], 'User reward detail retrieved successfully');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ApiResponse::error('Reward not found.', 404);
        } catch (\Exception $e) {
            dd($e->getMessage());
            return ApiResponse::error('An error occurred while fetching user reward detail.', 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/currentUser/point-goal",
     *     summary="Get the point goal for the current user",
     *     security={{"bearerAuth":{}}},
     *     tags={"Current User"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Point goal retrieved successfully"),
     *             @OA\Property(property="data", type="object"),
     *         )
     *     )
     * )
     */
    public function pointGoal(Request $request)
    {
        try {

            $user = resolve('user');
            $todayRecycling = Recycling::where('user_id', $user->id)
                ->whereDate('created_at', Carbon::today())
                ->sum('reward');
            $setting = Setting::first();
            if (!$setting) {
                $setting = null;
            } else {
                $maxPoint = $setting->user_max_daily_reward;
            }
            return ApiResponse::success([
                'user_total_point' => $user->point,
                'daily_point_goal' => $maxPoint,
                'today_reward' => $todayRecycling,
            ], 'Point goal retrieved successfully');
        } catch (\Exception $e) {
            return ApiResponse::error('An error occurred while fetching point goal.', 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/currentUser/send-email-verification-code",
     *     summary="Send email verification code",
     *     tags={"Current User"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Verification code sent successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="A verification code has been sent to your email"),
     *             @OA\Property(property="data", type="string", example=null)
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Failed to send verification code",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Failed to send verification code. Please try again later."),
     *             @OA\Property(property="errors", type="string", example=null)
     *         )
     *     )
     * )
     */
    public function sendEmailVerificationCode(Request $request)
    {
        try {
            $user = resolve('user');

            $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

            $user->verify_code = $code;
            $user->verification_expires_at = Carbon::now()->addMinutes(10);
            $user->save();

            Mail::to($user->email)->send(new SendEmailVerificationCode($code));

            return ApiResponse::success(null, 'A verification code has been sent to your email');
        } catch (\Exception $e) {
            return ApiResponse::error('Failed to send verification code. Please try again later.', 500);
        }
    }


    /**
     * @OA\Post(
     *     path="/api/v1/currentUser/verify-email",
     *     summary="Verify email with code",
     *     tags={"Current User"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"code"},
     *             @OA\Property(property="code", type="string", example="123456")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Email verified successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Email verified successfully"),
     *             @OA\Property(property="data", type="string", example=null)
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid or expired verification code",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Invalid verification code."),
     *             @OA\Property(property="errors", type="string", example=null)
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="An unexpected error occurred."),
     *             @OA\Property(property="errors", type="string", example=null)
     *         )
     *     )
     * )
     */
    public function verifyEmail(Request $request)
    {
        try {
            $request->validate([
                'code'  => 'required|string',
            ]);

            $user = resolve('user');

            if ($user->verify_code !== $request->code) {
                throw new \Exception('Invalid verification code.', 400);
            }

            if (!$user->verification_expires_at || Carbon::now()->gt($user->verification_expires_at)) {
                throw new \Exception('Verification code has expired.', 400);
            }

            $user->email_verified_at = now();
            $user->verify_code = null;
            $user->verification_expires_at = null;
            $user->save();

            return ApiResponse::success(null, 'Email verified successfully');
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 500);
        }
    }


    /**
     * @OA\Get(
     *     path="/api/v1/currentUser/leaderboard",
     *     security={{"bearerAuth":{}}},
     *     tags={"Current User"},
     *     summary="Get leaderboard",
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Event not found"
     *     )
     * )
     */
    public function leaderboard(Request $request)
    {
        try {
            $user = resolve('user');

            $leaderboardData = DB::table('recyclings')
                ->join('users', 'recyclings.user_id', '=', 'users.id')
                ->select(
                    'users.id',
                    'users.name',
                    'users.username',
                    'users.first_name',
                    'users.last_name',
                    'users.display_name',
                    DB::raw('SUM(recyclings.reward) as total_points')
                )
                ->groupBy('users.id', 'users.name', 'users.username', 'users.first_name', 'users.last_name', 'users.display_name')
                ->orderByDesc('total_points')
                ->get();

            $rankedUsers = $leaderboardData->map(function ($item, $key) {
                $item->rank = $key + 1;
                return $item;
            });

            $currentUserData = $rankedUsers->firstWhere('id', $user->id);

            $myRank = null;
            $myPoints = 0;
            if ($currentUserData) {
                $myRank = $currentUserData->rank;
                $myPoints = (float) $currentUserData->total_points;
            }

            $totalContributors = $rankedUsers->count();

            $getInitials = function ($firstName, $lastName, $fallbackName) {
                if (!empty($firstName) || !empty($lastName)) {
                    $first = !empty($firstName) ? strtoupper(substr($firstName, 0, 1)) : '';
                    $last = !empty($lastName) ? strtoupper(substr($lastName, 0, 1)) : '';
                    return $first . $last;
                }

                $parts = explode(' ', trim($fallbackName));
                $initials = '';
                foreach ($parts as $part) {
                    $initials .= strtoupper(substr($part, 0, 1));
                }
                return substr($initials, 0, 2);
            };

            $topContributors = $rankedUsers->take(20)->map(function ($contributor) use ($getInitials) {
                return [
                    'rank' => $contributor->rank,
                    'name' => $contributor->display_name,
                    'initials' => $getInitials($contributor->first_name, $contributor->last_name, $contributor->name),
                    'total_points' => (float) $contributor->total_points
                ];
            });

            $myDataForList = null;
            if ($currentUserData) {
                $myDataForList = [
                    'name' => $currentUserData->username . ' (You)',
                    'initials' => $getInitials($currentUserData->first_name, $currentUserData->last_name, $currentUserData->name),
                    'total_points' => (float) $currentUserData->total_points
                ];
            }

            $response = [
                'my_impact' => [
                    'total_points' => $myPoints,
                ],
                'leaderboard_position' => [
                    'my_rank' => $myRank,
                    'total_contributors' => $totalContributors,
                ],
                'top_contributors' => $topContributors,
                'my_rank_data' => $myDataForList
            ];

            return ApiResponse::success($response, 'Leaderboard data retrieved successfully');
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/currentUser/recycling-history",
     *     security={{"bearerAuth":{}}},
     *     tags={"Current User"},
     *     summary="Get leaderboard",
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description=""
     *     )
     * )
     */
    public function recyclingHistory(Request $request)
    {
        try {
            $user = resolve('user');
            $recyclings = Recycling::with([
                'bin' => function ($query) {
                    $query->select('id', 'bin_type_id');
                },
                'bin.type' => function ($query) {
                    $query->select('id', 'name');
                }
            ])
                ->where('user_id', $user->id)
                ->orderBy('id', 'DESC')
                ->get()
                ->groupBy(function ($item) {
                    return Carbon::parse($item->created_at)->format('Y-m');
                });

            return ApiResponse::success(['data' => $recyclings], 'Reyycling history data retrieved successfully');
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode() ?: 500);
        }
    }
}
