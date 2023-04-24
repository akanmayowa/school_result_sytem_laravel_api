<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {
        Schema::table('candidate_indexings', function (Blueprint $table) {
                        // $table->bigInteger('ref_id')->nullable()->change();
                        $table->string('title')->nullable()->change();
                        $table->string('middle_name')->nullable()->change();
                        $table->string('years_of_experience')->nullable()->change();
                        $table->string('marital_status')->nullable()->default('S')->change();
                        $table->string('english')->nullable()->change();
                        $table->string('biology')->nullable()->change();
                        $table->string('health_science')->nullable()->change();
                        $table->string('chemistry')->nullable()->change();
                        $table->string('mathematics')->nullable()->change();
                        $table->string('geography')->nullable()->change();
                        $table->string('economics')->nullable()->change();
                        $table->string('food_and_nutrition')->nullable()->change();
                        $table->string('accounting')->nullable()->change();
                        $table->string('commerce')->nullable()->change();
                        $table->string('physics')->nullable()->change();
                        $table->string('technical_drawing')->nullable()->change();
                        $table->string('integrated_science')->nullable()->change();
                        $table->string('general_science')->nullable()->change();
                        $table->string('agric')->nullable()->change();
                        $table->bigInteger('seatings')->nullable()->change();
                        $table->string('reg_nurse')->nullable()->change();
                        $table->string('reg_midwife')->nullable()->change();
                        $table->bigInteger('verify_birth_certificate')->default(0)->nullable()->change();
                        $table->bigInteger('verify_o_level')->default(0)->nullable()->change();
                        $table->bigInteger('verify_marriage_certificate')->default(0)->nullable()->change();
                        $table->bigInteger('verify_credentials')->default(0)->nullable()->change();
                        $table->bigInteger('certificate_$_75')->default(0)->nullable()->change();
                        $table->bigInteger('letter_of_reference')->default(0)->nullable()->change();
                        $table->bigInteger('on_course')->default(0)->nullable()->change();
                        $table->bigInteger('degree_holder')->default(0)->nullable()->change();
                        $table->string('form_no')->nullable()->change();
                        $table->bigInteger('verify_status')->default(1)->nullable()->change();
                        $table->bigInteger('verify_status_2')->default(1)->nullable()->change();
                        $table->string('nationality')->nullable()->change()->change();
                        $table->string('certificate_evaluated')->nullable()->change();
                        $table->string('certificate_evaluated_2')->nullable()->change();
                        $table->string('yoruba')->nullable()->change();
                        $table->string('igbo')->nullable()->change();
                        $table->string('hausa')->nullable()->change();
                        $table->string('history')->nullable()->change();
                        $table->string('religious_knowledge')->nullable()->change();
                        $table->string('government')->nullable()->change();
                        $table->string('literature')->nullable()->change();
                        $table->string('nd_certificate_upload')->nullable()->change();
                        $table->enum('validate',['yes', 'no'])->default('no')->change();
                        $table->bigInteger('dont_det')->nullable()->change();
                        $table->string('year_of_certificate_evaluated')->nullable()->change();
                        $table->string('year_of_certificate_evaluated_2')->nullable()->change();
                        $table->dateTime('registered_at')->nullable()->change();
                        $table->bigInteger('visible')->default(1)->change();
                        $table->bigInteger('indexed')->default(1)->change();
                        $table->bigInteger('unverified')->default(1)->change();
                        $table->string('exam_date')->nullable()->change();
                        $table->string('exam_number_2')->nullable()->change();
        });
    }


    public function down()
    {
            Schema::dropIfExists('candidate_indexings');
    }
};
