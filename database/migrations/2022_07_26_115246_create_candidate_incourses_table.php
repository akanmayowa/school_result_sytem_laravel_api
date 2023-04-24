<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {
        Schema::create('candidate_incourses', function (Blueprint $table) {
            $table->id();
            $table->string('candidate_index');
            $table->string('course_header');
            $table->string('school_code');
            $table->bigInteger('first_semester_score');
            $table->bigInteger('second_semester_score');
            $table->bigInteger('third_semester_score');
            $table->string('operator_id');
            $table->decimal('total_score', 8,2 );
            $table->decimal('average_score', 8,2);
            $table->string('exam_id');
            $table->bigInteger('new')->default(1);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('candidate_incourses');
    }
};
