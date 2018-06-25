<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRateQuestionAnswersTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rate_question_answers_translations', function (Blueprint $table) {

            $table->increments('id');
            $table->string('locale');
            $table->string('title',255);

            $table->integer('rate_question_answer_id')->unsigned();
            $table->foreign('rate_question_answer_id')->references('id')->on('rate_question_answers');

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
        Schema::dropIfExists('rate_question_answers_translations');
    }
}
