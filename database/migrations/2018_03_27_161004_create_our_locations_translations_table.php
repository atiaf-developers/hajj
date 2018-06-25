<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOurLocationsTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('our_locations_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('locale');
            $table->string('title',255);
            $table->text('address');

            $table->integer('our_location_id')->unsigned();
            $table->foreign('our_location_id')->references('id')->on('our_locations');


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
        Schema::dropIfExists('our_locations_translations');
    }
}
