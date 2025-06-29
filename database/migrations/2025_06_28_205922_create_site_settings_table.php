<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSiteSettingsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('site_settings')) {
            Schema::create('site_settings', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique();
                $table->text('value')->nullable();
                $table->string('type')->default('text'); // text, boolean, number, json, file
                $table->string('group')->default('general'); // general, appearance, email, api, system
                $table->string('label')->nullable();
                $table->text('description')->nullable();
                $table->boolean('is_public')->default(false);
                $table->timestamps();

                // Indexes
                $table->index(['group', 'key']);
                $table->index('is_public');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
}
