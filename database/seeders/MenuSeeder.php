<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\MenuRole;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run(): void
    {
        $jsonPath = public_path('menus.json');
        if (!file_exists($jsonPath)) {
            $this->command->warn("menus.json file not found.");
            return;
        }
        $json = file_get_contents($jsonPath);
        $menus = json_decode($json, true);

        if (empty($menus)) {
            $this->command->warn("No menus found in menus.json.");
            return;
        }
        $menuMap = [];
        foreach ($menus as $menuData) {
            $menu = Menu::updateOrCreate([
                'menu_key' => $menuData['key']
            ], [
                'name' => $menuData['name'],
                'link' => $menuData['link'],
                'order' => $menuData['order'],
                'image' => $menuData['image'] ?? null,
                'is_heading' => $menuData['is_heading'],
            ]);

            $menuMap[$menuData['key']] = $menu->id;
        }

        foreach ($menus as $menuData) {
            if (!empty($menuData['parent_key']) && isset($menuMap[$menuData['parent_key']])) {
                Menu::where('menu_key', $menuData['key'])->update([
                    'parent_id' => $menuMap[$menuData['parent_key']]
                ]);
            }
        }

        foreach ($menus as $menuData) {
            if (isset($menuData['roles']) && is_array($menuData['roles'])) {
                $roleIds = Role::whereIn('name', $menuData['roles'])->pluck('id')->toArray();
                if (!empty($roleIds)) {
                    Menu::where('menu_key', $menuData['key'])->first()->roles()->sync($roleIds);
                }
            }
        }

        $this->command->info("Menus seeded successfully!");
    }
}
