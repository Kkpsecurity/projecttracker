<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hb837_findings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hb837_id')->constrained('hb837')->onDelete('cascade');

            // Optional link to a map plot/pin
            $table->foreignId('plot_id')->nullable()->constrained('plots')->nullOnDelete();

            // Optional attribution
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            // Core structured fields
            $table->string('category')->nullable();
            $table->string('severity')->nullable();
            $table->string('location_context')->nullable();
            $table->text('description')->nullable();
            $table->text('recommendation')->nullable();

            // Workflow/status (kept flexible for MVP)
            $table->string('status')->nullable();

            // Store the source of the finding (manual entry vs llm extraction, etc.)
            $table->string('source')->nullable();

            $table->timestamps();

            $table->index(['hb837_id', 'created_at']);
            $table->index('plot_id');
            $table->index('category');
            $table->index('severity');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hb837_findings');
    }
};
