<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('final_results', function (Blueprint $table) {
            $table->id();
            $table->string('school_code')->nullable();
            $table->string('candidate_index');
            $table->string('course_header');
            $table->string('total_credit');
            $table->string('weighted_score');
            $table->string('gpa');
            $table->string('waheb70');
            $table->string('year');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('final_results');
    }
};
