<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\BinType;
use App\Models\WasteType;
use Inertia\Inertia;
use App\Http\Requests\StoreBinTypeRequest;
use App\Http\Requests\UpdateBinTypeRequest;

class BinTypeController extends Controller
{
    public function __construct() {
        $this->middleware('permission:view-bin-type')->only(['index', 'show']);
        $this->middleware('permission:create-bin-type')->only(['create', 'store']);
        $this->middleware('permission:update-bin-type')->only(['edit', 'update', 'toggleStatus']);
        $this->middleware('permission:delete-bin-type')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $search = $request->search;
        $paginate = is_numeric($request->paginate) && $request->paginate > 0 ? $request->paginate : 10;
        $sortBy = $request->sort_by ?? 'id';
        $sortOrder = $request->sort_order ?? 'desc';

        $filters = $request->only(['search', 'paginate', 'sort_by', 'sort_order']);

        $binTypes = BinType::when($search, fn($q) => $q->where('name', 'like', "%{$search}%"))
            ->orderBy($sortBy, $sortOrder)
            ->paginate($paginate)
            ->appends($filters);

        return Inertia::render('BinType/Index', [
            'binTypesData' => $binTypes,
            'filters' => $filters,
            'qty_bin_types' => BinType::count(),
        ]);
    }

    public function create()
    {
        return Inertia::render('BinType/Form', [
            'waste_types' => WasteType::all(),
        ]);
    }

    public function store(StoreBinTypeRequest $request)
    {
        $data = $request->only(['name', 'fixed_qrcode', 'point']);

        $data['image'] = $this->uploadFile($request, 'image', 'bin-types');
        $data['icon'] = $this->uploadFile($request, 'icon', 'bin-types/icon');

        $binType = BinType::create($data);

        $binType->wasteTypes()->attach($this->getWasteTypeIds($request->bin_type_waste));

        return redirect('/bin-types')->with(['success' => 'Bin type created successfully']);
    }

    public function show(BinType $binType)
    {
        return Inertia::render('BinType/View', [
            'binType' => $binType->load('wasteTypes'),
        ]);
    }

    public function edit(BinType $binType)
    {
        return Inertia::render('BinType/Form', [
            'binType' => $binType->load('wasteTypes'),
            'waste_types' => WasteType::all(),
        ]);
    }

    public function update(UpdateBinTypeRequest $request, BinType $binType)
    {
        $data = $request->only(['name', 'fixed_qrcode', 'point']);

        if ($request->hasFile('image')) {
            $data['image'] = $this->uploadFile($request, 'image', 'bin-types');
        }

        if ($request->hasFile('icon')) {
            $data['icon'] = $this->uploadFile($request, 'icon', 'bin-types/icon');
        }

        $binType->update($data);
        $binType->wasteTypes()->sync($this->getWasteTypeIds($request->bin_type_waste));

        return redirect()->back()->with(['success' => 'Bin type updated successfully']);
    }

    public function destroy(BinType $binType)
    {
		$binType->load('bins');
		if ($binType->bins) {
			return response()->json([
				'success' => false,
				'message' => 'Deletion failed: This bin type is linked to one or more bins',
			]);
		}
		$binType->delete();

		return response()->json([
			'success' => true,
			'message' => 'Bin type has been deleted successfully',
		]);

    }

    /**
     * Handle file upload.
     */
    private function uploadFile(Request $request, string $field, string $directory): ?string
    {
        if (!$request->hasFile($field)) {
            return null;
        }

        $file = $request->file($field);
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->storeAs("public/images/{$directory}", $filename);

        return $filename;
    }

    /**
     * Get waste type IDs, create if not exists.
     */
    private function getWasteTypeIds(array $wasteTypes): array
    {
        return collect($wasteTypes)
            ->map(fn($name) => WasteType::firstOrCreate(['name' => $name])->id)
            ->toArray();
    }
}
