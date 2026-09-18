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
        Schema::create('credentials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_profile_id')->constrained('profiles');
            $table->string('name');
            $table->string('username');
            $table->string('secret');
            $table->string('category')->nullable();
            $table->json('properties')->nullable();
            $table->boolean('is_favorite')->default(false);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('profiles');
            $table->foreignId('updated_by')->nullable()->constrained('profiles');
            $table->foreignId('deleted_by')->nullable()->constrained('profiles');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credentials');
    }
};
