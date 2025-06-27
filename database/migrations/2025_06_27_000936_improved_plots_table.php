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
        Schema::table('plots', function (Blueprint $table) {
            // Add new columns to existing plots table
            $table->text('description')->nullable()->after('plot_name');
            $table->enum('plot_type', ['custom', 'prospect', 'client'])->default('custom')->after('description');
            $table->string('client_contact_name')->nullable()->after('plot_type');
            $table->string('client_contact_email')->nullable()->after('client_contact_name');
            $table->string('client_contact_phone')->nullable()->after('client_contact_email');
            $table->boolean('is_active')->default(true)->after('client_contact_phone');
            
            // Add indexes for performance
            $table->index(['plot_type', 'is_active']);
            $table->index('plot_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plots', function (Blueprint $table) {
            // Remove indexes
            $table->dropIndex(['plot_type', 'is_active']);
            $table->dropIndex(['plot_name']);
            
            // Remove added columns
            $table->dropColumn([
                'description',
                'plot_type',
                'client_contact_name',
                'client_contact_email',
                'client_contact_phone',
                'is_active'
            ]);
        });
    }
};
