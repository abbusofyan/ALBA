<?php

namespace App\Services;

use App\Models\VerificationCode;
use Carbon\Carbon;

class VerificationService
{
    public static function verifyUserCode($phone, $code)
    {
		$lastCode = VerificationCode::where('phone', $phone)->orderBy('id', 'DESC')->first();

        if (!$lastCode) {
            return [
                'success' => false,
                'message' => 'Phone not found.'
            ];
        }

        if ($lastCode->code !== $code) {
            return [
                'success' => false,
                'message' => 'Verification code is incorrect.'
            ];
        }

        if ($lastCode->expires_at && now()->greaterThan($lastCode->expires_at)) {
            return [
                'success' => false,
                'message' => 'Verification code has expired.'
            ];
        }

		$lastCode->delete();
		
        return [
            'success' => true,
            'message' => 'Verification successful.'
        ];
    }
}
