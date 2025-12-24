<?php

namespace App\Exports;

use App\Models\Event;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class EventExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Event::with('type', 'district', 'user.roles')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Secret Code',
            'Event Type',
            'District',
            'School Name',
            'Organization Name',
            'Event Name',
            'Address',
            'Postal Code',
            'Date Start',
            'Date End',
            'Time Start',
            'Time End',
            'Description',
        ];
    }

    public function map($event): array
    {
        $schoolName = null;
        $enterpriseName = null;

        if ($event->user) {
            if ($event->user->roles->contains('name', 'School')) {
                $schoolName = $event->user->name;
            }
            if ($event->user->roles->contains('name', 'Enterprise')) {
                $enterpriseName = $event->user->name;
            }
        }

        return [
            $event->code,
            $event->secret_code,
            optional($event->type)->name,
            optional($event->district)->name,
            $schoolName,
            $enterpriseName,
            $event->name,
            $event->address,
            $event->postal_code,
            $event->date_start,
            $event->date_end,
			date("H:i A", strtotime($event->time_start)),
			date("H:i A", strtotime($event->time_end)),
            $event->description,
        ];
    }
}
