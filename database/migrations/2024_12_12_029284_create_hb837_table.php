<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHb837Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {Schema::dropIfExists('hb837');
        Schema::create('hb837', function (Blueprint $table) {
            $table->id();

            // Foreign key relationships
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->unsignedBigInteger('assigned_consultant_id')->nullable();

            /**
             * Owner Id
             * @deprecated message="Owner ID is deprecated and will be removed in a future release."
             */
            $table->unsignedBigInteger('owner_id')->nullable();
            $table->string('owner_name')->nullable();

            // HB837-specific fields
            $table->string('management_company')->nullable();
            $table->string('property_name')->nullable();
            $table->enum('property_type', config('hb837.property_types'))->nullable();
            $table->integer('units')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('county')->nullable();
            $table->string('state', 2)->nullable();
            $table->string('zip', 10)->nullable();
            $table->string('phone', 15)->nullable();

            // Inspection details
            $table->date('scheduled_date_of_inspection')->nullable();
            $table->date('report_submitted')->nullable();
            $table->enum('report_status', [config('hb837.report_statuses')])->nullable();
            $table->enum('securitygauge_crime_risk', [config('hb837.security_gauge')])->nullable();

            // Contact details
            $table->string('property_manager_name')->nullable();
            $table->string('property_manager_email')->nullable();

            // Regional details
            $table->string('regional_manager_name')->nullable();
            $table->string('regional_manager_email')->nullable();

            // Agreement and contracting details
            $table->date('agreement_submitted')->nullable();
            $table->enum('contracting_status', [config('hb837.contracting_statuses')])->nullable();

            // Financial data
            $table->float('quoted_price', 11, 2)->nullable();
            $table->float('sub_fees_estimated_expenses', 11, 2)->nullable();
            $table->float('project_net_profit', 11, 2)->nullable();
            $table->date('billing_req_sent')->nullable();
            $table->text('financial_notes')->nullable();

            // Macro details
            $table->string('macro_client')->nullable();
            $table->string('macro_contact')->nullable();
            $table->string('macro_email')->nullable();

            // notes
            $table->text('notes')->nullable();

            $table->timestamps();

            // Foreign key constraints
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('set null');
            $table->foreign('assigned_consultant_id')->references('id')->on('consultants')->onDelete('set null');
            $table->foreign('owner_id')->references('id')->on('owners')->onDelete('set null');
        });

        Schema::create('hb837_files', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('hb837_id');
            $table->string('filename');
            $table->string('file_type');
            $table->string('original_filename');
            $table->string('file_path');
            $table->integer('file_size')->nullable();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('hb837_id')->references('id')->on('hb837')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hb837_files');
        Schema::dropIfExists('hb837');
    }
}
