<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {
        Schema::table('training_schools', function (Blueprint $table) {
            $table->tinyInteger('can_register')->default(1);
        });
    }

    public function down()
    {
        Schema::table('training_schools', function (Blueprint $table) {
            $table->dropColumn(['can_register']);
        });
    }
};
