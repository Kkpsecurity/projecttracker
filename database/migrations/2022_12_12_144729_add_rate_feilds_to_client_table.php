<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRateFeildsToClientTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table(
            'clients',
            function (Blueprint $table) {
                $table->float('project_services_total', 11, 2)->nullable();
                $table->float('project_expenses_total', 11, 2)->nullable();
                $table->float('final_services_total', 11, 2)->nullable();
                $table->float('final_billing_total', 11, 2)->nullable();
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('client', function (Blueprint $table) {
            $table->dropColumn('project_services_total');
            $table->dropColumn('project_expenses_total');
            $table->dropColumn('final_services_total');
            $table->dropColumn('final_billing_total');
        });
    }
}
