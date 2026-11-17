<?php

namespace Database\Factories;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ActivityLog>
 */
class ActivityLogFactory extends Factory
{
    protected $model = ActivityLog::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'action' => fake()->randomElement(['user.login', 'incident.submitted', 'incident.updated']),
            'subject_type' => 'App\\Models\\Incident',
            'subject_id' => fake()->numberBetween(1, 100),
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
            'metadata' => ['browser' => 'Chrome', 'ip' => fake()->ipv4()],
        ];
    }
}
