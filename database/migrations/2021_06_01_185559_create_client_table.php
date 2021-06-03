<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('client_name')->index('client_name_idx');
            $table->string('project_name')->index('project_name_idx');
            $table->text('poc')->nullable();
            $table->text('status')->nullable();
            $table->text('quick_status')->nullable();
            $table->text('description')->nullable();
            $table->text('corporate_name')->nullable();

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
        Schema::dropIfExists('clients');
    }
}
