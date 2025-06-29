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
        Schema::create('hb837', function (Blueprint $table) {
            $table->id();

            // Foreign key relationships
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('assigned_consultant_id')->nullable()->constrained('consultants')->onDelete('set null');

            // Property owner information
            $table->unsignedBigInteger('owner_id')->nullable();
            $table->string('owner_name')->nullable();

            // Property information
            $table->string('property_name')->nullable();
            $table->enum('property_type', config('hb837.property_types', ['garden', 'midrise', 'highrise', 'industrial', 'bungalo']))->nullable();
            $table->integer('units')->nullable();
            $table->string('management_company')->nullable();

            // Property address
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('county')->nullable();
            $table->string('state', 2)->nullable();
            $table->string('zip', 10)->nullable();
            $table->string('phone', 15)->nullable();

            // Project workflow and dates
            $table->enum('report_status', config('hb837.report_statuses', ['not-started', 'in-progress', 'in-review', 'completed']))->nullable();
            $table->enum('contracting_status', config('hb837.contracting_statuses', ['quoted', 'started', 'executed', 'closed']))->nullable();
            $table->date('scheduled_date_of_inspection')->nullable();
            $table->date('report_submitted')->nullable();
            $table->date('billing_req_sent')->nullable();
            $table->date('agreement_submitted')->nullable();

            // Financial information
            $table->decimal('quoted_price', 10, 2)->nullable();
            $table->decimal('sub_fees_estimated_expenses', 10, 2)->nullable();
            $table->decimal('project_net_profit', 10, 2)->nullable();
            $table->text('financial_notes')->nullable();

            // Security assessment
            $table->string('securitygauge_crime_risk')->nullable();
            $table->text('notes')->nullable();

            // Contact management
            $table->string('property_manager_name')->nullable();
            $table->string('property_manager_email')->nullable();
            $table->string('regional_manager_name')->nullable();
            $table->string('regional_manager_email')->nullable();

            // Macro client (parent company) information
            $table->string('macro_client')->nullable();
            $table->string('macro_contact')->nullable();
            $table->string('macro_email')->nullable();

            $table->timestamps();

            // Indexes for performance and common queries
            $table->index('report_status');
            $table->index('contracting_status');
            $table->index('assigned_consultant_id');
            $table->index('scheduled_date_of_inspection');
            $table->index('property_name');
            $table->index('county');
            $table->index('macro_client');
            $table->index(['report_status', 'contracting_status']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hb837');
    }
};
