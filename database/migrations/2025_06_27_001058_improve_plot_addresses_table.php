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
        Schema::table('plot_addresses', function (Blueprint $table) {
            // Change latitude and longitude to decimal for better precision
            $table->decimal('latitude', 10, 8)->change();
            $table->decimal('longitude', 11, 8)->change();

            // Add additional address fields for better property management
            $table->string('street_address')->nullable()->after('location_name');
            $table->string('city')->nullable()->after('street_address');
            $table->string('state')->nullable()->after('city');
            $table->string('zip_code')->nullable()->after('state');
            $table->string('country')->default('USA')->after('zip_code');

            // Add property details
            $table->text('description')->nullable()->after('country');
            $table->string('property_type')->nullable()->after('description'); // residential, commercial, industrial, etc.
            $table->decimal('property_value', 12, 2)->nullable()->after('property_type');
            $table->decimal('square_footage', 10, 2)->nullable()->after('property_value');

            // Add status and metadata
            $table->enum('status', ['active', 'inactive', 'pending', 'sold'])->default('active')->after('square_footage');
            $table->json('metadata')->nullable()->after('status'); // For storing custom fields
            $table->timestamp('verified_at')->nullable()->after('metadata');
            $table->unsignedBigInteger('verified_by')->nullable()->after('verified_at');

            // Add indexes for better performance
            $table->index(['latitude', 'longitude'], 'coordinates_index');
            $table->index(['city', 'state'], 'location_index');
            $table->index('status');
            $table->index('property_type');
            $table->index('verified_at');

            // Add foreign key for verified_by if users table exists
            // $table->foreign('verified_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plot_addresses', function (Blueprint $table) {
            // Remove indexes
            $table->dropIndex('coordinates_index');
            $table->dropIndex('location_index');
            $table->dropIndex(['status']);
            $table->dropIndex(['property_type']);
            $table->dropIndex(['verified_at']);

            // Remove added columns
            $table->dropColumn([
                'street_address',
                'city',
                'state',
                'zip_code',
                'country',
                'description',
                'property_type',
                'property_value',
                'square_footage',
                'status',
                'metadata',
                'verified_at',
                'verified_by',
            ]);

            // Revert latitude and longitude to string
            $table->string('latitude')->change();
            $table->string('longitude')->change();
        });
    }
};
