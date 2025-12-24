<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Department;
use App\Models\Permission;
use Inertia\Inertia;
use DB;
use App\Http\Requests\StoreStaffFormRequest;
use App\Http\Requests\UpdateStaffFormRequest;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Hash;
use DateTime;

class StaffController extends Controller
{
	public function __construct()
	{
		$this->middleware('permission:view-staff')->only(['index', 'show']);
		$this->middleware('permission:create-staff')->only(['create', 'store']);
		$this->middleware('permission:update-staff')->only(['edit', 'update', 'toggleStatus']);
		$this->middleware('permission:delete-staff')->only(['destroy']);
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

		$filters = $request->only(['search', 'paginate', 'sort_by', 'sort_order']);

		$staffsData = User::role('Staff')->when($search, function ($q) use ($search) {
		    $q->where('name', 'like', "%{$search}%");
			$q->orWhere('email', 'like', "%{$search}%");
			$q->orWhere('contact', 'like', "%{$search}%");
			$q->orWhere('username', 'like', "%{$search}%");
		})
		->orderBy($sortBy, $sortOrder)
		->paginate($paginate)
		->appends($filters);

		return Inertia::render('Staff/Index', [
		    'staffsData' => $staffsData,
		    'filters' => $filters,
		    'qty_staff' => User::count(),
		]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
		$departments = Department::all();

		$permissions = Permission::all();
		$modulesWithPermissions = $permissions->groupBy(function ($permission) {
		    return $permission->description;
		});

		return Inertia::render('Staff/Form', [
			'departments' => $departments,
			'modules_with_permissions' => $modulesWithPermissions
		]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStaffFormRequest $request)
    {

		$formattedDOB = new DateTime($request->dob);

		DB::beginTransaction();
        try {
	        $user = User::create([
	            'name' => $request->name,
	            'email' => $request->email,
	            'password' => $request->password,
				'status' => $request->status,
	        ]);

			$user->assignRole('Staff');

			foreach ($request->permissions as $modulesWithPermissions) {
				foreach ($modulesWithPermissions as $permission) {
					if ($permission['value']) {
						$user->givePermissionTo($permission['name']);
					}
				}
			}

			DB::commit();
			return redirect('/staffs')->with(['success' => 'New staff created successfully']);
        } catch (\Exception $e) {
        	DB::rollback();
			return redirect()->back()->with(['error' => 'Failed to create a new staff']);
        }

    }

    /**
     * Display the specified resource.
     */
	 public function show(User $staff)
     {

		return Inertia::render('Staff/View', [
			'user' => $staff,
		]);
     }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $staff)
    {
		$staff->getDirectPermissions();

		$permissions = Permission::all();
		$modulesWithPermissions = $permissions->groupBy(function ($permission) {
		    return $permission->description;
		});

		return Inertia::render('Staff/Form', [
			'staff' => $staff,
			'modules_with_permissions' => $modulesWithPermissions
		]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStaffFormRequest $request, User $staff)
    {
		DB::beginTransaction();
		try {

			$data = [
				'name' => $request->name,
				'email' => $request->email,
				'status' => $request->status,
			];
			if ($request->password) {
				$data['password'] = Hash::make($request->password);
			}
			$staff->update($data);

			$permissionsName = array_reduce($request->permissions, function ($carry, $items) {
			    foreach ($items as $item) {
			        if (!empty($item['value']) && isset($item['name'])) {
			            $carry[] = $item['name'];
			        }
			    }
			    return $carry;
			}, []);

			$staff->syncPermissions($permissionsName);
			DB::commit();
			return redirect()->back()->with(['success' => 'Staff updated successfully']);
		} catch (\Exception $e) {
			DB::rollback();
			return redirect()->back()->with(['error' => 'Failed to update staff ' . $e->getMessage()]);
		}

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $staff)
    {
		$staff->delete();
		return response()->json([
			'success' => true,
			'message' => 'Staff has been deleted successfully'
		]);
    }

	public function toggleStatus(Request $request, User $staff) {
		if ($staff->status) {
			$staff->update(['status' => 0]);
			$message = 'Staff has been deactivated';
		} else {
			$staff->update(['status' => 1]);
			$message = 'Staff has been activated';
		}
		return response()->json([
			'success' => true,
			'message' => $message
		]);
	}

}
