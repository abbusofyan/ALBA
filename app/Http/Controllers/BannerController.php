<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Banner;
use App\Models\BannerPlacement;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\StoreBannerRequest;
use App\Http\Requests\UpdateBannerRequest;

class BannerController extends Controller
{

	public function __construct()
	{
		$this->middleware('permission:view-banner')->only(['index', 'show']);
		$this->middleware('permission:create-banner')->only(['create', 'store']);
		$this->middleware('permission:update-banner')->only(['edit', 'update', 'toggleStatus']);
	}

	public function index(Request $request)
	{
		$search = $request->search;
		$paginate = is_numeric($request->paginate) && $request->paginate > 0 ? $request->paginate : 10;
		$sortBy = $request->sort_by ?? 'id';
		$sortOrder = $request->sort_order ?? 'asc';
		$eventType = $request->event_type ?? 'all';
		$status = $request->status ?? 'all';

		$filters = $request->only(['search', 'paginate', 'sort_by', 'sort_order', 'status']);

		$bannerData = BannerPlacement::with('banners')->when($search, function ($q) use ($search) {
		        $q->whereHas('banner', function ($query) use ($search) {
		            $query->where('url', 'like', "%{$search}%");
		        });
		    })
			->when($status != 'all', function ($q) use ($status) {
				$q->whereHas('banner', function ($query) use ($status) {
					$q->where('status', $status);
		        });
			})
			->orderBy($sortBy, $sortOrder)
			->paginate($paginate)
			->appends($filters);

		return Inertia::render('Banner/Index', [
			'bannerData' => $bannerData,
			'filters' => $filters,
			'qty_banner' => Banner::count(),
		]);
	}

	public function create()
	{
		$placements = BannerPlacement::all();
		return Inertia::render('Banner/Form', [
			'placements' => $placements
		]);
	}

	public function edit(BannerPlacement $banner)
	{
		$banner->load('banners');
		$placements = BannerPlacement::all();
		return Inertia::render('Banner/Form', [
			'banner' => $banner,
			'placements' => $placements
		]);
	}

	public function store(StoreBannerRequest $request)
	{
		DB::beginTransaction();
		try {
			foreach ($request->banners as $key => $banner) {
				if ($banner['image']) {

					Banner::where('placement_id', $banner['placement_id'])->delete();

					$imageFile = $banner['image'];
					$filename = time() . '_' . $imageFile->getClientOriginalName();
					$path = $imageFile->storeAs('public/images/banners', $filename);
					$image = $filename;

					Banner::create([
						'image' => $image,
						'placement_id' => $banner['placement_id'],
						'url' => $banner['url'],
						'status' => $banner['status']
					]);

				}
			}

			DB::commit();
			return redirect('/banners')->with(['success' => 'Banner Updated']);
		} catch (\Exception $e) {
			DB::rollback();
			return redirect()->back()->with(['error' => $e->getMessage()]);
		}
	}

	public function update(BannerPlacement $bannerPlacement, UpdateBannerRequest $request)
	{
	    DB::beginTransaction();
	    try {
			$bannerPlacement->update(['status' => $request->status]);

	        $incomingIds = collect($request->banners)
	            ->pluck('id')
	            ->filter()
	            ->toArray();

	        Banner::where('placement_id', $bannerPlacement->id)
	            ->whereNotIn('id', $incomingIds)
	            ->delete();

	        foreach ($request->banners as $banner) {
	            $data = [
	                'placement_id' => $bannerPlacement->id,
	                'url' => $banner['url'],
	                'status' => 1,
	            ];

	            if (!is_string($banner['image'])) {
	                $imageFile = $banner['image'];
	                $filename = time() . '_' . $imageFile->getClientOriginalName();
	                $imageFile->storeAs('public/images/banners', $filename);
	                $data['image'] = $filename;
	            }

	            if (!empty($banner['id'])) {
	                Banner::where('id', $banner['id'])->update($data);
	            } else {
	                Banner::create($data);
	            }
	        }

	        DB::commit();
	        return redirect('/banners')->with(['success' => 'Banner updated successfully']);
	    } catch (\Exception $e) {
	        DB::rollback();
	        return redirect()->back()->with(['error' => $e->getMessage()]);
	    }
	}


	public function toggleStatus(Request $request, BannerPlacement $bannerPlacement)
	{
		if ($bannerPlacement->status) {
			$bannerPlacement->update(['status' => 0]);
			$message = 'Banner has been deactivated';
		} else {
			$bannerPlacement->update(['status' => 1]);
			$message = 'Banner has been activated';
		}
		return response()->json([
			'success' => true,
			'message' => $message
		]);
	}
}
