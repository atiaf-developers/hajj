<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBuildingsFloorsRoomsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('buildings_floors_rooms', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('number');
            $table->integer('available_of_accommodation');
            $table->integer('remaining_available_of_accommodation');
            $table->integer('building_floor_id')->unsigned();
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
        Schema::dropIfExists('buildings_floors_rooms');
    }

}
