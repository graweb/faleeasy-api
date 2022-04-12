<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->nullable()->unsigned()->index();
            $table->string('teacher', 40)->nullable();
            $table->date('day')->nullable();
            $table->smallInteger('start_hour')->nullable();
            $table->smallInteger('end_hour')->nullable();
            $table->string('subject')->nullable();
            $table->string('speak', 1)->nullable();
            $table->string('listen', 1)->nullable();
            $table->string('read', 1)->nullable();
            $table->string('write', 1)->nullable();
            $table->tinyInteger('situation')->nullable()->comment('0 = scheduled / 1 = confirmed / 2 = finished / 3 = canceled / 4 = blocked');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('schedules');
    }
}
