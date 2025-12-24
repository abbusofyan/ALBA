<?php

namespace Database\Factories;

use App\Models\Reward;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reward>
 */
class RewardFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->sentence(2),
            'description' => $this->faker->paragraph,
            'tnc' => $this->faker->paragraph,
            'label' => $this->faker->word,
            'price' => $this->faker->numberBetween(100, 1000),
            'image' => null,
            'status' => 1,
        ];
    }
}
