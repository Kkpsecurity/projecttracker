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
        Schema::table('plot_addresses', function (Blueprint $table) {
            // Add country column if it doesn't exist
            if (!Schema::hasColumn('plot_addresses', 'country')) {
                $table->string('country')->default('US')->after('zip_code');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plot_addresses', function (Blueprint $table) {
            if (Schema::hasColumn('plot_addresses', 'country')) {
                $table->dropColumn('country');
            }
        });
    }
};
