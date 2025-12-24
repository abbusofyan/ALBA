<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\Department;
use App\Models\Permission;
use App\Models\UserSecondaryEmail;
use App\Models\Bin;
use App\Mail\RegisterEntityConfirmationMail;
use App\Http\Requests\StoreEnterpriseFormRequest;
use App\Http\Requests\UpdateEnterpriseFormRequest;
use Inertia\Inertia;
use DB;
use DateTime;
use App\Exports\EnterpriseExport;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as ExcelFormat;

class EnterpriseController extends Controller
{

	public function __construct()
    {
        $this->middleware('permission:view-enterprise')->only(['index', 'show']);
        $this->middleware('permission:create-enterprise')->only(['create', 'store']);
        $this->middleware('permission:update-enterprise')->only(['edit', 'update', 'toggleStatus']);
        $this->middleware('permission:delete-enterprise')->only(['destroy']);
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
		$status = $request->status ?? 'all';

		$filters = $request->only(['search', 'paginate', 'sort_by', 'sort_order', 'status']);

		$enterprisesData = User::role('Enterprise')
		    ->when($search, function ($q) use ($search) {
		        $q->where(function($query) use ($search) {
		            $query->where('name', 'like', "%{$search}%")
		                  ->orWhere('email', 'like', "%{$search}%")
		                  ->orWhere('phone', 'like', "%{$search}%")
		                  ->orWhere('username', 'like', "%{$search}%");
		        });
		    })
		    ->when($status != 'all', function($q) use($status) {
		        $q->where('status', $status);
		    })
		    ->orderBy($sortBy, $sortOrder)
		    ->paginate($paginate)
		    ->appends($filters);

		return Inertia::render('Enterprise/Index', [
		    'enterprisesData' => $enterprisesData,
		    'filters' => $filters,
		    'qty_enterprise' => User::count(),
		]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
		$departments = Department::all();
		$uniqueID = User::generateUniqueID('enterprise');
		return Inertia::render('Enterprise/Form', [
			'departments' => $departments,
			'unique_id' => $uniqueID
		]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEnterpriseFormRequest $request)
    {
		DB::beginTransaction();
        try {
	        $user = User::create([
	            'name' => $request->name,
	            'username' => $request->username,
	            'email' => $request->email,
	            'password' => $request->password,
				'phone' => $request->phone,
				'status' => $request->status,
				'address' => $request->address,
				'postal_code' => $request->postal_code
	        ]);

			$user->assignRole('Enterprise');

			foreach ($request->secondary_email as $secondary_email) {
				UserSecondaryEmail::create([
					'user_id' => $user->id,
					'email' => $secondary_email
				]);
			}

			DB::commit();
			return redirect('/enterprises')->with(['success' => 'New enterprise created successfully']);
        } catch (\Exception $e) {
        	DB::rollback();
			return redirect()->back()->with(['error' => 'Failed to create a new enterprise']);
        }

    }

    /**
     * Display the specified resource.
     */
	 public function show(User $enterprise)
     {
		$enterprise->load('secondaryEmail', 'bins.type');
		return Inertia::render('Enterprise/View', [
			'user' => $enterprise,
		]);
     }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $enterprise)
    {
		$enterprise->load('secondaryEmail', 'bins.type');
		return Inertia::render('Enterprise/Form', [
			'enterprise' => $enterprise,
		]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEnterpriseFormRequest $request, User $enterprise)
    {
		DB::beginTransaction();
		try {
			$data = [
				'name' => $request->name,
				'username' => $request->username,
				'email' => $request->email,
				'phone' => $request->phone,
				'status' => $request->status,
				'address' => $request->address,
				'postal_code' => $request->postal_code,
				'can_order_pickup' => $request->can_order_pickup
			];
			if ($request->password) {
				$data['password'] = Hash::make($request->password);
			}

			if ($request->status && !$enterprise->activated_at) {
				$data['activated_at'] = now();
				Mail::to($enterprise->email)->send(new RegisterEntityConfirmationMail('enterprise', $enterprise));
			}

			$enterprise->update($data);

			UserSecondaryEmail::where('user_id', $enterprise->id)->delete();

			foreach ($request->secondary_email as $email) {
				UserSecondaryEmail::create([
					'user_id' => $enterprise->id,
					'email' => $email
				]);
			}

			Bin::where('organization_id', $enterprise->id)->update(['organization_id' => null]);
			if ($request->bins) {
				foreach ($request->bins as $bin) {
					Bin::find($bin['id'])->update([
						'organization_id' => $enterprise->id
					]);
				}
			}

			DB::commit();
			return redirect()->back()->with(['success' => 'Enterprise updated successfully']);
		} catch (\Exception $e) {
			DB::rollback();
			return redirect()->back()->with(['error' => 'Failed to update enterprise ' . $e->getMessage()]);
		}

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $enterprise)
    {
		$enterprise->delete();
		return response()->json([
			'success' => true,
			'message' => 'Enterprise has been deleted successfully'
		]);
    }

	public function toggleStatus(Request $request, User $enterprise) {
		if ($enterprise->status) {
			$enterprise->update(['status' => 0]);
			$message = 'Enterprise has been deactivated';
		} else {
			$data['status'] = 1;
			if (!$enterprise->activated_at) {
				$data['activated_at'] = now();
				Mail::to($enterprise->email)->send(new RegisterEntityConfirmationMail('enterprise', $enterprise));
			}
			$enterprise->update($data);
			$message = 'Enterprise has been activated';
		}
		return response()->json([
			'success' => true,
			'message' => $message
		]);
	}

	public function export() {
		$filename = 'Export enterprises data ' . date('Y-m-d') . '.xlsx';
		return Excel::download(new EnterpriseExport, $filename, ExcelFormat::XLSX);
	}

}
