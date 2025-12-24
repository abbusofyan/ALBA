<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Role::class, 'role');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Role/Index');
    }

    public function getData()
    {
        $roles = Role::with(['users:id,name', 'permissions:id,name'])->get(['id', 'name']);

        $roles = $roles->map(function ($role) {
            return [
                'id' => $role->id,
                'name' => $role->name,
                'users' => $role->users,
                'permissions' => $role->permissions->map(function ($permission) {
                    return [
                        'id' => $permission->id,
                        'name' => $permission->name,
                        'selected' => true,
                    ];
                }),
            ];
        });

        return response()->json(['data' => $roles]);
    }


    public function getUsers(Request $request)
    {
        $search = $request->input('search', '');
        $itemsPerPage = $request->input('itemsPerPage', 10);
        $page = $request->input('page', 1);
        $selectedRole = $request->input('roleId', null);

        $query = User::with('roles')
            ->when($search, function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })
            ->when($selectedRole, function ($q) use ($selectedRole) {
                $q->whereHas('roles', function ($query) use ($selectedRole) {
                    $query->where('roles.id', $selectedRole);
                });
            });

        $users = $itemsPerPage != -1
            ? $query->paginate($itemsPerPage, ['*'], 'page', $page)
            : $query->get();

        return response()->json([
            'data' => $users->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'roles' => $user->roles->map(function ($role) {
                        return [
                            'id' => $role->id,
                            'name' => $role->name,
                        ];
                    }),
                ];
            }),
            'total' => $itemsPerPage != -1 ? $users->total() : $users->count(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Role::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'required|array|min:1',
            'permissions.*' => 'exists:permissions,id',
        ]);

        DB::beginTransaction();
        try {
            $role = Role::create(['name' => $validated['name']]);
            $role->givePermissionTo($validated['permissions']);
            $rolesFile = public_path('roles.json');
            $roles = file_exists($rolesFile) ? json_decode(file_get_contents($rolesFile), true) : [];
            $permissionNames = Permission::whereIn('id', $validated['permissions'])->pluck('name')->toArray();
            $roles[$role->name] = $permissionNames;
            file_put_contents($rolesFile, json_encode($roles, JSON_PRETTY_PRINT));
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Role created successfully',
                'data' => $role,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating role: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to create role. Please try again later.',
            ], 500);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id) {}

    /**
     * Update the specified resource in storage.
     */

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'required|unique:roles,name,' . $role->id . ',id',
            'permissions' => 'required|array|min:1',
            'permissions.*' => 'exists:permissions,id',
        ]);

        DB::beginTransaction();
        try {
            $oldRoleName = $role->name;
            $role->update(['name' => $validated['name']]);
            $role->syncPermissions($validated['permissions']);

            $rolesFile = public_path('roles.json');
            $menusFile = public_path('menus.json');

            $roles = file_exists($rolesFile) ? json_decode(file_get_contents($rolesFile), true) : [];
            $menus = file_exists($menusFile) ? json_decode(file_get_contents($menusFile), true) : [];

            if ($oldRoleName !== $validated['name'] && isset($roles[$oldRoleName])) {
                $roles[$validated['name']] = $roles[$oldRoleName];
                unset($roles[$oldRoleName]);
            }

            $permissionNames = Permission::whereIn('id', $validated['permissions'])->pluck('name')->toArray();
            $roles[$validated['name']] = $permissionNames;
            file_put_contents($rolesFile, json_encode($roles, JSON_PRETTY_PRINT));

            if ($oldRoleName !== $validated['name']) {
                foreach ($menus as &$menu) {
                    if (isset($menu['roles']) && is_array($menu['roles'])) {
                        foreach ($menu['roles'] as &$roleName) {
                            if ($roleName === $oldRoleName) {
                                $roleName = $validated['name'];
                            }
                        }
                    }
                }
                file_put_contents($menusFile, json_encode($menus, JSON_PRETTY_PRINT));
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Role updated successfully',
                'data' => $role,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating role: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to update role. Please try again later.',
            ], 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     */

    public function destroy(Role $role)
    {
        $roleName = $role->name;

        DB::beginTransaction();
        try {
            $role->syncPermissions([]);
            $role->delete();

            $rolesFile = public_path('roles.json');
            $menusFile = public_path('menus.json');

            if (file_exists($rolesFile)) {
                $roles = json_decode(file_get_contents($rolesFile), true) ?? [];
                if (isset($roles[$roleName])) {
                    unset($roles[$roleName]);
                    file_put_contents($rolesFile, json_encode($roles, JSON_PRETTY_PRINT));
                }
            }

            if (file_exists($menusFile)) {
                $menus = json_decode(file_get_contents($menusFile), true) ?? [];
                foreach ($menus as &$menu) {
                    if (isset($menu['roles']) && is_array($menu['roles'])) {
                        $menu['roles'] = array_values(array_filter($menu['roles'], fn($r) => $r !== $roleName));
                    }
                }
                file_put_contents($menusFile, json_encode($menus, JSON_PRETTY_PRINT));
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Role deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting role: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete role. Please try again later.',
            ], 500);
        }
    }
}
