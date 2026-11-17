<?php

namespace Database\Factories;

use App\Models\SecurityEvent;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SecurityEvent>
 */
class SecurityEventFactory extends Factory
{
    protected $model = SecurityEvent::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'event_type' => fake()->randomElement(['failed_login', 'suspicious_activity', 'access_denied']),
            'severity' => fake()->randomElement(['low', 'medium', 'high', 'critical']),
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
            'description' => fake()->sentence(),
            'metadata' => ['attempt_count' => fake()->numberBetween(1, 5)],
        ];
    }
}
