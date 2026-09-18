<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * The role_permissions table no longer relies on an `is_active` flag to
     * determine whether a permission is assigned to a role. Instead, a
     * role_permission record's existence represents an active assignment:
     * adding a permission creates a record, removing it deletes the record.
     */
    public function up(): void
    {
        Schema::table('role_permissions', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('role_permissions', function (Blueprint $table) {
            $table->boolean('is_active')->default(true);
        });
    }
};
