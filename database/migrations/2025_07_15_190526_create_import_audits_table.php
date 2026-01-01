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
        Schema::create('import_audits', function (Blueprint $table) {
            $table->id();
            $table->string('import_id')->index(); // UUID or identifier for the import batch
            $table->string('type')->index(); // Type of audit: backup, import, etc.
            $table->json('changes')->nullable(); // JSON data containing audit details
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            
            // Indexes for performance
            $table->index('created_at');
            $table->index(['type', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('import_audits');
    }
};
