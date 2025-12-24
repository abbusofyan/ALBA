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
use App\Http\Requests\StoreSchoolFormRequest;
use App\Http\Requests\UpdateSchoolFormRequest;
use App\Exports\SchoolExport;
use Inertia\Inertia;
use DB;
use DateTime;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as ExcelFormat;

class SchoolController extends Controller
{
	public function __construct()
    {
        $this->middleware('permission:view-school')->only(['index', 'show']);
        $this->middleware('permission:create-school')->only(['create', 'store']);
        $this->middleware('permission:update-school')->only(['edit', 'update', 'toggleStatus']);
        $this->middleware('permission:delete-school')->only(['destroy']);
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

		$schoolsData = User::role('School')
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

		return Inertia::render('School/Index', [
		    'schoolsData' => $schoolsData,
		    'filters' => $filters,
		    'qty_school' => User::count(),
		]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
		$departments = Department::all();
		$uniqueID = User::generateUniqueID('school');
		return Inertia::render('School/Form', [
			'departments' => $departments,
			'unique_id' => $uniqueID
		]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSchoolFormRequest $request)
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

			$user->assignRole('School');

			foreach ($request->secondary_email as $secondary_email) {
				UserSecondaryEmail::create([
					'user_id' => $user->id,
					'email' => $secondary_email
				]);
			}

			DB::commit();
			return redirect('/schools')->with(['success' => 'New school created successfully']);
        } catch (\Exception $e) {
        	DB::rollback();
			return redirect()->back()->with(['error' => 'Failed to create a new school']);
        }

    }

    /**
     * Display the specified resource.
     */
	 public function show(User $school)
     {
		$school->load('secondaryEmail', 'bins.type');
		return Inertia::render('School/View', [
			'user' => $school,
		]);
     }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $school)
    {
		$school->load('secondaryEmail', 'bins.type');
		return Inertia::render('School/Form', [
			'school' => $school,
		]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSchoolFormRequest $request, User $school)
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

			if ($request->status && !$school->activated_at) {
				$data['activated_at'] = now();
				Mail::to($school->email)->send(new RegisterEntityConfirmationMail('school', $school));
			}

			$school->update($data);

			UserSecondaryEmail::where('user_id', $school->id)->delete();

			foreach ($request->secondary_email as $email) {
				UserSecondaryEmail::create([
					'user_id' => $school->id,
					'email' => $email
				]);
			}

			Bin::where('organization_id', $school->id)->update(['organization_id' => null]);
			if ($request->bins) {
				foreach ($request->bins as $bin) {
					Bin::find($bin['id'])->update([
						'organization_id' => $school->id
					]);
				}
			}
			DB::commit();
			return redirect()->back()->with(['success' => 'School updated successfully']);
		} catch (\Exception $e) {
			DB::rollback();
			return redirect()->back()->with(['error' => 'Failed to update school ' . $e->getMessage()]);
		}

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $school)
    {
		$school->delete();
		return response()->json([
			'success' => true,
			'message' => 'School has been deleted successfully'
		]);
    }

	public function toggleStatus(Request $request, User $school) {
		if ($school->status) {
			$school->update(['status' => 0]);
			$message = 'School has been deactivated';
		} else {
			$data['status'] = 1;
			if (!$school->activated_at) {
				$data['activated_at'] = now();
				Mail::to($school->email)->send(new RegisterEntityConfirmationMail('school', $school));
			}
			$school->update($data);
			$message = 'School has been activated';
		}
		return response()->json([
			'success' => true,
			'message' => $message
		]);
	}

	public function export() {
		$filename = 'Export schools data ' . date('Y-m-d') . '.xlsx';
		return Excel::download(new SchoolExport, $filename, ExcelFormat::XLSX);
	}

}
