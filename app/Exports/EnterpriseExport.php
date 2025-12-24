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

class EnterpriseExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    public function collection()
    {
		return User::role('Enterprise')->with('secondaryEmail')->get()->map(function($enterprise) {
			$secondaryEmail = '';
			if ($enterprise->secondaryEmail) {
				$secondaryEmail = implode("\n", array_column($enterprise->secondaryEmail->toArray(), 'email'));
			}
			return [
				'Unique ID' => $enterprise->username,
				'Enterprise Name' => $enterprise->name,
				'Email' => $enterprise->email,
				'Secondary Email' => $secondaryEmail,
				'Contact' => $enterprise->phone,
				'Status' => $enterprise->status_text,
				'Address' => $enterprise->address,
				'Postal Code' => $enterprise->postal_code
			];
		});
    }

    public function headings(): array
    {
        return ['Unique ID', 'Enterprise name', 'Email', 'Contact', 'Status', 'Address', 'Postal Code'];
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
