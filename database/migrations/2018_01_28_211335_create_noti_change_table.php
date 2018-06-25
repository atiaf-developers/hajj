<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotiChangeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('noti_change', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('noti_object_id')->unsigned();
            $table->integer('actor_id')->unsigned();
            $table->foreign('noti_object_id')->references('id')->on('noti_object');
            $table->foreign('actor_id')->references('id')->on('users');
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
        Schema::dropIfExists('noti_change');
    }
}
