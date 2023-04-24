<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('school_performances', function (Blueprint $table) {
            $table->tinyInteger('new')->default(0);
        });
    }

    public function down()
    {
        Schema::table('school_performances', function (Blueprint $table) {
            $table->dropColumn('new');
        });
    }
};
