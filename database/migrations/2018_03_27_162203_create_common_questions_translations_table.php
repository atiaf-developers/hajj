<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommonQuestionsTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('common_questions_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('locale');
            $table->string('question',255);
            $table->text('answer');
            $table->integer('common_question_id')->unsigned();
            $table->foreign('common_question_id')->references('id')->on('common_questions');
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
        Schema::dropIfExists('common_questions_translations');
    }
}
