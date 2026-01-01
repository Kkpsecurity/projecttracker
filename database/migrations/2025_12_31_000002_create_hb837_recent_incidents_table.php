<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hb837_recent_incidents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hb837_id');
            $table->unsignedBigInteger('created_by')->nullable();

            // Template allows flexible labels (e.g., "Summer 2025", "2024-2025", "N/A").
            $table->string('incident_date', 60)->nullable();

            // Short narrative description of event and outcome.
            $table->text('summary');

            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['hb837_id', 'sort_order']);

            $table->foreign('hb837_id')->references('id')->on('hb837')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hb837_recent_incidents');
    }
};
