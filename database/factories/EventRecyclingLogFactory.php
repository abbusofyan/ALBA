<?php

namespace Database\Factories;

use App\Models\EventRecyclingLog;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EventRecyclingLog>
 */
class EventRecyclingLogFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = EventRecyclingLog::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'event_id' => \App\Models\Event::factory(),
            'recycling_id' => \App\Models\Recycling::factory(),
        ];
    }
}
