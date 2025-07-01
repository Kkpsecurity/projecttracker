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
            // Add module-related metadata fields
            $table->string('import_session_id')->nullable()->index()->after('user_id');
            $table->string('import_source')->default('manual')->after('import_session_id');
            $table->timestamp('last_import_at')->nullable()->after('import_source');
            $table->json('import_metadata')->nullable()->after('last_import_at');

            // Add fields that might be missing from original migration
            if (!Schema::hasColumn('hb837', 'macro_client')) {
                $table->string('macro_client')->nullable()->after('notes');
            }
            if (!Schema::hasColumn('hb837', 'macro_contact')) {
                $table->string('macro_contact')->nullable()->after('macro_client');
            }
            if (!Schema::hasColumn('hb837', 'macro_email')) {
                $table->string('macro_email')->nullable()->after('macro_contact');
            }
            if (!Schema::hasColumn('hb837', 'property_manager_name')) {
                $table->string('property_manager_name')->nullable()->after('notes');
            }
            if (!Schema::hasColumn('hb837', 'property_manager_email')) {
                $table->string('property_manager_email')->nullable()->after('property_manager_name');
            }
            if (!Schema::hasColumn('hb837', 'regional_manager_name')) {
                $table->string('regional_manager_name')->nullable()->after('property_manager_email');
            }
            if (!Schema::hasColumn('hb837', 'regional_manager_email')) {
                $table->string('regional_manager_email')->nullable()->after('regional_manager_name');
            }
            if (!Schema::hasColumn('hb837', 'financial_notes')) {
                $table->text('financial_notes')->nullable()->after('notes');
            }
            if (!Schema::hasColumn('hb837', 'securitygauge_crime_risk')) {
                $table->string('securitygauge_crime_risk')->nullable()->after('notes');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hb837', function (Blueprint $table) {
            // Remove module-related metadata fields
            $table->dropColumn([
                'import_session_id',
                'import_source',
                'last_import_at',
                'import_metadata'
            ]);

            // Note: We don't remove the other fields as they might be needed
            // by the existing system. Only remove module-specific metadata.
        });
    }
};
