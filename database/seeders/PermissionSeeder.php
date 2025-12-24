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
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		DB::statement('SET FOREIGN_KEY_CHECKS=0;');

		DB::table('permissions')->truncate();

		DB::statement('SET FOREIGN_KEY_CHECKS=1;');

		$permissions = [
			[
				'module' => 'user',
				'description' => 'User',
				'ability' => ["view", "update", "delete"],
			],
			[
				'module' => 'staff',
				'description' => 'Staff (Only admin account)',
				'ability' => ["view", "create", "update", "delete"],
			],
			[
				'module' => 'school',
				'description' => 'School',
				'ability' => ["view", "create", "update", "delete"],
			],
			[
				'module' => 'enterprise',
				'description' => 'Enterprise',
				'ability' => ["view", "create", "update", "delete"],
			],
			[
				'module' => 'bin',
				'description' => 'Bin Management',
				'ability' => ["view", "create", "update", "delete"],
			],
			[
				'module' => 'bin-type',
				'description' => 'Bin Type Management',
				'ability' => ["view", "create", "update", "delete"],
			],
			[
				'module' => 'recycling',
				'description' => 'Recycling',
				'ability' => ["view"],
			],
			[
				'module' => 'event',
				'description' => 'Event',
				'ability' => ["view", "create", "update", "delete"],
			],
			[
				'module' => 'reward',
				'description' => 'Reward',
				'ability' => ["view", "create", "update", "delete"],
			],
			[
				'module' => 'banner',
				'description' => 'Banner',
				'ability' => ["view", "update"],
			],
			[
				'module' => 'setting',
				'description' => 'Setting',
				'ability' => ["view", "update"],
			]
		];

		$permissionsName = [];

		foreach ($permissions as $permissionsByModule) {
			foreach ($permissionsByModule['ability'] as $permission) {
				$permissionName = $permission . '-' . $permissionsByModule['module'];
				Permission::firstOrCreate([
					'name'   => $permissionName,
					'module' => $permissionsByModule['module'],
					'description' => $permissionsByModule['description']
				]);

				$permissionsName[] = $permissionName;
			}
		}

		$users = [
			[
				'name'              => 'Admin',
				'email'			    => 'admin@corsivalab.space',
				'username'          => 'admin',
				'email_verified_at' => now(),
				'phone'             => '0645978456',
				'role'  			=> 'Admin',
				'password'          => 'Password123',
				'point'				=> 10000
			],
			[
				'name' 				=> 'Wise',
				'username'		 	=> 'Wise',
				'email' 			=> 'wise.lim@albagroup.asia',
				'phone' 			=> '12309875',
				'role' 				=> 'Staff',
				'password'  		=> 'password',
				'point'				=> 10000
			],
			[
				'name' 				=> 'Javier',
				'username'		 	=> 'Javier',
				'email' 			=> 'javier.chua@albagroup.asia',
				'phone' 			=> '12309876',
				'role' 				=> 'Staff',
				'password'  		=> 'password',
				'point'				=> 10000
			],
			[
				'first_name' 		=> 'Test',
				'last_name'			=> 'User',
				'email' 			=> 'user@gmail.com',
				'phone' 			=> '+6512341234',
				'role' 				=> 'Public',
				'password'  		=> 'password123',
				'point'				=> 10000
			],
			[
				'first_name' 		=> 'Rachel',
				'last_name'			=> 'Theen',
				'email' 			=> 'rachel@otg-lab.com',
				'phone' 			=> '+6597204939',
				'role' 				=> 'Public',
				'password'  		=> 'password',
				'point'				=> 10000
			],
		];

		foreach ($users as $user) {
			$newUser = User::firstOrCreate([
				'email' => $user['email'],
			], [
				'first_name' 		=> $user['first_name'] ?? null,
				'last_name'			=> $user['last_name'] ?? null,
				'name'              => $user['name'] ?? $user['first_name'] . ' ' . $user['last_name'],
				'username'          => $user['username'] ?? null,
				'email_verified_at' => now(),
				'password'          => $user['password'],
				'phone'             => $user['phone'],
				'status'            => 1,
				'point' 			=> 10000
			]);

			$newUser->assignRole($user['role']);

			if ($user['role'] != 'Public') {
				$newUser->syncPermissions($permissionsName);
			}
		}

		$this->command->info('Permission updated');
	}
}
