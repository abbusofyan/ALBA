<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Helpers\ApiResponse;
use App\Http\Requests\StoreRewardRequest;
use App\Models\Reward;
use App\Models\Voucher;
use App\Models\Event;
use App\Imports\VoucherImport;
use Illuminate\Support\Facades\Storage;
use App\Exports\RewardExport;
use App\Exports\RewardVoucherExport;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as ExcelFormat;
use DB;

class RewardController extends Controller
{

	public function __construct()
	{
		$this->middleware('permission:view-reward')->only(['index', 'show']);
		$this->middleware('permission:create-reward')->only(['create', 'store']);
		$this->middleware('permission:update-reward')->only(['edit', 'update', 'toggleStatus']);
		$this->middleware('permission:delete-reward')->only(['destroy']);
	}


	public function index(Request $request)
	{
		$search = $request->search;
		$paginate = is_numeric($request->paginate) && $request->paginate > 0 ? $request->paginate : 10;
		$sortBy = $request->sort_by ?? 'id';
		$sortOrder = $request->sort_order ?? 'desc';
		$status = $request->status ?? 'all';

		$filters = $request->only(['search', 'paginate', 'sort_by', 'sort_order', 'status']);

		$rewards = Reward::withCount([
			'vouchers',
			'vouchers as usage' => function ($query) {
				$query->where('status', 2);
			}
		])->when($search, function ($q) use ($search) {
			$q->where('name', 'like', "%{$search}%");
			$q->orWhere('description', 'like', "%{$search}%");
			$q->orWhere('label', 'like', "%{$search}%");
		})
			->when($status != 'all', function ($q) use ($status) {
				$q->where('status', $status);
			})
			->orderBy($sortBy, $sortOrder)
			->paginate($paginate)
			->appends($filters);

		return Inertia::render('Reward/Index', [
			'rewards' => $rewards,
			'filters' => $filters,
			'qty_reward' => Reward::count(),
		]);
	}

	public function create()
	{
		$events = Event::with('type')->whereIn('event_type_id', [3, 4])->get();
		return Inertia::render('Reward/Form', [
			'events' => $events
		]);
	}

	public function show(Reward $reward)
	{
		$reward->load('event');
		return Inertia::render('Reward/View', [
			'reward' => $reward
		]);
	}

	public function edit(Reward $reward)
	{
		$events = Event::with('type')->whereIn('event_type_id', [3, 4])->get();
		$reward->load('vouchers.user', 'event.type');
		return Inertia::render('Reward/Form', [
			'reward' => $reward,
			'events' => $events
		]);
	}

	public function store(StoreRewardRequest $request)
    {
        DB::beginTransaction();
        try {
            // Handle image upload
            $image = null;
            if ($request->hasFile('image')) {
                $imageFile = $request->file('image');
                $filename = time() . '_' . $imageFile->getClientOriginalName();
                $path = $imageFile->storeAs('public/images/reward', $filename);
                $image = $filename;
            }

            // Prepare reward data
            $data = $request->except('vouchers', 'new_vouchers', 'updated_vouchers', 'deleted_voucher_ids', 'import');
            $data['code'] = Reward::generateCode();
            $data['image'] = $image;

            // Handle date range
            if ($request->date) {
                $dates = explode(' to ', $request->date);
                $dateStart = $dates[0];
                $dateEnd = isset($dates[1]) ? $dates[1] : $dates[0];
                $data['start_date'] = $dateStart;
                $data['end_date'] = $dateEnd;
            }

            // Create the reward
            $reward = Reward::create($data);

            // Handle voucher creation using the same logic as update function
            $this->handleVoucherCreation($request, $reward);

            // Handle voucher import
            if ($request->hasFile('import')) {
                Excel::import(new VoucherImport($reward->id), $request->file('import'));
            }

            DB::commit();
            return redirect('/rewards')->with(['success' => 'Reward created successfully']);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with(['error' => $e->getMessage()]);
        }
    }

    public function update(Request $request, Reward $reward)
    {
        DB::beginTransaction();

        try {
            $data = $request->except('image', 'import', 'vouchers', 'deleted_voucher_ids', 'updated_vouchers', 'new_vouchers');

            // Handle image upload
            if ($request->hasFile('image')) {
                if ($reward->image && Storage::exists('public/images/reward/' . $reward->image)) {
                    Storage::delete('public/images/reward/' . $reward->image);
                }

                $imageFile = $request->file('image');
                $filename = time() . '_' . $imageFile->getClientOriginalName();
                $imageFile->storeAs('public/images/reward', $filename);
                $data['image'] = $filename;
            }

            // Handle date range
            if ($request->date) {
                $dates = explode(' to ', $request->date);
                $dateStart = $dates[0];
                $dateEnd = isset($dates[1]) ? $dates[1] : $dates[0];
                $data['start_date'] = $dateStart;
                $data['end_date'] = $dateEnd;
            }

            // Update reward basic data
            $reward->update($data);

            // Handle voucher changes efficiently
            $this->handleVoucherChanges($request, $reward);

            // Handle voucher import
            if ($request->hasFile('import')) {
                Excel::import(new VoucherImport($reward->id), $request->file('import'));
            }

            DB::commit();
            return redirect('/rewards')->with(['success' => 'Reward updated successfully']);

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with(['error' => $e->getMessage()]);
        }
    }

