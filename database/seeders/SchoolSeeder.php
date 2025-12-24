<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;
use Faker\Factory as Faker;
use App\Models\User;

class SchoolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

		$faker = Faker::create();
		for ($i = 0; $i < 10; $i++) {
			$email = $faker->unique()->safeEmail;
			$uniqueID = User::generateUniqueID('school');
			$user = User::firstOrCreate([
	            'email' => $email,
	        ], [
	            'name'              => $faker->company,
				'username'          => $uniqueID,
	            'email_verified_at' => now(),
	            'password'          => 'Password123',
				'phone'				=> $faker->phoneNumber,
	            'status'            => 1,
				'activated_at'		=> now()
	        ]);

	        /** Add USER'S ROLE **/
	        $user->assignRole('School');

		}
    }
}
