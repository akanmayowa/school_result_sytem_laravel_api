<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {
        Schema::table('course_headers', function (Blueprint $table) {
            $table->string('total_units')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('course_headers', function (Blueprint $table) {
        $table->string('total_unit')->change();
    });
    }
};
