<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Department;
use App\Models\Permission;
use App\Models\Recycling;
use App\Models\BannedList;
use App\Http\Requests\StoreUserFormRequest;
use App\Http\Requests\UpdateUserFormRequest;
use App\Helpers\ApiResponse;
use Inertia\Inertia;
use DateTime;
use DB;
use App\Exports\UserExport;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as ExcelFormat;

class UserController extends Controller
{
	public function __construct()
	{
		$this->middleware('permission:view-user')->only(['index', 'show']);
		$this->middleware('permission:create-user')->only(['create', 'store']);
		$this->middleware('permission:update-user')->only(['edit', 'update', 'toggleStatus']);
		$this->middleware('permission:delete-user')->only(['destroy']);
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

		$usersData = User::role('Public')
		    ->when($search, function ($q) use ($search) {
		        $q->where(function($query) use ($search) {
		            $query->where('name', 'like', "%{$search}%")
		                  ->orWhere('first_name', 'like', "%{$search}%")
						  ->orWhere('last_name', 'like', "%{$search}%")
						  ->orWhere('email', 'like', "%{$search}%")
		                  ->orWhere('phone', 'like', "%{$search}%")
		                  ->orWhere('username', 'like', "%{$search}%")
						  ->orWhere(\DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', "%{$search}%");
		        });
		    })
		    ->when($status != 'all', function($q) use($status) {
		        $q->where('status', $status);
		    })
		    ->orderBy($sortBy, $sortOrder)
		    ->paginate($paginate)
		    ->appends($filters);

		return Inertia::render('User/Index', [
		    'usersData' => $usersData,
		    'filters' => $filters,
		    'qty_user' => User::count(),
		]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

		return Inertia::render('User/Form');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserFormRequest $request)
    {

		$formattedDOB = new DateTime($request->dob);

		DB::beginTransaction();
        try {
	        $user = User::create([
	            'name' => $request->name,
	            'username' => $request->username,
	            'email' => $request->email,
	            'password' => $request->password,
				'phone' => $request->phone,
				'dob' => $request->dob ? $formattedDOB->format('Y-m-d') : null,
				'stauts' => $request->status,
				'address' => $request->address
	        ]);

			$user->assignRole('Public');

			DB::commit();
			return redirect('/users')->with(['success' => 'New user created successfully']);
        } catch (\Exception $e) {
        	DB::rollback();
			dd($e->getMessage());
			return redirect()->back()->with(['error' => 'Failed to create a new user']);
        }

    }

    /**
     * Display the specified resource.
     */
	 public function show(Request $request, User $user)
     {
		 $search = $request->search;
		 $paginate = is_numeric($request->paginate) && $request->paginate > 0 ? $request->paginate : 10;
		 $sortBy = $request->sort_by ?? 'id';
		 $sortOrder = $request->sort_order ?? 'desc';
		 $status = $request->status ?? 'all';

		 $filters = $request->only(['search', 'paginate', 'sort_by', 'sort_order', 'status']);

		 $activities = Recycling::with('bin.type', 'binType', 'user')->where('user_id', $user->id)->when($search, function ($q) use ($search) {
		 	$q->where('name', 'like', "%{$search}%");
		 	$q->orWhere('email', 'like', "%{$search}%");
		 	$q->orWhere('phone', 'like', "%{$search}%");
		 	$q->orWhere('username', 'like', "%{$search}%");
		 })
		 ->when($status != 'all', function($q) use($status) {
		 	$q->where('status', $status);
		 })
		 ->orderBy($sortBy, $sortOrder)
		 ->paginate($paginate)
		 ->appends($filters);

		 $user->load('joinedEvents.user', 'joinedEvents.type');

		return Inertia::render('User/View', [
			'user' => $user,
			'filters' => $filters,
			'activities' => $activities
		]);
     }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
		$user->getDirectPermissions();

		$permissions = Permission::all();
		$modulesWithPermissions = $permissions->groupBy(function ($permission) {
		    return $permission->module_name;
		});

		return Inertia::render('User/Form', [
			'user' => $user,
			'modules_with_permissions' => $modulesWithPermissions
		]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserFormRequest $request, User $user)
    {
		DB::beginTransaction();
		try {

			$fullname = $request->first_name . ' ' . $request->last_name;
			$data = [
				'name' => $fullname,
				'first_name' => $request->first_name,
				'last_name' => $request->last_name,
				'email' => $request->email,
				'phone' => $request->phone,
				'address' => $request->address,
				'postal_code' => $request->postal_code,
				'status' => $request->status,
				'point' => $request->point,
				'display_name' => $request->display_name
			];
			if ($request->password) {
				$data['password'] = Hash::make($request->password);
			}
			$user->update($data);
			DB::commit();
			return redirect()->back()->with(['success' => 'User updated successfully']);
		} catch (\Exception $e) {
			DB::rollback();
			return redirect()->back()->with(['error' => 'Failed to update user ' . $e->getMessage()]);
		}

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
		$user->delete();
		return response()->json([
			'success' => true,
			'message' => 'User has been deleted successfully'
		]);
    }

	public function activate(Request $request, User $user) {
		$user->update(['status' => User::STATUS_ACTIVE]);
		$message = 'User has been activated';
		return response()->json([
			'success' => true,
			'message' => $message
		]);
	}

	public function toggleStatus(Request $request, User $user) {
		if ($user->status) {
			$user->update(['status' => 0]);
			$message = 'This account has been deactivated';
		} else {
			$user->update(['status' => 1]);
			$message = 'This account has been activated';
		}
		return response()->json([
			'success' => true,
			'message' => $message
		]);
	}

	public function export()
	{
	    $filename = 'Exported users data ' . date('Y-m-d') . '.xlsx';
	    return Excel::download(new UserExport, $filename, ExcelFormat::XLSX);
	}

	public function recyclings(Request $request, User $user)
	{
		$search = $request->search;
		$paginate = is_numeric($request->paginate) && $request->paginate > 0 ? $request->paginate : 10;
		$sortBy = $request->sort_by ?? 'recyclings.id';
		$sortOrder = $request->sort_order ?? 'desc';

		$recyclings = $user->recyclings()
			->select('recyclings.*') // important for avoiding ambiguous columns
			->with(['bin', 'binType'])
			->leftJoin('bins', 'bins.id', '=', 'recyclings.bin_id')
			->when($search, function ($q) use ($search) {
				$q->where(function ($query) use ($search) {
					$query->whereHas('bin', function ($bq) use ($search) {
						$bq->where('code', 'like', "%{$search}%")
						   ->orWhere('address', 'like', "%{$search}%");
					});
				});
			})
			->when($sortBy, function ($q) use ($sortBy, $sortOrder) {
				switch ($sortBy) {
					case 'created_at':
						$q->orderBy('recyclings.created_at', $sortOrder);
						break;
					case 'bins.id':
						$q->orderBy('bins.id', $sortOrder);
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

	public function vouchers(Request $request, User $user)
	{
		$search = $request->search;
		$paginate = is_numeric($request->paginate) && $request->paginate > 0 ? $request->paginate : 10;
		$sortBy = $request->sort_by ?? 'users.id';
		$sortOrder = $request->sort_order ?? 'desc';

		$rewards = $user->rewards()
			->with(['reward', 'voucher'])
			->select('user_rewards.*', 'rewards.name as reward_name', 'vouchers.code as voucher_code')
			->leftJoin('rewards', 'rewards.id', '=', 'user_rewards.reward_id')
			->leftJoin('vouchers', 'vouchers.id', '=', 'user_rewards.voucher_id')
			->when($search, function ($q) use ($search) {
				$q->where(function ($query) use ($search) {
					$query->whereHas('reward', function ($uq) use ($search) {
						$uq->where('name', 'like', "%{$search}%")
							->orWhere('price', 'like', "%{$search}%");
					})
					->orWhereHas('voucher', function ($bq) use ($search) {
						$bq->where('code', 'like', "%{$search}%");
					});
				});
			})
			->when($sortBy, function ($q) use ($sortBy, $sortOrder) {
				switch ($sortBy) {
					case 'reward_name':
						$q->orderBy('rewards.name', $sortOrder);
						break;
					case 'point':
						$q->orderBy('rewards.point', $sortOrder);
						break;
					case 'redeemed_at':
						$q->orderBy('user_rewards.created_at', $sortOrder);
						break;
					case 'code':
						$q->orderBy('vouchers.code', $sortOrder);
						break;
					default:
						$q->orderBy('user_rewards.id', $sortOrder);
				}
			})
			->paginate($paginate);

		return response()->json($rewards);
	}


	public function ban(Request $request, User $user)
	{
		$validated = $request->validate([
			'reason'   => 'required|string|max:500',
			'duration' => 'required|integer',
			'evidence' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
		]);

		DB::beginTransaction();

		try {
			$evidenceFilename = null;
			if ($request->hasFile('evidence')) {
				$image = $request->file('evidence');
				$filename = time() . '_' . $image->getClientOriginalName();
				$path = $image->storeAs('public/images/ban_evidence', $filename);
				$evidenceFilename = $filename;
			}

			BannedList::create([
				'user_id' => $user->id,
				'reason' => $request->reason,
				'evidence_filename' => $evidenceFilename,
				'duration_days' => $request->duration,
				'moderator' => auth()->user()->id
			]);

			$user->update([
				'status' => User::STATUS_BANNED
			]);
			DB::commit();
			return ApiResponse::success([], 'User has been banned');
		} catch (\Exception $e) {
			DB::rollback();
			return ApiResponse::error('Failed to ban user' . $e->getMessage());
		}
	}

}
