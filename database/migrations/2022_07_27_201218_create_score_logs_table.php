<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {
        Schema::create('score_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_id');
            $table->string('candidate_index');
            $table->unsignedBigInteger('q1');
            $table->unsignedBigInteger('q2');
            $table->unsignedBigInteger('q3');
            $table->unsignedBigInteger('q4');
            $table->unsignedBigInteger('q5');
            $table->string('course_header');
            $table->string('course_key');
            $table->string('marker_key');
            $table->string('exam_date');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('score_logs');
    }
};
