<?php

namespace App\Http\Controllers;

use App\Exports\RecyclingExport;
use Illuminate\Http\Request;
use App\Models\Recycling;
use App\Models\BinType;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as ExcelFormat;
use Illuminate\Http\Response;

class RecyclingController extends Controller
{

	public function __construct()
	{
		$this->middleware('permission:view-recycling')->only(['index', 'show']);
	}

	public function index(Request $request)
	{
		$search = $request->search;
		$paginate = is_numeric($request->paginate) && $request->paginate > 0 ? $request->paginate : 10;
		$sortBy = $request->sort_by ?? 'id';
		$sortOrder = $request->sort_order ?? 'desc';
		$binType = $request->bin_type ?? 'all';
		$status = $request->status ?? 'all';

		$filters = $request->only(['search', 'paginate', 'sort_by', 'sort_order', 'bin_type', 'status']);

		$recyclingsData = Recycling::with('user', 'bin', 'binType')
			->whereHas('user')
			->when($search, function ($q) use ($search) {
				$q->where(function ($query) use ($search) {
					$query->whereHas('bin', function ($binQuery) use ($search) {
						$binQuery->where('address', 'like', "%{$search}%");
					})->orWhereHas('bin.type', function ($typeQuery) use ($search) {
						$typeQuery->where('name', 'like', "%{$search}%");
					})->orWhereHas('user', function ($typeQuery) use ($search) {
						$typeQuery->where('name', 'like', "%{$search}%")
							->orWhere('first_name', 'like', "%{$search}%")
							->orWhere('last_name', 'like', "%{$search}%")
							->orWhere('email', 'like', "%{$search}%")
							->orWhere(\DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', "%{$search}%")
							->orWhere('phone', 'like', "%{$search}%");
					});;
				});
			})
			->when($binType != 'all', function ($q) use ($binType) {
				$q->whereHas('bin.type', function ($query) use ($binType) {
					$query->where('id', $binType);
				});
			})
			->orderBy($sortBy, $sortOrder)
			->paginate($paginate)
			->appends($filters);

		// $statsData = $this->getStatsData();
		$chartData = $this->getChartData();

		return Inertia::render('Recycling/Index', [
			'recyclingsData' => $recyclingsData,
			'filters' => $filters,
			'qty_recyclings' => Recycling::count(),
			'bin_types' => BinType::all(),
			// 'stats' => $statsData,
			'chart_data' => $chartData
		]);
	}

	public function export()
	{
		return Excel::download(new RecyclingExport, 'Export recycling data ' . date('Y-m-d') . '.xlsx', ExcelFormat::XLSX);
	}

	public function exportStream()
    {
        $fileName = 'recycling_data_' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        $callback = function () {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'ID',
                'User Name',
                'User Email',
                'Bin Address',
                'Bin Type',
                'Reward',
                'Photo URL',
                'Created At',
            ]);

            // Fetch data in chunks (no memory overload)
            Recycling::with(['user', 'bin.type'])
                ->orderBy('id')
                ->chunk(1000, function ($rows) use ($handle) {
                    foreach ($rows as $recycling) {
                        fputcsv($handle, [
                            $recycling->id,
                            optional($recycling->user)->name,
                            optional($recycling->user)->email,
                            optional($recycling->bin)->address,
                            optional(optional($recycling->bin)->type)->name,
                            $recycling->reward,
                            $recycling->photo_url,
                            $recycling->created_at->format('Y-m-d H:i:s'),
                        ]);
                    }
                });

            fclose($handle);
        };

        return response()->stream($callback, Response::HTTP_OK, $headers);
    }

	public function show(Recycling $recycling)
	{
		$recycling->load('user', 'bin.type', 'binType');
		return Inertia::render('Recycling/View', [
			'recycling' => $recycling,
		]);
	}

	private function getStatsData()
	{
	    $startOfWeek = Carbon::now()->startOfWeek()->toDateTimeString();
	    $endOfWeek = Carbon::now()->endOfWeek()->toDateTimeString();

	    $data = DB::table('bin_types')
	        ->leftJoin('recyclings', function ($join) use ($startOfWeek, $endOfWeek) {
	            $join->on('bin_types.id', '=', 'recyclings.bin_type_id')
	                 ->whereBetween('recyclings.created_at', [$startOfWeek, $endOfWeek]);
	        })
	        ->leftJoin('users', 'recyclings.user_id', '=', 'users.id') // Join with users table
	        ->whereNull('users.deleted_at') // Exclude soft-deleted users
	        ->select('bin_types.name as bin_type_name', DB::raw('COUNT(recyclings.id) as total'))
	        ->groupBy('bin_types.name')
	        ->pluck('total', 'bin_type_name')
	        ->toArray();
	    return $data;
	}

	public function getChartData()
	{
	    $startDate = Carbon::now()->startOfWeek();
	    $endDate = Carbon::now()->endOfWeek();

	    $rawData = DB::table('recyclings')
	        ->join('bin_types', 'recyclings.bin_type_id', '=', 'bin_types.id')
	        ->select(
	            DB::raw('DATE(recyclings.created_at) as date'),
	            'bin_types.name as bin_type',
	            DB::raw('COUNT(*) as total_recyclings')
	        )
	        ->whereBetween('recyclings.created_at', [$startDate, $endDate])
	        ->groupBy(DB::raw('DATE(recyclings.created_at)'), 'bin_types.name')
	        ->orderBy('date')
	        ->get();

	    // Build date range (Mon - Sun)
	    $dates = collect();
	    for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
	        $dates->push($date->format('Y-m-d'));
	    }

	    // Get all bin types involved
	    $binTypes = $rawData->pluck('bin_type')->unique();

	    // Fill missing dates with 0 totals for each bin type
	    $filledData = [];

	    foreach ($binTypes as $binType) {
	        // Get existing entries for this bin type
	        $entries = $rawData->where('bin_type', $binType)->keyBy('date');

	        foreach ($dates as $date) {
	            $filledData[] = [
	                'date' => $date,
	                'bin_type' => $binType,
	                'total_recyclings' => isset($entries[$date]) ? $entries[$date]->total_recyclings : 0,
	            ];
	        }
	    }

	    return $filledData;
	}


}
