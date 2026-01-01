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
        Schema::table('hb837_files', function (Blueprint $table) {
            $table->string('file_position')->nullable()->after('file_category');
            $table->index('file_position');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hb837_files', function (Blueprint $table) {
            $table->dropIndex(['file_position']);
            $table->dropColumn('file_position');
        });
    }
};
