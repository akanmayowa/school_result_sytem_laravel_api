<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('operator_id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('photo')->nullable();
            $table->string('user_status')->default('active');
            $table->enum('user_role',['super_admin', 'admin', 'school_admin','student','training_school_admin'])->default('admin');
            $table->bigInteger('training_school_id')->unsigned()->nullable();
            $table->string('phone_number')->unique()->nullable();
            $table->string('two_factor_code')->nullable();
            $table->dateTime('expires_at')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }


    public function down()
    {
        Schema::dropIfExists('users');
    }

};
