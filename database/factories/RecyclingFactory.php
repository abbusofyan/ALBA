<?php

namespace Database\Factories;

use App\Models\Recycling;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Recycling>
 */
class RecyclingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Recycling::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'bin_id' => \App\Models\Bin::factory(),
            'reward' => $this->faker->randomFloat(2, 1, 100),
            'photo' => $this->faker->imageUrl(),
        ];
    }
}
