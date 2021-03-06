<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBuildingsAccommodationTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('buildings_accommodation', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('pilgrim_id')->unsigned();
            $table->integer('building_floor_id')->unsigned();
            $table->boolean('type');
            $table->foreign('pilgrim_id')->references('id')->on('pilgrims');
            $table->foreign('building_floor_id')->references('id')->on('buildings_floors');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('buildings_accommodation');
    }

}
