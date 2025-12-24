<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call([
            // RoleSeeder::class,
            // PermissionSeeder::class,
            // SchoolSeeder::class,
            // EnterpriseSeeder::class,
            // WasteTypeSeeder::class,
            // BinTypeSeeder::class,
            // BinsTableSeeder::class,
            // DistrictSeeder::class,
            // EventTypeSeeder::class,
            // EventTableSeeder::class,
            // RewardSeeder::class,
			// UserRewardSeeder::class,
            // UserJoinedEventSeeder::class,
			// SettingSeeder::class,
			// ServiceOptionSeeder::class,
			PickUpWasteTypeSeeder::class,
			PickUpWeightRangeSeeder::class,
			PickUpTimeSlotSeeder::class
        ]);
    }
}
