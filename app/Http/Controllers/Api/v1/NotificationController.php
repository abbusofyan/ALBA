<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\ApiController;
use App\Models\PushNotification;
use App\Models\NotificationUser;
use App\Models\UserSeenNotification;
use Illuminate\Http\Request;
use App\Helpers\ApiResponse;

class NotificationController extends ApiController
{

	/**
	 * @OA\Get(
	 *     path="/api/v1/notifications",
	 *     security={{"bearerAuth":{}}},
	 *     tags={"Notification"},
	 *     @OA\Response(
	 *         response=200,
	 *         description="List of service options",
	 *         @OA\JsonContent(
	 *             type="object",
	 *             @OA\Property(property="status", type="boolean", example=true),
	 *             @OA\Property(property="message", type="string", example="service options fetched successfully"),
	 *         )
	 *     )
	 * )
	 */
	public function all(Request $request)
    {
		$user = resolve('user');

        // Paginate unseen notifications (e.g., 10 per page)
        $notifications = PushNotification::select('id', 'title', 'body', 'created_at')
            ->whereDoesntHave('seenByUsers', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Group by Year + Month
        $grouped = $notifications->getCollection()
            ->groupBy(function ($item) {
                return $item->created_at->format('F Y'); // Example: "May 2025"
            });

		return ApiResponse::success([
			'data' => $grouped,
			'pagination' => [
                'current_page' => $notifications->currentPage(),
                'last_page'    => $notifications->lastPage(),
                'per_page'     => $notifications->perPage(),
                'total'        => $notifications->total(),
            ],
		]);
    }

	/**
	 * @OA\Get(
	 *     path="/api/v1/notifications/mark-as-read/{notificationId}",
	 *     tags={"Notification"},
	 *     security={{"bearerAuth":{}}},
	 *     @OA\Parameter(
	 *         name="notificationId",
	 *         in="path",
	 *         required=true,
	 *         @OA\Schema(type="integer", example=1)
	 *     ),
	 *     @OA\Response(
	 *         response=200,
	 *         description="List of service options",
	 *         @OA\JsonContent(
	 *             type="object",
	 *             @OA\Property(property="status", type="boolean", example=true),
	 *             @OA\Property(property="message", type="string", example="service options fetched successfully"),
	 *         )
	 *     )
	 * )
	 */
	public function markAsRead($notificationId) {
		$user = resolve('user');
		UserSeenNotification::firstOrCreate(
		    [
		        'user_id' => $user->id,
		        'notification_id' => $notificationId
		    ],
		    [
		        'seen_at' => now()
		    ]
		);
		return ApiResponse::success([], 'Notification marked as read');
	}

	/**
	 * @OA\Get(
	 *     path="/api/v1/notifications/mark-as-read-all",
	 *     security={{"bearerAuth":{}}},
	 *     tags={"Notification"},
	 *     @OA\Response(
	 *         response=200,
	 *         description="List of service options",
	 *         @OA\JsonContent(
	 *             type="object",
	 *             @OA\Property(property="status", type="boolean", example=true),
	 *             @OA\Property(property="message", type="string", example="service options fetched successfully"),
	 *         )
	 *     )
	 * )
	 */
	public function markAsReadAll() {
	    $user = resolve('user');

	    // Get all unseen notification IDs
	    $notificationIds = PushNotification::whereDoesntHave('seenByUsers', function ($query) use ($user) {
	        $query->where('user_id', $user->id);
	    })->pluck('id');

	    if ($notificationIds->isEmpty()) {
			return ApiResponse::error('No unseen notifications');
	    }

	    // Prepare data for bulk insert
	    $insertData = $notificationIds->map(function ($id) use ($user) {
	        return [
	            'user_id' => $user->id,
	            'notification_id' => $id,
	            'seen_at' => now(),
				'created_at' => now(),
				'updated_at' => now(),
	        ];
	    })->toArray();

	    UserSeenNotification::insert($insertData);

		return ApiResponse::success([], 'All notifications marked as read');
	}


}
