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
        Schema::create('hb837_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hb837_id')->constrained('hb837')->onDelete('cascade');
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('filename');
            $table->string('original_filename');
            $table->string('file_path');
            $table->string('mime_type')->nullable();
            $table->bigInteger('file_size')->nullable();
            $table->string('file_category')->nullable(); // report, contract, assessment, etc.
            $table->text('description')->nullable();
            $table->timestamps();

            // Indexes for file management
            $table->index('hb837_id');
            $table->index('uploaded_by');
            $table->index('file_category');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hb837_files');
    }
};
