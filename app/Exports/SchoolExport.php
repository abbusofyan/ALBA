<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use App\Models\User;

class SchoolExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    public function collection()
    {
		return User::role('School')->with('secondaryEmail')->get()->map(function($school) {
			$secondaryEmail = '';
			if ($school->secondaryEmail) {
			    $secondaryEmail = implode("\n", array_column($school->secondaryEmail->toArray(), 'email'));
			}
			return [
				'Unique ID' => $school->username,
				'School Name' => $school->name,
				'Email' => $school->email,
				'Secondary Email' => $secondaryEmail,
				'Contact' => $school->phone,
				'Status' => $school->status_text,
				'Address' => $school->address,
				'Postal Code' => $school->postal_code
			];
		});
    }

    public function headings(): array
    {
        return ['Unique ID', 'School name', 'Email', 'Secondary Email', 'Contact', 'Status', 'Address', 'Postal Code'];
    }

	public function styles(Worksheet $sheet)
	{
		return [
			'D' => [
                'alignment' => [
                    'wrapText' => true,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP,
                ],
            ],
		];
	}
}
