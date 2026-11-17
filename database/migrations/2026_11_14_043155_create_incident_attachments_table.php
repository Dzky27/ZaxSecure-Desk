<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('incident_attachments')) {
            Schema::create('incident_attachments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('incident_id')->constrained('incidents')->cascadeOnDelete();
                $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
                $table->string('original_name');
                $table->string('storage_path');
                $table->string('mime_type')->nullable();
                $table->unsignedBigInteger('file_size');
                $table->string('file_hash', 64)->nullable();
                $table->timestamps();

                $table->index('incident_id');
                $table->index('uploaded_by');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('incident_attachments');
    }
};
