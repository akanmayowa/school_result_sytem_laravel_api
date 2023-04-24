<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {
        Schema::create('course_modules', function (Blueprint $table) {
            $table->id();
            $table->string('course_key');
            $table->string('description');
            $table->tinyInteger('credits');
            $table->bigInteger('serial_number');
            $table->boolean('delete_status')->default(0);
            $table->boolean('practical')->default(0);
            $table->string('header_key');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('course_modules');
    }
};
