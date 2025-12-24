<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Voucher;
use App\Helpers\ApiResponse;

class VoucherController extends Controller
{
    public function detail($voucherId)
    {
        $voucher = Voucher::with('reward')->find($voucherId);

        if (!$voucher) {
            return ApiResponse::error('Voucher not found', 404);
        }

        $data = [
            'id' => $voucher->id,
            'code' => $voucher->code,
            'status' => $voucher->status,
            'reward' => [
                'id' => $voucher->reward->id,
                'name' => $voucher->reward->name,
                'label' => $voucher->reward->label,
                'price' => $voucher->reward->price,
                'description' => $voucher->reward->description,
                'image' => $voucher->reward->image,
                'image_url' => $voucher->reward->image_url,
                'status' => $voucher->reward->status,
                'status_text' => $voucher->reward->status_text,
                'is_expired' => $voucher->reward->is_expired
            ]
        ];

        return ApiResponse::success(['voucher' => $data], 'Voucher detail retrieved successfully.');
    }
}
