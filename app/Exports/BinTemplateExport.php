<?php

namespace App\Exports;

use App\Models\BinType;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BinTemplateExport implements FromArray, WithEvents, WithMultipleSheets
{
    use Exportable;

    protected array $binTypes;

    public function __construct()
    {
        $this->binTypes = BinType::pluck('name')->toArray();
    }

    public function sheets(): array
    {
        return [
            'Sheet1' => $this,
            'Sheet2' => new class($this->binTypes) implements FromArray {
                protected array $binTypes;

                public function __construct(array $binTypes)
                {
                    $this->binTypes = $binTypes;
                }

                public function array(): array
                {
                    return array_map(fn($name) => [$name], $this->binTypes);
                }
            }
        ];
    }

    public function array(): array
    {
        return [
            ['ID', 'Bin Type', 'Remark', 'Postal Code', 'Radius', 'QR Code', 'Address', 'Latitude', 'Longitude', 'Status', 'Is Hidden']
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                /** @var Worksheet $sheet */
                $sheet = $event->sheet->getDelegate();

                // Set dropdown validation for B2:B100 referencing Sheet2 A1:A{count}
                $count = count($this->binTypes);
				$range = "'Worksheet 1'!\$A\$1:\$A\$$count";

                for ($row = 2; $row <= 100; $row++) {
                    $cell = "B$row";
                    $validation = $sheet->getCell($cell)->getDataValidation();
                    $validation->setType(DataValidation::TYPE_LIST);
                    $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
                    $validation->setAllowBlank(true);
                    $validation->setShowInputMessage(true);
                    $validation->setShowErrorMessage(true);
                    $validation->setShowDropDown(true);
                    $validation->setFormula1($range);
                }
            }
        ];
    }
}
