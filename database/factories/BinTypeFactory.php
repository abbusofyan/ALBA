<?php

namespace Database\Factories;

use App\Models\BinType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BinType>
 */
class BinTypeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = BinType::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word,
            'image' => $this->faker->imageUrl(),
            'icon' => $this->faker->imageUrl(),
            'fixed_qrcode' => $this->faker->boolean,
            'point' => $this->faker->randomFloat(2, 0, 10),
        ];
    }
}
