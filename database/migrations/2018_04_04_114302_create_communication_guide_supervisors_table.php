<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommunicationGuideSupervisorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('communication_guide_supervisors', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('communication_guide_id')->unsigned();
            $table->foreign('communication_guide_id')->references('id')->on('communication_guides');

            $table->integer('supervisor_id')->unsigned();
            $table->foreign('supervisor_id')->references('id')->on('supervisors');

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
        Schema::dropIfExists('communication_guide_supervisors');
    }
}
