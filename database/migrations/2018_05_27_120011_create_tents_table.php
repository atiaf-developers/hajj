<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTentsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('tents', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('number');
            $table->integer('available_of_accommodation');
            $table->integer('remaining_available_of_accommodation');
            $table->boolean('gender');
            $table->boolean('type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('tents');
    }

}
