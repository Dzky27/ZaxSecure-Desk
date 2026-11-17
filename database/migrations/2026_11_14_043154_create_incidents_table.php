<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('incidents')) {
            Schema::create('incidents', function (Blueprint $table) {
                $table->id();
                $table->string('ticket_number')->unique();
                $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
                $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('category_id')->constrained('incident_categories')->restrictOnDelete();
                $table->string('title');
                $table->text('description');
                $table->enum('priority', ['low', 'medium', 'high', 'critical'])->index();
                $table->enum('status', ['pending', 'investigating', 'in_progress', 'resolved', 'rejected'])->default('pending')->index();
                $table->dateTime('occurred_at')->nullable();
                $table->string('related_url')->nullable();
                $table->dateTime('resolved_at')->nullable();
                $table->timestamps();

                $table->index('user_id');
                $table->index('assigned_to');
                $table->index('category_id');
                $table->index('created_at');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('incidents');
    }
};
