<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {
        Schema::create('training_schools', function (Blueprint $table) {
            $table->id();
            $table->string('school_code')->unique();
            $table->string('index_code');
            $table->unsignedBigInteger('state_id');
            $table->unsignedBigInteger('school_category_id');
            $table->string('school_name')->unique();
            $table->string('contact');
            $table->string('position');
            $table->string('password');
            $table->string('phone');
            $table->string('email');
            //status active = 1 and deactivated = 0
            $table->tinyInteger('status')->default(1);
            $table->foreign('state_id')->references('id')->on('states');
            $table->foreign('school_category_id')->references('id')->on('school_categories');
            $table->timestamps();
        });
    }




    public function down()
    {
        Schema::dropIfExists('training_schools');
    }
};
