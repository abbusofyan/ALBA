<?php

namespace App\Helpers;
use App\Models\User;
use App\Models\BannedList;

class ApiResponse
{
    public static function success($data = null, $message = 'Success')
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'errors' => null,
        ]);
    }

    public static function error($message = 'Error occurred', $code = 500)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => null,
            'errors' => null,
        ], $code);
    }

	public static function banned(User $user)
    {
		$bannedList = BannedList::where('user_id', $user->id)->latest()->first();
		if ($bannedList->duration_days) {
			$message = 'Your account has been banned for '
				    . $bannedList->duration_days . ' Day(s) from '
				    . date('Y-m-d', strtotime($bannedList->created_at)) . ' due to ' . $bannedList->reason . '. Please contact +65 3105 1608';
		} else {
			$message = 'Your account has been banned permanently due to ' . $bannedList->reason . '. Please contact +65 3105 1608';
		}
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => null,
            'errors' => null,
        ], 401);
    }

    public static function validation($errors, $message = 'The given data was invalid.', $code = 422)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => null,
            'errors' => $errors,
        ], $code);
    }
}
