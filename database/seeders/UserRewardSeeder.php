<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Reward;
use App\Models\UserReward;
use App\Models\User;

class UserRewardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
		$user = User::where('email', 'user@gmail.com')->first();
        $rewards = Reward::with('vouchers')->get();
		foreach ($rewards as $reward) {
			$redeem = rand(0, 1);
			if (!$redeem) {
				continue;
			}
			foreach ($reward->vouchers as $voucher) {
				$status = rand(1,2);
				if ($status == 2) {
					$voucher->update(['status' => 2]);
					UserReward::create([
						'user_id' => $user->id,
						'reward_id' => $reward->id,
						'voucher_id' => $voucher->id
					]);
					break;
				}
			}
		}
    }
}
