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
        Schema::create('credential_accesses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('credential_id')->constrained('credentials')->onDelete('cascade');
            $table->foreignId('profile_id')->nullable()->constrained('profiles')->onDelete('cascade');
            $table->foreignId('user_group_id')->nullable()->constrained('user_groups')->onDelete('cascade');
            $table->string('access_level');
            $table->foreignId('created_by')->constrained('profiles')->onDelete('cascade');
            $table->foreignId('updated_by')->constrained('profiles')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credential_accesses');
    }
};
