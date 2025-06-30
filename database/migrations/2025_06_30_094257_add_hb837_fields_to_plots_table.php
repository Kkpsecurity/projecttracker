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
        Schema::table('plots', function (Blueprint $table) {
            $table->unsignedBigInteger('hb837_id')->nullable();
            $table->string('lot_number')->nullable();
            $table->string('block_number')->nullable();
            $table->string('subdivision_name')->nullable();
            $table->decimal('coordinates_latitude', 10, 7)->nullable();
            $table->decimal('coordinates_longitude', 10, 7)->nullable();

            // Foreign key constraint
            $table->foreign('hb837_id')->references('id')->on('hb837')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plots', function (Blueprint $table) {
            $table->dropForeign(['hb837_id']);
            $table->dropColumn([
                'hb837_id',
                'lot_number',
                'block_number',
                'subdivision_name',
                'coordinates_latitude',
                'coordinates_longitude'
            ]);
        });
    }
};
