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
        Schema::create('exam_offenders', function (Blueprint $table) {
            $table->id();
            $table->string('candidate_index');
            $table->string('course_header');
            $table->string('registration_date')->nullable();
            $table->string('exam_date');
            $table->string('exam_offence_id');
            $table->boolean('duration')->default(2);
            $table->text('comment')->nullable();
            $table->string('school_code')->nullable();
            $table->boolean('status')->default(1);
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
        Schema::dropIfExists('exam_offenders');
    }
};
