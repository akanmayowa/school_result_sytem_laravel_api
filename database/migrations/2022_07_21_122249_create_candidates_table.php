<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {
        Schema::create('candidates', function (Blueprint $table) {
            $table->id();
            $table->string('school_code')->nullable();
            $table->string('candidate_index')->nullable();
            $table->string('course_header');
            $table->string('exam_id');
            $table->dateTime('exam_date')->nullable();
            $table->boolean('fresh')->default(0);
            $table->string('resist')->nullable();
            $table->string('resist_after_absence')->nullable();
            $table->string('form_no')->nullable();
            $table->string('MPrevEntry')->nullable();
            $table->string('major')->nullable();
            $table->string('operation_id')->nullable();
            $table->enum('registration_type', ['fresh','resit','resitall','resit_after_abs'])->default('fresh');
            $table->enum('reg_status', ['pending','approved','disapproved','resubmitted'])->default('pending');
            $table->text('admin_comment')->nullable();
            $table->text('school_comment')->nullable();
            $table->boolean('visible')->default(0);
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
        Schema::dropIfExists('candidates');
    }
};
