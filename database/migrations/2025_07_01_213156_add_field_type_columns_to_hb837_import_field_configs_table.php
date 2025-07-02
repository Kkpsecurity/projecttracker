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
        Schema::table('hb837_import_field_configs', function (Blueprint $table) {
            $table->boolean('is_config_field')->default(false)->after('is_system_field');
            $table->boolean('is_custom_field')->default(false)->after('is_config_field');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hb837_import_field_configs', function (Blueprint $table) {
            $table->dropColumn(['is_config_field', 'is_custom_field']);
        });
    }
};
