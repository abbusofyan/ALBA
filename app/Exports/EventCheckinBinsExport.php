<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class EventCheckinBinsExport implements FromCollection, WithHeadings, WithColumnFormatting
{
    protected $event;

    public function __construct($event)
    {
        $this->event = $event;
    }

	public function collection()
	{
	    $query = \DB::table('event_recycling_logs as erl')
	        ->join('recyclings as r', 'erl.recycling_id', '=', 'r.id')
	        ->join('users as u', 'r.user_id', '=', 'u.id')
	        ->join('bin_types as bt', 'r.bin_type_id', '=', 'bt.id')
	        ->leftJoin('bins as b', 'r.bin_id', '=', 'b.id')
	        ->select(
	            \DB::raw("CASE
	                WHEN u.name IS NULL OR u.name = ''
	                THEN CONCAT(u.first_name, ' ', u.last_name)
	                ELSE u.name
	            END as user_name"),
	            'u.email',
	            'u.phone',

	            \DB::raw("COUNT(DISTINCT COALESCE(b.code, CONCAT('deleted_', bt.id))) as total_unique_bins"),

	            \DB::raw("GROUP_CONCAT(
	                DISTINCT CASE
	                    WHEN b.code IS NULL THEN CONCAT('deleted_bin_', bt.id)  -- keep uniqueness internally
	                    ELSE b.code
	                END
	                ORDER BY
	                    CASE WHEN b.code IS NULL THEN 1 ELSE 0 END,  -- real bins first
	                    b.code ASC,
	                    bt.id ASC
	                SEPARATOR ';'
	            ) as raw_bin_breakdown")
	        )
	        ->where('erl.event_id', $this->event->id)
	        ->groupBy('u.id', 'u.name', 'u.first_name', 'u.last_name', 'u.email', 'u.phone');

	    $data = $query->get();

	    foreach ($data as $row) {
	        $row->bin_breakdown = preg_replace('/deleted_bin_\d+/', 'deleted_bin', $row->raw_bin_breakdown);
	        unset($row->raw_bin_breakdown);
	    }

	    return $data;
	}


    public function headings(): array
    {
        return [
            'User Name',
            'Email',
            'Phone',
            'Total Unique Bin Scanned',
			'Bin Breakdown'
        ];
    }

	public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT,
            'B' => NumberFormat::FORMAT_TEXT,
            'C' => NumberFormat::FORMAT_TEXT,
			'D' => NumberFormat::FORMAT_TEXT,
			'E' => NumberFormat::FORMAT_TEXT,
        ];
    }
}
