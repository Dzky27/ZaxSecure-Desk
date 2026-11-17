<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('incident_updates')) {
            Schema::create('incident_updates', function (Blueprint $table) {
                $table->id();
                $table->foreignId('incident_id')->constrained('incidents')->cascadeOnDelete();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('old_status')->nullable();
                $table->string('new_status')->nullable();
                $table->text('message')->nullable();
                $table->enum('visibility', ['public', 'internal'])->default('public');
                $table->timestamps();

                $table->index('incident_id');
                $table->index('user_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('incident_updates');
    }
};
