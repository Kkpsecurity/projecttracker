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
        // Drop foreign key constraint from hb837 table if it exists
        Schema::table('hb837', function (Blueprint $table) {
            $table->dropForeign(['owner_id']);
            $table->dropColumn('owner_id');
        });

        // Drop the owners table
        Schema::dropIfExists('owners');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate owners table structure (basic)
        Schema::create('owners', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip')->nullable();
            $table->string('company_name')->nullable();
            $table->string('tax_id')->nullable();
            $table->timestamps();
        });

        // Recreate foreign key in hb837 table
        Schema::table('hb837', function (Blueprint $table) {
            $table->unsignedBigInteger('owner_id')->nullable()->after('management_company');
            $table->foreign('owner_id')->references('id')->on('owners')->onDelete('set null');
        });
    }
};
