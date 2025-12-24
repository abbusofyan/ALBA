<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bin;
use App\Models\BinType;
use App\Models\EWasteBinType;
use App\Models\WasteType;
use App\Models\User;
use Inertia\Inertia;
use App\Http\Requests\StoreBinFormRequest;
use App\Http\Requests\UpdateBinFormRequest;
use App\Helpers\ApiResponse;
use App\Services\BinService;
use App\Exports\BinExport;
use App\Exports\BinTemplateExport;
use App\Imports\BinImport;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as ExcelFormat;

class BinController extends Controller
{
	public function __construct()
	{
		$this->middleware('permission:view-bin')->only(['index', 'show']);
		$this->middleware('permission:create-bin')->only(['create', 'store']);
		$this->middleware('permission:update-bin')->only(['edit', 'update', 'toggleStatus']);
		$this->middleware('permission:delete-bin')->only(['destroy']);
	}

	/**
	 * Display a listing of the resource.
	 */
	public function index(Request $request)
	{
		$search = $request->search;
		$paginate = is_numeric($request->paginate) && $request->paginate > 0 ? $request->paginate : 10;
		$sortBy = $request->sort_by ?? 'id';
		$sortOrder = $request->sort_order ?? 'desc';
		$binType = $request->bin_type ?? 'all';
		$status = $request->status ?? 'all';
		$filters = $request->only(['search', 'paginate', 'sort_by', 'sort_order', 'bin_type', 'status']);

		$binsData = [];

		if ($binType) {
			$binsData = Bin::with('type')
			->when($search, function ($q) use ($search) {
				$search = ltrim($search, '#');
				$q->where(function ($sub) use ($search) {
					$sub->where('address', 'like', "%{$search}%")
					->orWhere('code', 'like', "%{$search}%")
					->orWhereRaw("LPAD(id, 8, '0') LIKE ?", ["%{$search}%"]);
				});
			})
			->when($binType != 'all', function ($q) use ($binType) {
				$q->whereIn('bin_type_id', $binType);
			})
			->when($status != 'all', function ($q) use ($status) {
				$q->where('status', $status);
			})
			->orderBy($sortBy, $sortOrder)
			->paginate($paginate)
			->appends($filters);
		}

		return Inertia::render('Bin/Index', [
			'binsData' => $binsData,
			'filters' => $filters,
			'qty_bin' => Bin::count(),
			'bin_types' => BinType::all()
		]);
	}

	/**
	 * Show the form for creating a new resource.
	 */
	public function create()
	{
		$organizations = User::role(['School', 'Enterprise'])->with('roles')->get();
		return Inertia::render('Bin/Form', [
			'organizations' => $organizations,
			'bin_types' => BinType::with('wasteTypes')->get(),
		]);
	}

	/**
	 * Store a newly created resource in storage.
	 */
	public function store(StoreBinFormRequest $request)
	{
		$binWasteTypeIds = [];
		$code = $request->code;
		if ($request->bin_type_id != 1) {
			$code = Bin::generateCode();
		}
		$bin = Bin::create([
			'code' => $code,
			'bin_type_id' => $request->bin_type_id,
			'address' => $request->address,
			'postal_code' => $request->postal_code,
			'map_radius' => $request->map_radius,
			'lat' => $request->lat,
			'long' => $request->long,
			'remark' => $request->remark,
			'status' => $request->status
		]);

		return redirect('/bins')->with(['created' => 'New bin created successfully']);
	}

