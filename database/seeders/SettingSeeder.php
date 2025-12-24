<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

		DB::statement('SET FOREIGN_KEY_CHECKS=0;');

		DB::table('max_daily_rewards')->truncate();

		DB::statement('SET FOREIGN_KEY_CHECKS=1;');

		\App\Models\Setting::create([
			'user_max_daily_reward' => 600,
			'user_max_monthly_redemption' => 600
		]);
    }
}
