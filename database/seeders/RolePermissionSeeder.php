<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run()
    {
        $permissionsPath = public_path('permissions.json');
        $rolesPath = public_path('roles.json');

        if (file_exists($permissionsPath)) {
            $json = file_get_contents($permissionsPath);
            $permissions = json_decode($json, true) ?? [];

            if (empty($permissions)) {
                $this->command->warn("No permissions found in permissions.json.");
                return;
            }

            foreach ($permissions as $module => $permissionsByModule) {
                foreach ($permissionsByModule as $permission) {
                    Permission::firstOrCreate([
                        'name'   => $permission,
                        'module' => $module,
                    ]);
                }
            }
            $this->command->info("Permissions seeded successfully!");
        }

        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $adminPermissions = Permission::pluck('name')->toArray();
        $adminRole->syncPermissions($adminPermissions);

        if (file_exists($rolesPath)) {
            $jsonRoles = file_get_contents($rolesPath);
            $roles = json_decode($jsonRoles, true) ?? [];

            foreach ($roles as $roleName => $rolePermissions) {
                if ($roleName === 'Admin') {
                    continue;
                }

                $role = Role::firstOrCreate(['name' => $roleName]);
                $validPermissions = Permission::whereIn('name', $rolePermissions)->pluck('name')->toArray();
                $role->syncPermissions($validPermissions);
            }
            $this->command->info("Roles seeded successfully!");
        }
    }
}
