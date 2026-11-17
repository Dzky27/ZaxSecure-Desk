<?php

namespace Database\Factories;

use App\Models\Incident;
use App\Models\IncidentUpdate;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<IncidentUpdate>
 */
class IncidentUpdateFactory extends Factory
{
    protected $model = IncidentUpdate::class;

    public function definition(): array
    {
        return [
            'incident_id' => Incident::factory(),
            'user_id' => User::factory(),
            'old_status' => 'pending',
            'new_status' => 'investigating',
            'message' => fake()->sentence(),
            'visibility' => fake()->randomElement(['public', 'internal']),
        ];
    }
}
