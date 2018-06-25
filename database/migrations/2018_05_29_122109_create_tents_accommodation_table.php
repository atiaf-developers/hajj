<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTentsAccommodationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tents_accommodation', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('pilgrim_id')->unsigned();
            $table->integer('tent_id')->unsigned();
            $table->foreign('pilgrim_id')->references('id')->on('pilgrims');
            $table->foreign('tent_id')->references('id')->on('tents');
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
        Schema::dropIfExists('tents_accommodation');
    }
}
