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
        Schema::create('clients', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('client_name')->nullable(false);
            $table->string('project_name')->nullable(false);
            $table->text('poc')->nullable();
            $table->text('status')->nullable();
            $table->text('quick_status')->nullable();
            $table->text('description')->nullable();
            $table->text('corporate_name')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->string('file1')->nullable();
            $table->string('file2')->nullable();
            $table->string('file3')->nullable();
            $table->double('project_services_total')->nullable();
            $table->double('project_expenses_total')->nullable();
            $table->double('final_services_total')->nullable();
            $table->double('final_billing_total')->nullable();
            
            // Add indexes for common searches
            $table->index('client_name');
            $table->index('project_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
