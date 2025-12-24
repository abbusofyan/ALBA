<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\MenuRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class MenuController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Menu::class, 'menu');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $search = $request->search;

        $order = $request->order ?? 'name';
        $by = $request->by ?? 'asc';
        $paginate = is_numeric($request->paginate) && $request->paginate > 0 ? $request->paginate : 10;

        $filters = $request->only(['search', 'order', 'by', 'paginate']);

        $menuData = Menu::with('parent')->when($search, function ($query) use ($search) {
            $query->where('name', 'LIKE', "%{$search}%");
        })
            ->orderBy($order, $by)
            ->paginate($paginate)
            ->appends($filters);

        return Inertia::render('Menu/Index', [
            'menuData' => $menuData,
            'filters' => $filters,
        ]);
    }
    public function getData()
    {
        $menus = Menu::with('parent:id,name')->get();

        return response()->json(['data' => $menus]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Menu/Form', [
            'menus' => Menu::all(),
            'roles' => Role::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:menus,name',
            'link' => 'nullable|string',
            'order' => 'required|integer',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id',
            'is_heading'   => 'nullable|boolean',
        ]);

        $parentMenu = $request->parent ? Menu::find($request->parent) : null;
        $parentKey = $parentMenu ? $parentMenu->menu_key : null;
        $image = null;

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = $file->getClientOriginalName();
            $destinationPath = public_path('menus');
            $file->move($destinationPath, $fileName);
            $image = asset("menus/$fileName");
        }

        if ($parentMenu) {
            $parentNameSlug = strtolower(str_replace(' ', '-', $parentMenu->name));
            $menuNameSlug = strtolower(str_replace(' ', '-', $validated['name']));
            $menuKey = $parentNameSlug . '-' . $menuNameSlug . '-management';
        } else {
            $menuKey = strtolower(str_replace(' ', '-', $validated['name'])) . '-management';
        }

        $menu = Menu::create([
            'name' => $validated['name'],
            'link' => $validated['link'],
            'order' => $validated['order'],
            'parent_id' => $parentMenu ? $parentMenu->id : null,
            'image' => $image,
            'menu_key' => $menuKey,
            'is_heading' => $validated['is_heading'] ?? false,
        ]);

        $roleNames = [];
        foreach ($request->roles as $roleId) {
            $role = Role::find($roleId);
            if ($role) {
                MenuRole::create([
                    'menu_id' => $menu->id,
                    'role_id' => $role->id,
                ]);
                $roleNames[] = $role->name;
            }
        }

        $jsonPath = public_path('menus.json');
        $json = file_get_contents($jsonPath);
        $menus = json_decode($json, true) ?? [];

        $menus[] = [
            'key' => $menuKey,
            'name' => $menu->name,
            'link' => $menu->link,
            'order' => $menu->order,
            'parent_key' => $parentKey,
            'image' => $menu->image,
            'is_heading' => $menu->is_heading,
            'roles' => $roleNames
        ];

        file_put_contents($jsonPath, json_encode($menus, JSON_PRETTY_PRINT));

        return redirect()->route('menu.index')->with('created', "Created menu successfully");
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
    public function edit(Menu $menu)
    {
        return Inertia::render('Menu/Form', [
            'menu_detail' => $menu->load('roles', 'parent'),
            'menus' => Menu::all(),
            'roles' => Role::all(),
        ]);
    }


    /**
     * Update the specified resource in storage.
     */

    public function update(Request $request, Menu $menu)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:menus,name,' . $menu->id,
            'order' => 'required|numeric',
            'link' => 'nullable|string',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id',
            'is_heading'   => 'nullable|boolean',
        ]);

        $oldMenuKey = $menu->menu_key;
        $parentMenu = $request->parent ? Menu::find($request->parent) : null;
        $parentKey = $parentMenu ? $parentMenu->menu_key : null;
        $image = $menu->image;

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = $file->getClientOriginalName();
            $destinationPath = public_path('menus');
            $file->move($destinationPath, $fileName);
            $image = asset("menus/$fileName");
        }

        $menuKey = strtolower(str_replace(' ', '-', $validated['name'])) . '-management';
        $menu->update([
            'name' => $validated['name'],
            'order' => $validated['order'],
            'link' => $validated['link'],
            'parent_id' => $parentMenu ? $parentMenu->id : null,
            'image' => $image,
            'menu_key' => $menuKey,
            'is_heading' => $validated['is_heading'] ?? false,
        ]);

        $existingRoleIds = $menu->roles->pluck('id')->toArray();
        $newRoleIds = $request->roles;

        if (array_diff($existingRoleIds, $newRoleIds) || array_diff($newRoleIds, $existingRoleIds)) {
            MenuRole::where('menu_id', $menu->id)->delete();
            $roleNames = [];
            foreach ($newRoleIds as $roleId) {
                $role = Role::find($roleId);
                if ($role) {
                    MenuRole::create([
                        'menu_id' => $menu->id,
                        'role_id' => $role->id,
                    ]);
                    $roleNames[] = $role->name;
                }
            }
        } else {
            $roleNames = $menu->roles->pluck('name')->toArray();
        }

        $menusPath = public_path('menus.json');
        $permissionsPath = public_path('permissions.json');
        $menus = file_exists($menusPath) ? json_decode(file_get_contents($menusPath), true) : [];

        foreach ($menus as &$item) {
            if ($item['key'] === $oldMenuKey) {
                $item['key'] = $menuKey;
                $item['name'] = $menu->name;
                $item['link'] = $menu->link;
                $item['order'] = $menu->order;
                $item['parent_key'] = $parentKey;
                $item['image'] = $menu->image;
                $item['is_heading'] = $menu->is_heading;
                $item['roles'] = $roleNames;
                break;
            }
        }

        file_put_contents($menusPath, json_encode($menus, JSON_PRETTY_PRINT));
        $permissions = file_exists($permissionsPath) ? json_decode(file_get_contents($permissionsPath), true) : [];

        if ($oldMenuKey !== $menuKey && isset($permissions[$oldMenuKey])) {
            $permissions[$menuKey] = $permissions[$oldMenuKey];
            unset($permissions[$oldMenuKey]);
            file_put_contents($permissionsPath, json_encode($permissions, JSON_PRETTY_PRINT));
        }
        Permission::where('module', $oldMenuKey)->update(['module' => $menuKey]);
        return redirect()->route('menu.index')->with('updated', "Updated menu successfully");
    }


    /**
     * Remove the specified resource from storage.
     */

    public function destroy(Request $request, Menu $menu)
    {
        if ($menu->children()->exists()) {
            return back()->withErrors(['message' => 'Cannot delete menu with sub-menus. Please delete sub-menus first']);
        }
        $moduleKey = $menu->menu_key;

        DB::transaction(function () use ($menu, $moduleKey) {
            $menu->delete();
            $this->updateMenusJson($moduleKey);
            $this->updatePermissionsJson($moduleKey);
            $this->updateRolesJson($moduleKey);
            Permission::where('module', $moduleKey)->delete();
        });

        return back()->with('deleted', 'Menu deleted successfully');
    }

    private function updateMenusJson($moduleKey)
    {
        $filePath = public_path('menus.json');
        if (!file_exists($filePath)) {
            return;
        }
        $json = file_get_contents($filePath);
        $menus = json_decode($json, true) ?? [];

        $updatedMenus = array_filter($menus, function ($menuItem) use ($moduleKey) {
            return $menuItem['key'] !== $moduleKey;
        });
        file_put_contents($filePath, json_encode(array_values($updatedMenus), JSON_PRETTY_PRINT));
    }

    private function updatePermissionsJson($moduleKey)
    {
        $filePath = public_path('permissions.json');
        if (!file_exists($filePath)) {
            return;
        }
        $jsonPermissions = file_get_contents($filePath);
        $permissions = json_decode($jsonPermissions, true) ?? [];
        if (isset($permissions[$moduleKey])) {
            unset($permissions[$moduleKey]);
            file_put_contents($filePath, json_encode($permissions, JSON_PRETTY_PRINT));
        }
    }

    private function updateRolesJson($moduleKey)
    {
        $filePath = public_path('roles.json');
        if (!file_exists($filePath)) {
            return;
        }

        $permissionsToDelete = Permission::where('module', $moduleKey)->pluck('name')->toArray();
        if (empty($permissionsToDelete)) {
            return;
        }

        $jsonRoles = file_get_contents($filePath);
        $roles = json_decode($jsonRoles, true) ?? [];

        foreach ($roles as $roleName => &$permissions) {
            $permissions = array_values(array_diff($permissions, $permissionsToDelete));
        }

        file_put_contents($filePath, json_encode($roles, JSON_PRETTY_PRINT));
    }
}
