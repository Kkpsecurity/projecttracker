<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hb837_statute_conditions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hb837_id');
            $table->unsignedBigInteger('created_by')->nullable();

            // Stable key for each required statute condition (e.g. "cctv_system").
            $table->string('condition_key', 64);

            // e.g. compliant | non_compliant | unknown
            $table->string('status', 32)->nullable();

            $table->text('observations')->nullable();
            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();

            $table->unique(['hb837_id', 'condition_key']);
            $table->index(['hb837_id', 'sort_order']);

            $table->foreign('hb837_id')->references('id')->on('hb837')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hb837_statute_conditions');
    }
};
