<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommunicationGuidesTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('communication_guides_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('locale');
            $table->string('title',255);
            $table->string('description',255);
            $table->integer('communication_guide_id')->unsigned();
            $table->foreign('communication_guide_id')->references('id')->on('communication_guides');
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
        Schema::dropIfExists('communication_guides_translations');
    }
}
