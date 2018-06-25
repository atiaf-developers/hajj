<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRateQuestionsTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rate_questions_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title',255);

            $table->integer('rate_question_id')->unsigned();
            $table->foreign('rate_question_id')->references('id')->on('rate_questions');


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
        Schema::dropIfExists('rate_questions_translations');
    }
}
