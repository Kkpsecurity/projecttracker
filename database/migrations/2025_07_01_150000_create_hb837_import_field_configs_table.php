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
        Schema::create('hb837_import_field_configs', function (Blueprint $table) {
            $table->id();

            // Field identification
            $table->string('database_field')->unique();
            $table->string('field_label');
            $table->text('description')->nullable();

            // Field properties
            $table->enum('field_type', [
                'string', 'text', 'integer', 'decimal', 'date',
                'boolean', 'enum', 'foreign_key'
            ]);
            $table->integer('max_length')->nullable();
            $table->boolean('nullable')->default(true);
            $table->string('default_value')->nullable();

            // Database schema properties
            $table->boolean('is_foreign_key')->default(false);
            $table->string('foreign_table')->nullable();
            $table->string('foreign_key_column')->nullable();
            $table->boolean('is_system_field')->default(false); // Prevents deletion

            // Excel mapping
            $table->json('excel_column_mappings'); // Array of possible Excel column names

            // Validation rules
            $table->json('validation_rules')->nullable();
            $table->json('enum_values')->nullable(); // For enum fields

            // Data transformation
            $table->string('transformation_type')->nullable(); // date, money, phone, etc.
            $table->json('transformation_options')->nullable();

            // Import behavior
            $table->boolean('is_required_for_import')->default(false);
            $table->boolean('is_updatable')->default(true);
            $table->boolean('is_creatable')->default(true);

            // Status and metadata
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->json('metadata')->nullable(); // Additional configuration

            $table->timestamps();

            // Indexes
            $table->index('field_type');
            $table->index('is_active');
            $table->index('is_foreign_key');
            $table->index('is_system_field');
            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hb837_import_field_configs');
    }
};
