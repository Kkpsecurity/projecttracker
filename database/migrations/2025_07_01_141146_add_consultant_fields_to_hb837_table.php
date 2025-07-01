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
            $table->text('consultant_notes')->nullable()->after('notes');
            $table->string('assigned_consultant')->nullable()->after('assigned_consultant_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hb837', function (Blueprint $table) {
            $table->dropColumn('consultant_notes');
            $table->dropColumn('assigned_consultant');
        });
    }
};
