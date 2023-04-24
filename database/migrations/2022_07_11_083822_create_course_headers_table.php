<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {
        Schema::create('course_headers', function (Blueprint $table) {
            $table->id();
            $table->string('header_key');
            $table->string('description');
            $table->string('cadre')->nullable();
            $table->boolean('delete_status')->default(0);
            $table->bigInteger('total_units');
            $table->bigInteger('modules');
            $table->timestamp('exam_date');
            $table->string('add_year');
            $table->string('month');
            $table->string('index_code');
            $table->timestamps();
        });
    }


    public function down()
    {
        Schema::dropIfExists('course_headers');
    }
};
