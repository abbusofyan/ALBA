<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class EventRecyclingExport implements FromCollection, WithHeadings, WithColumnFormatting
{
    protected $event;

    public function __construct($event)
    {
        $this->event = $event;
    }

    public function collection()
    {
        return collect($this->event->recyclings)->map(function ($recycling) {
            return [
                // adjust based on your schema
                'date_time' => $recycling->created_at?->format('Y-m-d H:i:s'),
                'name'      => $recycling->user?->display_name
                                ?? trim(($recycling->user?->first_name ?? '') . ' ' . ($recycling->user?->last_name ?? '')),
                'email'     => $recycling->user?->email,
                'bin_code'  => $recycling->bin?->code,
                'bin_type'  => $recycling->binType?->name,
				'reward' 	=> (int) $recycling->reward,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Date/Time',
            'Name',
            'Email',
            'Bin ID',
            'Bin Type',
            'Reward',
        ];
    }

    public function columnFormats(): array
    {
        return [
            // 'A' => NumberFormat::FORMAT_DATE_DATETIME, // Date/Time column
            'F' => NumberFormat::FORMAT_NUMBER,        // Reward column
        ];
    }
}
