<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\Bin;

class BinExport implements FromCollection, WithHeadings
{
    public function collection()
    {
		$bins = Bin::with('type')->get()->map(function($bin) {
			return [
				'ID' => $bin->code,
				'Bin Type' => $bin->type->name,
				'Remark' => $bin->remark,
				'Postal Code' => $bin->postal_code,
				'Radius' => $bin->map_radius ? $bin->map_radius . 'm' : '',
				'QR Code' => $bin->qrcode_content,
				'Address' => $bin->address,
				'Latitude' => $bin->lat,
				'Longitude' => $bin->long,
				'Status' => $bin->status_text,
				'Hidden' => $bin->visibility ? 'No' : 'Yes'
			];
		});
		return $bins;
    }

    public function headings(): array
    {
        return ['ID', 'Bin Type', 'Remark', 'Postal Code', 'Radius', 'QR Code', 'Address', 'Latitude', 'Longitude', 'Status', 'Is Hidden'];
    }
}
