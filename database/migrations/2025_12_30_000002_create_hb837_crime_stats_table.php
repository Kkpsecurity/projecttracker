<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hb837_crime_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hb837_id')->constrained('hb837')->onDelete('cascade');

            // Optional pointer to the source file upload (SecurityGauge PDF) if present
            $table->foreignId('hb837_file_id')->nullable()->constrained('hb837_files')->nullOnDelete();

            // Time window / metadata
            $table->string('source')->nullable(); // e.g. SecurityGauge
            $table->string('report_title')->nullable();
            $table->date('period_start')->nullable();
            $table->date('period_end')->nullable();

            // High-level summary fields (keep flexible; we will refine in Phase 3)
            $table->string('crime_risk')->nullable();

            // Structured payload (tables/metrics extracted from PDF)
            $table->json('stats')->nullable();

            // Human review fields
            $table->boolean('is_reviewed')->default(false);
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();

            $table->timestamps();

            $table->unique('hb837_id');
            $table->index('hb837_file_id');
            $table->index('is_reviewed');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hb837_crime_stats');
    }
};
