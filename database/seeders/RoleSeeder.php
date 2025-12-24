<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserMeta;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Storage;
use App\Models\Menu;
use Illuminate\Support\Arr;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		$roles = ['Admin', 'Staff', 'Public', 'School', 'Enterprise'];

		foreach ($roles as $role) {
			Role::firstOrCreate([
				'name'   => $role
			]);
		}

		$this->command->info('Initial role addedd');

    }
}
