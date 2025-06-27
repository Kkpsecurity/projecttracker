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
        Schema::create('plots', function (Blueprint $table) {
            $table->id();
            $table->string('plot_name');
            $table->text('description')->nullable();
            $table->enum('plot_type', ['custom', 'prospect', 'client'])->default('custom');
            $table->string('client_contact_name')->nullable();
            $table->string('client_contact_email')->nullable();
            $table->string('client_contact_phone')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['plot_type', 'is_active']);
            $table->index('plot_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plots');
    }
};
