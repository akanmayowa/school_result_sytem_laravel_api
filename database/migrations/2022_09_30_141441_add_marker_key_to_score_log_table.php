<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {
        Schema::table('score_logs', function (Blueprint $table) {
//                $table->string('marker_key')->nullable();
        });
    }

    public function down()
    {
        Schema::table('score_logs', function (Blueprint $table) {
//            $table->dropColumn(['marker_key']);
        });
    }
};
