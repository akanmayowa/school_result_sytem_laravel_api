<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(){
        Schema::create('general_logs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('auth_id');
            $table->string('log_message');
            $table->string('target')->nullable();
            $table->string('model')->nullable();
            $table->timestamps();
        });
    }

    public function down(){
        Schema::dropIfExists('general_logs');
    }
};
