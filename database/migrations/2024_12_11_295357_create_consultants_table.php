<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreateConsultantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('consultants', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('dba_company_name')->nullable();
            $table->string('mailing_address')->nullable();
            $table->date('fcp_expiration_date')->nullable();
            $table->string('assigned_light_meter')->nullable();
            $table->date('lm_nist_expiration_date')->nullable();
            $table->decimal('subcontractor_bonus_rate', 8, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('consultant_files', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('consultant_id');
            $table->string('file_type'); // Keeping only one file_type column
            $table->string('original_filename');
            $table->string('file_path');
            $table->integer('file_size')->nullable();
            $table->timestamps();

            $table->foreign('consultant_id')
                ->references('id')
                ->on('consultants')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop child table first
        Schema::dropIfExists('consultant_files');
        // Drop parent table
        Schema::dropIfExists('consultants');
    }
}
