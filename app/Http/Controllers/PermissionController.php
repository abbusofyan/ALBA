<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Permission::class, 'permission');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $search = $request->search;
        $paginate = is_numeric($request->paginate) && $request->paginate > 0 ? $request->paginate : 10;

        $filters = $request->only(['search', 'paginate']);

        $permissionData = Permission::with('roles')->when($search, function ($query) use ($search) {
            $query->where('name', 'LIKE', "%{$search}%");
        })
            ->paginate($paginate)
            ->appends($filters);

        $transformedData = $permissionData->through(function ($permission) {
            return [
                'id' => $permission->id,
                'name' => $permission->name,
                'roles' => $permission->roles->pluck('name'),
                'module' => $permission->module ? ucfirst(str_replace('-', ' ', $permission->module)) : 'N/A',
                'created_at' => $permission->created_at,
            ];
        });

        return Inertia::render('Permission/Index', [
            'permissionData' => $transformedData,
            'filters' => $filters,
        ]);
    }

    public function getPermissions(Request $request)
    {
        $permissions = Permission::all();
        $groupedPermissions = [];
        $names = [];

        foreach ($permissions as $permission) {
            $moduleParts = explode('-', $permission->module);
            $managementIndex = array_search('management', $moduleParts);

            if ($managementIndex !== false) {
                $modulePrefix = implode('-', array_slice($moduleParts, 0, $managementIndex));
            } else {
                $modulePrefix = $permission->module;
            }

            if ($modulePrefix && !in_array($modulePrefix, $names)) {
                $names[] = $modulePrefix;
            }

            if (!isset($groupedPermissions[$permission->module])) {
                $groupedPermissions[$permission->module] = [
                    'name' => ucfirst(str_replace(['_', '-'], ' ', $permission->module)),
                    'permissions' => []
                ];
            }

            $formattedName = ucfirst(str_replace('-', ' ', $permission->name));
            foreach ($names as $module) {
                if (str_ends_with($permission->name, '-' . $module)) {
                    $formattedName = ucfirst(str_replace('-', ' ', substr($permission->name, 0, -strlen($module) - 1)));
                    break;
                }
            }
            $groupedPermissions[$permission->module]['permissions'][] = [
                'id' => $permission->id,
                'name' => $formattedName,
                'selected' => false,
            ];
        }
        return response()->json([
            'data' => array_values($groupedPermissions),
            'modules' => $names,
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
        $filePath = public_path('permissions.json');
        $permissions = file_exists($filePath) ? json_decode(file_get_contents($filePath), true) : [];

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name',
            'module_id' => 'required|exists:menus,id',
        ]);

        $menu = Menu::findOrFail($request->module_id);
        $permissionName = $validated['name'] . '-' . strtolower(str_replace(' ', '-', $menu->name));

        DB::beginTransaction();
        try {
            $permission = Permission::firstOrCreate([
                'name' => $permissionName,
                'module' => $menu->menu_key,
            ]);

            $menuKey = $menu->menu_key;
            if (!isset($permissions[$menuKey])) {
                $permissions[$menuKey] = [];
            }
            if (!in_array($permissionName, $permissions[$menuKey])) {
                $permissions[$menuKey][] = $permissionName;
                file_put_contents($filePath, json_encode($permissions, JSON_PRETTY_PRINT));
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $permission->wasRecentlyCreated
                    ? 'Permission created successfully'
                    : 'Permission already exists',
                'data' => $permission
            ], $permission->wasRecentlyCreated ? 201 : 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating permission: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to create permission. Please try again later.'
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
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */

    public function update(Request $request, Permission $permission)
    {
        $oldPermissionName = $permission->name;
        $moduleName = $permission->module;
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name,' . $permission->id,
        ]);
        DB::beginTransaction();
        try {
            $permission->update(['name' => $validated['name']]);

            $permissionsFile = public_path('permissions.json');
            $rolesFile = public_path('roles.json');
            $permissions = file_exists($permissionsFile) ? json_decode(file_get_contents($permissionsFile), true) : [];

            if ($moduleName && isset($permissions[$moduleName])) {
                $key = array_search($oldPermissionName, $permissions[$moduleName]);
                if ($key !== false) {
                    $permissions[$moduleName][$key] = $validated['name'];
                }
            }
            file_put_contents($permissionsFile, json_encode($permissions, JSON_PRETTY_PRINT));
            if (file_exists($rolesFile)) {
                $roles = json_decode(file_get_contents($rolesFile), true) ?? [];
                foreach ($roles as $roleName => $rolePermissions) {
                    $key = array_search($oldPermissionName, $rolePermissions);
                    if ($key !== false) {
                        $roles[$roleName][$key] = $validated['name'];
                    }
                }
                file_put_contents($rolesFile, json_encode($roles, JSON_PRETTY_PRINT));
            }
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Permission updated successfully',
                'data' => $permission
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating permission: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update permission. Please try again later.'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */

    public function destroy(Permission $permission)
    {
        $permissionName = $permission->name;
        $moduleName = $permission->module;

        DB::beginTransaction();
        try {

            $permission->delete();

            $permissionsFile = public_path('permissions.json');
            $rolesFile = public_path('roles.json');

            $permissions = file_exists($permissionsFile) ? json_decode(file_get_contents($permissionsFile), true) : [];

            if ($moduleName && isset($permissions[$moduleName])) {
                $index = array_search($permissionName, $permissions[$moduleName]);
                if ($index !== false) {
                    unset($permissions[$moduleName][$index]);
                    $permissions[$moduleName] = array_values($permissions[$moduleName]);
                    if (empty($permissions[$moduleName])) {
                        unset($permissions[$moduleName]);
                    }
                }
            }

            file_put_contents($permissionsFile, json_encode($permissions, JSON_PRETTY_PRINT));

            if (file_exists($rolesFile)) {
                $roles = json_decode(file_get_contents($rolesFile), true) ?? [];
                foreach ($roles as $roleName => &$rolePermissions) {
                    if (($key = array_search($permissionName, $rolePermissions)) !== false) {
                        unset($rolePermissions[$key]);
                        $rolePermissions = array_values($rolePermissions);
                    }
                }

                file_put_contents($rolesFile, json_encode($roles, JSON_PRETTY_PRINT));
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Permission deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting permission: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete permission. Please try again later.',
            ], 500);
        }
    }
}
