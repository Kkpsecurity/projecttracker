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
        Schema::table('hb837', function (Blueprint $table) {
            $table->date('billing_req_submitted')->nullable()->after('billing_req_sent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hb837', function (Blueprint $table) {
            $table->dropColumn('billing_req_submitted');
        });
    }
};
