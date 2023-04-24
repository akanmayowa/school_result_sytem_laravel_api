<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->text('activity');
            $table->string('data_table');
            $table->string('user_agent');
            $table->unsignedBigInteger('data_unique_id');
            $table->timestamps();
        });
    }


    public function down()
    {
        Schema::dropIfExists('activities');
    }
};
