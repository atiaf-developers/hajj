<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBusesAccommodationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('buses_accommodation', function (Blueprint $table) {
               $table->increments('id');
            $table->integer('pilgrim_id')->unsigned();
            $table->integer('pilgrim_bus_id')->unsigned();
            $table->foreign('pilgrim_id')->references('id')->on('pilgrims');
            $table->foreign('pilgrim_bus_id')->references('id')->on('pilgrims_buses');
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
        Schema::dropIfExists('buses_accommodation');
    }
}
