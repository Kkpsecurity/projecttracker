<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ImprovePlotAddressesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add improvements to plot_addresses table if needed
        if (Schema::hasTable('plot_addresses')) {
            Schema::table('plot_addresses', function (Blueprint $table) {
                // Add any missing columns or indexes
                if (!Schema::hasColumn('plot_addresses', 'status')) {
                    $table->string('status')->default('active')->after('updated_at');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('plot_addresses')) {
            Schema::table('plot_addresses', function (Blueprint $table) {
                if (Schema::hasColumn('plot_addresses', 'status')) {
                    $table->dropColumn('status');
                }
            });
        }
    }
}
