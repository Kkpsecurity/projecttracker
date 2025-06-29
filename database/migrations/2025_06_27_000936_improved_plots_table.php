<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ImprovedPlotsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if plots table exists and add any missing columns
        if (Schema::hasTable('plots')) {
            Schema::table('plots', function (Blueprint $table) {
                // Add any additional columns that might be needed
                if (!Schema::hasColumn('plots', 'status')) {
                    $table->string('status')->default('active')->after('updated_at');
                }
                if (!Schema::hasColumn('plots', 'notes')) {
                    $table->text('notes')->nullable()->after('status');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('plots')) {
            Schema::table('plots', function (Blueprint $table) {
                if (Schema::hasColumn('plots', 'status')) {
                    $table->dropColumn('status');
                }
                if (Schema::hasColumn('plots', 'notes')) {
                    $table->dropColumn('notes');
                }
            });
        }
    }
}
