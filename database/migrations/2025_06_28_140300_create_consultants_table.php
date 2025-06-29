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
        Schema::create('consultants', function (Blueprint $table) {
            $table->id();

            // Basic consultant information
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('dba_company_name')->nullable();
            $table->text('mailing_address')->nullable();

            // Professional certifications and equipment
            $table->date('fcp_expiration_date')->nullable()->comment('FCP certification expiry');
            $table->string('assigned_light_meter')->nullable()->comment('Equipment assignment');
            $table->date('lm_nist_expiration_date')->nullable()->comment('Light meter calibration expiry');

            // Financial information
            $table->decimal('subcontractor_bonus_rate', 8, 2)->nullable()->comment('Hourly or project rate');

            // Additional notes
            $table->text('notes')->nullable();

            $table->timestamps();

            // Indexes for performance
            $table->index(['first_name', 'last_name']);
            $table->index('email');
            $table->index('fcp_expiration_date');
            $table->index('lm_nist_expiration_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consultants');
    }
};
