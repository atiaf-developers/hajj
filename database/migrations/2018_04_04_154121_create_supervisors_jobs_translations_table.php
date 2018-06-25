<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSupervisorsJobsTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supervisors_jobs_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('locale');
            $table->string('title',255);
            $table->integer('supervisor_job_id')->unsigned();
            $table->foreign('supervisor_job_id')->references('id')->on('supervisors_jobs');
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
        Schema::dropIfExists('supervisors_jobs_translations');
    }
}
