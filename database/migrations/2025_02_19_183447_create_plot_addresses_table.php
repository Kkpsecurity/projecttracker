<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlotAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('plots',     function (Blueprint $table) {
            $table->id();
            $table->string('plot_name');
            $table->timestamps();
        });

        Schema::create('plot_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plot_id')->constrained()->onDelete('cascade');
            $table->string('latitude');
            $table->string('longitude');
            $table->string('location_name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('plots');
        Schema::dropIfExists('plot_addresses');
    }
}
