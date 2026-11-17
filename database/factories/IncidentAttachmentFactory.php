<?php

namespace Database\Factories;

use App\Models\Incident;
use App\Models\IncidentAttachment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<IncidentAttachment>
 */
class IncidentAttachmentFactory extends Factory
{
    protected $model = IncidentAttachment::class;

    public function definition(): array
    {
        return [
            'incident_id' => Incident::factory(),
            'uploaded_by' => User::factory(),
            'original_name' => fake()->word() . '.pdf',
            'storage_path' => 'attachments/' . Str::random(40) . '.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => fake()->numberBetween(1024, 10485760),
            'file_hash' => hash('sha256', Str::random(32)),
        ];
    }
}
