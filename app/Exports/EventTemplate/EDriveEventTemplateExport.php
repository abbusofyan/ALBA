<?php

namespace App\Exports\EventTemplate;

use App\Models\WasteType;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;

class EDriveEventTemplateExport implements FromArray, WithMultipleSheets, WithEvents, WithTitle
{
    use Exportable;

    protected array $wasteTypes;

    public function __construct()
    {
        $this->wasteTypes = WasteType::pluck('name')->toArray();
    }

    public function sheets(): array
    {
        return [
            $this, // Event Data
            new class($this->wasteTypes) implements FromArray, WithTitle, WithEvents {
                protected array $wasteTypes;

                public function __construct(array $wasteTypes)
                {
                    $this->wasteTypes = $wasteTypes;
                }

                public function array(): array
                {
                    return array_merge(
                        [['Recyclables']], // column header
                        array_map(fn($name) => [$name], $this->wasteTypes)
                    );
                }

                public function title(): string
                {
                    return 'Recyclables';
                }

                public function registerEvents(): array
                {
                    return [
                        AfterSheet::class => function (AfterSheet $event) {
                            // Bold the header row (A1)
                            $event->sheet->getStyle('A1')->applyFromArray([
                                'font' => [
                                    'bold' => true,
                                ],
                            ]);
                        },
                    ];
                }
            }
        ];
    }

    public function array(): array
    {
        return [
            ['ID', 'District', 'Postal Code', 'Start Date', 'Start Time', 'End Date', 'End Time']
        ];
    }

    public function title(): string
    {
        return 'Event Data';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getStyle('A1:G1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                ]);
            },
        ];
    }
}
