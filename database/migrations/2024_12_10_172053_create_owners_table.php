<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOwnersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('owners', function (Blueprint $table) {
            $table->id();

            $table->string('name')->nullable(); // Owner's name
            $table->string('email')->nullable(); // Email for contact
            $table->string('phone', 15)->nullable(); // Phone number
            $table->string('address')->nullable(); // Address
            $table->string('city')->nullable(); // City
            $table->string('state', 2)->nullable(); // State abbreviation
            $table->string('zip', 10)->nullable(); // ZIP code
            $table->string('company_name')->nullable(); // If the owner is a company
            $table->string('tax_id')->nullable(); // Tax Identification Number

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
        Schema::dropIfExists('owners');
    }
}
