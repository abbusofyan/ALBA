<?php

namespace Database\Factories;

use App\Models\Bin;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Bin>
 */
class BinFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Bin::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'bin_type_id' => \App\Models\BinType::factory(),
            'address' => $this->faker->address,
            'postal_code' => $this->faker->postcode,
            'lat' => $this->faker->latitude,
            'long' => $this->faker->longitude,
            'map_radius' => $this->faker->randomNumber(2),
            'status' => 1,
            'e_waste_bin_type_id' => null,
            'qrcode' => $this->faker->uuid,
            'remark' => $this->faker->sentence,
        ];
    }
}
