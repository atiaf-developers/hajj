<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePilgrimsAccommodationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pilgrims_accommodation', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('pilgrim_id')->unsigned();
            $table->integer('lounge_id')->unsigned();
            $table->boolean('type');
            $table->foreign('pilgrim_id')->references('id')->on('pilgrims');
            $table->foreign('lounge_id')->references('id')->on('lounges');
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
        Schema::dropIfExists('pilgrims_accommodation');
    }
}
