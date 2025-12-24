<?php

namespace App\Exports;

use App\Models\Reward;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class RewardExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Reward::all();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Code',
            'Name',
            'Price',
            'Label',
            'Image URL',
            'Status',
            'Remaining Vouchers',
            'Created At',
            'Updated At',
            'Description',
            'Terms & Conditions',
        ];
    }

    public function map($reward): array
    {
        return [
            $reward->id,
            $reward->code,
            $reward->name,
            $reward->price,
            $reward->label,
            $reward->image_url,
            $reward->status_text,
            $reward->remaining_vouchers,
            $reward->created_at->format('Y-m-d H:i:s'),
            $reward->updated_at->format('Y-m-d H:i:s'),
            $reward->description,
            $reward->tnc,
        ];
    }
}
