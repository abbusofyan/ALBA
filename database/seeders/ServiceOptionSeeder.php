<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServiceOptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
		$options = ['Request for Outreach', 'Visitor Centre', 'Private Events', 'Others'];

		foreach ($options as $option) {
			\App\Models\ServiceOption::firstOrCreate([
				'name'   => $option
			]);
		}
    }
}