	public function recyclings(Request $request, Bin $bin)
	{
		$search = $request->search;
		$paginate = is_numeric($request->paginate) && $request->paginate > 0 ? $request->paginate : 10;
		$sortBy = $request->sort_by ?? 'recyclings.id';
		$sortOrder = $request->sort_order ?? 'desc';

		$recyclings = $bin->recyclings()
			->select('recyclings.*') // important for avoiding ambiguous columns
			->with(['user', 'bin', 'binType'])
			->leftJoin('users', 'users.id', '=', 'recyclings.user_id')
			// ->leftJoin('bins', 'bins.id', '=', 'recyclings.bin_id')
			->when($search, function ($q) use ($search) {
				$q->where(function ($query) use ($search) {
					$query->whereHas('user', function ($uq) use ($search) {
						$uq->where('first_name', 'like', "%{$search}%")
							->orWhere('last_name', 'like', "%{$search}%")
							->orWhere(\DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', "%{$search}%")
						    ->orWhere('email', 'like', "%{$search}%");
					})
					->orWhereHas('bin', function ($bq) use ($search) {
						$bq->where('code', 'like', "%{$search}%")
						   ->orWhere('address', 'like', "%{$search}%");
					});
				});
			})
			->when($sortBy, function ($q) use ($sortBy, $sortOrder) {
				switch ($sortBy) {
					case 'user':
						$q->orderBy('users.first_name', $sortOrder);
						break;
					case 'created_at':
						$q->orderBy('recyclings.created_at', $sortOrder);
						break;
					case 'reward':
						$q->orderBy('recyclings.reward', $sortOrder);
						break;
					default:
						$q->orderBy('recyclings.id', $sortOrder);
				}
			})
			->paginate($paginate);

		return response()->json($recyclings);
	}

	/**
	 * Display the specified resource.
	 */
	public function show(Bin $bin)
	{
		$bin->load('type.wasteTypes', 'organization');
		return Inertia::render('Bin/View', [
			'bin' => $bin,
		]);
	}

	/**
	 * Show the form for editing the specified resource.
	 */
	public function edit(Bin $bin)
	{
		$organizations = User::role(['School', 'Enterprise'])->with('roles')->get();
		$bin->load('type.wasteTypes');
		return Inertia::render('Bin/Form', [
			'bin' => $bin,
			'organizations' => $organizations,
			'bin_types' => BinType::with('wasteTypes')->get(),
		]);
	}

	/**
	 * Update the specified resource in storage.
	 */
	public function update(UpdateBinFormRequest $request, Bin $bin)
	{
		$bin->update($request->all());
		return redirect('/bins')->with(['created' => 'Bin updated successfully']);
	}

	/**
	 * Remove the specified resource from storage.
	 */
	public function destroy(Bin $bin)
	{
		$bin->delete();
		return response()->json([
			'success' => true,
			'message' => 'Bin has been deleted successfully'
		]);
	}

	public function toggleStatus(Request $request, Bin $bin)
	{
		if ($bin->status) {
			$bin->update(['status' => 0]);
			$message = 'Bin has been deactivated';
		} else {
			$bin->update(['status' => 1]);
			$message = 'Bin has been activated';
		}
		return response()->json([
			'success' => true,
			'message' => $message
		]);
	}

	public function toggleVisibility(Request $request, Bin $bin)
	{
		if ($bin->visibility) {
			$bin->update(['visibility' => 0]);
			$message = 'Bin is hidden';
		} else {
			$bin->update(['visibility' => 1]);
			$message = 'Bin is shown';
		}
		return response()->json([
			'success' => true,
			'message' => $message
		]);
	}

	public function getMapLocation(Request $request)
	{
		try {
			// if (!$request->bin_type_id) {
			// 	return ApiResponse::success(['locations' => []], 'Bin locations retrieved successfully');
			// }
			$binLocations = BinService::get($request->all());
			return ApiResponse::success(['locations' => $binLocations], 'Bin locations retrieved successfully');
		} catch (\Exception $e) {
			return ApiResponse::error($e->getMessage(), 500);
		}
	}

	public function downloadTemplate()
	{
		return (new BinTemplateExport)->download('Import Bin Template.xlsx', ExcelFormat::XLSX);
	}

	public function export()
	{
		return Excel::download(new BinExport, 'Bin data ' . date('Y-m-d') . '.xlsx', ExcelFormat::XLSX);
	}

	public function import(Request $request)
	{
		$request->validate([
			'file' => 'required|mimes:xlsx,xls,csv|max:2048',
		]);

		try {
			Excel::import(new BinImport, $request->file('file'));
			return response()->json(['message' => 'File imported successfully.']);
		} catch (\Exception $e) {
			return response()->json(['error' => $e->getMessage()], 500);
		}
	}

	public function fetchDetailByIds(Request $request) {
		$ids = $request->ids;
	    $bins = Bin::with('type')->whereIn('id', $ids)->get();
	    return response()->json($bins);
	}
}
