<?php

namespace App\Exports;

use App\Models\Recycling;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class RecyclingExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Recycling::with('user', 'bin.type')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'User Name',
            'User Email',
            'Bin Address',
            'Bin Type',
            'Reward',
            'Photo URL',
            'Created At',
        ];
    }

    public function map($recycling): array
    {
        return [
            $recycling->id,
            optional($recycling->user)->name,
            optional($recycling->user)->email,
            optional($recycling->bin)->address,
            optional(optional($recycling->bin)->type)->name,
            $recycling->reward,
            $recycling->photo_url,
            $recycling->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
