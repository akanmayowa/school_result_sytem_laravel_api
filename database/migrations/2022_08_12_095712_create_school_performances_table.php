<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('school_performances', function (Blueprint $table) {
            $table->id();
            $table->string('school_code');
            $table->string('candidate_index');
            $table->string('passed');
            $table->string('absent');
            $table->string('no_incourse');
            $table->string('failed');
            $table->string('malpractice');
            $table->string('exam_id');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('school_performances');
    }
};
