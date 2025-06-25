<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddConsultantNotesTohb837 extends Migration
{
    public function up()
    {
        Schema::table('hb837', function (Blueprint $table) {
            $table->text('consultant_notes')->nullable()->after('notes');
        });
    }

    public function down()
    {
        Schema::table('hb837', function (Blueprint $table) {
            $table->dropColumn('consultant_notes');
        });
    }
}
