<?php

namespace Database\Factories;

use App\Models\IncidentCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<IncidentCategory>
 */
class IncidentCategoryFactory extends Factory
{
    protected $model = IncidentCategory::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'name' => ucfirst($name),
            'slug' => Str::slug($name) . '-' . fake()->unique()->numerify('###'),
            'description' => fake()->sentence(),
            'is_active' => true,
        ];
    }
}
