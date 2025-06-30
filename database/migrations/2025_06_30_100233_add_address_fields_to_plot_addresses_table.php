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
            // Add the missing address fields that the seeder expects
            $table->string('street_address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip_code')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plot_addresses', function (Blueprint $table) {
            $table->dropColumn([
                'street_address',
                'city',
                'state',
                'zip_code'
            ]);
        });
    }
};
