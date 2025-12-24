<?php
include_once 'auth.php';
include_once 'Web/SelectData.php';
include_once 'Web/Dashboard.php';
include_once 'Web/Menu.php';
include_once 'Web/Role.php';
include_once 'Web/AppData.php';
include_once 'Web/Notification.php';
include_once 'Web/Permission.php';
include_once 'Web/User.php';
include_once 'Web/School.php';
include_once 'Web/ApiLog.php';
include_once 'Web/Staff.php';
include_once 'Web/Enterprise.php';
include_once 'Web/Bin.php';
include_once 'Web/BinType.php';
include_once 'Web/Recycling.php';
include_once 'Web/Event.php';
include_once 'Web/AutoComplete.php';
include_once 'Web/DownloadTemplate.php';
include_once 'Web/Reward.php';
include_once 'Web/Voucher.php';
include_once 'Web/Setting.php';
include_once 'Web/Banner.php';
include_once 'Web/PushNotification.php';


use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Recycling;

Route::get('/', function() {
	return redirect('/login');
});

Route::get('/privacy-policy', function() {
	return view('privacy-policy');
});

Route::get('/terms-and-conditions', function() {
	return view('tnc');
});

Route::get('/test', function() {
    $duplicatedEmails = DB::table('users')
        ->select('email', DB::raw('COUNT(email) AS total'))
        ->groupBy('email')
        ->having('total', '>', 1)
        ->get();
	$data = [];
	foreach ($duplicatedEmails as $duplicatedEmail) {
		$users = User::where('email', $duplicatedEmail->email)->get();
		foreach ($users as $user) {
			$recyclings = Recycling::where('user_id', $user->id)->count();
			if ($recyclings > 0) {
				$data[] = [
					'email' => $duplicatedEmail->email,
					'user_id' => $user->id,
					'role' => $user->roles[0]->name,
					'recyclings' => $recyclings
				];
			}
		}
	}
	dd($data);
});

Route::get('/getNewStagingUser', function() {
	$users = \App\Models\User::with('recyclings')->where('id', '>=', 58354)->get()->toArray();
	dd(json_encode($users));
});


Route::get('/removeDuplicateRVMRecycling', function() {

	try {
        DB::transaction(function () {
            // 🧩 Step 1: Fetch duplicate recycling batches per user and timestamp
            $duplicates = DB::select("
                SELECT
                    user_id,
                    created_at,
                    GROUP_CONCAT(id ORDER BY id ASC) AS id_to_remove,
                    SUM(reward) AS total_reward,
                    (SUM(reward) - MIN(reward)) AS reward_to_deduct
                FROM recyclings
				WHERE
		   		created_at >= '2025-09-16 21:33:14'
		   		AND bin_type_id = 1
                GROUP BY user_id, created_at
                HAVING COUNT(id) > 1
                ORDER BY user_id, created_at ASC
            ");

            $prevUserId = null;
            $prevCreatedAt = null;

            foreach ($duplicates as $duplicate) {
                $isWithin2Sec = (
                    $prevUserId === $duplicate->user_id &&
                    abs(strtotime($prevCreatedAt) - strtotime($duplicate->created_at)) <= 2
                );

                $user = User::find($duplicate->user_id);
                if (!$user) continue;

                $idToRemoveArr = explode(',', $duplicate->id_to_remove);

                if ($isWithin2Sec) {
                    // 🧨 Entire batch is within 2 seconds → delete all + full point deduction
                    $user->decrement('point', $duplicate->total_reward);
                    Recycling::whereIn('id', $idToRemoveArr)->delete();
                } else {
                    // ✅ First valid batch → keep one, delete the rest
                    array_shift($idToRemoveArr); // keep first record
                    if (!empty($idToRemoveArr)) {
                        Recycling::whereIn('id', $idToRemoveArr)->delete();
                    }

                    $user->decrement('point', $duplicate->reward_to_deduct);
                }

                // Remember this batch for the 2-second rule
                $prevUserId = $duplicate->user_id;
                $prevCreatedAt = $duplicate->created_at;
            }
        });

        return response()->json(['message' => 'Duplicate recyclings cleaned successfully']);
    } catch (\Throwable $e) {
        return response()->json([
            'error' => 'Cleanup failed',
            'message' => $e->getMessage(),
        ], 500);
    }
});




// dev purpose only remove soon
Route::get('/fix-event-district-seeder-unmatch', function() {
	$events = \App\Models\Event::with('district')->whereIn('event_type_id', [1,2])->get();
	foreach ($events as $event) {
		$oneMapAPI = App\Helpers\Helper::singaporeOneMapAPI($event->district->name);
		$location = $oneMapAPI->json()['results'][0] ?? [];
		if($location) {
		    $event->address = $location['ADDRESS'];
		    $event->postal_code = $location['POSTAL'];
		    $event->lat = $location['LATITUDE'];
		    $event->long = $location['LONGITUDE'];
		    $event->save();
		}
	}
	dd('test');
	echo 'Events updated';
});
Route::middleware(['web'])->group(function () {
    Route::fallback(function () {
        return Inertia::render('Error', ['status' => 404]);
    });
});

Route::get('/run-migrate-fresh-seed', function () {
    Artisan::call('migrate:fresh');
    Artisan::call('db:seed');
    return 'Migration and seeding completed!';
});
