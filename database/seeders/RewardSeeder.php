<?php

namespace Database\Seeders;

use App\Models\Reward;
use App\Models\Voucher;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class RewardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Storage::disk('public')->deleteDirectory('images/reward');
        Storage::disk('public')->makeDirectory('images/reward');

        $imageName = null;
        try {
            $response = Http::get('https://down-id.img.susercontent.com/file/id-11134207-7qul5-ljw3dz1d125m6e');
            if ($response->successful()) {
                $imageName = uniqid() . '.jpg';
                $path = 'images/reward/' . $imageName;
                Storage::disk('public')->put($path, $response->body());
            }
        } catch (\Exception $e) {
            // Silently fail if image download is not successful
        }

        for ($i = 0; $i < 20; $i++) {
            $reward = Reward::factory()->create([
                'code' => Reward::generateCode(),
                'image' => $imageName,
            ]);

            $numberOfVouchers = rand(20, 50);
            for ($j = 0; $j < $numberOfVouchers; $j++) {
                Voucher::create([
                    'reward_id' => $reward->id,
                    'code' => 'VOUCHER-' . uniqid(),
                    'status' => 1
                ]);
            }
        }
    }
}
