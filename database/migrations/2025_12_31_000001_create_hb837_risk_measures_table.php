<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hb837_risk_measures', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hb837_id');
            $table->unsignedBigInteger('created_by')->nullable();

            // 4.1 .. 4.6 (stored as string to keep the label stable)
            $table->string('section', 10);

            // Optional explicit numbering, otherwise derived by ordering.
            $table->unsignedInteger('measure_no')->nullable();

            // CB1..CB4
            $table->string('cb_rank', 5)->nullable();

            $table->text('measure');
            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();

            $table->index(['hb837_id', 'section', 'sort_order']);

            $table->foreign('hb837_id')->references('id')->on('hb837')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hb837_risk_measures');
    }
};
