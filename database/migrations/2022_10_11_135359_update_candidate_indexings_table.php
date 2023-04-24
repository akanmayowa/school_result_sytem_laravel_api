<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {
        Schema::table('candidate_indexings', function (Blueprint $table) {
            $table->string('photo', 255)->nullable()->change();
            $table->string('birth_certificate_upload',255)->nullable()->change();
            $table->string('marriage_certificate_upload',255)->nullable()->change();
            $table->string('olevel_certificate_upload', 255)->nullable()->change();
            $table->string('olevel_2_certificate_upload', 255)->nullable()->change();
            $table->string('phn_certificate_upload', 255)->nullable()->change();
            $table->string('phn_2_certificate_upload', 255)->nullable()->change();
            $table->string('nd_certificate_upload', 255)->nullable()->change();
            $table->string('hnd_certificate_upload', 255)->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('candidate_indexings', function (Blueprint $table) {
            $table->dropColumn(['photo'])->change()->nullable();
            $table->dropColumn(['birth_certificate_upload'])->nullable()->change();
            $table->dropColumn(['marriage_certificate_upload'])->nullable()->change();
            $table->dropColumn(['olevel_certificate_upload'])->nullable()->change();
            $table->dropColumn(['olevel_2_certificate_upload'])->nullable()->change();
            $table->dropColumn(['phn_certificate_upload'])->nullable()->change();
            $table->dropColumn(['phn_2_certificate_upload'])->nullable()->change();
            $table->dropColumn(['nd_certificate_upload'])->nullable()->change();
            $table->dropColumn(['hnd_certificate_upload'])->nullable()->change();
        });
    }
};
