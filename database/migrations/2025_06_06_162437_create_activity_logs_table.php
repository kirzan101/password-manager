<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->string('module')->nullable();
            $table->string('description')->nullable(); // e.g., "User created", "Profile updated"
            $table->string('status')->nullable(); // e.g., pending, completed, failed
            $table->string('type'); // e.g., create, update, delete
            $table->json('properties')->nullable(); // JSON to store additional properties
            $table->json('old_properties')->nullable(); // JSON to store old properties before the change
            $table->foreignId('processed_by')->nullable()->constrained('profiles')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
