<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSuitesLoungeTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('suites_lounge', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('number');
            $table->integer('available_of_accommodation');
            $table->boolean('gender');
            $table->integer('suite_id')->unsigned();
            $table->foreign('suite_id')->references('id')->on('suites');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('suites_lounge');
    }

}
