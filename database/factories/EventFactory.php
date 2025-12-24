<?php

namespace Database\Factories;

use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Event::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'event_type_id' => \App\Models\EventType::factory(),
            'district_id' => \App\Models\District::factory(),
            'user_id' => \App\Models\User::factory(),
            'name' => $this->faker->sentence,
            'address' => $this->faker->address,
            'postal_code' => $this->faker->postcode,
            'lat' => $this->faker->latitude,
            'long' => $this->faker->longitude,
            'date_start' => Carbon::now(),
            'date_end' => Carbon::now()->addDays(7),
            'time_start' => '09:00:00',
            'time_end' => '17:00:00',
            'description' => $this->faker->paragraph,
            'image' => $this->faker->imageUrl(),
            'status' => 1,
            'code' => $this->faker->unique()->word,
            'secret_code' => $this->faker->unique()->word,
        ];
    }
}
