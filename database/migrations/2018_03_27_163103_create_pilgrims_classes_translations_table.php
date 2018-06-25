<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePilgrimsClassesTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pilgrims_class_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('locale');
            $table->string('title',255);

            $table->integer('pilgrims_class_id')->unsigned();
            $table->foreign('pilgrims_class_id')->references('id')->on('pilgrims_class');

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
        Schema::dropIfExists('pilgrims_class_translations');
    }
}
