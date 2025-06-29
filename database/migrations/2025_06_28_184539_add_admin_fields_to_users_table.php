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
        Schema::table('users', function (Blueprint $table) {
            // Admin/User Management fields
            $table->boolean('is_admin')->default(false)->after('remember_token');
            $table->boolean('is_active')->default(true)->after('is_admin');
            $table->string('avatar')->nullable()->after('is_active');
            $table->string('phone')->nullable()->after('avatar');
            $table->text('bio')->nullable()->after('phone');
            $table->json('preferences')->nullable()->after('bio');
            $table->boolean('email_verified')->default(false)->after('preferences');
            $table->boolean('two_factor_enabled')->default(false)->after('email_verified');
            $table->timestamp('password_changed_at')->nullable()->after('two_factor_enabled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop admin fields in reverse order
            $table->dropColumn([
                'password_changed_at',
                'two_factor_enabled',
                'email_verified',
                'preferences',
                'bio',
                'phone',
                'avatar',
                'is_active',
                'is_admin'
            ]);
        });
    }
};
