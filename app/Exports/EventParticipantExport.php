<?php

namespace App\Exports;

use App\Models\Event;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class EventParticipantExport implements FromCollection, WithHeadings, WithMapping
{

	public $event;

	public function __construct($event) {
		$this->event = $event;
	}

    public function collection()
    {
		return $this->event->participants;
    }

    public function headings(): array
    {
        return [
            'First Name',
            'Last Name',
            'Nickname',
            'Email',
        ];
    }

    public function map($participant): array
    {
        return [
            $participant->first_name,
			$participant->last_name,
			$participant->display_name,
			$participant->email,
        ];
    }
}
