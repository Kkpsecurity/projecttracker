<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, update any existing 'in-progress' values to 'underway'
        DB::statement("UPDATE hb837 SET report_status = 'underway' WHERE report_status = 'in-progress'");
        
        // Drop the old constraint
        DB::statement('ALTER TABLE hb837 DROP CONSTRAINT IF EXISTS hb837_report_status_check');
        
        // Add the new constraint with 'underway' instead of 'in-progress'
        DB::statement("ALTER TABLE hb837 ADD CONSTRAINT hb837_report_status_check CHECK (report_status IN ('not-started', 'underway', 'in-review', 'completed'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert any 'underway' values back to 'in-progress'
        DB::statement("UPDATE hb837 SET report_status = 'in-progress' WHERE report_status = 'underway'");
        
        // Drop the current constraint
        DB::statement('ALTER TABLE hb837 DROP CONSTRAINT IF EXISTS hb837_report_status_check');
        
        // Restore the old constraint with 'in-progress'
        DB::statement("ALTER TABLE hb837 ADD CONSTRAINT hb837_report_status_check CHECK (report_status IN ('not-started', 'in-progress', 'in-review', 'completed'))");
    }
};
