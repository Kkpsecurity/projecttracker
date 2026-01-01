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
        Schema::table('hb837', function (Blueprint $table) {
            // Remove the redundant assigned_consultant name field
            // We'll use the relationship to get consultant names instead
            $table->dropColumn('assigned_consultant');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hb837', function (Blueprint $table) {
            // Re-add the field if we need to rollback
            $table->string('assigned_consultant')->nullable();
        });
    }
};
