<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('score_marker_ones', function (Blueprint $table) {
            $table->id();
            $table->string('course_header');
            $table->string('course_key');
            $table->string('candidate_index');
            $table->decimal('q1')->default(0.0);
            $table->decimal('q2')->default(0.0);
            $table->decimal('q3')->default(0.0);
            $table->decimal('q4')->default(0.0);
            $table->decimal('q5')->default(0.0);
            $table->decimal('total_score')->default(0.0);
            $table->string('school_code')->nullable();
            $table->string('exam_center')->nullable();
            $table->string('exam_id')->nullable();
            $table->string('examiner')->nullable();
            $table->string('operator_id')->nullable();
            $table->boolean('status')->default(0);
            $table->string('new')->default(0);
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
        Schema::dropIfExists('score_marker_ones');
    }
};
