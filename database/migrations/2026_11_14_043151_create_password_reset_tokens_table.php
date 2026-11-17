<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // NOTE: Intentionally empty. The `password_reset_tokens` table is already
        // created by the base Laravel migration 0001_01_01_000000_create_users_table.php.
        // This migration exists for workflow consistency (same class-naming as siblings)
        // but must remain empty to avoid duplicate-table errors.
    }

    public function down(): void
    {
        // NOTE: Intentionally empty. No-op since the table is created by base Laravel migration.
    }
};
