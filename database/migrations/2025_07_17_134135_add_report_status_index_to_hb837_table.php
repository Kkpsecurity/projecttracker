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
            // Add index for report_status column for better query performance
            // This is done separately to avoid conflicts with enum constraints
            $table->index('report_status', 'idx_hb837_report_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hb837', function (Blueprint $table) {
            // Drop the report_status index
            $table->dropIndex('idx_hb837_report_status');
        });
    }
};
