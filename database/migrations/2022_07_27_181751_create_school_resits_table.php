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
        Schema::create('school_resits', function (Blueprint $table) {
            $table->id();
            $table->string('candidate_index')->nullable();
            $table->string('subject_code')->nullable();
            $table->string('school_code')->nullable();
            $table->string('resit_header')->nullable();
            $table->string('batch')->nullable();
            $table->string('exam_date')->nullable();
            $table->boolean('resit_reg_status')->default(1);
            $table->string('old_exam_date')->nullable();
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
        Schema::dropIfExists('school_resits');
    }
};
