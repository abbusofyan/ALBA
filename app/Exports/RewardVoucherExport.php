<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class RewardVoucherExport implements FromCollection, WithHeadings, WithColumnFormatting
{
    protected $reward;

    public function __construct($reward)
    {
        $this->reward = $reward;
    }

	public function collection()
	{
		$vouchers = $this->reward->vouchers()->with('user')->get()->map(function($voucher) {
			return [
				'Date Created' => $voucher->created_at,
				'Voucher Code' => $voucher->code,
				'Status' => $voucher->status_text,
				'Redeemed By' => $voucher->user?->name,
				'Email' => $voucher->user?->email
			];
		});
		return $vouchers;
	}


    public function headings(): array
    {
        return [
            'Date Created',
            'Voucher Code',
            'Status',
            'Redeemed By',
			'Email'
        ];
    }

	public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT,
            'B' => NumberFormat::FORMAT_TEXT,
            'C' => NumberFormat::FORMAT_TEXT,
			'D' => NumberFormat::FORMAT_TEXT,
        ];
    }
}
