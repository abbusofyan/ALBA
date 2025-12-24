<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class StaffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

		$staffs = [
			[
				'name' => 'Wise',
				'username' => 'Wise',
				'email' => 'wise.lim@albagroup.asia',
				'phone' => '12309875',
			],
			[
				'name' => 'Javier',
				'username' => 'Javier',
				'email' => 'javier.chua@albagroup.asia',
				'phone' => '12309876',
			],
		];

		foreach ($staffs as $staff) {
			$newStaff = User::firstOrCreate([
				'email' => $staff['email'],
			], [
				'name'              => $staff['name'],
				'username'          => $staff['username'],
				'email_verified_at' => now(),
				'password'          => 'password',
				'phone'             => $staff['phone'],
				'status'            => 1
			]);

			$newStaff->assignRole('Staff');
		}

    }
}
