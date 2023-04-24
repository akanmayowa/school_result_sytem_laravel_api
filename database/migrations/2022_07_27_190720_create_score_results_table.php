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
        Schema::create('score_results', function (Blueprint $table) {
            $table->id();
            $table->string('school_code');
            $table->string('candidate_index');
            $table->string('course_average');
            $table->string('course_key');
            $table->string('course_header');
            $table->string('course_unit')->default(0);
            $table->string('year');
            $table->boolean('new')->default(0);
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
        Schema::dropIfExists('score_results');
    }
};