    /**
     * Handle voucher creation for new rewards
     */
    private function handleVoucherCreation(Request $request, Reward $reward)
    {
        if ($request->has('new_vouchers') && !empty($request->new_vouchers)) {
            // Handle optimized structure - create vouchers from new_vouchers array
            $this->createVouchersFromArray($request->new_vouchers, $reward);
        } elseif ($request->has('vouchers') && !empty($request->vouchers)) {
            // Handle legacy structure - create vouchers from vouchers array
            $this->createVouchersFromArray($request->vouchers, $reward);
        }
    }

    /**
     * Handle voucher changes efficiently based on the optimized frontend data
     */
    private function handleVoucherChanges(Request $request, Reward $reward)
    {
        // Check if we're using the optimized voucher structure
        $isOptimizedRequest = $request->has('deleted_voucher_ids') ||
                             $request->has('updated_vouchers') ||
                             $request->has('new_vouchers');

	 	$this->handleOptimizedVoucherChanges($request, $reward);
    }

    /**
     * Handle voucher changes using the optimized approach
     */
    private function handleOptimizedVoucherChanges(Request $request, Reward $reward)
    {
        // 1. Handle deleted vouchers
        if ($request->has('deleted_voucher_ids') && !empty($request->deleted_voucher_ids)) {
            $reward->vouchers()
                   ->whereIn('id', $request->deleted_voucher_ids)
                   ->delete();
        }

        // 2. Handle updated vouchers
        if ($request->has('updated_vouchers') && !empty($request->updated_vouchers)) {
            foreach ($request->updated_vouchers as $voucherData) {
                if (!empty($voucherData['id'])) {
                    $reward->vouchers()
                           ->where('id', $voucherData['id'])
                           ->update([
                               'code' => $voucherData['code'],
                               'status' => $voucherData['status'] ?? 1,
                           ]);
                }
            }
        }

        // 3. Handle new vouchers
        if ($request->has('new_vouchers') && !empty($request->new_vouchers)) {
            $this->createVouchersFromArray($request->new_vouchers, $reward);
        }
    }

    /**
     * Create vouchers from an array of voucher data
     */
    private function createVouchersFromArray(array $vouchersData, Reward $reward)
    {
        foreach ($vouchersData as $voucherData) {
            $this->createSingleVoucher($voucherData, $reward);
        }
    }

    /**
     * Create a single voucher with duplicate checking
     */
    private function createSingleVoucher(array $voucherData, Reward $reward)
    {
        // Skip empty codes
        if (empty($voucherData['code'])) {
            return;
        }

        // Avoid inserting duplicates
        // $existing = $reward->vouchers()
        //                   ->where('code', $voucherData['code'])
        //                   ->first();

        // if (!$existing) {
            $reward->vouchers()->create([
                'code' => $voucherData['code'],
                'status' => $voucherData['status'] ?? 1,
            ]);
        // }
    }

	public function destroy(Reward $reward)
	{
		if ($reward->image && Storage::exists('public/images/reward/' . $reward->image)) {
			Storage::delete('public/images/reward/' . $reward->image);
		}
		$reward->vouchers()->delete();
		$reward->delete();

		return redirect()->route('rewards.index')->with('success', 'Reward deleted successfully');
	}

	public function vouchers(Request $request, Reward $reward)
	{
		$search = $request->search;
		$paginate = is_numeric($request->paginate) && $request->paginate > 0 ? $request->paginate : 10;
		$sortBy = $request->sort_by ?? 'id';
		$sortOrder = $request->sort_order ?? 'desc';

		$vouchers = $reward->vouchers()->with('user')
			->when($search, function ($q) use ($search) {
				$q->where('code', 'like', "%{$search}%")
					->orWhereHas('user', function($uq) use($search) {
						$uq->where('first_name', 'like', "%{$search}%")
							->orWhere('last_name', 'like', "%{$search}%")
							->orWhere('email', 'like', "%{$search}%")
							->orWhere('phone', 'like', "%{$search}%")
							->orWhere(\DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', "%{$search}%");
					});
			})
			->orderBy($sortBy, $sortOrder)
			->paginate($paginate);

		return response()->json($vouchers);
	}

	public function export()
	{
		return Excel::download(new RewardExport, 'Export reward data ' . date('Y-m-d') . '.xlsx', ExcelFormat::XLSX);
	}

	public function toggleStatus(Request $request, Reward $reward)
	{
		if ($reward->status) {
			$reward->update(['status' => 0]);
			$message = 'Reward has been deactivated';
		} else {
			$reward->update(['status' => 1]);
			$message = 'Reward has been activated';
		}
		return response()->json([
			'success' => true,
			'message' => $message
		]);
	}

	public function downloadVouchers(Reward $reward)
	{
		$reward->load('vouchers');
		return Excel::download(new RewardVoucherExport($reward), $reward->name . ' ' . $reward->code . ' Voucher Codes.xlsx', ExcelFormat::XLSX);
	}

}
