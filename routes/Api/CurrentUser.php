<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\CurrentUserController;

$prefix = env('PREFIX_API', 'v1') . "/currentUser";

Route::group(['middleware' => ['sanctum'], 'prefix' => $prefix], function () use ($prefix) {
    Route::get('', [CurrentUserController::class, 'currentUser']);
    Route::post('/Update', [CurrentUserController::class, 'update']);
    Route::post('/ChangePassword', [CurrentUserController::class, 'changePassword']);
    Route::get('/reward/{status?}', [CurrentUserController::class, 'getUserRewards']);
    Route::get('/reward/{reward}/detail', [CurrentUserController::class, 'getRewardDetail']);
	Route::get('/events/joined', [CurrentUserController::class, 'joinedEvents']);
	Route::get('/events/joined/{event_id}', [CurrentUserController::class, 'detailJoinedEvent']);
    Route::get('/events/rewards', [CurrentUserController::class, 'rewardEvents']);
    Route::get('/points-history', [CurrentUserController::class, 'pointsHistory']);
    Route::get('/point-goal', [CurrentUserController::class, 'pointGoal']);
	Route::post('/send-email-verification-code', [CurrentUserController::class, 'sendEmailVerificationCode']);
	Route::post('/verify-email', [CurrentUserController::class, 'verifyEmail']);
	Route::get('/leaderboard', [CurrentUserController::class, 'leaderboard']);
	Route::get('/recycling-history', [CurrentUserController::class, 'recyclingHistory']);
});
