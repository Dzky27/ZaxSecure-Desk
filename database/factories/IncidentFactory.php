<?php

namespace Database\Factories;

use App\Models\Incident;
use App\Models\IncidentCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Incident>
 */
class IncidentFactory extends Factory
{
    protected $model = Incident::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ticket_number' => 'INC-' . date('Ymd') . '-' . strtoupper(Str::random(4)) . fake()->unique()->numerify('###'),
            'user_id' => User::factory(),
            'assigned_to' => null,
            'category_id' => IncidentCategory::factory(),
            'title' => fake()->sentence(5),
            'description' => fake()->paragraph(),
            'priority' => fake()->randomElement(['low', 'medium', 'high', 'critical']),
            'status' => fake()->randomElement(['pending', 'investigating', 'in_progress', 'resolved', 'rejected']),
            'occurred_at' => fake()->dateTimeBetween('-30 days', 'now'),
            'related_url' => fake()->optional()->url(),
            'resolved_at' => null,
        ];
    }
}
